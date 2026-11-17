<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    /**
     * Proses pemesanan kamar mandiri oleh pelanggan publik / tamu
     */
    public function store(Request $request)
    {
        // Pastikan pengguna sudah terautentikasi (wajib login / register)
        if (!Auth::check()) {
            return redirect()->route('login', ['redirect' => 'booking'])
                ->with('error', 'Silakan masuk ke akun Anda atau mendaftar terlebih dahulu untuk memesan kamar.');
        }

        $noIdentitasRules = ['required', 'string', 'max:50'];
        if (empty(Auth::user()->no_identitas)) {
            $noIdentitasRules[] = Rule::unique('users', 'no_identitas')->ignore(Auth::id());
        }

        $validated = $request->validate([
            'tipe_kamar' => 'required|in:Standar,Deluxe,Suite',
            'tanggal_check_in' => 'required|date|after_or_equal:today',
            'tanggal_check_out' => 'required|date|after:tanggal_check_in',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_identitas' => $noIdentitasRules,
            'no_hp' => 'required|string|max:20',
            'metode_pembayaran' => 'required|in:Tunai,Transfer,QRIS',
        ], [
            'tanggal_check_in.after_or_equal' => 'Tanggal check-in tidak boleh sebelum hari ini.',
            'tanggal_check_out.after' => 'Tanggal check-out harus minimal satu hari setelah tanggal check-in.',
            'nama.required' => 'Nama lengkap pemesan wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'no_identitas.required' => 'Nomor NIK / Paspor wajib diisi.',
            'no_identitas.unique' => 'Nomor NIK / Paspor ini sudah terdaftar pada akun lain.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
        ]);

        $checkIn = Carbon::parse($validated['tanggal_check_in']);
        $checkOut = Carbon::parse($validated['tanggal_check_out']);
        $durasiMalam = max(1, $checkIn->diffInDays($checkOut));

        // Cari kamar bebas bentrok & simpan booking secara atomik dalam satu transaksi terkunci
        $pemesanan = DB::transaction(function () use ($validated, $checkIn, $durasiMalam) {
            $kamarQuery = Kamar::where('tipe', $validated['tipe_kamar'])
                ->where('status', '!=', 'Perawatan')
                ->whereDoesntHave('pemesanans', function ($q) use ($validated) {
                    $q->whereIn('status', ['Dipesan', 'Check-in'])
                        ->where('tanggal_check_in', '<', $validated['tanggal_check_out'])
                        ->where('tanggal_check_out', '>', $validated['tanggal_check_in']);
                });

            if ($checkIn->isToday()) {
                $kamarQuery->where('status', 'Tersedia')->where('status_kebersihan', 'Bersih');
            }

            $kamar = $kamarQuery->lockForUpdate()->first();
            if (!$kamar) {
                return null;
            }

            $tamu = Auth::user();
            $tamu->update([
                'nama' => $validated['nama'] ?: $tamu->nama,
                'no_hp' => $validated['no_hp'] ?: $tamu->no_hp,
                'no_identitas' => $tamu->no_identitas ?: $validated['no_identitas'],
            ]);

            return Pemesanan::create([
                'kode_booking' => Pemesanan::generateKodeBooking(),
                'tamu_id' => $tamu->id,
                'kamar_id' => $kamar->id,
                'tanggal_check_in' => $validated['tanggal_check_in'],
                'tanggal_check_out' => $validated['tanggal_check_out'],
                'total_harga' => $durasiMalam * $kamar->harga_per_malam,
                'status' => 'Dipesan',
                'pembuat_id' => $tamu->id,
            ]);
        });

        if (!$pemesanan) {
            return back()->with('error', "Mohon maaf, semua unit Kamar {$validated['tipe_kamar']} sudah penuh untuk tanggal yang Anda pilih. Silakan pilih tipe kamar lain atau sesuaikan tanggal menginap.");
        }

        // Jika metode pembayaran QRIS, langsung arahkan ke gerbang pembayaran QRIS dinamis
        if ($validated['metode_pembayaran'] === 'QRIS') {
            return redirect()->route('booking.bayar', $pemesanan->id)
                ->with('success', "Reservasi berhasil dibuat dengan Kode: {$pemesanan->kode_booking}. Silakan selesaikan pembayaran QRIS.");
        }

        return redirect()->route('booking.sukses', $pemesanan->id)
            ->with('success', "Selamat! Reservasi kamar Anda berhasil diproses dengan Kode: {$pemesanan->kode_booking}.");
    }

    /**
     * Halaman Gerbang Pembayaran QRIS Dinamis Otomatis
     */
    public function bayar(Pemesanan $pemesanan)
    {
        // Batasi akses: hanya pemilik reservasi atau staf
        if (Auth::user()->peran === 'Tamu' && $pemesanan->tamu_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk membuka halaman pembayaran ini.');
        }

        // Jika sudah lunas, langsung arahkan ke voucher resmi
        if ($pemesanan->pembayaran()->exists()) {
            return redirect()->route('booking.sukses', $pemesanan->id)
                ->with('success', 'Reservasi ini telah lunas terverifikasi otomatis.');
        }

        $pemesanan->load(['tamu', 'kamar']);
        
        // Batas waktu pembayaran (15 menit dari pembuatan pemesanan)
        $expiredAt = $pemesanan->created_at ? $pemesanan->created_at->addMinutes(15) : now()->addMinutes(15);
        $sisaDetik = max(10, now()->diffInSeconds($expiredAt, false));
        
        // Buat string payload standar EMVCo QRIS resmi Indonesia
        $qrisPayload = self::generateQrisString($pemesanan);

        return view('booking.bayar', compact('pemesanan', 'qrisPayload', 'sisaDetik'));
    }

    /**
     * Pengecekan Status Pembayaran Real-Time (Auto-Polling API)
     */
    public function statusPembayaran(Pemesanan $pemesanan)
    {
        if (Auth::user()->peran === 'Tamu' && $pemesanan->tamu_id !== Auth::id()) {
            abort(403);
        }

        $isPaid = $pemesanan->pembayaran()->exists();

        return response()->json([
            'is_paid' => $isPaid,
            'status' => $isPaid ? 'Lunas' : 'Menunggu Pembayaran',
            'redirect_url' => route('booking.sukses', $pemesanan->id),
        ]);
    }

    /**
     * Simulasi Pembayaran QRIS Otomatis (Scan via m-Banking / E-Wallet)
     */
    public function simulasiBayar(Request $request, Pemesanan $pemesanan)
    {
        abort_unless(config('app.demo_enabled'), 403, 'Simulasi pembayaran hanya tersedia pada mode demo/pengembangan.');

        if (Auth::user()->peran === 'Tamu' && $pemesanan->tamu_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        DB::transaction(function () use ($pemesanan) {
            Pembayaran::updateOrCreate(
                ['pemesanan_id' => $pemesanan->id],
                [
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => 'QRIS',
                    'jumlah_bayar' => $pemesanan->total_harga,
                    'kembalian' => 0,
                    'penerima_id' => null, // Sistem Verifikasi Otomatis
                ]
            );
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran QRIS berhasil diverifikasi otomatis oleh sistem!',
                'redirect_url' => route('booking.sukses', $pemesanan->id),
            ]);
        }

        return redirect()->route('booking.sukses', $pemesanan->id)
            ->with('success', "Pembayaran QRIS berhasil diverifikasi otomatis! Voucher resmi reservasi {$pemesanan->kode_booking} telah terbit.");
    }

    /**
     * Webhook Handler resmi untuk integrasi Payment Gateway nyata (Midtrans / Xendit)
     */
    public function webhookQris(Request $request)
    {
        $secret = (string) config('services.qris.webhook_secret');
        abort_if($secret === '', 503, 'QRIS webhook secret belum dikonfigurasi.');
        abort_unless(
            hash_equals($secret, (string) $request->header('X-Webhook-Secret', '')),
            403,
            'Signature webhook tidak valid.'
        );

        $orderId = $request->input('order_id') ?? $request->input('external_id');
        $status = strtolower($request->input('transaction_status') ?? $request->input('status') ?? '');

        if ($orderId) {
            $pemesanan = Pemesanan::where('kode_booking', $orderId)->first();
            if ($pemesanan && in_array($status, ['settlement', 'capture', 'paid', 'completed'])) {
                Pembayaran::updateOrCreate(
                    ['pemesanan_id' => $pemesanan->id],
                    [
                        'tanggal_bayar' => now(),
                        'metode_pembayaran' => 'QRIS',
                        'jumlah_bayar' => $pemesanan->total_harga,
                        'kembalian' => 0,
                        'penerima_id' => null,
                    ]
                );
                return response()->json(['status' => 'success', 'message' => 'QRIS payment recorded']);
            }
        }

        return response()->json(['status' => 'received'], 200);
    }

    /**
     * Generator Payload Standar Nasional EMVCo QRIS Indonesia
     */
    public static function generateQrisString(Pemesanan $pemesanan): string
    {
        $amount = number_format($pemesanan->total_harga, 0, '', '');
        $bookingCode = $pemesanan->kode_booking;
        $merchantName = "ZSL RESIDENCE HOTEL";

        $payload = "000201"
            . "010212"
            . "26580014ID.LINKAJA.WWW01189360099901234567890215ID102026889901303"
            . "51440014ID.CO.QRIS.WWW0215ID1020268899010303UME"
            . "52047011"
            . "5303360"
            . "54" . sprintf("%02d", strlen($amount)) . $amount
            . "5802ID"
            . "59" . sprintf("%02d", strlen($merchantName)) . $merchantName
            . "6007JAKARTA"
            . "610510110"
            . "62" . sprintf("%02d", strlen("01" . sprintf("%02d", strlen($bookingCode)) . $bookingCode))
            . "01" . sprintf("%02d", strlen($bookingCode)) . $bookingCode;

        $dataToHash = $payload . "6304";
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($dataToHash); $i++) {
            $x = (($crc >> 8) ^ ord($dataToHash[$i])) & 0xFF;
            $x ^= $x >> 4;
            $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
        }
        $checksum = strtoupper(sprintf('%04X', $crc));

        return $dataToHash . $checksum;
    }

    /**
     * Halaman bukti voucher reservasi pelanggan
     */
    public function sukses(Pemesanan $pemesanan)
    {
        // Batasi akses: hanya tamu pemesan atau staf yang boleh melihat voucher
        if (Auth::user()->peran === 'Tamu' && $pemesanan->tamu_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat voucher reservasi ini.');
        }

        $pemesanan->load(['tamu', 'kamar', 'pembayaran']);
        return view('booking_sukses', compact('pemesanan'));
    }

    /**
     * Halaman Khusus Formulir Pemesanan Kamar (Dedicated Booking Page)
     */
    public function showForm(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login', [
                'redirect' => 'booking',
                'tipe' => $request->query('tipe'),
            ])->with('error', 'Silakan masuk ke akun Anda atau mendaftar terlebih dahulu untuk memesan kamar.');
        }

        $user = Auth::user();
        $tipeKamar = $request->query('tipe', 'Standar');
        $checkInDefault = $request->query('check_in', date('Y-m-d'));
        $checkOutDefault = $request->query('check_out', date('Y-m-d', strtotime('+1 day')));

        $summary = $this->roomSummaryByType();
        $daftarKamar = $this->baseRoomCatalog($summary);

        return view('booking.form', compact('user', 'tipeKamar', 'daftarKamar', 'checkInDefault', 'checkOutDefault'));
    }

    /**
     * Halaman Khusus Katalog Kamar untuk Pelanggan / Tamu (Dedicated Room Catalog Page)
     */
    public function kamarIndex(Request $request)
    {
        $user = Auth::user();
        $summary = $this->roomSummaryByType();

        $standarTersedia = $summary['Standar']['tersedia'];
        $standarTotal = $summary['Standar']['total'];
        $deluxeTersedia = $summary['Deluxe']['tersedia'];
        $deluxeTotal = $summary['Deluxe']['total'];
        $suiteTersedia = $summary['Suite']['tersedia'];
        $suiteTotal = $summary['Suite']['total'];

        $standarFasilitasLengkap = [
            'Kenyamanan & Tidur' => [
                'Kasur King Koil Pillow-Top dengan busa memori ortopedi',
                'Linen katun Mesir 350 benang yang sejuk & higienis',
                'Menu pilihan bantal lembut & bantal microfiber anti-alergi',
                'Tirai blackout kedap cahaya 100% untuk istirahat lelap',
            ],
            'Kamar Mandi & Kebersihan' => [
                'Kamar mandi marmer dengan rain shower tekanan presisi',
                'Perlengkapan mandi botani organik ramah lingkungan',
                'Pengering rambut berkekuatan tinggi 1800W',
                'Jubah mandi katun wafel & sandal resor eksklusif',
            ],
            'Teknologi & Hiburan' => [
                'Smart TV 50 inci 4K dengan saluran satelit internasional',
                'Akses Wi-Fi 6 berkecepatan tinggi tanpa batas kuota',
                'Meja kerja ergonomis dengan stopkontak universal & USB',
                'Brankas digital pribadi ukuran laptop 15 inci',
            ],
            'Santapan & Layanan Kamar' => [
                'Termasuk sarapan pagi prasmanan bintang lima setiap hari',
                'Fasilitas pembuat kopi & aneka teh herbal seduh harian',
                'Air mineral botol kaca Pristine segar tanpa batas',
                'Layanan housekeeping harian & turn-down service malam',
            ],
        ];

        $deluxeFasilitasLengkap = [
            'Kenyamanan & Tidur' => [
                'Kasur King Bed Ergonomis dengan lapisan pegas pocket independen',
                'Linen katun sutra 450 benang super halus & sejuk',
                'Balkon santai pribadi dengan daybed pemandangan samudra',
                'Area ruang santai privat dengan sofa empuk & meja jati',
            ],
            'Kamar Mandi & Spa' => [
                'Bathtub teraso terbuka menghadap alam & samudra',
                'Kamar mandi marmer ganda dengan rain shower terpisah',
                'Perlengkapan mandi aromaterapi impor premium',
                'Garam rendam relaksasi & jubah kimono katun lembut',
            ],
            'Teknologi & Hiburan' => [
                'Smart TV 55 inci 4K OLED dengan akses Netflix & YouTube',
                'Sistem audio nirkabel Bluetooth Hi-Fi Harman Kardon',
                'Panel kontrol suhu AC digital & pencahayaan suasana',
                'Brankas digital & stasiun pengisian daya nirkabel',
            ],
            'Santapan & Layanan' => [
                'Sajian teh sore (afternoon tea) gratis diantarkan tiap hari',
                'Mesin espresso kapsul dengan pilihan varian biji kopi',
                'Minibar sambutan gratis saat kedatangan di kamar',
                'Keranjang buah tropis segar harian & prioritas check-in cepat',
            ],
        ];

        $suiteFasilitasLengkap = [
            'Kenyamanan & Tidur' => [
                '1 Master Super King Bed + 2 Twin Bed ultra-mewah',
                'Linen katun satin 600 benang standar presidential suite',
                'Walk-in closet & ruang rias mewah dengan pencahayaan studio',
                'Sofa ruang tamu formal berkapasitas jamuan keluarga',
            ],
            'Kamar Mandi & Spa Rooftop' => [
                'Plunge pool rendam privat di dek rooftop dengan pemandangan 360°',
                'Jacuzzi pribadi terbuka & bathtub marmer Carrara ganda',
                'Pengering rambut Dyson Supersonic & perlengkapan spa lengkap',
                'Minyak esensial murni, sabun alami & jubah sutra resor',
            ],
            'Teknologi & Hiburan' => [
                'Smart TV 65 inci 4K di ruang tamu & seluruh kamar tidur',
                'Private cinema soundstage dengan Dolby Atmos surround',
                'Wi-Fi 6 pita lebar eksklusif kecepatan gigabit',
                'Smart home automation (suhu, tirai, dan ambient lighting)',
            ],
            'Santapan & Privilese Butler' => [
                'Layanan butler pribadi berdedikasi siaga 24 jam',
                'Jamuan makan malam selamat datang eksklusif dari Executive Chef',
                'Sarapan privat mewah diantar langsung ke meja makan unit',
                'Bar pribadi, kulkas pendingin wine & koleksi teh TWG pilihan',
            ],
        ];

        $daftarKamar = [
            'heritage-garden' => [
                'nama' => 'Suite Standar Heritage Garden',
                'tipe' => 'Standar',
                'kategori' => 'Heritage Suite',
                'tagline' => 'Taman tropis asri & kolam refleksi.',
                'deskripsi_lengkap' => 'Suasana tenang beraksen kayu jati dengan balkon privat menghadap taman tropis dan kolam refleksi.',
                'tarif' => $summary['Standar']['min_harga'],
                'luas' => '45 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '1 Tempat Tidur Queen',
                'pemandangan' => 'Taman Tropis & Kolam Refleksi',
                'fasilitas' => [
                    'Termasuk Sarapan Pagi Mewah',
                    'Akses Wi-Fi Kecepatan Tinggi',
                    'Smart TV 50 Inci & Saluran Satelit',
                    'Kamar Mandi Marmer & Rain Shower',
                    'Balkon Privat Teduh Berkanopi',
                ],
                'fasilitas_lengkap' => $standarFasilitasLengkap,
                'foto' => asset('images/rooms/demo-suite-1.png'),
                'fallback_foto' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1200&q=80',
                'unit_tersedia' => $standarTersedia,
                'total_unit' => $standarTotal,
            ],
            'heritage-pool' => [
                'nama' => 'Suite Heritage Pool Terrace',
                'tipe' => 'Standar',
                'kategori' => 'Heritage Suite',
                'tagline' => 'Akses langsung ke tepi kolam laguna.',
                'deskripsi_lengkap' => 'Teras santai berlantai kayu dengan akses langsung menuju kolam renang laguna biru resor.',
                'tarif' => $summary['Standar']['min_harga'],
                'luas' => '48 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '1 Tempat Tidur Queen',
                'pemandangan' => 'Teras Tepi Kolam Laguna',
                'fasilitas' => [
                    'Teras Santai Akses Langsung Kolam',
                    'Termasuk Sarapan Pagi Mewah',
                    'Kursi Berjemur Privat di Dek Teras',
                    'Smart TV 50 Inci & Soundbar Bluetooth',
                    'Perlengkapan Mandi Aromaterapi Alami',
                ],
                'fasilitas_lengkap' => $standarFasilitasLengkap,
                'foto' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80',
                'fallback_foto' => asset('images/rooms/demo-suite-1.png'),
                'unit_tersedia' => $standarTersedia,
                'total_unit' => $standarTotal,
            ],
            'heritage-sanctuary' => [
                'nama' => 'Suite Heritage Sanctuary Twin',
                'tipe' => 'Standar',
                'kategori' => 'Heritage Suite',
                'tagline' => 'Dua ranjang nyaman & panorama perbukitan.',
                'deskripsi_lengkap' => 'Kamar tenang dengan dua ranjang ergonomis dan sudut meja kerja menghadap panorama perbukitan asri.',
                'tarif' => $summary['Standar']['min_harga'],
                'luas' => '50 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '2 Tempat Tidur Single Ergonomis',
                'pemandangan' => 'Perbukitan Hijau Asri & Lembah',
                'fasilitas' => [
                    'Sepasang Tempat Tidur Twin Ergonomis',
                    'Termasuk Sarapan Pagi Mewah',
                    'Sudut Meja Kerja Eksekutif Jati',
                    'Mesin Teh Herbal Pilihan Resor',
                    'Kamar Mandi Marmer & Jubah Mandi Mewah',
                ],
                'fasilitas_lengkap' => $standarFasilitasLengkap,
                'foto' => 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=1200&q=80',
                'fallback_foto' => asset('images/rooms/demo-suite-1.png'),
                'unit_tersedia' => $standarTersedia,
                'total_unit' => $standarTotal,
            ],
            'villa-ocean' => [
                'nama' => 'Villa Eksekutif Tepi Laut',
                'tipe' => 'Deluxe',
                'kategori' => 'Executive Villa',
                'tagline' => 'Balkon samudra & bathtub teraso terbuka.',
                'deskripsi_lengkap' => 'Vila tepi pantai dengan balkon privat pemandangan samudra lepas, bathtub teraso terbuka, dan teh sore harian.',
                'tarif' => $summary['Deluxe']['min_harga'],
                'luas' => '65 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '1 Tempat Tidur King',
                'pemandangan' => 'Panorama Samudra Hindia & Senja',
                'fasilitas' => [
                    'Balkon Privat Menghadap Samudra',
                    'Sajian Teh Sore Gratis Setiap Hari',
                    'Bathtub Teraso Terbuka & Rain Shower',
                    'Minibar Pilihan Selamat Datang',
                    'Mesin Pembuat Kopi Espresso Kapsul',
                ],
                'fasilitas_lengkap' => $deluxeFasilitasLengkap,
                'foto' => asset('images/rooms/demo-suite-2.png'),
                'fallback_foto' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80',
                'unit_tersedia' => $deluxeTersedia,
                'total_unit' => $deluxeTotal,
            ],
            'villa-lagoon' => [
                'nama' => 'Villa Sunset Lagoon Deluxe',
                'tipe' => 'Deluxe',
                'kategori' => 'Executive Villa',
                'tagline' => 'Jacuzzi privat terapung di atas laguna.',
                'deskripsi_lengkap' => 'Vila berkonsep terapung di atas laguna dengan jacuzzi luar ruangan pribadi dan dek santai sunset.',
                'tarif' => $summary['Deluxe']['min_harga'],
                'luas' => '70 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '1 Tempat Tidur King',
                'pemandangan' => 'Laguna Sunset Eksotis',
                'fasilitas' => [
                    'Jacuzzi Pribadi Terbuka Luar Ruangan',
                    'Akses Dermaga Kayu Laguna Eksklusif',
                    'Keranjang Buah Segar Eksotis Harian',
                    'Sistem Audio Hi-Fi Bluetooth',
                    'Sarapan Mengapung (Floating Breakfast) Opsional',
                ],
                'fasilitas_lengkap' => $deluxeFasilitasLengkap,
                'foto' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80',
                'fallback_foto' => asset('images/rooms/demo-suite-2.png'),
                'unit_tersedia' => $deluxeTersedia,
                'total_unit' => $deluxeTotal,
            ],
            'villa-garden' => [
                'nama' => 'Villa Royal Garden Pavilion',
                'tipe' => 'Deluxe',
                'kategori' => 'Executive Villa',
                'tagline' => 'Gazebo privat di tengah taman tropis.',
                'deskripsi_lengkap' => 'Vila asri berpagar pepohonan rimbun dengan gazebo pribadi, walk-in dressing room, dan shower luar ruang.',
                'tarif' => $summary['Deluxe']['min_harga'],
                'luas' => '68 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '1 Tempat Tidur King',
                'pemandangan' => 'Halaman Tropis & Paviliun Zen',
                'fasilitas' => [
                    'Paviliun Gazebo Pribadi di Halaman',
                    'Walk-in Wardrobe & Ruang Rias Luas',
                    'Pancuran Mandi Luar Ruang Bergaya Resor',
                    'Layanan Butler Panggilan On-Call',
                    'Sajian Canapé Sambutan Petang Hari',
                ],
                'fasilitas_lengkap' => $deluxeFasilitasLengkap,
                'foto' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
                'fallback_foto' => asset('images/rooms/demo-suite-2.png'),
                'unit_tersedia' => $deluxeTersedia,
                'total_unit' => $deluxeTotal,
            ],
            'suite-penthouse' => [
                'nama' => 'Penthouse Presidensial 360°',
                'tipe' => 'Suite',
                'kategori' => 'Presidential Penthouse',
                'tagline' => 'Plunge pool rooftop & butler pribadi 24 jam.',
                'deskripsi_lengkap' => 'Penthouse puncak dengan plunge pool rooftop 360 derajat, ruang perjamuan terpisah, dan layanan butler 24 jam.',
                'tarif' => $summary['Suite']['min_harga'],
                'luas' => '120 m²',
                'kapasitas' => '4 Tamu Dewasa',
                'ranjang' => '1 Super King + 2 Twin Bed',
                'pemandangan' => 'Lanskap Samudra & Resor 360°',
                'fasilitas' => [
                    'Layanan Butler Pribadi Berdedikasi 24 Jam',
                    'Kolam Renang Rendam Plunge Pool di Rooftop',
                    'Ruang Tamu Formal & Meja Jamuan Terpisah',
                    'Jamuan Makan Malam Sambutan Spesial',
                    'Fasilitas Check-in & Check-out Dalam Kamar',
                ],
                'fasilitas_lengkap' => $suiteFasilitasLengkap,
                'foto' => asset('images/rooms/demo-suite-3.png'),
                'fallback_foto' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',
                'unit_tersedia' => $suiteTersedia,
                'total_unit' => $suiteTotal,
            ],
            'suite-imperial' => [
                'nama' => 'Royal Imperial Sky Villa',
                'tipe' => 'Suite',
                'kategori' => 'Presidential Penthouse',
                'tagline' => 'Duplex dua lantai dengan bioskop privat.',
                'deskripsi_lengkap' => 'Unit duplex dua lantai dengan private cinema Dolby Atmos, dek bintang teleskopik, dan akses helipad VIP.',
                'tarif' => $summary['Suite']['min_harga'],
                'luas' => '160 m²',
                'kapasitas' => '4 Tamu Dewasa',
                'ranjang' => '2 Tempat Tidur King',
                'pemandangan' => 'Langit Tak Bertepi & Laut Lepas',
                'fasilitas' => [
                    'Tata Ruang Duplex Dua Lantai Berwibawa',
                    'Private Cinema Room dengan Soundstage Dolby',
                    'Bar Pribadi & Koleksi Minuman Spesial',
                    'Dek Observasi Bintang & Teleskop Astronomi',
                    'Akses Helipad & Penjemputan VIP Resor',
                ],
                'fasilitas_lengkap' => $suiteFasilitasLengkap,
                'foto' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1200&q=80',
                'fallback_foto' => asset('images/rooms/demo-suite-3.png'),
                'unit_tersedia' => $suiteTersedia,
                'total_unit' => $suiteTotal,
            ],
            'suite-residence' => [
                'nama' => 'The ZSL Residence',
                'tipe' => 'Suite',
                'kategori' => 'Presidential Penthouse',
                'tagline' => 'Tiga kamar tidur & kolam renang infinity 15m.',
                'deskripsi_lengkap' => 'Kediaman tiga kamar tidur utama dengan infinity pool privat 15 meter, dapur chef, dan layanan sopir mewah.',
                'tarif' => $summary['Suite']['min_harga'],
                'luas' => '220 m²',
                'kapasitas' => '6 Tamu Dewasa',
                'ranjang' => '3 Tempat Tidur King Master',
                'pemandangan' => 'Horizon Samudra & Matahari Senja',
                'fasilitas' => [
                    'Kolam Renang Infinity Privat 15 Meter',
                    'Dapur Chef Lengkap & Meja Jamuan 10 Kursi',
                    'Layanan Kendaraan Mewah Chauffeur Resor',
                    'Ruang Sauna & Ruang Pijat Spa Privat',
                    'Prioritas Reservasi Seluruh Fasilitas Resor',
                ],
                'fasilitas_lengkap' => $suiteFasilitasLengkap,
                'foto' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80',
                'fallback_foto' => asset('images/rooms/demo-suite-3.png'),
                'unit_tersedia' => $suiteTersedia,
                'total_unit' => $suiteTotal,
            ],
        ];

        $totalTersedia = $standarTersedia + $deluxeTersedia + $suiteTersedia;
        $totalKamar = $standarTotal + $deluxeTotal + $suiteTotal;

        return view('pelanggan.kamar', compact('user', 'daftarKamar', 'totalTersedia', 'totalKamar'));
    }

    /**
     * Portal Pelanggan / Tamu (Katalog Kamar, Tanggal Booking & History Pemesanan)
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $summary = $this->roomSummaryByType();
        $daftarKamar = $this->baseRoomCatalog($summary);

        // 2. Ambil seluruh riwayat pemesanan milik tamu ini
        $riwayatPemesanan = Pemesanan::where('tamu_id', $user->id)
            ->with(['kamar', 'pembayaran'])
            ->latest()
            ->get();

        // 3. Statistik personal tamu
        $stats = [
            'total_booking' => $riwayatPemesanan->count(),
            'aktif' => $riwayatPemesanan->whereIn('status', ['Dipesan', 'Check-in'])->count(),
            'selesai' => $riwayatPemesanan->where('status', 'Check-out')->count(),
        ];

        return view('pelanggan.dashboard', compact('daftarKamar', 'riwayatPemesanan', 'stats', 'user'));
    }

    private function roomSummaryByType(): array
    {
        $defaults = [
            'Standar' => 300000,
            'Deluxe' => 550000,
            'Suite' => 950000,
        ];

        $rows = Kamar::select('tipe')
            ->selectRaw('count(*) as total_unit')
            ->selectRaw("sum(case when status = 'Tersedia' and status_kebersihan = 'Bersih' then 1 else 0 end) as tersedia_count")
            ->selectRaw('min(harga_per_malam) as min_harga')
            ->groupBy('tipe')
            ->get()
            ->keyBy('tipe');

        $summary = [];
        foreach ($defaults as $tipe => $defaultHarga) {
            $row = $rows->get($tipe);
            $summary[$tipe] = [
                'total' => (int) ($row->total_unit ?? 0),
                'tersedia' => (int) ($row->tersedia_count ?? 0),
                'min_harga' => (int) ($row->min_harga ?? $defaultHarga),
            ];
        }

        return $summary;
    }

    private function baseRoomCatalog(array $summary): array
    {
        return [
            'Standar' => [
                'nama' => 'Suite Standar Heritage',
                'tipe' => 'Standar',
                'tarif' => $summary['Standar']['min_harga'],
                'luas' => '45 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '1 Tempat Tidur Queen',
                'fasilitas' => [
                    'Termasuk Sarapan Pagi Mewah',
                    'Akses Wi-Fi Kecepatan Tinggi',
                    'Smart TV 50 Inci & Saluran Kabel',
                    'Kamar Mandi Marmer & Perlengkapan Mandi Premium',
                    'Pendingin Udara AC Individual',
                ],
                'foto' => asset('images/rooms/demo-suite-1.png'),
                'fallback_foto' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
                'unit_tersedia' => $summary['Standar']['tersedia'],
                'total_unit' => $summary['Standar']['total'],
            ],
            'Deluxe' => [
                'nama' => 'Villa Eksekutif Tepi Laut',
                'tipe' => 'Deluxe',
                'tarif' => $summary['Deluxe']['min_harga'],
                'luas' => '65 m²',
                'kapasitas' => '2 Tamu Dewasa',
                'ranjang' => '1 Tempat Tidur King',
                'fasilitas' => [
                    'Sajian Teh Sore Gratis Setiap Hari',
                    'Balkon Privat Menghadap Senja',
                    'Bathtub Teraso Terbuka & Rain Shower',
                    'Minibar Pilihan Gratis Saat Kedatangan',
                    'Mesin Pembuat Kopi Espresso Kapsul',
                ],
                'foto' => asset('images/rooms/demo-suite-2.png'),
                'fallback_foto' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
                'unit_tersedia' => $summary['Deluxe']['tersedia'],
                'total_unit' => $summary['Deluxe']['total'],
            ],
            'Suite' => [
                'nama' => 'Penthouse Presidensial',
                'tipe' => 'Suite',
                'tarif' => $summary['Suite']['min_harga'],
                'luas' => '120 m²',
                'kapasitas' => '4 Tamu Dewasa',
                'ranjang' => '1 Super King + 2 Twin Bed',
                'fasilitas' => [
                    'Layanan Butler Pribadi Berdedikasi 24 Jam',
                    'Kolam Renang Rendam Privat di Rooftop',
                    'Ruang Tamu Formal & Meja Makan Terpisah',
                    'Jamuan Makan Malam Selamat Datang',
                    'Fasilitas Check-in & Check-out di Dalam Kamar',
                ],
                'foto' => asset('images/rooms/demo-suite-3.png'),
                'fallback_foto' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
                'unit_tersedia' => $summary['Suite']['tersedia'],
                'total_unit' => $summary['Suite']['total'],
            ],
        ];
    }

    /**
     * Pembatalan reservasi mandiri oleh pelanggan
     */
    public function batalkanPemesanan(Pemesanan $pemesanan)
    {
        if ($pemesanan->tamu_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak untuk membatalkan reservasi ini.');
        }

        if ($pemesanan->status !== 'Dipesan') {
            return back()->with('error', 'Hanya reservasi dengan status "Dipesan" yang dapat dibatalkan.');
        }

        $pemesanan->update(['status' => 'Dibatalkan']);

        return back()->with('success', "Reservasi dengan Kode {$pemesanan->kode_booking} berhasil dibatalkan.");
    }
}
