@props(['item'])

<div x-show="(selectedCategory === 'all' || selectedCategory === '{{ $item->kategori }}') && 
             ('{{ strtolower(addslashes($item->nama)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($item->deskripsi)) }}'.includes(searchQuery.toLowerCase()))"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col border border-[#C9A227]/20 group">
    
    <!-- Photo with Badges -->
    <div class="relative aspect-video overflow-hidden bg-stone-100">
        <img src="{{ $item->foto }}" 
             alt="{{ $item->nama }}" 
             loading="lazy"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        
        <!-- Category Badge -->
        <span class="absolute top-3 right-3 bg-black/50 backdrop-blur-md text-white text-[11px] font-medium px-2.5 py-1 rounded-full uppercase tracking-wider">
            {{ $item->kategori }}
        </span>

        <!-- Special Highlight Badge -->
        @if($item->badge)
        <span class="absolute top-3 left-3 text-xs font-bold px-3 py-1 rounded-full shadow-md
            @if($item->badge === 'Signature') bg-[#7A1F2B] text-white
            @elseif($item->badge === 'Favorit') bg-[#C9A227] text-[#241B16]
            @else bg-emerald-600 text-white @endif">
            {{ $item->badge }}
        </span>
        @endif
    </div>

    <!-- Details -->
    <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
        <div>
            <div class="flex items-center justify-between gap-2 mb-1.5">
                <h3 class="font-serif font-bold text-lg text-[#241B16] group-hover:text-[#7A1F2B] transition-colors line-clamp-1">
                    {{ $item->nama }}
                </h3>
                <div class="flex items-center space-x-1 flex-shrink-0 bg-[#C9A227]/10 px-2 py-0.5 rounded-md text-[#C9A227]">
                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="text-xs font-bold text-[#241B16]">{{ $item->rating }}</span>
                </div>
            </div>

            <p class="text-sm text-[#241B16]/70 line-clamp-2 leading-relaxed">
                {{ $item->deskripsi }}
            </p>
        </div>

        <!-- Price and Action -->
        <div class="pt-3 border-t border-[#C9A227]/15 flex items-center justify-between">
            <div>
                <span class="text-[11px] text-[#241B16]/60 uppercase tracking-wider block">Harga Porsi</span>
                <span class="text-lg font-bold text-[#7A1F2B]">
                    @if($item->display_price == 0)
                        GRATIS 🎉
                    @else
                        Rp {{ number_format($item->display_price, 0, ',', '.') }}
                    @endif
                </span>
            </div>

            <button @click="addToCart({ id: {{ $item->id }}, nama: '{{ addslashes($item->nama) }}', harga: {{ $item->display_price }}, foto: '{{ $item->foto }}' })"
                    class="bg-[#7A1F2B] hover:bg-[#3D0F15] text-white px-4 py-2 rounded-xl text-sm font-semibold shadow transition-all active:scale-95 flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Pesan</span>
            </button>
        </div>
    </div>
</div>
