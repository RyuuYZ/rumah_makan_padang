<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Raso Mandeh - Warisan Kuliner Minangkabau Sejak 1950')</title>
    <meta name="description" content="Rumah Makan Padang Raso Mandeh - Nikmati cita rasa masakan Minang autentik resep turun-temurun sejak 1950. Rendang Payakumbuh 8 jam, Ayam Pop, Gulai Kakap, dan Es Tebak.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🍛</text></svg>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,600&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="restaurantApp()" class="bg-[#F5EFE2] text-[#241B16] font-sans antialiased selection:bg-[#7A1F2B] selection:text-white" x-cloak>

    <!-- Header Navigation -->
    @include('components.header')

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Cart Slide-over Drawer -->
    @include('components.cart-drawer')

    <!-- Footer -->
    @include('components.footer')

    <!-- Floating Toast Notification -->
    <div x-show="showNotification" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
         x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-6 left-6 z-50 bg-[#241B16] text-[#F5EFE2] px-5 py-3.5 rounded-xl shadow-2xl border border-[#C9A227]/30 flex items-center space-x-3 max-w-sm">
        <span class="w-8 h-8 rounded-full bg-[#C9A227]/20 flex items-center justify-center text-[#C9A227] flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </span>
        <p class="text-sm font-medium" x-text="notificationMessage"></p>
    </div>

    <!-- Image Asset Protection -->
    <style>
        img {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
            -webkit-user-select: none;
            user-select: none;
            pointer-events: none;
        }
        /* Re-enable pointer events only for interactive elements that contain images */
        button img, a img, [x-on\:click] img, [\\@click] img {
            pointer-events: auto;
        }
    </style>
    <script>
        document.addEventListener('contextmenu', function (e) {
            if (e.target.tagName === 'IMG' || e.target.closest('img')) {
                e.preventDefault();
                return false;
            }
        });
        document.addEventListener('dragstart', function (e) {
            if (e.target.tagName === 'IMG') {
                e.preventDefault();
                return false;
            }
        });
    </script>

</body>
</html>
