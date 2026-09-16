# 🍛 Rasa Mandeh — Aplikasi Pemesanan Makanan Padang Otentik

Aplikasi mobile Flutter **"Rasa Mandeh"** menyajikan pengalaman pemesanan masakan Minangkabau modern, elegan, dan *fully functional* end-to-end. Dibangun dengan palet khas Minang (krem gading, marun tua, emas), state management terpusat, navigasi deklaratif `go_router`, dan persistensi lokal `shared_preferences`.

---

## 📱 Fitur Utama & 10 Halaman Lengkap

1. **Splash Screen (`/splash`)**:
   - Monogram elegan **"RM"** dengan animasi scale & fade.
   - Pengecekan otomatis sesi login di local storage.
   - Auto-navigate ke Home (jika sudah login) atau Login (jika belum).
2. **Register Screen (`/register`)**:
   - Form registrasi: Nama lengkap, Email/No. HP, Password (min 8 karakter), Konfirmasi password.
   - Validasi input komprehensif & auto-login saat pendaftaran sukses.
3. **Login Screen (`/login`)**:
   - Form kredensial dengan toggle sembunyikan/tampilkan kata sandi.
   - Tombol **"Masuk Instan (Akun Demo)"** 1-tap untuk kemudahan evaluasi.
   - Integrasi sesi login otomatis ke `shared_preferences`.
4. **Home Screen (`/`)**:
   - Sapaan personal nama user & pemilih lokasi pengiriman interaktif (bottom sheet).
   - Search bar real-time.
   - Carousel banner promo masakan Padang (kode diskon: `MANDEMURAH`).
   - Scroll kategori horizontal (Semua, Lauk Utama, Ayam & Bebek, Gulai & Kuah, Ikan & Seafood, Sayur, Sambal, Minuman, Paket).
   - Grid "Menu Paling Dicari" dengan rating badge emas, harga Rupiah, dan tombol cepat `+` tambah ke keranjang.
   - Bottom Navigation Bar dengan indikator badge porsi aktif di keranjang.
5. **Menu Screen (`/menu`)**:
   - Katalog masakan Padang lengkap (18+ hidangan otentik).
   - Filter kategori, pencarian kata kunci, pengurutan (Terpopuler, Rating Tertinggi, Harga Terendah/Tertinggi).
   - Toggle tampilan Grid vs List horizontal.
   - Tap kartu membuka modal detail porsi lengkap.
6. **Menu Detail Modal (Bottom Sheet)**:
   - Foto hidangan resolusi tinggi, deskripsi kaya bumbu, info gramasi porsi, level kepedasan cabai.
   - Input catatan khusus (contoh: *"kuah gulai banyakin, sambal dipisah ya uni"*).
   - Stepper jumlah porsi (+ / -) dan tombol tambah ke keranjang dengan kalkulasi harga dinamis.
7. **Cart Screen (`/cart`)**:
   - Daftar item belanjaan dengan foto, harga, catatan khusus, stepper kuantitas, dan hapus item.
   - Fitur kupon promo: Masukkan `MANDEMURAH` untuk mendapatkan diskon langsung Rp 15.000.
   - Kalkulasi otomatis: Subtotal, Ongkos Kirim (Gratis jika belanja ≥ Rp 75.000), Biaya Layanan, Total Akhir.
   - **Persistensi State**: Data keranjang tetap tersimpan aman di `shared_preferences` saat aplikasi ditutup/dibuka kembali.
   - Empty state visual saat keranjang kosong.
8. **Payment Screen (`/payment`)**:
   - Toggle metode pengiriman: **Antar ke Alamat (Delivery)** vs **Ambil di Resto (Pickup)**.
   - Formulir alamat tujuan pengiriman.
   - Metode pembayaran interaktif:
     - **QRIS / E-Wallet** (GoPay, OVO, ShopeePay, DANA)
     - **Transfer Bank Virtual Account** (BCA, Mandiri, BRI, BNI)
     - **Bayar di Tempat (COD)**
   - Ringkasan rincian biaya transparan sebelum checkout.
9. **Order Loading & Tracking Screen (`/order-loading/:orderId`)**:
   - Pelacakan tahapan pemesanan real-time:
     1. *Pesanan Dikonfirmasi*
     2. *Dapur Sedang Memasak*
     3. *Sedang Diantar Kurir*
     4. *Pesanan Sampai*
   - Kartu identitas kurir (nama, armada, kontak telepon).
   - Tombol simulasi tahap berikutnya untuk menguji seluruh alur dengan cepat.
10. **Order History & Detail Screen (`/orders` & `/orders/:orderId`)**:
    - Tab pesanan aktif vs riwayat pesanan selesai.
    - Status badge tematik (hijau selesai, kuning proses, merah batal).
    - Rincian pesanan lengkap dengan daftar item dan rincian transaksi.
    - Tombol **"Pesan Lagi"** yang memasukkan hidangan lama kembali ke keranjang.
11. **Profile Screen (`/profile`)**:
    - Info akun user, avatar, tier **Minang Gold Member**.
    - Menu cepat: Riwayat Pesanan, Alamat Tersimpan, Voucher Saya, Bantuan WhatsApp, Tentang Rasa Mandeh.
    - Tombol Keluar (Logout) dengan konfirmasi dialog yang membersihkan sesi dari `shared_preferences`.

---

## 🎨 Identitas Visual & Desain

- **Warna Latar**: Krem & Putih Gading (`#FAF7F0`, `#FFFFFF`, `#F4EFE6`)
- **Aksen Utama**: Marun Tua Minang (`#70121A`, `#4A0B11`)
- **Aksen Sekunder**: Emas Minangkabau (`#D4AF37`, `#F7E6AA`)
- **Tipografi**:
  - Judul / Brand: Serif Elegan (`Playfair Display`)
  - Konten / Body: Sans-Serif Modern & Bersih (`Plus Jakarta Sans`)

---

## 📂 Struktur Direktori Proyek

```
lib/
├── main.dart                      # Inisialisasi app, MultiProvider, MaterialApp.router
├── models/                        # Model data dengan serialisasi JSON
│   ├── cart_item_model.dart
│   ├── menu_item_model.dart
│   ├── order_model.dart
│   └── user_model.dart
├── providers/                     # State management terpusat (ChangeNotifier)
│   ├── auth_provider.dart
│   ├── cart_provider.dart
│   ├── menu_provider.dart
│   └── order_provider.dart
├── routes/                        # Konfigurasi deklaratif go_router
│   └── app_router.dart
├── screens/                       # 10+ Layar & Modal aplikasi
│   ├── auth/
│   │   ├── login_screen.dart
│   │   └── register_screen.dart
│   ├── cart/
│   │   └── cart_screen.dart
│   ├── checkout/
│   │   ├── order_loading_screen.dart
│   │   └── payment_screen.dart
│   ├── home/
│   │   └── home_screen.dart
│   ├── main_scaffold.dart         # ShellRoute bottom navigation & badge counter
│   ├── menu/
│   │   ├── menu_detail_modal.dart
│   │   └── menu_screen.dart
│   ├── order/
│   │   ├── order_detail_screen.dart
│   │   └── order_history_screen.dart
│   ├── profile/
│   │   └── profile_screen.dart
│   └── splash_screen.dart
├── services/                      # Repositori data & layer API/Storage
│   ├── auth_service.dart
│   ├── mock_data_service.dart
│   └── storage_service.dart
├── theme/                         # Sistem token warna & tipografi terpusat
│   ├── app_colors.dart
│   ├── app_theme.dart
│   └── app_typography.dart
├── utils/                         # Formatter & konstanta
│   ├── constants.dart
│   └── currency_formatter.dart
└── widgets/                       # Komponen UI modular & reusable
    ├── category_chip.dart
    ├── custom_text_field.dart
    ├── food_card.dart
    ├── primary_button.dart
    ├── promo_banner.dart
    ├── quantity_stepper.dart
    ├── rm_logo.dart
    └── status_badge.dart
```

---

## 🚀 Cara Menjalankan Aplikasi

1. **Unduh seluruh dependensi**:
   ```bash
   flutter pub get
   ```

2. **Jalankan analisis kode** (memastikan 0 errors & 0 warnings):
   ```bash
   flutter analyze
   ```

3. **Jalankan pengujian unit & widget**:
   ```bash
   flutter test
   ```

4. **Jalankan aplikasi di perangkat / emulator**:
   ```bash
   flutter run
   ```

---

## 🔑 Akun Demo Pengujian

Untuk pengujian cepat tanpa repot mengetik:
- Klik tombol **"Masuk Instan (Akun Demo)"** pada halaman Login.
- Atau login manual:
  - **Email**: `budi@rasamandeh.com`
  - **Password**: `password123`
- **Kode Voucher Promo**: `MANDEMURAH` (Diskon Rp 15.000)
