<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'no_identitas' => 'required|string|max:50|unique:users,no_identitas',
            'no_hp' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'no_identitas.required' => 'Nomor identitas (NIK/Paspor) wajib diisi.',
            'no_identitas.unique' => 'Nomor identitas ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_identitas' => $validated['no_identitas'],
            'no_hp' => $validated['no_hp'] ?? null,
            'password' => bcrypt($validated['password']),
            'peran' => 'Tamu',
            'aktif' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user)->with('success', 'Pendaftaran akun berhasil! Selamat datang di Lumina Grand Hotel, ' . $user->nama . '.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->aktif) {
                Auth::logout();
                return back()->with('error', 'Akun Anda dinonaktifkan. Hubungi Manajer.');
            }

            return $this->redirectBasedOnRole($user)->with('success', 'Selamat datang kembali, ' . $user->nama . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function demoLogin(string $peran)
    {
        $peranNormalized = in_array(strtolower($peran), ['pelanggan', 'tamu']) ? 'Tamu' : $peran;
        $user = User::where('peran', $peranNormalized)->where('aktif', true)->first();

        if (!$user) {
            return back()->with('error', 'Akun demo untuk peran ' . $peran . ' tidak ditemukan.');
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole($user)->with('success', 'Berhasil masuk sebagai demo: ' . $user->nama);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->peran === 'Tamu') {
            if (request('redirect') === 'bayar') {
                $bookingId = request('id') ?? \App\Models\Pemesanan::latest()->first()->id;
                return redirect()->route('booking.bayar', $bookingId);
            }
            if (request('redirect') === 'voucher') {
                $bookingId = request('id') ?? \App\Models\Pemesanan::latest()->first()->id;
                return redirect()->route('booking.sukses', $bookingId);
            }
            if (request('redirect') === 'riwayat') {
                return redirect()->route('pelanggan.dashboard');
            }
            if (request('redirect') === 'form' || request('redirect') === 'booking_form') {
                $tipeParam = request('tipe') ? '?tipe=' . urlencode(request('tipe')) : '';
                return redirect()->to(route('booking.form') . $tipeParam);
            }
            if (request('redirect') === 'booking' || session('redirect_to') === 'booking') {
                session()->forget('redirect_to');
                $tipeParam = request('tipe') ? '?tipe=' . urlencode(request('tipe')) : '';
                return redirect()->to(route('booking.form') . $tipeParam);
            }
            return redirect()->intended(route('pelanggan.kamar'));
        }

        return match ($user->peran) {
            'Manajer' => redirect()->route('manajer.kamar.index'),
            'Resepsionis' => redirect()->route('resepsionis.dashboard'),
            'Petugas Kebersihan' => redirect()->route('kebersihan.index'),
            default => redirect()->route('login'),
        };
    }
}
