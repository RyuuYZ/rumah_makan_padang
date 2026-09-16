<section class="relative min-h-screen flex items-center justify-center pt-24 pb-16 overflow-hidden">
    <!-- Ambient Minang Glow -->
    <div class="absolute inset-0 bg-gradient-to-br from-[#F5EFE2] via-[#7A1F2B]/5 to-[#C9A227]/10 pointer-events-none"></div>
    <div class="absolute top-1/4 left-1/10 w-96 h-96 bg-[#C9A227]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/10 w-96 h-96 bg-[#7A1F2B]/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Left: Content -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 bg-[#7A1F2B]/10 border border-[#7A1F2B]/20 px-4 py-1.5 rounded-full">
                    <span class="w-2 h-2 rounded-full bg-[#7A1F2B] animate-pulse"></span>
                    <span class="text-[#7A1F2B] font-semibold tracking-wider uppercase text-xs">Warisan Keluarga Sejak 1950</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-serif font-bold text-[#241B16] leading-[1.15]">
                    {!! nl2br(e($settings['home_title'] ?? "Rasa Autentik\nMinangkabau\ndalam Setiap Gigitan")) !!}
                </h1>

                <p class="text-lg text-[#241B16]/80 max-w-xl mx-auto lg:mx-0 leading-relaxed font-normal">
                    {{ $settings['home_subtitle'] ?? 'Nikmati kelezatan masakan Padang legendaris dengan resep warisan leluhur yang telah dijaga selama lebih dari 7 dekade. Dimasak dengan santan kental dan rempah pilihan langsung dari Sumatera Barat.' }}
                </p>

                <!-- Stats Highlights -->
                <div class="grid grid-cols-3 gap-4 pt-4 border-t border-[#C9A227]/25 max-w-lg mx-auto lg:mx-0">
                    <div class="space-y-1">
                        <div class="flex items-center justify-center lg:justify-start space-x-1 text-[#C9A227]">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-bold text-2xl text-[#241B16]">4.9</span>
                        </div>
                        <p class="text-xs text-[#241B16]/70">Rating Kepuasan</p>
                    </div>
                    <div class="space-y-1">
                        <div class="font-bold text-2xl text-[#241B16] text-center lg:text-left">50+</div>
                        <p class="text-xs text-[#241B16]/70">Lauk Setiap Hari</p>
                    </div>
                    <div class="space-y-1">
                        <div class="font-bold text-2xl text-[#241B16] text-center lg:text-left">73+</div>
                        <p class="text-xs text-[#241B16]/70">Tahun Menjaga Mutu</p>
                    </div>
                </div>

                <!-- Call to Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                    <a href="#menu" class="bg-[#7A1F2B] hover:bg-[#3D0F15] text-white px-8 py-4 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5 text-center flex items-center justify-center space-x-2">
                        <span>Pesan Menu Sekarang</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('order.search.form') }}" class="border-2 border-[#7A1F2B] text-[#7A1F2B] hover:bg-[#7A1F2B] hover:text-white px-8 py-4 rounded-xl font-semibold transition-all text-center flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Cari Pesanan</span>
                    </a>
                </div>
            </div>

            <!-- Hero Right: Visual Photo & Floating Badges -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md">
                    <!-- Songket Aura Backdrop -->
                    <div class="absolute -inset-4 bg-gradient-to-tr from-[#C9A227]/30 to-[#7A1F2B]/30 rounded-3xl blur-2xl transform rotate-3"></div>

                    <!-- Main Dish Image -->
                    <div class="relative z-10 overflow-hidden rounded-3xl shadow-2xl border-4 border-white/60">
                        <img src="{{ $settings['home_hero_image'] ?? '/main-foto.webp' }}" 
                             alt="Nasi Padang Komplit Raso Mandeh" 
                             class="w-full h-[420px] object-cover hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <span class="bg-[#C9A227] text-[#241B16] text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">
                                Signature Dish
                            </span>
                        </div>
                    </div>

                    <!-- Floating Customer Satisfaction Card -->
                    <div class="absolute -bottom-6 -left-6 z-20 bg-white/95 backdrop-blur-sm p-4 rounded-2xl shadow-xl border border-[#C9A227]/30 flex items-center space-x-3.5">
                        <div class="w-12 h-12 bg-[#C9A227]/20 rounded-xl flex items-center justify-center text-[#C9A227]">
                            <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-[#241B16]">10,000+ Pelanggan</div>
                            <div class="text-xs text-[#241B16]/70">Puas dengan Kelezatan Minang</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
