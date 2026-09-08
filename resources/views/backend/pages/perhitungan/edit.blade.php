@extends('backend.layouts.app')

@section('title')
    {{ __('Edit Perhitungan Emisi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-screen-xl md:p-6">
        <div class="space-y-6">

            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">{{ __('Edit Perhitungan Emisi') }}</h2>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">{{ __('Terjadi kesalahan!') }}</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.perhitungan.update', $perhitungan->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <input type="hidden" name="kategori" value="{{ $perhitungan->kategori }}">
                <input type="hidden" name="metode" value="{{ $perhitungan->metode }}">

                @if ($perhitungan->metode === 'bahan_bakar')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jenis Bahan Bakar') }}</label>
                        <select name="Bahan_bakar"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700">
                            @foreach ($bahanBakar as $bb)
                                <option value="{{ $bb->id }}"
                                    {{ $bb->id == $perhitungan->Bahan_bakar ? 'selected' : '' }}>
                                    {{ __($bb->Bahan_bakar) }} ({{ __($bb->kategori) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif ($perhitungan->metode === 'jarak_tempuh')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jenis Kendaraan') }}</label>
                        <select name="jenis"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700">
                            @foreach ($jenis as $trans)
                                <option value="{{ $trans->id }}"
                                    {{ $trans->id == $perhitungan->jenis ? 'selected' : '' }}>
                                    {{ __($trans->jenis) }} ({{ __($trans->kategori) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif ($perhitungan->metode === 'biaya')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jenis Biaya') }}</label>
                        <select name="jenisKendaraan"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700">
                            @foreach ($jenisKendaraan as $b)
                                <option value="{{ $b->id }}"
                                    {{ $b->id == $perhitungan->jenisKendaraan ? 'selected' : '' }}>
                                    {{ __($b->jenisKendaraan) }} ({{ __($b->kategori) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nilai Input') }}</label>
                    <input type="number" name="nilai_input" step="0.01" value="{{ $perhitungan->nilai_input }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titik Awal') }}</label>
                    <input type="text" name="titik_awal" value="{{ old('titik_awal', $perhitungan->titik_awal) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700"
                        placeholder="{{ __('Contoh: Padang') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Titik Tujuan') }}</label>
                    <input type="text" name="titik_tujuan" value="{{ old('titik_tujuan', $perhitungan->titik_tujuan) }}"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700"
                        placeholder="{{ __('Contoh: Bukittinggi') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Jumlah Orang') }}</label>
                    <input type="number" name="jumlah_orang" min="1" value="{{ $perhitungan->jumlah_orang }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Tanggal') }}</label>
                    <input type="date" name="tanggal" value="{{ $perhitungan->tanggal }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:bg-gray-900 dark:text-white dark:border-gray-700">
                </div>

                <div class="flex justify-start gap-3">
                    <button type="submit" class="btn-success">
                        <i class="bi bi-save2"></i> {{ __('Perbarui') }}
                    </button>
                    <a href="{{ route('admin.perhitungan.index') }}" class="btn-default">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
