<?php

namespace App\Http\Controllers;

use App\Models\Kamar;

class LandingController extends Controller
{
    public function index()
    {
        // Halaman ini khusus pengunjung yang belum masuk (unauthenticated guest)
        // Jika sudah login, langsung alihkan ke portal operasional sesuai peran masing-masing
        if (auth()->check()) {
            return match (auth()->user()->peran) {
                'Tamu' => redirect()->route('pelanggan.kamar'),
                'Manajer' => redirect()->route('manajer.kamar.index'),
                'Resepsionis' => redirect()->route('resepsionis.dashboard'),
                'Petugas Kebersihan' => redirect()->route('kebersihan.index'),
                default => redirect()->route('pelanggan.kamar'),
            };
        }

        $tipeKamar = Kamar::select('tipe', 'harga_per_malam')
            ->selectRaw('count(*) as total_unit')
            ->selectRaw("sum(case when status = 'Tersedia' and status_kebersihan = 'Bersih' then 1 else 0 end) as tersedia_count")
            ->groupBy('tipe', 'harga_per_malam')
            ->orderBy('harga_per_malam', 'asc')
            ->get();

        $totalKamar = Kamar::count();
        $kamarTersedia = Kamar::tersedia()->count();

        return view('landing', compact('tipeKamar', 'totalKamar', 'kamarTersedia'));
    }
}
