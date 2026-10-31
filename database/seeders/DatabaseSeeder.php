<?php

namespace Database\Seeders;

use App\Models\Kamar;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Pengguna (Staf & Tamu)
        $manajer = User::updateOrCreate(
            ['email' => 'manajer@hotel.com'],
            [
                'nama' => 'Budi Santoso (Manajer)',
                'password' => Hash::make('password123'),
                'peran' => 'Manajer',
                'aktif' => true,
                'no_hp' => '081234567890',
                'no_identitas' => '3201010101010001',
                'alamat' => 'Jl. Merdeka No. 10, Jakarta',
            ]
        );

        $resepsionis = User::updateOrCreate(
            ['email' => 'resepsionis@hotel.com'],
            [
                'nama' => 'Siti Rahma (Resepsionis)',
                'password' => Hash::make('password123'),
                'peran' => 'Resepsionis',
                'aktif' => true,
                'no_hp' => '081234567891',
                'no_identitas' => '3201010101010002',
                'alamat' => 'Jl. Sudirman No. 25, Jakarta',
            ]
        );

        $kebersihan = User::updateOrCreate(
            ['email' => 'kebersihan@hotel.com'],
            [
                'nama' => 'Agus Wicaksono (Kebersihan)',
                'password' => Hash::make('password123'),
                'peran' => 'Petugas Kebersihan',
                'aktif' => true,
                'no_hp' => '081234567892',
                'no_identitas' => '3201010101010003',
                'alamat' => 'Jl. Melati No. 5, Bogor',
            ]
        );

        $tamu1 = User::updateOrCreate(
            ['no_identitas' => '3201010101010004'],
            [
                'nama' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'password' => Hash::make('password123'),
                'peran' => 'Tamu',
                'aktif' => true,
                'no_hp' => '081299887766',
                'alamat' => 'Jl. Dago No. 12, Bandung',
            ]
        );

        $tamu2 = User::updateOrCreate(
            ['no_identitas' => '3201010101010005'],
            [
                'nama' => 'Dewi Lestari',
                'email' => 'dewi.lestari@example.com',
                'password' => Hash::make('password123'),
                'peran' => 'Tamu',
                'aktif' => true,
                'no_hp' => '081299887755',
                'alamat' => 'Jl. Malioboro No. 40, Yogyakarta',
            ]
        );

        // 2. Buat Kamar
        $kamar101 = Kamar::updateOrCreate(['nomor_kamar' => '101'], [
            'tipe' => 'Standar',
            'harga_per_malam' => 300000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar102 = Kamar::updateOrCreate(['nomor_kamar' => '102'], [
            'tipe' => 'Standar',
            'harga_per_malam' => 300000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar103 = Kamar::updateOrCreate(['nomor_kamar' => '103'], [
            'tipe' => 'Standar',
            'harga_per_malam' => 300000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Kotor', // Kamar kotor siap dibersihkan untuk demo Petugas Kebersihan
        ]);

        $kamar104 = Kamar::updateOrCreate(['nomor_kamar' => '104'], [
            'tipe' => 'Standar',
            'harga_per_malam' => 350000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar105 = Kamar::updateOrCreate(['nomor_kamar' => '105'], [
            'tipe' => 'Standar',
            'harga_per_malam' => 320000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar201 = Kamar::updateOrCreate(['nomor_kamar' => '201'], [
            'tipe' => 'Deluxe',
            'harga_per_malam' => 550000,
            'status' => 'Terisi', // Sedang ditempati tamu Dewi Lestari untuk demo Check-Out
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar202 = Kamar::updateOrCreate(['nomor_kamar' => '202'], [
            'tipe' => 'Deluxe',
            'harga_per_malam' => 550000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar203 = Kamar::updateOrCreate(['nomor_kamar' => '203'], [
            'tipe' => 'Deluxe',
            'harga_per_malam' => 550000,
            'status' => 'Perawatan',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar204 = Kamar::updateOrCreate(['nomor_kamar' => '204'], [
            'tipe' => 'Deluxe',
            'harga_per_malam' => 620000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar205 = Kamar::updateOrCreate(['nomor_kamar' => '205'], [
            'tipe' => 'Deluxe',
            'harga_per_malam' => 580000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar301 = Kamar::updateOrCreate(['nomor_kamar' => '301'], [
            'tipe' => 'Suite',
            'harga_per_malam' => 950000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar302 = Kamar::updateOrCreate(['nomor_kamar' => '302'], [
            'tipe' => 'Suite',
            'harga_per_malam' => 950000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar303 = Kamar::updateOrCreate(['nomor_kamar' => '303'], [
            'tipe' => 'Suite',
            'harga_per_malam' => 1250000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        $kamar304 = Kamar::updateOrCreate(['nomor_kamar' => '304'], [
            'tipe' => 'Suite',
            'harga_per_malam' => 1450000,
            'status' => 'Tersedia',
            'status_kebersihan' => 'Bersih',
        ]);

        // 3. Buat Transaksi Contoh
        // Transaksi 1: Check-out dan lunas (untuk laporan pendapatan)
        $bookingLalu = Pemesanan::updateOrCreate(
            ['kode_booking' => 'BKG-20261010-001'],
            [
                'tamu_id' => $tamu1->id,
                'kamar_id' => $kamar101->id,
                'tanggal_check_in' => '2026-10-10',
                'tanggal_check_out' => '2026-10-12',
                'total_harga' => 600000,
                'status' => 'Check-out',
                'pembuat_id' => $resepsionis->id,
            ]
        );

        Pembayaran::updateOrCreate(
            ['pemesanan_id' => $bookingLalu->id],
            [
                'tanggal_bayar' => '2026-10-12 11:30:00',
                'metode_pembayaran' => 'QRIS',
                'jumlah_bayar' => 600000,
                'kembalian' => 0,
                'penerima_id' => $resepsionis->id,
            ]
        );

        // Transaksi 2: Sedang Check-in (Dewi Lestari di Kamar 201)
        Pemesanan::updateOrCreate(
            ['kode_booking' => 'BKG-20261014-002'],
            [
                'tamu_id' => $tamu2->id,
                'kamar_id' => $kamar201->id,
                'tanggal_check_in' => '2026-10-14',
                'tanggal_check_out' => '2026-10-16',
                'total_harga' => 1100000,
                'status' => 'Check-in',
                'pembuat_id' => $resepsionis->id,
            ]
        );
    }
}
