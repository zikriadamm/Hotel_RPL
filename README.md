# ZSL Residence (HotelOps) — Sistem Informasi Manajemen Hotel & Resor

> **Tugas Besar Rekayasa Perangkat Lunak (RPL) - Semester 3**  
> Sistem Reservasi Tamu & Manajemen Operasional Hotel (*Property Management System / Front Desk System*) untuk **ZSL Residence Hotel & Resor** berbasis **Laravel 10**, **Blade**, **Tailwind CSS**, dan **Alpine.js**.

---

## 🌟 Fitur Utama

1. **Portal Pelanggan & Reservasi Mandiri:**
   - **Landing Page & Katalog Suite:** 9 pilihan unit/suite lengkap beserta fasilitas terperinci dan pengecekan ketersediaan tanggal.
   - **Booking & Pembayaran QRIS Dinamis:** Perhitungan total tarif otomatis, payload EMVCo QRIS atas nama `ZSL RESIDENCE HOTEL`, webhook settlement (`/api/webhook/qris`), serta cetak voucher reservasi elektronik.
   - **Portal Tamu:** Riwayat reservasi, pembatalan pesanan mandiri, membership tier (`ZSL Classic`, `ZSL Prestige Gold`, `ZSL Elite Royal`), serta CRUD profil dan kata sandi.

2. **Denah Kamar Visual Interaktif:**
   - Monitoring status kamar real-time: *Tersedia*, *Terisi*, *Kotor*, dan *Perawatan*.
   - Panel inspektur interaktif di sisi kanan untuk melihat rincian tamu, kamar, dan tindakan cepat.

3. **Otomatisasi Sinkronisasi Status Kamar:**
   - **Check-In:** Memvalidasi kesiapan kamar (`Tersedia` dan `Bersih`) sebelum mengubah status kamar menjadi **Terisi**.
   - **Check-Out & Kasir:** Menghitung total tagihan, nominal pembayaran tunai/transfer/QRIS, serta kembalian secara otomatis, lalu mengubah status kamar menjadi **Kotor**.
   - **Housekeeping:** Petugas kebersihan memverifikasi dan menandai kamar selesai dibersihkan, sehingga otomatis kembali menjadi **Tersedia & Bersih**.

4. **Otorisasi Hak Akses Berbasis Peran (RBAC):**
   - **Pelanggan / Tamu:** Katalog akomodasi, pemesanan mandiri, riwayat transaksi, dan pengelolaan akun.
   - **Resepsionis:** Registrasi buku tamu, pembuatan reservasi kamar, check-in tamu, kasir pembayaran & cetak kuitansi.
   - **Petugas Kebersihan (Housekeeping):** Papan tugas antrean kamar kotor dan verifikasi kamar bersih.
   - **Manajer Hotel:** Kontrol penuh master data kamar (CRUD), manajemen akun staf, serta akses ke seluruh laporan.

5. **Tiga Format Laporan Manajerial & Cetak:**
   - **Laporan Okupansi Harian:** Analisis tingkat keterisian unit hotel per hari.
   - **Laporan Pendapatan Bulanan:** Rekapitulasi transaksi penerimaan kas (Tunai, Bank, QRIS).
   - **Laporan Riwayat Tamu Menginap:** Catatan histori durasi dan kamar yang pernah disewa.
   - **Kuitansi & Kop Resmi ZSL Residence:** Tampilan siap cetak (*print-friendly*) lengkap dengan simbol *Gerbang Senja*.

6. **Desain Sistem & Identitas Merek:**
   - **Identitas Visual:** Paket logo **ZSL Residence ("Gerbang Senja")** pada `public/brand/zsl-residence/` lengkap dengan favicon dan web manifest.
   - **Palet Warna:** *Midnight Navy* (`#0F1D38` / `#0A1526`), *Burnished Brass / Champagne Gold* (`#A37730` / `#C69B56`), dan *Warm Ivory* (`#FCFAF7`).
   - **Font Pairing:** **Plus Jakarta Sans** untuk antarmuka & keterbacaan data, dipadukan dengan **Yellowtail** sebagai aksen skrip editorial.

---

## 🚀 Panduan Menjalankan Sistem Lokal

### 1. Kebutuhan Sistem (Prerequisites)
- PHP >= 8.1 dengan ekstensi `pdo_sqlite` atau `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`.
- Composer
- Web browser modern

### 2. Instalasi & Setup

```bash
# 1. Clone repositori
git clone <URL_REPO_GITHUB_ANDA>
cd "tubes lagi"

# 2. Salin environment file
cp .env.example .env

# 3. Instal dependensi Composer (jika belum ada folder vendor)
composer install

# 4. Generate Application Key
php artisan key:generate

# 5. Jalankan migrasi dan seeder database
touch database/database.sqlite
php artisan migrate:fresh --seed

# 6. Jalankan server lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser di: **http://127.0.0.1:8000**

---

## 🔑 Akun Demo Pengujian

Aplikasi dilengkapi tombol **1-Klik Demo Login** pada halaman login untuk memudahkan pengujian oleh dosen/penguji:

| Peran (Role) | Email | Kata Sandi | Akses Modul |
| :--- | :--- | :--- | :--- |
| **Manajer Hotel** | `manajer@hotel.com` | `password123` | Master Kamar, Manajemen Staf, Seluruh Laporan |
| **Resepsionis** | `resepsionis@hotel.com` | `password123` | Denah Kamar, Reservasi, Buku Tamu, Kasir Check-out |
| **Petugas Kebersihan** | `kebersihan@hotel.com` | `password123` | Papan Tugas Housekeeping & Verifikasi Bersih |
| **Pelanggan / Tamu** | `ahmad.fauzi@example.com` | `password123` | Katalog Suite, Booking Mandiri, Riwayat, Profil |

---

## 🧪 Pengujian Otomatis (Automated Testing)

Sistem telah diuji dengan pengujian fitur otomatis (Unit & Feature Tests) menggunakan PHPUnit:

```bash
php artisan test
```

**Hasil Pengujian:**
- **33 Tests passed (120 assertions)** — mencakup validasi nomor kamar, tarif > 0, proteksi RBAC, portal pelanggan & booking mandiri, kasir check-out, webhook QRIS, serta penolakan check-in jika kamar masih terisi atau kotor.

---

## ⚙️ Continuous Integration (GitHub Actions)

Repositori ini menggunakan **GitHub Actions** untuk memeriksa kualitas kode dan menjalankan pengujian otomatis pada setiap `push` dan `pull_request`.

- **Berkas Workflow:** [`.github/workflows/ci.yml`](.github/workflows/ci.yml)
- **Lingkungan Runner:** `ubuntu-latest` dengan **PHP 8.1** (`pdo_sqlite`, `sqlite3`, `mbstring`, `xml`, `ctype`, `iconv`, `intl`) dan database pengujian **SQLite `:memory:`** (sesuai `phpunit.xml`).
- **Tahapan Pipeline:**
  1. `actions/checkout@v4` — Mengambil kode terbaru dari repositori.
  2. `shivammathur/setup-php@v2` — Menyiapkan runtime PHP 8.1 beserta ekstensi yang dibutuhkan.
  3. `cp .env.example .env` — Menyiapkan berkas environment uji.
  4. `composer install --no-interaction --prefer-dist --optimize-autoloader` — Menginstal seluruh dependensi Composer.
  5. `php artisan key:generate` — Membuat `APP_KEY` untuk lingkungan CI.
  6. `find app routes tests -name "*.php" -print0 | xargs -0 -n1 php -l` — Memeriksa sintaks PHP (*lint*).
  7. `php artisan test` — Menjalankan seluruh unit & feature test PHPUnit.

### Menjalankan Langkah CI Secara Lokal

**Linux / macOS / Git Bash:**
```bash
cp -n .env.example .env
composer install --no-interaction --prefer-dist
php artisan key:generate
find app routes tests -name "*.php" -print0 | xargs -0 -n1 php -l
php artisan test
```

**Windows (PowerShell + PHP Laragon):**
```powershell
if (-not (Test-Path .env)) { Copy-Item .env.example .env }
composer install --no-interaction --prefer-dist
php artisan key:generate
Get-ChildItem -Recurse app, routes, tests -Filter *.php | ForEach-Object { php -l $_.FullName }
php artisan test
```

---

## 📄 Dokumen Terkait
- [docs/LAPORAN_CI.md](docs/LAPORAN_CI.md): Laporan Tugas Pipeline CI (Bukti Run, Ringkasan Error & Perbaikan, Pembagian Peran Tim).
- [docs/ci-cd-report.md](docs/ci-cd-report.md): Ringkasan Laporan Praktik Pipeline CI/CD.
- [design.md](design.md): Panduan Desain Sistem & Token UI/UX.
- [PLAN_SISTEM_HOTEL_OPERASIONAL.md](PLAN_SISTEM_HOTEL_OPERASIONAL.md): Perencanaan dan spesifikasi kebutuhan fungsional sistem.
