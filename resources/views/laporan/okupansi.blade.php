@extends('layouts.app')

@section('title', 'Laporan Okupansi Kamar Harian')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0A1526] tracking-tight">Laporan Okupansi Kamar (Harian)</h1>
            <p class="text-xs sm:text-sm text-[#5A6578] mt-0.5">Rekapitulasi tingkat keterisian kamar harian dan persentase okupansi hotel.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold py-2.5 px-5 rounded-full shadow flex items-center space-x-1.5 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- Filter Tanggal (Hidden on Print) -->
    <div class="bg-white p-4 rounded-3xl border border-[#EFEAE0] shadow-sm no-print">
        <form action="{{ route('laporan.okupansi') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-bold text-[#0A1526] uppercase">Pilih Tanggal Laporan:</label>
            <input type="date" name="tanggal" value="{{ $tanggal }}" class="px-4 py-2 bg-[#FCFAF7] border border-[#EFEAE0] rounded-full text-xs font-bold text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
            <button type="submit" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold px-5 py-2 rounded-full shadow-xs transition">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- 3 Bento KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 no-print">
        <div class="bg-white border border-[#EFEAE0] p-5 rounded-3xl">
            <span class="text-xs font-bold text-[#5A6578] uppercase block">Total Unit Kamar</span>
            <span class="text-3xl font-bold text-[#0A1526] mt-1 block">{{ $totalKamar }} Kamar</span>
        </div>
        <div class="bg-[#fdd8d5] border border-[#f8b4ae] p-5 rounded-3xl">
            <span class="text-xs font-bold text-[#991b1b] uppercase block">Kamar Terisi</span>
            <span class="text-3xl font-bold text-[#991b1b] mt-1 block">{{ $kamarTerisiCount }} Kamar</span>
        </div>
        <div class="bg-[#d6f5e8] border border-[#a9ebd0] p-5 rounded-3xl">
            <span class="text-xs font-bold text-[#065f46] uppercase block">Persentase Okupansi Harian</span>
            <span class="text-3xl font-bold text-[#065f46] mt-1 block">{{ $persentaseOkupansi }}%</span>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div class="bg-white p-8 sm:p-10 rounded-4xl border border-[#EFEAE0] shadow-sm text-[#1c1917]">
        <!-- Letterhead Header -->
        <div class="border-b-2 border-[#0A1526] pb-4 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-[#0A1526] tracking-tight">LUMINA GRAND HOTEL</h2>
                    <p class="text-xs text-[#5A6578] font-bold">LAPORAN TINGKAT OKUPANSI KAMAR HARIAN</p>
                </div>
                <div class="text-right text-xs">
                    <div class="text-[#5A6578] font-semibold">Tanggal Analisis:</div>
                    <div class="text-sm font-black text-[#0A1526]">{{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="border border-[#EFEAE0] rounded-2xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs text-[#1c1917]">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] font-bold text-[#5A6578] border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-4 py-3.5">Nomor Kamar</th>
                        <th class="px-4 py-3.5">Tipe Kamar</th>
                        <th class="px-4 py-3.5">Tarif / Malam</th>
                        <th class="px-4 py-3.5">Status Keterisian</th>
                        <th class="px-4 py-3.5">Nama Tamu Menginap</th>
                        <th class="px-4 py-3.5">Kebersihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    @foreach($kamarData as $item)
                        <tr class="hover:bg-[#FCFAF7]/60 transition">
                            <td class="px-4 py-3 font-bold font-mono text-[#0A1526]">No. {{ $item['nomor_kamar'] }}</td>
                            <td class="px-4 py-3 text-[#5A6578] font-medium">{{ $item['tipe'] }}</td>
                            <td class="px-4 py-3 font-semibold">Rp {{ number_format($item['harga_per_malam'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-extrabold">
                                @if($item['status_keterisian'] === 'Terisi')
                                    <span class="text-[#991b1b] bg-[#fdd8d5] px-2.5 py-0.5 rounded-full text-[10px]">Terisi</span>
                                @elseif($item['status_keterisian'] === 'Dipesan')
                                    <span class="text-[#1e40af] bg-[#d4e5f9] px-2.5 py-0.5 rounded-full text-[10px]">Dipesan</span>
                                @else
                                    <span class="text-[#065f46] bg-[#d6f5e8] px-2.5 py-0.5 rounded-full text-[10px]">Kosong</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-bold text-[#0A1526]">{{ $item['nama_tamu'] }}</td>
                            <td class="px-4 py-3 text-[10px] font-bold">
                                <span class="{{ $item['status_kebersihan'] === 'Bersih' ? 'text-[#065f46]' : 'text-[#7c2d12]' }}">
                                    {{ $item['status_kebersihan'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 pt-8 text-center text-xs">
            <div></div>
            <div>
                <p class="text-[#5A6578] mb-14 font-medium">Mengetahui, Manajer Hotel</p>
                <p class="font-bold border-t border-[#0A1526] pt-1 inline-block px-8 text-[#0A1526]">
                    {{ auth()->user()->nama }}
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

