<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir POS - Raso Mandeh</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .kasir-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
        .kasir-scroll::-webkit-scrollbar-track { background: transparent; }
        .kasir-scroll::-webkit-scrollbar-thumb { background: #e5e5e5; border-radius: 10px; }
        .kasir-scroll::-webkit-scrollbar-thumb:hover { background: #d4d4d4; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-neutral-100 text-neutral-800 antialiased overflow-hidden">

    <!-- MAIN WRAPPER TERKUNCI 100% -->
    <div x-data="kasirApp()" class="fixed inset-0 flex w-full h-full overflow-hidden">
        
        <!-- KOLOM KIRI: MENU -->
        <div class="flex-1 flex flex-col h-full min-w-0 bg-neutral-50 z-10 shadow-sm relative overflow-hidden">
            
            <!-- Header -->
            <div class="h-16 bg-white border-b border-neutral-200 flex items-center justify-between px-5 flex-shrink-0 z-20">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="w-9 h-9 flex items-center justify-center text-neutral-500 hover:text-[#7A1F2B] hover:bg-rose-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div class="h-5 w-px bg-neutral-300"></div>
                    <h1 class="font-black text-lg text-[#7A1F2B] tracking-tight uppercase">Point of Sale</h1>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="relative w-64 lg:w-80 hidden md:block">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" x-model="searchQuery" placeholder="Cari hidangan..." class="w-full pl-9 pr-4 py-2 bg-neutral-100 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#C9A227] focus:ring-2 focus:ring-[#C9A227]/20 outline-none transition-all">
                    </div>
                    
                    <!-- Profil Kasir -->
                    <div class="flex items-center space-x-3 p-1 rounded-[20px] bg-transparent transition-all max-w-[220px]">
                        <div class="w-10 h-10 rounded-[16px] overflow-hidden bg-gradient-to-br from-[#7A1F2B] to-[#9A2A38] text-[#C9A227] font-serif font-bold flex items-center justify-center flex-shrink-0 text-lg shadow-sm">
                            @if(auth()->user()->profile_photo_url)
                                <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full object-cover" alt="Profile">
                            @else
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            @endif
                        </div>
                        <div class="min-w-0 flex-1 hidden md:block text-left pr-3">
                            <p class="text-[13px] font-black text-[#1E293B] truncate leading-tight">{{ auth()->user()->name ?? 'Administrator' }}</p>
                            <p class="text-[11px] font-medium text-[#64748B] truncate leading-tight">Terminal Kasir</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white border-b border-neutral-200 px-5 py-3 flex-shrink-0 z-10 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">
                <div class="flex space-x-2 overflow-x-auto kasir-scroll pb-1">
                    <button @click="activeCategory = 'all'" 
                            :class="activeCategory === 'all' ? 'bg-[#7A1F2B] text-white border-[#7A1F2B]' : 'bg-white text-neutral-600 border-neutral-200 hover:border-neutral-300 hover:bg-neutral-50'" 
                            class="px-4 py-1.5 rounded-lg border text-sm font-semibold whitespace-nowrap transition-colors shadow-sm">
                        Semua Menu
                    </button>
                    @php
                        $categories = $menuItems->pluck('kategori')->unique();
                    @endphp
                    @foreach($categories as $cat)
                        <button @click="activeCategory = '{{ $cat }}'" 
                                :class="activeCategory === '{{ $cat }}' ? 'bg-[#7A1F2B] text-white border-[#7A1F2B]' : 'bg-white text-neutral-600 border-neutral-200 hover:border-neutral-300 hover:bg-neutral-50'" 
                                class="px-4 py-1.5 rounded-lg border text-sm font-semibold whitespace-nowrap transition-colors capitalize shadow-sm">
                            {{ str_replace('-', ' ', $cat) }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Menu Grid -->
            <!-- SCROLL KIRI DIKUNCI -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-5 kasir-scroll min-h-0 relative">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-4 pb-20">
                    <template x-for="item in filteredMenus" :key="item.id">
                        <div @click="addToCart(item)" class="bg-white rounded-xl shadow-sm border border-neutral-200 overflow-hidden cursor-pointer hover:shadow-md hover:border-[#C9A227] transition-all flex flex-col group">
                            <div class="h-32 bg-neutral-100 relative overflow-hidden flex-shrink-0">
                                <img :src="item.foto || 'https://ui-avatars.com/api/?name=' + item.nama + '&background=F5EFE2&color=7A1F2B'" :alt="item.nama" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div x-show="item.stock_quantity !== null && item.stock_quantity <= 0" class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center z-10">
                                    <span class="bg-neutral-800 text-white font-bold text-[10px] px-2 py-1 rounded">HABIS</span>
                                </div>
                                <div x-show="item.stock_quantity !== null && item.stock_quantity > 0 && item.stock_quantity <= 10" class="absolute bottom-1 right-1 z-10">
                                    <span class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow-sm">
                                        Sisa <span x-text="item.stock_quantity"></span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="p-3 flex flex-col flex-1">
                                <h3 class="font-semibold text-xs text-neutral-800 leading-tight mb-2 group-hover:text-[#7A1F2B] transition-colors line-clamp-2" x-text="item.nama"></h3>
                                <div class="mt-auto flex items-center justify-between">
                                    <span class="text-[#7A1F2B] font-bold text-sm" x-text="formatRupiah(item.display_price)"></span>
                                    <div class="w-6 h-6 rounded-md bg-neutral-100 flex items-center justify-center text-neutral-500 group-hover:bg-[#C9A227] group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="filteredMenus.length === 0" class="flex flex-col items-center justify-center text-neutral-400 bg-transparent py-20" x-cloak>
                    <svg class="w-16 h-16 text-neutral-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <p class="font-medium text-neutral-600">Menu tidak ditemukan</p>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: CART (STRICT HEIGHT LIMIT) -->
        <div class="w-[340px] xl:w-[380px] flex-shrink-0 bg-white shadow-[0_0_30px_rgba(0,0,0,0.05)] border-l border-neutral-200 flex flex-col h-full overflow-hidden relative z-20">
            
            <!-- Tabs (Tinggi Fixed) -->
            <div class="flex-shrink-0 p-3 bg-white border-b border-neutral-100 z-10">
                <div class="flex bg-neutral-100 p-1 rounded-lg">
                    <button @click="mode = 'walkin'" :class="mode === 'walkin' ? 'bg-white shadow-sm text-[#7A1F2B]' : 'text-neutral-500 hover:text-neutral-700'" class="flex-1 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center justify-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span>Walk-in</span>
                    </button>
                    <button @click="mode = 'scan'" :class="mode === 'scan' ? 'bg-white shadow-sm text-[#7A1F2B]' : 'text-neutral-500 hover:text-neutral-700'" class="flex-1 py-1.5 text-xs font-semibold rounded-md transition-all flex items-center justify-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        <span>Scan Web QR</span>
                    </button>
                </div>
            </div>

            <!-- AREA DINAMIS KANAN -->
            <div class="flex-1 flex flex-col min-h-0 bg-neutral-50 overflow-hidden relative">
                
                <!-- SCAN MODE WRAPPER -->
                <div x-show="mode === 'scan'" class="flex flex-col h-full w-full overflow-hidden" style="display: none;" x-cloak>
                    <div class="flex-1 overflow-y-auto p-4 flex flex-col space-y-4 kasir-scroll min-h-0">
                        <div class="bg-neutral-50 p-3 rounded-xl border border-neutral-200">
                            <div id="qr-reader" class="w-full bg-black rounded-lg overflow-hidden shadow-inner relative min-h-[220px] flex items-center justify-center"></div>
                            <div class="mt-3 flex space-x-2">
                                <input type="text" x-model="manualCode" @keyup.enter="findOrder(manualCode)" placeholder="Ketik kode..." class="flex-1 px-3 py-2 bg-white border border-neutral-300 rounded-lg text-sm outline-none">
                                <button @click="findOrder(manualCode)" class="px-4 py-2 bg-[#241B16] text-white rounded-lg text-sm font-semibold hover:bg-black transition-colors">Cari</button>
                            </div>
                        </div>

                        <div x-show="scannedOrder" class="bg-white border border-neutral-200 shadow-sm p-4 rounded-xl flex flex-col flex-1" style="display:none;" x-cloak>
                            <!-- Detail Pesanan Web QR Sama Persis Seperti Sebelumnya... -->
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-neutral-800 font-mono text-sm" x-text="scannedOrder?.order_number"></span>
                                <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold uppercase" x-text="scannedOrder?.status"></span>
                            </div>
                            
                            <div class="text-sm text-neutral-600 mb-4 pb-4 border-b border-neutral-100 space-y-1">
                                <div class="flex justify-between"><span class="text-neutral-400">Pelanggan</span> <span class="font-medium text-neutral-800" x-text="scannedOrder?.customer_name"></span></div>
                                <div class="flex justify-between"><span class="text-neutral-400">Tipe</span> <span class="font-medium text-neutral-800 capitalize" x-text="scannedOrder?.service_type"></span></div>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto kasir-scroll space-y-3 mb-4 pr-1 min-h-[100px]">
                                <template x-for="item in scannedOrder?.items" :key="item.id">
                                    <div class="flex justify-between text-sm">
                                        <div class="flex space-x-2">
                                            <span class="font-semibold text-neutral-400" x-text="item.quantity + 'x'"></span> 
                                            <span class="font-medium text-neutral-800" x-text="item.name"></span>
                                        </div>
                                        <div class="font-semibold text-neutral-800" x-text="item.subtotal_formatted"></div>
                                    </div>
                                </template>
                            </div>
                            
                            <div class="mt-auto border-t border-dashed border-neutral-200 pt-3 flex justify-between items-center mb-4">
                                <span class="font-bold text-neutral-500 uppercase text-[10px]">Total</span>
                                <span class="font-bold text-lg text-[#7A1F2B]" x-text="scannedOrder?.total_formatted"></span>
                            </div>
                            
                            <button @click="payScannedOrder()" :disabled="paying || scannedOrder?.status === 'completed'" class="w-full py-2.5 bg-[#7A1F2B] hover:bg-[#5a1620] disabled:bg-neutral-300 disabled:text-neutral-500 text-white font-semibold rounded-lg shadow-sm transition-colors flex items-center justify-center space-x-2 text-sm">
                                <span x-text="scannedOrder?.status === 'completed' ? 'Sudah Lunas' : 'Konfirmasi & Selesai'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- WALKIN MODE WRAPPER -->
                <div x-show="mode === 'walkin'" class="flex flex-col h-full w-full overflow-hidden" x-cloak>
                    
                    <!-- Customer Form (Fixed Top) -->
                    <div class="flex-shrink-0 p-3 bg-white border-b border-neutral-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)] z-20">
                        <div class="space-y-2">
                            <input type="text" x-model="walkin.customer_name" placeholder="Nama Pelanggan (Wajib)" class="w-full text-sm px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg focus:bg-white focus:border-[#C9A227] focus:ring-1 focus:ring-[#C9A227] outline-none">
                            
                            <div class="flex space-x-2">
                                <select x-model="walkin.service_type" class="flex-1 text-sm px-2 py-2 bg-neutral-50 border border-neutral-200 rounded-lg focus:bg-white focus:border-[#C9A227] focus:ring-1 focus:ring-[#C9A227] outline-none">
                                    <option value="dine-in">Dine In (Makan Sini)</option>
                                    <option value="takeaway">Takeaway (Bungkus)</option>
                                </select>
                                
                                <button x-show="walkin.service_type === 'dine-in'" @click="showTableModal = true" type="button" 
                                    class="w-24 text-sm px-2 py-2 bg-neutral-50 border border-neutral-200 rounded-lg outline-none text-center font-semibold hover:border-[#C9A227] hover:bg-white transition-colors truncate"
                                    :class="{'text-[#7A1F2B] bg-[#F5EFE2] border-[#C9A227]/30': walkin.table_number}">
                                    <span x-text="walkin.table_number ? walkin.table_number : 'Pilih Meja'"></span>
                                </button>
                            </div>
                            
                            <select x-model="walkin.branch_id" class="w-full text-sm px-2 py-2 bg-neutral-50 border border-neutral-200 rounded-lg focus:bg-white focus:border-[#C9A227] focus:ring-1 focus:ring-[#C9A227] outline-none">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Cart Items List (Scrollable) -->
                    <!-- INI BAGIAN YANG AKAN BISA DI-SCROLL PADA KANAN -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-2 kasir-scroll min-h-0 bg-neutral-50">
                        <template x-if="cart.length === 0">
                            <div class="flex flex-col items-center justify-center text-neutral-400 py-10">
                                <svg class="w-12 h-12 mb-2 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                <p class="text-xs font-semibold">Belum ada pesanan</p>
                            </div>
                        </template>
                        
                        <template x-for="item in cart" :key="item.id">
                            <div class="bg-white p-2.5 rounded-xl border border-neutral-200 shadow-sm flex items-center justify-between overflow-hidden">
                                <div class="flex-1 min-w-0 pr-2">
                                    <h4 class="font-semibold text-xs text-neutral-800 truncate mb-0.5" x-text="item.nama"></h4>
                                    <div class="text-[#C9A227] text-[11px] font-bold" x-text="formatRupiah(item.display_price)"></div>
                                </div>
                                <div class="flex items-center space-x-1 bg-neutral-50 rounded-lg p-0.5 border border-neutral-100 z-10">
                                    <button @click="updateCart(item.id, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-neutral-500 hover:text-rose-600 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>
                                    <span class="w-6 text-center text-xs font-bold text-neutral-800" x-text="item.quantity"></span>
                                    <button @click="updateCart(item.id, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded shadow-sm text-neutral-500 hover:text-[#7A1F2B] transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Checkout Footer (Fixed Bottom) -->
                    <div class="flex-shrink-0 bg-white border-t border-neutral-200 p-4 shadow-[0_-5px_15px_rgba(0,0,0,0.03)] z-20">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider">Total Item</span>
                            <span class="text-xs font-semibold text-neutral-800" x-text="cart.reduce((total, item) => total + item.quantity, 0) + ' Porsi'"></span>
                        </div>
                        <div class="flex justify-between items-end mb-3 border-b border-neutral-100 pb-3">
                            <span class="text-[10px] font-bold text-neutral-500 uppercase tracking-wider mb-0.5">Total Bayar</span>
                            <span class="text-2xl font-bold text-[#7A1F2B]" x-text="formatRupiah(cartTotal)"></span>
                        </div>
                        
                        <button @click="checkoutWalkin()" :disabled="cart.length === 0 || !walkin.customer_name || paying" class="w-full py-3 bg-[#7A1F2B] hover:bg-[#5a1620] disabled:bg-neutral-300 disabled:text-neutral-400 text-white font-semibold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2 text-sm">
                            <svg x-show="paying" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span x-text="paying ? 'Memproses...' : 'Proses & Bayar'"></span>
                        </button>
                        
                        <div class="text-center mt-2 h-4">
                            <button @click="cart = []" x-show="cart.length > 0 && !paying" class="text-[11px] font-semibold text-neutral-400 hover:text-rose-500 transition-colors underline">
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Global Success Overlay -->
        <div x-show="successMessage" class="absolute inset-0 z-50 flex items-center justify-center" style="display: none;" x-cloak>
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            
            <!-- Modal Card -->
            <div class="relative bg-white p-8 rounded-2xl shadow-2xl flex flex-col items-center text-center max-w-sm w-full mx-4 border border-neutral-100 transform transition-all">
                <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-5 ring-8 ring-emerald-50">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h3 class="font-black text-2xl text-neutral-900 mb-2 font-serif" x-text="successMessage"></h3>
                <p class="text-sm text-neutral-500 mb-8">Transaksi telah berhasil dicatat ke dalam sistem dan berstatus Lunas.</p>
                <button @click="resetAll()" class="px-6 py-3.5 bg-[#7A1F2B] hover:bg-[#9A2A38] text-white rounded-xl font-bold shadow-lg shadow-[#7A1F2B]/30 transition-all w-full text-sm">Buat Pesanan Baru</button>
            </div>
        </div>

        <!-- Table Selection Modal -->
        <div x-show="showTableModal" class="absolute inset-0 z-50 flex items-center justify-center" style="display: none;" x-cloak>
            <div x-show="showTableModal" @click="showTableModal = false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            
            <div class="relative bg-neutral-50 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] mx-4 flex flex-col overflow-hidden z-10 border border-neutral-200">
                <div class="px-5 py-4 bg-white border-b border-neutral-200 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="font-bold text-lg text-neutral-800">Pilih Meja</h3>
                        <p class="text-xs text-neutral-500">Cabang yang dipilih akan menentukan meja yang tersedia.</p>
                    </div>
                    <button @click="showTableModal = false" class="p-2 text-neutral-400 hover:text-neutral-700 bg-neutral-50 hover:bg-neutral-100 rounded-full transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <!-- Table Grid -->
                <div class="p-5 overflow-y-auto kasir-scroll grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    <template x-for="table in branchTables" :key="table.id">
                        <button 
                            @click="table.status === 'available' ? selectTable(table.table_number) : null"
                            :class="{
                                'bg-[#F5EFE2] border-[#C9A227]/40 hover:bg-[#ebdaba] hover:border-[#C9A227] hover:shadow-md cursor-pointer': table.status === 'available',
                                'bg-rose-100 border-rose-300 opacity-60 cursor-not-allowed': table.status === 'occupied',
                                'bg-amber-100 border-amber-300 opacity-70 cursor-not-allowed': table.status === 'reserved',
                                'ring-2 ring-offset-2 ring-[#7A1F2B]': walkin.table_number === table.table_number
                            }"
                            class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 transition-all relative aspect-square group">
                            
                            <!-- Checkmark if selected -->
                            <div x-show="walkin.table_number === table.table_number" class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-[#7A1F2B] text-white rounded-full flex items-center justify-center shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>

                            <span class="font-bold text-lg mb-1" :class="{
                                'text-[#7A1F2B]': table.status === 'available',
                                'text-rose-700': table.status === 'occupied',
                                'text-amber-700': table.status === 'reserved'
                            }" x-text="table.table_number.replace('Meja ', '')"></span>
                            
                            <span class="text-[9px] font-semibold uppercase px-1.5 py-0.5 rounded text-white" :class="{
                                'bg-[#C9A227]': table.status === 'available',
                                'bg-rose-500': table.status === 'occupied',
                                'bg-amber-500': table.status === 'reserved'
                            }" x-text="table.status === 'available' ? 'Kosong' : (table.status === 'occupied' ? 'Terisi' : 'Booking')"></span>
                            
                            <span class="mt-1 text-[10px] font-medium text-neutral-500 opacity-80" x-text="table.capacity + ' Kursi'"></span>
                        </button>
                    </template>

                    <div x-show="branchTables.length === 0" class="col-span-full py-10 text-center text-neutral-400">
                        <p class="text-sm">Tidak ada meja di cabang ini.</p>
                    </div>
                </div>
                
                <div class="px-5 py-3 bg-white border-t border-neutral-200 shrink-0 flex items-center justify-between">
                    <div class="flex space-x-3 text-xs text-neutral-500">
                        <div class="flex items-center space-x-1"><div class="w-3 h-3 rounded-sm bg-[#F5EFE2] border border-[#C9A227]/40"></div><span>Kosong</span></div>
                        <div class="flex items-center space-x-1"><div class="w-3 h-3 rounded-sm bg-rose-100 border border-rose-300"></div><span>Terisi</span></div>
                        <div class="flex items-center space-x-1"><div class="w-3 h-3 rounded-sm bg-amber-100 border border-amber-300"></div><span>Booking</span></div>
                    </div>
                    <button @click="selectTable('')" class="text-xs font-bold text-neutral-500 hover:text-rose-600 transition-colors underline">Clear Pilihan</button>
                </div>
            </div>
        </div>

    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        const allMenuItems = @json($menuItems ?? []);
        const allTables = @json($tables ?? []);
        const defaultBranchId = {{ $branches->first()->id ?? 1 }};
        
        Alpine.data('kasirApp', () => ({
            menus: allMenuItems,
            allTables: allTables,
            showTableModal: false,
            activeCategory: 'all',
            searchQuery: '',
            mode: 'walkin',
            
            cart: [],
            walkin: {
                customer_name: '',
                service_type: 'dine-in',
                table_number: '',
                branch_id: defaultBranchId
            },
            
            html5QrcodeScanner: null,
            manualCode: '',
            scannedOrder: null,
            paying: false,
            successMessage: '',
            
            init() {
                this.$watch('mode', value => {
                    if(value === 'scan') {
                        setTimeout(() => this.startScanner(), 300);
                    } else {
                        this.stopScanner();
                    }
                });
            },
            
            get filteredMenus() {
                return this.menus.filter(item => {
                    const matchCategory = this.activeCategory === 'all' || item.kategori === this.activeCategory;
                    const matchSearch = item.nama.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return matchCategory && matchSearch;
                });
            },

            get branchTables() {
                return this.allTables.filter(t => t.branch_id == this.walkin.branch_id);
            },
            
            selectTable(tableNumber) {
                this.walkin.table_number = tableNumber;
                this.showTableModal = false;
            },
            
            get cartTotal() {
                return this.cart.reduce((total, item) => total + (item.display_price * item.quantity), 0);
            },
            
            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            },
            
            addToCart(menu) {
                if(menu.stock_quantity !== null && menu.stock_quantity <= 0) {
                    alert('Stok habis!');
                    return;
                }
                const existing = this.cart.find(item => item.id === menu.id);
                if (existing) {
                    if (menu.stock_quantity !== null && existing.quantity >= menu.stock_quantity) {
                        alert('Melebihi sisa stok!');
                        return;
                    }
                    existing.quantity++;
                } else {
                    this.cart.unshift({
                        id: menu.id,
                        nama: menu.nama,
                        display_price: menu.display_price,
                        quantity: 1
                    });
                }
            },
            
            updateCart(id, change) {
                const index = this.cart.findIndex(item => item.id === id);
                if (index !== -1) {
                    const newQty = this.cart[index].quantity + change;
                    if (newQty <= 0) {
                        this.cart.splice(index, 1);
                    } else {
                        const menu = this.menus.find(m => m.id === id);
                        if (menu && menu.stock_quantity !== null && newQty > menu.stock_quantity) {
                            alert('Melebihi sisa stok!');
                            return;
                        }
                        this.cart[index].quantity = newQty;
                    }
                }
            },
            
            checkoutWalkin() {
                if(this.cart.length === 0 || !this.walkin.customer_name) return;
                this.paying = true;
                
                const payload = {
                    customer_name: this.walkin.customer_name,
                    customer_phone: 'Walk-in Kasir',
                    order_type: this.walkin.service_type === 'takeaway' ? 'takeaway' : 'dine_in',
                    service_type: this.walkin.service_type,
                    table_number: this.walkin.service_type === 'dine-in' ? this.walkin.table_number : null,
                    notes: 'Diproses dari kasir (Walk-in)',
                    branch_id: this.walkin.branch_id || 1,
                    source: 'pos',
                    payment_status: 'paid',
                    items: this.cart.map(item => ({
                        menu_item_id: item.id,
                        quantity: item.quantity,
                        notes: ''
                    }))
                };
                
                fetch('{{ route('orders.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(async res => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data.success) {
                        throw new Error(data.message || 'Gagal mengirim pesanan');
                    }
                    return data.data;
                })
                .then(order => {
                    this.paying = false;
                    this.cart = [];
                    this.walkin.customer_name = '';
                    this.walkin.table_number = '';
                    this.successMessage = `Pembayaran Berhasil! Pesanan #${order.order_number || order.id} telah tercatat.`;
                    setTimeout(() => { this.successMessage = ''; }, 4000);
                })
                .catch(err => {
                    this.paying = false;
                    alert('Gagal: ' + (err.message || 'Terjadi kesalahan sistem.'));
                });
            },
            
            startScanner() {
                if(this.html5QrcodeScanner) return;
                if(typeof Html5QrcodeScanner === 'undefined') {
                    alert('Tunggu sebentar, modul scanner sedang dimuat...');
                    return;
                }
                this.html5QrcodeScanner = new Html5QrcodeScanner(
                    "qr-reader", { fps: 10, qrbox: 250, aspectRatio: 1.0 }
                );
                this.html5QrcodeScanner.render(
                    (decodedText, decodedResult) => {
                        this.html5QrcodeScanner.clear();
                        this.findOrder(decodedText);
                    },
                    (errorMessage) => {}
                );
            },
            
            stopScanner() {
                if(this.html5QrcodeScanner) {
                    this.html5QrcodeScanner.clear();
                    this.html5QrcodeScanner = null;
                }
            },
            
            findOrder(code) {
                if(!code) return;
                fetch('{{ route('admin.pos.findOrder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_code: code })
                })
                .then(r => r.json())
                .then(data => {
                    if(data.success) {
                        this.scannedOrder = data.order;
                        this.stopScanner();
                    } else {
                        alert(data.message);
                        this.startScanner();
                    }
                })
                .catch(e => {
                    alert('Error jaringan atau kode tidak ditemukan');
                    this.startScanner();
                });
            },
            
            payScannedOrder() {
                if(!this.scannedOrder) return;
                this.paying = true;
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('status', 'completed');

                fetch('/kasir/orders/' + this.scannedOrder.id + '/status', {
                    method: 'POST',
                    body: formData
                })
                .then(r => r.json())
                .then(() => {
                    this.paying = false;
                    this.successMessage = 'Pembayaran QR Web Berhasil!';
                })
                .catch(e => {
                    this.paying = false;
                    alert('Gagal menyelesaikan pembayaran.');
                });
            },
            
            resetAll() {
                this.successMessage = '';
                this.cart = [];
                this.walkin.customer_name = '';
                this.walkin.table_number = '';
                this.scannedOrder = null;
                this.manualCode = '';
                if(this.mode === 'scan') {
                    this.startScanner();
                }
            }
        }));
    });
    </script>
</body>
</html>
