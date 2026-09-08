@extends('backend.layouts.app')

@section('title')
    {{ __('Dashboard Karyawan') }} | {{ config('app.name') }}
@endsection

@section('before_vite_build')
    <script>
        // Data untuk grafik emisi karyawan (spesifik untuk karyawan yang login)
        var emissionChartData = @json($emission_chart_data['data']);
        var emissionChartLabels = @json($emission_chart_data['labels']);
    </script>
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-screen-2xl md:p-6">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                {{ __('Selamat Datang, Karyawan') }} {{ $karyawan->nama_lengkap ?? ""}}!
            </h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                            {{ __('Home') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Dashboard') }}</li>
                </ol>
            </nav>
        </div>

        <div class="space-y-6">
            {{-- Ringkasan Informasi Karyawan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {{-- Card: Total Perjalanan Pribadi --}}
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">{{ __('Total Perjalanan Pribadi') }}</h3>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($total_perjalanan_karyawan ?? 0) }} {{ __('kali') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Jumlah perjalanan yang Anda laporkan.') }}</p>
                </div>

                {{-- Card: Total Emisi Pribadi (Bulan Ini) --}}
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">{{ __('Total Emisi Pribadi') }} (Bulan Ini)</h3>
                    <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ number_format($total_emisi_karyawan ?? 0, 2, ',', '.') }} kg CO₂</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Total emisi karbon Anda untuk bulan ini.') }}</p>
                </div>

                {{-- Placeholder untuk card tambahan jika diperlukan --}}
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 shadow-md">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-2">{{ __('Target Emisi Anda') }}</h3>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ __('Belum Ada') }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Atur target untuk mengurangi jejak karbon Anda.') }}</p>
                </div>
            </div>

            {{-- Grafik Tren Emisi Pribadi --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">{{ __('Grafik Tren Emisi Pribadi Bulanan') }}</h3>
                <div class="relative h-80">
                    <canvas id="emissionChart"></canvas>
                </div>
                <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('Total emisi (kg CO₂) yang Anda catat setiap bulan.') }}</p>
            </div>

            {{-- Aktivitas Perhitungan Terbaru (Pribadi) --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 sm:p-8">
                <h3 class="text-lg font-medium text-gray-700 dark:text-white mb-4">{{ __('Aktivitas Perhitungan Terbaru Anda') }}</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Tanggal') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Metode') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Emisi (kg CO₂)') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    {{ __('Rute') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($aktivitas_terbaru_karyawan ?? [] as $aktivitas)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ \Carbon\Carbon::parse($aktivitas->tanggal)->translatedFormat('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ __($aktivitas->metode) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-500 dark:text-green-400 font-semibold">
                                        {{ number_format($aktivitas->hasil_emisi, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                        {{ $aktivitas->titik_awal }} - {{ $aktivitas->titik_tujuan }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        {{ __('Belum ada aktivitas perhitungan terbaru dari Anda.') }}
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart untuk Tren Emisi Pribadi Karyawan
            const emissionCtx = document.getElementById('emissionChart');
            if (emissionCtx) {
                new Chart(emissionCtx, {
                    type: 'bar', // Menggunakan chart batang
                    data: {
                        labels: emissionChartLabels,
                        datasets: [{
                            label: '{{ __("Emisi (kg CO₂)") }}',
                            data: emissionChartData,
                            backgroundColor: 'rgba(59, 130, 246, 0.6)', // Warna biru Tailwind dengan transparansi
                            borderColor: '#3B82F6', // Warna biru Tailwind
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    color: 'rgb(107, 114, 128)' // Warna teks legend (gray-500)
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(107, 114, 128)' // Warna teks sumbu X
                                },
                                grid: {
                                    color: 'rgba(229, 231, 235, 0.2)' // Warna grid sumbu X dengan transparansi
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(107, 114, 128)' // Warna teks sumbu Y
                                },
                                grid: {
                                    color: 'rgba(229, 231, 235, 0.2)' // Warna grid sumbu Y dengan transparansi
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
