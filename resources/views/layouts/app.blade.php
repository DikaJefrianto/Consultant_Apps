<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ config('settings.site_favicon') ?? asset('favicon.ico') }}" type="image/x-icon">

    {{-- Tailwind CSS CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Bootstrap CSS CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    {{-- Font Awesome CSS CDN (Untuk ikon-ikon) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- AOS (Animate On Scroll) CSS CDN --}}
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    {{-- STACK UNTUK CUSTOM STYLES DARI CHILD VIEWS --}}
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">

    {{-- Header --}}
    <header class="bg-white shadow fixed top-0 left-0 right-0 z-50"> {{-- Fixed header --}}
        <div class="mx-auto max-w-7xl flex items-center justify-between py-3 px-4 md:px-6">
            <div class="flex items-center justify-between w-full md:w-auto">
                <img src="/images/ns-longl-color.png" alt="Naima Sustainability" class="logo" />
                <button id="menu-toggle" class="md:hidden ml-3 focus:outline-none">
                    <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            {{-- Navigasi Desktop --}}
            <nav class="hidden md:flex ml-10 space-x-6">
                {{-- Menambahkan kelas Tailwind untuk styling dasar --}}
                <a href="{{ url ('/') }}" class="nav-link text-gray-700 hover:text-green-600 transition-colors duration-200">Beranda</a>
                <a href="{{ url ('/#tentang-kami') }}" class="nav-link text-gray-700 hover:text-green-600 transition-colors duration-200">Tentang Kami</a>
                <a href="{{ url ('/#fitur') }}" class="nav-link text-gray-700 hover:text-green-600 transition-colors duration-200">Fitur</a>
                <a href="{{ route('guides.index') }}" class="nav-link text-gray-700 hover:text-green-600 transition-colors duration-200">Panduan</a>
                <a href="{{ url ('/#faq') }}" class="nav-link text-gray-700 hover:text-green-600 transition-colors duration-200">FAQ</a>
                <a href="{{ url ('/#hubungi-kami') }}" class="nav-link text-gray-700 hover:text-green-600 transition-colors duration-200">Hubungi Kami</a>
            </nav>

            <div class="hidden md:flex items-center space-x-3">
                @auth
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center dropdown-toggle gap-2"
                            data-bs-toggle="dropdown">
                            <span class="text-sm text-gray-800 font-bold">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if (Auth::user()->can('dashboard.view'))
                                <li><a href="{{ route('admin.dashboard') }}" class="dropdown-item">Dashboard</a></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Edit Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-red-600">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                @endauth
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden bg-white px-4 pb-4 border-t border-gray-200">
            <nav class="flex flex-col space-y-3 py-2">
                <a href="#beranda" class="nav-link scroll-link text-base text-gray-700 hover:text-green-600 transition-colors duration-200">Beranda</a>
                <a href="#tentang-kami" class="nav-link scroll-link text-base text-gray-700 hover:text-green-600 transition-colors duration-200">Tentang Kami</a>
                <a href="#fitur" class="nav-link scroll-link text-base text-gray-700 hover:text-green-600 transition-colors duration-200">Fitur</a>
                <a href="{{ route('guides.index') }}" class="nav-link text-base text-gray-700 hover:text-green-600 transition-colors duration-200">Panduan</a>
                <a href="#faq" class="nav-link scroll-link text-base text-gray-700 hover:text-green-600 transition-colors duration-200">FAQ</a>
                <a href="#hubungi-kami" class="nav-link scroll-link text-base text-gray-700 hover:text-green-600 transition-colors duration-200">Hubungi Kami</a>
                @auth
                    @if (Auth::user()->can('dashboard.view'))
                        <a href="{{ route('admin.dashboard') }}" class="nav-link text-base text-gray-700 hover:text-green-600 transition-colors duration-200">Dashboard</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="btn btn-secondary w-full text-base">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary w-full text-base">Login</a>
                @endauth
            </nav>
        </div>
    </header>

   
    <div class="pt-[60px]"> {{-- Sesuaikan nilai ini dengan tinggi header --}}
        @yield('content')
    </div>


    {{--Footer--}}
    <footer class="bg-green-800 text-white mt-auto py-10 px-4">
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8 max-w-7xl">
            <div>
                <img src="/images/logo/ns-white-color-dark.png" alt="Naima Sustainability" class="h-20"> {{-- Assuming a white logo variant --}}
                <p class="text-sm text-green-200 leading-relaxed text-justify">
                    Naima Sustainability adalah platform terdepan untuk membantu bisnis Anda
                    mengukur dan mengelola jejak karbon, mendukung masa depan yang lebih hijau.
                </p>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4 text-green-100">Navigasi</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ url ('/') }}" class="text-green-200 hover:text-white transition-colors duration-200 ">Beranda</a></li>
                    <li><a href="{{ url ('/#tentang-kami') }}" class="text-green-200 hover:text-white transition-colors duration-200 ">Tentang Kami</a></li>
                    <li><a href="{{ url ('/#fitur') }}" class="text-green-200 hover:text-white transition-colors duration-200 ">Fitur</a></li>
                    <li><a href="{{ route('guides.index') }}" class="text-green-200 hover:text-white transition-colors duration-200">Panduan</a></li> {{-- Diarahkan ke route panduan --}}
                    <li><a href="{{ url ('/#faq') }}" class="text-green-200 hover:text-white transition-colors duration-200 ">FAQ</a></li>
                    <li><a href="{{ url ('/#hubungi-kami') }}" class="text-green-200 hover:text-white transition-colors duration-200 ">Hubungi Kami</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4 text-green-100">Kontak Kami</h3>
                <address class="not-italic text-green-200 text-sm space-y-2">
                    <p class="flex items-center"><i class="fas fa-map-marker-alt mr-2"></i> Jl. Siteba, Padang, Sumatera Barat,Indonesia</p>
                    <p class="flex items-center"><i class="fas fa-envelope mr-2"></i> <a href="mailto:contactnaima@gmail.com" class="hover:underline">contactnaima@gmail.com</a></p>
                    <p class="flex items-center"><i class="fas fa-phone-alt mr-2"></i> <a href="tel:+6287083817392" class="hover:underline">+62 870-8381-7392</a></p>
                </address>
            </div>
            <div>
                <h3 class="font-bold text-lg mb-4 text-green-100">Ikuti Kami</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-green-200 hover:text-white transition-colors duration-200" aria-label="LinkedIn">
                        <i class="fab fa-linkedin text-2xl"></i>
                    </a>
                    <a href="#" class="text-green-200 hover:text-white transition-colors duration-200" aria-label="Instagram">
                        <i class="fab fa-instagram text-2xl"></i>
                    </a>
                    <a href="#" class="text-green-200 hover:text-white transition-colors duration-200" aria-label="Facebook">
                        <i class="fab fa-facebook text-2xl"></i>
                    </a>
                    <a href="#" class="text-green-200 hover:text-white transition-colors duration-200" aria-label="Twitter">
                        <i class="fab fa-twitter text-2xl"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center text-sm text-green-300 border-t border-green-700 pt-6 mt-10">
            © {{ date('Y') }} Naima Sustainability. All Rights Reserved.
        </div>
    </footer>

    {{-- Bootstrap JS Bundle CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- AOS (Animate On Scroll) JS CDN --}}
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000, // Durasi animasi dalam ms
            once: true,     // Animasi hanya terjadi sekali saat elemen masuk viewport
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Logika untuk Mobile Menu Toggle
            const menuToggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');

            if (menuToggle && mobileMenu) {
                menuToggle.addEventListener('click', function () {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Logika untuk Smooth Scrolling
            document.querySelectorAll('.scroll-link').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    const targetId = this.getAttribute('href');
                    const currentPath = window.location.pathname;

                    // Periksa apakah tautan adalah scroll-link di halaman saat ini (misalnya home page)
                    // Atau jika itu adalah tautan ke hash di halaman root (seperti /#section)
                    if (targetId.startsWith('#') && (currentPath === '/' || currentPath.endsWith('/'))) {
                        e.preventDefault(); // Mencegah perilaku default tautan
                        const targetElement = document.querySelector(targetId);
                        if (targetElement) {
                            targetElement.scrollIntoView({ behavior: 'smooth' });
                            // Sembunyikan menu mobile setelah klik (jika terbuka)
                            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                                mobileMenu.classList.add('hidden');
                            }
                        }
                    }
                    // Jika itu tautan ke bagian di halaman *lain* (misalnya dari /panduan ke /#tentang-kami)
                    // Biarkan browser menangani navigasi terlebih dahulu, lalu gulir
                    else if (targetId.startsWith('/') && targetId.includes('#')) {
                        // Jangan preventDefault, biarkan browser menavigasi ke halaman dulu
                        // Setelah navigasi, browser akan mencoba menggulir ke #hash
                        // Ini akan bekerja secara otomatis jika section id ada di halaman tujuan
                    }
                });
            });
        });
    </script>

    {{-- STACK UNTUK CUSTOM SCRIPTS DARI CHILD VIEWS --}}
    @stack('scripts')
</body>
</html>
