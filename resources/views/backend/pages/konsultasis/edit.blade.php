@extends('backend.layouts.app')

@section('title')
    {{ __('Kelola Konsultasi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Kelola Permintaan Konsultasi') }}
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
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Kelola') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        @include('backend.layouts.partials.messages')

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-4">{{ __('Form Persetujuan Konsultasi') }}</h3>

            <form action="{{ route('admin.konsultasis.update', $konsultasi->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Perusahaan Pengaju') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->perusahaan->nama ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Topik Konsultasi') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->topik }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Waktu Diajukan') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->waktu_diajukan ? $konsultasi->waktu_diajukan->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lokasi Diajukan') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->lokasi_diajukan ?? '-' }}</p>
                    </div>
                </div>

                <hr class="my-6 border-gray-200 dark:border-gray-700">

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status Konsultasi') }} <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="form-select mt-1 block w-full" required>
                        <option value="pending" {{ old('status', $konsultasi->status) == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                        <option value="diterima" {{ old('status', $konsultasi->status) == 'diterima' ? 'selected' : '' }}>{{ __('Diterima') }}</option>
                        <option value="ditolak" {{ old('status', $konsultasi->status) == 'ditolak' ? 'selected' : '' }}>{{ __('Ditolak') }}</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="waktu_disetujui" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Waktu Disetujui') }}</label>
                    <input type="datetime-local" name="waktu_disetujui" id="waktu_disetujui" class="form-input mt-1 block w-full" value="{{ old('waktu_disetujui', $konsultasi->waktu_disetujui ? $konsultasi->waktu_disetujui->format('Y-m-d\TH:i') : '') }}">
                    @error('waktu_disetujui')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="lokasi_disetujui" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lokasi Disetujui') }}</label>
                    <input type="text" name="lokasi_disetujui" id="lokasi_disetujui" class="form-input mt-1 block w-full" value="{{ old('lokasi_disetujui', $konsultasi->lokasi_disetujui) }}">
                    @error('lokasi_disetujui')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="catatan_admin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Catatan Admin (Opsional)') }}</label>
                    <textarea name="catatan_admin" id="catatan_admin" rows="3" class="form-textarea mt-1 block w-full">{{ old('catatan_admin', $konsultasi->catatan_admin) }}</textarea>
                    @error('catatan_admin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <a href="{{ route('admin.konsultasis.index') }}" class="btn-danger">
                        <i class="bi bi-x-circle mr-2"></i>
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="btn-success">
                        <i class="bi bi-save mr-2"></i>
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
