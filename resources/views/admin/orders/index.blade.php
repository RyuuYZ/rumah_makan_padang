@extends('layouts.admin')

@section('title', 'Pesanan Masuk - Admin Raso Mandeh')
@section('header_title', 'Kelola Pesanan Masuk')

@section('content')
<div x-data="{ selectedOrder: null, isDetailModalOpen: false }" class="space-y-5">
    
    <!-- Clean Filter Bar -->
    <div class="bg-white p-5 rounded-3xl border border-[#C9A227]/20 shadow-[0_4px_20px_rgba(201,162,39,0.05)] relative overflow-hidden">
        <div class="absolute inset-0 opacity-5 bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] pointer-events-none"></div>
        <form method="GET" action="{{ route('admin.orders.index') }}" class="relative z-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-neutral-500 mb-1 uppercase tracking-wider">Status Pesanan</label>
                <select name="status" class="w-full text-xs py-2 px-3 rounded-xl border border-neutral-300 bg-white font-medium focus:ring-1 focus:ring-[#7A1F2B] outline-none">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Order::STATUSES as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                        {{ ucfirst($st) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-neutral-500 mb-1 uppercase tracking-wider">Cabang Restoran</label>
                <select name="branch_id" class="w-full text-xs py-2 px-3 rounded-xl border border-neutral-300 bg-white font-medium focus:ring-1 focus:ring-[#7A1F2B] outline-none">
                    <option value="">Semua Cabang</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                        {{ $b->kota }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-neutral-500 mb-1 uppercase tracking-wider">Metode Layanan</label>
                <select name="method" class="w-full text-xs py-2 px-3 rounded-xl border border-neutral-300 bg-white font-medium focus:ring-1 focus:ring-[#7A1F2B] outline-none">
                    <option value="">Semua Metode</option>
                    <option value="dine-in" {{ request('method') === 'dine-in' ? 'selected' : '' }}>Makan di Tempat (Dine-in)</option>
                    <option value="delivery" {{ request('method') === 'delivery' ? 'selected' : '' }}>Pesan Antar (Delivery)</option>
                    <option value="online" {{ request('method') === 'online' ? 'selected' : '' }}>Online Pickup</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full bg-[#7A1F2B] hover:bg-[#611922] text-white text-xs font-semibold py-2 px-4 rounded-xl shadow-xs transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.orders.index') }}" class="py-2 px-3 rounded-xl bg-neutral-100 hover:bg-neutral-200 text-neutral-600 text-xs font-semibold transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-[#C9A227]/20 shadow-[0_4px_20px_rgba(201,162,39,0.05)] overflow-hidden">
        <div class="w-full">
            <table class="w-full text-left text-xs table-fixed">
                <thead class="bg-[#F5EFE2]/50 border-b border-[#C9A227]/20 text-[11px] font-bold text-[#7A1F2B] uppercase tracking-wider font-serif">
                    <tr>
                        <th class="w-[12%] py-3.5 px-3">ID & Kode</th>
                        <th class="w-[14%] py-3.5 px-3">Pelanggan</th>
                        <th class="w-[14%] py-3.5 px-3">Cabang & Metode</th>
                        <th class="w-[26%] py-3.5 px-3">Menu Dipesan</th>
                        <th class="w-[13%] py-3.5 px-3">Total Tagihan</th>
                        <th class="w-[15%] py-3.5 px-3">Status Pesanan</th>
                        <th class="w-[6%] py-3.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-neutral-50/50 transition-colors">
                        <td class="py-3 px-3">
                            <span class="font-bold text-neutral-900">#{{ $order->id }}</span>
                            <span class="text-xs font-mono text-[#7A1F2B] font-bold block mt-1 px-1.5 py-0.5 bg-[#7A1F2B]/10 rounded inline-block" title="Kode Pesanan">{{ $order->order_number }}</span>
                            <span class="text-[10px] text-neutral-400 block mt-1">{{ $order->created_at->format('d M, H:i') }}</span>
                        </td>
                        <td class="py-3 px-3">
                            <span class="font-semibold text-neutral-900 block truncate">{{ $order->customer_name ?? 'Walk-in' }}</span>
                            <span class="text-[10px] text-neutral-400 block truncate">{{ $order->customer_phone ?? '-' }}</span>
                        </td>
                        <td class="py-3 px-3">
                            <span class="font-medium text-neutral-800 block truncate">{{ $order->branch->kota ?? '-' }}</span>
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase mt-0.5
                                @if($order->method === 'dine-in') bg-purple-50 text-purple-700
                                @elseif($order->method === 'delivery') bg-blue-50 text-blue-700
                                @else bg-teal-50 text-teal-700 @endif">
                                {{ $order->method }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <p class="truncate text-neutral-600 font-normal" title="@foreach($order->items as $it){{ $it->quantity }}x {{ $it->menuItem->nama ?? 'Item' }}{{ !$loop->last ? ', ' : '' }}@endforeach">
                                @foreach($order->items as $idx => $it)
                                    {{ $it->quantity }}x {{ $it->menuItem->nama ?? 'Item' }}{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            </p>
                            @if($order->notes)
                            <span class="text-[10px] text-[#7A1F2B] font-medium block truncate mt-0.5" title="{{ $order->notes }}">
                                Catatan: {{ $order->notes }}
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-3 font-bold text-[#7A1F2B] text-sm whitespace-nowrap">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3" x-data="orderStatusRow({{ $order->id }}, '{{ $order->status }}')">
                            <div class="relative inline-flex items-center w-full max-w-[130px]">
                                <select 
                                    x-model="status" 
                                    @change="updateStatus($event.target.value)" 
                                    class="w-full text-[11px] font-bold py-1.5 pl-2.5 pr-7 rounded-full border cursor-pointer outline-none shadow-2xs transition-all disabled:opacity-60"
                                    :class="statusClasses[status] || 'bg-neutral-50 text-neutral-700 border-neutral-200'">
                                    @foreach(\App\Models\Order::STATUSES as $st)
                                        <option value="{{ $st }}">{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                                <div x-show="saving" class="absolute right-2 pointer-events-none" style="display: none;" x-cloak>
                                    <svg class="animate-spin h-3.5 w-3.5 text-current opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 text-right whitespace-nowrap relative">
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
                                     
                                    <button @click="selectedOrder = {{ json_encode($order) }}; isDetailModalOpen = true; openMenu = false" 
                                            class="w-full text-left px-4 py-2 text-xs font-medium text-neutral-700 hover:bg-[#F5EFE2] hover:text-[#7A1F2B] transition-colors">
                                        Lihat Rincian
                                    </button>
                                    
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus pesanan #{{ $order->id }}?')">
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
                        <td colspan="7" class="py-10 text-center text-neutral-400">
                            Tidak ditemukan pesanan dengan filter tersebut.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-admin-pagination :paginator="$orders" entity="Pesanan" />
    </div>

    <!-- Clean Detail Order Modal -->
    <div x-show="isDetailModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-cloak>
        <div class="fixed inset-0 bg-neutral-900/50 backdrop-blur-xs" @click="isDetailModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl p-5 space-y-4 border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 pb-3">
                    <h3 class="font-bold text-base text-neutral-900 flex items-center gap-2">
                        Rincian Pesanan #<span x-text="selectedOrder?.id"></span>
                        <span class="text-[11px] font-mono bg-neutral-100 px-2 py-0.5 rounded text-neutral-500 border border-neutral-200" title="Kode Pesanan" x-text="selectedOrder?.order_number"></span>
                    </h3>
                    <button @click="isDetailModalOpen = false" class="text-neutral-400 hover:text-neutral-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <template x-if="selectedOrder">
                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-2 bg-neutral-50 p-3 rounded-xl border border-neutral-100">
                            <div>
                                <span class="text-neutral-400 block text-[10px] uppercase font-semibold">Pelanggan</span>
                                <strong class="text-neutral-800" x-text="selectedOrder.customer_name || 'Walk-in'"></strong>
                            </div>
                            <div>
                                <span class="text-neutral-400 block text-[10px] uppercase font-semibold">Telepon</span>
                                <strong class="text-neutral-800" x-text="selectedOrder.customer_phone || '-'"></strong>
                            </div>
                            <div class="mt-2">
                                <span class="text-neutral-400 block text-[10px] uppercase font-semibold">Cabang</span>
                                <strong class="text-neutral-800" x-text="selectedOrder.branch?.kota || '-'"></strong>
                            </div>
                            <div class="mt-2">
                                <span class="text-neutral-400 block text-[10px] uppercase font-semibold">Metode</span>
                                <strong class="uppercase text-neutral-800" x-text="selectedOrder.method"></strong>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block mb-2">Item Masakan</span>
                            <div class="space-y-1.5 max-h-48 overflow-y-auto">
                                <template x-for="item in selectedOrder.items" :key="item.id">
                                    <div class="flex justify-between items-center py-1.5 px-2.5 bg-neutral-50 rounded-lg">
                                        <span class="font-medium text-neutral-800" x-text="item.quantity + 'x ' + (item.menu_item?.nama || 'Item')"></span>
                                        <span class="font-semibold text-neutral-900" x-text="'Rp ' + Number(item.price * item.quantity).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="border-t border-neutral-100 pt-3 flex justify-between items-center">
                            <span class="text-xs text-neutral-500 font-medium">Total Pembayaran:</span>
                            <span class="text-lg font-bold text-[#7A1F2B]" x-text="'Rp ' + Number(selectedOrder.total).toLocaleString('id-ID')"></span>
                        </div>

                        <template x-if="selectedOrder.notes">
                            <div class="p-2.5 bg-amber-50 rounded-xl border border-amber-200/60 text-amber-800 text-[11px]">
                                <strong>Catatan:</strong> <span x-text="selectedOrder.notes"></span>
                            </div>
                        </template>
                    </div>
                </template>

                <div class="pt-2">
                    <button @click="isDetailModalOpen = false" class="w-full bg-neutral-100 hover:bg-neutral-200 text-neutral-700 py-2.5 rounded-xl font-semibold text-xs transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        function initOrderStatus() {
            if (typeof Alpine !== 'undefined' && !Alpine._orderStatusRegistered) {
                Alpine._orderStatusRegistered = true;
                Alpine.data('orderStatusRow', (orderId, initialStatus) => ({
                    orderId: orderId,
                    status: initialStatus,
                    previousStatus: initialStatus,
                    saving: false,
                    statusClasses: {
                        'pending': 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
                        'confirmed': 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
                        'process': 'bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-100',
                        'ready': 'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
                        'completed': 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100',
                        'cancelled': 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100'
                    },
                    async updateStatus(newStatus) {
                        if (this.saving) return;
                        this.saving = true;
                        const targetStatus = newStatus;
                        try {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                            const res = await fetch(`/admin/orders/${this.orderId}/status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token
                                },
                                body: JSON.stringify({ status: targetStatus })
                            });
                            const data = await res.json();
                            if (res.ok && data.success) {
                                this.status = targetStatus;
                                this.previousStatus = targetStatus;
                                if (window.showToast) {
                                    window.showToast(data.message || 'Status pesanan berhasil diperbarui!', 'success');
                                }
                                window.dispatchEvent(new CustomEvent('order-status-updated', {
                                    detail: { orderId: this.orderId, status: targetStatus, pendingCount: data.pending_count }
                                }));
                            } else {
                                throw new Error(data.message || 'Gagal memperbarui status');
                            }
                        } catch (err) {
                            console.error('Error updating order status:', err);
                            this.status = this.previousStatus;
                            if (window.showToast) {
                                window.showToast(err.message || 'Gagal memperbarui status pesanan', 'error');
                            }
                        } finally {
                            this.saving = false;
                        }
                    }
                }));
            }
        }

        if (window.Alpine) {
            initOrderStatus();
        } else {
            document.addEventListener('alpine:init', initOrderStatus);
        }
    })();
    </script>
</div>
@endsection
