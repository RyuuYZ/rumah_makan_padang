# 🚀 Quick Start Guide - Raso Mandeh

Panduan instalasi dan persiapan lokal untuk proyek *Raso Mandeh* (Sistem ERP, Kasir POS & Pemesanan).

## 📋 Prerequisites Checklist

Pastikan lingkungan lokal (mesin/PC) Anda sudah terinstal:
- [ ] PHP 8.2 atau yang lebih baru
- [ ] MySQL 8.0 atau yang lebih baru
- [ ] Node.js 18 atau yang lebih baru
- [ ] Composer
- [ ] Git

## 🎯 Panduan Instalasi (5 Menit)

### 1️⃣ Database Setup

Buat database baru melalui terminal/command prompt MySQL, atau gunakan GUI seperti phpMyAdmin/DBeaver.

```bash
# Login to MySQL
mysql -u root -p

# Create database (Contoh nama: rumah_makan_padang)
CREATE DATABASE rumah_makan_padang;
exit
```

### 2️⃣ Pengaturan Proyek & Dependency

Proyek ini menggunakan arsitektur monolitik Laravel. Semua dependensi (PHP dan JS) harus diinstal di direktori akar (root).

```bash
# Buka terminal dan masuk ke direktori proyek
cd rumah_makan_padang

# Install dependensi PHP (Laravel)
composer install

# Install dependensi NPM (Tailwind & Vite)
npm install
```

### 3️⃣ Konfigurasi Environment (.env)

Gandakan file `.env.example` menjadi `.env`, lalu konfigurasi koneksi database Anda.

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```
**Penting:** Buka file `.env` dan pastikan kredensial database sudah benar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rumah_makan_padang
DB_USERNAME=root
DB_PASSWORD=
```

### 4️⃣ Setup Storage & Database Migration

Langkah ini **sangat penting** agar fitur unggah gambar (seperti unggah Foto Profil Admin dan Menu) dapat berjalan.

```bash
# Menghubungkan folder public dengan storage (WAJIB DILAKUKAN)
php artisan storage:link

# Jalankan semua migrasi tabel ke dalam database
php artisan migrate
```
*(Catatan: Jika ada error saat migrasi, pastikan XAMPP/MySQL Anda sudah berjalan dan nama database cocok dengan `.env`).*

### 5️⃣ Menjalankan Aplikasi Lokal

Anda memerlukan **dua terminal** yang berjalan secara bersamaan: satu untuk *server* PHP, dan satu lagi untuk kompiler aset Vite (Frontend CSS/JS).

**Terminal 1 (Backend Server):**
```bash
php artisan serve
```

**Terminal 2 (Frontend Vite Server):**
```bash
npm run dev
```

✅ **Selesai!** Aplikasi sudah dapat diakses melalui browser Anda di URL:
- Halaman Pelanggan (Utama): [http://localhost:8000](http://localhost:8000)
- Panel Admin (Login): [http://localhost:8000/admin/login](http://localhost:8000/admin/login)

---

## 🛠️ Panduan Tambahan / Troubleshooting

### Perubahan File CSS (Tailwind)
Jika Anda mengubah file `.blade.php` atau menambah *class* Tailwind, Anda **wajib** menyalakan `npm run dev`. Namun jika ingin mem-build aset untuk produksi tanpa menyalakan server Vite terus-menerus, jalankan:
```bash
npm run build
```

### Aplikasi Tidak Bisa Menampilkan Foto Profil
Pastikan Anda sudah menjalankan perintah `php artisan storage:link`. Jika sebelumnya Anda memakai Windows dan *symlink* gagal (atau shortcut `storage` di folder `public` *corrupt*), silakan hapus shortcut `public/storage` lama dan jalankan `php artisan storage:link` kembali lewat Command Prompt mode Administrator.

### Reset Database
Jika database berantakan atau Anda ingin mengembalikannya ke skema awal yang bersih:
```bash
php artisan migrate:fresh
```

## 📝 Next Steps
- 🔑 **Uji Login Admin:** Pastikan login, verifikasi 2FA, dan fitur *ID Card* berjalan lancar.
- 🎨 **Cek UI:** Pastikan tidak ada desain yang pecah (*build* Vite jika iya).
- 🐛 **Bantuan:** Periksa log di folder `storage/logs/laravel.log` jika menemui pesan *500 Server Error*.
