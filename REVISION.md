# 📋 Rekapitulasi Revisi & Riwayat Perubahan (Changelog Developer)
**Proyek:** Rumah Makan Padang "RM Raso Mandeh"  
**Platform:** Web (Laravel 12 / PHP 8.5) & Mobile (Flutter 3.x / Dart 3.x)  
**Dokumen ini dibuat khusus sebagai referensi tunggal developer agar tidak perlu lagi mencari riwayat perubahan di chat WhatsApp.**

---

## 📱 1. Pembaruan Aplikasi Mobile (Flutter)

### A. Fitur Pencarian Menu Akurat & Relevan (Search Fix)
- **Masalah:**
  - Sebelumnya, filter pencarian mencocokkan kata kunci ke *deskripsi makanan* (`item.description`).
  - Akibatnya:
    - Mengetik `"rendang"` memunculkan **Paket Nasi Kapau** di urutan pertama (karena deskripsinya mencantumkan "... Rendang Daging ..." dan rating/ulasannya lebih tinggi).
    - Mengetik `"gulai"` memunculkan **Paket Nasi Kapau**, **Nasi + Ayam Pop** (karena ada frasa "... disiram kuah gulai ..."), dan **Sayur Daun Singkong** (karena "... gulai nangka ...").
- **Solusi & Implementasi:**
  - File: `mobile/lib/providers/menu_provider.dart`
  - Filter pencarian difokuskan secara ketat pada **Nama Menu** (`item.name`) dan **Kategori Resmi** (`item.category`).
  - Menghapus pencarian liar pada deskripsi menu.
  - Menambahkan **Relevance Sorting**:
    1. *Peringkat 1:* Menu yang namanya diawali kata kunci (misal: `"Gulai Daging"`).
    2. *Peringkat 2:* Menu yang namanya mengandung kata kunci.
- **Hasil:**
  - Cari `"rendang"` $\rightarrow$ Hanya menampilkan **Rendang Daging**.
  - Cari `"gulai"` $\rightarrow$ Hanya menampilkan **Gulai Daging** dan **Gulai Kepala Ikan**.
  - Cari `"ayam"` $\rightarrow$ Menampilkan **Nasi + Ayam Pop**, **Ayam Bakar Padang**, **Ayam Goreng Padang**.

---

### B. Logo Google Warna-Warni Resmi
- **Masalah:** Tombol *"Masuk dengan Akun Google"* pada form login menggunakan huruf 'G' berwarna biru tunggal (`#4285F4`).
- **Solusi & Implementasi:**
  - File: `mobile/lib/screens/auth/login_screen.dart`
  - Mengonversi aset `goole.webp` menjadi logo resmi Google 4-warna (*Merah, Kuning, Hijau, Biru*) beresolusi tajam (256x256 RGBA):
    - `mobile/assets/icons/ic_google.png`
    - `mobile/assets/icons/ic_google.webp`
  - Mengganti teks 'G' biru dengan widget `Image.asset('assets/icons/ic_google.png')`.

---

### C. Unduh Struk Digital Berfungsi Penuh (PDF & Storage)
- **Masalah:** Tombol *"Unduh Struk Digital (PDF)"* pada modal rincian pesanan hanya menampilkan `SnackBar` tiruan tanpa benar-benar membuat atau mengunduh dokumen.
- **Solusi & Implementasi:**
  - File Baru: `mobile/lib/services/receipt_service.dart`
  - File Diubah:
    - `mobile/lib/screens/order/order_detail_screen.dart`
    - `mobile/android/app/src/main/kotlin/com/rasamandeh/rasa_mandeh/MainActivity.kt`
    - `mobile/android/app/src/main/AndroidManifest.xml`
    - `mobile/android/app/src/main/res/xml/file_paths.xml`
  - **Kemampuan Fitur:**
    1. **Dynamic Data Binding:** Data struk otomatis terhubung dengan `OrderModel` (nomor transaksi, tanggal/waktu, metode pembayaran, rincian item, subtotal, diskon, ongkir, biaya layanan, total bayar, stempel lunas).
    2. **Pure Dart PDF Generator:** Menghasilkan PDF 1.4 valid tanpa library eksternal yang berat.
    3. **Android Storage & FileProvider:**
       - Tersimpan langsung di folder publik `Download/` ponsel (`struk_RSO-xxx.pdf`) via `MediaStore.Downloads`.
       - Menyediakan tombol aksi langsung: **Buka PDF** (langsung membuka di aplikasi pembaca PDF bawaan HP) dan **Bagikan** (ke WhatsApp, Telegram, Email, dll.).

---

### D. Fitur Rating & Pembuatan Ulasan Pengguna (Reviews)
- **Masalah:** Rating menu tidak seragam dan pengguna belum bisa membuat ulasan langsung dari aplikasi mobile.
- **Solusi & Implementasi:**
  - File Baru:
    - `mobile/lib/models/review_model.dart`
    - `mobile/lib/screens/menu/write_review_modal.dart`
  - File Diubah:
    - `mobile/lib/screens/home/home_screen.dart` (standardisasi rating kartu featured menu).
    - `mobile/lib/widgets/food_card.dart` (tampilan bintang dan jumlah ulasan pada card grid & horizontal).
    - `mobile/lib/screens/menu/menu_detail_modal.dart` (daftar ulasan pembeli + tombol *"Tulis Ulasan"*).
    - `mobile/lib/services/api_service.dart` & `mobile/lib/services/mock_data_service.dart`.
  - **Endpoint API Backend:**
    - `GET /api/v1/menu-items/{id}/reviews` (Mengambil ulasan menu).
    - `POST /api/v1/reviews` (Kirim ulasan baru dari mobile/web).

---

### E. Perbaikan Unduh APK Mobile dari Web (Bukan File HTML)
- **Masalah:** Saat tombol unduh aplikasi di website diklik, browser mengunduh halaman web sebagai file `.apk.html` alih-alih file biner Android `.apk`.
- **Solusi & Implementasi:**
  - File: `resources/views/components/app-download-section.blade.php` (menghapus atribut `download="..."` yang merusak MIME type di browser mobile).
  - File: `app/Http/Controllers/AppDownloadController.php` (menambahkan response binary header `application/vnd.android.package-archive` dan redirect CDN statis).
  - File: `vercel.json` (menambahkan rute statis `/downloads/(.*)`).
  - File: `.gitignore` (memastikan `public/downloads/rasa-mandeh.apk` tetap terlacak di git).

---

## 🖥️ 2. Pembaruan Backend & Web (Laravel 12)

### A. POS Kasir & Alur Transaksi
- Memperbaiki parsing data scanner POS pada `PosController::findOrder` sehingga nama menu dan nama cabang tidak kosong atau salah label.
- Perhitungan subtotal item di scanner POS menggunakan `$item->price * $item->quantity`.
- Penyelesaian pesanan (`status = completed`) otomatis mengubah `payment_status = paid`, mencatat `cashier_id`, membuat entitas `Payment`, dan membebaskan status meja menjadi `available`.
- Pembatalan pesanan (`status = cancelled`) otomatis merilis meja dan mengubah pembayaran menjadi `voided`.

### B. Integritas Data & Database
- Menambahkan `SoftDeletes` dan kolom `deleted_at` pada model `MenuItem` agar riwayat transaksi masa lalu tidak rusak jika menu dihapus.
- Menambahkan `withTrashed()` pada relasi `menuItem` di `OrderItem`, `BranchMenuPrice`, dan `PackageItem`.
- Memperbaiki urutan `TableSeeder` pada `DatabaseSeeder.php` setelah cabang selesai dibuat (total 120 meja terisi).
- Penambahan cabang baru di `BranchController::store` otomatis menyalin harga dasar menu dan menginisialisasi 20 meja.
- Menghitung pendapatan bersih di `DashboardController` hanya dari pesanan yang selesai (`completed` / `paid`).
- Migrasi deduplikasi cabang (`2026_09_20_162500_cleanup_duplicate_branches.php`) untuk merapikan data cabang ganda.

### C. Keamanan & Autentikasi
- Integrasi *Two-Factor Authentication* (2FA) Google Authenticator dengan QR Code (Chillerlan QRCode) untuk akun Admin.
- Banner peringatan kuning jika akun admin belum mengaktifkan 2FA.
- Rate limiting pada verifikasi token QR login dan 2FA guna mencegah brute-force.
- Log Aktivitas Sistem (`SystemLog`) terpusat untuk memantau aktivitas admin dan IP address.

### D. Optimasi Gambar & Format WebP
- Implementasi `WebpUploadService` untuk konversi otomatis gambar menu PNG/JPG/WEBP ke `.webp`.
- Endpoint `POST /api/v1/menu-items/upload-image` untuk unggah gambar secara programmatic.
- Perlindungan klik kanan dan drag pada gambar hidangan untuk mencegah pencurian aset.

---

## 🚀 3. Panduan Cepat untuk Developer (Quick Start)

### Menjalankan Backend Laravel:
```bash
# Instalasi & setup awal (jika fresh clone)
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed --force
npm install && npm run build

# Menjalankan server lokal
php artisan serve --host=0.0.0.0 --port=8000
```

### Menjalankan Aplikasi Mobile Flutter:
```bash
cd mobile

# Mengambil dependensi
flutter pub get

# Menjalankan pengujian & linter
flutter analyze
flutter test

# Menjalankan di perangkat/emulator (Hot Reload: 'r', Hot Restart: 'R')
flutter run

# Membangun file APK rilis terbaru
flutter build apk --release
cp build/app/outputs/flutter-apk/app-release.apk ../public/downloads/rasa-mandeh.apk
```

---

## 📁 4. Berkas Utama yang Perlu Diketahui Developer

| Komponen | Lokasi File | Keterangan |
|---|---|---|
| **Pencarian & Katalog Mobile** | `mobile/lib/providers/menu_provider.dart` | Logika pencarian berbasis nama, filter kategori, dan sorting. |
| **Unduh Struk Mobile** | `mobile/lib/services/receipt_service.dart` | Generator PDF murni Dart & MethodChannel Android. |
| **Android Native Bridge** | `mobile/android/app/src/main/kotlin/.../MainActivity.kt` | Handler simpan ke Download, FileProvider buka & share PDF. |
| **Ulasan & Review Mobile** | `mobile/lib/screens/menu/write_review_modal.dart` | Modal rating bintang & form ulasan pengguna. |
| **API Review Backend** | `app/Http/Controllers/Api/ReviewController.php` | Controller API untuk ambil & simpan ulasan. |
| **Download APK Controller** | `app/Http/Controllers/AppDownloadController.php` | Pengendali unduhan biner APK resmi di web. |
| **File APK Rilis** | `public/downloads/rasa-mandeh.apk` | File instalasi Android final yang diunduh pengguna. |
