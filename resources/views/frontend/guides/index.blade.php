@extends('home') {{-- Pastikan ini extend ke layout utama aplikasi Anda --}}

@section('title', 'Panduan Lengkap - Nama Aplikasi Anda')

@section('content')
    <div class="container mx-auto px-4 py-8 bg-gray-100 text-gray-900 min-h-screen">
        <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Panduan Lengkap</h1>
        {{-- BAGIAN UTAMA: TABEL PANDUAN  --}}
        <section class="mb-12">
            <div class="overflow-x-auto shadow-lg rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Kolom Judul Panduan --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Judul Panduan
                            </th>
                            {{-- Kolom Kategori --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori
                            </th>
                            {{-- Kolom Deskripsi Singkat --}}
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deskripsi
                            </th>
                            {{-- Kolom File (untuk unduh) --}}
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                File
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($guides as $guide)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Data Judul --}}
                                <td class="px-6 py-4 whitespace-nowrap text-s font-medium text-gray-900">
                                    {{ $guide->title }}
                                </td>
                                {{-- Data Kategori --}}
                                <td class="px-6 py-4 whitespace-nowrap text-s text-gray-500">
                                    {{ $guide->category ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-s text-gray-500">
                                    {{ Str::limit($guide->description, 100) ?? '-' }}
                                </td>
                                {{-- Data File --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                    @if ($guide->file_path)
                                        <a href="{{ $guide->file_url }}"
                                            download="{{ basename($guide->file_path) }}" {{-- <<< TAMBAHKAN INI --}}
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-900 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Unduh
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Belum ada panduan yang tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{--bagian satu--}}
            <div class="max-w-7xl mx-auto text-center mt-40">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6" data-aos="fade-up">Panduan Penggunaan Naima Sustainability</h2>
                    <p class="text-gray-700 mb-10 text-lg max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="100">
                        Pelajari cara memanfaatkan Naima Sustainability secara maksimal dengan panduan video interaktif kami
                        dan artikel yang mudah dipahami.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col" data-aos="fade-up" data-aos-delay="200">
                            <div class="relative w-full" style="padding-top: 56.25%;"> {{-- 16:9 Aspect Ratio --}}
                                <iframe class="absolute top-0 left-0 w-full h-full"
                                    src="https://www.youtube.com/embed/aj2npG432zc?si=0PIuAtBpipLh6LpJ"
                                    title="Panduan Naima Bagian 1" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
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
                </div>
                {{--Bagian dua--}}
                <div class="container mx-auto px-4 max-w-7xl mt-20">
                    <div class="text-center mb-10" data-aos="fade-down">
                        <h2 class="text-3xl md:text-3xl font-bold text-green-800 mb-4 mt-4">
                            Langkah langkah penggunaan sistem
                        </h2>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                        <div class="order-2 md:order-1 text-left md:text-left" data-aos="fade-right">
                            <p class="text-gray-700 leading-relaxed text-lg">
                                Langkah pertama yang harus anda lakukan adalah akses website atau url dari NaimaSustainabilty. Disini kamu akan melihat home page dari sistem ini. Di awal kamu akan di suguhkan alasan kenapa harus pilih NaimaSustainabilty.
                            </p>
                        </div>
                        <div class="order-1 md:order-2 text-center" data-aos="fade-left">
                            <img src="/images/panduan1.png" alt="Responsible Business" class="mx-auto section-image rounded-lg w-100" style="max-width: 500px; height: auto;">
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                        <div class="order-2 md:order-1 text-left md:text-left" data-aos="fade-right">
                            <p class="text-gray-700 leading-relaxed text-lg">
                                Langkah kedu  adalah scrol website sampai ke bawah hingga kamu menemukan akses hubungi kami, atau di bagian header klik navigasi hubungikami. Silahkan cari tombol hubungi WhatsApp yang telah tersedia, maka ketika klik tombol tersebut anda akan di arahkan ke WhatsAppAdmin.
                            </p>
                        </div>
                        <div class="order-1 md:order-2 text-center" data-aos="fade-left">
                            <img src="/images/panduan2.png" alt="Responsible Business" class="mx-auto section-image rounded-lg w-100" style="max-width: 500px; height: auto;">
                        </div>
                    </div>
                </div>
                {{--akhir bagian dua--}}
        </section>
    </div>
@endsection
