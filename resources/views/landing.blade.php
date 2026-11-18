<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ZSL Residence Hotel &amp; Resor</title>
  <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
  <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
  <link rel="manifest" href="{{ asset('site.webmanifest') }}">
  <meta name="theme-color" content="#0F1D38">
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Yellowtail&display=swap" rel="stylesheet">
  
  <!-- ScrollCraft Engine Runtime Styles -->
  <link rel="stylesheet" href="{{ asset('css/scrollcraft.css') }}?v={{ filemtime(public_path('css/scrollcraft.css')) }}">
  <!-- Lumina Grand Design System Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/lumina-grand.css') }}?v={{ filemtime(public_path('css/lumina-grand.css')) }}">
  <!-- Alpine.js Reactive Engine -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{
    openAuthPrompt: {{ !auth()->check() ? (request('booking') ? 'true' : 'false') : 'false' }},
    isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
    tipeKamar: 'Standar',
    selectedTipe: '',
    checkIn: '{{ date('Y-m-d') }}',
    checkOut: '{{ date('Y-m-d', strtotime('+1 day')) }}',
    bookingQuery() {
      var params = new URLSearchParams();
      if (this.selectedTipe) params.set('tipe', this.selectedTipe);
      if (this.checkIn) params.set('check_in', this.checkIn);
      if (this.checkOut) params.set('check_out', this.checkOut);
      return params.toString();
    },
    mulaiBooking(tipe) {
      this.selectedTipe = tipe || this.tipeKamar || '';
      if (!this.isLoggedIn) {
        this.openAuthPrompt = true;
      } else {
        var qs = this.bookingQuery();
        window.location.href = '{{ route('booking.form') }}' + (qs ? '?' + qs : '');
      }
    }
  }" :class="{ 'modal-open': openAuthPrompt }">

  @if(request('booking') && auth()->check())
    <script>
      window.location.href = "{{ route('booking.form') }}" + (window.location.search ? window.location.search.replace('booking=1', '').replace('?&', '?').replace('&&', '&') : '');
    </script>
  @endif

  @if(session('error'))
    <div class="lumina-toast-alert toast-error" id="lumina-toast">
      <div style="display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <span>{{ session('error') }}</span>
      </div>
      <button onclick="document.getElementById('lumina-toast').remove()" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.1rem; line-height: 1;">&times;</button>
    </div>
  @endif

  @if(session('success'))
    <div class="lumina-toast-alert toast-success" id="lumina-toast">
      <div style="display: flex; align-items: center; gap: 8px;">
        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
      </div>
      <button onclick="document.getElementById('lumina-toast').remove()" style="background: none; border: none; cursor: pointer; color: inherit; font-size: 1.1rem; line-height: 1;">&times;</button>
    </div>
  @endif

  <!-- ========================================================================
       Bilah Navigasi Atas (Sticky Nav)
       ======================================================================== -->
  <header class="lumina-nav">
    <a href="{{ route('landing') }}" class="nav-brand" aria-label="ZSL Residence — Beranda" style="flex-direction: row; align-items: center;">
      <img src="{{ asset('brand/zsl-residence/lockups/zsl-logo-horizontal-color.svg') }}" alt="ZSL Residence" style="height: 54px; width: auto; display: block;">
    </a>

    <nav class="nav-links">
      <a href="#experiences" class="active">Beranda</a>
      <a href="#philosophy">Informasi</a>
      <a href="#suites">Pilihan Kamar</a>
      <a href="#story">Layanan</a>
    </nav>

    <div class="nav-actions">
      @auth
        @if(auth()->user()->peran === 'Tamu')
          <div style="display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('pelanggan.dashboard') }}" class="btn-pill-navy" style="text-decoration: none; font-size: 0.78rem; padding: 0.5rem 1rem;">
              <span>Riwayat Pesanan</span>
            </a>
            <a href="{{ route('booking.form') }}" class="btn-pill-gold" style="text-decoration: none; font-size: 0.78rem; padding: 0.5rem 1rem;">
              <span>Pesan Kamar</span>
            </a>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
              @csrf
              <button type="submit" class="btn-link-subtle" style="background: none; border: none; cursor: pointer; padding: 0 4px; font-size: 0.78rem;">Keluar</button>
            </form>
          </div>
        @else
          <a href="{{ auth()->user()->peran === 'Manajer' ? route('manajer.kamar.index') : (auth()->user()->peran === 'Resepsionis' ? route('resepsionis.dashboard') : route('kebersihan.index')) }}" class="btn-pill-navy">
            <span>Panel Operasional ({{ auth()->user()->peran }})</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        @endif
      @else
        <a href="{{ route('login', ['redirect' => 'booking']) }}" class="btn-link-subtle" style="font-weight: 700; color: var(--navy-900);">Masuk</a>
        <a href="{{ route('register', ['redirect' => 'booking']) }}" class="btn-link-subtle" style="font-weight: 700; color: var(--gold-600);">Daftar</a>
      @endauth
    </div>
  </header>

  <!-- ========================================================================
       BAGIAN 1: Panggung Utama Hero (Parallax Dimensi + Aksentuasi Kaligrafi)
       ======================================================================== -->
  <section class="sc-hero-stage" id="experiences">
    <!-- Lapisan 1: Suasana Senja Jauh -->
    <div class="hero-plane-far" id="hero-plane-far"></div>

    <!-- Lapisan 2: Arsitektur Senja & Kolam Refleksi -->
    <div class="hero-plane-mid" id="hero-plane-mid"></div>

    <!-- Lapisan 3: Efek Gradien Pencahayaan -->
    <div class="hero-plane-atm"></div>

    <!-- Konten Utama Hero -->
    <div class="hero-stage-content">
      <span class="eyebrow" style="color: var(--gold-300);">ZSL Residence Hotel &amp; Resor</span>
      
      <h1 class="hero-title">
        Kamar Nyaman untuk Perjalanan Kerja <span class="script-accent">dan Liburan Keluarga</span>
      </h1>
      
      <p class="hero-desc" style="width: 100%; max-width: 660px; margin: 0 auto 2rem auto; text-align: center; text-wrap: balance;">
        Tersedia {{ $totalKamar ?? 14 }} kamar Standar, Deluxe, dan Suite mulai Rp 300.000 per malam. Pesan langsung lewat web dengan pembayaran tunai, transfer, atau QRIS.
      </p>

      <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; margin-top: 1.5rem;">
        <a href="#suites" class="btn-pill-gold">
          <span>Lihat Pilihan Kamar</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#booking" class="btn-pill-navy" style="background: rgba(10, 21, 38, 0.7); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
          <span>Cek Jadwal Menginap</span>
        </a>
      </div>
    </div>
  </section>

  <!-- ========================================================================
       Bilah Formulir Melayang (Floating Booking Bar)
       ======================================================================== -->
  <div class="floating-bar-anchor" id="booking">
    <form @submit.prevent="mulaiBooking(tipeKamar)" class="floating-bar">
      <!-- Kolom 1: Pilihan Tipe Kamar -->
      <div class="floating-col">
        <svg class="col-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
        <div class="col-content">
          <span class="col-label">Tipe Kamar</span>
          <select class="col-input" x-model="tipeKamar" style="font-weight: 700;">
            <option value="Standar">Standar (Rp 300.000)</option>
            <option value="Deluxe">Deluxe (Rp 550.000)</option>
            <option value="Suite">Suite (Rp 950.000)</option>
          </select>
        </div>
      </div>

      <!-- Kolom 2: Tanggal -->
      <div class="floating-col">
        <svg class="col-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <div class="col-content">
          <span class="col-label">Tanggal Menginap</span>
          <div style="display: flex; align-items: center; gap: 6px; flex-wrap: nowrap;">
            <input type="date" class="col-input" style="flex: 1; min-width: 0; width: auto;" x-model="checkIn" min="{{ date('Y-m-d') }}">
            <span style="color: var(--gold-600); flex-shrink: 0;">&rarr;</span>
            <input type="date" class="col-input" style="flex: 1; min-width: 0; width: auto;" x-model="checkOut" :min="checkIn">
          </div>
        </div>
      </div>

      <!-- Kolom 3: Status Unit -->
      <div class="floating-col" style="border-bottom: none;">
        <svg class="col-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <div class="col-content">
          <span class="col-label">Ketersediaan Saat Ini</span>
          <span class="col-input" style="display: flex; align-items: center; font-weight: 700; color: #065F46; white-space: nowrap;">
            {{ $kamarTersedia ?? 0 }}/{{ $totalKamar ?? 14 }} Kamar Siap
          </span>
        </div>
      </div>

      <!-- Kolom 4: Tombol Aksi -->
      <button type="submit" class="btn-float-search">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="11" cy="11" r="8"></circle>
          <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <span>Pesan Sekarang</span>
      </button>
    </form>
  </div>

  <!-- ========================================================================
       BAGIAN 2: Informasi Praktis Menginap
       ======================================================================== -->
  <section class="sc-philosophy-section" id="philosophy">
    <div class="sc-philosophy-box">
      <span class="eyebrow">Informasi Menginap</span>
      
      <h2 class="sc-philosophy-lead">
        Kamar dicek bersih sebelum tamu masuk, dan kuitansi atau voucher langsung keluar setelah transaksi.
      </h2>

      <p style="color: var(--gold-600); font-weight: 600; font-size: 1.05rem; max-width: 680px; margin: 0 auto 2.5rem auto; line-height: 1.6;">
        Data ketersediaan kamar, meja resepsionis, dan bagian kebersihan terhubung dalam satu jadwal agar tidak ada pemesanan ganda.
      </p>

      <div class="sc-metrics-strip">
        <div class="sc-metric-card">
          <div class="sc-metric-val">14.00 / 12.00</div>
          <div class="sc-metric-lbl">Jam Check-In &amp; Check-Out Harian</div>
        </div>
        <div class="sc-metric-card">
          <div class="sc-metric-val">{{ $totalKamar ?? 14 }} Unit</div>
          <div class="sc-metric-lbl">Pilihan Standar, Deluxe, dan Suite</div>
        </div>
        <div class="sc-metric-card">
          <div class="sc-metric-val">Tunai &amp; QRIS</div>
          <div class="sc-metric-lbl">Bayar di Kasir atau Lewat Kode QR</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================================================================
       BAGIAN 3: Katalog Pilihan Kamar
       ======================================================================== -->
  <section class="section-suites" id="suites">
    <div class="section-head-center">
      <span class="eyebrow">Daftar Tipe Kamar</span>
      <h2 class="section-title">
        Pilihan Kamar <span class="script-accent">ZSL Residence</span>
      </h2>
      <p class="section-desc">
        Setiap tarif sudah termasuk sarapan pagi untuk tamu terdaftar, akses Wi-Fi, dan air mineral harian.
      </p>
    </div>

    <div class="suites-grid">
      
      <!-- Tipe 1: Kamar Standar -->
      <article class="suite-card">
        <div class="suite-photo-wrapper">
          <img src="{{ asset('images/rooms/demo-suite-1.png') }}" alt="Kamar Standar ZSL Residence" class="suite-photo">
          <span class="suite-tag-pill">Lantai 1 &bull; Kamar 101–105</span>
        </div>
        <div class="suite-card-body">
          <h3 class="suite-title">Kamar Standar</h3>
          <p class="suite-desc">
            Kamar nyaman dengan ranjang Queen, AC, Smart TV, meja kerja, serta kamar mandi shower air panas.
          </p>
          <div class="specs-strip">
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>2 Tamu</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 19h20M2 5v14M22 5v14M2 9h20"></path></svg>
              <span>1 Ranjang Queen</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
              <span>Luas 45 m&sup2;</span>
            </div>
          </div>
          <div class="suite-card-footer">
            <div class="price-box">
              <span class="price-val">Rp 300.000</span>
              <span class="price-unit">/malam &bull; Termasuk Sarapan</span>
            </div>
            <button type="button" @click="mulaiBooking('Standar')" class="btn-circle-arrow" aria-label="Pesan Kamar Standar" style="border: none; cursor: pointer;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </article>

      <!-- Tipe 2: Kamar Deluxe -->
      <article class="suite-card">
        <div class="suite-photo-wrapper">
          <img src="{{ asset('images/rooms/demo-suite-2.png') }}" alt="Kamar Deluxe ZSL Residence" class="suite-photo">
          <span class="suite-tag-pill">Lantai 2 &bull; Kamar 201–205</span>
        </div>
        <div class="suite-card-body">
          <h3 class="suite-title">Kamar Deluxe</h3>
          <p class="suite-desc">
            Ruang lebih lapang dengan ranjang King, balkon pribadi, minibar, pembuat kopi, serta kamar mandi bathtub dan shower.
          </p>
          <div class="specs-strip">
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>2 Tamu</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 19h20M2 5v14M22 5v14M2 9h20"></path></svg>
              <span>1 Ranjang King</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
              <span>Luas 65 m&sup2;</span>
            </div>
          </div>
          <div class="suite-card-footer">
            <div class="price-box">
              <span class="price-val">Rp 550.000</span>
              <span class="price-unit">/malam &bull; Sarapan &amp; Minibar</span>
            </div>
            <button type="button" @click="mulaiBooking('Deluxe')" class="btn-circle-arrow" aria-label="Pesan Kamar Deluxe" style="border: none; cursor: pointer;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </article>

      <!-- Tipe 3: Kamar Suite -->
      <article class="suite-card">
        <div class="suite-photo-wrapper">
          <img src="{{ asset('images/rooms/demo-suite-3.png') }}" alt="Kamar Suite ZSL Residence" class="suite-photo">
          <span class="suite-tag-pill">Lantai 3 &bull; Kamar 301–304</span>
        </div>
        <div class="suite-card-body">
          <h3 class="suite-title">Kamar Suite Keluarga</h3>
          <p class="suite-desc">
            Unit paling luas untuk keluarga atau rombongan dengan ruang duduk terpisah, meja makan, dan dua area tidur.
          </p>
          <div class="specs-strip">
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>4 Tamu</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 19h20M2 5v14M22 5v14M2 9h20"></path></svg>
              <span>1 King + 2 Single</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
              <span>Luas 120 m&sup2;</span>
            </div>
          </div>
          <div class="suite-card-footer">
            <div class="price-box">
              <span class="price-val">Rp 950.000</span>
              <span class="price-unit">/malam &bull; Ruang Tamu Terpisah</span>
            </div>
            <button type="button" @click="mulaiBooking('Suite')" class="btn-circle-arrow" aria-label="Pesan Kamar Suite Keluarga" style="border: none; cursor: pointer;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </article>

    </div>
  </section>

  <!-- ========================================================================
       BAGIAN 4: Fasilitas & Layanan Hotel
       ======================================================================== -->
  <section class="section-story" id="story">
    <div class="section-head-center">
      <span class="eyebrow">Layanan Tamu</span>
      <h2 class="section-title">
        Proses Menginap yang <span class="script-accent" style="white-space: nowrap;">Jelas &amp; Praktis</span>
      </h2>
      <p class="section-desc">
        Dari pemesanan mandiri hingga pengembalian kunci saat check-out, semua tercatat rapi di sistem meja depan.
      </p>
    </div>

    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
        </div>
        <h3 class="value-title">Kamar Dicek Sebelum <span style="white-space: nowrap;">Check-In</span></h3>
        <p class="value-desc">
          Kunci hanya diserahkan untuk kamar berstatus bersih setelah petugas tata graha menyelesaikan pembersihan.
        </p>
      </div>

      <div class="value-card">
        <div class="value-icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
            <line x1="6" y1="1" x2="6" y2="4"></line>
            <line x1="10" y1="1" x2="10" y2="4"></line>
            <line x1="14" y1="1" x2="14" y2="4"></line>
          </svg>
        </div>
        <h3 class="value-title">Termasuk Sarapan &amp; Wi-Fi</h3>
        <p class="value-desc">
          Semua unit dilengkapi sarapan pagi harian, perlengkapan mandi, air mineral, serta koneksi internet gratis.
        </p>
      </div>

      <div class="value-card">
        <div class="value-icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <h3 class="value-title">Resepsionis Siaga 24 Jam</h3>
        <p class="value-desc">
          Butuh datang lebih malam, pembatalan jadwal, atau kuitansi cetak? Staf meja depan siap membantu kapan saja.
        </p>
      </div>
    </div>
  </section>

  <!-- ========================================================================
       BAGIAN 5: Ajakan Reservasi
       ======================================================================== -->
  <section class="cta-banner-wrapper">
    <div class="cta-banner">
      <div class="cta-banner-inner">
        <span class="eyebrow" style="color: var(--gold-300);">Reservasi Langsung</span>
        <h2 class="cta-title">
          Siap Memesan Kamar <span class="script-accent-gold" style="white-space: nowrap;">Hari Ini?</span>
        </h2>
        <p class="cta-desc">
          Masuk atau daftar akun tamu dalam satu menit untuk memilih tanggal, mengecek ketersediaan unit, dan mencetak voucher booking.
        </p>
        <button type="button" @click="mulaiBooking()" class="btn-pill-gold" style="border: none; cursor: pointer;">
          <span>Pesan Kamar Sekarang</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </section>

  @if (config('app.demo_enabled'))
  <!-- ========================================================================
       Portal Pengujian (Simulasi Akses Multi-Peran)
       ======================================================================== -->
  <section class="evaluator-portal" data-sc-in>
    <span class="evaluator-badge">SIMULASI AKSES CEPAT PENGUJIAN</span>
    <h3 class="evaluator-title">Simulasi Akses Multi-Peran &amp; Pelanggan</h3>
    <p class="evaluator-desc">
      Pilih peran di bawah untuk langsung mencoba akses sistem HotelOps dan simulasi pemesanan kamar tamu tanpa input manual:
    </p>

    <div class="evaluator-buttons" data-sc-in data-sc-stagger="90">
      <a href="{{ route('demo.login', ['peran' => 'Tamu', 'redirect' => 'booking']) }}" class="btn-eval-role" style="border-color: var(--gold-500); background: rgba(198, 155, 86, 0.1);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <span>Masuk: <strong>Pelanggan / Tamu (Ahmad Fauzi)</strong></span>
      </a>
      <a href="{{ route('demo.login', 'Resepsionis') }}" class="btn-eval-role">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
        <span>Masuk: <strong>Resepsionis (Meja Depan)</strong></span>
      </a>
      <a href="{{ route('demo.login', 'Petugas Kebersihan') }}" class="btn-eval-role">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
        <span>Masuk: <strong>Petugas Kebersihan (Tata Graha)</strong></span>
      </a>
      <a href="{{ route('demo.login', 'Manajer') }}" class="btn-eval-role">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
        <span>Masuk: <strong>Manajer Hotel (Eksekutif)</strong></span>
      </a>
    </div>
  </section>
  @endif

  <!-- ========================================================================
       Kaki Halaman (Lumina Footer)
       ======================================================================== -->
  <footer id="footer" class="lumina-footer">
    <div class="footer-inner">
      <div class="footer-brand">
        <img src="{{ asset('brand/zsl-residence/lockups/zsl-logo-horizontal-on-dark.svg') }}" alt="ZSL Residence" style="height: 56px; width: auto; display: block;">
        <p style="color: #8C97A8; font-size: 0.8rem; margin-top: 8px;">Kenyamanan Menginap untuk Kerja dan Keluarga</p>
      </div>
      <div style="display: flex; gap: 2rem; font-size: 0.82rem;">
        <span style="color: var(--gold-400);">zikri +6282259258947</span>
        <span>layanan@zslresidence.com</span>
      </div>
    </div>
    <div class="footer-copyright">
      <p>&copy; {{ date('Y') }} ZSL Residence Hotel &amp; Resor. Sistem Manajemen Operasional Hotel.</p>
    </div>
  </footer>

  <!-- ========================================================================
       MODAL AUTENTIKASI DIPERLUKAN (WAJIB LOGIN / REGISTER SEBELUM BOOKING)
       ======================================================================== -->
  <div x-cloak x-show="openAuthPrompt" class="lumina-modal-backdrop" @keydown.escape.window="openAuthPrompt = false">
    <div class="lumina-modal-dialog" style="max-width: 500px; text-align: center;" @click.outside="openAuthPrompt = false">
      <div style="display: flex; justify-content: flex-end;">
        <button type="button" class="lumina-modal-close" @click="openAuthPrompt = false" aria-label="Tutup Peringatan">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>

      <div style="width: 58px; height: 58px; border-radius: 20px; background: rgba(198, 155, 86, 0.15); color: var(--gold-600); display: flex; align-items: center; justify-content: center; margin: -10px auto 14px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
      </div>

      <span class="modal-badge" style="margin: 0 auto 8px;">
        <span class="modal-badge-dot"></span>
        Autentikasi Tamu Diperlukan
      </span>

      <h2 class="lumina-modal-title" style="font-size: 1.4rem; margin-bottom: 8px;">
        Masuk atau Daftar Terlebih Dahulu
      </h2>
      <p class="lumina-modal-subtitle" style="max-width: 400px; margin: 0 auto 22px; font-size: 0.82rem; line-height: 1.55;">
        Untuk memesan kamar pilihan Anda di <strong>ZSL Residence Hotel &amp; Resor</strong>, silakan masuk ke akun Anda atau mendaftarkan akun tamu baru terlebih dahulu.
      </p>

      <!-- 2 Tombol Aksi: Masuk & Daftar -->
      <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
        <a :href="'{{ route('login') }}?redirect=booking' + (bookingQuery() ? '&' + bookingQuery() : '')" class="btn-booking-submit" style="text-decoration: none;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
          <span>Masuk ke Akun Saya</span>
        </a>

        <a :href="'{{ route('register') }}?redirect=booking' + (bookingQuery() ? '&' + bookingQuery() : '')" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 0.85rem 1.5rem; background: #fff; color: var(--navy-900); border: 2px solid var(--navy-900); border-radius: var(--radius-pill); font-weight: 800; font-size: 0.88rem; text-decoration: none; transition: all 0.2s ease;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
          <span>Daftar Akun Tamu Baru</span>
        </a>

        @if (config('app.demo_enabled'))
        <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0 2px;">
          <div style="flex: 1; height: 1px; background: var(--warm-200);"></div>
          <span style="font-size: 0.68rem; font-weight: 700; color: var(--gold-600); text-transform: uppercase;">Atau Pengujian Cepat</span>
          <div style="flex: 1; height: 1px; background: var(--warm-200);"></div>
        </div>

        <a href="{{ route('demo.login', ['peran' => 'Tamu', 'redirect' => 'booking']) }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 0.75rem 1rem; background: rgba(198, 155, 86, 0.12); color: var(--navy-900); border: 1px dashed var(--gold-500); border-radius: var(--radius-pill); font-weight: 800; font-size: 0.82rem; text-decoration: none; transition: all 0.2s ease;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          <span>Simulasi Cepat Pelanggan (Ahmad Fauzi)</span>
        </a>
        @endif
      </div>

      <!-- Feature Strip -->
      <div style="border-top: 1px solid var(--warm-200); padding-top: 14px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; font-size: 0.7rem; color: var(--text-body);">
        <div>
          <strong style="display: block; color: var(--navy-900);">1 Menit</strong>
          <span>Daftar Cepat</span>
        </div>
        <div>
          <strong style="display: block; color: var(--navy-900);">Voucher Resmi</strong>
          <span>Cetak Langsung</span>
        </div>
        <div>
          <strong style="display: block; color: var(--navy-900);">Jaminan Kamar</strong>
          <span>Harga Terbaik</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Penanda Bagian Aktif Navigasi Atas -->
  <script>
    (function() {
      var navLinks = Array.prototype.slice.call(document.querySelectorAll('.nav-links a[href^="#"]'));
      var navSections = navLinks.map(function(link) {
        var target = document.querySelector(link.getAttribute('href'));
        return target ? { id: target.id, el: target, link: link } : null;
      }).filter(Boolean);

      function setActiveNav(id) {
        navLinks.forEach(function(link) {
          var isMatch = link.getAttribute('href') === '#' + id;
          link.classList.toggle('active', isMatch);
        });
      }

      navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
          var href = link.getAttribute('href');
          if (href && href.length > 1) {
            setActiveNav(href.slice(1));
          }
        });
      });

      function updateActiveSection() {
        if (!navSections.length) return;
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;
        var activeId = navSections[0].id;
        var triggerLine = 180;

        for (var i = 0; i < navSections.length; i++) {
          if (navSections[i].el.getBoundingClientRect().top <= triggerLine) {
            activeId = navSections[i].id;
          }
        }

        if ((window.innerHeight + scrollY) >= document.documentElement.scrollHeight - 40) {
          activeId = navSections[navSections.length - 1].id;
        }

        setActiveNav(activeId);
      }

      window.addEventListener('scroll', updateActiveSection, { passive: true });
      updateActiveSection();
    })();
  </script>
</body>
</html>
