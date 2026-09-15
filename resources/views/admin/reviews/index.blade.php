@extends('layouts.admin')

@section('title', 'Moderasi Ulasan - Admin Raso Mandeh')
@section('header_title', 'Moderasi Ulasan Pelanggan')

@section('content')
<div class="space-y-5">
    
    <!-- Clean Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-neutral-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reviews.index') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors
               {{ !request('status') ? 'bg-[#7A1F2B] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                Semua Ulasan
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors
               {{ request('status') === 'pending' ? 'bg-[#7A1F2B] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors
               {{ request('status') === 'approved' ? 'bg-[#7A1F2B] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                Disetujui
            </a>
        </div>

        <span class="text-xs text-neutral-400">
            Total: <strong class="text-neutral-800">{{ $reviews->total() }}</strong> ulasan
        </span>
    </div>

    <!-- Clean Reviews Table -->
    <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left text-xs table-fixed">
                <thead class="bg-neutral-50/70 border-b border-neutral-200/80 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider font-serif">
                    <tr>
                        <th class="w-[18%] py-3 px-4">Pelanggan</th>
                        <th class="w-[15%] py-3 px-4">Cabang</th>
                        <th class="w-[15%] py-3 px-4">Rating</th>
                        <th class="w-[35%] py-3 px-4">Ulasan & Testimoni</th>
                        <th class="w-[11%] py-3 px-4">Status</th>
                        <th class="w-[6%] py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="py-3 px-4">
                            <span class="font-bold text-neutral-900 block truncate">{{ $review->nama_pelanggan }}</span>
                            <span class="text-[10px] text-neutral-400 block">{{ $review->created_at->format('d M Y') }}</span>
                        </td>
                        <td class="py-3 px-4 text-neutral-600 font-medium truncate">
                            {{ $review->branch->kota ?? 'Umum' }}
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="flex items-center space-x-0.5 text-amber-400">
                                @for($i = 0; $i < $review->rating; $i++)
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <p class="text-neutral-700 text-xs leading-relaxed" title="{{ $review->komentar }}">
                                "{{ \Illuminate\Support\Str::limit($review->komentar, 100) }}"
                            </p>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold mb-1
                                {{ $review->is_approved ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-amber-50 text-amber-700 border border-amber-200/60' }}">
                                {{ $review->is_approved ? '● Tampil' : '○ Menunggu' }}
                            </span>
                            @if($review->is_pinned)
                            <br>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#7A1F2B]/10 text-[#7A1F2B] border border-[#7A1F2B]/20 mt-1">
                                📌 Disematkan
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div x-data="{ openMenu: false }" class="inline-block text-left relative">
                                <button @click="openMenu = !openMenu" @click.away="openMenu = false" 
                                        class="p-1.5 rounded-xl text-neutral-400 hover:text-[#7A1F2B] hover:bg-[#F5EFE2] transition-colors focus:outline-none"
                                        title="Opsi Aksi">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                    </svg>
                                </button>
                                
                                <div x-show="openMenu" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-lg border border-[#C9A227]/20 z-50 py-1.5 overflow-hidden"
                                     style="display: none;"
                                     x-cloak>
                                     
                                    <form action="{{ route('admin.reviews.toggleApprove', $review->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-neutral-700 hover:bg-[#F5EFE2] hover:text-[#7A1F2B] transition-colors">
                                            {{ $review->is_approved ? 'Sembunyikan Ulasan' : 'Setujui (Tampilkan)' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.reviews.togglePin', $review->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-neutral-700 hover:bg-[#F5EFE2] hover:text-[#7A1F2B] transition-colors">
                                            {{ $review->is_pinned ? 'Lepas Sematan' : 'Sematkan ke Beranda' }}
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                            Hapus Ulasan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-neutral-400">
                            Tidak ada ulasan pada kategori ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-admin-pagination :paginator="$reviews" entity="Ulasan" />
    </div>

</div>
@endsection
