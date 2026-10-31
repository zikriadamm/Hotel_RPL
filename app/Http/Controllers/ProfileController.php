<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan Profil Pelanggan (Read)
     */
    public function show(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Hitung kelengkapan profil (5 indikator: nama, email, no_identitas, no_hp, alamat)
        $fields = [
            'nama' => !empty($user->nama),
            'email' => !empty($user->email),
            'no_identitas' => !empty($user->no_identitas),
            'no_hp' => !empty($user->no_hp),
            'alamat' => !empty($user->alamat),
        ];

        $completedCount = count(array_filter($fields));
        $completeness = (int) round(($completedCount / count($fields)) * 100);

        // Statistik Reservasi Tamu
        $totalPemesanan = Pemesanan::where('tamu_id', $user->id)->count();
        $aktifPemesanan = Pemesanan::where('tamu_id', $user->id)
            ->whereIn('status', ['Dipesan', 'Check-in'])
            ->count();
        $selesaiPemesanan = Pemesanan::where('tamu_id', $user->id)
            ->where('status', 'Check-out')
            ->count();
        
        $totalPengeluaran = Pemesanan::where('tamu_id', $user->id)
            ->where('status', '!=', 'Dibatalkan')
            ->sum('total_harga');

        // Tingkat Keanggotaan Tamu (Guest Membership Tier)
        $memberTier = 'Lumina Classic';
        if ($selesaiPemesanan >= 5 || $totalPengeluaran >= 5000000) {
            $memberTier = 'Lumina Elite Royal';
        } elseif ($selesaiPemesanan >= 2 || $totalPengeluaran >= 2000000) {
            $memberTier = 'Lumina Prestige Gold';
        }

        $stats = [
            'total' => $totalPemesanan,
            'aktif' => $aktifPemesanan,
            'selesai' => $selesaiPemesanan,
            'pengeluaran' => $totalPengeluaran,
            'tier' => $memberTier,
        ];

        return view('pelanggan.profil', compact('user', 'completeness', 'fields', 'stats'));
    }

    /**
     * Perbarui Data Identitas Profil Pelanggan (Update)
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_identitas' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:25'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'no_identitas.unique' => 'Nomor identitas (NIK / Paspor) ini sudah terdaftar di sistem.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Ubah Kata Sandi Akun Pelanggan (Update Security)
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'password_saat_ini' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password_saat_ini.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($request->password_saat_ini, $user->password)) {
            return back()->withErrors(['password_saat_ini' => 'Kata sandi saat ini yang Anda masukkan salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Kata sandi akun Anda berhasil diperbarui.');
    }

    /**
     * Hapus Akun & Data Pribadi Pelanggan (Delete)
     */
    public function destroy(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'password_konfirmasi_hapus' => ['required', 'string'],
        ], [
            'password_konfirmasi_hapus.required' => 'Masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun.',
        ]);

        if (!Hash::check($request->password_konfirmasi_hapus, $user->password)) {
            return back()->withErrors(['password_konfirmasi_hapus' => 'Kata sandi konfirmasi salah. Penghapusan akun dibatalkan.']);
        }

        // Cek apakah masih ada reservasi aktif (Dipesan / Check-in)
        $hasActiveBookings = Pemesanan::where('tamu_id', $user->id)
            ->whereIn('status', ['Dipesan', 'Check-in'])
            ->exists();

        if ($hasActiveBookings) {
            return back()->with('error', 'Akun tidak dapat dihapus karena Anda masih memiliki reservasi aktif (Dipesan atau Sedang Menginap). Silakan batalkan atau selesaikan reservasi Anda terlebih dahulu.');
        }

        $namaUser = $user->nama;

        DB::transaction(function () use ($user) {
            $pemesananIds = Pemesanan::where('tamu_id', $user->id)->pluck('id');
            Pembayaran::whereIn('pemesanan_id', $pemesananIds)->delete();
            Pemesanan::whereIn('id', $pemesananIds)->delete();
            $user->delete();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', "Akun tamu atas nama {$namaUser} berhasil dihapus dari sistem.");
    }
}
