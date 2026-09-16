<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keamanan 2FA - Raso Mandeh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-neutral-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Decorative background -->
    <div class="absolute inset-0 z-0">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-[#7A1F2B]/10 blur-3xl"></div>
        <div class="absolute top-[60%] -right-[10%] w-[40%] h-[40%] rounded-full bg-[#C9A227]/10 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden relative z-10">
        
        <!-- Red Header -->
        <div class="bg-[#B91C1C] px-8 py-10 flex flex-col items-center justify-center text-center relative">
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
            <div class="mb-6 p-3 bg-rose-50 text-rose-600 text-xs rounded-lg border border-rose-100">
                {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('admin.login.2fa.submit') }}" method="POST">
    @csrf
 @csrf
                <div class="mb-6">
                    <label class="block text-xs font-bold text-neutral-800 tracking-wider mb-2 uppercase">Masukkan 6 Digit Kode OTP</label>
                    <input type="text" name="code" maxlength="6" class="w-full px-4 py-4 bg-white border-2 border-neutral-200 rounded-xl text-center text-2xl tracking-[0.3em] font-mono font-bold text-neutral-800 focus:outline-none focus:ring-4 focus:ring-[#B91C1C]/20 focus:border-[#B91C1C] transition-all" placeholder="000000" required autocomplete="off" autofocus>
                </div>

                <button type="submit" class="w-full py-3.5 bg-[#B91C1C] hover:bg-[#991B1B] text-white font-bold rounded-xl shadow-md transition-all mb-6">
                    Verifikasi & Masuk
                </button>

                <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-sm font-medium text-neutral-500 hover:text-neutral-800 transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Halaman Login</span>
                </a>
            </form>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
    @csrf
 @csrf
            </form>
        </div>
    </div>
</body>
</html>
