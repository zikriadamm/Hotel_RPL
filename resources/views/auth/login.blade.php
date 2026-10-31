@extends('layouts.app')

@section('title', 'Masuk ke Sistem — Lumina Grand')

@section('content')
<div class="max-w-md mx-auto my-6">
    <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-hover border border-warm-200">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-navy-900 border border-gold-500/30 text-gold-300 flex items-center justify-center mx-auto mb-3 shadow-md">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-navy-900 tracking-tight">Lumina Grand</h1>
            <p class="text-xs text-gold-600 mt-1 font-semibold tracking-wider uppercase">Sistem Operasional Meja Depan &amp; PMS</p>
        </div>

        @if(request('redirect') === 'booking')
            <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl text-xs flex items-center space-x-2">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Silakan <strong>masuk ke akun Anda</strong> untuk melanjutkan reservasi kamar.</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            @if(request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif
            <div>
                <label for="email" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Alamat Email</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                    class="w-full px-4 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                    placeholder="nama@hotel.com">
            </div>

            <div x-data="{ showPass: {{ request('preview_pass') ? 'true' : 'false' }} }">
                <label for="password" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" id="password" required value="{{ request('demo_pass') ?? '' }}"
                        class="w-full pl-4 pr-11 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                        placeholder="••••••••">
                    <button type="button" @click="showPass = !showPass"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-navy-700/60 hover:text-navy-900 focus:outline-none transition"
                        :title="showPass ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'"
                        :aria-label="showPass ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
                        <!-- Icon Mata Terbuka (Lihat) -->
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Icon Mata Dicoret (Sembunyikan) -->
                        <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-warm-200 text-navy-800 focus:ring-gold-500">
                    <span class="ml-2 text-navy-700/70 font-medium">Ingat sesi saya</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-navy-800 hover:bg-navy-700 text-white font-bold py-3.5 px-4 rounded-full text-xs shadow transition mt-2">
                Masuk ke Aplikasi
            </button>

            <div class="text-center pt-2">
                <p class="text-xs text-navy-700/70">
                    Belum memiliki akun?
                    <a href="{{ route('register', request('redirect') ? ['redirect' => request('redirect')] : []) }}" class="font-bold text-gold-600 hover:text-navy-900 underline transition ml-1">
                        Daftar Akun Baru
                    </a>
                </p>
            </div>
        </form>

        <!-- Quick Demo Login Buttons -->
        <div class="mt-8 pt-6 border-t border-warm-200">
            <p class="text-[11px] font-bold text-gold-600 uppercase tracking-wider text-center mb-3">
                Simulasi Akses Cepat (Pengujian Sistem):
            </p>
            <div class="space-y-2">
                <a href="{{ route('demo.login', ['peran' => 'Tamu', 'redirect' => request('redirect')]) }}" class="flex items-center justify-between p-3 border border-gold-300 bg-amber-50/70 hover:bg-amber-100/70 rounded-xl text-xs font-bold text-navy-900 transition">
                    <span class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span>Masuk sebagai <strong>Pelanggan / Tamu (Ahmad Fauzi)</strong></span>
                    </span>
                    <span class="text-gold-600 font-extrabold">&rarr;</span>
                </a>

                <a href="{{ route('demo.login', 'Manajer') }}" class="flex items-center justify-between p-3 border border-warm-200 bg-warm-50 hover:bg-warm-100 rounded-xl text-xs font-bold text-navy-900 transition">
                    <span class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span>
                        <span>Masuk sebagai <strong>Manajer Hotel</strong></span>
                    </span>
                    <span class="text-gold-600">&rarr;</span>
                </a>

                <a href="{{ route('demo.login', 'Resepsionis') }}" class="flex items-center justify-between p-3 border border-warm-200 bg-warm-50 hover:bg-warm-100 rounded-xl text-xs font-bold text-navy-900 transition">
                    <span class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-navy-800"></span>
                        <span>Masuk sebagai <strong>Resepsionis</strong></span>
                    </span>
                    <span class="text-gold-600">&rarr;</span>
                </a>

                <a href="{{ route('demo.login', 'Petugas Kebersihan') }}" class="flex items-center justify-between p-3 border border-warm-200 bg-warm-50 hover:bg-warm-100 rounded-xl text-xs font-bold text-navy-900 transition">
                    <span class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        <span>Masuk sebagai <strong>Petugas Kebersihan</strong></span>
                    </span>
                    <span class="text-gold-600">&rarr;</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

