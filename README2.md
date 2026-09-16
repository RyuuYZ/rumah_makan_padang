# 🍛 Rumah Makan Padang "Raso Mandeh"

Aplikasi web full-stack monolitik **Laravel 11** untuk Rumah Makan Padang "Raso Mandeh", menggunakan template engine **Blade**, **Tailwind CSS**, dan **Alpine.js**.

Semua kode backend, database, dan frontend terintegrasi dalam satu direktori project utama tanpa pemisahan folder `backend` dan `frontend`.

---

## 🎨 Filosofi Desain Khas Minangkabau

- **Warna Identitas Songket**:
  - `Songket Red`: `#7A1F2B` (Aksen utama)
  - `Songket Gold`: `#C9A227` (Aksen kemewahan & rating)
  - `Songket Dark`: `#3D0F15` (Hover & kontras)
  - `Warm Cream`: `#F5EFE2` (Latar belakang hangat)
  - `Tinta Coklat`: `#241B16` (Tipografi teks)
- **Tipografi**:
  - Headline: *Playfair Display* (Serif tradisional-elegan)
  - Body: *Inter* (Sans-serif modern dan nyaman dibaca)
- **Elemen Arsitektur**:
  - *Gonjong Curved Divider*: Garis pembatas dekoratif terinspirasi bentuk atap Rumah Gadang.

---

## 🚀 Fitur Utama

1. **Hero Section Interaktif**: Narasi warisan 1950, statistik rating 4.9, porsi harian 50+, dan visual hidangan khas.
2. **Pemilih Cabang (Branch Selector)**: Pilihan 6 cabang kota (Jakarta Selatan, Bandung, Surabaya, Medan, Palembang, Bukittinggi) dengan jam operasional dan kontak pemesanan lokal.
3. **Menu Section Dinamis**:
   - Filter instan berdasarkan kategori (*Lauk Daging, Lauk Ayam, Lauk Ikan, Sayur & Sambal, Minuman Tradisional*).
   - Pencarian hidangan secara langsung (*Live Search*).
   - Tag kelezatan (*Signature, Favorit, Baru*).
4. **Keranjang Pesanan (Slide-over Cart Drawer)**:
   - Tambah/kurang porsi hidangan.
   - Perhitungan total tagihan real-time berformat Rupiah.
   - **Checkout Langsung ke WhatsApp**: Pesanan diformat otomatis dan diarahkan ke nomor WhatsApp cabang yang dipilih.
5. **Cerita Kami (About Heritage)**: Narasi sejarah kedai Bukittinggi sejak 1950, teknik memasak arang kayu tradisional, dan rempah alami Ranah Minang.
6. **Ulasan Pelanggan (Testimonial)**: Testimoni pelanggan asli dengan bintang rating 5 emas.
7. **RESTful API**: Rute `/api/v1/branches`, `/api/v1/menu-items`, dan `/api/v1/orders` tetap tersedia.

---

## 🛠️ Cara Menjalankan Project

### 1. Prasyarat
- PHP 8.2+
- Composer
- Node.js & npm

### 2. Migrasi & Seed Database
```bash
# Jalankan migrasi dan isi database dengan hidangan Minang & cabang
php artisan migrate:fresh --seed
```

### 3. Kompilasi Aset Frontend (Tailwind CSS & JS)
```bash
# Untuk mode development (live watch)
npm run dev

# Atau untuk production bundle
npm run build
```

### 4. Menjalankan Server Laravel
```bash
php artisan serve
```
Aplikasi dapat diakses melalui browser di: **http://localhost:8000**
