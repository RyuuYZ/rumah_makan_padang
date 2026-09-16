@props(['paginator', 'entity' => 'DATA'])

@if ($paginator->hasPages() || $paginator->total() > 0)
<div class="px-6 py-4 border-t border-neutral-100 bg-white flex flex-col sm:flex-row items-center justify-between gap-4">
    <!-- Total Pill Badge -->
    <div class="inline-flex items-center px-4 py-2 rounded-full bg-neutral-100 border border-neutral-200/80 text-[11px] font-bold text-neutral-600 tracking-wider uppercase shadow-2xs">
        TOTAL:&nbsp;<span class="text-neutral-900 font-extrabold text-xs">{{ number_format($paginator->total(), 0, ',', '.') }}</span>&nbsp;{{ strtoupper($entity) }}
    </div>

    <!-- Pagination Navigator Controls -->
    @if ($paginator->hasPages())
    <div class="flex items-center space-x-2" 
         x-data="adminTablePagination({{ $paginator->currentPage() }}, {{ $paginator->lastPage() }})">
        
        <!-- Prev Button -->
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full border border-neutral-200 bg-neutral-100/50 text-neutral-300 text-xs font-semibold cursor-not-allowed select-none shadow-2xs">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                <span>Prev</span>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" 
               class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-[#7A1F2B]/40 text-neutral-600 hover:text-[#7A1F2B] text-xs font-bold transition-all shadow-2xs group">
                <svg class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                <span>Prev</span>
            </a>
        @endif

        <!-- Jump To Page Pill -->
        <div class="flex items-center space-x-2 bg-white px-3 py-1 rounded-full border border-neutral-200 shadow-2xs">
            <span class="text-[11px] font-bold text-neutral-500 uppercase tracking-wider">KE HAL:</span>
            <input type="number" 
                   x-model.number="targetPage" 
                   @keydown.enter.prevent="goToPage()"
                   min="1" 
                   max="{{ $paginator->lastPage() }}" 
                   class="w-12 text-center py-0.5 px-1 bg-neutral-50 border border-neutral-300 rounded-lg text-xs font-black text-neutral-800 outline-none focus:border-[#7A1F2B] focus:ring-1 focus:ring-[#7A1F2B] transition-all"
                   value="{{ $paginator->currentPage() }}">
            <span class="text-xs font-bold text-neutral-400">/ {{ $paginator->lastPage() }}</span>
            <button type="button" 
                    @click="goToPage()" 
                    class="inline-flex items-center space-x-1 bg-[#7A1F2B] hover:bg-[#611922] text-white px-2.5 py-1 rounded-lg text-xs font-bold shadow-2xs transition-all hover:scale-105 active:scale-95 cursor-pointer">
                <span>Go</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </button>
        </div>

        <!-- Next Button -->
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" 
               class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full border border-neutral-200 bg-white hover:bg-neutral-50 hover:border-[#7A1F2B]/40 text-neutral-600 hover:text-[#7A1F2B] text-xs font-bold transition-all shadow-2xs group">
                <span>Next</span>
                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-full border border-neutral-200 bg-neutral-100/50 text-neutral-300 text-xs font-semibold cursor-not-allowed select-none shadow-2xs">
                <span>Next</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif

    </div>
    @endif
</div>
@endif
