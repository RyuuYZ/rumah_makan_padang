<section id="menu" class="py-20 bg-[#F5EFE2]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Title -->
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-[#7A1F2B] font-semibold text-xs uppercase tracking-widest block mb-2">Hidangan Spesial</span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-serif font-bold text-[#241B16]">
                Menu Autentik Raso Mandeh
            </h2>
            <p class="text-sm sm:text-base text-[#241B16]/75 mt-3 leading-relaxed">
                Dimasak segar setiap pagi dengan santan kelapa murni, cabai segar Minang, dan racikan rempah istimewa keluarga.
            </p>
        </div>

        <!-- Category Filter Pills -->

        <div class="flex flex-wrap items-center justify-center gap-2.5 mb-8">
            @foreach($categories as $category)
            <button @click="selectedCategory = '{{ $category['id'] }}'"
                    :class="selectedCategory === '{{ $category['id'] }}' 
                        ? 'bg-[#7A1F2B] text-white shadow-songket font-semibold scale-105' 
                        : 'bg-white text-[#241B16] hover:bg-[#C9A227]/15 border border-[#C9A227]/30'"
                    class="px-5 py-2.5 rounded-full text-sm transition-all duration-200 cursor-pointer">
                {{ $category['name'] }}
            </button>
            @endforeach
        </div>

        <!-- Live Search Bar -->
        <div class="max-w-md mx-auto mb-12">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#7A1F2B]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari lauk favorit (Rendang, Ayam Pop, Gulai...)" 
                       class="w-full pl-11 pr-10 py-3.5 rounded-2xl bg-white border border-[#C9A227]/40 text-[#241B16] placeholder-[#241B16]/50 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#7A1F2B] focus:border-transparent transition-all">
                <button x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-[#241B16]/50 hover:text-[#7A1F2B]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($menuItems as $item)
                @include('components.menu-card', ['item' => $item])
            @endforeach
        </div>

    </div>
</section>

