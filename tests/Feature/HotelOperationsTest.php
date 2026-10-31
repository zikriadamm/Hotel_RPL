<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HotelOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $manajer;
    protected User $resepsionis;
    protected User $kebersihan;
    protected User $tamu;
    protected Kamar $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manajer = User::create([
            'nama' => 'Pak Manajer',
            'email' => 'manajer@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'Manajer',
            'aktif' => true,
            'no_identitas' => 'NIK-MGR-001',
        ]);

        $this->resepsionis = User::create([
            'nama' => 'Mbak Resepsionis',
            'email' => 'resepsionis@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'Resepsionis',
            'aktif' => true,
            'no_identitas' => 'NIK-RCP-001',
        ]);

        $this->kebersihan = User::create([
            'nama' => 'Mas Kebersihan',
            'email' => 'kebersihan@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'Petugas Kebersihan',
            'aktif' => true,
            'no_identitas' => 'NIK-CLN-001',
        ]);

        $this->tamu = User::create([
            'nama' => 'Tamu Contoh',
            'email' => 'tamu@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'Tamu',
            'aktif' => true,
            'no_identitas' => 'NIK-TMU-001',
        ]);

        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'tipe' => 'Standar',
            'harga_per_malam' => 300000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);
    }

    /** Test 1: Nomor kamar tidak boleh ganda */
    public function test_nomor_kamar_tidak_boleh_ganda(): void
    {
        $response = $this->actingAs($this->manajer)->post(route('manajer.kamar.store'), [
            'nomor_kamar' => '101', // Sudah ada di database
            'tipe' => 'Deluxe',
            'harga_per_malam' => 500000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $response->assertSessionHasErrors('nomor_kamar');
    }

    /** Test 2: Harga per malam harus lebih dari 0 */
    public function test_harga_per_malam_harus_lebih_dari_nol(): void
    {
        $response = $this->actingAs($this->manajer)->post(route('manajer.kamar.store'), [
            'nomor_kamar' => '102',
            'tipe' => 'Deluxe',
            'harga_per_malam' => 0, // Tidak boleh <= 0
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $response->assertSessionHasErrors('harga_per_malam');
    }

    /** Test 3: Nomor identitas pengguna tidak boleh ganda */
    public function test_no_identitas_pengguna_tidak_boleh_ganda(): void
    {
        $response = $this->actingAs($this->resepsionis)->post(route('resepsionis.tamu.store'), [
            'nama' => 'Tamu Baru',
            'no_identitas' => 'NIK-TMU-001', // Duplikat dengan tamu yang sudah ada
            'email' => 'tamu.baru@test.com',
        ]);

        $response->assertSessionHasErrors('no_identitas');
    }

    /** Test 4: Resepsionis dilarang menghapus data kamar (RBAC Check) */
    public function test_resepsionis_dilarang_menghapus_kamar(): void
    {
        $response = $this->actingAs($this->resepsionis)->delete(route('manajer.kamar.destroy', $this->kamar->id));

        $response->assertStatus(403);
    }

    /** Test 5: Resepsionis dilarang mengakses laporan manajerial (RBAC Check) */
    public function test_resepsionis_dilarang_mengakses_laporan_pendapatan(): void
    {
        $response = $this->actingAs($this->resepsionis)->get(route('laporan.pendapatan'));

        $response->assertStatus(403);
    }

    /** Test 6: Check-in otomatis mengubah status kamar menjadi Terisi */
    public function test_proses_check_in_mengubah_status_kamar_menjadi_terisi(): void
    {
        $pemesanan = Pemesanan::create([
            'kode_booking' => 'BKG-TEST-001',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => today(),
            'tanggal_check_out' => today()->addDays(2),
            'total_harga' => 600000,
            'status' => 'Dipesan',
            'pembuat_id' => $this->resepsionis->id,
        ]);

        $response = $this->actingAs($this->resepsionis)->post(route('resepsionis.pemesanan.checkin', $pemesanan->id));
        $response->assertRedirect();

        $this->assertEquals('Check-in', $pemesanan->fresh()->status);
        $this->assertEquals('Terisi', $this->kamar->fresh()->status);
    }

    /** Test 7: Check-out otomatis mencatat pembayaran dan mengubah kamar menjadi Kotor */
    public function test_proses_check_out_mencatat_pembayaran_dan_ubah_kebersihan_menjadi_kotor(): void
    {
        $pemesanan = Pemesanan::create([
            'kode_booking' => 'BKG-TEST-002',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => today()->subDays(2),
            'tanggal_check_out' => today(),
            'total_harga' => 600000,
            'status' => 'Check-in',
            'pembuat_id' => $this->resepsionis->id,
        ]);
        $this->kamar->update(['status' => 'Terisi', 'status_kebersihan' => 'Bersih']);

        $response = $this->actingAs($this->resepsionis)->post(route('resepsionis.pemesanan.checkout', $pemesanan->id), [
            'metode_pembayaran' => 'Tunai',
            'jumlah_bayar' => 700000, // Bayar 700rb, kembalian 100rb
        ]);

        $response->assertRedirect(route('resepsionis.invoice', $pemesanan->id));

        $this->assertEquals('Check-out', $pemesanan->fresh()->status);
        $this->assertEquals('Kotor', $this->kamar->fresh()->status_kebersihan);
        $this->assertDatabaseHas('pembayarans', [
            'pemesanan_id' => $pemesanan->id,
            'metode_pembayaran' => 'Tunai',
            'jumlah_bayar' => 700000,
            'kembalian' => 100000,
        ]);
    }

    /** Test 8: Petugas kebersihan menandai bersih otomatis mengubah kamar menjadi Tersedia */
    public function test_petugas_kebersihan_menandai_bersih_mengubah_kamar_menjadi_tersedia(): void
    {
        $this->kamar->update([
            'status' => 'Tersedia',
            'status_kebersihan' => 'Kotor',
        ]);

        $response = $this->actingAs($this->kebersihan)->post(route('kebersihan.kamar.bersih', $this->kamar->id));
        $response->assertRedirect();

        $freshKamar = $this->kamar->fresh();
        $this->assertEquals('Bersih', $freshKamar->status_kebersihan);
        $this->assertEquals('Tersedia', $freshKamar->status);
    }

    /** Test 9: Manajer dapat mengakses ketiga laporan */
    public function test_manajer_dapat_mengakses_semua_laporan(): void
    {
        $this->actingAs($this->manajer)->get(route('laporan.okupansi'))->assertOk();
        $this->actingAs($this->manajer)->get(route('laporan.pendapatan'))->assertOk();
        $this->actingAs($this->manajer)->get(route('laporan.riwayat_tamu'))->assertOk();
    }

    /** Test 10: Sistem menolak reservasi ganda pada rentang tanggal yang bertabrakan (anti double booking) */
    public function test_sistem_menolak_reservasi_pada_tanggal_yang_bentrok(): void
    {
        // Buat pemesanan pertama: tanggal 10-15
        Pemesanan::create([
            'kode_booking' => 'BKG-TEST-001',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(5)->toDateString(),
            'tanggal_check_out' => now()->addDays(10)->toDateString(),
            'total_harga' => 1500000,
            'status' => 'Dipesan',
            'pembuat_id' => $this->resepsionis->id,
        ]);

        // Coba buat pemesanan kedua pada rentang tanggal yang bertabrakan: tanggal 7-12
        $response = $this->actingAs($this->resepsionis)->post(route('resepsionis.pemesanan.store'), [
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(7)->toDateString(),
            'tanggal_check_out' => now()->addDays(12)->toDateString(),
        ]);

        $response->assertSessionHas('error');
    }

    /** Test 11: Pelanggan tanpa login ditolak dan diarahkan untuk login/register terlebih dahulu */
    public function test_pelanggan_tanpa_login_wajib_masuk_atau_daftar_sebelum_booking(): void
    {
        $response = $this->post(route('booking.store'), [
            'tipe_kamar' => $this->kamar->tipe,
            'tanggal_check_in' => now()->addDays(1)->toDateString(),
            'tanggal_check_out' => now()->addDays(3)->toDateString(),
            'nama' => 'Tamu Anonim',
            'email' => 'anonim@example.com',
            'no_identitas' => 'NIK-ANONIM-001',
            'no_hp' => '081299998888',
            'metode_pembayaran' => 'QRIS',
        ]);

        $response->assertRedirect(route('login'));
    }

    /** Test 12: Pelanggan yang telah login dapat melakukan reservasi kamar mandiri dengan QRIS otomatis */
    public function test_pelanggan_publik_dapat_melakukan_reservasi_kamar_mandiri(): void
    {
        $response = $this->actingAs($this->tamu)->post(route('booking.store'), [
            'tipe_kamar' => $this->kamar->tipe,
            'tanggal_check_in' => now()->addDays(1)->toDateString(),
            'tanggal_check_out' => now()->addDays(3)->toDateString(),
            'nama' => 'Budi Santoso',
            'email' => $this->tamu->email,
            'no_identitas' => $this->tamu->no_identitas,
            'no_hp' => '081299998888',
            'metode_pembayaran' => 'QRIS',
        ]);

        $this->assertDatabaseHas('pemesanans', [
            'kamar_id' => $this->kamar->id,
            'tamu_id' => $this->tamu->id,
            'status' => 'Dipesan',
        ]);

        $pemesanan = Pemesanan::where('kamar_id', $this->kamar->id)->first();
        // Untuk metode QRIS, diarahkan ke gerbang pembayaran QRIS dinamis
        $response->assertRedirect(route('booking.bayar', $pemesanan->id));
        $response->assertSessionHas('success');

        // Buka halaman pembayaran QRIS
        $this->actingAs($this->tamu)->get(route('booking.bayar', $pemesanan->id))
            ->assertOk()
            ->assertSee('Pembayaran QRIS Otomatis')
            ->assertSee($pemesanan->kode_booking)
            ->assertSee('ID102026889901')
            ->assertSee('qrcode-container');

        // Status awal belum lunas
        $statusResp = $this->actingAs($this->tamu)->get(route('booking.status', $pemesanan->id));
        $statusResp->assertJson(['is_paid' => false]);

        // Simulasikan pembayaran QRIS berhasil
        $payResp = $this->actingAs($this->tamu)->post(route('booking.simulasi_bayar', $pemesanan->id));
        $payResp->assertRedirect(route('booking.sukses', $pemesanan->id));

        // Status kini lunas
        $statusResp2 = $this->actingAs($this->tamu)->get(route('booking.status', $pemesanan->id));
        $statusResp2->assertJson(['is_paid' => true]);

        // Buka voucher resmi lunas
        $this->actingAs($this->tamu)->get(route('booking.sukses', $pemesanan->id))
            ->assertOk()
            ->assertSee($pemesanan->kode_booking)
            ->assertSee('LUNAS')
            ->assertSee($this->tamu->nama);
    }

    /** Test 13: Pelanggan ditolak jika kamar tipe tersebut sudah terisi pada tanggal yang dipilih */
    public function test_pelanggan_ditolak_jika_tipe_kamar_penuh_pada_tanggal_tersebut(): void
    {
        // Isi kamar yang ada dengan reservasi
        Pemesanan::create([
            'kode_booking' => 'BKG-BLOCKED-01',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(2)->toDateString(),
            'tanggal_check_out' => now()->addDays(5)->toDateString(),
            'total_harga' => 900000,
            'status' => 'Dipesan',
            'pembuat_id' => $this->tamu->id,
        ]);

        // Pelanggan mencoba booking tipe yang sama pada rentang tanggal bertabrakan
        $response = $this->actingAs($this->tamu)->post(route('booking.store'), [
            'tipe_kamar' => $this->kamar->tipe,
            'tanggal_check_in' => now()->addDays(3)->toDateString(),
            'tanggal_check_out' => now()->addDays(6)->toDateString(),
            'nama' => $this->tamu->nama,
            'email' => $this->tamu->email,
            'no_identitas' => $this->tamu->no_identitas,
            'no_hp' => '081288887777',
            'metode_pembayaran' => 'Transfer',
        ]);

        $response->assertSessionHas('error');
    }

    /** Test 14: Pendaftaran akun baru dengan redirect booking mengarahkan ke formulir pemesanan */
    public function test_register_dengan_redirect_booking_mengarahkan_ke_formulir_booking(): void
    {
        $response = $this->post(route('register'), [
            'nama' => 'Pelanggan Baru Terdaftar',
            'email' => 'pelanggan.baru@example.com',
            'no_identitas' => 'NIK-REG-777',
            'no_hp' => '081233334444',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'redirect' => 'booking',
        ]);

        $response->assertRedirect(route('booking.form'));
        $this->assertAuthenticated();
    }

    /** Test 15: Simulasi akses cepat demo login pelanggan dengan redirect booking mengarahkan ke formulir booking */
    public function test_simulasi_akses_cepat_demo_login_pelanggan(): void
    {
        $response = $this->get(route('demo.login', ['peran' => 'Tamu', 'redirect' => 'booking']));
        $response->assertRedirect(route('booking.form'));
        $this->assertAuthenticatedAs($this->tamu);
    }

    /** Test 16: Tamu dapat mengakses portal pelanggan dan melihat katalog serta riwayat pemesanan */
    public function test_tamu_dapat_mengakses_portal_pelanggan_dan_melihat_riwayatnya(): void
    {
        // Buat pemesanan untuk tamu ini
        $pemesanan = Pemesanan::create([
            'kode_booking' => 'BKG-TEST-TAMU-01',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(5)->toDateString(),
            'tanggal_check_out' => now()->addDays(7)->toDateString(),
            'total_harga' => 600000,
            'status' => 'Dipesan',
            'pembuat_id' => $this->tamu->id,
        ]);

        $response = $this->actingAs($this->tamu)->get(route('pelanggan.dashboard'));
        $response->assertOk()
            ->assertSee('Portal Pelanggan &amp; Riwayat Reservasi', false)
            ->assertSee($pemesanan->kode_booking)
            ->assertSee('Suite Standar Heritage')
            ->assertSee('Villa Eksekutif Tepi Laut')
            ->assertSee('Penthouse Presidensial');
    }

    /** Test 17: Tamu dapat membatalkan reservasi miliknya yang berstatus Dipesan */
    public function test_tamu_dapat_membatalkan_pemesanannya_sendiri(): void
    {
        $pemesanan = Pemesanan::create([
            'kode_booking' => 'BKG-BATAL-01',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(10)->toDateString(),
            'tanggal_check_out' => now()->addDays(12)->toDateString(),
            'total_harga' => 600000,
            'status' => 'Dipesan',
            'pembuat_id' => $this->tamu->id,
        ]);

        $response = $this->actingAs($this->tamu)->post(route('pelanggan.pemesanan.batal', $pemesanan->id));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Dibatalkan', $pemesanan->fresh()->status);
    }

    /** Test 18: Tamu dilarang membatalkan pemesanan milik tamu lain */
    public function test_tamu_dilarang_membatalkan_pemesanan_milik_orang_lain(): void
    {
        $tamuLain = User::create([
            'nama' => 'Tamu Lain',
            'email' => 'tamulain@test.com',
            'password' => bcrypt('password123'),
            'peran' => 'Tamu',
            'aktif' => true,
            'no_identitas' => 'NIK-OTHER-001',
        ]);

        $pemesananLain = Pemesanan::create([
            'kode_booking' => 'BKG-OTHER-01',
            'tamu_id' => $tamuLain->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(15)->toDateString(),
            'tanggal_check_out' => now()->addDays(17)->toDateString(),
            'total_harga' => 600000,
            'status' => 'Dipesan',
            'pembuat_id' => $tamuLain->id,
        ]);

        $response = $this->actingAs($this->tamu)->post(route('pelanggan.pemesanan.batal', $pemesananLain->id));
        $response->assertStatus(403);
    }

    /** Test 19: Tamu dapat mengakses halaman khusus formulir pemesanan kamar */
    public function test_tamu_dapat_mengakses_halaman_formulir_pemesanan_khusus(): void
    {
        $response = $this->actingAs($this->tamu)->get(route('booking.form', ['tipe' => 'Deluxe']));
        $response->assertOk()
            ->assertSee('Formulir Pemesanan Kamar')
            ->assertSee('Villa Eksekutif Tepi Laut')
            ->assertSee('Konfirmasi &amp; Proses Reservasi Sekarang', false);
    }

    /** Test 20: Tamu yang login membuka landing page dialihkan otomatis ke katalog kamar */
    public function test_tamu_login_dialihkan_dari_landing_page_ke_katalog_kamar(): void
    {
        $response = $this->actingAs($this->tamu)->get(route('landing'));
        $response->assertRedirect(route('pelanggan.kamar'));
    }

    /** Test 21: Tamu dapat mengakses katalog kamar akomodasi dengan foto dan tombol pesan */
    public function test_tamu_dapat_mengakses_katalog_kamar_akomodasi(): void
    {
        $response = $this->actingAs($this->tamu)->get(route('pelanggan.kamar'));
        $response->assertOk()
            ->assertSee('Katalog Kamar Akomodasi')
            ->assertSee('Pilihan Kamar &amp; Suite Kemewahan', false)
            ->assertSee('9 Pilihan Kamar')
            ->assertSee('Suite Standar Heritage Garden')
            ->assertSee('Suite Heritage Pool Terrace')
            ->assertSee('Suite Heritage Sanctuary Twin')
            ->assertSee('Villa Eksekutif Tepi Laut')
            ->assertSee('Villa Sunset Lagoon Deluxe')
            ->assertSee('Villa Royal Garden Pavilion')
            ->assertSee('Penthouse Presidensial 360°')
            ->assertSee('Royal Imperial Sky Villa')
            ->assertSee('The Lumina Grand Residence')
            ->assertSee('Lihat Fasilitas &amp; Pesan', false)
            ->assertSee('Daftar Fasilitas Lengkap Kamar')
            ->assertSee('Pesan Kamar Ini');

        $daftarKamar = $response->viewData('daftarKamar');
        $this->assertCount(9, $daftarKamar);
    }

    /** Test 22: Staf yang login membuka landing page dialihkan otomatis ke dashboard operasional */
    public function test_staf_login_dialihkan_dari_landing_page_ke_panel_operasional(): void
    {
        $responseManager = $this->actingAs($this->manajer)->get(route('landing'));
        $responseManager->assertRedirect(route('manajer.kamar.index'));

        $responseFrontDesk = $this->actingAs($this->resepsionis)->get(route('landing'));
        $responseFrontDesk->assertRedirect(route('resepsionis.dashboard'));
    }

    /** Test 23: Pemesanan metode Tunai langsung diarahkan ke voucher sukses */
    public function test_pemesanan_tunai_langsung_diarahkan_ke_voucher_sukses(): void
    {
        $kamar2 = Kamar::create([
            'nomor_kamar' => '102',
            'tipe' => 'Deluxe',
            'harga_per_malam' => 550000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $response = $this->actingAs($this->tamu)->post(route('booking.store'), [
            'tipe_kamar' => 'Deluxe',
            'tanggal_check_in' => now()->addDays(5)->toDateString(),
            'tanggal_check_out' => now()->addDays(7)->toDateString(),
            'nama' => 'Tamu Tunai',
            'email' => 'tunai@example.com',
            'no_identitas' => 'NIK-TUNAI-001',
            'no_hp' => '081233332222',
            'metode_pembayaran' => 'Tunai',
        ]);

        $pemesanan = Pemesanan::where('kamar_id', $kamar2->id)->first();
        $response->assertRedirect(route('booking.sukses', $pemesanan->id));
    }

    /** Test 24: Webhook QRIS eksternal secara otomatis memverifikasi pembayaran */
    public function test_webhook_qris_otomatis_mencatat_pembayaran_dan_settlement(): void
    {
        $pemesanan = Pemesanan::create([
            'kode_booking' => 'BKG-WEBHOOK-99',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(12)->toDateString(),
            'tanggal_check_out' => now()->addDays(14)->toDateString(),
            'total_harga' => 600000,
            'status' => 'Dipesan',
            'pembuat_id' => $this->tamu->id,
        ]);

        $response = $this->postJson(route('api.webhook.qris'), [
            'order_id' => 'BKG-WEBHOOK-99',
            'transaction_status' => 'settlement',
            'gross_amount' => 600000,
        ]);

        $response->assertOk()
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('pembayarans', [
            'pemesanan_id' => $pemesanan->id,
            'metode_pembayaran' => 'QRIS',
        ]);
    }

    /** Test 25: Tamu dapat mengakses dan melihat halaman profilnya sendiri (Read Profil) */
    public function test_tamu_dapat_melihat_halaman_profilnya(): void
    {
        $response = $this->actingAs($this->tamu)->get(route('pelanggan.profil'));
        $response->assertOk()
            ->assertSee('Data Identitas Pribadi Tamu')
            ->assertSee($this->tamu->nama)
            ->assertSee($this->tamu->email)
            ->assertSee('Kelengkapan Profil');
    }

    /** Test 26: Tamu dapat memperbarui data identitas profilnya (Update Profil) */
    public function test_tamu_dapat_memperbarui_data_profilnya(): void
    {
        $response = $this->actingAs($this->tamu)->put(route('pelanggan.profil.update'), [
            'nama' => 'Ahmad Fauzi Alamsyah',
            'email' => 'ahmad.alamsyah@example.com',
            'no_identitas' => '3201019909090005',
            'no_hp' => '081299998888',
            'alamat' => 'Jl. Kemang Raya No. 45, Jakarta Selatan',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $this->tamu->id,
            'nama' => 'Ahmad Fauzi Alamsyah',
            'email' => 'ahmad.alamsyah@example.com',
            'no_identitas' => '3201019909090005',
            'no_hp' => '081299998888',
        ]);
    }

    /** Test 27: Tamu dapat memperbarui kata sandi akunnya (Update Security) */
    public function test_tamu_dapat_mengubah_kata_sandi_dengan_benar(): void
    {
        $response = $this->actingAs($this->tamu)->put(route('pelanggan.profil.password'), [
            'password_saat_ini' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('newpassword123', $this->tamu->fresh()->password));
    }

    /** Test 28: Tamu ditolak menghapus akun jika masih memiliki reservasi kamar aktif */
    public function test_tamu_dilarang_menghapus_akun_jika_memiliki_reservasi_aktif(): void
    {
        Pemesanan::create([
            'kode_booking' => 'BKG-ACTIVE-BLOCK',
            'tamu_id' => $this->tamu->id,
            'kamar_id' => $this->kamar->id,
            'tanggal_check_in' => now()->addDays(2)->toDateString(),
            'tanggal_check_out' => now()->addDays(4)->toDateString(),
            'total_harga' => 600000,
            'status' => 'Dipesan',
            'pembuat_id' => $this->tamu->id,
        ]);

        $response = $this->actingAs($this->tamu)->delete(route('pelanggan.profil.destroy'), [
            'password_konfirmasi_hapus' => 'password123',
        ]);

        $response->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $this->tamu->id]);
    }

    /** Test 29: Tamu dapat menghapus akunnya secara permanen jika tidak ada reservasi aktif (Delete Profil) */
    public function test_tamu_dapat_menghapus_akunnya_secara_permanen(): void
    {
        $tempTamu = User::create([
            'nama' => 'Tamu Sementara Hapus',
            'email' => 'tamu.hapus@example.com',
            'password' => Hash::make('password123'),
            'no_identitas' => 'NIK-TEMP-999',
            'peran' => 'Tamu',
            'aktif' => true,
        ]);

        $response = $this->actingAs($tempTamu)->delete(route('pelanggan.profil.destroy'), [
            'password_konfirmasi_hapus' => 'password123',
        ]);

        $response->assertRedirect(route('landing'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $tempTamu->id]);
        $this->assertGuest();
    }
}
