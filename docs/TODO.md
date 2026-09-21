# Checklist & Roadmap Pengujian E2E (Playwright)

## 1. Setup & Infrastruktur Pengujian
- [x] Instalasi `@playwright/test` dan browser binary Chromium
- [x] Konfigurasi `playwright.config.ts` (port 8000, webServer, resilient timeouts)
- [x] Penambahan script pengujian ke `package.json` (`test:e2e`, `test:e2e:ui`, `test:e2e:headed`)
- [x] Pembuatan Auth Fixture (`e2e/fixtures/auth.setup.ts`) untuk sesi Admin
- [x] Penambahan integrasi CI/CD `.github/workflows/playwright.yml`

## 2. Implementasi Test Suite Berdasarkan Logika Bisnis
- [x] **CUJ-01 (Customer Order & Checkout)**: `e2e/customer-order.spec.ts`
  - [x] Pemilihan cabang & filter kategori menu
  - [x] Tambah menu ke keranjang (Alpine.js state) & modifikasi kuantitas (+ / -)
  - [x] Validasi form (nama pelanggan, nomor meja, radio dine-in / takeaway)
  - [x] Pembuatan pesanan & verifikasi navigasi ke halaman status QR (`/pesanan/{token}`)
  - [x] Validasi panduan informatif saat keranjang belanja kosong (empty state)
- [x] **CUJ-02 (Customer Tracking & Reservasi)**: `e2e/customer-features.spec.ts`
  - [x] Pelacakan status pesanan di `/cek-pesanan` (penanganan kode salah vs valid)
  - [x] Form reservasi meja (validasi batas waktu dan pengajuan booking sukses)
- [x] **CUJ-03 (Admin Auth & Security)**: `e2e/admin-auth.spec.ts`
  - [x] Proteksi middleware rute tertutup `/admin/*` untuk tamu (Guest redirect to login)
  - [x] Penanganan kesalahan saat kredensial salah (alert pesan error tampil)
  - [x] Login sukses dengan kredensial valid dan alur logout bersih
- [x] **CUJ-04 (Admin Orders & POS Kasir)**: `e2e/admin-order-pos.spec.ts`
  - [x] Filter status pesanan & transisi state: `pending` -> `process`
  - [x] Integrasi Kasir POS: pencarian kode pesanan manual & penyelesaian transaksi lunas (Bayar Tunai)
  - [x] Verifikasi aturan bisnis: status bayar menjadi `paid`, order `completed`, struk siap dicetak
- [x] **CUJ-05 (Admin Master Data)**: `e2e/admin-master-data.spec.ts`
  - [x] Kelola menu: pencarian kata kunci, filter kategori, verifikasi data tabel
  - [x] Kelola cabang: menampilkan seluruh cabang aktif beserta jam operasional dan kontak WhatsApp

## 3. Eksekusi & Validasi
- [x] Menjalankan test suite E2E secara otomatis (`npm run test:e2e`)
- [x] Menemukan & memperbaiki 3 bug nyata (LazyLoading pada Dashboard Admin, missing branch pricing, dan table availability collision)
- [x] Menjalankan Laravel Pint formatter (`vendor/bin/pint --format agent`)
- [x] Verifikasi 100% test lulus (12/12 passed)
