@extends('layouts.app')

@section('title', 'Dasbor Meja Resepsionis')

@section('content')
<div x-data="{ 
    bookingModal: false, 
    checkoutModal: false,
    kamarId: '',
    selectedRoom: {{ $kamars->first() ? $kamars->first()->toJson() : 'null' }},
    selectedBooking: {{ $kamars->first() && $kamars->first()->pemesananAktif ? $kamars->first()->pemesananAktif->load('tamu')->toJson() : 'null' }},
    jumlahBayar: 0,
    totalHarga: 0,
    metodeBayar: 'Tunai',
    get kembalian() {
        return Math.max(0, this.jumlahBayar - this.totalHarga);
    },
    selectKamar(kamar, booking) {
        this.selectedRoom = kamar;
        this.selectedBooking = booking;
        if (booking) {
            this.totalHarga = booking.total_harga;
            this.jumlahBayar = booking.total_harga;
        }
    }
}">

    <!-- Sub-Header Row: Page Title + Active Guests Chips (Exact match with reference image) -->
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0A1526] tracking-tight">Operasional Kamar & Tamu</h1>
            <p class="text-xs sm:text-sm text-[#5A6578] mt-0.5">Pantau status kamar, kelola tamu menginap, serta transaksi kasir meja depan.</p>
        </div>

        <!-- Horizontal Guest / VIP Chips -->
        <div class="flex items-center space-x-3 overflow-x-auto pb-1 sm:pb-0">
            @foreach($pemesananAktif->take(3) as $aktif)
                <div class="flex items-center space-x-2.5 bg-white py-1.5 px-3 rounded-full border border-[#EFEAE0] shadow-xs flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-[#F7F4EE] text-[#0A1526] font-bold flex items-center justify-center text-xs border border-[#EFEAE0]">
                        {{ substr($aktif->tamu->nama, 0, 1) }}
                    </div>
                    <div class="text-left">
                        <div class="text-xs font-bold text-[#0A1526] leading-tight">{{ Str::limit($aktif->tamu->nama, 16) }}</div>
                        <div class="text-[10px] text-[#5A6578] leading-tight">Kamar {{ $aktif->kamar->nomor_kamar }} ({{ $aktif->kamar->tipe }})</div>
                    </div>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $aktif->status === 'Check-in' ? 'bg-[#fdd8d5] text-[#991b1b]' : 'bg-[#d4e5f9] text-[#1e40af]' }}">
                        {{ $aktif->status }}
                    </span>
                </div>
            @endforeach

            <button @click="bookingModal = true; kamarId = ''" class="flex-shrink-0 bg-[#0A1526] hover:bg-black text-white text-xs font-bold py-2 px-4 rounded-full shadow flex items-center space-x-1.5 transition">
                <span>+ Reservasi</span>
            </button>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN BENTO GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- ========================================== -->
        <!-- LEFT COLUMN: BENTO METRICS + ROOMS (8 Cols) -->
        <!-- ========================================== -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Section 1: Business Summary Bento (6 Pastel Cards) -->
            <div class="bg-white p-6 rounded-3xl border border-[#EFEAE0] shadow-sm">
                <!-- Section Header with Dark Circular Badge -->
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-[#0A1526]">Ringkasan Operasional Kamar</h2>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="text-[11px] text-[#5A6578] font-semibold bg-[#FCFAF7] px-2.5 py-1 rounded-full border border-[#EFEAE0]">Sistem Terintegrasi Real-time</span>
                    </div>
                </div>

                <!-- 6 Pastel Tiles (2 rows x 3 columns) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                    
                    <!-- Tile 1: Kamar Kotor (Pastel Peach) -->
                    <div class="bg-[#fde8cf] border border-[#f6caa2] p-4 rounded-3xl flex flex-col justify-between transition hover:shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#7c2d12] flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#ea580c]"></span>
                                <span>Kamar Kotor</span>
                            </span>
                            <div class="w-6 h-6 rounded-full bg-white/60 flex items-center justify-center text-[#7c2d12]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-[#7c2d12] my-1">{{ $stats['kotor'] }}</div>
                        <div class="text-[11px] font-semibold text-[#9a3412]/80">Menunggu dibersihkan</div>
                    </div>

                    <!-- Tile 2: Kamar Tersedia (Pastel Sky Blue) -->
                    <div class="bg-[#d4e5f9] border border-[#b6d4f6] p-4 rounded-3xl flex flex-col justify-between transition hover:shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#1e40af] flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#2563eb]"></span>
                                <span>Kamar Bersih</span>
                            </span>
                            <div class="w-6 h-6 rounded-full bg-white/60 flex items-center justify-center text-[#1e40af]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-[#1e40af] my-1">{{ $stats['tersedia'] }}</div>
                        <div class="text-[11px] font-semibold text-[#1e40af]/80">Tersedia siap ditempati</div>
                    </div>

                    <!-- Tile 3: Total Kamar (Pastel Lavender) -->
                    <div class="bg-[#e3d6f8] border border-[#cdbaf4] p-4 rounded-3xl flex flex-col justify-between transition hover:shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#5b21b6] flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#7c3aed]"></span>
                                <span>Total Kamar</span>
                            </span>
                            <div class="w-6 h-6 rounded-full bg-white/60 flex items-center justify-center text-[#5b21b6]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-[#5b21b6] my-1">{{ $stats['total'] }}</div>
                        <div class="text-[11px] font-semibold text-[#5b21b6]/80">Unit kamar hotel aktif</div>
                    </div>

                    <!-- Tile 4: Kamar Terisi (Pastel Blush Coral) -->
                    <div class="bg-[#fdd8d5] border border-[#f8b4ae] p-4 rounded-3xl flex flex-col justify-between transition hover:shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#991b1b] flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#dc2626]"></span>
                                <span>Kamar Terisi</span>
                            </span>
                            <div class="w-6 h-6 rounded-full bg-white/60 flex items-center justify-center text-[#991b1b]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-[#991b1b] my-1">{{ $stats['terisi'] }}</div>
                        <div class="text-[11px] font-semibold text-[#991b1b]/80">Tamu sedang menginap</div>
                    </div>

                    <!-- Tile 5: Kamar Perawatan (Pastel Sand) -->
                    <div class="bg-[#fae8d8] border border-[#f0caa9] p-4 rounded-3xl flex flex-col justify-between transition hover:shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#854d0e] flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#ca8a04]"></span>
                                <span>Perawatan</span>
                            </span>
                            <div class="w-6 h-6 rounded-full bg-white/60 flex items-center justify-center text-[#854d0e]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-[#854d0e] my-1">{{ $stats['perawatan'] }}</div>
                        <div class="text-[11px] font-semibold text-[#854d0e]/80">Perbaikan & fasilitas</div>
                    </div>

                    <!-- Tile 6: Okupansi Harian (Pastel Mint) -->
                    <div class="bg-[#d6f5e8] border border-[#a9ebd0] p-4 rounded-3xl flex flex-col justify-between transition hover:shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#065f46] flex items-center space-x-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#059669]"></span>
                                <span>Okupansi Hari Ini</span>
                            </span>
                            <div class="w-6 h-6 rounded-full bg-white/60 flex items-center justify-center text-[#065f46]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>
                        </div>
                        @php
                            $rateOkupansi = $stats['total'] > 0 ? round(($stats['terisi'] / $stats['total']) * 100, 1) : 0;
                        @endphp
                        <div class="text-3xl font-bold text-[#065f46] my-1">{{ $rateOkupansi }}%</div>
                        <div class="text-[11px] font-semibold text-[#065f46]/80">Tingkat keterisian hotel</div>
                    </div>

                </div>
            </div>

            <!-- Section 2: Visual Room Bento Grid (Interactive Denah) -->
            <div class="bg-white p-6 rounded-3xl border border-[#EFEAE0] shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                            <!-- Bed icon -->
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-[#0A1526]">Denah Kamar Visual</h2>
                            <p class="text-xs text-[#5A6578]">Klik kamar mana saja untuk melihat detail & aksi pada panel sebelah kanan.</p>
                        </div>
                    </div>

                    <div class="hidden sm:flex items-center space-x-2 text-xs font-semibold">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-[#d4e5f9] text-[#1e40af] text-[10px]">Tersedia</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-[#fdd8d5] text-[#991b1b] text-[10px]">Terisi</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-[#fde8cf] text-[#7c2d12] text-[10px]">Kotor</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-[#fae8d8] text-[#854d0e] text-[10px]">Perawatan</span>
                    </div>
                </div>

                <!-- Room Tiles -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach($kamars as $kamar)
                        @php
                            $booking = $kamar->pemesananAktif;
                            $isAvail = ($kamar->status === 'Tersedia' && $kamar->status_kebersihan === 'Bersih');
                            $isOcc = ($kamar->status === 'Terisi');
                            $isDirty = ($kamar->status_kebersihan === 'Kotor');
                            $isMaint = ($kamar->status === 'Perawatan');

                            $tileBg = 'bg-[#d4e5f9] border-[#b6d4f6] text-[#1e40af]';
                            $pillBadge = 'bg-white/80 text-[#1e40af]';
                            $statusText = 'Tersedia';

                            if ($isOcc) {
                                $tileBg = 'bg-[#fdd8d5] border-[#f8b4ae] text-[#991b1b]';
                                $pillBadge = 'bg-white/80 text-[#991b1b]';
                                $statusText = 'Terisi';
                            } elseif ($isDirty) {
                                $tileBg = 'bg-[#fde8cf] border-[#f6caa2] text-[#7c2d12]';
                                $pillBadge = 'bg-white/80 text-[#7c2d12]';
                                $statusText = 'Kotor';
                            } elseif ($isMaint) {
                                $tileBg = 'bg-[#fae8d8] border-[#f0caa9] text-[#854d0e]';
                                $pillBadge = 'bg-white/80 text-[#854d0e]';
                                $statusText = 'Perawatan';
                            }
                        @endphp

                        <div @click="selectKamar({{ $kamar->toJson() }}, {{ $booking ? $booking->load('tamu')->toJson() : 'null' }})"
                            :class="selectedRoom && selectedRoom.id === {{ $kamar->id }} ? 'ring-2 ring-[#0A1526] scale-[1.02]' : ''"
                            class="cursor-pointer border p-3.5 rounded-2xl transition hover:shadow-md flex flex-col justify-between {{ $tileBg }}">
                            
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-lg">No. {{ $kamar->nomor_kamar }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs {{ $pillBadge }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                                <div class="text-xs font-semibold opacity-90">{{ $kamar->tipe }}</div>
                                <div class="text-xs font-bold mt-1">Rp {{ number_format($kamar->harga_per_malam, 0, ',', '.') }}</div>

                                @if($booking && $booking->status === 'Check-in')
                                    <div class="mt-2 pt-2 border-t border-black/10 text-[11px] leading-tight font-medium">
                                        <div class="font-bold truncate">Tamu: {{ $booking->tamu->nama }}</div>
                                        <div class="text-[10px] opacity-75">Out: {{ \Carbon\Carbon::parse($booking->tanggal_check_out)->format('d M') }}</div>
                                    </div>
                                @elseif($booking && $booking->status === 'Dipesan')
                                    <div class="mt-2 pt-2 border-t border-black/10 text-[11px] leading-tight font-medium">
                                        <div class="font-bold truncate">Pemesanan: {{ $booking->tamu->nama }}</div>
                                        <div class="text-[10px] opacity-75">In: {{ \Carbon\Carbon::parse($booking->tanggal_check_in)->format('d M') }}</div>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-3 pt-2 text-right">
                                <span class="text-[10px] font-bold underline opacity-80 hover:opacity-100">Pilih Kamar &rarr;</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Section 3: Cleaning & Occupancy Milestones Progress (Matches reference image's progress bar) -->
            <div class="bg-white p-6 rounded-3xl border border-[#EFEAE0] shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <h2 class="text-lg font-bold text-[#0A1526]">Status Progres Operasional Hari Ini</h2>
                    </div>
                    <span class="text-xs font-bold text-[#5A6578]">Target Kebersihan: 100%</span>
                </div>

                @php
                    $kebersihanPct = $stats['total'] > 0 ? round(($stats['bersih'] ?? ($stats['total'] - $stats['kotor'])) / $stats['total'] * 100) : 0;
                @endphp

                <!-- Progress Bar Slider -->
                <div class="mb-4">
                    <div class="flex justify-between text-[11px] font-bold text-[#5A6578] mb-1.5">
                        <span>0%</span>
                        <span>25%</span>
                        <span>50%</span>
                        <span>75%</span>
                        <span>100% Selesai</span>
                    </div>
                    <div class="w-full bg-[#F7F4EE] h-3 rounded-full overflow-hidden flex">
                        <div class="bg-[#0A1526] h-full transition-all duration-500 rounded-full" style="width: {{ $kebersihanPct }}%"></div>
                    </div>
                    <div class="text-right text-[11px] font-extrabold text-[#0A1526] mt-1">
                        {{ $kebersihanPct }}% Kamar Berstatus Bersih
                    </div>
                </div>

                <!-- Active Operational Task Items -->
                <div class="space-y-2.5 pt-2 border-t border-[#EFEAE0]">
                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-[#FCFAF7] border border-[#EFEAE0]">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#ea580c]"></span>
                            <span class="text-xs font-bold text-[#0A1526]">{{ $stats['kotor'] }} Kamar Menunggu Pembersihan</span>
                        </div>
                        <span class="text-[10px] font-extrabold px-3 py-1 rounded-full bg-[#fde8cf] text-[#7c2d12]">
                            Petugas Kebersihan
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-2.5 rounded-2xl bg-[#FCFAF7] border border-[#EFEAE0]">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2563eb]"></span>
                            <span class="text-xs font-bold text-[#0A1526]">{{ $stats['tersedia'] }} Kamar Siap Ditempati Tamu</span>
                        </div>
                        <span class="text-[10px] font-extrabold px-3 py-1 rounded-full bg-[#d4e5f9] text-[#1e40af]">
                            Siap Dipesan
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- RIGHT COLUMN: DETAIL INSPECTOR PANEL (4 Cols) -->
        <!-- (Matches right profile card in reference image) -->
        <!-- ========================================== -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Card 1: Inspector Profile Card -->
            <div class="bg-white p-6 rounded-3xl border border-[#EFEAE0] shadow-sm text-center">
                <!-- Top Micro Icons -->
                <div class="flex items-center justify-between mb-4">
                    <button class="w-7 h-7 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[#5A6578] hover:text-[#0A1526] text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    <div class="flex items-center space-x-1.5">
                        <button class="w-7 h-7 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[#5A6578] hover:text-[#0A1526] text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                        <button class="w-7 h-7 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[#5A6578] hover:text-[#0A1526] text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                        </button>
                    </div>
                </div>

                <!-- Big Circular Avatar / Room Badge -->
                <div class="w-24 h-24 rounded-full bg-[#FCFAF7] border-2 border-[#EFEAE0] mx-auto flex items-center justify-center shadow-inner mb-3 overflow-hidden">
                    <template x-if="selectedBooking">
                        <span class="text-3xl font-bold text-[#0A1526]" x-text="selectedBooking.tamu.nama.substring(0, 2).toUpperCase()"></span>
                    </template>
                    <template x-if="!selectedBooking && selectedRoom">
                        <span class="text-2xl font-bold text-[#0A1526]" x-text="'#' + selectedRoom.nomor_kamar"></span>
                    </template>
                </div>

                <!-- Name & Subtitle -->
                <template x-if="selectedBooking">
                    <div>
                        <h3 class="text-xl font-bold text-[#0A1526]" x-text="selectedBooking.tamu.nama"></h3>
                        <p class="text-xs text-[#5A6578] font-medium mt-0.5" x-text="'Tamu Menginap di Kamar ' + selectedRoom.nomor_kamar + ' (' + selectedRoom.tipe + ')'"></p>
                    </div>
                </template>
                <template x-if="!selectedBooking && selectedRoom">
                    <div>
                        <h3 class="text-xl font-bold text-[#0A1526]" x-text="'Kamar Nomor ' + selectedRoom.nomor_kamar"></h3>
                        <p class="text-xs text-[#5A6578] font-medium mt-0.5" x-text="'Tipe ' + selectedRoom.tipe + ' &bull; Status: ' + selectedRoom.status"></p>
                    </div>
                </template>

                <!-- Row of Micro Action Buttons (Matching reference image) -->
                <div class="flex items-center justify-center space-x-2 mt-4 pt-4 border-t border-[#EFEAE0]">
                    <button title="Ubah" class="w-8 h-8 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[#5A6578] hover:text-[#0A1526] hover:bg-white transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    </button>
                    <button title="Hubungi" class="w-8 h-8 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[#5A6578] hover:text-[#0A1526] hover:bg-white transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </button>
                    <button title="Kirim Tagihan" class="w-8 h-8 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[#5A6578] hover:text-[#0A1526] hover:bg-white transition shadow-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </button>
                    <button @click="bookingModal = true; kamarId = selectedRoom ? selectedRoom.id : ''" title="Reservasi Baru" class="w-8 h-8 rounded-full bg-[#0A1526] text-white flex items-center justify-center hover:bg-black transition shadow-xs">
                        <span class="text-sm font-bold">+</span>
                    </button>
                </div>
            </div>

            <!-- Card 2: Detailed Information Card (Matching reference image) -->
            <div class="bg-white p-6 rounded-3xl border border-[#EFEAE0] shadow-sm">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#EFEAE0]">
                    <div class="flex items-center space-x-2">
                        <div class="w-6 h-6 rounded-full bg-[#0A1526] text-white flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-[#0A1526]">Rincian Informasi Kamar</h3>
                    </div>
                </div>

                <!-- Attributes List -->
                <div class="space-y-3.5 text-xs">
                    <!-- Row 1: Nomor Kamar -->
                    <div class="flex items-center justify-between p-2 rounded-2xl hover:bg-[#FCFAF7] transition">
                        <div class="flex items-center space-x-2 text-[#5A6578]">
                            <span class="w-6 h-6 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center font-bold text-[10px]">#</span>
                            <span>Nomor Kamar</span>
                        </div>
                        <span class="font-extrabold text-[#0A1526]" x-text="selectedRoom ? 'Kamar ' + selectedRoom.nomor_kamar : '-'"></span>
                    </div>

                    <!-- Row 2: Tipe & Tarif -->
                    <div class="flex items-center justify-between p-2 rounded-2xl hover:bg-[#FCFAF7] transition">
                        <div class="flex items-center space-x-2 text-[#5A6578]">
                            <span class="w-6 h-6 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[10px]">Rp</span>
                            <span>Tarif Sewa</span>
                        </div>
                        <span class="font-bold text-[#0A1526]" x-text="selectedRoom ? 'Rp ' + Number(selectedRoom.harga_per_malam).toLocaleString('id-ID') + '/malam' : '-'"></span>
                    </div>

                    <!-- Row 3: Status Kebersihan -->
                    <div class="flex items-center justify-between p-2 rounded-2xl hover:bg-[#FCFAF7] transition">
                        <div class="flex items-center space-x-2 text-[#5A6578]">
                            <span class="w-6 h-6 rounded-full bg-[#FCFAF7] border border-[#EFEAE0] flex items-center justify-center text-[10px]">
                                <svg class="w-3.5 h-3.5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            <span>Kebersihan</span>
                        </div>
                        <span class="font-bold" :class="selectedRoom && selectedRoom.status_kebersihan === 'Bersih' ? 'text-[#065f46]' : 'text-[#7c2d12]'"
                              x-text="selectedRoom ? selectedRoom.status_kebersihan : '-'"></span>
                    </div>

                    <!-- Conditional Booking Attributes -->
                    <template x-if="selectedBooking">
                        <div class="space-y-3.5 pt-2 border-t border-[#EFEAE0]">
                            <!-- Kode Reservasi -->
                            <div class="flex items-center justify-between p-2 rounded-2xl hover:bg-[#FCFAF7] transition">
                                <span class="text-[#5A6578]">Kode Reservasi</span>
                                <span class="font-mono font-bold text-[#0A1526]" x-text="selectedBooking.kode_booking"></span>
                            </div>

                            <!-- Total Tagihan -->
                            <div class="flex items-center justify-between p-2 rounded-2xl bg-[#FCFAF7] border border-[#EFEAE0]">
                                <span class="text-[#5A6578] font-semibold">Total Biaya</span>
                                <span class="font-extrabold text-sm text-[#0A1526]" x-text="'Rp ' + Number(selectedBooking.total_harga).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Primary Action Buttons in Inspector -->
                <div class="mt-6 pt-4 border-t border-[#EFEAE0]">
                    <!-- If room has active check-in booking: trigger checkout -->
                    <template x-if="selectedBooking && selectedBooking.status === 'Check-in'">
                        <button @click="checkoutModal = true" class="w-full bg-[#fdd8d5] hover:bg-[#fbc8c2] text-[#991b1b] border border-[#f8b4ae] font-extrabold text-xs py-3 px-4 rounded-full shadow-xs transition flex items-center justify-center space-x-2">
                            <span>Check-Out & Kasir Pembayaran</span>
                            <span>&rarr;</span>
                        </button>
                    </template>

                    <!-- If room has pending booking: trigger checkin -->
                    <template x-if="selectedBooking && selectedBooking.status === 'Dipesan'">
                        <form :action="'/resepsionis/pemesanan/' + selectedBooking.id + '/check-in'" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Konfirmasi proses Check-in untuk tamu ini?')" class="w-full bg-[#d4e5f9] hover:bg-[#bfdaf7] text-[#1e40af] border border-[#b6d4f6] font-extrabold text-xs py-3 px-4 rounded-full shadow-xs transition flex items-center justify-center space-x-2">
                                <span>Konfirmasi Check-In Tamu</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </form>
                    </template>

                    <!-- If room is available: create booking -->
                    <template x-if="!selectedBooking && selectedRoom && selectedRoom.status === 'Tersedia' && selectedRoom.status_kebersihan === 'Bersih'">
                        <button @click="bookingModal = true; kamarId = selectedRoom.id" class="w-full bg-[#0A1526] hover:bg-black text-white font-extrabold text-xs py-3 px-4 rounded-full shadow transition flex items-center justify-center space-x-2">
                            <span>Pesan Kamar Ini Sekarang</span>
                            <span>+</span>
                        </button>
                    </template>

                    <!-- If room is dirty -->
                    <template x-if="selectedRoom && selectedRoom.status_kebersihan === 'Kotor'">
                        <div class="p-2.5 rounded-2xl bg-[#fde8cf] border border-[#f6caa2] text-center text-xs font-bold text-[#7c2d12]">
                            Kamar memerlukan pembersihan oleh petugas kebersihan sebelum dapat dipesan kembali.
                        </div>
                    </template>
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL RESERVASI BARU (Warm Bento Style) -->
    <div x-show="bookingModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-[#FCFAF7] rounded-3xl max-w-lg w-full p-7 shadow-2xl border border-[#dec5b5]" @click.away="bookingModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFEAE0]">
                <h3 class="text-lg font-bold text-[#0A1526]">Reservasi Kamar Baru</h3>
                <button @click="bookingModal = false" class="w-7 h-7 rounded-full bg-white border border-[#EFEAE0] text-[#5A6578] hover:text-[#0A1526] font-bold text-sm flex items-center justify-center">&times;</button>
            </div>

            <form action="{{ route('resepsionis.pemesanan.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-[#0A1526] uppercase">Pilih Tamu Terdaftar</label>
                        <a href="{{ route('resepsionis.tamu.index') }}" class="text-xs text-[#1e40af] font-semibold hover:underline">+ Daftarkan Tamu Baru</a>
                    </div>
                    <select name="tamu_id" required class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                        <option value="">-- Pilih Tamu --</option>
                        @foreach($tamuList as $tamu)
                            <option value="{{ $tamu->id }}">{{ $tamu->nama }} (NIK: {{ $tamu->no_identitas }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Pilih Kamar (Tersedia & Bersih)</label>
                    <select name="kamar_id" x-model="kamarId" required class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                        <option value="">-- Pilih Kamar --</option>
                        @foreach($kamarTersedia as $k)
                            <option value="{{ $k->id }}">Kamar {{ $k->nomor_kamar }} - Tipe {{ $k->tipe }} (Rp {{ number_format($k->harga_per_malam, 0, ',', '.') }}/malam)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Tanggal Check-In</label>
                        <input type="date" name="tanggal_check_in" value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required
                            class="w-full px-3.5 py-2 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Tanggal Check-Out</label>
                        <input type="date" name="tanggal_check_out" value="{{ date('Y-m-d', strtotime('+1 day')) }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                            class="w-full px-3.5 py-2 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-2 border-t border-[#EFEAE0]">
                    <button type="button" @click="bookingModal = false" class="px-4 py-2 text-xs text-[#5A6578] font-bold hover:bg-white rounded-full">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-xs bg-[#0A1526] hover:bg-black text-white rounded-full font-bold shadow">Simpan Reservasi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CHECK-OUT & KASIR (Warm Bento Style) -->
    <div x-show="checkoutModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-[#FCFAF7] rounded-3xl max-w-md w-full p-7 shadow-2xl border border-[#dec5b5]" @click.away="checkoutModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFEAE0]">
                <h3 class="text-lg font-bold text-[#0A1526]">Kasir Check-Out & Pembayaran</h3>
                <button @click="checkoutModal = false" class="w-7 h-7 rounded-full bg-white border border-[#EFEAE0] text-[#5A6578] hover:text-[#0A1526] font-bold text-sm flex items-center justify-center">&times;</button>
            </div>

            <template x-if="selectedBooking">
                <form :action="'/resepsionis/pemesanan/' + selectedBooking.id + '/check-out'" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div class="bg-white p-3.5 rounded-2xl border border-[#EFEAE0] text-xs space-y-1.5">
                        <div class="flex justify-between">
                            <span class="text-[#5A6578]">Kode Reservasi:</span>
                            <span class="font-mono font-bold text-[#0A1526]" x-text="selectedBooking.kode_booking"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[#5A6578]">Total Tagihan:</span>
                            <span class="font-extrabold text-sm text-[#0A1526]" x-text="'Rp ' + Number(totalHarga).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Metode Pembayaran</label>
                        <select name="metode_pembayaran" x-model="metodeBayar" required class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="QRIS">QRIS</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Nominal Uang Diterima (Rp)</label>
                        <input type="number" name="jumlah_bayar" x-model.number="jumlahBayar" :min="totalHarga" required
                            class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-bold text-[#0A1526] focus:ring-2 focus:ring-[#0A1526]">
                    </div>

                    <div class="p-3.5 bg-[#d6f5e8] border border-[#a9ebd0] rounded-2xl flex justify-between items-center text-[#065f46]">
                        <span class="text-xs font-bold">Kembalian:</span>
                        <span class="text-base font-black" x-text="'Rp ' + Number(kembalian).toLocaleString('id-ID')"></span>
                    </div>

                    <p class="text-[11px] text-[#5A6578] italic">
                        * Status kamar otomatis beralih menjadi <strong>Kotor</strong> setelah pembayaran diverifikasi.
                    </p>

                    <div class="pt-4 flex justify-end space-x-2 border-t border-[#EFEAE0]">
                        <button type="button" @click="checkoutModal = false" class="px-4 py-2 text-xs text-[#5A6578] font-bold hover:bg-white rounded-full">Batal</button>
                        <button type="submit" class="px-5 py-2.5 text-xs bg-[#fdd8d5] hover:bg-[#fbc8c2] text-[#991b1b] border border-[#f8b4ae] rounded-full font-extrabold shadow-xs">
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection


