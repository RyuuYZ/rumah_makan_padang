<header :class="[
            isScrolled ? 'bg-[#F5EFE2]/95 backdrop-blur-md shadow-md border-b border-[#C9A227]/20 py-3' : 'bg-transparent py-5',
            isNavbarHidden ? '-translate-y-full' : 'translate-y-0'
        ]"
        class="fixed top-0 left-0 right-0 z-40 transition-transform duration-300 ease-in-out">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="#" class="flex items-center space-x-3 group">
                <img src="/logo/Logo_Final.png" alt="Raso Mandeh Logo" class="brand-logo group-hover:scale-105 transition-transform">
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#menu" class="text-[#241B16] hover:text-[#7A1F2B] font-medium transition-colors relative group py-1">
                    Menu Kami
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#7A1F2B] transition-all duration-200 group-hover:w-full"></span>
                </a>
                <a href="#cabang" class="text-[#241B16] hover:text-[#7A1F2B] font-medium transition-colors relative group py-1">
                    Pilih Cabang
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#7A1F2B] transition-all duration-200 group-hover:w-full"></span>
                </a>
                <a href="#cerita" class="text-[#241B16] hover:text-[#7A1F2B] font-medium transition-colors relative group py-1">
                    Cerita Kami
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#7A1F2B] transition-all duration-200 group-hover:w-full"></span>
                </a>
                <a href="#ulasan" class="text-[#241B16] hover:text-[#7A1F2B] font-medium transition-colors relative group py-1">
                    Ulasan
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#7A1F2B] transition-all duration-200 group-hover:w-full"></span>
                </a>
                <a href="{{ route('order.search.form') }}" class="text-[#7A1F2B] font-bold hover:text-[#5a1620] transition-colors relative group py-1 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Cari Pesanan</span>
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[#7A1F2B] transition-all duration-200 group-hover:w-full"></span>
                </a>

                <!-- Selected branch badge indicator -->
                <div class="flex items-center space-x-1.5 bg-[#C9A227]/15 border border-[#C9A227]/30 px-3 py-1 rounded-full text-xs font-semibold text-[#7A1F2B]">
                    <svg class="w-3.5 h-3.5 text-[#C9A227]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span x-text="selectedBranch"></span>
                </div>

                <!-- Cart Button -->
                <button @click="isCartOpen = true" 
                        class="relative p-2.5 rounded-full hover:bg-[#C9A227]/20 text-[#241B16] transition-colors focus:outline-none"
                        aria-label="Buka Keranjang">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span x-show="cartCount > 0" 
                          x-text="cartCount"
                          class="absolute -top-1 -right-1 bg-[#7A1F2B] text-white text-[11px] font-bold w-5 h-5 flex items-center justify-center rounded-full shadow border-2 border-[#F5EFE2]">
                    </span>
                </button>

                <!-- CTA Button -->
                <a href="#menu" class="bg-[#7A1F2B] hover:bg-[#3D0F15] text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-md hover:shadow-lg transform active:scale-95 text-sm flex items-center space-x-2">
                    <span>Pesan Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>

            <!-- Mobile Action Buttons -->
            <div class="flex items-center space-x-3 md:hidden">
                <button @click="isCartOpen = true" 
                        class="relative p-2 rounded-full hover:bg-[#C9A227]/20 text-[#241B16]"
                        aria-label="Keranjang">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <span x-show="cartCount > 0" 
                          x-text="cartCount"
                          class="absolute -top-1 -right-1 bg-[#7A1F2B] text-white text-[10px] font-bold w-4 h-4 flex items-center justify-center rounded-full">
                    </span>
                </button>

                <button @click="isMobileMenuOpen = !isMobileMenuOpen" 
                        class="p-2 text-[#241B16] rounded-lg hover:bg-[#C9A227]/20 focus:outline-none"
                        aria-label="Toggle Menu">
                    <svg x-show="!isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="isMobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="isMobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             @click.away="isMobileMenuOpen = false"
             class="md:hidden mt-4 pb-4 pt-2 border-t border-[#C9A227]/20 space-y-3 bg-[#F5EFE2] rounded-2xl px-4 shadow-xl">
            <a href="#menu" @click="isMobileMenuOpen = false" class="block font-medium py-2 text-[#241B16] hover:text-[#7A1F2B]">Menu Kami</a>
            <a href="#cabang" @click="isMobileMenuOpen = false" class="block font-medium py-2 text-[#241B16] hover:text-[#7A1F2B]">Pilih Cabang</a>
            <a href="#cerita" @click="isMobileMenuOpen = false" class="block font-medium py-2 text-[#241B16] hover:text-[#7A1F2B]">Cerita Kami</a>
            <a href="#ulasan" @click="isMobileMenuOpen = false" class="block font-medium py-2 text-[#241B16] hover:text-[#7A1F2B]">Ulasan</a>
            <a href="{{ route('order.search.form') }}" class="block font-bold py-2 pb-3 text-[#7A1F2B] hover:text-[#5a1620] flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Cari Pesanan Saya</span>
            </a>
            <div class="pt-2 border-t border-[#C9A227]/20 flex items-center justify-between">
                <span class="text-xs text-[#241B16]/70">Cabang saat ini:</span>
                <span class="text-xs font-bold text-[#7A1F2B]" x-text="selectedBranch"></span>
            </div>
        </div>
    </nav>
</header>
