@extends('layouts.admin')

@section('title', 'POS Kasir & Scanner - Raso Mandeh')

@section('header', 'POS Kasir & Scanner')

@section('content')
<div x-data="posScanner()" class="min-h-[70vh]">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Kolom Kiri: Scanner & Input -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Pilihan Input -->
            <div class="bg-white p-6 rounded-3xl border border-[#C9A227]/20 shadow-sm relative overflow-hidden">
                <h3 class="font-serif font-bold text-lg text-[#7A1F2B] mb-4 relative z-10">Cari Pesanan</h3>
                
                <div class="flex p-1 bg-neutral-100 rounded-xl mb-6 relative z-10">
                    <button @click="mode = 'scan'; startScanner()" :class="mode === 'scan' ? 'bg-white shadow text-[#7A1F2B]' : 'text-neutral-500 hover:text-neutral-700'" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                        Scan QR Kamera
                    </button>
                    <button @click="mode = 'manual'; stopScanner()" :class="mode === 'manual' ? 'bg-white shadow text-[#7A1F2B]' : 'text-neutral-500 hover:text-neutral-700'" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                        Ketik Manual
                    </button>
                </div>

                <!-- Mode Scan QR -->
                <div x-show="mode === 'scan'" class="space-y-4 relative z-10">
                    <div id="qr-reader" class="w-full bg-neutral-900 rounded-2xl overflow-hidden aspect-square border-4 border-neutral-100 relative">
                        <!-- Pustaka html5-qrcode akan me-render UI di sini -->
                    </div>
                    <p class="text-xs text-center text-neutral-500 font-medium">
                        Arahkan kamera ke QR Code pesanan pelanggan. Pastikan pencahayaan cukup.
                    </p>
                </div>

                <!-- Mode Manual -->
                <div x-show="mode === 'manual'" style="display: none;" class="space-y-4 relative z-10">
                    <div>
                        <label class="block text-xs font-bold text-neutral-600 mb-2 uppercase tracking-wider">Kode / Nomor Pesanan</label>
                        <div class="flex space-x-2">
                            <input type="text" x-model="manualCode" @keyup.enter="findOrder(manualCode)" placeholder="Contoh: RM-ALTECG-12092026" class="flex-1 px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#7A1F2B] focus:border-transparent outline-none transition-all font-mono text-sm uppercase">
                            <button @click="findOrder(manualCode)" :disabled="loading || !manualCode" class="px-6 py-3 bg-[#7A1F2B] hover:bg-[#5a1620] disabled:opacity-50 text-white font-bold rounded-xl transition-colors">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Kolom Kanan: Hasil Pencarian / Detail Pesanan -->
        <div class="lg:col-span-7">
            <div class="bg-white h-full p-6 lg:p-8 rounded-3xl border border-[#C9A227]/20 shadow-[0_4px_24px_rgba(201,162,39,0.06)] relative overflow-hidden flex flex-col">
                
                <!-- State: Empty / Belum ada data -->
                <div x-show="!orderData && !loading" class="flex-1 flex flex-col items-center justify-center text-neutral-400 py-12">
                    <div class="w-24 h-24 bg-neutral-50 rounded-full flex items-center justify-center mb-4 border border-neutral-100">
                        <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="font-medium text-sm">Menunggu input pesanan...</p>
                    <p class="text-xs mt-1 text-center max-w-xs">Scan QR dari pelanggan atau masukkan kode secara manual untuk memproses pesanan dan pembayaran.</p>
                </div>

                <!-- State: Loading -->
                <div x-show="loading" class="flex-1 flex flex-col items-center justify-center text-[#7A1F2B] py-12" style="display: none;">
                    <svg class="animate-spin -ml-1 mr-3 h-10 w-10 text-[#7A1F2B]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-4 font-bold text-sm animate-pulse">Mencari pesanan...</p>
                </div>

                <!-- State: Error -->
                <div x-show="error" class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl mb-4 text-sm font-medium flex items-start space-x-3" style="display: none;">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-text="error"></span>
                </div>

                <!-- State: Order Found -->
                <div x-show="orderData && !loading" style="display: none;" class="flex-1 flex flex-col relative z-10">
                    <!-- Order Header -->
                    <div class="flex justify-between items-start mb-6 pb-6 border-b border-neutral-100">
                        <div>
                            <h2 class="font-serif font-bold text-2xl text-[#241B16] mb-1" x-text="orderData?.order_number"></h2>
                            <div class="flex space-x-3 text-xs font-medium text-neutral-500">
                                <span x-text="orderData?.created_at"></span>
                                <span>&bull;</span>
                                <span x-text="orderData?.branch?.name"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold capitalize"
                                  :class="{
                                      'bg-amber-100 text-amber-800': orderData?.status === 'pending',
                                      'bg-blue-100 text-blue-800': orderData?.status === 'cooking',
                                      'bg-indigo-100 text-indigo-800': orderData?.status === 'ready',
                                      'bg-emerald-100 text-emerald-800': orderData?.status === 'confirmed' || orderData?.status === 'completed',
                                      'bg-rose-100 text-rose-800': orderData?.status === 'cancelled'
                                  }" x-text="orderData?.status"></span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="grid grid-cols-2 gap-4 mb-6 bg-neutral-50 p-4 rounded-2xl border border-neutral-100">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-neutral-500 font-bold mb-1">Pelanggan</p>
                            <p class="font-semibold text-sm text-[#241B16]" x-text="orderData?.customer_name"></p>
                            <p class="text-xs text-neutral-500 mt-0.5" x-text="orderData?.customer_phone"></p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-neutral-500 font-bold mb-1">Metode & Meja</p>
                            <p class="font-semibold text-sm text-[#241B16] uppercase" x-text="orderData?.service_type"></p>
                            <p class="text-xs text-neutral-500 mt-0.5">Meja: <span x-text="orderData?.table_number"></span></p>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="flex-1 overflow-y-auto mb-6 pr-2 scrollbar-hide">
                        <p class="text-[10px] uppercase tracking-widest text-[#C9A227] font-bold mb-3 font-serif">Rincian Menu</p>
                        <div class="space-y-3">
                            <template x-for="item in orderData?.items" :key="item.id">
                                <div class="flex justify-between items-start pb-3 border-b border-neutral-50 last:border-0 last:pb-0">
                                    <div class="flex-1 pr-4">
                                        <div class="flex items-start space-x-2">
                                            <span class="font-bold text-sm text-[#7A1F2B] mt-0.5" x-text="item.quantity + 'x'"></span>
                                            <div>
                                                <p class="font-semibold text-sm text-[#241B16]" x-text="item.name"></p>
                                                <p class="text-xs text-neutral-400 mt-0.5" x-text="item.price_formatted"></p>
                                                <template x-if="item.notes">
                                                    <p class="text-[11px] text-neutral-500 bg-neutral-100 p-1.5 rounded mt-1.5 italic" x-text="'Catatan: ' + item.notes"></p>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-sm text-[#241B16]" x-text="item.subtotal_formatted"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Total & Action -->
                    <div class="pt-5 border-t border-dashed border-neutral-200 mt-auto">
                        <div class="flex justify-between items-center mb-6">
                            <p class="text-sm font-bold text-neutral-500 uppercase tracking-wider">Total Tagihan</p>
                            <p class="font-serif font-black text-3xl text-[#7A1F2B]" x-text="orderData?.total_formatted"></p>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button @click="orderData = null; manualCode = ''; error = ''; startScanner()" class="px-6 py-3.5 bg-neutral-100 hover:bg-neutral-200 text-[#241B16] font-bold rounded-xl transition-colors text-sm">
                                Batal / Pindai Lain
                            </button>
                            <div class="flex-1" x-show="orderData?.status === 'pending' || orderData?.status === 'cooking' || orderData?.status === 'ready' || orderData?.status === 'confirmed'">
                                <button type="button" 
                                        @click="completeOrder()" 
                                        :disabled="completing"
                                        class="w-full py-3.5 px-4 bg-[#7A1F2B] hover:bg-[#5a1620] disabled:opacity-60 text-white font-bold rounded-xl shadow-lg transition-all text-sm flex items-center justify-center space-x-2">
                                    <svg x-show="!completing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg x-show="completing" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;" x-cloak>
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span x-text="completing ? 'Memproses...' : 'Tandai Selesai (Lunas)'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Load html5-qrcode library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('posScanner', () => ({
        mode: 'scan', // 'scan' or 'manual'
        manualCode: '',
        orderData: null,
        loading: false,
        error: '',
        html5QrcodeScanner: null,
        
        init() {
            // Start scanner on load if in scan mode
            if(this.mode === 'scan') {
                setTimeout(() => {
                    this.startScanner();
                }, 500); // Wait for DOM to be ready
            }
        },
        
        startScanner() {
            if(this.html5QrcodeScanner) {
                this.html5QrcodeScanner.clear();
                this.html5QrcodeScanner = null;
            }
            
            this.html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", { fps: 10, qrbox: 250, aspectRatio: 1.0 }
            );
            
            this.html5QrcodeScanner.render(
                (decodedText, decodedResult) => {
                    // On success
                    console.log(`Scan result: ${decodedText}`);
                    this.html5QrcodeScanner.clear(); // Stop scanning once found
                    this.findOrder(decodedText);
                },
                (errorMessage) => {
                    // On error/fail - usually ignored as it scans continuously
                }
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
            
            this.loading = true;
            this.error = '';
            this.orderData = null;
            
            // Call API
            fetch('{{ route('admin.pos.findOrder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ order_code: code })
            })
            .then(response => response.json())
            .then(data => {
                this.loading = false;
                if(data.success) {
                    this.orderData = data.order;
                    this.stopScanner(); // Ensure scanner stops if we got data from manual
                } else {
                    this.error = data.message || 'Gagal menemukan pesanan.';
                    if(this.mode === 'scan') this.startScanner(); // Restart scanner
                }
            })
            .catch(error => {
                this.loading = false;
                this.error = 'Terjadi kesalahan jaringan.';
                console.error('Error:', error);
                if(this.mode === 'scan') this.startScanner();
            });
        },
        
        completeOrder() {
            if (!this.orderData || this.completing) return;
            this.completing = true;
            this.error = '';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch(`/admin/orders/${this.orderData.id}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ status: 'completed' })
            })
            .then(r => r.json())
            .then(data => {
                this.completing = false;
                if (data.success) {
                    this.orderData.status = 'completed';
                    if (window.showToast) {
                        window.showToast(data.message || 'Pesanan berhasil diselesaikan!', 'success');
                    }
                    window.dispatchEvent(new CustomEvent('order-status-updated', {
                        detail: { orderId: this.orderData.id, status: 'completed', pendingCount: data.pending_count }
                    }));
                } else {
                    this.error = data.message || 'Gagal menyelesaikan pesanan.';
                    if (window.showToast) {
                        window.showToast(data.message || 'Gagal menyelesaikan pesanan.', 'error');
                    }
                }
            })
            .catch(err => {
                this.completing = false;
                this.error = 'Terjadi kesalahan jaringan.';
                if (window.showToast) {
                    window.showToast('Terjadi kesalahan jaringan.', 'error');
                }
            });
        }
    }));
});
</script>

<style>
/* Override default styling of html5-qrcode */
#qr-reader {
    border: none !important;
}
#qr-reader__scan_region {
    background-color: #171717;
}
#qr-reader__dashboard_section_csr span {
    color: #fff !important;
}
#qr-reader button {
    background-color: #C9A227;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}
#qr-reader select {
    padding: 8px;
    border-radius: 8px;
    margin-bottom: 10px;
    width: 100%;
}
</style>
@endsection
