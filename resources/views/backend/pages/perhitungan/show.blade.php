@extends('backend.layouts.app')

@section('title')
    {{ __('Detail Perhitungan Emisi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-screen-lg md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
            {{ __('Perjalanan Dinas') }}
        </h2>
        <a href="{{ route('admin.perhitungan.index') }}" class="btn-default">
            <i class="bi bi-arrow-left"></i> {{ __('Kembali') }}
        </a>
    </div>

    @php
        $metode = match ($perhitungan->metode) {
            'bahan_bakar' => 'Bahan Bakar',
            'jarak_tempuh' => 'Jarak Tempuh',
            'biaya' => 'Biaya',
            default => ucfirst($perhitungan->metode),
        };

        $jenis = match ($perhitungan->metode) {
            'bahan_bakar' => $perhitungan->bahanBakar->Bahan_bakar ?? '-',
            'jarak_tempuh' => $perhitungan->transportasi->jenis ?? '-',
            'biaya' => $perhitungan->biaya->jenisKendaraan ?? '-',
            default => '-',
        };

        $labelInput = match ($perhitungan->metode) {
            'bahan_bakar' => 'Konsumsi Bahan Bakar',
            'jarak_tempuh' => 'Jarak Tempuh',
            'biaya' => 'Biaya Transportasi',
            default => 'Nilai Input',
        };

        $satuan = match ($perhitungan->metode) {
            'bahan_bakar' => 'liter',
            'jarak_tempuh' => 'km',
            'biaya' => 'USD',
            default => '',
        };
    @endphp

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-6 text-sm text-gray-800 dark:text-white text-base">
        <div class="grid md:grid-cols-1 gap-x-12 gap-y-5 text-base space-y-4">
            <div class="flex gap-2">
                <div class="w-44 font-medium">Perusahaan</div>
                <div>:</div>
                <div>{{ $perhitungan->user->karyawan->perusahaan->nama ?? '-' }}</div>
            </div>

            <div class="flex gap-2">
                <div class="w-44 font-medium">Karyawan</div>
                <div>:</div>
                <div>{{ $perhitungan->user->name ?? '-' }}</div>
            </div>
            {{-- Kolom Kiri --}}
            <div class="flex gap-2">
                <div class="w-44 font-medium">Tujuan Perjalanan</div>
                <div>:</div>
                <div>{{ $perhitungan->titik_awal }} → {{ $perhitungan->titik_tujuan }}</div>
            </div>

            <div class="flex gap-2">
                <div class="w-44 font-medium">Metode</div>
                <div>:</div>
                <div>{{ $metode }}</div>
            </div>

            <div class="flex gap-2">
                <div class="w-44 font-medium">Kategori</div>
                <div>:</div>
                <div>{{ $perhitungan->kategori }}</div>
            </div>

            <div class="flex gap-2">
                <div class="w-44 font-medium">
                    @switch($perhitungan->metode)
                        @case('bahan_bakar') Jenis Bahan Bakar @break
                        @case('jarak_tempuh') Jenis Transportasi @break
                        @case('biaya') Jenis Biaya @break
                        @default Jenis
                    @endswitch
                </div>
                <div>:</div>
                <div>{{ $jenis }}</div>
            </div>

            <div class="flex gap-2">
                <div class="w-44 font-medium">{{ $labelInput }}</div>
                <div>:</div>
                <div>{{ number_format($perhitungan->nilai_input, 2) }} {{ $satuan }}</div>
            </div>

            <div class="flex gap-2">
                <div class="w-44 font-medium">Jumlah Orang</div>
                <div>:</div>
                <div>{{ $perhitungan->jumlah_orang }}</div>
            </div>



            <div class="flex gap-2">
                <div class="w-44 font-medium">Tanggal</div>
                <div>:</div>
                <div>{{ \Carbon\Carbon::parse($perhitungan->tanggal)->translatedFormat('d F Y') }}</div>
            </div>

        </div>

        <div class="flex gap-2 border-t mt-6 pt-4 items-center">
            <div class="w-44 font-bold text-lg">Total Emisi</div>
            <div>:</div>
            <div class="text-green-600 dark:text-green-400 text-xl font-bold">
                {{ number_format($perhitungan->hasil_emisi, 4) }} kg CO₂e
            </div>
        </div>
    </div>
</div>
@endsection
