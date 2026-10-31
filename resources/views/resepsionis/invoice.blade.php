@extends('layouts.app')

@section('title', 'Kuitansi Pembayaran - ' . $pemesanan->kode_booking)

@section('content')
<div class="max-w-3xl mx-auto my-4">
    <!-- Action Bar (Hidden on Print) -->
    <div class="flex items-center justify-between mb-4 no-print">
        <a href="{{ route('resepsionis.dashboard') }}" class="text-xs font-bold text-[#0A1526] hover:underline flex items-center">
            &larr; Kembali ke Denah Kamar
        </a>
        <button onclick="window.print()" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold px-4 py-2 rounded-full shadow flex items-center space-x-1.5 transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Cetak Kuitansi Resmi</span>
        </button>
    </div>

    <!-- Official Printable Invoice Card -->
    <div class="bg-white p-8 sm:p-12 rounded-4xl border border-[#EFEAE0] shadow-md text-[#1c1917]">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b-2 border-[#0A1526] pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-[#0A1526]">LUMINA GRAND HOTEL</h1>
                <p class="text-xs text-[#5A6578] font-semibold mt-0.5">Sistem Manajemen Operasional & Meja Resepsionis</p>
                <p class="text-[11px] text-[#5A6578]">Jl. Pariwisata Raya No. 45, Indonesia | Telp: (021) 7654321</p>
            </div>
            <div class="mt-4 sm:mt-0 text-left sm:text-right">
                <span class="inline-block bg-[#0A1526] text-white px-3 py-1 rounded-full text-[10px] font-mono font-bold tracking-wider">
                    BUKTI PEMBAYARAN RESMI
                </span>
                <div class="text-[10px] text-[#5A6578] mt-2 font-semibold">Kode Reservasi:</div>
                <div class="text-base font-mono font-black text-[#0A1526]">{{ $pemesanan->kode_booking }}</div>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-2 gap-6 mb-8 text-xs">
            <div>
                <h3 class="text-[10px] font-bold text-[#5A6578] uppercase tracking-wider mb-2">Informasi Tamu</h3>
                <div class="font-extrabold text-sm text-[#0A1526]">{{ $pemesanan->tamu->nama }}</div>
                <div class="text-[#5A6578] mt-0.5">NIK/Identitas: <strong>{{ $pemesanan->tamu->no_identitas }}</strong></div>
                <div class="text-[#5A6578]">No. HP: {{ $pemesanan->tamu->no_hp ?? '-' }}</div>
                <div class="text-[#5A6578]">Alamat: {{ $pemesanan->tamu->alamat ?? '-' }}</div>
            </div>
            <div class="text-right">
                <h3 class="text-[10px] font-bold text-[#5A6578] uppercase tracking-wider mb-2">Informasi Menginap</h3>
                <div>Kamar: <strong class="text-[#0A1526]">No. {{ $pemesanan->kamar->nomor_kamar }} ({{ $pemesanan->kamar->tipe }})</strong></div>
                <div class="text-[#5A6578] mt-0.5">Waktu Masuk: <strong>{{ \Carbon\Carbon::parse($pemesanan->tanggal_check_in)->format('d F Y') }}</strong></div>
                <div class="text-[#5A6578]">Waktu Keluar: <strong>{{ \Carbon\Carbon::parse($pemesanan->tanggal_check_out)->format('d F Y') }}</strong></div>
                @php
                    $nights = max(1, \Carbon\Carbon::parse($pemesanan->tanggal_check_in)->diffInDays(\Carbon\Carbon::parse($pemesanan->tanggal_check_out)));
                @endphp
                <div class="text-[#5A6578]">Durasi: <strong>{{ $nights }} Malam</strong></div>
            </div>
        </div>

        <!-- Billing Table -->
        <div class="border border-[#EFEAE0] rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-xs">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] font-bold text-[#5A6578] border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-4 py-3 text-left">Deskripsi Layanan</th>
                        <th class="px-4 py-3 text-center">Durasi</th>
                        <th class="px-4 py-3 text-right">Tarif / Malam</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-bold text-[#0A1526]">Sewa Kamar {{ $pemesanan->kamar->tipe }}</div>
                            <div class="text-[10px] text-[#5A6578]">Kamar Nomor {{ $pemesanan->kamar->nomor_kamar }}</div>
                        </td>
                        <td class="px-4 py-3 text-center font-bold">{{ $nights }} Malam</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($pemesanan->kamar->harga_per_malam, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-black text-[#0A1526]">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
                <tfoot class="bg-[#FCFAF7] border-t border-[#EFEAE0] font-bold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right text-[#5A6578]">Total Tagihan:</td>
                        <td class="px-4 py-3 text-right text-sm font-black text-[#0A1526]">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Receipt Details (Pastel Mint) -->
        @if($pemesanan->pembayaran)
            <div class="bg-[#d6f5e8] border border-[#a9ebd0] rounded-2xl p-4 mb-8">
                <div class="flex items-center justify-between border-b border-[#a9ebd0] pb-2 mb-3">
                    <span class="text-[10px] font-extrabold text-[#065f46] uppercase tracking-wider">Rincian Pembayaran Kasir</span>
                    <span class="text-xs font-black text-[#065f46]">LUNAS</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-[#065f46]/80 block text-[10px]">Metode Pembayaran:</span>
                        <strong class="text-[#065f46] text-xs font-black">{{ $pemesanan->pembayaran->metode_pembayaran }}</strong>
                    </div>
                    <div>
                        <span class="text-[#065f46]/80 block text-[10px]">Waktu Bayar:</span>
                        <strong class="text-[#065f46] text-xs font-bold">{{ $pemesanan->pembayaran->tanggal_bayar->format('d/m/Y H:i') }}</strong>
                    </div>
                    <div>
                        <span class="text-[#065f46]/80 block text-[10px]">Nominal Diterima:</span>
                        <strong class="text-[#065f46] text-xs font-black">Rp {{ number_format($pemesanan->pembayaran->jumlah_bayar, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span class="text-[#065f46]/80 block text-[10px]">Kembalian:</span>
                        <strong class="text-[#065f46] text-xs font-black">Rp {{ number_format($pemesanan->pembayaran->kembalian, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        @endif

        <!-- Signatures Footer -->
        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-[#EFEAE0] text-center text-xs">
            <div>
                <p class="text-[#5A6578] mb-14 font-medium">Tamu,</p>
                <p class="font-bold border-t border-[#0A1526] pt-1 inline-block px-8 text-[#0A1526]">{{ $pemesanan->tamu->nama }}</p>
            </div>
            <div>
                <p class="text-[#5A6578] mb-14 font-medium">Petugas Resepsionis (Kasir),</p>
                <p class="font-bold border-t border-[#0A1526] pt-1 inline-block px-8 text-[#0A1526]">
                    {{ $pemesanan->pembayaran?->penerima?->nama ?? auth()->user()->nama }}
                </p>
            </div>
        </div>

        <div class="mt-8 text-center text-[10px] text-[#5A6578]">
            Terima kasih telah menginap di Lumina Grand Hotel. Kuitansi ini sah sebagai bukti pembayaran resmi.
        </div>
    </div>
</div>
@endsection

