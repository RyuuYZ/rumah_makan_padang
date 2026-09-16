<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Khusus Admin - Raso Mandeh</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍛</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-neutral-100 font-sans antialiased min-h-screen flex items-center justify-center relative overflow-hidden p-4">

    <!-- Blurred Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="/dapur-raso-mandeh.webp" alt="Dapur Raso Mandeh" class="w-full h-full object-cover object-center scale-105" style="filter: brightness(0.6);">
        <div class="absolute inset-0 bg-neutral-900/40 backdrop-blur-sm"></div>
    </div>

    <!-- Main Split Card -->
    <div class="relative z-10 w-full max-w-[900px] bg-white shadow-2xl flex overflow-hidden rounded">
        
        <!-- Left Side: Branding (Red) -->
        <div class="hidden md:flex md:w-[45%] bg-[#7A1F2B] py-16 px-10 flex-col items-center justify-center text-center text-white relative">
            <div class="w-28 h-28 bg-white rounded-full flex items-center justify-center mb-6 shadow-lg transform hover:scale-105 transition-transform">
                <span class="text-[#7A1F2B] font-serif font-black text-4xl tracking-tighter">RM</span>
            </div>
            
            <h1 class="font-bold text-2xl tracking-tight mb-8">
                Rumah Makan<br>Raso Mandeh
            </h1>
            
            <div class="w-10 h-[2px] bg-white/40 mb-8"></div>
            
            <p class="text-[13px] text-white/90 font-medium mb-4">
                Sistem Informasi Manajemen Terpadu
            </p>
            <p class="text-[11px] text-white/75 leading-relaxed">
                Melayani anggota dan pelanggan<br>dengan prinsip transparansi & kekeluargaan
            </p>
        </div>

        <!-- Right Side: Form (White) -->
        <div class="w-full md:w-[55%] p-10 sm:p-14 flex flex-col justify-center bg-white">
            <div class="mb-8">
                <h2 class="text-xl font-bold text-neutral-900 tracking-tight mb-1.5">Masuk ke Sistem</h2>
                <p class="text-xs text-neutral-500">Silakan login menggunakan akun Anda</p>
            </div>

            <!-- Flash Alert -->
            @if(session('success'))
            <div class="mb-4 p-3 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3 rounded bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                @foreach($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
            @endif

            <div x-data="loginScanner()">
                <!-- State: Form Login Normal -->
                <div x-show="mode === 'form'" class="space-y-4">
                    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
    @csrf
 @csrf
                        
                        <div>
                            <label class="block text-[11px] font-semibold text-neutral-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="email" name="email" value="{{ old('email', 'admin@rasomandeh.com') }}" required autofocus class="w-full pl-9 pr-3 py-2 border border-neutral-300 rounded focus:border-[#7A1F2B] focus:ring-1 focus:ring-[#7A1F2B] outline-none text-xs transition-colors" placeholder="nama@rasomandeh.com">
                            </div>
                        </div>

                        <div x-data="{ showPass: false }">
                            <label class="block text-[11px] font-semibold text-neutral-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-neutral-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input :type="showPass ? 'text' : 'password'" name="password" value="password" required class="w-full pl-9 pr-9 py-2 border border-neutral-300 rounded focus:border-[#7A1F2B] focus:ring-1 focus:ring-[#7A1F2B] outline-none text-xs transition-colors" placeholder="Password">
                                <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-neutral-400 hover:text-neutral-600">
                                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="rounded-[2px] border-neutral-300 text-[#7A1F2B] focus:ring-[#7A1F2B] w-3 h-3">
                                <span class="text-[11px] text-neutral-500">Ingat saya</span>
                            </label>
                            <a href="#" onclick="alert('Hubungi Superadmin (admin@rasomandeh.com) atau IT Support untuk mereset password Anda. Reset otomatis dinonaktifkan demi keamanan ERP Raso Mandeh.')" class="text-[11px] font-bold text-[#7A1F2B] hover:underline">Lupa password?</a>
                        </div>

                        <button type="submit" class="w-full bg-[#7A1F2B] hover:bg-[#5a1620] text-white font-bold py-2.5 px-4 rounded transition-colors text-xs mt-2 shadow-sm">
                            Masuk
                        </button>
                    </form>
                    
                    <div class="relative py-3">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-neutral-200"></div>
                        </div>
                        <div class="relative flex justify-center text-[10px]">
                            <span class="px-2 bg-white text-neutral-400">Atau</span>
                        </div>
                    </div>

                    <button type="button" @click="startScanner()" class="w-full bg-[#10b981] hover:bg-[#059669] text-white font-bold py-2.5 px-4 rounded transition-colors text-xs shadow-sm flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span>Scan ID Card (QR Login)</span>
                    </button>
                </div>

                <!-- State: QR Scanner -->
                <div x-show="mode === 'scan'" style="display: none;" class="space-y-4">
                    <h3 class="font-bold text-center text-sm text-neutral-800">Scan QR ID Card</h3>
                    <p class="text-xs text-center text-neutral-500 mb-2">Arahkan kamera ke ID Card Anda untuk login otomatis.</p>
                    
                    <div id="qr-reader" class="w-full bg-neutral-900 rounded-xl overflow-hidden aspect-square relative border border-neutral-200"></div>
                    
                    <!-- Loading Status -->
                    <div x-show="loading" class="text-center py-2">
                        <span class="text-xs font-bold text-[#7A1F2B] animate-pulse">Memverifikasi ID Card...</span>
                    </div>

                    <!-- Error Alert -->
                    <div x-show="errorMsg" class="bg-rose-50 text-rose-600 text-xs p-3 rounded text-center font-semibold" x-text="errorMsg"></div>

                    <button type="button" @click="stopScanner()" class="w-full bg-neutral-100 hover:bg-neutral-200 text-neutral-700 font-bold py-2.5 px-4 rounded transition-colors text-xs mt-2">
                        Batal / Kembali
                    </button>
                </div>
            </div>

            <div class="mt-auto pt-8 text-center">
                <p class="text-[9px] text-neutral-400 uppercase tracking-wider">
                    &copy; {{ date('Y') }} Raso Mandeh — ERP v1.0
                </p>
                <a href="{{ url('/') }}" class="inline-block mt-1 text-[10px] font-medium text-neutral-400 hover:text-[#7A1F2B]">
                    Kembali ke Halaman Publik
                </a>
            </div>
        </div>
    </div>
    <!-- 2FA Modal Overlay -->
    @if(session()->has('2fa_user_id'))
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop Blur -->
        <div class="absolute inset-0 bg-neutral-900/60 backdrop-blur-md"></div>
        
        <!-- Modal Content (dari 2fa.blade.php) -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden relative z-10 animate-[fadeIn_0.3s_ease-out]">
            <!-- Red Header -->
            <div class="bg-[#7A1F2B] px-8 py-10 flex flex-col items-center justify-center text-center relative">
                <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] mix-blend-overlay"></div>
                
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mb-4 relative z-10 backdrop-blur-sm border border-white/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-black text-white mb-1 relative z-10">Keamanan Dua Langkah</h1>
                <p class="text-white/80 text-sm relative z-10">Otentikasi Dua Faktor (2FA) Aktif</p>
            </div>

            <!-- Body -->
            <div class="p-8 text-center">
                <p class="text-[13px] text-neutral-600 leading-relaxed mb-8">
                    Buka aplikasi <strong>Google Authenticator</strong> atau aplikasi TOTP lainnya di ponsel Anda untuk melihat kode verifikasi 6 digit saat ini.
                </p>

                @if(session('error'))
                <div class="mb-6 p-3 bg-rose-50 text-rose-600 text-xs rounded-lg border border-rose-100 font-semibold">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('admin.login.2fa.submit') }}" method="POST">
    @csrf
 @csrf
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-neutral-800 tracking-wider mb-2 uppercase">Masukkan 6 Digit Kode OTP</label>
                        <input type="text" name="code" maxlength="6" class="w-full px-4 py-4 bg-white border-2 border-neutral-200 rounded-xl text-center text-2xl tracking-[0.3em] font-mono font-bold text-neutral-800 focus:outline-none focus:ring-4 focus:ring-[#7A1F2B]/20 focus:border-[#7A1F2B] transition-all" placeholder="000000" required autocomplete="off" autofocus>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#7A1F2B] hover:bg-[#5a1620] text-white font-bold rounded-xl shadow-md transition-all mb-6 text-sm">
                        Verifikasi & Masuk
                    </button>

                    <a href="#" onclick="event.preventDefault(); document.getElementById('cancel-2fa-form').submit();" class="text-sm font-medium text-neutral-500 hover:text-neutral-800 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Batal & Kembali</span>
                    </a>
                </form>
                
                <form id="cancel-2fa-form" action="{{ route('admin.login.2fa.cancel') }}" method="POST" class="hidden">
    @csrf
 @csrf
                </form>
            </div>
        </div>
    </div>
    @endif
    
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('loginScanner', () => ({
            mode: 'form', // 'form' or 'scan'
            loading: false,
            errorMsg: '',
            html5QrcodeScanner: null,
            
            startScanner() {
                this.mode = 'scan';
                this.errorMsg = '';
                
                // Need a slight delay for Alpine to unhide the div
                setTimeout(() => {
                    if(this.html5QrcodeScanner) {
                        this.html5QrcodeScanner.clear();
                        this.html5QrcodeScanner = null;
                    }
                    
                    this.html5QrcodeScanner = new Html5QrcodeScanner(
                        "qr-reader", { fps: 10, qrbox: 250, aspectRatio: 1.0 }
                    );
                    
                    this.html5QrcodeScanner.render(
                        (decodedText) => {
                            this.html5QrcodeScanner.clear(); 
                            this.processQrLogin(decodedText);
                        },
                        (errorMessage) => { }
                    );
                }, 100);
            },
            
            stopScanner() {
                this.mode = 'form';
                if(this.html5QrcodeScanner) {
                    this.html5QrcodeScanner.clear();
                    this.html5QrcodeScanner = null;
                }
            },

            processQrLogin(token) {
                this.loading = true;
                this.errorMsg = '';
                
                fetch('{{ route('admin.login.qr') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ login_token: token })
                })
                .then(response => response.json())
                .then(data => {
                    this.loading = false;
                    if(data.success) {
                        window.location.href = data.redirect;
                    } else {
                        this.errorMsg = data.message || 'Token QR tidak valid.';
                        // Restart scanner after error
                        setTimeout(() => this.startScanner(), 2000);
                    }
                })
                .catch(error => {
                    this.loading = false;
                    this.errorMsg = 'Terjadi kesalahan jaringan.';
                    setTimeout(() => this.startScanner(), 2000);
                });
            }
        }));
    });
    </script>
    <style>
    #qr-reader { border: none !important; }
    #qr-reader__scan_region { background-color: #171717; }
    #qr-reader__dashboard_section_csr span { color: #fff !important; }
    #qr-reader button {
        background-color: #C9A227; color: white; border: none;
        padding: 8px 16px; border-radius: 8px; font-weight: bold;
        cursor: pointer; margin-top: 10px; width: 100%; font-size: 11px;
    }
    #qr-reader select { padding: 8px; border-radius: 8px; margin-bottom: 10px; width: 100%; font-size: 11px;}
    </style>
</body>
</html>
