@extends('backend.layouts.app') {{-- Sesuaikan dengan layout Anda jika berbeda --}}

@section('admin-content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">{{ __('report_title') }}</h1>

    {{-- Filter Section --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-700 dark:text-white mb-4">{{ __('filter_report') }}</h2>
        <form id="filterForm" action="{{ route('admin.laporan.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
            <div>
                <label for="bulan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('month') }}:</label>
                <select name="bulan" id="bulan"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md
                           dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int)$bulan === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month((int)$m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="tahun" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('year') }}:</label>
                <select name="tahun" id="tahun"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-md
                           dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach ($availableYears as $year)
                        <option value="{{ $year }}" {{ (int)$tahun === $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                       dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-blue-600">
                {{ __('show_report') }}
            </button>
        </form>
    </div>

    {{-- Laporan Perusahaan --}}
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700 dark:text-white">
                {{ __('company_report') }} ({{ __('month_label') }} {{ \Carbon\Carbon::createFromDate((int)$tahun, (int)$bulan)->translatedFormat('F') }} {{ __('year_label') }} {{ (int)$tahun }}) {{-- PERBAIKAN DI SINI --}}
            </h2>
            {{-- Export Buttons --}}
            <div class="space-x-2">
                {{-- CSV (Hijau Tua) --}}
                <a href="{{ route('admin.laporan.exportCsv', ['bulan' => $bulan, 'tahun' => $tahun,'lang' => App::getLocale()]) }}"
                    class="px-4 py-2 rounded-md text-sm font-medium
                           bg-green-700 text-white hover:bg-green-800
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600
                           dark:bg-green-800 dark:hover:bg-green-700 dark:focus:ring-green-700">
                    {{ __('export_csv') }}
                </a>
                {{-- Excel (Hijau Muda) --}}
                <a href="{{ route('admin.laporan.exportExcel', ['bulan' => $bulan, 'tahun' => $tahun,'lang' => App::getLocale()]) }}"
                    class="px-4 py-2 rounded-md text-sm font-medium
                           bg-green-500 text-white hover:bg-green-600
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-400
                           dark:bg-green-600 dark:hover:bg-green-500 dark:focus:ring-green-500">
                    {{ __('export_excel') }}
                </a>
                {{-- PDF (Merah) --}}
                <a href="{{ route('admin.laporan.exportPdf', ['bulan' => $bulan, 'tahun' => $tahun,'lang' => App::getLocale()]) }}"
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
                            {{ __('company_name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('total_emission_kg') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('daily_average_kg') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('number_of_trips') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('period') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ __('action') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($perusahaans as $index => $perusahaan)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $perusahaan->nama }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ number_format($perusahaan->total_emisi_filtered, 2, ',', '.') }} kg
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ number_format($perusahaan->rata_rata_harian, 2, ',', '.') }} kg
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $perusahaan->total_perjalanan }} {{ __('kali') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ \Carbon\Carbon::createFromDate((int)$tahun, (int)$bulan)->translatedFormat('F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.laporan.detail', ['perusahaan' => $perusahaan->id, 'bulan' => $bulan, 'tahun' => $tahun]) }}"
                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-600">
                                    {{ __('detail') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                {{ __('no_company_data') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Breakdown Emisi Section --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Breakdown per Bahan Bakar --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">{{ __('emission_by_fuel') }} ({{ __('month_label') }} {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} {{ __('year_label') }} {{ (int)$tahun }})</h3> {{-- PERBAIKAN DI SINI --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('fuel_type') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('total_emission_kg') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($emisiPerBahanBakar as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $data->nama_bakar }} ({{ __($data->kategori) }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($data->total_emisi, 2, ',', '.') }} kg
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('no_fuel_emission_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Breakdown per Transportasi --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">{{ __('emission_by_transport') }} ({{ __('month_label') }} {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }} {{ __('year_label') }} {{ (int)$tahun }})</h3> {{-- PERBAIKAN DI SINI --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('transport_type') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('total_emission_kg') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($emisiPerTransportasi as $data)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $data->nama_transportasi }} ({{ __($data->kategori) }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($data->total_emisi, 2, ',', '.') }} kg
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('no_transport_emission_data') }}
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
