@extends('layouts.app')

@section('title', 'Laporan Riwayat Tamu Bulanan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0A1526] tracking-tight">Laporan Riwayat Tamu (Bulanan)</h1>
            <p class="text-xs sm:text-sm text-[#5A6578] mt-0.5">Daftar tamu yang pernah menginap, nomor kamar yang disewa, serta durasi menginap.</p>
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
        <form action="{{ route('laporan.riwayat_tamu') }}" method="GET" class="flex flex-wrap items-center gap-3">
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

    <!-- Printable Report Container -->
    <div class="bg-white p-8 sm:p-10 rounded-4xl border border-[#EFEAE0] shadow-sm text-[#1c1917]">
        <!-- Letterhead Header -->
        <div class="border-b-2 border-[#0A1526] pb-4 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-[#0A1526] tracking-tight">LUMINA GRAND HOTEL</h2>
                    <p class="text-xs text-[#5A6578] font-bold">REKAPITULASI RIWAYAT TAMU MENGINAP</p>
                </div>
                <div class="text-right text-xs">
                    <div class="text-[#5A6578] font-semibold">Periode Bulan:</div>
                    <div class="text-sm font-black text-[#0A1526]">
                        {{ \Carbon\Carbon::create(null, $bulan)->translatedFormat('F') }} {{ $tahun }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Table of Guests -->
        <div class="border border-[#EFEAE0] rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs text-[#1c1917]">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] font-bold text-[#5A6578] border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-4 py-3.5">Nama Tamu</th>
                        <th class="px-4 py-3.5">No. Identitas (NIK)</th>
                        <th class="px-4 py-3.5">Kamar Disewa</th>
                        <th class="px-4 py-3.5">Tanggal Masuk (Check-In)</th>
                        <th class="px-4 py-3.5">Tanggal Keluar (Check-Out)</th>
                        <th class="px-4 py-3.5 text-center">Durasi Menginap</th>
                        <th class="px-4 py-3.5 text-right">Total Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    @forelse($riwayatData as $item)
                        <tr class="hover:bg-[#FCFAF7]/60 transition">
                            <td class="px-4 py-3 font-extrabold text-[#0A1526]">
                                {{ $item['nama_tamu'] }}
                                <div class="text-[10px] text-[#5A6578] font-normal">Kontak: {{ $item['no_hp'] ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-[#0A1526]">{{ $item['no_identitas'] }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-[#0A1526]">Kamar {{ $item['nomor_kamar'] }}</span>
                                <span class="block text-[10px] text-[#5A6578]">({{ $item['tipe_kamar'] }})</span>
                            </td>
                            <td class="px-4 py-3 text-[#5A6578]">{{ $item['tanggal_check_in'] }}</td>
                            <td class="px-4 py-3 text-[#5A6578]">{{ $item['tanggal_check_out'] }}</td>
                            <td class="px-4 py-3 text-center font-extrabold text-[#0A1526]">
                                <span class="bg-[#FCFAF7] px-2.5 py-0.5 rounded-full border border-[#EFEAE0]">
                                    {{ $item['durasi_malam'] }} Malam
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-black text-[#0A1526]">
                                Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-[#5A6578]">
                                Tidak ada riwayat tamu menginap pada periode bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 pt-8 text-center text-xs">
            <div></div>
            <div>
                <p class="text-[#5A6578] mb-14 font-medium">Mengetahui, Manajer Operasional</p>
                <p class="font-bold border-t border-[#0A1526] pt-1 inline-block px-8 text-[#0A1526]">
                    {{ auth()->user()->nama }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

