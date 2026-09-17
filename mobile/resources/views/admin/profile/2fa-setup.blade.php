@extends('layouts.admin')

@section('title', 'Setup 2FA - Raso Mandeh')
@section('header_title', 'Keamanan Dua Langkah (2FA)')

@section('content')
<div class="p-6 lg:p-8 max-w-4xl mx-auto">
    
    <div class="bg-white rounded-2xl shadow-sm border border-neutral-200 overflow-hidden">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center space-x-3 bg-[#F8FAFC]">
            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h2 class="font-bold text-base text-neutral-900">Aktifkan Otentikasi Dua Faktor (2FA)</h2>
        </div>

        <div class="p-6 lg:p-8 space-y-10">
            
            <!-- Step 1 -->
            <div class="flex gap-4">
                <div class="shrink-0 mt-0.5">
                    <div class="w-7 h-7 bg-[#B91C1C] rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm">1</div>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-neutral-900 mb-1.5">Unduh Aplikasi Authenticator</h3>
                    <p class="text-[13px] text-neutral-500 leading-relaxed">
                        Unduh aplikasi Google Authenticator, Microsoft Authenticator, atau Authy di ponsel cerdas Anda dari App Store atau Google Play Store.
                    </p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4">
                <div class="shrink-0 mt-0.5">
                    <div class="w-7 h-7 bg-[#B91C1C] rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm">2</div>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-neutral-900 mb-1.5">Pindai QR Code</h3>
                    <p class="text-[13px] text-neutral-500 leading-relaxed mb-6">
                        Buka aplikasi authenticator Anda, pilih opsi "Scan QR Code" atau "Pindai Kode Batang", lalu arahkan kamera ponsel ke QR Code di bawah ini.
                    </p>

                    <div class="max-w-xs p-6 bg-white border border-neutral-200 border-dashed rounded-2xl mx-auto flex flex-col items-center">
                        <div class="w-48 h-48 bg-white mb-4">
                            <img src="{{ $qrImage }}" alt="QR Code" class="w-full h-full object-contain">
                        </div>
                        <p class="text-[10px] text-neutral-400 font-bold tracking-wider uppercase mb-2">Kunci Manual (Jika QR Gagal)</p>
                        <div class="px-3 py-1.5 bg-[#E2E8F0] rounded-md text-neutral-700 font-mono text-xs font-bold tracking-widest">
                            {{ $secret }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4">
                <div class="shrink-0 mt-0.5">
                    <div class="w-7 h-7 bg-[#B91C1C] rounded-full flex items-center justify-center text-white text-sm font-bold shadow-sm">3</div>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-neutral-900 mb-1.5">Konfirmasi Kode Verifikasi</h3>
                    <p class="text-[13px] text-neutral-500 leading-relaxed mb-4">
                        Masukkan 6 digit kode yang sekarang ditampilkan di aplikasi authenticator Anda untuk mengonfirmasi bahwa penyiapan telah berhasil.
                    </p>

                    @if(session('error'))
                    <div class="mb-4 p-3 bg-rose-50 text-rose-600 text-xs rounded-lg border border-rose-100 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    @endif

                    <form action="{{ route('admin.2fa.confirm') }}" method="POST" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
    @csrf
 @csrf
                        <div class="w-full max-w-[200px]">
                            <input type="text" name="code" maxlength="6" class="w-full px-4 py-3 bg-neutral-50 border border-neutral-200 rounded-lg text-center text-lg tracking-[0.25em] font-mono text-neutral-800 focus:outline-none focus:ring-2 focus:ring-[#B91C1C]/50 focus:border-[#B91C1C] transition-all" placeholder="000000" required autocomplete="off">
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-[#B91C1C] hover:bg-[#991B1B] text-white text-sm font-bold rounded-lg shadow-sm transition-colors">
                            Aktifkan 2FA
                        </button>
                    </form>
                </div>
            </div>

        </div>
        
        <!-- Footer Actions -->
        <div class="px-6 py-4 border-t border-neutral-100 bg-neutral-50 flex justify-end">
            <a href="{{ route('admin.profile') }}" class="px-6 py-2 border border-neutral-300 hover:bg-neutral-100 text-neutral-700 text-sm font-medium rounded-lg transition-colors">
                Batal
            </a>
        </div>
    </div>
</div>
@endsection
