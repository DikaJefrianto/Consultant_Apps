@extends('backend.layouts.app')

@section('title')
    {{ __('Ajukan Konsultasi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Ajukan Permintaan Konsultasi') }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.konsultasis.index') }}">
                        {{ __('Konsultasi') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Ajukan') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        @include('backend.layouts.partials.messages')

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-4">{{ __('Form Pengajuan Konsultasi') }}</h3>

            <form action="{{ route('admin.konsultasis.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="topik" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Topik Konsultasi') }} <span class="text-red-500">*</span></label>
                    <input type="text" name="topik" id="topik" class="form-input mt-1 block w-full" value="{{ old('topik') }}" required>
                    @error('topik')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="lokasi_diajukan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lokasi Diajukan (Opsional)') }}</label>
                    <input type="text" name="lokasi_diajukan" id="lokasi_diajukan" class="form-input mt-1 block w-full" value="{{ old('lokasi_diajukan') }}">
                    @error('lokasi_diajukan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="waktu_diajukan" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Waktu Diajukan (Opsional)') }}</label>
                    <input type="datetime-local" name="waktu_diajukan" id="waktu_diajukan" class="form-input mt-1 block w-full" value="{{ old('waktu_diajukan') }}">
                    @error('waktu_diajukan')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('admin.konsultasis.index') }}" class="btn-danger">
                        <i class="bi bi-x-circle mr-2"></i>
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="btn-success">
                        <i class="bi bi-send mr-2"></i>
                        {{ __('Kirim Pengajuan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
