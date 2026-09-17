@extends('layouts.app')

@section('title', 'Cari Pesanan Saya - Raso Mandeh')

@section('content')
<div class="pt-32 pb-20 px-4 min-h-[80vh] flex items-center justify-center" x-data="customerScanner()">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-[#C9A227]/20">
        
        <div class="bg-[#7A1F2B] p-6 text-center text-white relative">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] mix-blend-overlay"></div>
            <div class="relative z-10">
                <svg class="w-12 h-12 mx-auto mb-3 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h2 class="font-serif font-bold text-2xl mb-1">Cek Status Pesanan</h2>
                <p class="text-white/80 text-sm">Lacak pesanan kamu dengan mudah</p>
            </div>
        </div>

        <div class="p-8">
            @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-xl mb-6 text-sm font-medium text-center">
                {{ session('error') }}
            </div>
            @endif

            <div class="flex p-1 bg-neutral-100 rounded-xl mb-6 relative z-10">
                <button @click="mode = 'manual'; stopScanner()" :class="mode === 'manual' ? 'bg-white shadow text-[#7A1F2B]' : 'text-neutral-500 hover:text-neutral-700'" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                    Ketik Manual
                </button>
                <button @click="mode = 'scan'; startScanner()" :class="mode === 'scan' ? 'bg-white shadow text-[#7A1F2B]' : 'text-neutral-500 hover:text-neutral-700'" class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                    Scan QR
                </button>
            </div>

            <form action="{{ route('order.search.submit') }}" method="POST" id="searchForm" class="space-y-6">
    @csrf
 @csrf
                
                <!-- Mode Manual -->
                <div x-show="mode === 'manual'" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-neutral-600 mb-2 uppercase tracking-wider text-center">Masukkan Kode Pesanan</label>
                        <input type="text" name="order_code" id="order_code_input" value="{{ old('order_code') }}" placeholder="Contoh: RM-ALTECG-12092026" autofocus
                               class="w-full px-4 py-3.5 bg-neutral-50 border border-neutral-200 rounded-xl focus:ring-2 focus:ring-[#7A1F2B] focus:border-transparent outline-none transition-all font-mono text-center uppercase text-sm">
                    </div>
                    
                    <button type="submit" class="w-full py-3.5 px-4 bg-[#7A1F2B] hover:bg-[#5a1620] text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center space-x-2">
                        <span>Cari Pesanan</span>
                    </button>
                </div>

                <!-- Mode Scan -->
                <div x-show="mode === 'scan'" style="display: none;" class="space-y-4">
                    <div id="qr-reader" class="w-full bg-neutral-900 rounded-2xl overflow-hidden aspect-square border-4 border-neutral-100 relative"></div>
                    <p class="text-xs text-center text-neutral-500 font-medium">
                        Arahkan kamera ke QR Code pesananmu.
                    </p>
                </div>

            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-xs font-bold text-neutral-400 hover:text-[#7A1F2B] transition-colors">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
        
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('customerScanner', () => ({
        mode: 'manual', // default manual
        html5QrcodeScanner: null,
        
        startScanner() {
            if(this.html5QrcodeScanner) {
                this.html5QrcodeScanner.clear();
                this.html5QrcodeScanner = null;
            }
            
            this.html5QrcodeScanner = new Html5QrcodeScanner(
                "qr-reader", { fps: 10, qrbox: 250, aspectRatio: 1.0 }
            );
            
            this.html5QrcodeScanner.render(
                (decodedText) => {
                    // On success
                    console.log(`Scan result: ${decodedText}`);
                    this.html5QrcodeScanner.clear(); // Stop scanning
                    document.getElementById('order_code_input').value = decodedText;
                    document.getElementById('searchForm').submit(); // Auto submit
                },
                (errorMessage) => {
                    // ignore errors during continuous scanning
                }
            );
        },
        
        stopScanner() {
            if(this.html5QrcodeScanner) {
                this.html5QrcodeScanner.clear();
                this.html5QrcodeScanner = null;
            }
        }
    }));
});
</script>

<style>
/* Override default styling of html5-qrcode */
#qr-reader { border: none !important; }
#qr-reader__scan_region { background-color: #171717; }
#qr-reader__dashboard_section_csr span { color: #fff !important; }
#qr-reader button {
    background-color: #C9A227; color: white; border: none;
    padding: 8px 16px; border-radius: 8px; font-weight: bold;
    cursor: pointer; margin-top: 10px; width: 100%;
}
#qr-reader select { padding: 8px; border-radius: 8px; margin-bottom: 10px; width: 100%; }
</style>
@endsection
