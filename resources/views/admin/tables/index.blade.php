@extends('layouts.admin')

@section('title', 'Manajemen Meja - Admin')
@section('header_title', 'Manajemen Meja & Kapasitas')

@section('content')
<div x-data="{ 
    showAddModal: false, 
    showEditModal: false,
    editData: null,
    branchFilter: '{{ $branchId ?? '' }}',
    filterByBranch() {
        window.location.href = '?branch_id=' + this.branchFilter;
    },
    openEdit(table) {
        this.editData = table;
        this.showEditModal = true;
    }
}">

    <!-- Filter & Add Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        
        <div class="flex items-center space-x-3 w-full sm:w-auto">
            <select x-model="branchFilter" @change="filterByBranch()" class="w-full sm:w-64 px-3 py-2 border-neutral-200 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227] bg-white shadow-sm">
                <option value="">Semua Cabang</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}">{{ $b->nama }}</option>
                @endforeach
            </select>
        </div>

        <button @click="showAddModal = true" class="w-full sm:w-auto bg-[#7A1F2B] hover:bg-[#9A2A38] text-white px-5 py-2.5 rounded-xl font-bold text-sm flex items-center justify-center space-x-2 transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Meja</span>
        </button>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @forelse($tables as $table)
        <div class="bg-white border border-neutral-200 rounded-2xl p-4 flex flex-col items-center justify-center relative group shadow-sm hover:border-[#C9A227] transition-all">
            
            <!-- Actions Dropdown -->
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="text-neutral-400 hover:text-neutral-700 bg-neutral-100 rounded-full p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/></svg>
                </button>
                <div x-show="open" class="absolute right-0 mt-1 w-32 bg-white rounded-xl shadow-lg border border-neutral-100 py-1 z-10" style="display: none;">
                    <button @click="openEdit({{ json_encode($table) }})" class="w-full text-left px-3 py-1.5 text-xs font-medium text-neutral-600 hover:bg-neutral-50 hover:text-[#7A1F2B]">
                        Edit Meja
                    </button>
                    <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Hapus meja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full text-left px-3 py-1.5 text-xs font-medium text-rose-600 hover:bg-rose-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>

            <!-- Table Visual -->
            <div class="w-16 h-16 rounded-full flex items-center justify-center font-bold text-lg mb-2 shadow-inner
                {{ ! $table->is_active ? 'bg-neutral-100 text-neutral-400 border-2 border-dashed border-neutral-300' :
                   ($table->status === 'available' ? 'bg-[#F5EFE2] text-[#7A1F2B] border-2 border-[#C9A227]/30' :
                   ($table->status === 'occupied' ? 'bg-rose-100 text-rose-700 border-2 border-rose-300' : 
                   'bg-amber-100 text-amber-700 border-2 border-amber-300')) }}">
                {{ str_replace('Meja ', '', $table->table_number) }}
            </div>
            
            <span class="text-xs font-semibold text-neutral-700 mb-1 text-center line-clamp-1">{{ $table->table_number }}</span>
            <span class="text-[10px] text-neutral-500 flex items-center justify-center space-x-1 mb-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span>{{ $table->capacity }} Kursi</span>
            </span>

            @if(!$table->is_active)
                <span class="bg-neutral-200 text-neutral-600 text-[9px] font-bold px-2 py-0.5 rounded-full">Nonaktif</span>
            @elseif($table->status === 'available')
                <span class="bg-emerald-100 text-emerald-700 text-[9px] font-bold px-2 py-0.5 rounded-full">Tersedia</span>
            @elseif($table->status === 'occupied')
                <span class="bg-rose-100 text-rose-700 text-[9px] font-bold px-2 py-0.5 rounded-full">Terisi</span>
            @else
                <span class="bg-amber-100 text-amber-700 text-[9px] font-bold px-2 py-0.5 rounded-full">Di-booking</span>
            @endif
        </div>
        @empty
        <div class="col-span-full py-16 text-center text-neutral-400">
            <p>Tidak ada data meja.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6 bg-white rounded-2xl border border-neutral-200/80 overflow-hidden shadow-xs">
        <x-admin-pagination :paginator="$tables" entity="Meja" />
    </div>

    <!-- ADD MODAL -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div x-show="showAddModal" class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden z-10">
            <div class="bg-[#7A1F2B] px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">Tambah Meja</h3>
                <button @click="showAddModal = false" class="text-white/70 hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form action="{{ route('admin.tables.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Pilih Cabang</label>
                        <select name="branch_id" required class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ $branchId == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Nomor/Label Meja</label>
                        <input type="text" name="table_number" required placeholder="Contoh: Meja 01" class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Kapasitas Kursi</label>
                        <input type="number" name="capacity" value="4" min="1" required class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Status Awal</label>
                        <select name="status" required class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                            <option value="available">Tersedia (Kosong)</option>
                            <option value="occupied">Terisi</option>
                            <option value="reserved">Di-booking</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 text-sm font-bold text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#7A1F2B] hover:bg-[#9A2A38] rounded-xl transition-colors shadow-md">Simpan Meja</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div x-show="showEditModal" class="absolute inset-0 bg-neutral-900/50 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden z-10" x-if="editData">
            <div class="bg-neutral-100 px-6 py-4 flex justify-between items-center border-b border-neutral-200">
                <h3 class="text-lg font-bold text-neutral-800">Edit Meja</h3>
                <button @click="showEditModal = false" class="text-neutral-400 hover:text-neutral-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <form :action="`/admin/tables/${editData?.id}`" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Nomor/Label Meja</label>
                        <input type="text" name="table_number" :value="editData?.table_number" required class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Kapasitas Kursi</label>
                        <input type="number" name="capacity" :value="editData?.capacity" min="1" required class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Status Meja</label>
                        <select name="status" x-model="editData.status" required class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                            <option value="available">Tersedia (Kosong)</option>
                            <option value="occupied">Terisi</option>
                            <option value="reserved">Di-booking</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-neutral-700 mb-1">Tersedia untuk digunakan?</label>
                        <select name="is_active" x-model="editData.is_active" class="w-full px-3 py-2 border-neutral-300 rounded-xl text-sm focus:ring-[#C9A227] focus:border-[#C9A227]">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif (Rusak/Renovasi)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="showEditModal = false" class="px-5 py-2.5 text-sm font-bold text-neutral-600 bg-neutral-100 hover:bg-neutral-200 rounded-xl transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-neutral-800 hover:bg-neutral-900 rounded-xl transition-colors shadow-md">Update Data</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
