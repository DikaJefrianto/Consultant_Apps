@extends('layouts.app')

@section('title')
    {{ __('Home') }} | {{ config('app.name') }}
@endsection

@push('styles')
    <style>
        /* Custom styles from your original code, kept for consistency */
        .logo {
            height: 50px;
        }

        .nav-link {
            color: #4a5568;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #319c63;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem; /* Slightly larger padding for better clickability */
            font-size: 1rem; /* Slightly larger font for prominence */
            font-weight: 600; /* Bolder font */
            border-radius: 0.5rem; /* More rounded corners */
            text-align: center;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #38a169; /* Green-700 */
            color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .btn-primary:hover {
            background-color: #2f855a; /* Green-800 */
            transform: translateY(-2px); /* Slight lift on hover */
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background-color: #e2e8f0; /* Gray-200 */
            color: #4a5568; /* Gray-700 */
            border: 1px solid #cbd5e0; /* Gray-300 border */
        }

        .btn-secondary:hover {
            background-color: #cbd5e0; /* Gray-300 */
            transform: translateY(-2px); /* Slight lift on hover */
            transition: all 0.3s ease;
        }

        /* Added for smooth scroll offset */
        html {
            scroll-behavior: smooth;
        }

        section {
            padding-top: 80px; /* Adjust based on your fixed header height */
            margin-top: -80px; /* Negative margin to account for padding-top */
        }

        /* Responsive adjustments for image sizes in sections */
        .section-image {
            max-width: 80%; /* Smaller on larger screens */
            height: auto;
        }
        @media (min-width: 768px) { /* Medium screens and up */
            .section-image {
                max-width: 100%;
            }
        }

        /* FAQ Accordion Styles */

        .accordion-item {
            border-bottom: 1px solid #e2e8f0; /* gray-200 */
        }
        .accordion-header {
            width: 100%;
            padding: 1.25rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #3182ce; /* blue-600 */
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            background-color: #f7fafc; /* gray-50 */
            transition: background-color 0.3s ease;
        }
        .accordion-header:hover {
            background-color: #edf2f7; /* gray-100 */
        }
        .accordion-header i {
            transition: transform 0.3s ease;
        }
        .accordion-header.active i {
            transform: rotate(180deg);
        }
        .accordion-content {
            padding: 1rem;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            display: none; /* Hidden by default */
            transition: max-height 0.3s ease-out, opacity 0.3s ease-out;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .accordion-content.open {
            display: block; /* Shown when open */
            max-height: 500px; /* Arbitrary large value to allow content to show */
            opacity: 1;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.-beta3/css/all.min.css">
@endpush

@section('content')
    <div class="min-h-screen max-w-full overflow-x-hidden mx-auto">
        {{-- Added padding-top to main content to account for fixed header --}}
        <div class="pt-[76px]"> {{-- Added horizontal padding for mobile to remove right edge gap --}}

            {{-- ============================================================================================================= --}}
            {{-- BAGIAN BERANDA (HERO SECTION) --}}
            {{-- ============================================================================================================= --}}
            <section id="beranda" class="w-screen relative bg-cover bg-center"
                style="background-image: url('/images/home-page.png'); height: 680px;">
                <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                <div
                    class="container mx-auto relative z-10 flex flex-col items-center justify-center h-full text-center text-white px-4"
                    data-aos="fade-up" data-aos-duration="1500">
                    <h1 class="text-3xl md:text-4xl font-bold" data-aos="fade-up" data-aos-delay="200">Ketahui Jejak Karbon dari Perjalanan Bisnis Anda</h1>
                    <p class="mt-4 text-sm md:text-base" data-aos="fade-up" data-aos-delay="400">Lacak Emisi, Laporkan dengan Mudah, dan Mulai Perubahan Sekarang!
                    </p>
                    <a href="{{route('admin.perhitungan.create')}}" class="btn btn-primary mt-6" data-aos="zoom-in" data-aos-delay="600">Hitung Jejak karbon kamu</a>
                </div>
            </section>


            {{-- ============================================================================================================= --}}
            {{-- SECTIONS OVERVIEW / WHY NAIMA --}}
            {{-- ============================================================================================================= --}}
            <section class="py-20 bg-white">
                <div class="container mx-auto px-4 max-w-7xl my-4">
                    <div class="text-center my-10" data-aos="fade-down">
                        <h2 class="text-3xl md:text-3xl font-bold text-green-800 mb-4 mt-4">
                            Mengapa Naima Sustainability?
                        </h2>
                        <p class="text-lg text-gray-800 max-w-2xl mx-auto text-center">
                            Kami percaya bahwa setiap bisnis, besar maupun kecil, memiliki peran penting
                            dalam menjaga keberlanjutan bumi. Naima hadir untuk mempermudah perjalanan Anda.
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="order-2 md:order-1 text-left md:text-left" data-aos="fade-right">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">
                                Jejak Karbon: Langkah Awal Menuju Keberlanjutan
                            </h3>
                            <p class="text-gray-700 leading-relaxed text-lg">
                                Perubahan iklim adalah tantangan global yang memerlukan tindakan nyata.
                                Mengukur jejak karbon Anda adalah fondasi untuk memahami dampak lingkungan
                                operasional bisnis Anda. Dengan data yang akurat, Anda dapat merencanakan
                                strategi pengurangan emisi yang efektif dan berkelanjutan.
                            </p>
                            <a href="#panduan" class="text-green-600 hover:text-green-800 mt-4 inline-block font-semibold scroll-link">
                                Pelajari Lebih Lanjut <i class="fas fa-long-arrow-alt-right ml-1"></i>
                            </a>
                        </div>
                        <div class="order-1 md:order-2 text-center" data-aos="fade-left">
                            <img src="/images/zero-emission.jpg" alt="Responsible Business" class="mx-auto section-image rounded-lg w-100" style="max-width: 350px; height: auto;">
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12 items-center pt-6" >
                        <div class="text-center md:text-left" data-aos="fade-right">
                            <img src="/images/digital-solution.webp" alt="Digital Solution" class="mx-auto section-image rounded-lg" style="max-width: 350px; height: auto;">
                        </div>
                        <div data-aos="fade-left">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">
                                Solusi Digital Naima: Akurat dan Mudah
                            </h3>
                            <p class="text-gray-700 leading-relaxed text-lg">
                                Naima menyediakan platform digital yang intuitif untuk melacak dan melaporkan
                                jejak karbon dari perjalanan bisnis Anda. Kami menyederhanakan proses kompleks
                                menjadi langkah-langkah yang mudah diikuti, membantu Anda fokus pada inti
                                bisnis sambil tetap berkontribusi pada lingkungan.
                            </p>
                            <a href="{{ route('login') }}" class="text-green-600 hover:text-green-800 mt-4 inline-block font-semibold">
                                Daftar Sekarang <i class="fas fa-long-arrow-alt-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================================================================= --}}
            {{-- BAGIAN TENTANG KAMI --}}
            {{-- ============================================================================================================= --}}
            <section id="tentang-kami" class="bg-gray-100 py-16 px-4">
                <div class="max-w-7xl mx-auto my-auto">
                    <h1 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12" data-aos="fade-up">Tentang Naima Sustainability</h1>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                        <div data-aos="fade-right">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-eye text-green-600 mr-3"></i> Visi Kami
                            </h2>
                            <p class="text-gray-700 leading-relaxed text-lg">
                                Menjadi platform terkemuka yang memberdayakan setiap organisasi untuk mengukur,
                                mengelola, dan mengurangi jejak karbon mereka, mendorong masa depan yang lebih
                                hijau dan berkelanjutan. Kami percaya bahwa setiap langkah kecil menuju
                                keberlanjutan adalah kontribusi besar bagi planet kita.
                            </p>
                        </div>
                        <div class="text-center" data-aos="fade-left">
                            <img src="/images/mobil_hijau.png" alt="Our Vision" class="mx-auto section-image rounded-lg w-100" style="max-width: 400px; height: auto;">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                        <div class="text-center md:order-2" data-aos="fade-left">
                            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-bullseye text-green-600 mr-3"></i> Misi Kami
                            </h2>
                            <ul class="list-none space-y-3 text-gray-700 leading-relaxed text-lg text-left">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2 flex-shrink-0"></i>
                                    Menyediakan alat perhitungan jejak karbon yang akurat dan mudah digunakan.
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2 flex-shrink-0"></i>
                                    Memberikan wawasan dan strategi actionable untuk pengurangan emisi.
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2 flex-shrink-0"></i>
                                    Membangun komunitas yang sadar lingkungan dan aktif berkontribusi.
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-600 mt-1 mr-2 flex-shrink-0"></i>
                                    Memfasilitasi pelaporan keberlanjutan yang transparan dan kredibel.
                                </li>
                            </ul>
                        </div>
                        <div class="text-center md:order-1" data-aos="fade-right">
                            <img src="/images/hitung.png" alt="Our Mission" class="mx-auto section-image rounded-lg w-100" style="max-width: 400px; height: auto;">
                        </div>
                    </div>

                    <div class="text-center my-16">
                        <h2 class="text-3xl font-bold text-gray-800 mb-8" data-aos="fade-up">Tim Kami</h2>
                        <p class="text-gray-700 mb-10 max-w-3xl mx-auto text-lg" data-aos="fade-up" data-aos-delay="100">
                            Kami adalah tim profesional yang bersemangat tentang keberlanjutan, teknologi, dan inovasi.
                            Setiap anggota tim membawa keahlian unik untuk mewujudkan visi Naima Sustainability.
                        </p>
                        {{-- Container untuk Horizontal Scrolling --}}
                        <div class="flex overflow-x-auto snap-x snap-mandatory px-4 py-2 gap-x-6 scrollbar-hide">
                            {{-- Card Anggota Tim 1 --}}
                            <div class="bg-white rounded-lg shadow-xl p-8 transform hover:scale-105 transition-transform duration-300 flex-shrink-0 w-72 sm:w-80 lg:w-96 snap-center" data-aos="zoom-in" data-aos-delay="200">
                                <img src="/images/imamjpg.jpg" alt="Nama Anggota" class="w-32 h-32 rounded-full mx-auto mb-5 object-cover border-4 border-green-300">
                                <h3 class="text-xl font-semibold text-gray-900 mb-1">Imam Teguh Islamy</h3>
                                <p class="text-green-700 font-medium text-base mb-3">CEO & Co-Founder</p>
                                <p class="text-gray-600 text-sm leading-relaxed">Sebagai CEO & Co-Founder Naima, Imam memimpin arah strategis perusahaan dengan visi berkelanjutan dan teknologi ramah lingkungan.</p>
                                <div class="flex justify-center space-x-4 mt-4">
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-linkedin-in text-lg"></i></a>
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-twitter text-lg"></i></a>
                                </div>
                            </div>
                            {{-- Card Anggota Tim 2 --}}
                            <div class="bg-white rounded-lg shadow-xl p-8 transform hover:scale-105 transition-transform duration-300 flex-shrink-0 w-72 sm:w-80 lg:w-96 snap-center" data-aos="zoom-in" data-aos-delay="300">
                                <img src="/images/alya.jpg" alt="Nama Anggota" class="w-32 h-32 rounded-full mx-auto mb-5 object-cover border-4 border-green-300">
                                <h3 class="text-xl font-semibold text-gray-900 mb-1">Alya Lihan Eltofani</h3>
                                <p class="text-green-700 font-medium text-base mb-3">Head of every Proposal</p>
                                <p class="text-gray-600 text-sm leading-relaxed">Sebagai kepala proposal, Alya bertanggung jawab memastikan setiap dokumen, perencanaan, dan pengajuan proyek tersusun rapi dan akurat.</p>
                                <div class="flex justify-center space-x-4 mt-4">
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-linkedin-in text-lg"></i></a>
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-github text-lg"></i></a>
                                </div>
                            </div>
                            {{-- Card Anggota Tim 3 --}}
                            <div class="bg-white rounded-lg shadow-xl p-8 transform hover:scale-105 transition-transform duration-300 flex-shrink-0 w-72 sm:w-80 lg:w-96 snap-center" data-aos="zoom-in" data-aos-delay="400">
                                <img src="images/faisal.jpg" alt="Nama Anggota" class="w-32 h-32 rounded-full mx-auto mb-5 object-cover border-4 border-green-300">
                                <h3 class="text-xl font-semibold text-gray-900 mb-1">Faisal Wimar</h3>
                                <p class="text-green-700 font-medium text-base mb-3">Head of Taking Analisyst</p>
                                <p class="text-gray-600 text-sm leading-relaxed">Sebagai kepala analis data perjalanan, Faisal memimpin proses analisis dan pengolahan data untuk perhitungan jejak karbon yang akurat.</p>
                                <div class="flex justify-center space-x-4 mt-4">
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-linkedin-in text-lg"></i></a>
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-researchgate text-lg"></i></a>
                                </div>
                            </div>
                            {{-- Card Anggota Tim 4 (Duplikat untuk demonstrasi scroll) --}}
                            <div class="bg-white rounded-lg shadow-xl p-8 transform hover:scale-105 transition-transform duration-300 flex-shrink-0 w-72 sm:w-80 lg:w-96 snap-center" data-aos="zoom-in" data-aos-delay="500">
                                <img src="images/dhea.jpg" alt="Nama Anggota" class="w-32 h-32 rounded-full mx-auto mb-5 object-cover border-4 border-green-300">
                                <h3 class="text-xl font-semibold text-gray-900 mb-1">Dhea Umi Amalia</h3>
                                <p class="text-green-700 font-medium text-base mb-3">Consultant for Communication and Marketing</p>
                                <p class="text-gray-600 text-sm leading-relaxed">Dhea bertugas sebagai penghubung komunikasi antara Naima dan perusahaan klien, serta menyusun strategi komunikasi dan pemasaran yang efektif.</p>
                                <div class="flex justify-center space-x-4 mt-4">
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-linkedin-in text-lg"></i></a>
                                    <a href="#" class="text-gray-600 hover:text-green-600"><i class="fab fa-behance text-lg"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- ============================================================================================================= --}}
            {{-- BAGIAN FITUR (NEW SECTION) --}}
            {{-- ============================================================================================================= --}}
            <section id="fitur" class="bg-white py-16 px-4">
                <div class="max-w-7xl mx-auto text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-12" data-aos="fade-up">Fitur Unggulan Naima Sustainability</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
                        <div class="flex flex-col items-center p-8 bg-gray-50 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-green-200 hover:shadow-2xl hover:border-green-300 hover:border" data-aos="zoom-in" data-aos-delay="100">
                            <div class="bg-green-100 p-4 rounded-full mb-6">
                                <i class="fas fa-calculator text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Kalkulator Jejak Karbon Akurat</h3>
                            <p class="text-gray-600 leading-relaxed text-center">
                                Hitung emisi dari berbagai moda transportasi dan aktivitas bisnis dengan metodologi yang diakui.
                            </p>
                        </div>
                        <div class="flex flex-col items-center p-8 bg-gray-50 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-green-200 hover:shadow-2xl hover:border-green-300 hover:border" data-aos="zoom-in" data-aos-delay="100">
                            <div class="bg-green-100 p-4 rounded-full mb-6">
                                <i class="fas fa-chart-line text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Visualisasi Data Interaktif</h3>
                            <p class="text-gray-600 leading-relaxed text-center">
                                Pahami dampak lingkungan Anda melalui grafik dan dashboard yang mudah dimengerti.
                            </p>
                        </div>
                        <div class="flex flex-col items-center p-8 bg-gray-50 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-green-200 hover:shadow-2xl hover:border-green-300 hover:border" data-aos="zoom-in" data-aos-delay="100">
                            <div class="bg-green-100 p-4 rounded-full mb-6">
                                <i class="fas fa-file-alt text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Laporan Keberlanjutan Otomatis</h3>
                            <p class="text-gray-600 leading-relaxed text-center">
                                Hasilkan laporan profesional sesuai standar keberlanjutan untuk pemangku kepentingan Anda.
                            </p>
                        </div>
                        <div class="flex flex-col items-center p-8 bg-gray-50 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-green-200 hover:shadow-2xl hover:border-green-300 hover:border" data-aos="zoom-in" data-aos-delay="100">
                            <div class="bg-green-100 p-4 rounded-full mb-6">
                                <i class="fas fa-lightbulb text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Rekomendasi Pengurangan Emisi</h3>
                            <p class="text-gray-600 leading-relaxed text-center">
                                Dapatkan saran personalisasi untuk mengurangi jejak karbon Anda secara efektif.
                            </p>
                        </div>
                        <div class="flex flex-col items-center p-8 bg-gray-50 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-green-200 hover:shadow-2xl hover:border-green-300 hover:border" data-aos="zoom-in" data-aos-delay="100">
                            <div class="bg-green-100 p-4 rounded-full mb-6">
                                <i class="fas fa-users text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Manajemen Tim Kolaboratif</h3>
                            <p class="text-gray-600 leading-relaxed text-center">
                                Ajak tim Anda untuk berpartisipasi dalam upaya keberlanjutan perusahaan.
                            </p>
                        </div>
                        <div class="flex flex-col items-center p-8 bg-gray-50 rounded-lg shadow-md transition-all duration-300 ease-in-out transform hover:scale-105 hover:bg-green-200 hover:shadow-2xl hover:border-green-300 hover:border" data-aos="zoom-in" data-aos-delay="100">
                            <div class="bg-green-100 p-4 rounded-full mb-6">
                                <i class="fas fa-shield-alt text-green-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">Keamanan Data Terjamin</h3>
                            <p class="text-gray-600 leading-relaxed text-center">
                                Data Anda aman dengan enkripsi tingkat tinggi dan kepatuhan privasi yang ketat.
                            </p>
                        </div>
                    </div>
                    <div class="mt-12" data-aos="fade-up" data-aos-delay="700">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Coba Semua Fitur Naima Sekarang
                            <i class="fas fa-magic ml-2"></i>
                        </a>
                    </div>
                </div>
            </section>

            {{-- ============================================================================================================= --}}
            {{-- BAGIAN PANDUAN --}}
            {{-- ============================================================================================================= --}}
            <section id="panduan" class="bg-gray-50 py-16 px-4">
                <div class="max-w-7xl mx-auto text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6" data-aos="fade-up">Panduan Penggunaan Naima Sustainability</h2>
                    <p class="text-gray-700 mb-10 text-lg max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                        Pelajari cara memanfaatkan Naima Sustainability secara maksimal dengan panduan video interaktif kami
                        dan artikel yang mudah dipahami.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col" data-aos="fade-up" data-aos-delay="200">
                            <div class="relative w-full" style="padding-top: 56.25%;"> {{-- 16:9 Aspect Ratio --}}
                                <iframe class="absolute top-0 left-0 w-full h-full"
                                    src="https://www.youtube.com/embed/FHxAjB05lU8?si=1cL1rlqiLpb26uio"
                                    title="YouTube video player" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Memulai: Registrasi & Dashboard</h3>
                                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                    Pelajari cara membuat akun, melengkapi profil perusahaan, dan memahami dashboard utama Naima.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col" data-aos="fade-up" data-aos-delay="300">
                            <div class="relative w-full" style="padding-top: 56.25%;">
                                <iframe class="absolute top-0 left-0 w-full h-full"
                                    src="https://www.youtube.com/embed/aj2npG432zc?si=0PIuAtBpipLh6LpJ"
                                    title="Panduan Naima Bagian 2" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Menghitung Jejak Karbon Anda</h3>
                                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                    Ikuti tutorial ini untuk memasukkan data perjalanan bisnis dan mendapatkan laporan jejak karbon Anda.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col" data-aos="fade-up" data-aos-delay="400">
                            <div class="relative w-full" style="padding-top: 56.25%;">
                                <iframe class="absolute top-0 left-0 w-full h-full"
                                    src="https://www.youtube.com/embed/aj2npG432zc?si=0PIuAtBpipLh6LpJ"
                                    title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen>
                                </iframe>
                            </div>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Analisis & Rekomendasi</h3>
                                <p class="text-gray-600 text-sm leading-relaxed mb-4">
                                    Interpretasikan hasil perhitungan Anda dan gunakan insight untuk merancang strategi keberlanjutan yang efektif.
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="mt-12 text-gray-700 text-lg" data-aos="fade-up" data-aos-delay="500">
                        Tidak menemukan jawaban yang Anda cari? Kunjungi bagian <a href="#faq" class="text-green-600 hover:underline font-semibold scroll-link">FAQ</a> kami
                        atau <a href="#hubungi-kami" class="text-green-600 hover:underline font-semibold scroll-link">hubungi kami</a> secara langsung.
                    </p>
                </div>
            </section>

            ---

            {{-- ============================================================================================================= --}}
            {{-- BAGIAN FAQ (NEW SECTION) --}}
            {{-- ============================================================================================================= --}}
            <section id="faq" class="bg-white py-16 px-4">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12" data-aos="fade-up">Pertanyaan yang Sering Diajukan (FAQ)</h2>

                    <div class="space-y-4">
                        <div class="accordion-item rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                            <button class="accordion-header flex justify-between items-center w-full text-lg font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 p-4 focus:outline-none">
                                Bagaimana cara Naima menghitung jejak karbon?
                                <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-300"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="text-gray-700 leading-relaxed p-4">
                                    Naima menggunakan metodologi standar industri yang diakui secara internasional untuk menghitung jejak karbon dari berbagai sumber, termasuk transportasi (udara, darat, laut), konsumsi energi, dan aktivitas operasional lainnya. Data yang Anda masukkan akan diproses menggunakan faktor emisi terbaru untuk memberikan estimasi yang akurat.
                                </p>
                            </div>
                        </div>

                        <div class="accordion-item rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                            <button class="accordion-header flex justify-between items-center w-full text-lg font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 p-4 focus:outline-none">
                                Apakah Naima cocok untuk bisnis kecil hingga besar?
                                <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-300"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="text-gray-700 leading-relaxed p-4">
                                    Ya, Naima dirancang dengan fleksibilitas untuk memenuhi kebutuhan bisnis dari berbagai skala. Fitur-fitur kami dapat disesuaikan, mulai dari startup hingga korporasi besar, memastikan setiap entitas dapat mengelola jejak karbonnya secara efisien.
                                </p>
                            </div>
                        </div>

                        <div class="accordion-item rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="300">
                            <button class="accordion-header flex justify-between items-center w-full text-lg font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 p-4 focus:outline-none">
                                Bagaimana keamanan data saya di Naima?
                                <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-300"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="text-gray-700 leading-relaxed p-4">
                                    Keamanan data adalah prioritas utama kami. Naima menggunakan enkripsi end-to-end, firewall canggih, dan praktik keamanan terbaik untuk melindungi semua informasi Anda. Kami juga patuh terhadap regulasi privasi data yang berlaku.
                                </p>
                            </div>
                        </div>

                        <div class="accordion-item rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                            <button class="accordion-header flex justify-between items-center w-full text-lg font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 p-4 focus:outline-none">
                                Bisakah saya mengintegrasikan Naima dengan sistem lain?
                                <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-300"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="text-gray-700 leading-relaxed p-4">
                                    Naima dirancang untuk kemungkinan integrasi melalui API. Silakan hubungi tim dukungan kami untuk mendiskusikan kebutuhan integrasi spesifik Anda.
                                </p>
                            </div>
                        </div>

                        <div class="accordion-item rounded-lg shadow-md overflow-hidden" data-aos="fade-up" data-aos-delay="500">
                            <button class="accordion-header flex justify-between items-center w-full text-lg font-semibold text-gray-800 bg-gray-50 hover:bg-gray-100 p-4 focus:outline-none">
                                Bagaimana cara mendapatkan dukungan jika saya memiliki masalah?
                                <i class="fas fa-chevron-down text-gray-500 transform transition-transform duration-300"></i>
                            </button>
                            <div class="accordion-content">
                                <p class="text-gray-700 leading-relaxed p-4">
                                    Anda bisa menghubungi tim dukungan kami melalui formulir kontak di halaman ini, email, atau telepon yang tertera. Tim kami siap membantu Anda dari hari Senin hingga Jumat, pukul 09.00 - 17.00 WIB.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            ---

            {{-- ============================================================================================================= --}}
            {{-- BAGIAN HUBUNGI KAMI --}}
            {{-- ============================================================================================================= --}}
            <section id="hubungi-kami" class="bg-gray-50 py-16 px-4">
                <div class="max-w-4xl mx-auto" data-aos="fade-up">
                    <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-6">Hubungi Kami</h2>
                    <p class="text-gray-700 text-center mb-10 text-lg max-w-2xl mx-auto">
                        Punya pertanyaan, saran, atau ingin berkolaborasi? Jangan ragu untuk menghubungi tim kami. Kami siap membantu Anda mencapai tujuan keberlanjutan bisnis Anda.
                    </p>

                    <div class="bg-white p-8 rounded-lg shadow-xl grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Informasi Kontak</h3>
                            <ul class="space-y-4 text-gray-700">
                                <li class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-green-600 mr-3 text-xl"></i>
                                    <span>Mustang No.14a, Dadok Tunggul Hitam, Kec. Koto Tangah, Kota Padang, Sumatera Barat 25586</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-phone-alt text-green-600 mr-3 text-xl"></i>
                                    <span>+62 812-3456-7890</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-envelope text-green-600 mr-3 text-xl"></i>
                                    <span>info@naimasustainability.com</span>
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-clock text-green-600 mr-3 text-xl"></i>
                                    <span>Senin - Jumat: 09:00 - 17:00 WIB</span>
                                </li>

                                {{--  Tautan WhatsApp di dalam Informasi Kontak --}}
                                <li class="flex items-center">
                                    <a href="https://wa.me/6281234567890?text=Halo%20Naima%2C%20saya%20ingin%20bertanya%20tentang%20layanan%20Anda."
                                    target="_blank"
                                    class="flex items-center text-green-600 hover:text-green-800 font-semibold transition-colors duration-200">
                                        <i class="fab fa-whatsapp text-green-600 mr-3 text-xl"></i>
                                        <span>Hubungi via WhatsApp</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="mt-8">
                                <h3 class="text-2xl font-bold text-gray-800 mb-4">Temukan Kami di Peta</h3>
                                <div class="w-full h-64 bg-gray-200 rounded-lg overflow-hidden shadow-md">
                                    {{-- Placeholder for Google Map --}}
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4030.8996190459575!2d100.3611164!3d-0.8835331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4c75a82229117%3A0xcfb0992b4f426421!2sMustang%20No.14a%2C%20Dadok%20Tunggul%20Hitam%2C%20Kec.%20Koto%20Tangah%2C%20Kota%20Padang%2C%20Sumatera%20Barat%2025586!5e1!3m2!1sid!2sid!4v1750511310239!5m2!1sid!2sid"
                                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                                    </iframe>
                                </div>
                            </div>
                        </div>

                        <div id="hubungi-kita" class="max-w-4xl mx-auto px-4 py-8"> {{-- div pembungkus form  --}}
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Kirim Pesan kepada Kami</h3>
                            <form action="{{ route('feedback.store') }}" method="POST" class="space-y-6">
                                @csrf
                                @if (session('success'))
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                                        <span class="block sm:inline">{{ session('success') }}</span>
                                    </div>
                                @endif

                                {{-- Tampilkan Pesan Error (dari validasi atau try-catch di controller) --}}
                                @if (session('error'))
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                        <span class="block sm:inline">{{ session('error') }}</span>
                                    </div>
                                @endif

                                {{-- Tampilkan Error Validasi Laravel --}}
                                @if ($errors->any())
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                        <strong class="font-bold">Ada beberapa masalah dengan input Anda:</strong>
                                        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div>
                                    <label for="name" class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                                    @error('name')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                                    <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="subject" class="block text-gray-700 text-sm font-semibold mb-2">Subjek</label>
                                    <input type="text" id="subject" name="subject" required value="{{ old('subject') }}"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">
                                    @error('subject')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="message" class="block text-gray-700 text-sm font-semibold mb-2">Pesan Anda</label>
                                    <textarea id="message" name="message" rows="5" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="bg-green-600 text-white font-bold py-3 px-6 rounded-md hover:bg-green-700 transition-colors duration-300 w-full md:w-auto">Kirim Pesan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div> {{-- Penutup div pt-[76px] --}}
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menu-toggle');


            menuToggle.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });

            // Smooth scroll for navigation links
            document.querySelectorAll('.scroll-link').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();

                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    const headerOffset = document.querySelector('header').offsetHeight; // Get dynamic header height
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });

                    // Close mobile menu if open
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                });
            });

            // Script untuk FAQ Accordion
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.addEventListener('click', () => {
                    const content = header.nextElementSibling;
                    const icon = header.querySelector('i');

                    // Tutup semua accordion lain kecuali yang diklik
                    document.querySelectorAll('.accordion-header').forEach(otherHeader => {
                        if (otherHeader !== header && otherHeader.classList.contains('active')) {
                            otherHeader.classList.remove('active');
                            otherHeader.nextElementSibling.classList.remove('open');
                            otherHeader.nextElementSibling.style.maxHeight = null;
                            otherHeader.nextElementSibling.style.opacity = 0;
                            otherHeader.querySelector('i').classList.remove('fa-chevron-up');
                            otherHeader.querySelector('i').classList.add('fa-chevron-down');
                        }
                    });

                    // Toggle accordion yang diklik
                    header.classList.toggle('active');
                    content.classList.toggle('open');
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-up');

                    if (content.classList.contains('open')) {
                        content.style.maxHeight = content.scrollHeight + "px"; // Set max-height based on content
                        content.style.opacity = 1;
                    } else {
                        content.style.maxHeight = null;
                        content.style.opacity = 0;
                    }
                });
            });
        });
    </script>
@endpush
