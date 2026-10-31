@extends('layouts.app')

@section('title', 'Konfirmasi Reservasi — ' . $pemesanan->kode_booking)

@section('content')
<div class="max-w-3xl mx-auto my-6">

    <!-- Action Bar (Hidden when Printing) -->
    <div class="flex items-center justify-between mb-5 no-print">
        <a href="{{ route('landing') }}" class="text-xs font-bold text-navy-800 hover:underline flex items-center space-x-1.5 transition">
            <span>&larr; Kembali ke Beranda</span>
        </a>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="bg-navy-800 hover:bg-black text-white text-xs font-bold px-4 py-2.5 rounded-full shadow flex items-center space-x-2 transition">
                <svg class="w-4 h-4 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Voucher Resmi</span>
            </button>
        </div>
    </div>

    <!-- Printable Booking Confirmation Voucher Card -->
    <div class="bg-white p-8 sm:p-12 rounded-4xl border border-warm-200 shadow-lg text-stone-900">
        
        <!-- Header Brand -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-navy-900 pb-6 mb-6">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-500"></span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-navy-900">LUMINA GRAND</h1>
                </div>
                <p class="text-xs text-gold-600 font-bold uppercase tracking-wider">Hotel &amp; Resor Bintang Lima</p>
                <p class="text-[11px] text-stone-500 mt-1">Jl. Pariwisata Raya No. 45, Indonesia &bull; Telp: (021) 7654321</p>
            </div>
            <div class="mt-4 sm:mt-0 text-left sm:text-right">
                <span class="inline-block bg-navy-900 text-white px-3.5 py-1 rounded-full text-[10px] font-mono font-bold tracking-wider">
                    VOUCHER RESERVASI RESMI
                </span>
                <div class="text-[10px] text-stone-500 mt-2 font-semibold uppercase">Kode Reservasi:</div>
                <div class="text-xl font-mono font-black text-navy-900 tracking-wider">{{ $pemesanan->kode_booking }}</div>
                @if($pemesanan->pembayaran)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 mt-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 mr-1.5"></span>
                        LUNAS &bull; Terverifikasi Otomatis ({{ $pemesanan->pembayaran->metode_pembayaran }})
                    </span>
                @else
                    <div class="mt-1.5 flex items-center space-x-1.5 justify-start sm:justify-end">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                            Menunggu Pembayaran
                        </span>
                        <a href="{{ route('booking.bayar', $pemesanan->id) }}" class="inline-flex items-center space-x-1 bg-red-600 hover:bg-red-700 text-white text-[10px] font-extrabold px-2.5 py-0.5 rounded-full no-print transition">
                            <span>Bayar QRIS &rarr;</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Banner Selamat Datang -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-6 text-xs text-emerald-800 flex items-center space-x-3">
            <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </span>
            <div class="leading-relaxed">
                @if($pemesanan->pembayaran)
                    <strong>Pembayaran Berhasil &amp; Reservasi Terkonfirmasi:</strong> Pembayaran Anda via {{ $pemesanan->pembayaran->metode_pembayaran }} sebesar Rp {{ number_format($pemesanan->pembayaran->jumlah_bayar, 0, ',', '.') }} telah terverifikasi otomatis. Kamar telah dialokasikan dan siap ditempati sesuai jadwal check-in.
                @else
                    <strong>Reservasi Anda Telah Berhasil Dicatat:</strong> Kamar telah dialokasikan khusus untuk Anda. Silakan tiba di hotel sesuai jadwal check-in dan selesaikan pembayaran di meja resepsionis atau scan QRIS.
                @endif
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6 text-xs bg-warm-50 p-6 rounded-3xl border border-warm-200">
            <div>
                <h3 class="text-[10px] font-bold text-gold-600 uppercase tracking-wider mb-2">Informasi Tamu Pemesan</h3>
                <div class="font-extrabold text-base text-navy-900">{{ $pemesanan->tamu->nama }}</div>
                <div class="text-stone-600 mt-1">NIK / Identitas: <strong>{{ $pemesanan->tamu->no_identitas }}</strong></div>
                <div class="text-stone-600">Alamat Email: <strong>{{ $pemesanan->tamu->email }}</strong></div>
                <div class="text-stone-600">Nomor Kontak: <strong>{{ $pemesanan->tamu->no_hp ?? '-' }}</strong></div>
            </div>
            <div>
                <h3 class="text-[10px] font-bold text-gold-600 uppercase tracking-wider mb-2">Rincian Akomodasi Kamar</h3>
                <div class="font-extrabold text-base text-navy-900">Kamar {{ $pemesanan->kamar->tipe }}</div>
                <div class="text-stone-600 mt-1">Nomor Unit Kamar: <strong class="text-navy-900">Kamar {{ $pemesanan->kamar->nomor_kamar }}</strong></div>
                <div class="text-stone-600">Waktu Masuk (Check-In): <strong>{{ \Carbon\Carbon::parse($pemesanan->tanggal_check_in)->format('d F Y') }} (14:00 WIB)</strong></div>
                <div class="text-stone-600">Waktu Keluar (Check-Out): <strong>{{ \Carbon\Carbon::parse($pemesanan->tanggal_check_out)->format('d F Y') }} (12:00 WIB)</strong></div>
                @php
                    $durasi = max(1, \Carbon\Carbon::parse($pemesanan->tanggal_check_in)->diffInDays(\Carbon\Carbon::parse($pemesanan->tanggal_check_out)));
                @endphp
                <div class="text-stone-600">Durasi Menginap: <strong>{{ $durasi }} Malam</strong></div>
            </div>
        </div>

        <!-- Billing Summary -->
        <div class="border border-warm-200 rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-xs">
                <thead class="bg-warm-100 uppercase text-[10px] font-bold text-stone-600 border-b border-warm-200">
                    <tr>
                        <th class="px-4 py-3 text-left">Deskripsi Layanan</th>
                        <th class="px-4 py-3 text-center">Durasi</th>
                        <th class="px-4 py-3 text-right">Tarif per Malam</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-warm-200">
                    <tr>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-navy-900">Sewa Kamar Suite {{ $pemesanan->kamar->tipe }}</div>
                            <div class="text-[10px] text-stone-500">Kamar Nomor {{ $pemesanan->kamar->nomor_kamar }} &bull; Termasuk Sarapan &amp; Wi-Fi Premium</div>
                        </td>
                        <td class="px-4 py-3.5 text-center font-bold">{{ $durasi }} Malam</td>
                        <td class="px-4 py-3.5 text-right">Rp {{ number_format($pemesanan->kamar->harga_per_malam, 0, ',', '.') }}</td>
                        <td class="px-4 py-3.5 text-right font-black text-navy-900">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-warm-50 font-bold border-t border-warm-200">
                    <tr>
                        <td colspan="3" class="px-4 py-3.5 text-right text-stone-600">Total Biaya Reservasi:</td>
                        <td class="px-4 py-3.5 text-right text-base font-black text-navy-900">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @if($pemesanan->pembayaran)
                        <tr class="bg-emerald-50/60 border-t border-emerald-200 text-emerald-800">
                            <td colspan="3" class="px-4 py-3 text-right font-extrabold text-[11px]">
                                Pembayaran Lunas ({{ $pemesanan->pembayaran->metode_pembayaran }}):
                            </td>
                            <td class="px-4 py-3 text-right font-extrabold text-sm text-emerald-700">
                                Rp {{ number_format($pemesanan->pembayaran->jumlah_bayar, 0, ',', '.') }}
                                <div class="text-[9px] font-normal text-emerald-600 mt-0.5">
                                    {{ \Carbon\Carbon::parse($pemesanan->pembayaran->tanggal_bayar)->format('d M Y, H:i') }} WIB
                                </div>
                            </td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        </div>

        <!-- Check-in Guide & Notes -->
        <div class="border-t border-warm-200 pt-5 text-xs text-stone-600 space-y-1.5">
            <h4 class="font-bold text-navy-900 text-xs uppercase tracking-wider">Petunjuk &amp; Ketentuan Check-In:</h4>
            <p>&bull; Harap membawa dan memperlihatkan kartu identitas resmi (KTP / Paspor) yang sesuai dengan nama pemesan.</p>
            <p>&bull; Pembayaran tagihan dapat diselesaikan langsung di meja resepsionis saat kedatangan (Tunai, Debit/Kredit, atau QRIS).</p>
            <p>&bull; Layanan bantuan Concierge 24 Jam: <strong>+62 812 3456 7890</strong> atau email ke <strong>layanan@luminagrand.com</strong>.</p>
        </div>

        <!-- Footer Seal -->
        <div class="mt-8 pt-6 border-t border-warm-200 text-center text-[10px] text-stone-500">
            Terima kasih telah memilih Lumina Grand Hotel &amp; Resor. Voucher ini sah dan diterbitkan secara elektronik oleh Sistem PMS HotelOps.
        </div>
    </div>
</div>
@endsection
