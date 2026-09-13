# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- Komponen paginasi kustom (`x-admin-pagination`) dengan lencana `TOTAL: X [ENTITY]`, tombol `< Prev` dan `Next >`, serta kotak lompat halaman interaktif `KE HAL: [input] / Y [Go]` yang mempertahankan parameter filter URL.
- Alpine.js helper `adminTablePagination` pada layout admin untuk navigasi halaman instan.
- Pembaruan status pesanan berbasis *asynchronous* (AJAX / Fetch API) di panel Admin tanpa *page refresh* dan tanpa *scroll jump* (posisi *scroll* tetap stabil).
- Sistem *Global Floating Toast Notification* animasi berbasis Alpine.js pada layout Admin (`adminGlobalToast`) dengan helper `window.showToast(message, type)` dan event listener `@notify.window`.
- Feature test `AdminOrderStatusTest` untuk memvalidasi endpoint pembaruan status pesanan via JSON, validasi input, autentikasi, dan fallback form.
- Integrasi *Two-Factor Authentication* (2FA) menggunakan *Google Authenticator* (Chillerlan QRCode) yang ditautkan ke akun masing-masing admin (mendukung banyak *device* untuk akun yang sama).
- Tampilan notifikasi *banner* kuning peringatan 2FA (otomatis muncul di semua halaman admin jika 2FA belum aktif).
- Fitur *System Logs* (Log Sistem) terpusat untuk melacak semua aktivitas Admin (Login, aksi, dll) termasuk IP Address, menggunakan model `SystemLog`.
- Implementasi fungsionalitas unggah foto profil kustom dengan pemotongan *1:1 aspect ratio* presisi menggunakan `Cropper.js` (dengan CDN ringan).
- Pembaruan mekanisme rute (`routes/web.php`) untuk mengakomodasi fitur pengunggahan foto profil (`admin.profile.photo`) dan pembacaan *Activity Logs* (`admin.logs.index`).
- Penambahan properti tabel database `profile_photo_path` pada entitas `User` beserta *accessor* *photo URL*-nya.
- Penambahan tautan ke *Buku Panduan* dan *Log Sistem* di bagian *Sidebar* Menu Dasbor Admin.

### Changed
- Penyesuaian layout seluruh tabel data Admin (`orders`, `menu`, `reviews`, `logs`, dan `dashboard`) menjadi `table-fixed` non-scrollable (tanpa scrollbar horizontal yang mengganggu estetika) dengan proporsi kolom yang presisi dan rapi.
- Mengganti seluruh kontrol paginasi standar di panel admin dengan komponen paginasi modern bertema Raso Mandeh (`#7A1F2B` dan `#C9A227`).
- Mengubah form pembaruan status pesanan di halaman Pesanan Masuk (`admin/orders/index`), Dashboard Admin (`admin/dashboard`), dan POS Scanner (`admin/pos/index`) menggunakan Alpine.js component (`orderStatusRow`, `dashboardOrderRow`, `completeOrder`) dengan perubahan warna badge/select status secara instan dan mini spinner loading.
- Sinkronisasi lencana (*badge*) jumlah pesanan *pending* pada *sidebar* Dasbor Admin secara *real-time* via event `@order-status-updated`.
- Method `updateStatus` pada `OrderController` mengembalikan response JSON terstruktur (`success`, `message`, `order`, dan `pending_count`) untuk request AJAX/JSON.
- Perombakan total antarmuka UI *ID Card* profil menjadi lebih premium bergaya kopdes, dengan foto asli pengguna, desain kartu 100% responsif, serta pengaturan ulang tombol navigasi.
- Mengubah mekanisme cetak *ID Card* (menggunakan `html2canvas`); menyembunyikan QR code dari layar namun secara dinamis menempatkannya langsung di dalam hasil file `.png` (unduhan akhir kartu ID).
- Menghapus badge/tulisan indikator "Sistem Kasir Aktif" dari struktur atas *header* navigasi Admin.
- Mengecilkan *font-size* dan rasio empuk (*padding*) pada menu-menu navigasi *sidebar*, serta logo utama, guna mencegah terjadinya *vertical scrolling* pada layar berukuran standar.
- Menghapus garis aksen (emas/kuning) pada komponen avatar di pojok *navbar*, mengembalikan kesan *clean UI*.
- Migrasi `add_role_and_phone_to_users_table`: tambah kolom `role` (admin/cashier/customer/warehouse_staff/kitchen), `phone`, dan `is_active` ke tabel `users` — menyatukan aktor ERD (ADMINS, CASHIERS) ke satu tabel sesuai arsitektur Laravel RBAC.
- Migrasi `add_order_fields_to_orders_table`: tambah kolom `order_number` (nomor unik per transaksi), `order_type` (dine_in/takeaway), `table_number`, `qr_code_token` (untuk QR Order pelanggan), `source` (pos/customer_web/qr_scan), `payment_status`, dan `cashier_id` FK ke tabel `orders`.
- Migrasi `create_payments_table`: tabel baru `payments` dengan dukungan metode `cash` dan `qris`, kolom `cash_given`/`change_amount` untuk kembalian tunai, `reference_number` untuk kode QRIS, dan `status` pembayaran.
- Migrasi `create_packages_table`: tabel `packages` dan `package_items` untuk fitur Paket Menu sesuai BRD (MENU-05) dan ERD.
- Migrasi `create_shifts_table`: tabel `shifts` untuk manajemen shift kasir sesuai BRD (POS-12), termasuk saldo awal dan penutupan.
- Model `Payment` dengan relasi ke `Order` dan `User` (cashier) serta helper methods `isCompleted()` dan `isCash()`.
- Model `Package` dengan relasi `belongsToMany` ke `MenuItem` melalui `package_items`.
- Model `PackageItem` sebagai pivot model antara `Package` dan `MenuItem`.
- Model `Shift` dengan relasi ke `User` (cashier) dan `Branch`, scope `open()`.
- Menghapus fitur checkout WhatsApp dari `cart-drawer` dan mengubahnya menjadi pemanggilan API `POST /api/v1/orders`.
- Membuat halaman status pesanan (`order-status.blade.php`) beserta route GET `/pesanan/{token}` yang menampilkan QR Code untuk discan oleh kasir (Sesuai dengan BRD CUS-07 & CUS-08).
- Method `orderStatus` di `ApiOrderController` untuk merender halaman order status.
- Setup awal Laravel Boost dan panduan AI Agent (`.agents/`, `boost.json`, `AGENTS.md`).
- Instalasi dependensi npm dan build Vite.
- Pengaturan environment database awal.
- Kategori etalase menu: Semua Hidangan, Ayam, Ikan, Daging Sapi, Toping & Bumbu, Minuman Tradisional.
- Perlindungan aset gambar dari klik kanan dan drag (anti-pencurian aset).
- Gambar logo kustom (`Logo_Final.png`) menggantikan logo teks lama di Header, Footer, dan Dashboard Admin.
- Layanan konversi & upload gambar otomatis format WebP (`WebpUploadService`).
- Form upload file gambar menu di dashboard admin (`admin/menu`) yang mengonversi file PNG/JPG/WEBP ke `.webp`.
- Endpoint API `POST /api/v1/menu-items/upload-image` untuk upload gambar hidangan WebP secara programmatic.
- Metadata `foto_format` dan URL lengkap gambar pada respon JSON API menu.
- Suite pengujian fitur WebP (`WebpUploadTest`).

### Changed
- Nama merek/restoran secara menyeluruh (teks, kode, seeder database, dan nama file gambar) diubah dari "Raso Minang" menjadi "Raso Mandeh".
- Logika filter etalase menu agar item dengan kategori 'Toping & Bumbu' tidak muncul pada tab 'Semua Hidangan'.
- Menghapus gambar besar signature dish dan memindahkan aset `main-foto.webp` ke bagian hero.
- Mengganti dan mengubah nama gambar bagian Cerita Kami menjadi `dapur-raso-mandeh.webp`.
- Update penyesuaian harga seluruh hidangan secara akurat berdasarkan permintaan.
- Penyesuaian besar ukuran logo kustom di semua layout (Header: `h-20`/`h-24`, Footer: `h-20`, Admin: `h-12`).
