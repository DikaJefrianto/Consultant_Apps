@extends('backend.layouts.app')

@section('title')
    {{ __('Detail Perusahaan') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Detail Perusahaan') }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.perusahaans.index') }}" class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Perusahaan') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">
                    {{ $perusahaan->nama }}
                </li>
            </ol>
        </nav>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
        <div class="space-y-4">
            <div>
                <h4 class="text-lg font-medium text-gray-800 dark:text-white">{{ __('Nama') }}</h4>
                <p class="text-gray-600 dark:text-gray-300">{{ $perusahaan->nama }}</p>
            </div>

            <div>
                <h4 class="text-lg font-medium text-gray-800 dark:text-white">{{ __('Email') }}</h4>
                <p class="text-gray-600 dark:text-gray-300">{{ $perusahaan->email }}</p>
            </div>

            <div>
                <h4 class="text-lg font-medium text-gray-800 dark:text-white">{{ __('Alamat') }}</h4>
                <p class="text-gray-600 dark:text-gray-300">{{ $perusahaan->alamat }}</p>
            </div>

            <div>
                <h4 class="text-lg font-medium text-gray-800 dark:text-white">{{ __('Keterangan') }}</h4>
                <p class="text-gray-600 dark:text-gray-300">{{ $perusahaan->keterangan }}</p>
            </div>

            <div>
                <h4 class="text-lg font-medium text-gray-800 dark:text-white">{{ __('Jumlah Karyawan') }}</h4>
                <p class="text-gray-600 dark:text-gray-300">{{ $perusahaan->karyawans_count ?? '0' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
