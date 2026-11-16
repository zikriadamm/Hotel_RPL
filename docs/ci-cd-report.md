# Laporan Praktik Pipeline CI

## 1. Informasi Repository & Workflow
- **Repository**: https://github.com/zikriadamm/Hotel_RPL
- **Berkas Workflow**: `.github/workflows/ci.yml`
- **Branch**: `ci/setup-pipeline` -> `main` (PR #1) & `ci/team-collaboration` -> `main` (PR #2)

## 2. Bukti Eksekusi Pipeline
- **Run Gagal (Merah)**: https://github.com/zikriadamm/Hotel_RPL/actions/runs/43720701810
- **Run Sukses (Hijau — PR #1)**: https://github.com/zikriadamm/Hotel_RPL/actions/runs/43723142934
- **Run Sukses (Hijau — Kolaborasi Tim / `main`)**: https://github.com/zikriadamm/Hotel_RPL/actions/runs/43859364155

## 3. Ringkasan Error dan Langkah Perbaikan

### Error yang Ditemukan
Saat workflow dijalankan setelah menambahkan *regression test* check-in pada `tests/Feature/HotelOperationsTest.php`, langkah `php artisan test` gagal dengan error berikut:

```text
FAILED Tests\Feature\HotelOperationsTest > check in ditolak jika kamar masih terisi atau kotor
Session is missing expected key [error]. Failed asserting that false is true.
```

**Penyebab**: Method `FrontDeskController::checkIn()` sebelumnya hanya memeriksa status pemesanan tanpa memvalidasi kesiapan kamar (`status === 'Tersedia'` dan `status_kebersihan === 'Bersih'`), sehingga kamar yang masih `Terisi` atau `Kotor` tetap lolos check-in.

### Langkah Perbaikan
1. Menambahkan validasi `status !== 'Tersedia' || status_kebersihan !== 'Bersih'` pada `app/Http/Controllers/FrontDeskController.php`.
2. Melakukan commit perbaikan, push ulang ke branch `ci/setup-pipeline`, dan merge PR #1 ke `main`.
3. Memverifikasi di tab **Actions** bahwa `composer install`, `php -l`, dan `php artisan test` (32 tests di PR #1; 33 tests setelah kolaborasi PR #2) berjalan sukses (hijau).

## 4. Pembagian Peran Tim

| Nama Anggota | NIM | Peran | Tugas & Penjelasan |
| :--- | :--- | :--- | :--- |
| Muhammad Zikri Adam | F5512530117 | CI/CD & Backend Fix | Membuat workflow `.github/workflows/ci.yml`, memperbaiki validasi check-in di `FrontDeskController`, serta review & merge PR ke `main`. |
| Habibi | F5512530113 | DevOps & Optimization | Mengonfigurasi cache dependensi Composer (`actions/cache@v4`) pada `.github/workflows/ci.yml` untuk mempercepat eksekusi CI. |
| Bayu Jaladara Anantakuva | F5512520097 | Build & Verification | Menambahkan skrip `test` pada `composer.json` dan memverifikasi perintah lint (`php -l`) serta test berjalan konsisten secara lokal. |
| Tabitah Creaselia Belicia Theona | F5512520086 | QA / Testing | Memverifikasi skenario gagal-perbaikan dan menambahkan regression test check-in kamar kotor (`Test 31`) pada `HotelOperationsTest.php`. |
| Dian Abdillah Melosovic | F5512530112 | Dokumentasi | Menyusun panduan CI di `README.md`, ringkasan error/perbaikan serta catatan kolaborasi di `docs/LAPORAN_CI.md`, dan bukti pipeline. |
