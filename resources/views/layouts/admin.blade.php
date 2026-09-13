<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard - Raso Mandeh')</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍛</text></svg>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="{ sidebarOpen: false }" class="bg-[#F9FAFB] text-neutral-800 font-sans antialiased">

    <div class="min-h-screen flex">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-neutral-900/60 backdrop-blur-xs z-40 lg:hidden"
             style="display: none;"
             x-cloak></div>

        <!-- Premium Light Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-40 w-64 bg-white flex flex-col transition-transform duration-300 ease-in-out border-r border-[#C9A227]/20 shadow-[4px_0_24px_rgba(122,31,43,0.05)] overflow-hidden">
            
            <!-- Subtle background pattern -->
            <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/batik-stripes.png')] mix-blend-multiply pointer-events-none"></div>
            
            <!-- Brand Header -->
            <div class="h-20 px-6 border-b border-[#C9A227]/10 flex items-center justify-between relative z-10 bg-white/50 backdrop-blur-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group w-full pt-2">
                    <img src="/logo/Logo_Final.png" alt="Raso Mandeh Logo" class="w-32 object-contain drop-shadow-sm transition-transform duration-300 group-hover:scale-105">
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-[#7A1F2B] hover:text-[#C9A227] p-2 bg-[#7A1F2B]/5 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-5 py-6 space-y-8 overflow-y-auto relative z-10 scrollbar-hide">
                
                <!-- Main Section -->
                <div>
                    <span class="px-4 text-[10px] font-bold tracking-widest uppercase text-[#C9A227] block mb-3 font-serif">Utama</span>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('admin.dashboard') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.orders.index') }}" 
                           class="flex items-center justify-between px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('admin.orders.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.orders.*') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                <span>Pesanan Masuk</span>
                            </div>
                            @php
                                $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
                            @endphp
                            @if($pendingOrdersCount > 0)
                            <span class="bg-[#C9A227] text-white shadow-sm text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-[#C9A227]/50">
                                {{ $pendingOrdersCount }}
                            </span>
                            @endif
                        </a>

                        <!-- Kasir POS Scanner -->
                        <a href="{{ route('kasir.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('kasir.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('kasir.*') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <span>Kasir POS (Mesin & Scan)</span>
                        </a>
                    </div>
                </div>

                <!-- Management Section -->
                <div>
                    <span class="px-4 text-[10px] font-bold tracking-widest uppercase text-[#C9A227] block mb-3 font-serif">Operasional</span>
                    <div class="space-y-1">
                        <a href="{{ route('admin.menu.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('admin.menu.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.menu.*') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span>Menu Masakan</span>
                        </a>

                        <a href="{{ route('admin.branches.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('admin.branches.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.branches.*') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Cabang Restoran</span>
                        </a>

                        <a href="{{ route('admin.tables.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('admin.tables.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.tables.*') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span>Kapasitas Meja</span>
                        </a>

                        <a href="{{ route('admin.reviews.index') }}" 
                           class="flex items-center justify-between px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('admin.reviews.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 {{ request()->routeIs('admin.reviews.*') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                <span>Moderasi Ulasan</span>
                            </div>
                            @php
                                $unapprovedCount = \App\Models\Review::where('is_approved', false)->count();
                            @endphp
                            @if($unapprovedCount > 0)
                            <span class="bg-[#C9A227] text-white shadow-sm text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-[#C9A227]/50">
                                {{ $unapprovedCount }}
                            </span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Sistem Section -->
                <div>
                    <span class="px-4 text-[10px] font-bold tracking-widest uppercase text-[#C9A227] block mb-3 font-serif">Sistem</span>
                    <div class="space-y-1">
                        <a href="{{ route('admin.logs.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200
                           {{ request()->routeIs('admin.logs.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <svg class="w-5 h-5 {{ request()->routeIs('admin.logs.*') ? 'text-[#C9A227]' : 'text-neutral-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <span>Log Sistem</span>
                        </a>

                        <a href="{{ route('admin.guide.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl font-medium text-[13px] transition-all duration-200 
                           {{ request()->routeIs('admin.guide.*') ? 'bg-[#7A1F2B] text-white shadow-md shadow-[#7A1F2B]/20 font-semibold' : 'text-neutral-600 hover:bg-[#F5EFE2] hover:text-[#7A1F2B]' }}">
                            <svg class="w-5 h-5 text-neutral-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span>Buku Panduan</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- User Info & Logout Footer -->
            <div class="p-4 border-t border-[#C9A227]/20 bg-[#F5EFE2]/50 relative z-10 space-y-3">

                <a href="{{ url('/') }}" target="_blank" 
                   class="flex items-center justify-center space-x-2 w-full py-2 px-3 rounded-xl border border-[#C9A227]/30 text-[#7A1F2B] hover:bg-[#7A1F2B] hover:text-white text-[11px] font-semibold transition-all">
                    <span>Lihat Halaman Restoran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
        </aside>

        <!-- Main Content Area with Fixed Sidebar Offset -->
        <div class="lg:pl-64 flex-1 flex flex-col min-w-0 min-h-screen bg-[#F5EFE2]/30">
            
            <!-- Premium Topbar -->
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-[#C9A227]/20 h-16 px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-shrink-0 shadow-sm">
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-[#7A1F2B] bg-[#7A1F2B]/5 hover:bg-[#7A1F2B]/10 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <div class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-neutral-500 font-medium font-serif">
                            <span class="hidden sm:inline">Admin</span>
                            <span class="hidden sm:inline text-[#C9A227]">•</span>
                            <span class="text-[#7A1F2B] font-bold text-base sm:text-lg truncate max-w-[150px] sm:max-w-none">@yield('header_title', 'Dashboard')</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="text-sm font-bold text-neutral-700 hidden md:block border-l border-neutral-200 pl-4 font-serif">
                        {{ date('d M Y') }}
                    </div>

                    <!-- Admin Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="flex items-center space-x-3 p-1.5 rounded-[20px] bg-white hover:bg-neutral-50 transition-all focus:outline-none">
                            <div class="w-10 h-10 rounded-[16px] overflow-hidden bg-gradient-to-br from-[#7A1F2B] to-[#9A2A38] text-[#C9A227] font-serif font-bold flex items-center justify-center flex-shrink-0 text-lg shadow-sm">
                                @if(Auth::user()->profile_photo_url)
                                    <img src="{{ Auth::user()->profile_photo_url }}" class="w-full h-full object-cover" alt="Profile">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1 hidden md:block text-left pr-2">
                                <p class="text-[13px] font-black text-[#1E293B] truncate leading-tight">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-[11px] font-medium text-[#64748B] truncate leading-tight">{{ Auth::user()->email ?? 'admin@rasomandeh.com' }}</p>
                            </div>
                            <svg class="w-4 h-4 text-neutral-400 mr-2" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                             class="absolute right-0 mt-2 w-56 rounded-2xl bg-white shadow-xl border border-neutral-100 overflow-hidden z-50"
                             style="display: none;">
                            <div class="p-3 border-b border-neutral-50 bg-neutral-50/50 md:hidden">
                                <p class="text-sm font-bold text-neutral-800 truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-xs text-neutral-500 truncate">{{ Auth::user()->email ?? 'admin@rasomandeh.com' }}</p>
                            </div>
                            <div class="p-2">
                                <a href="{{ route('admin.profile') }}" class="flex items-center space-x-3 px-3 py-2.5 text-sm font-medium text-neutral-600 hover:text-[#7A1F2B] hover:bg-neutral-50 rounded-xl transition-colors">
                                    <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Lihat Profil</span>
                                </a>
                                <div class="h-px bg-neutral-100 my-1"></div>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                        <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        <span>Keluar (Logout)</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Alerts -->
            @if(session('success'))
            <div class="mx-6 lg:mx-8 mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 lg:mx-8 mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200/80 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif

            <!-- Main Scrollable Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <!-- 2FA Security Warning Banner -->
                @if(Auth::check() && !Auth::user()->two_factor_confirmed_at)
                <div x-data="{ showWarning: true }" x-show="showWarning" class="mb-6 rounded-xl bg-amber-50 border border-amber-200 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-amber-900">Keamanan Akun Anda Belum Optimal!</h4>
                            <p class="text-xs text-amber-700 mt-0.5">Anda belum mengaktifkan Otentikasi Dua Faktor (2FA). Aktifkan sekarang untuk melindungi akun Anda dari akses tidak sah.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.profile') }}" class="whitespace-nowrap px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                            Aktifkan 2FA
                        </a>
                        <button @click="showWarning = false" class="p-2 text-amber-500 hover:bg-amber-100 hover:text-amber-700 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                @endif

                @yield('content')
            </main>
        </div>

    </div>

</body>
</html>
