{{-- 
    Komponen: App Download Section (Integrasi Mobile Flutter Rasa Mandeh)
    Disesuaikan 100% dengan Estetika Raso Mandeh:
    - Wadah kartu putih megah (rounded-3xl, shadow-xl) yang menyatu sempurna di atas latar #F5EFE2
    - Mockup Smartphone menampilkan persis Halaman Menu dari aplikasi Flutter (RM Logo, Pilih Laukmu, 47 Pilihan Menu, Grid Makanan Autentik & Gonjong Nav)
--}}

<section id="download-app" 
         x-data="{ 
             showGuideModal: false, 
             showQrModal: false,
             copied: false,
             copyUrl(url) {
                 navigator.clipboard.writeText(url).then(() => {
                     this.copied = true;
                     setTimeout(() => { this.copied = false; }, 2500);
                 });
             }
         }" 
         class="py-16 md:py-20 bg-[#F5EFE2] relative overflow-hidden">

    {{-- Decorative Batik Pattern --}}
    <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] mix-blend-multiply pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- KARTU UTAMA UNIFIED (Senada dengan section Reservasi Meja di atasnya) --}}
        <div class="bg-white rounded-3xl shadow-xl border border-[#C9A227]/25 p-6 sm:p-10 lg:p-12 overflow-hidden relative">
            
            {{-- Aksen Dekorasi Gonjong Emas di Sudut Kartu --}}
            <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-bl from-[#C9A227]/15 via-[#7A1F2B]/5 to-transparent rounded-bl-full pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-gradient-to-tr from-[#7A1F2B]/10 to-transparent rounded-full pointer-events-none"></div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">

                {{-- KOLOM KIRI: Teks Penjelasan, 4 Fitur Utama, Tombol Aksi, & Langkah Pasang --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Kicker Badge --}}
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-[#7A1F2B]/10 border border-[#7A1F2B]/20 text-[#7A1F2B] text-xs font-bold tracking-wide shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-[#C9A227] animate-pulse"></span>
                        <span>APLIKASI RESMI RASA MANDEH • v1.0.0</span>
                    </div>

                    {{-- Headline Utama --}}
                    <div class="space-y-2.5">
                        <h2 class="text-3xl sm:text-4xl lg:text-[40px] font-extrabold text-[#241B16] leading-[1.2] tracking-tight font-serif">
                            Instal Aplikasi di HP Android Anda,
                            <span class="relative inline-block text-[#7A1F2B]">
                                <span class="relative z-10">Pesan Masakan Minang</span>
                                <span class="absolute bottom-1 left-0 w-full h-3 bg-[#C9A227]/30 -rotate-1 rounded z-0"></span>
                            </span>
                            Lebih Praktis.
                        </h2>
                        <p class="text-[#241B16]/75 text-sm sm:text-base leading-relaxed max-w-2xl">
                            Unduh langsung file APK resmi Rasa Mandeh ke smartphone Android Anda. Nikmati kemudahan memesan rendang legendaris, melihat 47 pilihan menu autentik lengkap dengan foto, reservasi meja, dan dapatkan promo eksklusif langsung dari genggaman.
                        </p>
                    </div>

                    {{-- 4 KARTU FITUR (2x2 GRID) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        {{-- Kartu Fitur 1 --}}
                        <div class="bg-[#FDFBF7] rounded-2xl p-4 border border-[#C9A227]/20 shadow-xs hover:border-[#7A1F2B]/40 hover:shadow-sm transition-all duration-200 group">
                            <div class="w-9 h-9 rounded-xl bg-[#7A1F2B]/10 border border-[#7A1F2B]/20 flex items-center justify-center text-[#7A1F2B] mb-2.5 group-hover:scale-105 transition-transform">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-[#241B16] mb-1">Pesan Cepat & QR Meja Akurat</h3>
                            <p class="text-xs text-[#241B16]/70 leading-relaxed">
                                Pilih lauk favorit atau pesan langsung di meja resto dengan scan QR tanpa perlu antre lama.
                            </p>
                        </div>

                        {{-- Kartu Fitur 2 --}}
                        <div class="bg-[#FDFBF7] rounded-2xl p-4 border border-[#C9A227]/20 shadow-xs hover:border-[#C9A227]/60 hover:shadow-sm transition-all duration-200 group">
                            <div class="w-9 h-9 rounded-xl bg-[#C9A227]/15 border border-[#C9A227]/30 flex items-center justify-center text-[#9E7D17] mb-2.5 group-hover:scale-105 transition-transform">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-[#241B16] mb-1">Live Tracking Pesanan Real-Time</h3>
                            <p class="text-xs text-[#241B16]/70 leading-relaxed">
                                Pantau pesanan dari tahap racikan dapur, penggorengan panas, hingga dihidangkan di meja.
                            </p>
                        </div>

                        {{-- Kartu Fitur 3 --}}
                        <div class="bg-[#FDFBF7] rounded-2xl p-4 border border-[#C9A227]/20 shadow-xs hover:border-[#7A1F2B]/40 hover:shadow-sm transition-all duration-200 group">
                            <div class="w-9 h-9 rounded-xl bg-[#7A1F2B]/10 border border-[#7A1F2B]/20 flex items-center justify-center text-[#7A1F2B] mb-2.5 group-hover:scale-105 transition-transform">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-[#241B16] mb-1">Poin Pelanggan & Push Alert</h3>
                            <p class="text-xs text-[#241B16]/70 leading-relaxed">
                                Kumpulkan poin loyalitas untuk diskon spesial dan notifikasi langsung saat promo aktif.
                            </p>
                        </div>

                        {{-- Kartu Fitur 4 --}}
                        <div class="bg-[#FDFBF7] rounded-2xl p-4 border border-[#C9A227]/20 shadow-xs hover:border-[#C9A227]/60 hover:shadow-sm transition-all duration-200 group">
                            <div class="w-9 h-9 rounded-xl bg-[#C9A227]/15 border border-[#C9A227]/30 flex items-center justify-center text-[#9E7D17] mb-2.5 group-hover:scale-105 transition-transform">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-[#241B16] mb-1">Resep Autentik Minang 1950</h3>
                            <p class="text-xs text-[#241B16]/70 leading-relaxed">
                                47 pilihan menu warisan Payakumbuh dimasak 8 jam dengan kelapa sangrai murni pilihan.
                            </p>
                        </div>
                    </div>

                    {{-- BAR AKSI TOMBOL (HERO DOWNLOAD BUTTON + 2 TOMBOL PENDUKUNG) --}}
                    <div class="space-y-3 pt-1">
                        {{-- Tombol Utama: Download APK Android (Hero CTA) --}}
                        <a href="{{ $downloadUrl ?? (Route::has('app.download.apk') ? route('app.download.apk') : url('/download/apk')) }}" 
                           class="w-full bg-[#7A1F2B] hover:bg-[#5A1620] text-white rounded-2xl p-4 sm:px-6 sm:py-4 shadow-md shadow-[#7A1F2B]/20 hover:shadow-xl hover:shadow-[#7A1F2B]/35 transition-all duration-200 transform hover:-translate-y-0.5 flex items-center justify-between group border-2 border-[#C9A227]/40">
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-white/15 backdrop-blur-xs flex items-center justify-center text-[#C9A227] group-hover:scale-110 transition-transform shrink-0 ring-1 ring-white/20">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <span class="block text-[10px] sm:text-[11px] font-extrabold tracking-widest text-[#C9A227] uppercase">INSTALASI LANGSUNG ANDROID</span>
                                    <span class="block text-base sm:text-lg font-black text-white leading-tight">Download APK Android</span>
                                    <span class="block text-[10px] text-[#F5EFE2]/70 font-medium mt-0.5">Ukuran: ~55 MB • Versi Rilis Resmi Rasa Mandeh</span>
                                </div>
                            </div>
                            <div class="shrink-0 pl-2">
                                <span class="inline-flex items-center px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-xl bg-[#C9A227] text-xs font-black text-[#241B16] shadow-sm whitespace-nowrap">
                                    Unduh
                                </span>
                            </div>
                        </a>

                        {{-- Dua Tombol Pendukung: Cara Install & Scan QR (Berdampingan Rapi) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Tombol Sekunder 1: Cara Install di HP --}}
                            <button type="button" 
                                    @click="showGuideModal = true"
                                    class="bg-[#FDFBF7] hover:bg-white border border-[#C9A227]/30 hover:border-[#7A1F2B]/50 text-[#241B16] rounded-2xl px-4 py-3 shadow-xs hover:shadow transition-all duration-200 flex items-center space-x-3 text-left">
                                <div class="w-9 h-9 rounded-xl bg-[#7A1F2B]/10 flex items-center justify-center text-[#7A1F2B] shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-[#241B16]/50 uppercase tracking-wider">Panduan & Cara</span>
                                    <span class="block text-xs sm:text-sm font-bold text-[#241B16] whitespace-nowrap">Cara Install di HP</span>
                                </div>
                            </button>

                            {{-- Tombol Sekunder 2: Scan QR di HP --}}
                            <button type="button" 
                                    @click="showQrModal = true"
                                    class="bg-[#FDFBF7] hover:bg-white border border-[#C9A227]/30 hover:border-[#7A1F2B]/50 text-[#241B16] rounded-2xl px-4 py-3 shadow-xs hover:shadow transition-all duration-200 flex items-center space-x-3 text-left">
                                <div class="w-9 h-9 rounded-xl bg-[#C9A227]/20 flex items-center justify-center text-[#9E7D17] shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold text-[#241B16]/50 uppercase tracking-wider">Scan QR di HP</span>
                                    <span class="block text-xs sm:text-sm font-bold text-[#241B16] whitespace-nowrap">Langsung di HP</span>
                                </div>
                            </button>
                        </div>
                    </div>

                    {{-- KARTU 4 LANGKAH PASANG FILE APK --}}
                    <div class="bg-[#FDFBF7] border border-[#C9A227]/25 rounded-2xl p-4 shadow-xs">
                        <div class="flex items-center space-x-2 text-[#7A1F2B] font-bold text-xs uppercase tracking-wider mb-2.5">
                            <svg class="w-4 h-4 text-[#7A1F2B]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>Cara Pasang File APK di Smartphone Android:</span>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
                            <div class="p-2.5 rounded-xl bg-white border border-[#C9A227]/20 shadow-2xs">
                                <span class="block text-xs font-bold text-[#7A1F2B]">1. Unduh File</span>
                                <span class="block text-[11px] text-[#241B16]/65 mt-0.5">Klik tombol 'Download APK' di atas</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white border border-[#C9A227]/20 shadow-2xs">
                                <span class="block text-xs font-bold text-[#7A1F2B]">2. Buka Unduhan</span>
                                <span class="block text-[11px] text-[#241B16]/65 mt-0.5">Buka notifikasi atau folder Download HP</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white border border-[#C9A227]/20 shadow-2xs">
                                <span class="block text-xs font-bold text-[#7A1F2B]">3. Izinkan Sumber</span>
                                <span class="block text-[11px] text-[#241B16]/65 mt-0.5">Pilih 'Izinkan dari sumber ini' bila diminta</span>
                            </div>
                            <div class="p-2.5 rounded-xl bg-white border border-[#C9A227]/20 shadow-2xs">
                                <span class="block text-xs font-bold text-[#7A1F2B]">4. Selesai</span>
                                <span class="block text-[11px] text-[#241B16]/65 mt-0.5">Ketuk Install & nikmati aplikasi di Android!</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- KOLOM KANAN: MOCKUP SMARTPHONE MENAMPILKAN PERSIS HALAMAN MENU FLUTTER (SEPERTI FOTO SCREENSHOT USER) --}}
                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-[310px] sm:max-w-[335px]">

                        {{-- Glow Bayangan Halus di Belakang HP --}}
                        <div class="absolute inset-0 bg-gradient-to-tr from-[#7A1F2B]/15 via-[#C9A227]/20 to-[#7A1F2B]/10 rounded-[48px] blur-2xl transform scale-105 pointer-events-none"></div>

                        {{-- Smartphone Outer Frame (Dark Slate Titanium dengan Bezel Halus) --}}
                        <div class="relative bg-slate-900 border-[6px] border-[#241B16] rounded-[44px] shadow-2xl overflow-hidden ring-1 ring-[#C9A227]/30">

                            {{-- Dynamic Island / Punch Hole Notch --}}
                            <div class="absolute top-2 left-1/2 -translate-x-1/2 w-26 h-4.5 bg-black rounded-full z-30 flex items-center justify-between px-2.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-slate-800"></div>
                                <div class="w-2 h-2 rounded-full bg-slate-900 ring-1 ring-slate-800 flex items-center justify-center">
                                    <div class="w-1 h-1 rounded-full bg-blue-900/60"></div>
                                </div>
                            </div>

                            {{-- Mobile Status Bar (Tepat Jam 10:35 seperti Screenshot) --}}
                            <div class="pt-2 px-5 pb-1.5 bg-[#FAF8F5] flex items-center justify-between text-[#241B16] text-[10px] font-bold select-none border-b border-neutral-100">
                                <div class="flex items-center space-x-1">
                                    <span>10:35</span>
                                    <span class="text-[8px] font-normal text-neutral-400">M •••</span>
                                </div>
                                <div class="flex items-center space-x-1.5 text-[9px] text-[#241B16]/80">
                                    <span>🌙</span>
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L12 22l7.03-4.39C20.26 16.07 21 14.12 21 12c0-4.97-4.03-9-9-9z"/>
                                    </svg>
                                    <span class="text-[9px] font-mono">75</span>
                                </div>
                            </div>

                            {{-- SCREEN CONTENT: PERSIS TAMPILAN HALAMAN MENU FLUTTER (SESUAI FOTO HP USER) --}}
                            <div class="bg-[#FAF8F5] text-[#241B16] p-3 space-y-2.5 select-none font-sans text-xs">

                                {{-- 1. App Top Header: Logo RM Resmi Website & Keranjang --}}
                                <div class="flex items-center justify-between pt-0.5">
                                    <div class="flex items-center">
                                        <img src="/logo/Logo_Final.png" alt="Raso Mandeh Logo" class="h-8 w-auto object-contain">
                                    </div>
                                    {{-- Keranjang icon bulat --}}
                                    <div class="w-8 h-8 rounded-full border border-[#241B16]/15 flex items-center justify-center text-[#241B16] bg-white shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- 2. Judul Menu: 47 PILIHAN MENU & "Pilih laukmu." --}}
                                <div class="space-y-0.5">
                                    <span class="block text-[9px] font-black text-[#7A1F2B] tracking-widest uppercase">47 PILIHAN MENU</span>
                                    <h4 class="font-serif font-black text-base text-[#241B16] leading-tight">Pilih laukmu.</h4>
                                    <p class="text-[9px] text-[#241B16]/60 leading-tight">Semua menu disajikan sedap hari. Klik menu untuk melihat detail.</p>
                                </div>

                                {{-- 3. Search Bar --}}
                                <div class="relative">
                                    <div class="w-full bg-white border border-[#241B16]/15 rounded-full px-3 py-1.5 flex items-center space-x-2 text-[10px] text-[#241B16]/40 shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-[#241B16]/40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <span class="truncate">Cari rendang, ayam pop, gulai...</span>
                                    </div>
                                </div>

                                {{-- 4. Category Chips Horisontal --}}
                                <div class="flex items-center space-x-1.5 overflow-x-hidden text-[9px]">
                                    <span class="px-3 py-1 rounded-full bg-[#7A1F2B] text-white font-bold shadow-2xs">Semua</span>
                                    <span class="px-2.5 py-1 rounded-full bg-white border border-[#241B16]/15 text-[#241B16]/75">Paket</span>
                                    <span class="px-2.5 py-1 rounded-full bg-white border border-[#241B16]/15 text-[#241B16]/75">Daging</span>
                                    <span class="px-2.5 py-1 rounded-full bg-white border border-[#241B16]/15 text-[#241B16]/75">Ayam</span>
                                    <span class="px-2.5 py-1 rounded-full bg-white border border-[#241B16]/15 text-[#241B16]/75">Ikan</span>
                                </div>

                                {{-- 5. Filter Dropdown "Urutkan: Terlaris" --}}
                                <div class="w-full bg-white border border-[#241B16]/15 rounded-xl px-3 py-1 flex items-center justify-between text-[9px] text-[#241B16]/80 font-medium">
                                    <span>Urutkan: <strong>Terlaris</strong></span>
                                    <span class="text-[8px]">▼</span>
                                </div>

                                {{-- 6. GRID 2 KOLOM KARTU MENU (PERSIS SEPERTI FOTO SCREENSHOT USER) --}}
                                <div class="grid grid-cols-2 gap-2">

                                    {{-- Menu 1: Paket Nasi Kapau --}}
                                    <div class="bg-white rounded-xl border border-neutral-200/80 overflow-hidden shadow-2xs flex flex-col justify-between">
                                        <div class="relative h-20 bg-neutral-100 overflow-hidden">
                                            <img src="/menu/nasi-padang-rendang.webp" alt="Paket Nasi Kapau" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 bg-amber-600 text-white text-[7px] font-black px-1.5 py-0.5 rounded shadow-xs uppercase">KOMPLIT</span>
                                            <span class="absolute top-1 right-1 w-4.5 h-4.5 rounded-full bg-white/80 flex items-center justify-center text-[8px] text-neutral-400">♡</span>
                                        </div>
                                        <div class="p-2 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-[10px] font-bold text-[#241B16] truncate">Paket Nasi Kap...</h5>
                                                <span class="text-[8px] font-bold text-amber-600">★ 5.0</span>
                                            </div>
                                            <p class="text-[8px] text-[#241B16]/50 truncate">Nasi hangat, Rendang D...</p>
                                            <div class="flex items-center justify-between pt-0.5">
                                                <div>
                                                    <span class="block text-[10px] font-black text-[#7A1F2B]">Rp 35.000</span>
                                                    <span class="block text-[7px] text-[#241B16]/45">1.5k terjual</span>
                                                </div>
                                                <button class="w-5 h-5 rounded-md bg-[#F5EFE2] text-[#241B16] font-bold text-xs flex items-center justify-center border border-[#C9A227]/30 hover:bg-[#7A1F2B] hover:text-white transition-colors">+</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Menu 2: Rendang Daging --}}
                                    <div class="bg-white rounded-xl border border-neutral-200/80 overflow-hidden shadow-2xs flex flex-col justify-between">
                                        <div class="relative h-20 bg-neutral-100 overflow-hidden">
                                            <img src="/menu/nasi-padang-dendeng-balado.webp" alt="Rendang Daging" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 bg-amber-600 text-white text-[7px] font-black px-1.5 py-0.5 rounded shadow-xs uppercase">TERLARIS</span>
                                            <span class="absolute top-1 right-1 w-4.5 h-4.5 rounded-full bg-white/80 flex items-center justify-center text-[8px] text-rose-600">♥</span>
                                        </div>
                                        <div class="p-2 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-[10px] font-bold text-[#241B16] truncate">Rendang Daging</h5>
                                                <span class="text-[8px] font-bold text-amber-600">★ 4.9</span>
                                            </div>
                                            <p class="text-[8px] text-[#241B16]/50 truncate">Daging sapi pilihan yang dim...</p>
                                            <div class="flex items-center justify-between pt-0.5">
                                                <div>
                                                    <span class="block text-[10px] font-black text-[#7A1F2B]">Rp 22.000</span>
                                                    <span class="block text-[7px] text-[#241B16]/45">1.2k terjual</span>
                                                </div>
                                                <button class="w-5 h-5 rounded-md bg-[#F5EFE2] text-[#241B16] font-bold text-xs flex items-center justify-center border border-[#C9A227]/30 hover:bg-[#7A1F2B] hover:text-white transition-colors">+</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Menu 3: Nasi + Ayam Pop --}}
                                    <div class="bg-white rounded-xl border border-neutral-200/80 overflow-hidden shadow-2xs flex flex-col justify-between">
                                        <div class="relative h-20 bg-neutral-100 overflow-hidden">
                                            <img src="/menu/nasi-padang-ayam-pop.webp" alt="Ayam Pop" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 bg-amber-600 text-white text-[7px] font-black px-1.5 py-0.5 rounded shadow-xs uppercase">FAVORIT</span>
                                            <span class="absolute top-1 right-1 w-4.5 h-4.5 rounded-full bg-white/80 flex items-center justify-center text-[8px] text-rose-600">♥</span>
                                        </div>
                                        <div class="p-2 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-[10px] font-bold text-[#241B16] truncate">Nasi + Ayam Pop</h5>
                                                <span class="text-[8px] font-bold text-amber-600">★ 4.9</span>
                                            </div>
                                            <p class="text-[8px] text-[#241B16]/50 truncate">Nasi hangat pulen berpadu a...</p>
                                            <div class="flex items-center justify-between pt-0.5">
                                                <div>
                                                    <span class="block text-[10px] font-black text-[#7A1F2B]">Rp 25.000</span>
                                                    <span class="block text-[7px] text-[#241B16]/45">1.1k terjual</span>
                                                </div>
                                                <button class="w-5 h-5 rounded-md bg-[#F5EFE2] text-[#241B16] font-bold text-xs flex items-center justify-center border border-[#C9A227]/30 hover:bg-[#7A1F2B] hover:text-white transition-colors">+</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Menu 4: Telur Dadar Padang --}}
                                    <div class="bg-white rounded-xl border border-neutral-200/80 overflow-hidden shadow-2xs flex flex-col justify-between">
                                        <div class="relative h-20 bg-neutral-100 overflow-hidden">
                                            <img src="/menu/nasi-padang-telur-dadar.webp" alt="Telur Dadar" class="w-full h-full object-cover">
                                            <span class="absolute top-1 left-1 bg-amber-600 text-white text-[7px] font-black px-1.5 py-0.5 rounded shadow-xs uppercase">TERLARIS</span>
                                            <span class="absolute top-1 right-1 w-4.5 h-4.5 rounded-full bg-white/80 flex items-center justify-center text-[8px] text-neutral-400">♡</span>
                                        </div>
                                        <div class="p-2 space-y-1">
                                            <div class="flex items-center justify-between">
                                                <h5 class="text-[10px] font-bold text-[#241B16] truncate">Telur Dadar Pa...</h5>
                                                <span class="text-[8px] font-bold text-amber-600">★ 4.9</span>
                                            </div>
                                            <p class="text-[8px] text-[#241B16]/50 truncate">Telur bebek dadar tebal khas ...</p>
                                            <div class="flex items-center justify-between pt-0.5">
                                                <div>
                                                    <span class="block text-[10px] font-black text-[#7A1F2B]">Rp 14.000</span>
                                                    <span class="block text-[7px] text-[#241B16]/45">920 terjual</span>
                                                </div>
                                                <button class="w-5 h-5 rounded-md bg-[#F5EFE2] text-[#241B16] font-bold text-xs flex items-center justify-center border border-[#C9A227]/30 hover:bg-[#7A1F2B] hover:text-white transition-colors">+</button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- In-App Download CTA Button --}}
                                <a href="{{ $downloadUrl ?? (Route::has('app.download.apk') ? route('app.download.apk') : url('/download/apk')) }}" 
                                   class="w-full py-2 px-3 bg-[#7A1F2B] hover:bg-[#8B2332] text-white rounded-xl text-[10px] font-bold flex items-center justify-between shadow-xs border border-[#C9A227]/40 transition-transform active:scale-95 group">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-[#C9A227] shrink-0">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </span>
                                        <span class="font-extrabold text-white">Download Aplikasi Android</span>
                                    </div>
                                    <span class="px-1.5 py-0.5 rounded bg-[#C9A227] text-[8px] font-black text-[#241B16] shadow-xs">Unduh</span>
                                </a>

                                {{-- 7. BOTTOM NAVIGATION BAR (PERSIS SEPERTI SCREENSHOT USER: GONJONG BERANDA, MENU GOLD, PESANAN, PROFIL) --}}
                                <div class="pt-2 border-t border-[#241B16]/10 flex items-center justify-around text-[#241B16]/50 text-[8px] bg-white rounded-b-2xl">
                                    {{-- Beranda: Gonjong Minang --}}
                                    <div class="flex flex-col items-center space-y-0.5 cursor-pointer">
                                        <svg class="w-4 h-4 text-neutral-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2L2 9l2 2v10h16V11l2-2-10-7zm0 3.5L18 9v9H6V9l6-3.5z"/>
                                        </svg>
                                        <span>Beranda</span>
                                    </div>
                                    {{-- Menu: Aktif Emas (Sesuai Foto) --}}
                                    <div class="flex flex-col items-center space-y-0.5 text-[#C9A227] font-bold cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                        </svg>
                                        <span class="text-[#C9A227]">Menu</span>
                                    </div>
                                    {{-- Pesanan: Struk dengan badge dot merah --}}
                                    <div class="flex flex-col items-center space-y-0.5 cursor-pointer relative">
                                        <div class="relative">
                                            <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            <span class="absolute -top-0.5 -right-0.5 w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        </div>
                                        <span>Pesanan</span>
                                    </div>
                                    {{-- Profil --}}
                                    <div class="flex flex-col items-center space-y-0.5 cursor-pointer">
                                        <svg class="w-4 h-4 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Profil</span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 1: CARA INSTALL DI HP (SIDELOADING APK GUIDE)                       --}}
    {{-- ========================================================================= --}}
    <div x-show="showGuideModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/75 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">

        <div @click.away="showGuideModal = false"
             x-show="showGuideModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-[#C9A227]/30 relative">

            {{-- Close Button --}}
            <button @click="showGuideModal = false" 
                    class="absolute top-5 right-5 w-9 h-9 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 hover:text-neutral-800 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Modal Title --}}
            <div class="flex items-center space-x-3 mb-5">
                <div class="w-11 h-11 rounded-2xl bg-[#7A1F2B]/10 text-[#7A1F2B] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#241B16] font-serif">Panduan Instalasi APK Android</h3>
                    <p class="text-xs text-[#241B16]/60">Langkah mudah memasang aplikasi Rasa Mandeh di HP Anda</p>
                </div>
            </div>

            {{-- 4 Detailed Interactive Steps --}}
            <div class="space-y-3.5 my-6">
                <div class="flex items-start space-x-3.5 p-3 rounded-2xl bg-[#FDFBF7] border border-[#C9A227]/20">
                    <div class="w-7 h-7 rounded-xl bg-[#7A1F2B] text-white flex items-center justify-center text-xs font-bold shrink-0">1</div>
                    <div>
                        <h4 class="text-sm font-bold text-[#241B16]">Unduh File APK</h4>
                        <p class="text-xs text-[#241B16]/70 mt-0.5">
                            Ketuk tombol <strong>"Download APK Android"</strong>. Jika browser Chrome menampilkan notifikasi <em>"File mungkin berbahaya"</em>, pilih <strong>"Tetap Download"</strong> (Keep anyway).
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-3.5 p-3 rounded-2xl bg-[#FDFBF7] border border-[#C9A227]/20">
                    <div class="w-7 h-7 rounded-xl bg-[#7A1F2B] text-white flex items-center justify-center text-xs font-bold shrink-0">2</div>
                    <div>
                        <h4 class="text-sm font-bold text-[#241B16]">Buka Berkas Unduhan</h4>
                        <p class="text-xs text-[#241B16]/70 mt-0.5">
                            Setelah proses unduh selesai, tarik notifikasi HP ke bawah dan ketuk berkas <strong>rasa-mandeh-v1.0.0.apk</strong>, atau temukan di folder Download.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-3.5 p-3 rounded-2xl bg-[#FDFBF7] border border-[#C9A227]/20">
                    <div class="w-7 h-7 rounded-xl bg-[#7A1F2B] text-white flex items-center justify-center text-xs font-bold shrink-0">3</div>
                    <div>
                        <h4 class="text-sm font-bold text-[#241B16]">Izinkan Sumber Tidak Dikenal</h4>
                        <p class="text-xs text-[#241B16]/70 mt-0.5">
                            Jika muncul peringatan keamanan Android, ketuk <strong>Setelan (Settings)</strong>, lalu aktifkan centang <strong>"Izinkan dari sumber ini"</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex items-start space-x-3.5 p-3 rounded-2xl bg-[#FDFBF7] border border-[#C9A227]/20">
                    <div class="w-7 h-7 rounded-xl bg-[#7A1F2B] text-white flex items-center justify-center text-xs font-bold shrink-0">4</div>
                    <div>
                        <h4 class="text-sm font-bold text-[#241B16]">Pasang & Nikmati Sajian</h4>
                        <p class="text-xs text-[#241B16]/70 mt-0.5">
                            Ketuk tombol <strong>"Install"</strong> dan tunggu beberapa detik. Ikon aplikasi Rasa Mandeh kini siap digunakan di layar utama HP Anda!
                        </p>
                    </div>
                </div>
            </div>

            {{-- Trust Guarantee --}}
            <div class="flex items-center space-x-2 text-[11px] text-[#7A1F2B] bg-[#7A1F2B]/10 p-3 rounded-xl border border-[#7A1F2B]/20 mb-6">
                <svg class="w-4 h-4 text-[#7A1F2B] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span>Aplikasi resmi dari Raso Mandeh Resto. 100% aman, bebas malware, dan terverifikasi.</span>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center space-x-3">
                <a href="{{ $downloadUrl ?? (Route::has('app.download.apk') ? route('app.download.apk') : url('/download/apk')) }}" 
                   class="flex-1 py-3 px-5 bg-[#7A1F2B] hover:bg-[#5A1620] text-white rounded-xl text-center text-sm font-bold shadow-md shadow-[#7A1F2B]/20 transition-colors">
                    Download APK Sekarang
                </a>
                <button type="button" 
                        @click="showGuideModal = false"
                        class="px-5 py-3 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-sm font-bold transition-colors">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- MODAL 2: SCAN QR DI HP (DIRECT SCAN TO DOWNLOAD)                          --}}
    {{-- ========================================================================= --}}
    <div x-show="showQrModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/75 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">

        <div @click.away="showQrModal = false"
             x-show="showQrModal"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-[#C9A227]/30 text-center relative">

            {{-- Close Button --}}
            <button @click="showQrModal = false" 
                    class="absolute top-5 right-5 w-9 h-9 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 hover:text-neutral-800 flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Title --}}
            <div class="w-12 h-12 rounded-2xl bg-[#C9A227]/20 text-[#9E7D17] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-[#241B16] font-serif">Scan QR Code di Smartphone</h3>
            <p class="text-xs text-[#241B16]/60 mt-1 max-w-xs mx-auto">Arahkan kamera HP Android Anda ke QR Code di bawah untuk langsung mengunduh file APK.</p>

            {{-- QR Code Image Box --}}
            <div class="my-6 p-4 rounded-3xl bg-white border-2 border-dashed border-[#C9A227] inline-block shadow-sm">
                @if(isset($qrCodeSvg) && !empty($qrCodeSvg))
                    <img src="{{ $qrCodeSvg }}" alt="QR Code Unduh APK Rasa Mandeh" class="w-48 h-48 mx-auto object-contain">
                @else
                    <img src="{{ Route::has('app.download.qr') ? route('app.download.qr') : url('/download/qr') }}" alt="QR Code Unduh APK Rasa Mandeh" class="w-48 h-48 mx-auto object-contain">
                @endif
            </div>

            {{-- Steps list --}}
            <div class="text-left text-xs text-[#241B16]/75 bg-[#FDFBF7] p-3.5 rounded-2xl border border-[#C9A227]/20 space-y-1.5 mb-5">
                <div class="flex items-center space-x-2">
                    <span class="w-4 h-4 rounded-full bg-[#7A1F2B] text-white text-[10px] flex items-center justify-center font-bold">1</span>
                    <span>Buka Kamera atau Pemindai QR bawaan di HP Android.</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-4 h-4 rounded-full bg-[#7A1F2B] text-white text-[10px] flex items-center justify-center font-bold">2</span>
                    <span>Arahkan kamera ke kode QR di atas.</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-4 h-4 rounded-full bg-[#7A1F2B] text-white text-[10px] flex items-center justify-center font-bold">3</span>
                    <span>Ketuk link download yang muncul di layar HP.</span>
                </div>
            </div>

            {{-- Copy Link Button --}}
            <div class="flex items-center space-x-2">
                <button type="button" 
                        @click="copyUrl('{{ $downloadUrl ?? (Route::has('app.download.apk') ? route('app.download.apk') : url('/download/apk')) }}')"
                        class="flex-1 py-2.5 px-4 bg-neutral-100 hover:bg-neutral-200 text-[#241B16] rounded-xl text-xs font-bold transition-colors flex items-center justify-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span x-text="copied ? 'Tautan Berhasil Disalin!' : 'Salin Tautan Unduh'"></span>
                </button>
                <a href="{{ $downloadUrl ?? (Route::has('app.download.apk') ? route('app.download.apk') : url('/download/apk')) }}" 
                   class="py-2.5 px-4 bg-[#7A1F2B] hover:bg-[#5A1620] text-white rounded-xl text-xs font-bold transition-colors">
                    Unduh di PC
                </a>
            </div>

        </div>
    </div>

</section>
