@extends('layouts.admin')

@section('title', 'Menu Masakan - Admin Raso Mandeh')
@section('header_title', 'Kelola Menu Masakan')

@section('content')
<div x-data="{ 
        isAddModalOpen: false, 
        isEditModalOpen: false, 
        editItem: { id: null, nama: '', kategori: 'daging', deskripsi: '', foto: '', badge: '', rating: 5.0, harga: 30000, stock_quantity: null } 
     }" 
     class="space-y-5">
    
    <!-- Clean Action & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-neutral-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('admin.menu.index') }}" class="flex flex-wrap items-center gap-2.5">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="Cari hidangan..." 
                   class="text-xs py-2 px-3 rounded-xl border border-neutral-300 w-48 focus:ring-1 focus:ring-[#7A1F2B] outline-none">

            <select name="kategori" onchange="this.form.submit()" class="text-xs py-2 px-3 rounded-xl border border-neutral-300 bg-white font-medium outline-none">
                <option value="all">Semua Kategori</option>
                @foreach($categories as $key => $name)
                <option value="{{ $key }}" {{ request('kategori') === $key ? 'selected' : '' }}>
                    {{ $name }}
                </option>
                @endforeach
            </select>

            <button type="submit" class="bg-[#7A1F2B] hover:bg-[#611922] text-white text-xs font-semibold py-2 px-3.5 rounded-xl shadow-xs transition-colors">
                Cari
            </button>
            @if(request('search') || request('kategori'))
            <a href="{{ route('admin.menu.index') }}" class="text-xs text-neutral-400 hover:text-neutral-700 underline">
                Reset
            </a>
            @endif
        </form>

        <button @click="isAddModalOpen = true" 
                class="bg-[#7A1F2B] hover:bg-[#611922] text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-xs flex items-center justify-center space-x-1.5 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Hidangan Baru</span>
        </button>
    </div>

    <!-- Clean Menu Table -->
    <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left text-xs table-fixed">
                <thead class="bg-neutral-50/70 border-b border-neutral-200/80 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider font-serif">
                    <tr>
                        <th class="w-[32%] py-3 px-4">Hidangan</th>
                        <th class="w-[14%] py-3 px-4">Kategori</th>
                        <th class="w-[15%] py-3 px-4">Badge & Rating</th>
                        <th class="w-[14%] py-3 px-4">Harga Porsi</th>
                        <th class="w-[18%] py-3 px-4 text-center">Stok & Status</th>
                        <th class="w-[7%] py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($menuItems as $item)
                    @php
                        $basePrice = $item->branchPrices->first()->harga ?? 25000;
                    @endphp
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="py-3 px-4">
                            <div class="flex items-center space-x-3">
                                <img src="{{ $item->foto }}" alt="{{ $item->nama }}" class="w-10 h-10 rounded-xl object-cover flex-shrink-0 bg-neutral-100">
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-neutral-900 text-xs truncate">{{ $item->nama }}</h4>
                                    <p class="text-[11px] text-neutral-500 truncate" title="{{ $item->deskripsi }}">{{ $item->deskripsi }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-neutral-100 text-neutral-700">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="flex items-center space-x-1.5">
                                @if($item->badge)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold text-white
                                    @if($item->badge === 'Signature') bg-[#7A1F2B]
                                    @elseif($item->badge === 'Favorit') bg-[#C9A227] text-neutral-900
                                    @else bg-emerald-600 @endif">
                                    {{ $item->badge }}
                                </span>
                                @endif
                                <span class="text-xs font-semibold text-amber-500 flex items-center">
                                    ★ {{ $item->rating }}
                                </span>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-bold text-neutral-900 whitespace-nowrap">
                            Rp {{ number_format($basePrice, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center whitespace-nowrap space-y-1">
                            <div class="text-[11px] font-bold text-neutral-700">
                                @if($item->stock_quantity !== null)
                                    Sisa Stok: <span class="{{ $item->stock_quantity <= 5 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $item->stock_quantity }} porsi</span>
                                @else
                                    Stok: <span class="text-neutral-400">Tak Terbatas</span>
                                @endif
                            </div>
                            <form action="{{ route('admin.menu.toggleActive', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold transition-colors
                                    {{ $item->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60 hover:bg-emerald-100' : 'bg-neutral-100 text-neutral-500 border border-neutral-200 hover:bg-neutral-200' }}"
                                    title="Klik untuk mengubah ketersediaan">
                                    {{ $item->is_active ? '● Aktif' : '○ Non-aktif' }}
                                </button>
                            </form>
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
                                
                                <!-- Dropdown Menu -->
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
                                     
                                    <button @click="editItem = {
                                                id: {{ $item->id }},
                                                nama: '{{ addslashes($item->nama) }}',
                                                kategori: '{{ $item->kategori }}',
                                                deskripsi: '{{ addslashes($item->deskripsi) }}',
                                                foto: '{{ $item->foto }}',
                                                badge: '{{ $item->badge }}',
                                                rating: {{ $item->rating }},
                                                harga: {{ $basePrice }},
                                                stock_quantity: {{ $item->stock_quantity ?? 'null' }}
                                            }; isEditModalOpen = true; openMenu = false" 
                                            class="w-full text-left px-4 py-2 text-xs font-medium text-neutral-700 hover:bg-[#F5EFE2] hover:text-[#7A1F2B] transition-colors">
                                        Edit Hidangan
                                    </button>
                                    
                                    <form action="{{ route('admin.menu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus hidangan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-neutral-400">
                            Tidak ditemukan menu dengan filter tersebut.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-admin-pagination :paginator="$menuItems" entity="Menu" />
    </div>

    <!-- Modal Tambah Menu Baru -->
    <div x-show="isAddModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-neutral-900/50 backdrop-blur-xs" @click="isAddModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-5 space-y-4 border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="font-bold text-base text-neutral-900">Tambah Hidangan Baru</h3>
                    <button @click="isAddModalOpen = false" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Nama Hidangan *</label>
                        <input type="text" name="nama" required placeholder="Contoh: Gulai Tunjang" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:ring-1 focus:ring-[#7A1F2B] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Kategori *</label>
                            <select name="kategori" required class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 bg-white outline-none">
                                <option value="daging">Lauk Daging</option>
                                <option value="ayam">Lauk Ayam</option>
                                <option value="ikan">Lauk Ikan</option>
                                <option value="sayur">Sayur & Sambal</option>
                                <option value="minuman">Minuman Tradisional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Harga (Rp) *</label>
                            <input type="number" name="harga" required value="30000" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Badge</label>
                            <select name="badge" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 bg-white outline-none">
                                <option value="">Tanpa Badge</option>
                                <option value="Signature">Signature</option>
                                <option value="Favorit">Favorit</option>
                                <option value="Baru">Baru</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Rating</label>
                            <input type="number" step="0.1" min="1" max="5" name="rating" value="4.8" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1" title="Kosongkan jika stok tak terbatas">Stok (Opsional)</label>
                            <input type="number" min="0" name="stock_quantity" placeholder="Tak Terbatas" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Upload File Foto (Otomatis Konversi WebP)</label>
                        <input type="file" name="foto_file" accept="image/webp,image/png,image/jpeg,image/jpg" class="w-full text-xs p-2 rounded-xl border border-neutral-300 bg-neutral-50 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#7A1F2B] file:text-white hover:file:bg-[#611922]">
                        <p class="text-[10px] text-neutral-400 mt-1">Mendukung upload format .webp, .png, .jpg (otomatis diproses & disimpan sebagai .webp)</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Atau URL Foto Alternative</label>
                        <input type="text" name="foto" placeholder="/menu/nasi-padang-rendang.webp" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" placeholder="Rasa dan kelezatan hidangan..." class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="isAddModalOpen = false" class="py-2 px-3.5 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 rounded-xl bg-[#7A1F2B] hover:bg-[#611922] text-white font-semibold shadow-xs transition-colors">
                            Simpan Hidangan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Menu -->
    <div x-show="isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-neutral-900/50 backdrop-blur-xs" @click="isEditModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-5 space-y-4 border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="font-bold text-base text-neutral-900">Edit Hidangan</h3>
                    <button @click="isEditModalOpen = false" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/menu/' + editItem.id" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Nama Hidangan *</label>
                        <input type="text" name="nama" required x-model="editItem.nama" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:ring-1 focus:ring-[#7A1F2B] outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Kategori *</label>
                            <select name="kategori" required x-model="editItem.kategori" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 bg-white outline-none">
                                <option value="daging">Lauk Daging</option>
                                <option value="ayam">Lauk Ayam</option>
                                <option value="ikan">Lauk Ikan</option>
                                <option value="sayur">Sayur & Sambal</option>
                                <option value="minuman">Minuman Tradisional</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Harga (Rp) *</label>
                            <input type="number" name="harga" required x-model="editItem.harga" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Badge</label>
                            <select name="badge" x-model="editItem.badge" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 bg-white outline-none">
                                <option value="">Tanpa Badge</option>
                                <option value="Signature">Signature</option>
                                <option value="Favorit">Favorit</option>
                                <option value="Baru">Baru</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Rating</label>
                            <input type="number" step="0.1" min="1" max="5" name="rating" x-model="editItem.rating" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1" title="Kosongkan jika stok tak terbatas">Stok (Opsional)</label>
                            <input type="number" min="0" name="stock_quantity" x-model="editItem.stock_quantity" placeholder="Tak Terbatas" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Ganti Foto File (Otomatis WebP)</label>
                        <input type="file" name="foto_file" accept="image/webp,image/png,image/jpeg,image/jpg" class="w-full text-xs p-2 rounded-xl border border-neutral-300 bg-neutral-50 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#7A1F2B] file:text-white hover:file:bg-[#611922]">
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Atau Path / URL Foto</label>
                        <input type="text" name="foto" x-model="editItem.foto" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Deskripsi</label>
                        <textarea name="deskripsi" rows="2" x-model="editItem.deskripsi" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="isEditModalOpen = false" class="py-2 px-3.5 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 rounded-xl bg-[#7A1F2B] hover:bg-[#611922] text-white font-semibold shadow-xs transition-colors">
                            Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
