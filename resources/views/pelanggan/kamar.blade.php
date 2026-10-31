@extends('layouts.app')

@section('title', 'Katalog Kamar Akomodasi — Lumina Grand')

@section('content')
<div class="max-w-7xl mx-auto space-y-8" 
     x-data="{ 
         selectedFilter: 'all',
         modalOpen: false,
         activeKamar: null,
         kamarData: @js($daftarKamar),
         openModal(slug) {
             this.activeKamar = this.kamarData[slug];
             this.modalOpen = true;
             document.body.style.overflow = 'hidden';
         },
         closeModal() {
             this.modalOpen = false;
             document.body.style.overflow = 'auto';
         }
     }"
     @keydown.escape.window="closeModal()">

    <!-- ====================================================================
         1. Hero Header & Pengantar Tamu
         ==================================================================== -->
    <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-10 shadow-card relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-gold-100/40 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="flex items-center space-x-2 mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-gold-100 text-gold-800 border border-gold-300">
                        Katalog Kamar Eksklusif
                    </span>
                    <span class="text-xs text-stone-400">&bull;</span>
                    <span class="text-xs font-semibold text-stone-500">
                        Tamu: <strong class="text-navy-900">{{ $user->nama }}</strong>
                    </span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-navy-900 tracking-tight leading-tight">
                    Pilihan Kamar &amp; Suite Kemewahan
                </h1>
                <p class="text-sm sm:text-base text-stone-600 mt-2 leading-relaxed">
                    Setiap ruang dirancang dengan perpaduan wibawa modern dan kehangatan personal. Klik kartu kamar untuk melihat seluruh fasilitas lengkap sebelum melanjutkan pemesanan resmi.
                </p>
            </div>

            <!-- Quick Metrics Strip -->
            <div class="flex flex-wrap sm:flex-nowrap gap-3 bg-warm-50 p-4 rounded-2xl border border-warm-200 self-start lg:self-center">
                <div class="pr-5 border-r border-warm-200/80">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kamar Tersedia</span>
                    <div class="flex items-center space-x-1.5 mt-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="text-lg font-extrabold text-navy-900">{{ $totalTersedia }} Unit Bersih</span>
                    </div>
                </div>
                <div class="pr-5 border-r border-warm-200/80">
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Koleksi Suite</span>
                    <span class="text-lg font-extrabold text-navy-900 block mt-1">9 Pilihan Kamar</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Jaminan Tarif</span>
                    <span class="text-xs font-bold text-gold-700 block mt-2">Tarif Resmi Langsung</span>
                </div>
            </div>
        </div>

        <!-- Filter Tab Pill Bar -->
        <div class="mt-8 pt-6 border-t border-warm-200 flex flex-wrap items-center justify-between gap-4">
            <div class="inline-flex p-1 bg-warm-100 rounded-2xl border border-warm-200 text-xs font-bold">
                <button type="button" @click="selectedFilter = 'all'" 
                        :class="selectedFilter === 'all' ? 'bg-white text-navy-900 shadow-sm' : 'text-stone-600 hover:text-navy-900'"
                        class="px-4 py-2 rounded-xl transition cursor-pointer flex items-center space-x-1.5">
                    <span>Semua Pilihan</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="selectedFilter === 'all' ? 'bg-navy-900 text-gold-300' : 'bg-warm-200 text-stone-600'">9</span>
                </button>
                <button type="button" @click="selectedFilter = 'Standar'" 
                        :class="selectedFilter === 'Standar' ? 'bg-white text-navy-900 shadow-sm' : 'text-stone-600 hover:text-navy-900'"
                        class="px-4 py-2 rounded-xl transition cursor-pointer flex items-center space-x-1.5">
                    <span>Heritage Suite</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="selectedFilter === 'Standar' ? 'bg-navy-900 text-gold-300' : 'bg-warm-200 text-stone-600'">3</span>
                </button>
                <button type="button" @click="selectedFilter = 'Deluxe'" 
                        :class="selectedFilter === 'Deluxe' ? 'bg-white text-navy-900 shadow-sm' : 'text-stone-600 hover:text-navy-900'"
                        class="px-4 py-2 rounded-xl transition cursor-pointer flex items-center space-x-1.5">
                    <span>Executive Villa</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="selectedFilter === 'Deluxe' ? 'bg-navy-900 text-gold-300' : 'bg-warm-200 text-stone-600'">3</span>
                </button>
                <button type="button" @click="selectedFilter = 'Suite'" 
                        :class="selectedFilter === 'Suite' ? 'bg-white text-navy-900 shadow-sm' : 'text-stone-600 hover:text-navy-900'"
                        class="px-4 py-2 rounded-xl transition cursor-pointer flex items-center space-x-1.5">
                    <span>Presidential Penthouse</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                          :class="selectedFilter === 'Suite' ? 'bg-navy-900 text-gold-300' : 'bg-warm-200 text-stone-600'">3</span>
                </button>
            </div>

            <div class="flex items-center space-x-3 text-xs">
                <a href="{{ route('booking.form') }}" class="font-bold text-navy-800 hover:text-navy-950 flex items-center space-x-1.5 transition">
                    <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Buka Formulir Pemesanan</span>
                </a>
                <span class="text-stone-300">|</span>
                <a href="{{ route('pelanggan.dashboard') }}" class="font-bold text-gold-600 hover:text-gold-700 flex items-center space-x-1.5 transition">
                    <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Riwayat Reservasi Saya</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         2. Grid Kartu Kamar (Luxury Architectural Cards)
         ==================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @foreach($daftarKamar as $slug => $kamar)
            <div x-show="selectedFilter === 'all' || selectedFilter === '{{ $kamar['tipe'] }}'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 @click="openModal('{{ $slug }}')"
                 class="bg-white rounded-3xl sm:rounded-4xl border border-warm-200 overflow-hidden shadow-card hover:shadow-hover transition duration-300 flex flex-col group cursor-pointer relative">
                
                <!-- Gambar Kamar & Badge Overlay -->
                <div class="relative w-full h-64 sm:h-72 overflow-hidden bg-navy-950">
                    <img src="{{ $kamar['foto'] }}" 
                         alt="{{ $kamar['nama'] }}" 
                         onerror="this.onerror=null; this.src='{{ $kamar['fallback_foto'] }}';" 
                         class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-700 ease-out">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-black/30"></div>

                    <!-- Badge Kategori Tipe Kamar (Top Left) -->
                    <div class="absolute top-4 left-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-navy-900/90 text-gold-300 backdrop-blur-md border border-gold-500/30 shadow-sm">
                            {{ $kamar['kategori'] }}
                        </span>
                    </div>

                    <!-- Badge Ketersediaan Real-Time (Top Right) -->
                    <div class="absolute top-4 right-4">
                        @if($kamar['unit_tersedia'] > 0)
                            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-950/85 text-emerald-300 backdrop-blur-md border border-emerald-500/40 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>{{ $kamar['unit_tersedia'] }} Unit Tersedia</span>
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-rose-950/85 text-rose-300 backdrop-blur-md border border-rose-500/40 shadow-sm">
                                Penuh Hari Ini
                            </span>
                        @endif
                    </div>

                    <!-- Petunjuk Klik untuk Detail (Center on Hover) -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300 bg-navy-950/40 backdrop-blur-xs">
                        <span class="inline-flex items-center space-x-2 px-4 py-2 rounded-full bg-white/95 text-navy-900 font-extrabold text-xs shadow-lg transform translate-y-2 group-hover:translate-y-0 transition duration-300">
                            <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Klik untuk Lihat Fasilitas</span>
                        </span>
                    </div>

                    <!-- Tarif Overlay (Bottom Left) -->
                    <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-widest text-gold-300 block">Tarif Resmi</span>
                            <div class="flex items-baseline space-x-1 text-white">
                                <span class="text-2xl font-extrabold tracking-tight">Rp {{ number_format($kamar['tarif'], 0, ',', '.') }}</span>
                                <span class="text-xs text-stone-300 font-medium">/ malam</span>
                            </div>
                        </div>
                        <span class="text-[10px] font-medium text-stone-300 bg-black/40 px-2 py-0.5 rounded-md backdrop-blur-xs">
                            Termasuk Pajak
                        </span>
                    </div>
                </div>

                <!-- Informasi & Rincian Kamar -->
                <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-navy-900 group-hover:text-gold-600 transition">
                                {{ $kamar['nama'] }}
                            </h2>
                            <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                                {{ $kamar['tagline'] }}
                            </p>
                        </div>

                        <!-- Spesifikasi Teknis Kamar (Grid Bento Ringkas) -->
                        <div class="grid grid-cols-2 gap-2.5 p-3.5 bg-warm-50 rounded-2xl border border-warm-200 text-xs">
                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-white border border-warm-200 flex items-center justify-center text-navy-800 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                </div>
                                <div class="overflow-hidden">
                                    <span class="text-[10px] text-stone-400 block uppercase font-bold leading-none">Luas</span>
                                    <span class="font-bold text-navy-900 truncate block mt-0.5">{{ $kamar['luas'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-white border border-warm-200 flex items-center justify-center text-navy-800 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                                <div class="overflow-hidden">
                                    <span class="text-[10px] text-stone-400 block uppercase font-bold leading-none">Kapasitas</span>
                                    <span class="font-bold text-navy-900 truncate block mt-0.5">{{ $kamar['kapasitas'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-white border border-warm-200 flex items-center justify-center text-navy-800 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                                <div class="overflow-hidden">
                                    <span class="text-[10px] text-stone-400 block uppercase font-bold leading-none">Ranjang</span>
                                    <span class="font-bold text-navy-900 truncate block mt-0.5">{{ $kamar['ranjang'] }}</span>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div class="w-7 h-7 rounded-lg bg-white border border-warm-200 flex items-center justify-center text-navy-800 flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </div>
                                <div class="overflow-hidden">
                                    <span class="text-[10px] text-stone-400 block uppercase font-bold leading-none">Pemandangan</span>
                                    <span class="font-bold text-navy-900 truncate block mt-0.5">{{ $kamar['pemandangan'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Fasilitas Inklusif Bintang 5 -->
                        <div class="space-y-2 pt-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400 block">
                                Fasilitas Inklusif Unggulan:
                            </span>
                            <ul class="space-y-1.5 text-xs text-stone-600">
                                @foreach($kamar['fasilitas'] as $fasilitas)
                                    <li class="flex items-start space-x-2">
                                        <svg class="w-4 h-4 text-gold-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="leading-tight">{{ $fasilitas }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Tombol Aksi: Lihat Fasilitas & Detail -->
                    <div class="pt-4 border-t border-warm-200">
                        @if($kamar['unit_tersedia'] > 0)
                            <button type="button" 
                                    @click.stop="openModal('{{ $slug }}')" 
                                    class="w-full inline-flex items-center justify-center space-x-2 bg-navy-900 hover:bg-black text-white text-xs sm:text-sm font-bold py-3.5 px-6 rounded-2xl transition duration-200 shadow-card hover:shadow-hover group/btn cursor-pointer">
                                <span>Lihat Fasilitas &amp; Pesan</span>
                                <svg class="w-4 h-4 text-gold-300 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        @else
                            <button type="button" disabled 
                                    class="w-full inline-flex items-center justify-center space-x-2 bg-stone-100 border border-warm-200 text-stone-400 text-xs sm:text-sm font-bold py-3.5 px-6 rounded-2xl cursor-not-allowed">
                                <span>Semua Unit Terisi Penuh</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- ====================================================================
         3. Modal Interaktif Detail & Fasilitas Lengkap Kamar
         ==================================================================== -->
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-navy-950/80 backdrop-blur-md"
         style="display: none;">
        
        <!-- Backdrop dismiss area -->
        <div class="fixed inset-0" @click="closeModal()"></div>

        <!-- Dialog Box Container -->
        <div x-show="modalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 transform scale-95 translate-y-4"
             @click.stop
             class="bg-white w-full max-w-4xl max-h-[90vh] rounded-3xl sm:rounded-4xl border border-warm-200 shadow-2xl flex flex-col overflow-hidden relative z-10">

            <!-- Modal Header dengan Gambar & Tombol Tutup -->
            <div class="relative w-full h-56 sm:h-72 overflow-hidden bg-navy-950 flex-shrink-0">
                <template x-if="activeKamar">
                    <img :src="activeKamar.foto" 
                         :alt="activeKamar.nama" 
                         class="w-full h-full object-cover object-center">
                </template>
                <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/40 to-black/30"></div>

                <!-- Tombol Tutup Modal (Top Right) -->
                <button type="button" 
                        @click="closeModal()" 
                        class="absolute top-4 right-4 w-10 h-10 rounded-full bg-navy-900/80 text-white hover:bg-black hover:scale-105 transition flex items-center justify-center border border-white/20 backdrop-blur-md z-20 cursor-pointer shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <!-- Badge Kategori & Ketersediaan (Top Left) -->
                <div class="absolute top-4 left-4 flex items-center space-x-2">
                    <span x-text="activeKamar ? activeKamar.kategori : ''" 
                          class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-navy-900/90 text-gold-300 backdrop-blur-md border border-gold-500/30 shadow-sm">
                    </span>
                    <template x-if="activeKamar && activeKamar.unit_tersedia > 0">
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-950/85 text-emerald-300 backdrop-blur-md border border-emerald-500/40 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span x-text="activeKamar.unit_tersedia + ' Unit Tersedia'"></span>
                        </span>
                    </template>
                </div>

                <!-- Judul & Tagline di Header Gambar -->
                <div class="absolute bottom-4 left-6 right-6 text-white">
                    <h2 x-text="activeKamar ? activeKamar.nama : ''" 
                        class="text-2xl sm:text-3xl font-extrabold tracking-tight drop-shadow-sm"></h2>
                    <p x-text="activeKamar ? activeKamar.tagline : ''" 
                       class="text-xs sm:text-sm text-stone-200 mt-1 max-w-2xl line-clamp-2 drop-shadow-xs"></p>
                </div>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="overflow-y-auto p-6 sm:p-8 space-y-7 flex-1 text-navy-900">

                <!-- 1. Bento Spesifikasi Ruangan -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-warm-50 p-4 rounded-2xl border border-warm-200 text-xs">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white border border-warm-200 flex items-center justify-center text-gold-600 flex-shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block leading-tight">Luas Kamar</span>
                            <span x-text="activeKamar ? activeKamar.luas : ''" class="font-bold text-navy-900 block mt-0.5"></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white border border-warm-200 flex items-center justify-center text-gold-600 flex-shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block leading-tight">Kapasitas Tamu</span>
                            <span x-text="activeKamar ? activeKamar.kapasitas : ''" class="font-bold text-navy-900 block mt-0.5"></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white border border-warm-200 flex items-center justify-center text-gold-600 flex-shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block leading-tight">Konfigurasi Ranjang</span>
                            <span x-text="activeKamar ? activeKamar.ranjang : ''" class="font-bold text-navy-900 block mt-0.5"></span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white border border-warm-200 flex items-center justify-center text-gold-600 flex-shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-stone-400 block leading-tight">Pemandangan</span>
                            <span x-text="activeKamar ? activeKamar.pemandangan : ''" class="font-bold text-navy-900 block mt-0.5 truncate max-w-[120px]"></span>
                        </div>
                    </div>
                </div>

                <!-- 2. Deskripsi Ringkas Kamar -->
                <div>
                    <p x-text="activeKamar ? activeKamar.deskripsi_lengkap : ''" 
                       class="text-xs sm:text-sm text-stone-700 leading-relaxed bg-warm-50/80 px-4 py-3 rounded-2xl border border-warm-200">
                    </p>
                </div>

                <!-- 3. Daftar Fasilitas Lengkap per Kategori (Bento 2 Kolom) -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-warm-200 pb-2">
                        <h3 class="text-sm sm:text-base font-extrabold text-navy-900 flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Daftar Fasilitas Lengkap Kamar</span>
                        </h3>
                        <span class="text-[11px] font-semibold text-stone-500">Standar Bintang 5 Lumina Grand</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-if="activeKamar && activeKamar.fasilitas_lengkap">
                            <template x-for="(items, kategoriName) in activeKamar.fasilitas_lengkap" :key="kategoriName">
                                <div class="bg-warm-50/60 p-4 sm:p-5 rounded-2xl border border-warm-200 space-y-3">
                                    <div class="flex items-center space-x-2 text-xs font-extrabold text-navy-950 uppercase tracking-wider">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gold-600"></span>
                                        <span x-text="kategoriName"></span>
                                    </div>
                                    <ul class="space-y-2 text-xs text-stone-600">
                                        <template x-for="item in items" :key="item">
                                            <li class="flex items-start space-x-2">
                                                <svg class="w-3.5 h-3.5 text-gold-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                <span x-text="item" class="leading-tight"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>

                <!-- 4. Privilese & Garansi Pemesanan Resmi -->
                <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-200/80 flex items-center space-x-3 text-xs text-emerald-900">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <span class="font-bold block">Garansi Tarif Resmi &amp; Konfirmasi Instan</span>
                        <span class="text-emerald-800/80 text-[11px] block mt-0.5">Termasuk sarapan pagi, akses seluruh fasilitas resor, serta jaminan kamar siap dalam kondisi bersih terawat.</span>
                    </div>
                </div>

            </div>

            <!-- Modal Footer (Sticky Bottom Action Bar) -->
            <div class="p-5 sm:p-6 bg-warm-50 border-t border-warm-200 flex flex-col sm:flex-row items-center justify-between gap-4 flex-shrink-0">
                <div>
                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest block">Tarif Menginap Resmi</span>
                    <div class="flex items-baseline space-x-1.5 mt-0.5">
                        <span x-text="activeKamar ? 'Rp ' + Number(activeKamar.tarif).toLocaleString('id-ID') : ''" 
                              class="text-2xl font-extrabold text-navy-900"></span>
                        <span class="text-xs text-stone-500 font-semibold">/ malam</span>
                        <span class="text-[10px] text-stone-500 bg-warm-200 px-2 py-0.5 rounded-md font-medium ml-1">Pajak Termasuk</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <button type="button" 
                            @click="closeModal()" 
                            class="w-full sm:w-auto px-5 py-3 rounded-2xl border border-warm-300 text-stone-600 hover:text-navy-900 hover:bg-white text-xs font-bold transition cursor-pointer">
                        Tutup
                    </button>
                    
                    <a :href="'{{ route('booking.form') }}?tipe=' + (activeKamar ? activeKamar.tipe : '')" 
                       class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 bg-navy-900 hover:bg-black text-white text-xs sm:text-sm font-extrabold py-3 px-6 rounded-2xl transition duration-200 shadow-card hover:shadow-hover cursor-pointer group/btn">
                        <span>Pesan Kamar Ini</span>
                        <svg class="w-4 h-4 text-gold-300 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
