@extends('layouts.app')

@section('title', 'Profil Saya & Pengaturan Akun — Lumina Grand')

@section('content')
<div class="max-w-6xl mx-auto space-y-8" x-data="{ activeTab: 'identitas', deleteModalOpen: false }">

    <!-- ====================================================================
         1. Header Profil & Identitas Tamu
         ==================================================================== -->
    <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-8 shadow-card relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-gold-100/50 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-start sm:items-center space-x-5">
                <!-- Monogram Avatar -->
                <div class="w-18 h-18 sm:w-20 sm:h-20 rounded-3xl bg-navy-900 border-2 border-gold-500/40 text-gold-300 flex items-center justify-center font-extrabold text-3xl sm:text-4xl shadow-md flex-shrink-0">
                    {{ substr($user->nama, 0, 1) }}
                </div>
                
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-gold-100 text-gold-800 border border-gold-300">
                            {{ $stats['tier'] }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Akun Tamu Terverifikasi
                        </span>
                    </div>
                    
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">
                        {{ $user->nama }}
                    </h1>
                    
                    <p class="text-xs sm:text-sm text-stone-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>{{ $user->email }}</span>
                        <span class="text-stone-300">&bull;</span>
                        <span>Bergabung sejak {{ $user->created_at->format('d M Y') }}</span>
                    </p>
                </div>
            </div>

            <!-- Profile Completeness Meter -->
            <div class="w-full lg:w-72 bg-warm-50 p-4 rounded-2xl border border-warm-200 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-navy-900">Kelengkapan Profil</span>
                    <span class="font-extrabold text-gold-700">{{ $completeness }}%</span>
                </div>
                <div class="w-full h-2.5 bg-warm-200 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-gold-500 to-amber-500 rounded-full transition-all duration-500" 
                         style="width: {{ $completeness }}%"></div>
                </div>
                <p class="text-[11px] text-stone-500 leading-tight">
                    @if($completeness === 100)
                        Data profil Anda telah lengkap 100%. Reservasi akan berjalan instan.
                    @else
                        Lengkapi NIK dan alamat domisili untuk mempercepat proses check-in hotel.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         2. Statistik Ringkas Tamu (Bento Grid)
         ==================================================================== -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white border border-warm-200 p-4 sm:p-5 rounded-3xl shadow-card">
            <span class="text-[10px] sm:text-xs font-bold text-stone-400 uppercase tracking-wider block">Total Reservasi</span>
            <span class="text-2xl sm:text-3xl font-extrabold text-navy-900 mt-1 block">{{ $stats['total'] }} Kali</span>
            <span class="text-[11px] text-stone-500 mt-0.5 block">Sepanjang waktu</span>
        </div>

        <div class="bg-white border border-warm-200 p-4 sm:p-5 rounded-3xl shadow-card">
            <span class="text-[10px] sm:text-xs font-bold text-amber-700 uppercase tracking-wider block">Reservasi Aktif</span>
            <span class="text-2xl sm:text-3xl font-extrabold text-amber-900 mt-1 block">{{ $stats['aktif'] }} Unit</span>
            <span class="text-[11px] text-stone-500 mt-0.5 block">Dipesan / Menginap</span>
        </div>

        <div class="bg-white border border-warm-200 p-4 sm:p-5 rounded-3xl shadow-card">
            <span class="text-[10px] sm:text-xs font-bold text-emerald-700 uppercase tracking-wider block">Selesai Menginap</span>
            <span class="text-2xl sm:text-3xl font-extrabold text-emerald-900 mt-1 block">{{ $stats['selesai'] }} Kali</span>
            <span class="text-[11px] text-stone-500 mt-0.5 block">Check-out resmi</span>
        </div>

        <div class="bg-white border border-warm-200 p-4 sm:p-5 rounded-3xl shadow-card">
            <span class="text-[10px] sm:text-xs font-bold text-gold-700 uppercase tracking-wider block">Total Transaksi</span>
            <span class="text-lg sm:text-xl font-extrabold text-navy-900 mt-1 block truncate">
                Rp {{ number_format($stats['pengeluaran'], 0, ',', '.') }}
            </span>
            <span class="text-[11px] text-stone-500 mt-0.5 block">Akomodasi resmi</span>
        </div>
    </div>

    <!-- ====================================================================
         3. Tab Navigasi Pengaturan Profil
         ==================================================================== -->
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-warm-200 pb-4">
        <div class="inline-flex p-1 bg-warm-100 rounded-2xl border border-warm-200 text-xs font-bold">
            <button type="button" 
                    @click="activeTab = 'identitas'" 
                    :class="activeTab === 'identitas' ? 'bg-white text-navy-900 shadow-sm' : 'text-stone-600 hover:text-navy-900'"
                    class="px-5 py-2.5 rounded-xl transition cursor-pointer flex items-center space-x-2">
                <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span>Data Identitas Diri</span>
            </button>
            <button type="button" 
                    @click="activeTab = 'keamanan'" 
                    :class="activeTab === 'keamanan' ? 'bg-white text-navy-900 shadow-sm' : 'text-stone-600 hover:text-navy-900'"
                    class="px-5 py-2.5 rounded-xl transition cursor-pointer flex items-center space-x-2">
                <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Keamanan &amp; Kata Sandi</span>
            </button>
            <button type="button" 
                    @click="activeTab = 'privasi'" 
                    :class="activeTab === 'privasi' ? 'bg-white text-navy-900 shadow-sm' : 'text-stone-600 hover:text-navy-900'"
                    class="px-5 py-2.5 rounded-xl transition cursor-pointer flex items-center space-x-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                <span>Hapus Akun</span>
            </button>
        </div>

        <div class="flex items-center space-x-3 text-xs">
            <a href="{{ route('pelanggan.kamar') }}" class="font-bold text-navy-800 hover:text-navy-950 flex items-center space-x-1.5 transition">
                <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Katalog Kamar</span>
            </a>
            <span class="text-stone-300">|</span>
            <a href="{{ route('pelanggan.dashboard') }}" class="font-bold text-gold-600 hover:text-gold-700 flex items-center space-x-1.5 transition">
                <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Riwayat Reservasi</span>
            </a>
        </div>
    </div>

    <!-- ====================================================================
         4. PANEL 1: DATA IDENTITAS DIRI (UPDATE PROFIL)
         ==================================================================== -->
    <div x-show="activeTab === 'identitas'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-white rounded-3xl border border-warm-200 shadow-card overflow-hidden">
        
        <div class="p-6 sm:p-8 border-b border-warm-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-navy-900 tracking-tight">Data Identitas Pribadi Tamu</h2>
                <p class="text-xs text-stone-500 mt-1">Perbarui informasi kontak dan identitas resmi Anda untuk kemudahan verifikasi reservasi hotel.</p>
            </div>
            <span class="inline-flex items-center space-x-1.5 text-xs text-stone-500 bg-warm-50 border border-warm-200 px-3 py-1.5 rounded-full self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>ID Pengguna: #{{ $user->id }}</span>
            </span>
        </div>

        <form action="{{ route('pelanggan.profil.update') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Lengkap -->
                <div class="space-y-1.5">
                    <label for="nama" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                        Nama Lengkap Sesuai Identitas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           value="{{ old('nama', $user->nama) }}" 
                           required 
                           class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition @error('nama') border-rose-400 bg-rose-50/30 @enderror">
                    @error('nama')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                        Alamat Email Resmi <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required 
                           class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition @error('email') border-rose-400 bg-rose-50/30 @enderror">
                    @error('email')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor NIK / Paspor -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="no_identitas" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                            Nomor Identitas (NIK KTP / Paspor)
                        </label>
                        @if(empty($user->no_identitas))
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">+20% Kelengkapan</span>
                        @endif
                    </div>
                    <input type="text" 
                           id="no_identitas" 
                           name="no_identitas" 
                           value="{{ old('no_identitas', $user->no_identitas) }}" 
                           placeholder="Contoh: 3201012345670001 atau A12345678" 
                           class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition @error('no_identitas') border-rose-400 bg-rose-50/30 @enderror">
                    @error('no_identitas')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-stone-500">Digunakan untuk verifikasi cepat saat proses check-in di meja resepsionis.</p>
                </div>

                <!-- Nomor WhatsApp / Kontak -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label for="no_hp" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                            Nomor WhatsApp / Telepon
                        </label>
                        @if(empty($user->no_hp))
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">+20% Kelengkapan</span>
                        @endif
                    </div>
                    <input type="tel" 
                           id="no_hp" 
                           name="no_hp" 
                           value="{{ old('no_hp', $user->no_hp) }}" 
                           placeholder="Contoh: 081234567890" 
                           class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition @error('no_hp') border-rose-400 bg-rose-50/30 @enderror">
                    @error('no_hp')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-stone-500">Untuk pengiriman konfirmasi instan dan voucher reservasi resmi.</p>
                </div>
            </div>

            <!-- Alamat Lengkap Domisili -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="alamat" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                        Alamat Domisili Lengkap
                    </label>
                    @if(empty($user->alamat))
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">+20% Kelengkapan</span>
                    @endif
                </div>
                <textarea id="alamat" 
                          name="alamat" 
                          rows="3" 
                          placeholder="Masukkan alamat domisili, kota, dan provinsi tempat tinggal Anda..."
                          class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition @error('alamat') border-rose-400 bg-rose-50/30 @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-warm-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-xs text-stone-500">Perubahan data identitas akan langsung berlaku pada reservasi baru Anda.</span>
                
                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <button type="reset" class="w-full sm:w-auto px-5 py-3 rounded-2xl border border-warm-300 text-stone-600 hover:text-navy-900 text-xs font-bold transition">
                        Reset Formulir
                    </button>
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-navy-900 hover:bg-black text-white text-xs sm:text-sm font-bold py-3 px-6 rounded-2xl transition duration-200 shadow-card hover:shadow-hover">
                        <svg class="w-4 h-4 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Perubahan Identitas</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- ====================================================================
         5. PANEL 2: KEAMANAN & KATA SANDI (UPDATE PASSWORD)
         ==================================================================== -->
    <div x-show="activeTab === 'keamanan'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-white rounded-3xl border border-warm-200 shadow-card overflow-hidden"
         style="display: none;">
        
        <div class="p-6 sm:p-8 border-b border-warm-200">
            <h2 class="text-xl font-extrabold text-navy-900 tracking-tight">Keamanan &amp; Kata Sandi Akun</h2>
            <p class="text-xs text-stone-500 mt-1">Pastikan akun Anda terlindungi dengan menggunakan kombinasi kata sandi yang kuat.</p>
        </div>

        <form action="{{ route('pelanggan.profil.password') }}" method="POST" class="p-6 sm:p-8 space-y-6 max-w-2xl">
            @csrf
            @method('PUT')

            <!-- Kata Sandi Saat Ini -->
            <div class="space-y-1.5">
                <label for="password_saat_ini" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                    Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                </label>
                <input type="password" 
                       id="password_saat_ini" 
                       name="password_saat_ini" 
                       required 
                       placeholder="Masukkan kata sandi lama Anda"
                       class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition @error('password_saat_ini') border-rose-400 bg-rose-50/30 @enderror">
                @error('password_saat_ini')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kata Sandi Baru -->
            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                    Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="Minimal 6 karakter"
                       class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition @error('password') border-rose-400 bg-rose-50/30 @enderror">
                @error('password')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Kata Sandi Baru -->
            <div class="space-y-1.5">
                <label for="password_confirmation" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                    Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required 
                       placeholder="Ketik ulang kata sandi baru"
                       class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-gold-500/30 focus:border-gold-500 transition">
            </div>

            <!-- Action Button -->
            <div class="pt-4 border-t border-warm-200 flex items-center justify-end">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-navy-900 hover:bg-black text-white text-xs sm:text-sm font-bold py-3 px-6 rounded-2xl transition duration-200 shadow-card hover:shadow-hover">
                    <svg class="w-4 h-4 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ====================================================================
         6. PANEL 3: PRIVASI & HAPUS AKUN (DELETE AKUN)
         ==================================================================== -->
    <div x-show="activeTab === 'privasi'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="bg-white rounded-3xl border border-rose-200 shadow-card overflow-hidden"
         style="display: none;">
        
        <div class="p-6 sm:p-8 bg-rose-50/50 border-b border-rose-200 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-300 text-rose-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-rose-950 tracking-tight">Zona Privasi &amp; Penghapusan Akun</h2>
                <p class="text-xs text-rose-700 mt-0.5">Penghapusan akun bersifat permanen dan tidak dapat dibatalkan setelah diproses.</p>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <div class="space-y-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                <p>
                    Apabila Anda menghapus akun Lumina Grand Anda:
                </p>
                <ul class="list-disc pl-5 space-y-1.5 text-xs text-stone-600">
                    <li>Seluruh data identitas pribadi, riwayat booking yang telah selesai, serta voucher reservasi akan dihapus permanen.</li>
                    <li>Sesi Anda akan segera dikeluarkan dan akun tidak dapat dipulihkan kembali.</li>
                    <li><strong>Catatan Penting:</strong> Akun <em>tidak dapat dihapus</em> jika Anda saat ini masih memiliki reservasi berstatus aktif (Dipesan atau Sedang Menginap).</li>
                </ul>
            </div>

            <div class="pt-4 border-t border-rose-100 flex items-center justify-between flex-wrap gap-4">
                <span class="text-xs text-stone-500">Kebutuhan data pribadi Anda dilindungi Undang-Undang Privasi Konsumen.</span>
                
                <button type="button" 
                        @click="deleteModalOpen = true"
                        class="px-6 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs sm:text-sm font-bold transition shadow-card flex items-center space-x-2 cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Hapus Akun &amp; Data Saya</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         7. MODAL KONFIRMASI PENGHAPUSAN AKUN
         ==================================================================== -->
    <div x-show="deleteModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-navy-950/80 backdrop-blur-md"
         style="display: none;">
        
        <div class="fixed inset-0" @click="deleteModalOpen = false"></div>

        <div x-show="deleteModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             @click.stop
             class="bg-white w-full max-w-lg rounded-3xl border border-rose-200 shadow-2xl p-6 sm:p-8 space-y-6 relative z-10">
            
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-100 border border-rose-300 text-rose-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-navy-900">Konfirmasi Hapus Akun Permanen</h3>
                    <p class="text-xs text-stone-500 mt-1">Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi akun Anda untuk melanjutkan.</p>
                </div>
            </div>

            <form action="{{ route('pelanggan.profil.destroy') }}" method="POST" class="space-y-5">
                @csrf
                @method('DELETE')

                <div class="space-y-1.5">
                    <label for="password_konfirmasi_hapus" class="block text-xs font-bold text-navy-900 uppercase tracking-wider">
                        Kata Sandi Akun Anda <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           id="password_konfirmasi_hapus" 
                           name="password_konfirmasi_hapus" 
                           required 
                           placeholder="Ketik kata sandi Anda untuk konfirmasi"
                           class="w-full px-4 py-3 rounded-2xl bg-warm-50 border border-warm-200 text-navy-900 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 transition">
                </div>

                <div class="pt-3 border-t border-warm-200 flex items-center justify-end space-x-3">
                    <button type="button" 
                            @click="deleteModalOpen = false" 
                            class="px-5 py-2.5 rounded-xl border border-warm-300 text-stone-600 hover:text-navy-900 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-extrabold transition shadow-md">
                        Hapus Permanen
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
