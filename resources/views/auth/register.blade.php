@extends('layouts.app')

@section('title', 'Daftar Akun Baru — Lumina Grand')

@section('content')
<div class="max-w-md mx-auto my-6">
    <div class="bg-white p-8 sm:p-10 rounded-3xl shadow-hover border border-warm-200">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-navy-900 border border-gold-500/30 text-gold-300 flex items-center justify-center mx-auto mb-3 shadow-md">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-navy-900 tracking-tight">Lumina Grand</h1>
            <p class="text-xs text-gold-600 mt-1 font-semibold tracking-wider uppercase">Pendaftaran Akun Tamu</p>
            <p class="text-xs text-navy-700/70 mt-1">Daftarkan akun untuk kemudahan reservasi dan kenyamanan menginap.</p>
        </div>

        @if(request('redirect') === 'booking')
            <div class="mb-5 p-3.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl text-xs flex items-center space-x-2">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Silakan <strong>daftar akun tamu baru</strong> untuk menyelesaikan reservasi kamar.</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-3.5 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            @if(request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif
            <div>
                <label for="nama" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Nama Lengkap *</label>
                <input type="text" name="nama" id="nama" required value="{{ old('nama') }}"
                    class="w-full px-4 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                    placeholder="Nama lengkap sesuai tanda pengenal">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Alamat Email *</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                    class="w-full px-4 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                    placeholder="nama@email.com">
            </div>

            <div>
                <label for="no_identitas" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Nomor Identitas (NIK / Paspor) *</label>
                <input type="text" name="no_identitas" id="no_identitas" required value="{{ old('no_identitas') }}"
                    class="w-full px-4 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                    placeholder="Contoh: 3201010101010008">
            </div>

            <div>
                <label for="no_hp" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp</label>
                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}"
                    class="w-full px-4 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                    placeholder="0812xxxxxxxx">
            </div>

            <!-- Kata Sandi dengan Tombol Lihat Password -->
            <div x-data="{ showPass: {{ request('preview_pass') ? 'true' : 'false' }} }">
                <label for="password" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Kata Sandi (Min. 6 Karakter) *</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" id="password" required minlength="6" value="{{ request('demo_pass') ?? '' }}"
                        class="w-full pl-4 pr-11 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                        placeholder="••••••••">
                    <button type="button" @click="showPass = !showPass"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-navy-700/60 hover:text-navy-900 focus:outline-none transition"
                        :title="showPass ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'"
                        :aria-label="showPass ? 'Sembunyikan kata sandi' : 'Lihat kata sandi'">
                        <!-- Icon Mata Terbuka -->
                        <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Icon Mata Dicoret -->
                        <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Kata Sandi dengan Tombol Lihat Password -->
            <div x-data="{ showPassConfirm: {{ request('preview_pass') ? 'true' : 'false' }} }">
                <label for="password_confirmation" class="block text-xs font-bold text-navy-800 uppercase tracking-wider mb-1.5">Ulangi Kata Sandi *</label>
                <div class="relative">
                    <input :type="showPassConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required minlength="6" value="{{ request('demo_pass') ?? '' }}"
                        class="w-full pl-4 pr-11 py-2.5 bg-warm-50 border border-warm-200 rounded-xl text-xs font-medium text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500"
                        placeholder="••••••••">
                    <button type="button" @click="showPassConfirm = !showPassConfirm"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-navy-700/60 hover:text-navy-900 focus:outline-none transition"
                        :title="showPassConfirm ? 'Sembunyikan konfirmasi kata sandi' : 'Lihat konfirmasi kata sandi'"
                        :aria-label="showPassConfirm ? 'Sembunyikan konfirmasi kata sandi' : 'Lihat konfirmasi kata sandi'">
                        <!-- Icon Mata Terbuka -->
                        <svg x-show="!showPassConfirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Icon Mata Dicoret -->
                        <svg x-show="showPassConfirm" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-navy-800 hover:bg-navy-700 text-white font-bold py-3.5 px-4 rounded-full text-xs shadow transition mt-3">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-warm-200 text-center">
            <p class="text-xs text-navy-700/70">
                Sudah memiliki akun?
                <a href="{{ route('login', request('redirect') ? ['redirect' => request('redirect')] : []) }}" class="font-bold text-gold-600 hover:text-navy-900 underline transition ml-1">
                    Masuk ke Aplikasi
                </a>
            </p>
        </div>

    </div>
</div>
@endsection
