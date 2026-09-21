@extends('layouts.admin')

@section('title', 'Dashboard - Admin Raso Mandeh')
@section('header_title', 'Ringkasan Restoran')

@section('content')
<div class="space-y-6">
    
    <!-- 4 Clean Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-neutral-500 block">Total Pendapatan</span>
                <h3 class="text-xl font-bold text-neutral-900 tracking-tight mt-1 whitespace-nowrap">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
                <span class="text-[11px] font-medium text-emerald-600 mt-1 inline-block">Transaksi Berhasil</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-neutral-100 text-neutral-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-[#7A1F2B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-neutral-500 block">Total Pesanan</span>
                <h3 class="text-2xl font-bold text-neutral-900 tracking-tight mt-1">
                    {{ $totalOrders }}
                </h3>
                <span class="text-[11px] font-medium text-neutral-500 mt-1 inline-block">Semua Cabang</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-neutral-100 text-neutral-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-neutral-500 block">Perlu Diproses</span>
                <h3 class="text-2xl font-bold text-amber-600 tracking-tight mt-1">
                    {{ $pendingOrders }}
                </h3>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="text-[11px] font-medium text-[#7A1F2B] hover:underline mt-1 inline-block">
                    Lihat antrian &rarr;
                </a>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Active Menu Items -->
        <div class="bg-white p-5 rounded-2xl border border-neutral-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-neutral-500 block">Menu Aktif</span>
                <h3 class="text-2xl font-bold text-neutral-900 tracking-tight mt-1">
                    {{ $totalMenuItems }}
                </h3>
                <span class="text-[11px] font-medium text-neutral-500 mt-1 inline-block">{{ $totalBranches }} Cabang Aktif</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-neutral-100 text-neutral-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>

    </div>

    <!-- Clean Status Pipeline -->
    <div class="bg-white p-5 rounded-2xl border border-neutral-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-3.5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-500">Pipeline Status Pesanan</h3>
            <span class="text-xs text-neutral-400 font-normal">Klik untuk memfilter pesanan</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5">
            
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="p-3 rounded-xl bg-amber-50/70 border border-amber-200/70 text-center hover:bg-amber-100/60 transition-colors">
                <span class="text-[11px] font-semibold text-amber-800 uppercase tracking-wider block">Pending</span>
                <span class="text-xl font-bold text-amber-900 mt-0.5 block">{{ $statusCounts['pending'] }}</span>
                <span class="text-[10px] text-amber-700">Baru Masuk</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'confirmed']) }}" class="p-3 rounded-xl bg-blue-50/70 border border-blue-200/70 text-center hover:bg-blue-100/60 transition-colors">
                <span class="text-[11px] font-semibold text-blue-800 uppercase tracking-wider block">Confirmed</span>
                <span class="text-xl font-bold text-blue-900 mt-0.5 block">{{ $statusCounts['confirmed'] }}</span>
                <span class="text-[10px] text-blue-700">Dikonfirmasi</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'process']) }}" class="p-3 rounded-xl bg-orange-50/70 border border-orange-200/70 text-center hover:bg-orange-100/60 transition-colors">
                <span class="text-[11px] font-semibold text-orange-800 uppercase tracking-wider block">Process</span>
                <span class="text-xl font-bold text-orange-900 mt-0.5 block">{{ $statusCounts['process'] }}</span>
                <span class="text-[10px] text-orange-700">Sedang Diproses</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'ready']) }}" class="p-3 rounded-xl bg-purple-50/70 border border-purple-200/70 text-center hover:bg-purple-100/60 transition-colors">
                <span class="text-[11px] font-semibold text-purple-800 uppercase tracking-wider block">Ready</span>
                <span class="text-xl font-bold text-purple-900 mt-0.5 block">{{ $statusCounts['ready'] }}</span>
                <span class="text-[10px] text-purple-700">Siap Saji</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200/70 text-center hover:bg-emerald-100/60 transition-colors">
                <span class="text-[11px] font-semibold text-emerald-800 uppercase tracking-wider block">Completed</span>
                <span class="text-xl font-bold text-emerald-900 mt-0.5 block">{{ $statusCounts['completed'] }}</span>
                <span class="text-[10px] text-emerald-700">Selesai</span>
            </a>

            <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="p-3 rounded-xl bg-rose-50/70 border border-rose-200/70 text-center hover:bg-rose-100/60 transition-colors">
                <span class="text-[11px] font-semibold text-rose-800 uppercase tracking-wider block">Cancelled</span>
                <span class="text-xl font-bold text-rose-900 mt-0.5 block">{{ $statusCounts['cancelled'] }}</span>
                <span class="text-[10px] text-rose-700">Batal</span>
            </a>

        </div>
    </div>

    <!-- 2 Column Section: Recent Orders & Top Dishes -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Recent Orders (8 cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-neutral-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-neutral-200/80 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-neutral-900">Pesanan Masuk Terbaru</h3>
                    <p class="text-xs text-neutral-500">Daftar transaksi pesanan terakhir</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-[#7A1F2B] hover:underline">
                    Semua Pesanan &rarr;
                </a>
            </div>

            <div class="w-full">
                <table class="w-full text-left text-xs table-fixed">
                    <thead>
                        <tr class="bg-neutral-50/70 border-b border-neutral-200/80 text-[11px] font-semibold text-neutral-500 uppercase tracking-wider font-serif">
                            <th class="w-[40%] py-3 px-4">ID & Pelanggan</th>
                            <th class="w-[20%] py-3 px-4">Cabang</th>
                            <th class="w-[20%] py-3 px-4">Total</th>
                            <th class="w-[20%] py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-neutral-50/50 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-neutral-900">#{{ $order->id }}</span>
                                <span class="text-neutral-700 font-medium"> - {{ $order->customer_name ?? 'Walk-in' }}</span>
                                <span class="text-[10px] text-neutral-400 block mt-0.5">{{ $order->created_at->diffForHumans() }} ({{ ucfirst($order->method) }})</span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-neutral-700 truncate">
                                {{ $order->branch->kota ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-[#7A1F2B] whitespace-nowrap text-sm">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border
                                    @if($order->status === 'pending') bg-amber-50 text-amber-700 border-amber-200
                                    @elseif($order->status === 'confirmed') bg-blue-50 text-blue-700 border-blue-200
                                    @elseif($order->status === 'process') bg-orange-50 text-orange-700 border-orange-200
                                    @elseif($order->status === 'ready') bg-purple-50 text-purple-700 border-purple-200
                                    @elseif($order->status === 'completed') bg-emerald-50 text-emerald-700 border-emerald-200
                                    @elseif($order->status === 'cancelled') bg-rose-50 text-rose-700 border-rose-200
                                    @else bg-neutral-50 text-neutral-700 border-neutral-200 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-neutral-400">Belum ada pesanan masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Dishes & Quick Actions (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Top Dishes Widget -->
            <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-3.5">Hidangan Terlaris</h3>
                <div class="space-y-3">
                    @forelse($topDishes as $top)
                    <div class="flex items-center space-x-3">
                        <img src="{{ $top->menuItem->foto ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100' }}" 
                             alt="{{ $top->menuItem->nama ?? '-' }}" 
                             class="w-10 h-10 rounded-xl object-cover flex-shrink-0 bg-neutral-100">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-xs text-neutral-900 truncate">{{ $top->menuItem->nama ?? '-' }}</h4>
                            <span class="text-[10px] text-neutral-400 capitalize">{{ $top->menuItem->kategori ?? '-' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-neutral-800">{{ $top->total_qty }} porsi</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-neutral-400 text-center py-4">Belum ada data penjualan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Action Card -->
            <div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs p-5 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-neutral-500">Aksi Cepat</h4>
                <div class="space-y-2">
                    <a href="{{ route('admin.menu.index') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-neutral-200 hover:border-neutral-300 hover:bg-neutral-50 text-xs font-semibold text-neutral-800 transition-all">
                        <span>+ Tambah Menu Baru</span>
                        <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    <a href="{{ route('admin.branches.index') }}" class="flex items-center justify-between p-2.5 rounded-xl border border-neutral-200 hover:border-neutral-300 hover:bg-neutral-50 text-xs font-semibold text-neutral-800 transition-all">
                        <span>Kelola Jam Cabang</span>
                        <svg class="w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
