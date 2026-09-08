@extends('backend.layouts.app')

@section('title')
    {{ __('Admin Dashboard') }} | {{ config('app.name') }}
@endsection

@section('before_vite_build')
    <script>
        var userGrowthData = @json($user_growth_data['data']);
        var userGrowthLabels = @json($user_growth_data['labels']);
        // Data baru untuk grafik emisi
        var emissionChartData = @json($emission_chart_data['data']);
        var emissionChartLabels = @json($emission_chart_data['labels']);
    </script>
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-screen-2xl md:p-6"> {{-- Perbaikan: max-w-(--breakpoint-2xl) menjadi max-w-screen-2xl --}}
        <div x-data="{ pageName: '{{ __('Dashboard') }}' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Dashboard') }}</h2>
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12 space-y-6">
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-4 md:gap-6">
                    {{-- Kartu: Total Pengguna --}}
                    @include('backend.pages.dashboard.partials.card', [
                        'icon_svg' => asset('images/icons/user.svg'),
                        'label' => __('Users'),
                        'value' => $total_users,
                        'bg' => '#635BFF',
                        'class' => 'bg-white',
                        'url' => route('admin.users.index'),
                    ])
                    {{-- Kartu: Total Peran --}}
                    @include('backend.pages.dashboard.partials.card', [
                        'icon_svg' => asset('images/icons/key.svg'),
                        'label' => __('Roles'),
                        'value' => $total_roles,
                        'bg' => '#00D7FF',
                        'class' => 'bg-white',
                        'url' => route('admin.roles.index'),
                    ])
                    {{-- Kartu: Total Izin --}}
                    @include('backend.pages.dashboard.partials.card', [
                        'icon' => 'bi bi-shield-check',
                        'label' => __('Permissions'),
                        'value' => $total_permissions,
                        'bg' => '#FF4D96',
                        'class' => 'bg-white',
                        'url' => route('admin.permissions.index'),
                    ])
                    {{-- Kartu: Total Bahasa --}}
                    @include('backend.pages.dashboard.partials.card', [
                        'icon' => 'bi bi-translate',
                        'label' => __('Translations'),
                        'value' => $languages['total'] . ' / ' . $languages['active'],
                        'bg' => '#22C55E',
                        'class' => 'bg-white',
                        'url' => route('admin.translations.index'),
                    ])

                    {{-- NEW CARD: Total Perusahaan --}}
                    @include('backend.pages.dashboard.partials.card', [
                        'icon' => 'bi bi-building',
                        'label' => __('Total Perusahaan'),
                        'value' => $total_perusahaan,
                        'bg' => '#F97316',
                        'class' => 'bg-white',
                        'url' => route('admin.perusahaans.index')
                    ])

                    {{-- Card Total Emisi (Jika Anda ingin mengaktifkannya) --}}
                    {{-- @include('backend.pages.dashboard.partials.card', [
                        'icon' => 'bi bi-cloud-check',
                        'label' => __('Total Emisi'),
                        'value' => number_format($totalEmisi, 2) . ' kg CO₂',
                        'bg' => '#F97316',
                        'class' => 'bg-white',
                        'url' => route('admin.perhitungan.index'),
                    ]) --}}

                </div>
            </div>
        </div>

        {{-- Section for Emission Trend Chart --}}
        <div class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">{{ __('Grafik Tren Emisi Bulanan') }}</h3>
            <div class="relative h-80"> {{-- Add height for the chart --}}
                <canvas id="emissionChart"></canvas>
            </div>
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('Total emisi (kg CO₂) yang dicatat setiap bulan.') }}</p>
        </div>


        {{-- Quick Access to CRUD Modules (Uncomment if needed) --}}
        {{-- <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-white mb-4">{{ __('Quick Access to CRUD Modules') }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @php
                    $crudModules = [
                        ['label' => 'Perusahaans', 'route' => 'admin.perusahaans.index', 'icon' => 'bi bi-buildings', 'color' => '#0EA5E9'],
                        ['label' => 'Karyawans', 'route' => 'admin.karyawans.index', 'icon' => 'bi bi-person-badge', 'color' => '#9333EA'],
                        ['label' => 'Strategis', 'route' => 'admin.strategis.index', 'icon' => 'bi bi-lightbulb', 'color' => '#F59E0B'],
                        ['label' => 'bahan-bakar', 'route' => 'admin.bahan-bakar.index', 'icon' => 'bi bi-fuel-pump', 'color' => '#EF4444'],
                        ['label' => 'transportasi', 'route' => 'admin.transportasi.index', 'icon' => 'bi bi-truck-front', 'color' => '#10B981'],
                        ['label' => 'Feedbacks', 'route' => 'admin.feedbacks.index', 'icon' => 'bi bi-chat-left-text', 'color' => '#3B82F6'],
                        ['label' => 'Perjalanan Dinas', 'route' => 'admin.perjalanan-dinas.index', 'icon' => 'bi bi-geo-alt', 'color' => '#8B5CF6'],
                        ['label' => 'Perhitungan', 'route' => 'admin.perhitungan.index', 'icon' => 'bi bi-calculator', 'color' => '#F97316'],
                    ];
                @endphp

                @foreach ($crudModules as $modul)
                    <a href="{{ route($modul['route']) }}" class="rounded-xl p-4 text-white shadow hover:shadow-md transition-all duration-200 flex items-center gap-3" style="background-color: {{ $modul['color'] }}">
                        <i class="{{ $modul['icon'] }} text-xl"></i>
                        <span class="text-sm font-semibold">{{ __($modul['label']) }}</span>
                    </a>
                @endforeach
            </div>
        </div> --}}


        {{-- Grafik & History --}}
        <div class="mt-6">
            <div class="grid grid-cols-12 gap-4 md:gap-6">
                <div class="col-span-12">
                    <div class="grid grid-cols-12 gap-4 md:gap-6">
                        <div class="col-span-12 md:col-span-8">
                            @include('backend.pages.dashboard.partials.user-growth')
                        </div>
                        <div class="col-span-12 md:col-span-4">
                            @include('backend.pages.dashboard.partials.user-history')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart untuk Pertumbuhan Pengguna (existing)
            const userGrowthCtx = document.getElementById('userGrowthChart');
            if (userGrowthCtx) {
                new Chart(userGrowthCtx, {
                    type: 'line',
                    data: {
                        labels: userGrowthLabels,
                        datasets: [{
                            label: '{{ __("User Growth") }}',
                            data: userGrowthData,
                            backgroundColor: 'rgba(99, 91, 255, 0.2)',
                            borderColor: '#635BFF',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
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
                                    color: 'rgb(107, 114, 128)' // Tailwind gray-500
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(107, 114, 128)'
                                },
                                grid: {
                                    color: 'rgba(229, 231, 235, 0.2)' // Tailwind gray-200 with transparency
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(107, 114, 128)'
                                },
                                grid: {
                                    color: 'rgba(229, 231, 235, 0.2)'
                                }
                            }
                        }
                    }
                });
            }

            // NEW Chart for Emission Trend
            const emissionCtx = document.getElementById('emissionChart');
            if (emissionCtx) {
                new Chart(emissionCtx, {
                    type: 'bar',
                    data: {
                        labels: emissionChartLabels,
                        datasets: [{
                            label: '{{ __("Emisi (kg CO₂)") }}',
                            data: emissionChartData,
                            backgroundColor: 'rgba(249, 115, 22, 0.6)', // Tailwind orange-500 with transparency
                            borderColor: '#F97316',
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
                                    color: 'rgb(107, 114, 128)'
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(107, 114, 128)'
                                },
                                grid: {
                                    color: 'rgba(229, 231, 235, 0.2)'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    color: 'rgb(107, 114, 128)'
                                },
                                grid: {
                                    color: 'rgba(229, 231, 235, 0.2)'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
