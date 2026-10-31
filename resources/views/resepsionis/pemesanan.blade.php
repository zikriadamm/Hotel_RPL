@extends('layouts.app')

@section('title', 'Manajemen Pemesanan & Kasir')

@section('content')
<div x-data="{ 
    bookingModal: false, 
    checkoutModal: false, 
    selectedBooking: null,
    jumlahBayar: 0,
    totalHarga: 0,
    metodeBayar: 'Tunai',
    get kembalian() {
        return Math.max(0, this.jumlahBayar - this.totalHarga);
    }
}">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-[#0A1526] tracking-tight">Daftar Reservasi & Kasir</h1>
            <p class="text-xs sm:text-sm text-[#5A6578] mt-0.5">Kelola seluruh status pemesanan, verifikasi check-in, dan transaksi check-out.</p>
        </div>
        <button @click="bookingModal = true" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold py-2.5 px-5 rounded-full shadow flex items-center space-x-1.5 transition">
            <span>+ Buat Reservasi</span>
        </button>
    </div>

    <!-- Filter Bar (Bento Pill Style) -->
    <div class="bg-white p-4 rounded-3xl border border-[#EFEAE0] shadow-sm mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('resepsionis.pemesanan.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Reservasi / Tamu / Kamar..."
                class="px-4 py-2 bg-[#FCFAF7] border border-[#EFEAE0] rounded-full text-xs font-medium w-full sm:w-64 focus:ring-2 focus:ring-[#0A1526]">

            <select name="status" class="px-4 py-2 bg-[#FCFAF7] border border-[#EFEAE0] rounded-full text-xs font-bold focus:ring-2 focus:ring-[#0A1526]">
                <option value="">Semua Status</option>
                <option value="Dipesan" {{ request('status') === 'Dipesan' ? 'selected' : '' }}>Dipesan</option>
                <option value="Check-in" {{ request('status') === 'Check-in' ? 'selected' : '' }}>Check-in</option>
                <option value="Check-out" {{ request('status') === 'Check-out' ? 'selected' : '' }}>Check-out</option>
                <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <button type="submit" class="bg-[#0A1526] hover:bg-black text-white text-xs font-bold px-5 py-2 rounded-full shadow-xs transition">
                Terapkan Filter
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('resepsionis.pemesanan.index') }}" class="text-xs font-semibold text-[#5A6578] hover:text-[#0A1526] underline ml-1">Atur Ulang</a>
            @endif
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-3xl border border-[#EFEAE0] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1c1917]">
                <thead class="bg-[#FCFAF7] uppercase text-[10px] tracking-wider text-[#5A6578] font-extrabold border-b border-[#EFEAE0]">
                    <tr>
                        <th class="px-6 py-4">Kode Reservasi</th>
                        <th class="px-6 py-4">Nama Tamu</th>
                        <th class="px-6 py-4">Kamar</th>
                        <th class="px-6 py-4">Periode Menginap</th>
                        <th class="px-6 py-4">Total Biaya</th>
                        <th class="px-6 py-4">Status Pemesanan</th>
                        <th class="px-6 py-4">Status Bayar</th>
                        <th class="px-6 py-4 text-right">Aksi Meja Depan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFEAE0]">
                    @forelse($pemesanans as $p)
                        <tr class="hover:bg-[#FCFAF7]/60 transition">
                            <td class="px-6 py-4 font-mono font-bold text-[#0A1526]">
                                {{ $p->kode_booking }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-[#0A1526]">{{ $p->tamu->nama }}</div>
                                <div class="text-[10px] text-[#5A6578]">NIK: {{ $p->tamu->no_identitas }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-[#0A1526]">Kamar {{ $p->kamar->nomor_kamar }}</span>
                                <span class="block text-[10px] text-[#5A6578]">{{ $p->kamar->tipe }}</span>
                            </td>
                            <td class="px-6 py-4 text-[#5A6578] font-medium leading-relaxed">
                                <div>Masuk: {{ \Carbon\Carbon::parse($p->tanggal_check_in)->format('d/m/Y') }}</div>
                                <div>Keluar: {{ \Carbon\Carbon::parse($p->tanggal_check_out)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 font-extrabold text-[#0A1526]">
                                Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($p->status === 'Dipesan')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#d4e5f9] text-[#1e40af] border border-[#b6d4f6]">Dipesan</span>
                                @elseif($p->status === 'Check-in')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#fdd8d5] text-[#991b1b] border border-[#f8b4ae]">Check-in</span>
                                @elseif($p->status === 'Check-out')
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#d6f5e8] text-[#065f46] border border-[#a9ebd0]">Check-out</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-[#F7F4EE] text-[#5A6578]">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($p->pembayaran)
                                    <span class="font-extrabold text-[#065f46] block">Lunas ({{ $p->pembayaran->metode_pembayaran }})</span>
                                    <span class="text-[10px] text-[#5A6578]">{{ $p->pembayaran->tanggal_bayar->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="font-bold text-[#7c2d12] bg-[#fde8cf] px-2.5 py-0.5 rounded-full text-[10px]">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                @if($p->status === 'Dipesan')
                                    <form action="{{ route('resepsionis.pemesanan.checkin', $p->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Konfirmasi Check-in tamu?')" class="bg-[#d4e5f9] hover:bg-[#bfdaf7] text-[#1e40af] border border-[#b6d4f6] text-[11px] font-extrabold px-3 py-1 rounded-full transition">
                                            Check-in
                                        </button>
                                    </form>
                                    <form action="{{ route('resepsionis.pemesanan.batal', $p->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Batalkan reservasi?')" class="text-[11px] text-[#991b1b] hover:underline font-bold px-2 py-1">
                                            Batal
                                        </button>
                                    </form>
                                @elseif($p->status === 'Check-in')
                                    <button @click="
                                        checkoutModal = true; 
                                        selectedBooking = {{ $p->toJson() }};
                                        totalHarga = {{ $p->total_harga }};
                                        jumlahBayar = {{ $p->total_harga }};
                                    " class="bg-[#fdd8d5] hover:bg-[#fbc8c2] text-[#991b1b] border border-[#f8b4ae] text-[11px] font-extrabold px-3.5 py-1.5 rounded-full transition shadow-2xs">
                                        Check-out & Bayar
                                    </button>
                                @elseif($p->status === 'Check-out')
                                    <a href="{{ route('resepsionis.invoice', $p->id) }}" class="bg-[#FCFAF7] hover:bg-[#F7F4EE] border border-[#EFEAE0] text-[#0A1526] text-[11px] font-bold px-3 py-1.5 rounded-full transition">
                                        Kuitansi Resmi &rarr;
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-[#5A6578]">
                                Tidak ada data pemesanan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pemesanans->hasPages())
            <div class="p-4 border-t border-[#EFEAE0] bg-[#FCFAF7]">
                {{ $pemesanans->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL RESERVASI -->
    <div x-show="bookingModal" class="fixed inset-0 z-50 overflow-y-auto bg-black/40 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-[#FCFAF7] rounded-3xl max-w-lg w-full p-7 shadow-2xl border border-[#dec5b5]" @click.away="bookingModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-[#EFEAE0]">
                <h3 class="text-lg font-bold text-[#0A1526]">Buat Reservasi Kamar</h3>
                <button @click="bookingModal = false" class="w-7 h-7 rounded-full bg-white border border-[#EFEAE0] text-[#5A6578] hover:text-[#0A1526] font-bold text-sm flex items-center justify-center">&times;</button>
            </div>

            <form action="{{ route('resepsionis.pemesanan.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Pilih Tamu</label>
                    <select name="tamu_id" required class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                        <option value="">-- Pilih Tamu --</option>
                        @foreach($tamuList as $tamu)
                            <option value="{{ $tamu->id }}">{{ $tamu->nama }} (NIK: {{ $tamu->no_identitas }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#0A1526] uppercase mb-1">Pilih Kamar (Tersedia & Bersih)</label>
                    <select name="kamar_id" required class="w-full px-3.5 py-2.5 bg-white border border-[#EFEAE0] rounded-2xl text-xs font-medium focus:ring-2 focus:ring-[#0A1526]">
                        <option value="">-- Pilih Kamar --</option>
                        @foreach($kamarTersedia as $k)
                            <option value="{{ $k->id }}">Kamar {{ $k->nomor_kamar }} - {{ $k->tipe }} (Rp {{ number_format($k->harga_per_malam, 0, ',', '.') }}/malam)</option>
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
                    <button type="submit" class="px-5 py-2.5 text-xs bg-[#0A1526] hover:bg-black text-white rounded-full font-bold shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CHECK-OUT & KASIR -->
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

