@extends('layouts.app')

@section('title', 'Formulir Pemesanan Kamar — Lumina Grand')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    tipeKamar: '{{ $tipeKamar ?? 'Standar' }}',
    checkIn: '{{ $checkInDefault ?? date('Y-m-d') }}',
    checkOut: '{{ $checkOutDefault ?? date('Y-m-d', strtotime('+1 day')) }}',
    metodePembayaran: 'QRIS',
    tarifMap: {
        'Standar': 300000,
        'Deluxe': 550000,
        'Suite': 950000
    },
    namaMap: {
        'Standar': 'Suite Standar Heritage',
        'Deluxe': 'Villa Eksekutif Tepi Laut',
        'Suite': 'Penthouse Presidensial'
    },
    kategoriMap: {
        'Standar': 'Heritage Suite',
        'Deluxe': 'Executive Villa',
        'Suite': 'Presidential Penthouse'
    },
    fotoMap: {
        'Standar': '{{ $daftarKamar['Standar']['foto'] }}',
        'Deluxe': '{{ $daftarKamar['Deluxe']['foto'] }}',
        'Suite': '{{ $daftarKamar['Suite']['foto'] }}'
    },
    fallbackFotoMap: {
        'Standar': '{{ $daftarKamar['Standar']['fallback_foto'] }}',
        'Deluxe': '{{ $daftarKamar['Deluxe']['fallback_foto'] }}',
        'Suite': '{{ $daftarKamar['Suite']['fallback_foto'] }}'
    },
    luasMap: {
        'Standar': '45 m²',
        'Deluxe': '65 m²',
        'Suite': '120 m²'
    },
    kapasitasMap: {
        'Standar': '2 Tamu Dewasa',
        'Deluxe': '2 Tamu Dewasa',
        'Suite': '4 Tamu Dewasa'
    },
    ranjangMap: {
        'Standar': '1 Tempat Tidur Queen',
        'Deluxe': '1 Tempat Tidur King',
        'Suite': '1 Super King + 2 Twin Bed'
    },
    pemandanganMap: {
        'Standar': 'Taman Tropis & Kolam Refleksi',
        'Deluxe': 'Panorama Samudra Hindia',
        'Suite': 'Lanskap Samudra & Resor 360°'
    },
    get tarif() {
        return this.tarifMap[this.tipeKamar] || 300000;
    },
    get durasiMalam() {
        if (!this.checkIn || !this.checkOut) return 1;
        const d1 = new Date(this.checkIn);
        const d2 = new Date(this.checkOut);
        const diff = Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
        return diff > 0 ? diff : 1;
    },
    get totalBiaya() {
        return this.durasiMalam * this.tarif;
    },
    formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    },
    formatTanggal(dateStr) {
        if (!dateStr) return '-';
        const d = new Date(dateStr + 'T00:00:00');
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        return d.toLocaleDateString('id-ID', options);
    }
}">

    <!-- ====================================================================
         1. Navigasi Pintas / Breadcrumb Elegan
         ==================================================================== -->
    <div class="flex items-center justify-between text-xs text-stone-600 px-1">
        <a href="{{ route('pelanggan.kamar') }}" class="font-bold text-stone-700 hover:text-navy-900 transition flex items-center space-x-1.5 group">
            <svg class="w-4 h-4 text-gold-600 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Kembali ke Katalog Kamar</span>
        </a>
        <a href="{{ route('pelanggan.dashboard') }}" class="font-bold text-gold-700 hover:text-gold-800 transition flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span>Lihat Riwayat Reservasi Saya</span>
        </a>
    </div>

    <!-- ====================================================================
         2. Formulir Utama Pemesanan (2 Kolom Editorial Arsitektur)
         ==================================================================== -->
    <form action="{{ route('booking.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tipe_kamar" :value="tipeKamar">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- KOLOM KIRI (7 Kolom): Input Form Pemesanan -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Header Formulir -->
                <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-8 shadow-card">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center space-x-2 text-[10px] font-extrabold uppercase tracking-widest text-gold-800 bg-gold-100 border border-gold-300 px-3 py-1 rounded-full mb-2">
                                Reservasi Langsung Resmi
                            </div>
                            <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">
                                Formulir Pemesanan Kamar
                            </h1>
                            <p class="text-xs sm:text-sm text-stone-600 mt-1 leading-relaxed">
                                Tentukan tanggal menginap, konfirmasi profil tamu, dan selesaikan reservasi instan bebas antre.
                            </p>
                        </div>
                        <div class="flex items-center space-x-2 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-2xl self-start sm:self-auto flex-shrink-0">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Konfirmasi Instan</span>
                        </div>
                    </div>
                </div>

                <!-- Bagian 1: Pemilihan Tipe Kamar (Kartu Elegan dengan Foto Mini & Bebas Overflow) -->
                <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-8 shadow-card space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-warm-200">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-navy-900">Pilih Tipe Kamar Akomodasi</h2>
                            <p class="text-xs text-stone-500">Klik kartu untuk menentukan jenis kamar yang ingin Anda pesan.</p>
                        </div>
                        <span class="text-xs font-bold text-gold-600" x-text="namaMap[tipeKamar]"></span>
                    </div>

                    <div class="grid grid-cols-1 gap-3.5">
                        @foreach($daftarKamar as $kamarKey => $kamar)
                            <div @click="tipeKamar = '{{ $kamar['tipe'] }}'"
                                 class="p-4 sm:p-5 rounded-2xl border-2 cursor-pointer transition duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 select-none relative"
                                 :class="tipeKamar === '{{ $kamar['tipe'] }}' 
                                    ? 'border-gold-500 bg-amber-50/25 ring-2 ring-gold-500/15 shadow-sm' 
                                    : 'border-warm-200 hover:border-gold-300 hover:bg-warm-50/50 bg-white'">
                                
                                <!-- Foto Mini & Informasi Kamar -->
                                <div class="flex items-start sm:items-center space-x-4 flex-1 min-w-0">
                                    <!-- Foto Thumbnail -->
                                    <div class="w-20 h-20 sm:w-24 sm:h-20 rounded-xl overflow-hidden bg-navy-950 flex-shrink-0 relative border border-warm-200">
                                        <img src="{{ $kamar['foto'] }}" 
                                             alt="{{ $kamar['nama'] }}" 
                                             onerror="this.onerror=null; this.src='{{ $kamar['fallback_foto'] }}';" 
                                             class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/10"></div>
                                    </div>

                                    <!-- Teks Rincian -->
                                    <div class="min-w-0 flex-1 space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full"
                                                  :class="tipeKamar === '{{ $kamar['tipe'] }}' ? 'bg-gold-500 text-navy-950' : 'bg-warm-100 text-stone-600'">
                                                {{ $kamar['kategori'] ?? ('Tipe ' . $kamar['tipe']) }}
                                            </span>
                                            @if($kamar['unit_tersedia'] > 0)
                                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                                    {{ $kamar['unit_tersedia'] }} Unit Siap
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="font-bold text-sm sm:text-base text-navy-900 truncate">
                                            {{ $kamar['nama'] }}
                                        </h3>

                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-stone-500">
                                            <span class="inline-flex items-center space-x-1">
                                                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                                <span>{{ $kamar['luas'] }}</span>
                                            </span>
                                            <span>&bull;</span>
                                            <span class="inline-flex items-center space-x-1">
                                                <svg class="w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                <span>{{ $kamar['kapasitas'] }}</span>
                                            </span>
                                            <span>&bull;</span>
                                            <span class="truncate">{{ $kamar['ranjang'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tarif & Indikator Pilihan -->
                                <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 pt-3 sm:pt-0 border-warm-200/80 flex-shrink-0">
                                    <div class="text-left sm:text-right">
                                        <span class="text-[10px] uppercase font-bold text-stone-400 block">Tarif / Malam</span>
                                        <div class="text-base sm:text-lg font-extrabold text-navy-900 leading-tight">
                                            Rp {{ number_format($kamar['tarif'], 0, ',', '.') }}
                                        </div>
                                    </div>

                                    <!-- Custom Radio Circle Indicator -->
                                    <div class="sm:mt-2.5 flex items-center justify-center">
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition"
                                             :class="tipeKamar === '{{ $kamar['tipe'] }}' ? 'border-gold-500 bg-gold-500 text-navy-950 shadow-xs' : 'border-stone-300 bg-white'">
                                            <template x-if="tipeKamar === '{{ $kamar['tipe'] }}'">
                                                <svg class="w-3.5 h-3.5 text-navy-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Bagian 2: Periode Tanggal Menginap -->
                <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-8 shadow-card space-y-4">
                    <div class="pb-3 border-b border-warm-200">
                        <h2 class="text-base sm:text-lg font-bold text-navy-900">Periode Tanggal Menginap</h2>
                        <p class="text-xs text-stone-500">Tentukan jadwal kedatangan dan kepulangan Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-navy-900">
                                Tanggal Check-In (Mulai 14:00 WIB) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" name="tanggal_check_in" x-model="checkIn" min="{{ date('Y-m-d') }}" required
                                       class="w-full px-4 py-3 bg-warm-50 border border-warm-200 rounded-2xl text-xs font-bold text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-white transition">
                            </div>
                            <span class="text-[11px] text-stone-500 block">Waktu check-in resmi resor: Pukul 14:00 WIB</span>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-navy-900">
                                Tanggal Check-Out (Maksimal 12:00 WIB) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" name="tanggal_check_out" x-model="checkOut" :min="checkIn" required
                                       class="w-full px-4 py-3 bg-warm-50 border border-warm-200 rounded-2xl text-xs font-bold text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-white transition">
                            </div>
                            <span class="text-[11px] text-stone-500 block">Waktu check-out resmi: Maksimal 12:00 WIB</span>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Konfirmasi Data Tamu Pemesan -->
                <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-8 shadow-card space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-warm-200">
                        <div>
                            <h2 class="text-base sm:text-lg font-bold text-navy-900">Data Identitas Tamu Pemesan</h2>
                            <p class="text-xs text-stone-500">Data ini digunakan untuk pencetakan voucher resmi dan konfirmasi saat check-in.</p>
                        </div>
                        <span class="text-[11px] font-bold text-gold-700 bg-gold-50 px-2.5 py-1 rounded-full border border-gold-200">
                            Terisi Otomatis
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-navy-900">
                                Nama Lengkap Pemesan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" required value="{{ $user->nama }}"
                                   class="w-full px-4 py-3 bg-warm-50 border border-warm-200 rounded-2xl text-xs font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-white transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-navy-900">
                                Nomor NIK / Paspor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="no_identitas" required value="{{ $user->no_identitas ?? '' }}" placeholder="Contoh: 3201xxxxxxxxxxxx"
                                   class="w-full px-4 py-3 bg-warm-50 border border-warm-200 rounded-2xl text-xs font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-white transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-navy-900">
                                Alamat Email Aktif <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" required value="{{ $user->email }}"
                                   class="w-full px-4 py-3 bg-warm-50 border border-warm-200 rounded-2xl text-xs font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-white transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-navy-900">
                                Nomor WhatsApp / HP <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="no_hp" required value="{{ $user->no_hp ?? '' }}" placeholder="Contoh: 08123456789"
                                   class="w-full px-4 py-3 bg-warm-50 border border-warm-200 rounded-2xl text-xs font-semibold text-navy-900 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:bg-white transition">
                        </div>
                    </div>
                </div>

                <!-- Bagian 4: Pilihan Metode Pembayaran -->
                <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-8 shadow-card space-y-4">
                    <div class="pb-3 border-b border-warm-200">
                        <h2 class="text-base sm:text-lg font-bold text-navy-900">Pilihan Metode Pembayaran</h2>
                        <p class="text-xs text-stone-500">Pilih kanal pembayaran resmi yang paling nyaman untuk Anda.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex flex-col justify-between"
                               :class="metodePembayaran === 'QRIS' ? 'border-gold-500 bg-amber-50/25 ring-1 ring-gold-500/20' : 'border-warm-200 bg-white hover:border-gold-300'">
                            <input type="radio" name="metode_pembayaran" value="QRIS" x-model="metodePembayaran" class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                      :class="metodePembayaran === 'QRIS' ? 'border-gold-500 bg-gold-500' : 'border-stone-300'">
                                    <template x-if="metodePembayaran === 'QRIS'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-navy-950"></span>
                                    </template>
                                </span>
                            </div>
                            <div>
                                <span class="font-bold text-xs text-navy-900 block">QRIS Instan</span>
                                <span class="text-[10px] text-stone-500 block mt-0.5">BCA, Mandiri, GoPay, OVO, ShopeePay</span>
                            </div>
                        </label>

                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex flex-col justify-between"
                               :class="metodePembayaran === 'Transfer' ? 'border-gold-500 bg-amber-50/25 ring-1 ring-gold-500/20' : 'border-warm-200 bg-white hover:border-gold-300'">
                            <input type="radio" name="metode_pembayaran" value="Transfer" x-model="metodePembayaran" class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                      :class="metodePembayaran === 'Transfer' ? 'border-gold-500 bg-gold-500' : 'border-stone-300'">
                                    <template x-if="metodePembayaran === 'Transfer'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-navy-950"></span>
                                    </template>
                                </span>
                            </div>
                            <div>
                                <span class="font-bold text-xs text-navy-900 block">Transfer Virtual Account</span>
                                <span class="text-[10px] text-stone-500 block mt-0.5">Verifikasi otomatis bank 24 jam</span>
                            </div>
                        </label>

                        <label class="p-4 rounded-2xl border-2 cursor-pointer transition flex flex-col justify-between"
                               :class="metodePembayaran === 'Tunai' ? 'border-gold-500 bg-amber-50/25 ring-1 ring-gold-500/20' : 'border-warm-200 bg-white hover:border-gold-300'">
                            <input type="radio" name="metode_pembayaran" value="Tunai" x-model="metodePembayaran" class="sr-only">
                            <div class="flex items-center justify-between mb-2">
                                <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center"
                                      :class="metodePembayaran === 'Tunai' ? 'border-gold-500 bg-gold-500' : 'border-stone-300'">
                                    <template x-if="metodePembayaran === 'Tunai'">
                                        <span class="w-1.5 h-1.5 rounded-full bg-navy-950"></span>
                                    </template>
                                </span>
                            </div>
                            <div>
                                <span class="font-bold text-xs text-navy-900 block">Bayar di Resepsionis</span>
                                <span class="text-[10px] text-stone-500 block mt-0.5">Pelunasan langsung saat check-in</span>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN (5 Kolom): Sticky Ringkasan Reservasi & Konfirmasi -->
            <div class="lg:col-span-5 sticky top-24 space-y-6">

                <!-- Kartu Rincian Reservasi & Tagihan -->
                <div class="bg-navy-950 text-white rounded-3xl sm:rounded-4xl border border-gold-500/30 p-6 sm:p-7 shadow-xl overflow-hidden relative">
                    <div class="absolute -top-24 -right-24 w-48 h-48 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Header Ringkasan -->
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-gold-400"></span>
                            <h2 class="text-sm font-extrabold uppercase tracking-widest text-gold-300">Ringkasan Reservasi</h2>
                        </div>
                        <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider bg-white/5 px-2.5 py-1 rounded-full border border-white/10">
                            1 Kamar
                        </span>
                    </div>

                    <!-- Pratinjau Foto Kamar Terpilih -->
                    <div class="mt-4 rounded-2xl overflow-hidden relative h-40 bg-navy-900 border border-white/10">
                        <img :src="fotoMap[tipeKamar]" 
                             :alt="namaMap[tipeKamar]"
                             :onerror="'this.onerror=null; this.src=' + JSON.stringify(fallbackFotoMap[tipeKamar])" 
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/40 to-transparent"></div>
                        <div class="absolute bottom-3 left-3 right-3 flex items-end justify-between">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest text-gold-400 block" x-text="kategoriMap[tipeKamar]"></span>
                                <h3 class="font-extrabold text-base text-white leading-tight" x-text="namaMap[tipeKamar]"></h3>
                            </div>
                            <span class="text-xs text-stone-300 font-medium bg-black/40 px-2 py-0.5 rounded backdrop-blur-xs" x-text="luasMap[tipeKamar]"></span>
                        </div>
                    </div>

                    <!-- Jadwal Menginap & Durasi -->
                    <div class="mt-5 p-4 rounded-2xl bg-white/5 border border-white/10 space-y-3 text-xs">
                        <div class="flex items-center justify-between text-stone-300">
                            <span>Check-In:</span>
                            <span class="font-bold text-white" x-text="formatTanggal(checkIn)"></span>
                        </div>
                        <div class="flex items-center justify-between text-stone-300">
                            <span>Check-Out:</span>
                            <span class="font-bold text-white" x-text="formatTanggal(checkOut)"></span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-white/10 text-gold-300 font-bold">
                            <span>Durasi Menginap:</span>
                            <span x-text="durasiMalam + ' Malam'"></span>
                        </div>
                    </div>

                    <!-- Kalkulasi Rincian Biaya -->
                    <div class="mt-5 space-y-2.5 text-xs text-stone-300">
                        <div class="flex items-center justify-between">
                            <span x-text="formatRupiah(tarif) + ' &times; ' + durasiMalam + ' malam'"></span>
                            <span class="font-semibold text-white" x-text="formatRupiah(totalBiaya)"></span>
                        </div>
                        <div class="flex items-center justify-between text-stone-400 text-[11px]">
                            <span>Pajak Daerah &amp; Layanan Resor:</span>
                            <span class="text-emerald-400 font-semibold">Termasuk</span>
                        </div>
                        <div class="flex items-center justify-between text-stone-400 text-[11px]">
                            <span>Sarapan Pagi Mewah Setiap Hari:</span>
                            <span class="text-emerald-400 font-semibold">Inklusif</span>
                        </div>
                    </div>

                    <!-- Total Tagihan -->
                    <div class="mt-5 pt-4 border-t border-dashed border-gold-500/40 flex items-baseline justify-between">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-stone-400 block">Total Tagihan Bersih</span>
                            <span class="text-xs text-gold-400">Garansi Tarif Resmi Hotel</span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black text-gold-300 tracking-tight" x-text="formatRupiah(totalBiaya)"></div>
                    </div>

                    <!-- Tombol Konfirmasi Reservasi -->
                    <div class="mt-6 space-y-3">
                        <button type="submit" 
                                class="w-full bg-gold-500 hover:bg-gold-400 text-navy-950 font-extrabold text-xs sm:text-sm py-4 px-6 rounded-2xl transition duration-200 flex items-center justify-center space-x-2 shadow-hover cursor-pointer group">
                            <span>Konfirmasi &amp; Proses Reservasi Sekarang</span>
                            <svg class="w-4 h-4 text-navy-950 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                        <p class="text-[11px] text-stone-400 text-center leading-relaxed">
                            Dengan mengonfirmasi, voucher pemesanan resmi akan langsung diterbitkan ke akun Anda.
                        </p>
                    </div>

                    <!-- Garansi & Privilese Singkat -->
                    <div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-2 gap-3 text-[11px] text-stone-300">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Bebas Biaya Admin</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gold-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <span>Transaksi Terenkripsi</span>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </form>

</div>
@endsection
