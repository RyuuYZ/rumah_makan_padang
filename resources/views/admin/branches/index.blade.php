@extends('layouts.admin')

@section('title', 'Cabang Restoran - Admin Raso Mandeh')
@section('header_title', 'Kelola Cabang Restoran')

@section('content')
<div x-data="{ 
        isAddModalOpen: false, 
        isEditModalOpen: false, 
        editBranch: { id: null, nama: '', kota: '', alamat: '', jam_buka: '09:00 - 22:00', kontak_whatsapp: '' } 
     }" 
     class="space-y-5">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-neutral-200/80 shadow-xs">
        <div>
            <h3 class="text-sm font-bold text-neutral-900">Daftar Cabang Aktif</h3>
            <p class="text-xs text-neutral-500">Kelola operasional, alamat, dan nomor kontak WhatsApp cabang</p>
        </div>
        <button @click="isAddModalOpen = true" 
                class="bg-[#7A1F2B] hover:bg-[#611922] text-white text-xs font-semibold py-2 px-4 rounded-xl shadow-xs flex items-center justify-center space-x-1.5 transition-colors whitespace-nowrap">
            <svg class="w-4 h-4 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Tambah Cabang Baru</span>
        </button>
    </div>

    <!-- Clean Branches Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($branches as $branch)
        <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-neutral-300 transition-colors">
            <div>
                <div class="flex items-start justify-between gap-2 mb-2">
                    {{ $branch->alamat }}
                </p>

                <div class="space-y-1.5 pt-2 border-t border-neutral-100 text-xs text-neutral-600">
                    <div class="flex items-center space-x-2">
                        <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $branch->jam_buka }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>WA: <strong class="text-neutral-800">{{ $branch->kontak_whatsapp ?? '-' }}</strong></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mt-3 pt-3 border-t border-neutral-100 text-center">
                    <div class="p-2 bg-neutral-50 rounded-xl">
                        <span class="text-[10px] text-neutral-400 uppercase font-semibold block">Pesanan</span>
                        <strong class="text-xs text-neutral-900">{{ $branch->orders_count }}</strong>
                    </div>
                    <div class="p-2 bg-neutral-50 rounded-xl">
                        <span class="text-[10px] text-neutral-400 uppercase font-semibold block">Ulasan</span>
                        <strong class="text-xs text-neutral-900">{{ $branch->reviews_count }}</strong>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-neutral-100 flex items-center justify-end space-x-2">
                <button @click="editBranch = {
                            id: {{ $branch->id }},
                            nama: '{{ addslashes($branch->nama) }}',
                            kota: '{{ addslashes($branch->kota) }}',
                            alamat: '{{ addslashes($branch->alamat) }}',
                            jam_buka: '{{ addslashes($branch->jam_buka) }}',
                            kontak_whatsapp: '{{ $branch->kontak_whatsapp }}'
                        }; isEditModalOpen = true"
                        class="text-xs font-semibold py-1.5 px-3 rounded-lg bg-neutral-100 hover:bg-neutral-200 text-neutral-700 transition-colors">
                    Edit Data
                </button>
                <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Hapus cabang ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-semibold py-1.5 px-3 rounded-lg text-neutral-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Tambah Cabang -->
    <div x-show="isAddModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-neutral-900/50 backdrop-blur-xs" @click="isAddModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-5 space-y-4 border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="font-bold text-base text-neutral-900">Tambah Cabang Baru</h3>
                    <button @click="isAddModalOpen = false" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.branches.store') }}" method="POST" class="space-y-3 text-xs">
    @csrf
 @csrf
                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Nama Cabang *</label>
                        <input type="text" name="nama" required placeholder="Raso Mandeh - Yogyakarta" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Kota *</label>
                            <input type="text" name="kota" required placeholder="Yogyakarta" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Jam Operasional *</label>
                            <input type="text" name="jam_buka" required value="09:00 - 22:00" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">WhatsApp Hotline *</label>
                        <input type="text" name="kontak_whatsapp" placeholder="6281234567890" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Alamat Lengkap *</label>
                        <textarea name="alamat" rows="2" required placeholder="Alamat jalan lengkap cabang..." class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex justify-end space-x-2">
                        <button type="button" @click="isAddModalOpen = false" class="py-2 px-3.5 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-semibold transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="py-2 px-4 rounded-xl bg-[#7A1F2B] hover:bg-[#611922] text-white font-semibold shadow-xs transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Cabang -->
    <div x-show="isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-neutral-900/50 backdrop-blur-xs" @click="isEditModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-5 space-y-4 border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="font-bold text-base text-neutral-900">Edit Data Cabang</h3>
                    <button @click="isEditModalOpen = false" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form :action="'/admin/branches/' + editBranch.id" method="POST" class="space-y-3 text-xs">
    @csrf
 @csrf
                    @method('PUT')
                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Nama Cabang *</label>
                        <input type="text" name="nama" required x-model="editBranch.nama" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Kota *</label>
                            <input type="text" name="kota" required x-model="editBranch.kota" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-neutral-700 mb-1">Jam Operasional *</label>
                            <input type="text" name="jam_buka" required x-model="editBranch.jam_buka" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">WhatsApp Hotline *</label>
                        <input type="text" name="kontak_whatsapp" x-model="editBranch.kontak_whatsapp" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-neutral-700 mb-1">Alamat Lengkap *</label>
                        <textarea name="alamat" rows="2" required x-model="editBranch.alamat" class="w-full text-xs p-2.5 rounded-xl border border-neutral-300 outline-none"></textarea>
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
