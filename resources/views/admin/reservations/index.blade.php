@extends('layouts.admin')

@section('title', 'Manajemen Reservasi - Admin Raso Mandeh')
@section('header_title', 'Daftar Reservasi Pelanggan')

@section('content')
<div class="space-y-5">
    
    <!-- Clean Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-neutral-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.reservations.index') }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors
               {{ !request('status') ? 'bg-[#7A1F2B] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                Semua
            </a>
            <a href="{{ route('admin.reservations.index', ['status' => 'pending']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors
               {{ request('status') === 'pending' ? 'bg-[#7A1F2B] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.reservations.index', ['status' => 'confirmed']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors
               {{ request('status') === 'confirmed' ? 'bg-[#7A1F2B] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                Terkonfirmasi
            </a>
            <a href="{{ route('admin.reservations.index', ['status' => 'completed']) }}" 
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors
               {{ request('status') === 'completed' ? 'bg-[#7A1F2B] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200' }}">
                Selesai
            </a>
        </div>

        <span class="text-xs text-neutral-400">
            Total: <strong class="text-neutral-800">{{ $reservations->total() }}</strong> reservasi
        </span>
    </div>

    <!-- Reservations Table -->
    <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left text-xs table-fixed">
                <thead class="bg-neutral-50/70 border-b border-neutral-200/80 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider font-serif">
                    <tr>
                        <th class="w-[20%] py-3 px-4">Waktu Reservasi</th>
                        <th class="w-[25%] py-3 px-4">Pelanggan</th>
                        <th class="w-[20%] py-3 px-4">Cabang & Meja</th>
                        <th class="w-[15%] py-3 px-4">Tamu</th>
                        <th class="w-[12%] py-3 px-4">Status</th>
                        <th class="w-[8%] py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($reservations as $reservation)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="py-3 px-4">
                            <span class="font-bold text-neutral-900 block">{{ $reservation->reservation_time->format('d M Y') }}</span>
                            <span class="text-[10px] text-[#7A1F2B] font-semibold block">{{ $reservation->reservation_time->format('H:i') }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-neutral-900 block truncate">{{ $reservation->customer_name }}</span>
                            <span class="text-[10px] text-neutral-400 block">{{ $reservation->customer_phone }}</span>
                            @if($reservation->notes)
                            <span class="text-[10px] text-neutral-500 block truncate mt-1">Catatan: {{ $reservation->notes }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-neutral-600 font-medium truncate">
                            <span class="block">{{ $reservation->branch->kota ?? '-' }}</span>
                            @if($reservation->table)
                            <span class="text-[10px] font-bold text-neutral-800">Meja: {{ $reservation->table->table_number }}</span>
                            @else
                            <span class="text-[10px] text-amber-600">Belum di-assign meja</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="font-bold text-neutral-800">{{ $reservation->guest_count }}</span> Orang
                        </td>
                        <td class="py-3 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold
                                @if($reservation->status === 'confirmed') bg-blue-50 text-blue-700 border-blue-200/60
                                @elseif($reservation->status === 'completed') bg-emerald-50 text-emerald-700 border-emerald-200/60
                                @elseif($reservation->status === 'cancelled') bg-rose-50 text-rose-700 border-rose-200/60
                                @else bg-amber-50 text-amber-700 border-amber-200/60 @endif">
                                {{ ucfirst($reservation->status) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">
                            <div x-data="{ openMenu: false }" class="inline-block text-left relative">
                                <button @click="openMenu = !openMenu" @click.away="openMenu = false" 
                                        class="p-1.5 rounded-xl text-neutral-400 hover:text-[#7A1F2B] hover:bg-[#F5EFE2] transition-colors focus:outline-none">
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
                                     
                                    @if($reservation->status === 'pending')
                                    <form action="{{ route('admin.reservations.updateStatus', $reservation->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-blue-600 hover:bg-blue-50 transition-colors">
                                            Konfirmasi
                                        </button>
                                    </form>
                                    @endif

                                    @if($reservation->status === 'confirmed')
                                    <form action="{{ route('admin.reservations.updateStatus', $reservation->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-emerald-600 hover:bg-emerald-50 transition-colors">
                                            Selesai
                                        </button>
                                    </form>
                                    @endif

                                    @if(in_array($reservation->status, ['pending', 'confirmed']))
                                    <form action="{{ route('admin.reservations.updateStatus', $reservation->id) }}" method="POST" onsubmit="return confirm('Batalkan reservasi ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-amber-600 hover:bg-amber-50 transition-colors">
                                            Batalkan
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('admin.reservations.destroy', $reservation->id) }}" method="POST" onsubmit="return confirm('Hapus reservasi ini secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                            Hapus Data
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-neutral-400">
                            Tidak ada data reservasi ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-admin-pagination :paginator="$reservations" entity="Reservasi" />
    </div>

</div>
@endsection
