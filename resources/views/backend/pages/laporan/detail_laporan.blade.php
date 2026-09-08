@extends('backend.layouts.app')

@section('title')
    {{ __('Detail Laporan Perusahaan') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-screen-xl md:p-6">
        <div class="space-y-6">

            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                    {{ __('Detail Laporan untuk') }} {{ $perusahaan->nama }}
                </h2>
                <a href="{{ route('admin.laporan.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
                   dark:border-gray-600 dark:text-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600">
                    <i class="bi bi-arrow-left mr-2"></i> {{ __('return_to_main_report') }}
                </a>
            </div>

            {{-- Ringkasan Laporan Perusahaan --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-8">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">
                    {{ __('summary_period') }}: {{ \Carbon\Carbon::createFromDate((int)$tahun, (int)$bulan)->translatedFormat('F Y') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_emission') }}</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($totalEmisi, 2, ',', '.') }} kg CO₂</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('daily_average') }}</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($rataRataHarian, 2, ',', '.') }} kg CO₂</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('number_of_trips_short') }}</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalPerjalanan }} {{ __('kali') }}</p> {{-- PERBAIKAN DI SINI --}}
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('total_related_cost') }}</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Tabel Detail Perhitungan Emisi --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-white">{{ __('list_detailed_emission_calculations') }}</h3>

                    {{-- Tombol untuk halaman Detail Laporan (tambahan baru) --}}
                    <div class="space-x-2">
                        {{-- CSV Detail (Hijau Tua) --}}
                        <a href="{{ route('admin.laporan.detail.exportCsv', ['perusahaan' => $perusahaan->id, 'bulan' => $bulan, 'tahun' => $tahun,'lang' => App::getLocale()]) }}"
                            class="px-4 py-2 rounded-md text-sm font-medium
                                   bg-green-700 text-white hover:bg-green-800
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600
                                   dark:bg-green-800 dark:hover:bg-green-700 dark:focus:ring-green-700">
                            {{ __('export_csv') }}
                        </a>
                        {{-- Excel Detail (Hijau Muda) --}}
                        <a href="{{ route('admin.laporan.detail.exportExcel', ['perusahaan' => $perusahaan->id, 'bulan' => $bulan, 'tahun' => $tahun,'lang' => App::getLocale()]) }}"
                            class="px-4 py-2 rounded-md text-sm font-medium
                                   bg-green-500 text-white hover:bg-green-600
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400
                                   dark:bg-green-600 dark:hover:bg-green-500 dark:focus:ring-green-500">
                            {{ __('export_excel') }}
                        </a>
                        {{-- PDF Detail (Merah) --}}
                        <a href="{{ route('admin.laporan.detail.exportPdf', ['perusahaan' => $perusahaan->id, 'bulan' => $bulan, 'tahun' => $tahun , 'lang' => App::getLocale()]) }}"
                            class="px-4 py-2 rounded-md text-sm font-medium
                                   bg-red-600 text-white hover:bg-red-700
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500
                                   dark:bg-red-700 dark:hover:bg-red-600 dark:focus:ring-red-600">
                            {{ __('export_pdf') }}
                        </a>
                    </div>

                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('no') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('date') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('employee') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('method') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('type_bb_transport_cost') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('input_value') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('number_of_people') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('emission_kg') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('related_cost_rp') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('route') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($detailPerhitungans as $index => $perhitungan)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $perhitungan->tanggal->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $perhitungan->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ __($perhitungan->metode) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        @if ($perhitungan->metode == 'bahan_bakar' && $perhitungan->bahanBakar)
                                            {{ $perhitungan->bahanBakar->Bahan_bakar }} ({{ __($perhitungan->bahanBakar->kategori) }})
                                        @elseif ($perhitungan->metode == 'jarak_tempuh' && $perhitungan->transportasi)
                                            {{ $perhitungan->transportasi->jenis }} ({{ __($perhitungan->transportasi->kategori) }})
                                        @elseif ($perhitungan->metode == 'biaya' && $perhitungan->biaya)
                                            {{ $perhitungan->biaya->jenisKendaraan }} ({{ __($perhitungan->biaya->kategori) }})
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ number_format($perhitungan->nilai_input, 2, ',', '.') }}
                                        @if ($perhitungan->metode == 'bahan_bakar') {{ __('Liter') }}
                                        @elseif ($perhitungan->metode == 'jarak_tempuh') {{ __('km') }}
                                        @elseif ($perhitungan->metode == 'biaya') {{ __('Rp') }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $perhitungan->jumlah_orang }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ number_format($perhitungan->hasil_emisi, 2, ',', '.') }} {{ __('kg') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        Rp {{ number_format($perhitungan->biaya->factorEmisi ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $perhitungan->titik_awal }} - {{ $perhitungan->titik_tujuan }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('no_detailed_emission_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
