@extends('layouts.app')

@section('title', 'Unduh Aplikasi Mobile Rasa Mandeh - Resmi Android (APK)')

@section('content')
    <div class="pt-20">
        {{-- Breadcrumb --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-xs text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-[#7A1F2B] transition-colors">Beranda</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Unduh Aplikasi Mobile</span>
            </nav>
        </div>

        {{-- App Download Section --}}
        @include('components.app-download-section')
    </div>
@endsection
