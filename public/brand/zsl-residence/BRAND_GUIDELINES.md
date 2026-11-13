# ZSL Residence — Panduan Identitas & Penggunaan Logo

> Panduan ringkas penggunaan identitas visual resmi **ZSL Residence ("Gerbang Senja")** untuk media digital, antarmuka web, dan materi cetak perhotelan.

---

## 1. Filosofi & Struktur Logo
- **Konsep ("Gerbang Senja"):** Lengkungan kubah lobi bernuansa kuningan hangat (`#A37730` / `#C69B56`) menaungi monogram struktural **ZSL** serta ambang pintu masuk terbelah (`#0F1D38`) sebagai simbol penyambutan tamu yang tenang dan megah.
- **Varian Bentuk (*Lockups*):**
  1. **Simbol Tunggal (`source/zsl-symbol-master-color.svg`):** Ikon emblem persegi 1:1 untuk kartu kunci, *badge* profil, dan area terbatas.
  2. **Simbol Ukuran Kecil (`source/zsl-symbol-small.svg`):** Versi khusus tanpa balok ambang bawah untuk ukuran `16–24 px` (*favicon* tab browser & ikon mikro).
  3. **Lockup Horizontal (`lockups/zsl-logo-horizontal-color.svg`):** Varian utama untuk bilah navigasi situs web, kop surat, dan penanda area.
  4. **Lockup Bertumpuk (`lockups/zsl-logo-stacked-color.svg`):** Varian vertikal simetris untuk sampul depan, spanduk lobi, papan petunjuk, dan kartu sambutan kamar.
  5. **Wordmark Mandiri (`lockups/zsl-wordmark-color.svg`):** Tipografi `ZSL | RESIDENCE` tanpa kubah untuk area memanjang yang sangat tipis.
- **Panduan Berkas:**
  - `source/*.svg` & `lockups/*.svg` — Master vektor murni (tanpa `<text>` maupun bitmap; huruf dibangun dari kurva geometris).
  - `digital/*` — Turunan SVG & PNG transparan (`black`, `white`, `mono`, `square`, `app-icon`, 512–1200 px) untuk layar, presentasi, dan UI aplikasi.
  - `web-icons/*` — Set siap pasang untuk HTML (`favicon.ico`, `favicon.svg`, PNG `16/32/48/180/192/512`, `maskable-512.png`, `site.webmanifest`, dan `head-snippet.html`).

---

## 2. Zona Aman (*Clear Space*)
- Sisakan ruang kosong minimal sebesar **1× tinggi kaki horizontal huruf `L` / lebar celah tengah ambang pintu (`≈ 14 %` lebar simbol)** di keempat sisi logo pada skala apa pun.
- Di bilah navigasi web, pastikan terdapat jarak bernapas minimal `16 px` di kiri dan kanan logo agar tidak berimpitan dengan menu tautan.

---

## 3. Ukuran Minimum
| Varian | Layar Digital | Media Cetak |
| :--- | :--- | :--- |
| **Lockup Horizontal** | Lebar minimal **112 px** | Lebar minimal **28 mm** |
| **Lockup Bertumpuk** | Lebar minimal **96 px** | Lebar minimal **25 mm** |
| **Simbol Penuh** | Lebar minimal **28 px** | Lebar minimal **10 mm** |
| **Simbol Kecil (`-small` / Favicon)** | **16 × 16 px** (gunakan berkas di `web-icons/`) | Lebar minimal **6 mm** |

---

## 4. Sistem Warna Resmi

| Peran Warna | Nama Warna | HEX | RGB | CMYK (Cetak Proses) | Padanan Spot / Pantone (Estimasi) | Kontras |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| **Utama (Teks & Monogram)** | **Midnight Navy** | `#0F1D38` | `15, 29, 56` | `95, 80, 40, 58` | `Pantone 289 C` | `16.4:1` di atas `#FCFAF7` |
| **Aksen Terang (Kubah di Latar Terang)** | **Burnished Brass** | `#A37730` | `163, 119, 48` | `28, 48, 86, 18` | `Pantone 872 C / 7557 C` | `3.8:1` di atas `#FCFAF7` |
| **Aksen Gelap (Kubah di Latar Navy)** | **Champagne Gold** | `#C69B56` | `198, 155, 86` | `22, 38, 72, 6` | `Pantone 872 C` | `6.6:1` di atas `#0F1D38` |
| **Kanvas Hangat** | **Warm Ivory Stone** | `#FCFAF7` | `252, 250, 247` | `0, 1, 2, 0` | `Warm White / Kertas Natural` | — |

- **Kombinasi Latar yang Disetujui:**
  - **Latar Terang (`#FCFAF7` / `#FFFFFF`):** Gunakan varian `-color` (`#0F1D38` + `#A37730`), `-black`, atau `-mono-0f1d38`.
  - **Latar Gelap (`#0F1D38` / Foto Malam):** Gunakan varian `-on-dark` (`#FCFAF7` + `#C69B56`) atau versi putih yang sudah ditipiskan (`source/zsl-symbol-white-thinned.svg`) untuk mencegah efek mekarnya tinta cahaya (*irradiation*).
  - **Ukir Logam / Satu Warna:** Gunakan `digital/zsl-symbol-black.svg` atau `digital/zsl-symbol-mono-a37730.svg`.

---

## 5. Tipografi Pendukung
- **Judul & Tampilan Web:** `Plus Jakarta Sans` (Weight `700` & `800`) dan `Newsreader` (Italic `600i` untuk aksen editorial).
- **Teks Antarmuka & Badan:** `Plus Jakarta Sans` (`400`, `500`, `600`). System fallback: `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`.
- **Catatan Konstruksi Huruf & Lisensi:** Huruf `ZSL` dan `RESIDENCE` di dalam seluruh berkas SVG logo **digambar secara geometris sebagai kurva vektor murni (`<path>`)** — bukan teks hidup berbasis font eksternal — sehingga bebas ketergantungan font saat dibuka di perangkat apa pun. Font pendukung `Plus Jakarta Sans` dan `Newsreader` berlisensi terbuka (*SIL Open Font License 1.1* via Google Fonts).

---

## 6. Hal yang Dilarang (*Don'ts*)
- Jangan menarik, memipihkan, atau mengubah rasio aspek logo secara tidak proporsional.
- Jangan memutar kubah atau memisahkan huruf `ZSL` dan ambang pintu secara manual di luar varian resmi.
- Jangan menambahkan bayangan jatuh (*drop shadow*), garis luar (*outline*), atau efek cahaya (*glow*) di atas logo.
- Jangan mengetik ulang kata `RESIDENCE` memakai font biasa untuk mengganti *wordmark* resmi pada *lockup*.
- Jangan menempatkan logo berwarna Navy di atas foto gelap atau tekstur ramai tanpa wadah kontras tinggi.

---

## 7. Catatan Serah Terima & Batasan (*Handover Notes*)
- **Kesiapan Cetak & Produksi:** Seluruh bentuk telah diaudit (`svg_audit.py`: skor 99–100/100 tanpa `<text>` maupun bitmap). Untuk mesin potong vinil/ukir CNC kuningan, disarankan meninjau bukti cetak fisik (*Pantone/material proof*) karena berkas akhir disusun sebagai kurva SVG multi-elemen bersih.
- **Pemeriksaan Merek Dagang:** Pembuatan identitas ini mencakup riset pustaka desain lokal; tim hukum/pemilik disarankan tetap melakukan pencarian merek dagang (*trademark clearance search*) sebelum pendaftaran resmi komersial.
