@extends('backend.layouts.app')

@section('title')
    {{ __('Perhitungan Emisi') }} | {{ config('app.name') }}
@endsection

{{-- Tambahkan CSS kustom di sini --}}
@section('styles')
    <style>
        /* Menyembunyikan ikon kalender bawaan di beberapa browser */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            opacity: 0; /* Membuatnya tidak terlihat */
            position: absolute; /* Posisikan secara absolut untuk overlay */
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 1; /* Pastikan berada di atas ikon kustom */
        }

        /* Penyesuaian untuk Firefox */
        input[type="datetime-local"]::-moz-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
            z-index: 1;
        }

        /* Penyesuaian umum untuk tema gelap pada indikator picker (jika masih terlihat) */
        /* Filter ini bisa opsional, tergantung seberapa baik opacity:0 bekerja */
        .dark input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            /* filter: invert(1); */ /* Ini bisa menyebabkan masalah jika tidak sepenuhnya tersembunyi */
        }
    </style>
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-screen-xl md:p-6">
        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ __('Perhitungan Emisi') }}</h2>
            </div>

            {{-- Step 1: Pilih Metode --}}
            <form action="{{ route('admin.perhitungan.create') }}" method="GET">
                <h3 class="text-lg font-semibold text-center mb-4 text-gray-700 dark:text-white">
                    {{ __('Pilih Metode Perhitungan Emisi') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($metodeOptions as $option)
                        <button type="submit" name="metode" value="{{ $option['value'] }}"
                            class="border-2 rounded-xl p-4 shadow-sm transition-all hover:shadow-md text-center bg-white dark:bg-gray-900 {{ request('metode') == $option['value'] ? 'border-primary' : 'border-gray-200 dark:border-gray-700' }}">
                            <div class="space-y-2">
                                <h5 class="text-lg font-semibold {{ $option['color'] }} dark:text-white">
                                    <i class="bi {{ $option['icon'] }}"></i> {{ __($option['text']) }}
                                </h5>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __($option['desc']) }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>

            </form>

            {{-- Step 2: Pilih Kategori --}}
            @if (isset($metode) && $metode)
                <form action="{{ route('admin.perhitungan.create') }}" method="GET">
                    <input type="hidden" name="metode" value="{{ $metode }}">
                    <h3 class="text-lg font-semibold text-center mb-4 text-gray-700 dark:text-white">
                        {{ __('Pilih Kategori Transportasi') }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach (['Darat' => 'truck', 'Laut' => 'ship', 'Udara' => 'airplane-engines'] as $label => $icon)
                            <button type="submit" name="kategori" value="{{ $label }}"
                                class="border-2 rounded-xl p-4 shadow-sm transition-all hover:shadow-md text-center bg-white dark:bg-gray-900 {{ request('kategori') == $label ? 'border-primary' : 'border-gray-200 dark:border-gray-700' }}">
                                <div class="space-y-2">
                                    <h5 class="text-lg font-semibold text-gray-700 dark:text-white">
                                        <i class="bi bi-{{ $icon }}"></i> {{ __($label) }}
                                    </h5>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        @switch($label)
                                            @case('Darat')
                                                {{ __('Mobil, motor, kereta, dll.') }}
                                            @break

                                            @case('Laut')
                                                {{ __('Kapal, ferry, dll.') }}
                                            @break

                                            @case('Udara')
                                                {{ __('Pesawat, helikopter, dll.') }}
                                            @break
                                        @endswitch
                                    </p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </form>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="bg-red-100 dark:bg-red-800/20 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative"
                    role="alert">
                    <strong class="font-bold">{{ __('Terjadi kesalahan!') }}</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Step 3: Form Perhitungan --}}
            @if (isset($metode) && isset($kategori))
                <form action="{{ route('admin.perhitungan.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="kategori" value="{{ $kategori }}">
                    <input type="hidden" name="metode" value="{{ $metode }}">

                    {{-- Dynamic Form --}}
                    @if ($metode == 'bahan_bakar')
                        <div>
                            <label for="Bahan_bakar"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jenis Bahan Bakar') }}</label>
                            <select name="Bahan_bakar" id="Bahan_bakar" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                                @foreach ($bahanBakar as $bb)
                                    <option value="{{ $bb->id }}">{{ $bb->Bahan_bakar }} ({{ __($bb->kategori) }}) {{-- Ditambahkan __() untuk terjemahan kategori --}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nilai_input"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jumlah Konsumsi (Liter)') }}</label>
                            <input type="number" step="0.01" name="nilai_input" id="nilai_input" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                        </div>
                    @elseif ($metode == 'jarak_tempuh')
                        <div>
                            <label for="jenis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jenis Kendaraan') }}</label>
                            <select name="jenis" id="jenis" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                                @foreach ($jenis as $trans)
                                    <option value="{{ $trans->id }}">{{ $trans->jenis }} ({{ __($trans->kategori) }}) {{-- Ditambahkan __() untuk terjemahan kategori --}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nilai_input"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jarak Tempuh (km)') }}</label>
                            <input type="number" step="0.01" name="nilai_input" id="nilai_input" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                        </div>
                    @elseif ($metode == 'biaya')
                        <div>
                            <label for="jenisKendaraan"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jenis Biaya') }}</label>
                            <select name="jenisKendaraan" id="jenisKendaraan" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                                @foreach ($jenisKendaraan as $b)
                                    <option value="{{ $b->id }}">{{ $b->jenisKendaraan }} ({{ __($b->kategori) }}) {{-- Ditambahkan __() untuk terjemahan kategori --}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="nilai_input"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jumlah Biaya (Rp)') }}</label>
                            <input type="number" step="0.01" name="nilai_input" id="nilai_input" required
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                        </div>
                    @endif
                    <div>
                        <label for="titik_awal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titik Awal') }}</label>
                        <input type="text" name="titik_awal" id="titik_awal" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white"
                            placeholder="{{ __('Lokasi Awal Perjalanan anda') }}">
                    </div>

                    <div>
                        <label for="titik_tujuan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titik Tujuan') }}</label>
                        <input type="text" name="titik_tujuan" id="titik_tujuan" required
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white"
                            placeholder="{{ __('Tujuan Perjalanan anda') }}">
                    </div>

                    <div>
                        <label for="jumlah_orang" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jumlah Orang') }}</label>
                        <input type="number" name="jumlah_orang" id="jumlah_orang" value="1" min="1"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label for="tanggal"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tanggal') }}</label>
                        <input type="datetime-local" name="tanggal" id="tanggal"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm dark:bg-gray-900 dark:text-white">
                    </div>

                    <div class="flex justify-start gap-3">
                        <button type="submit" class="btn-success">
                            <i class="bi bi-calculator"></i> {{ __('Hitung & Simpan') }}
                        </button>
                        <a href="{{ route('admin.perhitungan.index') }}" class="btn-default">
                            {{ __('Batal') }}
                        </a>
                    </div>
                </form>
            @endif

        </div>
    </div>
@endsection

@section('scripts')
    {{-- Ini adalah contoh skrip untuk mengelola tema gelap/terang --}}
    {{-- Jika Anda sudah memiliki mekanisme tema, Anda tidak perlu bagian ini atau sesuaikan --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const htmlElement = document.documentElement; // Elemen <html> biasanya digunakan untuk kelas 'dark'
            // Contoh tombol toggle tema, jika ada di layout Anda
            const themeToggleButton = document.getElementById('theme-toggle');

            function applyTheme(isDark) {
                if (isDark) {
                    htmlElement.classList.add('dark');
                } else {
                    htmlElement.classList.remove('dark');
                }
            }

            // Membaca preferensi tema dari local storage atau sistem
            const currentTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            applyTheme(currentTheme === 'dark');

            if (themeToggleButton) {
                themeToggleButton.addEventListener('click', () => {
                    const isDark = htmlElement.classList.contains('dark');
                    applyTheme(!isDark);
                    localStorage.setItem('theme', !isDark ? 'dark' : 'light');
                });
            }
        });
    </script>
@endsection
