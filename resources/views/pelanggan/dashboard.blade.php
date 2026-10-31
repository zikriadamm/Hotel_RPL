@extends('layouts.app')

@section('title', 'Portal Pelanggan & Riwayat Reservasi — Lumina Grand')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    <!-- ====================================================================
         1. Header Profil & Identitas Tamu
         ==================================================================== -->
    <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-8 shadow-card">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-start sm:items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-navy-900 border border-gold-500/30 text-gold-300 flex items-center justify-center font-bold text-2xl flex-shrink-0 shadow-md">
                    {{ substr($user->nama, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-gold-100 text-gold-700 border border-gold-300">
                            Akun Tamu Terverifikasi
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight mt-1">
                        Selamat Datang, {{ $user->nama }}
                    </h1>
                    <p class="text-xs sm:text-sm text-stone-500 mt-0.5">
                        Kelola seluruh riwayat reservasi Anda, pantau status menginap, dan cetak voucher resmi kapan saja.
                    </p>
                </div>
            </div>

            <!-- Ringkasan Identitas Tamu -->
            <div class="flex flex-wrap sm:flex-nowrap gap-3 bg-warm-50 p-4 rounded-2xl border border-warm-200 text-xs">
                <div class="pr-4 border-r border-warm-200/80">
                    <span class="text-[10px] font-bold text-stone-500 uppercase block">Alamat Email</span>
                    <span class="font-bold text-navy-900 block mt-0.5">{{ $user->email }}</span>
                </div>
                <div class="pr-4 border-r border-warm-200/80">
                    <span class="text-[10px] font-bold text-stone-500 uppercase block">Nomor NIK / Paspor</span>
                    <span class="font-bold text-navy-900 block mt-0.5">{{ $user->no_identitas ?? 'Belum Diisi' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-stone-500 uppercase block">WhatsApp / Kontak</span>
                    <span class="font-bold text-navy-900 block mt-0.5">{{ $user->no_hp ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         2. Statistik Personal Tamu (3 Bento Cards Ringkas)
         ==================================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-warm-200 p-5 rounded-3xl shadow-card flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Total Reservasi</span>
                <span class="text-3xl font-extrabold text-navy-900 mt-1 block">{{ $stats['total_booking'] }} Kali</span>
                <span class="text-[11px] text-stone-500 mt-0.5 block">Riwayat pemesanan terdaftar</span>
            </div>
            <div class="w-12 h-12 rounded-full bg-warm-100 border border-warm-200 flex items-center justify-center text-navy-800">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
        </div>

        <div class="bg-amber-50/70 border border-amber-200 p-5 rounded-3xl shadow-card flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-900 uppercase tracking-wider block">Reservasi Aktif</span>
                <span class="text-3xl font-extrabold text-amber-900 mt-1 block">{{ $stats['aktif'] }} Unit</span>
                <span class="text-[11px] text-amber-800/80 mt-0.5 block">Dipesan / Sedang Menginap</span>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/80 border border-amber-300 flex items-center justify-center text-amber-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <div class="bg-emerald-50/70 border border-emerald-200 p-5 rounded-3xl shadow-card flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-900 uppercase tracking-wider block">Selesai Menginap</span>
                <span class="text-3xl font-extrabold text-emerald-900 mt-1 block">{{ $stats['selesai'] }} Kali</span>
                <span class="text-[11px] text-emerald-800/80 mt-0.5 block">Check-out telah diselesaikan</span>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/80 border border-emerald-300 flex items-center justify-center text-emerald-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </div>

    <!-- ====================================================================
         3. RIWAYAT RESERVASI SAYA (HALAMAN KHUSUS RIWAYAT PESANAN)
         ==================================================================== -->
    <div class="bg-white rounded-3xl border border-warm-200 shadow-card overflow-hidden">
        <div class="p-6 border-b border-warm-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span>
                    <h2 class="text-xl font-bold text-navy-900">Riwayat Reservasi Saya</h2>
                </div>
                <p class="text-xs text-stone-500 mt-0.5">Daftar lengkap pemesanan kamar yang pernah Anda lakukan di Lumina Grand Hotel &amp; Resor.</p>
            </div>
            <a href="{{ route('pelanggan.kamar') }}" class="inline-flex items-center space-x-2 bg-navy-900 hover:bg-black text-white text-xs font-bold px-5 py-2.5 rounded-full transition shadow-xs cursor-pointer self-start sm:self-auto">
                <svg class="w-4 h-4 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Pilih &amp; Pesan Kamar</span>
            </a>
        </div>

        @if($riwayatPemesanan->isEmpty())
            <div class="p-12 text-center bg-warm-50/50">
                <div class="w-16 h-16 rounded-full bg-warm-100 border border-warm-200 text-stone-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-navy-900">Belum Ada Riwayat Reservasi</h3>
                <p class="text-xs text-stone-500 max-w-md mx-auto mt-1 mb-5">
                    Anda belum memiliki riwayat reservasi kamar. Jelajahi katalog kamar kami untuk memilih suite idaman Anda.
                </p>
                <a href="{{ route('pelanggan.kamar') }}" class="bg-gold-500 hover:bg-gold-600 text-navy-950 font-bold text-xs px-6 py-3 rounded-full shadow transition inline-flex items-center space-x-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span>Jelajahi Katalog Kamar</span>
                </a>
                    <span>Buka Formulir Pemesanan Kamar &rarr;</span>
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-warm-100 uppercase text-[10px] font-extrabold text-stone-600 border-b border-warm-200 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Kode Reservasi</th>
                            <th class="px-6 py-4">Akomodasi Kamar</th>
                            <th class="px-6 py-4">Periode Menginap</th>
                            <th class="px-6 py-4">Total Tagihan</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-warm-200 text-stone-700">
                        @foreach($riwayatPemesanan as $pemesanan)
                            @php
                                $checkInCarbon = \Carbon\Carbon::parse($pemesanan->tanggal_check_in);
                                $checkOutCarbon = \Carbon\Carbon::parse($pemesanan->tanggal_check_out);
                                $durasi = max(1, $checkInCarbon->diffInDays($checkOutCarbon));
                            @endphp
                            <tr class="hover:bg-warm-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-navy-900">
                                    <div class="text-xs">{{ $pemesanan->kode_booking }}</div>
                                    <div class="text-[10px] text-stone-500 font-sans mt-0.5">
                                        Dipesan: {{ $pemesanan->created_at->format('d M Y, H:i') }} WIB
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-navy-900">Kamar {{ $pemesanan->kamar->nomor_kamar ?? '-' }}</div>
                                    <div class="text-[11px] text-gold-600 font-semibold">{{ $pemesanan->kamar->tipe ?? 'Tipe Standar' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-navy-900">
                                        {{ $checkInCarbon->format('d M Y') }} &rarr; {{ $checkOutCarbon->format('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-stone-500 mt-0.5">Durasi: <strong>{{ $durasi }} Malam</strong></div>
                                </td>
                                <td class="px-6 py-4 font-bold text-navy-900">
                                    <div class="text-xs">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</div>
                                    @if($pemesanan->pembayaran)
                                        <div class="text-[10px] text-emerald-600 font-bold mt-0.5 flex items-center space-x-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span>Lunas ({{ $pemesanan->pembayaran->metode_pembayaran }})</span>
                                        </div>
                                    @else
                                        <div class="text-[10px] text-amber-600 font-bold mt-0.5">Belum Bayar</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($pemesanan->status === 'Dipesan')
                                        @if($pemesanan->pembayaran)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                                                Lunas (Terkonfirmasi)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600 mr-1.5 animate-pulse"></span>
                                                Menunggu Pembayaran
                                            </span>
                                        @endif
                                    @elseif($pemesanan->status === 'Check-in')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                                            Sedang Menginap
                                        </span>
                                    @elseif($pemesanan->status === 'Check-out')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-stone-100 text-stone-700 border border-stone-200">
                                            Selesai Menginap
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-red-100 text-red-800 border border-red-200">
                                            Dibatalkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @if(!$pemesanan->pembayaran && $pemesanan->status === 'Dipesan')
                                        <a href="{{ route('booking.bayar', $pemesanan->id) }}" 
                                           class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-full bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold shadow-xs transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                            <span>Bayar QRIS</span>
                                        </a>
                                    @endif

                                    <a href="{{ route('booking.sukses', $pemesanan->id) }}" 
                                       class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-full bg-navy-800 hover:bg-black text-white text-[11px] font-bold shadow-xs transition">
                                        <svg class="w-3.5 h-3.5 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        <span>Voucher Resmi</span>
                                    </a>

                                    @if($pemesanan->status === 'Dipesan')
                                        <form action="{{ route('pelanggan.pemesanan.batal', $pemesanan->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan reservasi {{ $pemesanan->kode_booking }}?')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-[11px] font-bold transition cursor-pointer">
                                                <span>Batalkan</span>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- ====================================================================
         4. Banner Pintasan ke Halaman Khusus Katalog Kamar
         ==================================================================== -->
    <div class="bg-gradient-to-r from-navy-950 via-navy-900 to-navy-950 text-white rounded-3xl p-6 sm:p-8 border border-gold-500/30 shadow-card flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-gold-500/20 border border-gold-400/40 text-gold-300 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-base sm:text-lg text-white">Ingin Menambah Reservasi Kamar Baru?</h3>
                <p class="text-xs text-stone-300 mt-1 leading-relaxed">
                    Jelajahi pilihan lengkap: <strong>Suite Standar Heritage</strong>, <strong>Villa Eksekutif Tepi Laut</strong>, dan <strong>Penthouse Presidensial</strong> dengan foto resolusi tinggi dan fasilitas inklusif.
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-3 w-full md:w-auto flex-shrink-0">
            <a href="{{ route('pelanggan.kamar') }}" class="w-full md:w-auto text-center px-6 py-3 rounded-full bg-gold-500 hover:bg-gold-600 text-navy-950 text-xs font-extrabold transition shadow-hover">
                Buka Katalog Kamar &rarr;
            </a>
        </div>
    </div>

</div>
@endsection
