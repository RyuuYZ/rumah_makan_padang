@extends('layouts.app')

@section('title', 'Raso Mandeh - Rumah Makan Padang Autentik Sejak 1950')

@section('content')
    <!-- Hero Section -->
    @include('components.hero')

    <!-- Branch Selector Section -->
    @include('components.branch-selector')

    <!-- Signature Menu Section -->
    @include('components.menu-section')

    <!-- Minangkabau Gonjong Architectural Curved Divider -->
    <div class="gonjong-divider py-8"></div>

    <!-- Heritage Story Section -->
    @include('components.cerita-kami')

    <!-- Reservation Section -->
    @include('components.reservation-form')

    <!-- App Download Section (Unduh Aplikasi Android Rasa Mandeh) -->
    @include('components.app-download-section')

    <!-- Customer Reviews / Testimonials Section -->
    @include('components.testimonial-section')
@endsection
