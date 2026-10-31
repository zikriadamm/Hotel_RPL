@extends('layouts.app')

@section('title', 'Laporan Pendapatan Hotel Bulanan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0A1526] tracking-tight">Laporan Pendapatan Hotel (Bulanan)</h1>
            <p class="text-xs sm:text-sm text-[#5A6578] mt-0.5">Rekapitulasi transaksi penerimaan kas, transfer bank, dan QRIS per bulan.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold py-2.5 px-5 rounded-full shadow flex items-center space-x-1.5 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- Filter Bulan & Tahun (Hidden on Print) -->
    <div class="bg-white p-4 rounded-3xl border border-[#EFEAE0] shadow-sm no-print">
        <form action="{{ route('laporan.pendapatan') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center space-x-2">
                <label class="text-xs font-bold text-[#0A1526] uppercase">Bulan:</label>
                <select name="bulan" class="px-4 py-2 bg-[#FCFAF7] border border-[#EFEAE0] rounded-full text-xs font-bold text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <label class="text-xs font-bold text-[#0A1526] uppercase">Tahun:</label>
                <select name="tahun" class="px-4 py-2 bg-[#FCFAF7] border border-[#EFEAE0] rounded-full text-xs font-bold text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                    @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold px-5 py-2 rounded-full shadow-xs transition">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- 4 Pastel Bento Revenue Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 no-print">
        <div class="bg-[#e3d6f8] border border-[#cdbaf4] p-4 rounded-3xl">
            <span class="text-[10px] font-extrabold text-[#5b21b6] uppercase block">Total Penerimaan</span>
            <span class="text-lg sm:text-xl font-bold text-[#5b21b6] mt-1 block">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</span>
        </div>
        <div class="bg-[#d6f5e8] border border-[#a9ebd0] p-4 rounded-3xl">
            <span class="text-[10px] font-extrabold text-[#065f46] uppercase block">Tunai</span>
            <span class="text-base sm:text-lg font-bold text-[#065f46] mt-1 block">Rp {{ number_format($totalTunai, 0, ',', '.') }}</span>
        </div>
        <div class="bg-[#d4e5f9] border border-[#b6d4f6] p-4 rounded-3xl">
            <span class="text-[10px] font-extrabold text-[#1e40af] uppercase block">Transfer Bank</span>
            <span class="text-base sm:text-lg font-bold text-[#1e40af] mt-1 block">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span>
        </div>
        <div class="bg-[#fde8cf] border border-[#f6caa2] p-4 rounded-3xl">
            <span class="text-[10px] font-extrabold text-[#7c2d12] uppercase block">QRIS</span>
            <span class="text-base sm:text-lg font-bold text-[#7c2d12] mt-1 block">Rp {{ number_format($totalQRIS, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white p-8 sm:p-10 rounded-4xl border border-[#EFEAE0] shadow-sm text-[#1c1917]">
        <!-- Letterhead Header -->
        <div class="border-b-2 border-[#0A1526] pb-4 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-[#0A1526] tracking-tight">LUMINA GRAND HOTEL</h2>
                    <p class="text-xs text-[#5A6578] font-bold">REKAPITULASI LAPORAN PENDAPATAN BULANAN</p>
                </div>
                <div class="text-right text-xs">
                    <div class="text-[#5A6578] font-semibold">Periode Bulan:</div>
                    <div class="text-sm font-black text-[#0A1526]">
                        {{ \Carbon\Carbon::create(null, $bulan)->translatedFormat('F') }} {{ $tahun }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="border border-[#EFEAE0] rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs text-[#1c1917]">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] font-bold text-[#5A6578] border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-4 py-3.5">Tanggal & Jam</th>
                        <th class="px-4 py-3.5">Kode Reservasi</th>
                        <th class="px-4 py-3.5">Nama Tamu</th>
                        <th class="px-4 py-3.5">Kamar</th>
                        <th class="px-4 py-3.5">Metode Bayar</th>
                        <th class="px-4 py-3.5 text-right">Penerimaan Bersih</th>
                        <th class="px-4 py-3.5">Kasir Penerima</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    @forelse($pembayarans as $bayar)
                        <tr class="hover:bg-[#FCFAF7]/60 transition">
                            <td class="px-4 py-3 text-[#5A6578]">{{ $bayar->tanggal_bayar->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-[#0A1526]">{{ $bayar->pemesanan->kode_booking }}</td>
                            <td class="px-4 py-3 font-bold text-[#0A1526]">{{ $bayar->pemesanan->tamu->nama }}</td>
                            <td class="px-4 py-3 text-[#5A6578]">No. {{ $bayar->pemesanan->kamar->nomor_kamar }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold
                                    @if($bayar->metode_pembayaran === 'Tunai') bg-[#d6f5e8] text-[#065f46]
                                    @elseif($bayar->metode_pembayaran === 'Transfer') bg-[#d4e5f9] text-[#1e40af]
                                    @else bg-[#e3d6f8] text-[#5b21b6] @endif">
                                    {{ $bayar->metode_pembayaran }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-black text-[#0A1526]">
                                Rp {{ number_format($bayar->jumlah_bayar - $bayar->kembalian, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-[#5A6578]">{{ $bayar->penerima?->nama ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-[#5A6578]">
                                Tidak ada transaksi pembayaran pada periode bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-[#FCFAF7] font-black border-t border-[#EFEAE0]">
                    <tr>
                        <td colspan="5" class="px-4 py-3.5 text-right text-[#0A1526]">Total Keseluruhan Penerimaan:</td>
                        <td class="px-4 py-3.5 text-right text-sm text-[#0A1526]">Rp {{ number_format($totalPenerimaan, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 pt-8 text-center text-xs">
            <div></div>
            <div>
                <p class="text-[#5A6578] mb-14 font-medium">Menyetujui, Manajer Keuangan</p>
                <p class="font-bold border-t border-[#0A1526] pt-1 inline-block px-8 text-[#0A1526]">
                    {{ auth()->user()->nama }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

