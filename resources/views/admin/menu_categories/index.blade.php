@extends('layouts.admin')

@section('title', 'Kategori Menu - Admin Raso Mandeh')
@section('header_title', 'Kelola Kategori Menu')

@section('content')
<div x-data="{ 
        isAddModalOpen: false, 
        isEditModalOpen: false, 
        editItem: { id: null, nama: '' } 
     }" 
     class="space-y-5">
    
    <!-- Clean Action & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-neutral-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-sm font-bold text-neutral-800">Daftar Kategori Menu</h3>
            <p class="text-xs text-neutral-500">Kelola kategori masakan untuk filter di halaman depan.</p>
        </div>

        <button @click="isAddModalOpen = true" 
                class="bg-[#7A1F2B] hover:bg-[#611922] text-white text-xs font-semibold py-2.5 px-4 rounded-xl shadow-xs flex items-center justify-center space-x-1.5 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left text-xs">
                <thead class="bg-neutral-50/70 border-b border-neutral-200/80 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider font-serif">
                    <tr>
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4 text-center">Jumlah Menu</th>
                        <th class="w-[10%] py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($categories as $category)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="py-3 px-4 font-bold text-neutral-900">
                            {{ $category->nama }}
                        </td>
                        <td class="py-3 px-4 text-neutral-500">
                            {{ $category->slug }}
                        </td>
                        <td class="py-3 px-4 text-center">
                            <span class="bg-neutral-100 text-neutral-700 px-2 py-1 rounded-md font-semibold">
                                {{ $category->items_count }} Menu
                            </span>
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
                                                id: {{ $category->id }},
                                                nama: '{{ addslashes($category->nama) }}'
                                            }; isEditModalOpen = true; openMenu = false" 
                                            class="w-full text-left px-4 py-2 text-xs font-medium text-neutral-700 hover:bg-[#F5EFE2] hover:text-[#7A1F2B] transition-colors">
                                        Edit Kategori
                                    </button>
                                    
                                    <form action="{{ route('admin.menu-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
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
                        <td colspan="4" class="py-10 text-center text-neutral-400">
                            Belum ada kategori menu.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Kategori -->
    <div x-show="isAddModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-neutral-900/50 backdrop-blur-xs" @click="isAddModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-5 space-y-4 border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="font-bold text-base text-neutral-900">Tambah Kategori Menu</h3>
                    <button @click="isAddModalOpen = false" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.menu-categories.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Nama Kategori *</label>
                        <input type="text" name="nama" required placeholder="Contoh: Minuman Spesial" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:ring-1 focus:ring-[#7A1F2B] outline-none">
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="isAddModalOpen = false" class="py-2 px-3.5 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 rounded-xl bg-[#7A1F2B] hover:bg-[#611922] text-white font-semibold shadow-xs transition-colors">
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div x-show="isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-neutral-900/50 backdrop-blur-xs" @click="isEditModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-5 space-y-4 border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="font-bold text-base text-neutral-900">Edit Kategori</h3>
                    <button @click="isEditModalOpen = false" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/menu-categories/' + editItem.id" method="POST" class="space-y-3 text-xs">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Nama Kategori *</label>
                        <input type="text" name="nama" required x-model="editItem.nama" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 focus:ring-1 focus:ring-[#7A1F2B] outline-none">
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
