@extends('layouts.app')

@section('title')
    {{ __('Tentang Kami') }} | {{ config('app.name') }}
@endsection

@section('content')
    <section class="bg-white py-16 px-4">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-10">Tentang Naima Sustainability</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Visi Kami</h2>
                    <p class="text-gray-600 leading-relaxed">
                        Menjadi platform terkemuka yang memberdayakan setiap organisasi untuk mengukur,
                        mengelola, dan mengurangi jejak karbon mereka, mendorong masa depan yang lebih
                        hijau dan berkelanjutan. Kami percaya bahwa setiap langkah kecil menuju
                        keberlanjutan adalah kontribusi besar bagi planet kita.
                    </p>
                </div>
                <div class="text-center">
                    <img src="/images/vision.svg" alt="Our Vision" class="mx-auto w-full max-w-sm rounded-lg shadow-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
                <div class="text-center md:order-2"> {{-- Order 2 untuk menukar posisi di mobile --}}
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Misi Kami</h2>
                    <ul class="list-disc list-inside text-gray-600 leading-relaxed text-left pl-4">
                        <li>Menyediakan alat perhitungan jejak karbon yang akurat dan mudah digunakan.</li>
                        <li>Memberikan wawasan dan strategi actionable untuk pengurangan emisi.</li>
                        <li>Membangun komunitas yang sadar lingkungan dan aktif berkontribusi.</li>
                        <li>Memfasilitasi pelaporan keberlanjutan yang transparan dan kredibel.</li>
                    </ul>
                </div>
                <div class="text-center md:order-1"> {{-- Order 1 untuk menukar posisi di mobile --}}
                    <img src="/images/mission.svg" alt="Our Mission" class="mx-auto w-full max-w-sm rounded-lg shadow-lg">
                </div>
            </div>

            <div class="text-center my-16">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tim Kami</h2>
                <p class="text-gray-600 mb-8 max-w-3xl mx-auto">
                    Kami adalah tim profesional yang bersemangat tentang keberlanjutan, teknologi, dan inovasi.
                    Setiap anggota tim membawa keahlian unik untuk mewujudkan visi Naima Sustainability.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-gray-100 rounded-lg shadow-md p-6 transform hover:scale-105 transition-transform duration-300">
                        <img src="https://via.placeholder.com/100" alt="Nama Anggota" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold text-gray-800">Nama Anggota 1</h3>
                        <p class="text-green-600 text-sm">CEO & Co-Founder</p>
                        <p class="text-gray-600 text-xs mt-2">Berpengalaman lebih dari 10 tahun di bidang teknologi lingkungan.</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg shadow-md p-6 transform hover:scale-105 transition-transform duration-300">
                        <img src="https://via.placeholder.com/100" alt="Nama Anggota" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold text-gray-800">Nama Anggota 2</h3>
                        <p class="text-green-600 text-sm">Lead Developer</p>
                        <p class="text-gray-600 text-xs mt-2">Arsitek di balik platform Naima, dengan fokus pada efisiensi.</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg shadow-md p-6 transform hover:scale-105 transition-transform duration-300">
                        <img src="https://via.placeholder.com/100" alt="Nama Anggota" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                        <h3 class="text-xl font-semibold text-gray-800">Nama Anggota 3</h3>
                        <p class="text-green-600 text-sm">Environmental Consultant</p>
                        <p class="text-gray-600 text-xs mt-2">Pakar dalam metodologi perhitungan jejak karbon dan regulasi.</p>
                    </div>
                    </div>
            </div>
        </div>
    </section>
@endsection
