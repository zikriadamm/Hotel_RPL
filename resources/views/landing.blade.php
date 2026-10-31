<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lumina Grand Hotel &amp; Resor — Kemewahan Modern yang Hangat</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Yellowtail&display=swap" rel="stylesheet">
  
  <!-- ScrollCraft Engine Runtime Styles -->
  <link rel="stylesheet" href="{{ asset('css/scrollcraft.css') }}">
  <!-- Lumina Grand Design System Stylesheet -->
  <link rel="stylesheet" href="{{ asset('css/lumina-grand.css') }}">
  <!-- Alpine.js Reactive Engine -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{
    openAuthPrompt: {{ !auth()->check() ? (request('booking') ? 'true' : 'false') : 'false' }},
    isLoggedIn: {{ auth()->check() ? 'true' : 'false' }},
    checkIn: '{{ date('Y-m-d') }}',
    checkOut: '{{ date('Y-m-d', strtotime('+1 day')) }}',
    mulaiBooking(tipe) {
      if (!this.isLoggedIn) {
        this.openAuthPrompt = true;
      } else {
        window.location.href = '{{ route('booking.form') }}' + (tipe ? '?tipe=' + encodeURIComponent(tipe) : '');
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
       Scroll Progress Bar (Garis Progres Emas 3px Puncak Layar)
       ======================================================================== -->
  <div class="lumina-scroll-progress" id="scroll-progress"></div>

  <!-- ========================================================================
       Bilah Navigasi Atas (Sticky Nav)
       ======================================================================== -->
  <header class="lumina-nav">
    <a href="{{ route('landing') }}" class="nav-brand">
      <span class="brand-title">LUMINA GRAND</span>
      <span class="brand-subtitle">Hotel &amp; Resor</span>
    </a>

    <nav class="nav-links">
      <a href="#experiences" class="active">Pengalaman</a>
      <a href="#philosophy">Filosofi</a>
      <a href="#suites">Akomodasi</a>
      <a href="#story">Pelayanan</a>
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
        <a href="{{ route('login') }}" class="btn-link-subtle">Portal Staf</a>
        <a href="{{ route('login', ['redirect' => 'booking']) }}" class="btn-link-subtle" style="font-weight: 700; color: var(--navy-900);">Masuk</a>
        <a href="{{ route('register', ['redirect' => 'booking']) }}" class="btn-link-subtle" style="font-weight: 700; color: var(--gold-600);">Daftar</a>
        <button type="button" @click="mulaiBooking()" class="btn-pill-navy" style="cursor: pointer; border: none;">Pesan Kamar</button>
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
      <span class="eyebrow" style="color: var(--gold-300);">Puncak Keramahan yang Hangat</span>
      
      <!-- Signature Move: Aksen Tulisan Sambung -->
      <h1 class="hero-title">
        Kemegahan Arsitektur Berpadu <span class="script-accent">Ketenangan Abadi</span>
      </h1>
      
      <p class="hero-desc">
        Setiap ruang dirancang dengan perpaduan wibawa modern dan kehangatan personal, menghadirkan peristirahatan bintang lima yang melampaui ekspektasi.
      </p>

      <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center; margin-top: 1.5rem;">
        <a href="#suites" class="btn-pill-gold" data-sc-magnet="0.2">
          <span>Jelajahi Suite Kamar</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="#booking" class="btn-pill-navy" style="background: rgba(10, 21, 38, 0.7); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,0.2);">
          <span>Cek Ketersediaan</span>
        </a>
      </div>
    </div>

    <!-- Penanda Gulir Halaman -->
    <div class="scroll-cue" id="scroll-cue">
      <span class="scroll-cue-text">Gulir untuk eksplorasi</span>
      <div class="scroll-cue-pill">
        <div class="scroll-cue-dot"></div>
      </div>
    </div>
  </section>

  <!-- ========================================================================
       Bilah Formulir Melayang (Floating Booking Bar)
       ======================================================================== -->
  <div class="floating-bar-anchor" id="booking" data-sc-in="scale">
    <form @submit.prevent="mulaiBooking()" class="floating-bar">
      <!-- Kolom 1: Destinasi & Pilihan Akomodasi -->
      <div class="floating-col">
        <svg class="col-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
        <div class="col-content">
          <span class="col-label">Pilihan Akomodasi</span>
          <select class="col-input" x-model="tipeKamar" style="font-weight: 700;">
            <option value="Standar">Suite Standar (Rp 300.000)</option>
            <option value="Deluxe">Villa Eksekutif (Rp 550.000)</option>
            <option value="Suite">Penthouse Presidensial (Rp 950.000)</option>
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
          <div style="display: flex; gap: 8px;">
            <input type="date" class="col-input" x-model="checkIn" min="{{ date('Y-m-d') }}">
            <span style="color: var(--gold-600);">&rarr;</span>
            <input type="date" class="col-input" x-model="checkOut" :min="checkIn">
          </div>
        </div>
      </div>

      <!-- Kolom 3: Status Unit -->
      <div class="floating-col">
        <svg class="col-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
          <circle cx="9" cy="7" r="4"></circle>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
        </svg>
        <div class="col-content">
          <span class="col-label">Ketersediaan Kamar</span>
          <span class="col-input" style="display: flex; align-items: center; font-weight: 700; color: #065F46;">
            {{ $kamarTersedia ?? 7 }} Kamar Siap Ditempati
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
       BAGIAN 2: Filosofi & Metrik Operasional Langsung (Live Counting)
       ======================================================================== -->
  <section class="sc-philosophy-section" id="philosophy">
    <div class="sc-philosophy-box" data-sc-in>
      <span class="eyebrow">Keunggulan Operasional</span>
      
      <h2 class="sc-philosophy-lead">
        "Setiap ruang dirancang dengan perpaduan wibawa modern dan kehangatan personal, menghadirkan peristirahatan yang melampaui ekspektasi."
      </h2>

      <p style="color: var(--gold-600); font-weight: 600; font-size: 1.15rem; max-width: 680px; margin: 0 auto 3rem auto; line-height: 1.6;">
        Diorkestrasi oleh arsitektur operasional real-time: inspeksi kebersihan kamar tervalidasi sebelum Anda tiba.
      </p>

      <!-- Kotak Metrik Operasional dengan Animasi Berhitung Live -->
      <div class="sc-metrics-strip" data-sc-in data-sc-stagger="120">
        <div class="sc-metric-card" data-sc-tilt="6">
          <div class="sc-metric-val">
            <span data-sc-count="0 100" data-sc-count-ms="1400">0</span>%
          </div>
          <div class="sc-metric-lbl">Standar Higienis Terverifikasi</div>
        </div>
        <div class="sc-metric-card" data-sc-tilt="6">
          <div class="sc-metric-val">
            &lt; <span data-sc-count="0 3" data-sc-count-ms="1200">0</span> Menit
          </div>
          <div class="sc-metric-lbl">Rata-Rata Layanan Masuk (Check-In)</div>
        </div>
        <div class="sc-metric-card" data-sc-tilt="6">
          <div class="sc-metric-val">
            24/7
          </div>
          <div class="sc-metric-lbl">Layanan Butler &amp; Pendampingan 24 Jam</div>
        </div>
      </div>
    </div>
  </section>

  <!-- ========================================================================
       BAGIAN 3: Katalog Pilihan Kamar Suite (Animasi Bertingkat / Staggered)
       ======================================================================== -->
  <section class="section-suites" id="suites">
    <div class="section-head-center" data-sc-in>
      <span class="eyebrow">Pilihan Akomodasi</span>
      <h2 class="section-title">
        Suite Privat &amp; <span class="script-accent">Peristirahatan</span>
      </h2>
      <p class="section-desc">
        Koleksi peristirahatan mewah yang memadukan kayu jati hangat, marmer alami, dan kemudahan cerdas.
      </p>
    </div>

    <div class="suites-grid" data-sc-in data-sc-stagger="140">
      
      <!-- Suite 1: Suite Deluxe Standar -->
      <article class="suite-card" data-sc-tilt="6">
        <div class="suite-photo-wrapper">
          <img src="{{ asset('storage/rooms/demo-suite-1.png') }}" alt="Suite Deluxe Standar" class="suite-photo" onerror="this.src='https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=800&q=80'">
          <span class="suite-tag-pill">Lantai 1 &bull; Unit 101-103</span>
          <button class="suite-wishlist-btn" aria-label="Simpan ke daftar favorit">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
          </button>
        </div>
        <div class="suite-card-body">
          <h3 class="suite-title">Suite Deluxe Standar</h3>
          <p class="suite-desc">
            Ruang tenang bernuansa kayu jati dengan tempat tidur Queen-size, pancuran air marmer, dan meja kerja ergonomis.
          </p>
          <div class="specs-strip">
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>2 Tamu</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 19h20M2 5v14M22 5v14M2 9h20"></path></svg>
              <span>1 Tempat Tidur Queen</span>
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
            <button type="button" @click="mulaiBooking('Standar')" class="btn-circle-arrow" aria-label="Pesan Suite Deluxe Standar" style="border: none; cursor: pointer;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </article>

      <!-- Suite 2: Villa Eksekutif Tepi Laut -->
      <article class="suite-card" data-sc-tilt="6">
        <div class="suite-photo-wrapper">
          <img src="{{ asset('storage/rooms/demo-suite-2.png') }}" alt="Villa Eksekutif Tepi Laut" class="suite-photo" onerror="this.src='https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80'">
          <span class="suite-tag-pill">Lantai 2 &bull; Unit 201-203</span>
          <button class="suite-wishlist-btn" aria-label="Simpan ke daftar favorit">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
          </button>
        </div>
        <div class="suite-card-body">
          <h3 class="suite-title">Villa Eksekutif Tepi Laut</h3>
          <p class="suite-desc">
            Balkon privat yang menghadap pemandangan senja, bathtub teraso terbuka, minibar pilihan, dan tata suara terintegrasi.
          </p>
          <div class="specs-strip">
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>2 Tamu</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 19h20M2 5v14M22 5v14M2 9h20"></path></svg>
              <span>1 Tempat Tidur King</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
              <span>Luas 65 m&sup2;</span>
            </div>
          </div>
          <div class="suite-card-footer">
            <div class="price-box">
              <span class="price-val">Rp 550.000</span>
              <span class="price-unit">/malam &bull; Sajian Teh Sore</span>
            </div>
            <button type="button" @click="mulaiBooking('Deluxe')" class="btn-circle-arrow" aria-label="Pesan Villa Eksekutif Tepi Laut" style="border: none; cursor: pointer;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </article>

      <!-- Suite 3: Penthouse Presidensial -->
      <article class="suite-card" data-sc-tilt="6">
        <div class="suite-photo-wrapper">
          <img src="{{ asset('storage/rooms/demo-suite-3.png') }}" alt="Penthouse Presidensial" class="suite-photo" onerror="this.src='https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80'">
          <span class="suite-tag-pill">Penthouse &bull; Unit 301-302</span>
          <button class="suite-wishlist-btn" aria-label="Simpan ke daftar favorit">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
          </button>
        </div>
        <div class="suite-card-body">
          <h3 class="suite-title">Penthouse Presidensial</h3>
          <p class="suite-desc">
            Puncak kemewahan arsitektur dengan ruang tamu formal, kolam renang rendam privat, dan layanan butler berdedikasi.
          </p>
          <div class="specs-strip">
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              <span>4 Tamu</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 19h20M2 5v14M22 5v14M2 9h20"></path></svg>
              <span>Super King + 2 Twin Bed</span>
            </div>
            <div class="spec-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
              <span>Luas 120 m&sup2;</span>
            </div>
          </div>
          <div class="suite-card-footer">
            <div class="price-box">
              <span class="price-val">Rp 950.000</span>
              <span class="price-unit">/malam &bull; Layanan Butler &amp; Jamuan</span>
            </div>
            <button type="button" @click="mulaiBooking('Suite')" class="btn-circle-arrow" aria-label="Pesan Penthouse Presidensial" style="border: none; cursor: pointer;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </div>
      </article>

    </div>
  </section>

  <!-- ========================================================================
       BAGIAN 4: Pelayanan Pilihan (Kotak Bento Bertingkat)
       ======================================================================== -->
  <section class="section-story" id="story">
    <div class="section-head-center" data-sc-in>
      <span class="eyebrow">Keramahan Khas</span>
      <h2 class="section-title">
        Kenyamanan Sempurna dengan <span class="script-accent">Pelayanan Presisi</span>
      </h2>
      <p class="section-desc">
        Setiap detik menginap diorkestrasi secara harmonis. Dari sanitasi kamar bersertifikasi hingga layanan pendamping tamu personal yang peka terhadap setiap preferensi Anda.
      </p>
    </div>

    <div class="values-grid" data-sc-in data-sc-stagger="120">
      <!-- Layanan 1: Kebersihan Kamar -->
      <div class="value-card" data-sc-tilt="5">
        <div class="value-icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
          </svg>
        </div>
        <h3 class="value-title">Kebersihan Kamar Presisi</h3>
        <p class="value-desc">
          Status kebersihan kamar diverifikasi langsung melalui sistem operasional sebelum kunci diserahkan kepada Anda.
        </p>
      </div>

      <!-- Layanan 2: Gastronomi -->
      <div class="value-card" data-sc-tilt="5">
        <div class="value-icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
            <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
            <line x1="6" y1="1" x2="6" y2="4"></line>
            <line x1="10" y1="1" x2="10" y2="4"></line>
            <line x1="14" y1="1" x2="14" y2="4"></line>
          </svg>
        </div>
        <h3 class="value-title">Sajian Gastronomi Istimewa</h3>
        <p class="value-desc">
          Nikmati hidangan istimewa racikan koki eksekutif langsung dari kenyamanan ruang makan privat di dalam kamar Anda.
        </p>
      </div>

      <!-- Layanan 3: Layanan Butler -->
      <div class="value-card" data-sc-tilt="5">
        <div class="value-icon-box">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <h3 class="value-title">Layanan Butler Pribadi</h3>
        <p class="value-desc">
          Layanan butler 24 jam yang siap membantu setiap detail perjalanan, reservasi khusus, hingga kebutuhan relaksasi malam hari.
        </p>
      </div>
    </div>
  </section>

  <!-- ========================================================================
       BAGIAN 5: Banner Undangan Malam (Midnight Navy Scale Reveal)
       ======================================================================== -->
  <section class="cta-banner-wrapper" data-sc-in="scale">
    <div class="cta-banner">
      <div class="cta-banner-inner">
        <span class="eyebrow" style="color: var(--gold-300);">Layanan Reservasi Khusus</span>
        <h2 class="cta-title">
          Pesan Liburan Tak Terlupakan Anda <span class="script-accent-gold">Malam Ini</span>
        </h2>
        <p class="cta-desc">
          Nikmati tarif eksklusif pemesanan langsung, jamuan selamat datang saat check-in, dan jaminan kamar dengan pemandangan terbaik.
        </p>
        <button type="button" @click="mulaiBooking()" class="btn-pill-gold" data-sc-magnet="0.25" style="border: none; cursor: pointer;">
          <span>Pesan Langsung Sekarang</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </div>
    </div>
  </section>

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

  <!-- ========================================================================
       Kaki Halaman (Lumina Footer)
       ======================================================================== -->
  <footer id="footer" class="lumina-footer" data-sc-in>
    <div class="footer-inner">
      <div class="footer-brand">
        <span class="brand-title" style="font-size: 1.4rem; font-weight: 800; letter-spacing: 0.08em;">LUMINA GRAND</span>
        <p style="color: #616C7D; font-size: 0.8rem; margin-top: 4px;">Standar Kemewahan Modern yang Hangat</p>
      </div>
      <div style="display: flex; gap: 2rem; font-size: 0.82rem;">
        <span style="color: var(--gold-400);">Layanan Tamu (Concierge): +62 812 3456 7890</span>
        <span>layanan@luminagrand.com</span>
      </div>
    </div>
    <div class="footer-copyright">
      <p>&copy; {{ date('Y') }} Lumina Grand Hotel &amp; Resor. Sistem Manajemen Operasional Hotel.</p>
      <p style="color: #4C566A;">Ditenagai oleh Mesin Interaksi Scroll-Craft &bull; Laravel 10</p>
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
        Untuk memesan kamar pilihan Anda di <strong>Lumina Grand Hotel &amp; Resor</strong>, silakan masuk ke akun Anda atau mendaftarkan akun tamu baru terlebih dahulu.
      </p>

      <!-- 2 Tombol Aksi: Masuk & Daftar -->
      <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
        <a href="{{ route('login', ['redirect' => 'booking']) }}" class="btn-booking-submit" style="text-decoration: none;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
          <span>Masuk ke Akun Saya</span>
        </a>

        <a href="{{ route('register', ['redirect' => 'booking']) }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 0.85rem 1.5rem; background: #fff; color: var(--navy-900); border: 2px solid var(--navy-900); border-radius: var(--radius-pill); font-weight: 800; font-size: 0.88rem; text-decoration: none; transition: all 0.2s ease;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
          <span>Daftar Akun Tamu Baru</span>
        </a>

        <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0 2px;">
          <div style="flex: 1; height: 1px; background: var(--warm-200);"></div>
          <span style="font-size: 0.68rem; font-weight: 700; color: var(--gold-600); text-transform: uppercase;">Atau Pengujian Cepat</span>
          <div style="flex: 1; height: 1px; background: var(--warm-200);"></div>
        </div>

        <a href="{{ route('demo.login', ['peran' => 'Tamu', 'redirect' => 'booking']) }}" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 0.75rem 1rem; background: rgba(198, 155, 86, 0.12); color: var(--navy-900); border: 1px dashed var(--gold-500); border-radius: var(--radius-pill); font-weight: 800; font-size: 0.82rem; text-decoration: none; transition: all 0.2s ease;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          <span>Simulasi Cepat Pelanggan (Ahmad Fauzi)</span>
        </a>
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

  <!-- Script Runtime ScrollCraft -->
  <script src="{{ asset('js/scrollcraft.js') }}"></script>
  
  <!-- Pengendali Interaksi Gulir & Parallax -->
  <script>
    (function() {
      // 1. Jalankan Runtime Interaktif ScrollCraft
      if (window.ScrollCraft && typeof window.ScrollCraft.mount === 'function') {
        window.ScrollCraft.mount();
      }

      var progressBar = document.getElementById('scroll-progress');
      var scrollCue = document.getElementById('scroll-cue');
      var planeFar = document.getElementById('hero-plane-far');
      var planeMid = document.getElementById('hero-plane-mid');

      function onScroll() {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;
        var docHeight = document.documentElement.scrollHeight - window.innerHeight;
        
        // 2. Perbarui Bilah Progres Emas Puncak Layar
        if (progressBar && docHeight > 0) {
          var progress = Math.min(Math.max((scrollY / docHeight) * 100, 0), 100);
          progressBar.style.width = progress + '%';
        }

        // 3. Sembunyikan / Tampilkan Penanda Gulir
        if (scrollCue) {
          if (scrollY > 60) {
            scrollCue.classList.add('is-hidden');
          } else {
            scrollCue.classList.remove('is-hidden');
          }
        }

        // 4. Pergerakan Kedalaman Parallax Lapisan Hero
        if (planeFar && scrollY < window.innerHeight * 1.5) {
          planeFar.style.transform = 'translate3d(0, ' + (scrollY * 0.28) + 'px, 0)';
        }
        if (planeMid && scrollY < window.innerHeight * 1.5) {
          planeMid.style.transform = 'translate3d(0, ' + (scrollY * 0.14) + 'px, 0)';
        }
      }

      window.addEventListener('scroll', onScroll, { passive: true });
      onScroll();
    })();
  </script>
</body>
</html>
