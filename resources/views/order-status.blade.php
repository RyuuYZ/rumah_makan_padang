@extends('layouts.app')

@section('title', 'Status Pesanan - Raso Mandeh')

@section('content')
<div class="pt-32 pb-20 px-4 min-h-[80vh] flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden border border-[#C9A227]/20">
        <div class="bg-[#7A1F2B] p-6 text-center text-white relative">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] mix-blend-overlay"></div>
            <div class="relative z-10">
                <svg class="w-16 h-16 mx-auto mb-4 text-[#C9A227]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="font-serif font-bold text-2xl mb-1">Pesanan Diterima!</h2>
                <p class="text-white/80 text-sm">Silakan tunjukkan QR ini ke Kasir</p>
            </div>
        </div>

        <div class="p-8 text-center">
            <div class="inline-block p-4 bg-white rounded-2xl shadow-sm border border-neutral-100 mb-6">
                <!-- QR Code generated via local helper -->
                <img src="{!! \App\Helpers\QrCodeHelper::generate($order->qr_code_token, 200) !!}" alt="QR Order" class="w-48 h-48 mx-auto">
            </div>

            <div class="space-y-4 mb-8">
                <div>
                    <p class="text-xs text-neutral-500 font-medium uppercase tracking-wider mb-1">Nomor Pesanan</p>
                    <p class="font-bold text-xl text-[#241B16]">{{ $order->order_number }}</p>
                </div>
                
                <div class="flex items-center justify-between py-3 border-t border-b border-neutral-100">
                    <div class="text-left">
                        <p class="text-xs text-neutral-500 font-medium uppercase tracking-wider mb-1">Status</p>
                        <p class="font-bold text-[#C9A227] capitalize">{{ $order->status }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-neutral-500 font-medium uppercase tracking-wider mb-1">Total Bayar</p>
                        <p class="font-bold text-[#7A1F2B] text-lg">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col space-y-3 mt-4">
                <button type="button" onclick="downloadTicket()" id="downloadBtn"
                        class="block w-full py-3.5 px-4 bg-[#C9A227] hover:bg-[#b38e1e] text-white font-bold rounded-xl shadow-md transition-all flex items-center justify-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Simpan Tiket QR (Download)</span>
                </button>
                <a href="{{ route('home') }}" class="block w-full py-3.5 px-4 bg-neutral-100 hover:bg-neutral-200 text-[#241B16] font-semibold rounded-xl transition-colors">
                    Kembali ke Menu
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Ticket Template for Download -->
<div style="position: absolute; left: -9999px; top: 0;">
    <div id="ticket-download" style="width: 400px; background-color: #ffffff; padding: 30px; font-family: sans-serif; border: 1px solid #eee;">
        <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px dashed #eee; padding-bottom: 20px;">
            <h1 style="color: #7A1F2B; font-size: 24px; font-family: serif; font-weight: bold; margin: 0;">Raso Mandeh</h1>
            <p style="color: #666; font-size: 12px; margin: 5px 0 0 0;">Tiket Pesanan Pelanggan</p>
        </div>
        
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 14px;">
            <div>
                <strong style="display: block; color: #999; font-size: 10px; text-transform: uppercase;">Atas Nama</strong>
                <span style="color: #333; font-weight: bold;">{{ $order->customer_name ?: 'Walk-in' }}</span>
            </div>
            <div style="text-align: right;">
                <strong style="display: block; color: #999; font-size: 10px; text-transform: uppercase;">Meja/Tipe</strong>
                <span style="color: #333; font-weight: bold; text-transform: capitalize;">{{ $order->table_number ?: $order->order_type }}</span>
            </div>
        </div>

        <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
            <p style="font-size: 10px; color: #999; text-transform: uppercase; font-weight: bold; margin: 0 0 5px 0;">Kode Pesanan</p>
            <p style="font-size: 18px; font-weight: bold; color: #333; margin: 0 0 15px 0;">{{ $order->order_number }}</p>
            
            <!-- We load the QR cross-origin cleanly -->
            <img id="ticket-qr-img" src="{!! \App\Helpers\QrCodeHelper::generate($order->qr_code_token, 300) !!}" crossorigin="anonymous" style="width: 200px; height: 200px; margin: 0 auto; display: block;" />
        </div>

        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 14px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
            <div>
                <strong style="color: #333;">Total Item:</strong>
                <span style="color: #666;">{{ $order->items->sum('quantity') }}</span>
            </div>
            <div style="text-align: right;">
                <strong style="color: #333;">Total Harga:</strong>
                <strong style="color: #7A1F2B;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div style="text-align: center; font-size: 10px; color: #999;">
            <p style="margin: 0;">Dicetak: {{ now()->format('d M Y, H:i') }}</p>
            <p style="margin: 5px 0 0 0;">Harap tunjukkan tiket ini ke kasir.</p>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadTicket() {
    const btn = document.getElementById('downloadBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="animate-pulse">Menyiapkan Tiket...</span>';
    btn.disabled = true;

    const ticketElement = document.getElementById('ticket-download');
    const qrImg = document.getElementById('ticket-qr-img');

    // Make sure image is fully loaded before drawing
    if (qrImg.complete) {
        generateCanvas(ticketElement, btn, originalText);
    } else {
        qrImg.onload = () => generateCanvas(ticketElement, btn, originalText);
    }
}

function generateCanvas(ticketElement, btn, originalText) {
    html2canvas(ticketElement, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'Tiket-Pesanan-{{ $order->order_number }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        
        btn.innerHTML = originalText;
        btn.disabled = false;
    }).catch(err => {
        console.error(err);
        alert('Gagal membuat tiket gambar. Pastikan koneksi stabil.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>
@endsection
