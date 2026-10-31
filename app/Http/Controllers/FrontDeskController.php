<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FrontDeskController extends Controller
{
    public function dashboard()
    {
        $kamars = Kamar::orderBy('nomor_kamar')->get();

        $stats = [
            'total' => $kamars->count(),
            'tersedia' => $kamars->where('status', 'Tersedia')->where('status_kebersihan', 'Bersih')->count(),
            'terisi' => $kamars->where('status', 'Terisi')->count(),
            'kotor' => $kamars->where('status_kebersihan', 'Kotor')->count(),
            'perawatan' => $kamars->where('status', 'Perawatan')->count(),
        ];

        $pemesananAktif = Pemesanan::with(['tamu', 'kamar'])
            ->whereIn('status', ['Dipesan', 'Check-in'])
            ->orderBy('tanggal_check_in')
            ->get();

        $tamuList = User::where('peran', 'Tamu')->orderBy('nama')->get();
        $kamarTersedia = Kamar::where('status', 'Tersedia')->where('status_kebersihan', 'Bersih')->get();

        return view('resepsionis.dashboard', compact('kamars', 'stats', 'pemesananAktif', 'tamuList', 'kamarTersedia'));
    }

    public function tamuIndex()
    {
        $tamus = User::where('peran', 'Tamu')->latest()->paginate(15);
        return view('resepsionis.tamu', compact('tamus'));
    }

    public function tamuStore(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_identitas' => 'required|string|max:50|unique:users,no_identitas',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'no_identitas.unique' => 'Nomor Identitas (NIK/Paspor) sudah terdaftar dalam sistem.',
            'email.unique' => 'Email ini sudah terdaftar.',
        ]);

        $validated['peran'] = 'Tamu';
        $validated['aktif'] = true;
        $validated['password'] = bcrypt('tamu123');

        User::create($validated);

        return back()->with('success', 'Data tamu baru berhasil didaftarkan.');
    }

    public function pemesananIndex(Request $request)
    {
        $query = Pemesanan::with(['tamu', 'kamar', 'pembuat', 'pembayaran'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_booking', 'like', "%{$search}%")
                    ->orWhereHas('tamu', fn ($t) => $t->where('nama', 'like', "%{$search}%")->orWhere('no_identitas', 'like', "%{$search}%"))
                    ->orWhereHas('kamar', fn ($k) => $k->where('nomor_kamar', 'like', "%{$search}%"));
            });
        }

        $pemesanans = $query->paginate(15);
        $kamarTersedia = Kamar::where('status', 'Tersedia')->where('status_kebersihan', 'Bersih')->get();
        $tamuList = User::where('peran', 'Tamu')->orderBy('nama')->get();

        return view('resepsionis.pemesanan', compact('pemesanans', 'kamarTersedia', 'tamuList'));
    }

    public function pemesananStore(Request $request)
    {
        $validated = $request->validate([
            'tamu_id' => 'required|exists:users,id',
            'kamar_id' => 'required|exists:kamars,id',
            'tanggal_check_in' => 'required|date|after_or_equal:today',
            'tanggal_check_out' => 'required|date|after:tanggal_check_in',
        ], [
            'tanggal_check_in.after_or_equal' => 'Tanggal check-in tidak boleh di masa lampau.',
            'tanggal_check_out.after' => 'Tanggal check-out harus setelah tanggal check-in.',
        ]);

        $kamar = Kamar::findOrFail($validated['kamar_id']);

        if ($kamar->status !== 'Tersedia' || $kamar->status_kebersihan !== 'Bersih') {
            return back()->with('error', 'Kamar nomor ' . $kamar->nomor_kamar . ' sedang tidak tersedia untuk dipesan.');
        }

        // Cek bentrok tanggal reservasi (cegah double booking)
        $bentrok = Pemesanan::where('kamar_id', $kamar->id)
            ->whereIn('status', ['Dipesan', 'Check-in'])
            ->where('tanggal_check_in', '<', $validated['tanggal_check_out'])
            ->where('tanggal_check_out', '>', $validated['tanggal_check_in'])
            ->exists();

        if ($bentrok) {
            return back()->with('error', 'Kamar nomor ' . $kamar->nomor_kamar . ' sudah memiliki reservasi aktif pada rentang tanggal tersebut.');
        }

        $checkIn = Carbon::parse($validated['tanggal_check_in']);
        $checkOut = Carbon::parse($validated['tanggal_check_out']);
        $durasiMalam = max(1, $checkIn->diffInDays($checkOut));
        $totalHarga = $durasiMalam * $kamar->harga_per_malam;

        $kodeBooking = Pemesanan::generateKodeBooking();

        Pemesanan::create([
            'kode_booking' => $kodeBooking,
            'tamu_id' => $validated['tamu_id'],
            'kamar_id' => $kamar->id,
            'tanggal_check_in' => $validated['tanggal_check_in'],
            'tanggal_check_out' => $validated['tanggal_check_out'],
            'total_harga' => $totalHarga,
            'status' => 'Dipesan',
            'pembuat_id' => auth()->id(),
        ]);

        return back()->with('success', "Pemesanan berhasil dibuat dengan Kode Booking: {$kodeBooking}.");
    }

    public function checkIn(Pemesanan $pemesanan)
    {
        if ($pemesanan->status !== 'Dipesan') {
            return back()->with('error', 'Hanya pemesanan dengan status "Dipesan" yang dapat di-check-in.');
        }

        DB::transaction(function () use ($pemesanan) {
            $pemesanan->update(['status' => 'Check-in']);
            $pemesanan->kamar->update([
                'status' => 'Terisi',
            ]);
        });

        return back()->with('success', "Tamu berhasil Check-in ke kamar {$pemesanan->kamar->nomor_kamar}. Status kamar kini Terisi.");
    }

    public function checkOut(Request $request, Pemesanan $pemesanan)
    {
        if ($pemesanan->status !== 'Check-in') {
            return back()->with('error', 'Hanya pemesanan dengan status "Check-in" yang dapat di-check-out.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|in:Tunai,Transfer,QRIS',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        if ($validated['jumlah_bayar'] < $pemesanan->total_harga) {
            return back()->with('error', 'Jumlah pembayaran kurang! Total tagihan: Rp ' . number_format($pemesanan->total_harga, 0, ',', '.'));
        }

        $kembalian = $validated['jumlah_bayar'] - $pemesanan->total_harga;

        DB::transaction(function () use ($pemesanan, $validated, $kembalian) {
            // 1. Simpan bukti pembayaran jika belum dibayar via QRIS/online
            if (!$pemesanan->pembayaran) {
                Pembayaran::create([
                    'pemesanan_id' => $pemesanan->id,
                    'tanggal_bayar' => now(),
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'jumlah_bayar' => $validated['jumlah_bayar'],
                    'kembalian' => $kembalian,
                    'penerima_id' => auth()->id(),
                ]);
            }

            // 2. Ubah status pemesanan menjadi Check-out
            $pemesanan->update(['status' => 'Check-out']);

            // 3. Ubah status kamar kebersihan menjadi Kotor (sesuai aturan alur bisnis dokumen)
            $pemesanan->kamar->update([
                'status' => 'Tersedia',
                'status_kebersihan' => 'Kotor',
            ]);
        });

        return redirect()->route('resepsionis.invoice', $pemesanan->id)
            ->with('success', "Proses Check-out berhasil! Kamar {$pemesanan->kamar->nomor_kamar} otomatis berstatus Kotor dan siap dibersihkan.");
    }

    public function batalkan(Pemesanan $pemesanan)
    {
        if ($pemesanan->status !== 'Dipesan') {
            return back()->with('error', 'Hanya pemesanan bertatus "Dipesan" yang dapat dibatalkan.');
        }

        $pemesanan->update(['status' => 'Dibatalkan']);
        return back()->with('success', "Pemesanan {$pemesanan->kode_booking} berhasil dibatalkan.");
    }

    public function invoice(Pemesanan $pemesanan)
    {
        $pemesanan->load(['tamu', 'kamar', 'pembuat', 'pembayaran.penerima']);
        return view('resepsionis.invoice', compact('pemesanan'));
    }
}
