@extends('layouts.app')

@section('title', 'Pembayaran QRIS Otomatis — ' . $pemesanan->kode_booking)

@section('content')
<div class="max-w-6xl mx-auto space-y-6" x-data="{
    sisaDetik: {{ $sisaDetik }},
    isPaid: false,
    checking: false,
    isSubmittingSim: false,
    formatTimer() {
        const m = Math.floor(this.sisaDetik / 60);
        const s = this.sisaDetik % 60;
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    },
    init() {
        // Countdown timer interval
        const timer = setInterval(() => {
            if (this.sisaDetik > 0) {
                this.sisaDetik--;
            } else {
                clearInterval(timer);
            }
        }, 1000);

        // Auto-polling check status payment every 3 seconds
        const poller = setInterval(async () => {
            if (this.isPaid || this.sisaDetik <= 0) {
                clearInterval(poller);
                return;
            }
            try {
                this.checking = true;
                const res = await fetch('{{ route('booking.status', $pemesanan->id) }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.checking = false;
                if (data.is_paid) {
                    this.isPaid = true;
                    clearInterval(poller);
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1200);
                }
            } catch (err) {
                this.checking = false;
            }
        }, 3000);
    },
    async triggerSimulasiBayar() {
        this.isSubmittingSim = true;
        try {
            const res = await fetch('{{ route('booking.simulasi_bayar', $pemesanan->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.isPaid = true;
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1000);
            }
        } catch (e) {
            this.isSubmittingSim = false;
            alert('Gagal memproses simulasi pembayaran.');
        }
    }
}">

    <!-- Navigasi Pintas / Breadcrumb -->
    <div class="flex items-center justify-between text-xs text-stone-600 px-1">
        <div class="flex items-center space-x-2">
            <a href="{{ route('pelanggan.kamar') }}" class="font-bold text-stone-700 hover:text-navy-900 transition flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                <span>Katalog Kamar</span>
            </a>
            <span>/</span>
            <span class="text-stone-400">Formulir Pemesanan</span>
            <span>/</span>
            <span class="font-bold text-navy-900">Pembayaran QRIS Otomatis</span>
        </div>
        <a href="{{ route('pelanggan.dashboard') }}" class="font-bold text-gold-700 hover:text-gold-800 transition flex items-center space-x-1.5">
            <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <span>Riwayat Reservasi Saya</span>
        </a>
    </div>

    <!-- Alert Status Pembayaran Berhasil (Muncul saat lunas terverifikasi) -->
    <div x-show="isPaid" x-cloak class="p-4 sm:p-5 bg-emerald-500 text-white rounded-3xl shadow-lg flex items-center justify-between transition transform duration-500">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-white text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-base leading-tight">Pembayaran QRIS Berhasil Diverifikasi!</h3>
                <p class="text-xs text-emerald-100 mt-0.5">Sistem sedang mengalihkan Anda ke halaman voucher resmi...</p>
            </div>
        </div>
        <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- Grid Dua Kolom: QRIS Resmi (Kiri) & Live Status / Rincian Tagihan (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ====================================================================
             KOLOM KIRI (6 Kolom): Kartu QRIS Resmi Standar Nasional
             ==================================================================== -->
        <div class="lg:col-span-6 bg-white rounded-3xl sm:rounded-4xl border border-warm-200 p-6 sm:p-8 shadow-card flex flex-col items-center text-center relative overflow-hidden">
            
            <!-- Header QRIS Nasional Merah Putih -->
            <div class="w-full pb-4 border-b border-warm-200 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="px-2.5 py-1 bg-red-600 text-white font-black text-xs tracking-wider rounded">QRIS</span>
                    <span class="text-[10px] font-extrabold text-stone-400 tracking-widest uppercase">GPN</span>
                </div>
                <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider">
                    Standar BI &bull; QR Dinamis
                </span>
            </div>

            <!-- Identitas Merchant -->
            <div class="mt-4 mb-3">
                <h2 class="text-lg font-black text-navy-900 tracking-tight">LUMINA GRAND HOTEL &amp; RESOR</h2>
                <div class="text-[11px] text-stone-500 font-mono mt-0.5">
                    NMID: <strong class="text-stone-700">ID102026889901</strong> &bull; Acuan: {{ $pemesanan->kode_booking }}
                </div>
            </div>

            <!-- Wadah Kode QR Otomatis -->
            <div class="p-4 sm:p-5 bg-white rounded-3xl border-2 border-dashed border-warm-200 shadow-sm relative group my-2">
                <div id="qrcode-container" class="flex justify-center items-center w-[230px] h-[230px] sm:w-[260px] sm:h-[260px]"></div>
                
                <!-- Center Logo QRIS Overlay -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-11 h-11 bg-white rounded-xl shadow-md border border-stone-200 flex items-center justify-center p-1">
                        <span class="bg-red-600 text-white text-[9px] font-black px-1.5 py-0.5 rounded leading-none">QRIS</span>
                    </div>
                </div>
            </div>

            <!-- Nominal Pas -->
            <div class="mt-3 py-2 px-6 rounded-full bg-warm-100 border border-warm-200 inline-block">
                <span class="text-[10px] uppercase font-bold text-stone-400 block leading-none">Nominal Pembayaran Pas</span>
                <span class="text-xl font-black text-navy-900 mt-1 block">
                    Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                </span>
            </div>

            <!-- Logo Bank & E-Wallet Yang Didukung -->
            <div class="mt-6 pt-5 border-t border-warm-200 w-full space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-stone-400 block">
                    Dapat Di-Scan Melalui Semua Aplikasi:
                </span>
                <div class="flex flex-wrap items-center justify-center gap-1.5 text-[10px] font-bold text-stone-600">
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">BCA Mobile</span>
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">Livin' Mandiri</span>
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">BRImo</span>
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">BNI Mobile</span>
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">GoPay</span>
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">OVO</span>
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">ShopeePay</span>
                    <span class="px-2 py-1 bg-warm-50 rounded-lg border border-warm-200">DANA</span>
                </div>
            </div>

        </div>

        <!-- ====================================================================
             KOLOM KANAN (6 Kolom): Status Real-Time & Simulator Verifikasi
             ==================================================================== -->
        <div class="lg:col-span-6 space-y-6">

            <!-- Card Status Pembayaran & Countdown Timer -->
            <div class="bg-navy-950 text-white rounded-3xl sm:rounded-4xl border border-gold-500/30 p-6 sm:p-7 shadow-xl space-y-5 relative overflow-hidden">
                <div class="absolute -top-20 -right-20 w-44 h-44 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex items-center justify-between pb-4 border-b border-white/10">
                    <div class="flex items-center space-x-2.5">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                        </span>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-amber-300">
                            Menunggu Pembayaran
                        </span>
                    </div>
                    
                    <!-- Timer Hitung Mundur -->
                    <div class="flex items-center space-x-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/15">
                        <svg class="w-3.5 h-3.5 text-gold-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="font-mono font-bold text-xs text-white" x-text="formatTimer()"></span>
                    </div>
                </div>

                <!-- Info Verifikasi Otomatis -->
                <div class="flex items-center space-x-3 p-3.5 bg-white/5 rounded-2xl border border-white/10 text-xs text-stone-300">
                    <div class="w-7 h-7 rounded-xl bg-gold-500/20 text-gold-300 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div class="leading-relaxed">
                        <strong>Verifikasi Real-Time:</strong> Sistem secara otomatis mendeteksi transaksi Anda. Tidak perlu upload bukti transfer manual.
                    </div>
                </div>

                <!-- Rincian Pemesanan -->
                <div class="space-y-3 text-xs pt-1">
                    <div class="flex justify-between text-stone-300">
                        <span>Kode Pemesanan:</span>
                        <span class="font-mono font-bold text-white">{{ $pemesanan->kode_booking }}</span>
                    </div>
                    <div class="flex justify-between text-stone-300">
                        <span>Tipe Akomodasi:</span>
                        <span class="font-bold text-white">Suite {{ $pemesanan->kamar->tipe }} (Kamar {{ $pemesanan->kamar->nomor_kamar }})</span>
                    </div>
                    <div class="flex justify-between text-stone-300">
                        <span>Jadwal Menginap:</span>
                        <span class="font-bold text-white">
                            {{ \Carbon\Carbon::parse($pemesanan->tanggal_check_in)->format('d M') }} &rarr; {{ \Carbon\Carbon::parse($pemesanan->tanggal_check_out)->format('d M Y') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-stone-300">
                        <span>Nama Pemesan:</span>
                        <span class="font-bold text-white">{{ $pemesanan->tamu->nama }}</span>
                    </div>
                    <div class="flex items-baseline justify-between pt-3 border-t border-dashed border-white/20 text-gold-300">
                        <span class="text-xs uppercase font-bold text-stone-400">Total Tagihan:</span>
                        <span class="text-2xl font-black">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>

            </div>

            <!-- ================================================================
                 WIDGET SIMULATOR PEMBAYARAN CEPAT (Pengujian & Demo Sidang)
                 ================================================================ -->
            <div class="bg-white rounded-3xl border border-warm-200 p-6 sm:p-7 shadow-card space-y-4">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-gold-600"></span>
                    <h3 class="text-sm font-extrabold uppercase tracking-wider text-navy-900">
                        Simulasi Scan QRIS Instan
                    </h3>
                </div>
                <p class="text-xs text-stone-500 leading-relaxed">
                    Untuk kemudahan demonstrasi dan pengujian sistem, klik tombol di bawah untuk mensimulasikan pembayaran m-Banking/E-Wallet berhasil tanpa perlu transfer uang nyata.
                </p>

                <!-- Tombol Trigger Simulasi -->
                <button type="button" 
                        @click="triggerSimulasiBayar()"
                        :disabled="isSubmittingSim || isPaid"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm py-4 px-6 rounded-2xl transition duration-200 flex items-center justify-center space-x-2 shadow-card hover:shadow-hover cursor-pointer group disabled:opacity-50">
                    <template x-if="!isSubmittingSim && !isPaid">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-emerald-200 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>Simulasikan Scan Pembayaran Berhasil</span>
                        </div>
                    </template>
                    <template x-if="isSubmittingSim">
                        <div class="flex items-center space-x-2">
                            <span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>Memproses Verifikasi Pembayaran...</span>
                        </div>
                    </template>
                    <template x-if="isPaid">
                        <span>Pembayaran Telah Terverifikasi! Mengalihkan...</span>
                    </template>
                </button>

                <div class="pt-3 border-t border-warm-200 flex items-center justify-between text-xs text-stone-500">
                    <span>Metode: QRIS Dinamis Otomatis</span>
                    <a href="{{ route('pelanggan.dashboard') }}" class="font-bold text-navy-800 hover:underline">
                        Bayar Nanti di Riwayat &rarr;
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>

<!-- Script QRCode.js Lokal -->
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const payload = {!! json_encode($qrisPayload) !!};
        const container = document.getElementById('qrcode-container');
        if (container && typeof QRCode !== 'undefined') {
            new QRCode(container, {
                text: payload,
                width: 250,
                height: 250,
                colorDark: "#0A1526",
                colorLight: "#FFFFFF",
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    });
</script>
@endsection
