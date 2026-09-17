@extends('layouts.admin')

@section('title', 'Buku Panduan Penggunaan - Raso Mandeh')

@section('header')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
    <div>
        <h1 class="text-2xl font-black text-neutral-900 font-serif">Buku Panduan</h1>
        <p class="text-sm text-neutral-500 mt-1">Panduan lengkap operasional sistem manajemen Raso Mandeh.</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-sm border border-neutral-200 overflow-hidden">
        
        <!-- Hero Banner Guide -->
        <div class="bg-gradient-to-r from-[#7A1F2B] to-[#9A2A38] p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -translate-y-1/2 translate-x-1/3"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-[#C9A227] opacity-20 rounded-full translate-y-1/2 -translate-x-1/4 blur-2xl"></div>
            
            <div class="relative z-10">
                <svg class="w-12 h-12 mb-4 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <h2 class="text-3xl font-black font-serif mb-2">Selamat Datang di Sistem Raso Mandeh!</h2>
                <p class="text-white/80 max-w-xl text-sm leading-relaxed">
                    Sistem ini dirancang untuk memudahkan Anda mengelola seluruh cabang restoran Padang Raso Mandeh secara terpusat, mulai dari pesanan, meja, hingga inventaris dapur.
                </p>
            </div>
        </div>

        <!-- Contents -->
        <div class="p-8">
            <div class="prose prose-sm sm:prose-base max-w-none prose-headings:font-serif prose-headings:text-[#7A1F2B] prose-a:text-[#C9A227] prose-a:no-underline hover:prose-a:underline">
                
                <h3 class="flex items-center space-x-2 border-b border-neutral-100 pb-2">
                    <span class="bg-[#F5EFE2] text-[#7A1F2B] w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm">1</span>
                    <span>Terminal Kasir (POS)</span>
                </h3>
                <p>
                    Terminal Kasir adalah antarmuka utama yang digunakan oleh staf kasir di setiap cabang restoran.
                </p>
                <ul>
                    <li><strong>Pesanan Walk-in:</strong> Di menu sebelah kanan, isi nama pelanggan, pilih tipe layanan (Dine-in / Takeaway), dan klik makanan yang dipesan.</li>
                    <li><strong>Pemilihan Meja (Interaktif):</strong> Saat memilih <em>Dine-in</em>, Anda wajib menekan tombol <strong>Pilih Meja</strong>. Sebuah denah meja cabang akan muncul:
                        <ul>
                            <li><span class="inline-block w-3 h-3 bg-[#F5EFE2] border border-[#C9A227]/40 rounded-sm mr-1"></span> <strong>Krem:</strong> Meja Kosong (Bisa dipilih)</li>
                            <li><span class="inline-block w-3 h-3 bg-amber-100 border border-amber-300 rounded-sm mr-1"></span> <strong>Oranye:</strong> Meja telah di-booking tamu (Tidak bisa dipilih)</li>
                            <li><span class="inline-block w-3 h-3 bg-rose-100 border border-rose-300 rounded-sm mr-1"></span> <strong>Merah:</strong> Meja sedang terisi / dipakai makan (Tidak bisa dipilih)</li>
                        </ul>
                    </li>
                    <li><strong>Scanner QR:</strong> Kasir juga dapat memindai (Scan) <em>QR Code</em> dari handphone pelanggan yang memesan secara mandiri melalui meja mereka.</li>
                </ul>

                <h3 class="flex items-center space-x-2 border-b border-neutral-100 pb-2 mt-10">
                    <span class="bg-[#F5EFE2] text-[#7A1F2B] w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm">2</span>
                    <span>Manajemen Kapasitas Meja</span>
                </h3>
                <p>
                    Sebagai Admin, Anda memegang kendali penuh atas tata letak meja di seluruh cabang. Buka menu <strong>Sistem > Kapasitas Meja</strong> untuk mengelola meja.
                </p>
                <ul>
                    <li>Setiap cabang telah otomatis dibuatkan 20 meja sebagai awalan.</li>
                    <li>Anda dapat mengedit nomor meja, kapasitas kursi, dan mengatur status manual jika diperlukan (misal: tamu selesai makan, admin merubah status meja kembali menjadi Tersedia).</li>
                    <li>Jika ada meja yang sedang direnovasi atau rusak, Anda dapat mematikan (Disable) meja tersebut agar tidak muncul di pilihan Kasir.</li>
                </ul>

                <h3 class="flex items-center space-x-2 border-b border-neutral-100 pb-2 mt-10">
                    <span class="bg-[#F5EFE2] text-[#7A1F2B] w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm">3</span>
                    <span>Reservasi Online Publik</span>
                </h3>
                <p>
                    Pelanggan dapat memesan meja (Booking/Reservasi) langsung dari Landing Page. Data pemesanan tersebut akan masuk ke dalam sistem dan meja yang di-booking otomatis bisa ditandai. (Fitur notifikasi WhatsApp akan berjalan di sistem latar belakang apabila sudah dikonfigurasi).
                </p>

                <h3 class="flex items-center space-x-2 border-b border-neutral-100 pb-2 mt-10">
                    <span class="bg-[#F5EFE2] text-[#7A1F2B] w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm">4</span>
                    <span>Sistem Keamanan 2FA (Two-Factor Authentication)</span>
                </h3>
                <p>
                    Sistem dilengkapi dengan keamanan setara perbankan. Seluruh Admin Kasir diwajibkan (atau disarankan) mengaktifkan <strong>Google Authenticator</strong>.
                </p>
                <ul>
                    <li>Buka halaman Profil Akun di pojok kanan atas.</li>
                    <li>Klik tab <strong>Keamanan Akun</strong> dan klik <strong>Aktifkan 2FA</strong>.</li>
                    <li>Pindai QR Code yang muncul menggunakan aplikasi Google Authenticator, Authy, atau sejenisnya dari handphone Anda.</li>
                    <li>Masukkan 6-digit PIN untuk konfirmasi.</li>
                </ul>

                <div class="mt-12 p-5 bg-neutral-50 rounded-2xl border border-neutral-200 text-center">
                    <p class="text-sm font-medium text-neutral-500 mb-0">
                        Butuh bantuan teknis lebih lanjut? Hubungi Tim IT / Developer Raso Mandeh di email <a href="mailto:it@rasomandeh.co.id" class="text-[#7A1F2B] font-bold">it@rasomandeh.co.id</a>.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
