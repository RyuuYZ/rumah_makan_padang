<section id="cabang" class="py-16 bg-white/70 border-y border-[#C9A227]/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-[#7A1F2B] font-semibold text-xs uppercase tracking-widest block mb-2">Lokasi Restoran</span>
            <h2 class="text-3xl sm:text-4xl font-serif font-bold text-[#241B16]">
                Pilih Cabang Raso Mandeh Terdekat
            </h2>
            <p class="text-sm sm:text-base text-[#241B16]/70 mt-2">
                Hadir di berbagai kota besar di Indonesia. Pilih cabang Anda untuk melihat ketersediaan menu dan layanan pemesanan setempat.
            </p>
        </div>

        <!-- Branches Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
            @foreach($branches as $branch)
            <button @click="selectedBranch = '{{ $branch->kota }}'"
                    :class="selectedBranch === '{{ $branch->kota }}' 
                        ? 'border-[#7A1F2B] bg-[#7A1F2B] text-white shadow-songket scale-[1.03]' 
                        : 'border-[#C9A227]/30 bg-white hover:border-[#C9A227] hover:bg-[#F5EFE2]/60 text-[#241B16]'"
                    class="p-4 rounded-2xl border-2 transition-all duration-200 text-center flex flex-col items-center justify-center space-y-2 group">
                <div :class="selectedBranch === '{{ $branch->kota }}' ? 'text-[#C9A227]' : 'text-[#7A1F2B] group-hover:scale-110'" 
                     class="w-10 h-10 rounded-full bg-black/5 flex items-center justify-center transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm leading-tight">{{ $branch->kota }}</h4>
                    <span :class="selectedBranch === '{{ $branch->kota }}' ? 'text-white/80' : 'text-[#241B16]/60'" class="text-[11px] block mt-0.5">
                        {{ $branch->jam_buka }}
                    </span>
                </div>
            </button>
            @endforeach
        </div>

        <!-- Selected Branch Detail Card -->
        <div class="mt-8 p-6 bg-gradient-to-r from-[#7A1F2B]/5 via-[#C9A227]/10 to-[#7A1F2B]/5 rounded-2xl border border-[#C9A227]/30 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-[#7A1F2B] text-white flex items-center justify-center flex-shrink-0 shadow">
                    <svg class="w-6 h-6 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1.5 sm:gap-2">
                        <h4 class="font-bold text-lg text-[#241B16] leading-tight">Cabang Terpilih: Raso Mandeh - <span x-text="selectedBranch"></span></h4>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 w-fit whitespace-nowrap">Buka Hari Ini</span>
                    </div>
                    <p class="text-sm text-[#241B16]/75 mt-0.5">
                        Pesanan dan layanan makan di tempat akan diproses langsung oleh tim dapur cabang <span class="font-semibold" x-text="selectedBranch"></span>.
                    </p>
                </div>
            </div>
            <a href="#menu" class="bg-[#7A1F2B] hover:bg-[#3D0F15] text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow transition-all whitespace-nowrap">
                Pilih Menu dari Cabang Ini &rarr;
            </a>
        </div>
    </div>
</section>
