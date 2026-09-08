@extends('backend.layouts.app')

@section('title')
    {{ __('Detail Strategi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Detail Strategi') }}
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
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.strategis.index') }}">
                        {{ __('Strategi') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Detail') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-4">{{ __('Informasi Strategi') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Nama Program') }}</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $strategi->nama_program }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ ucfirst($strategi->status) }}</p>
                </div>

                {{-- Hanya tampilkan informasi Perusahaan jika user adalah Admin atau Super Admin --}}
                @if (auth()->user()->hasRole(['Admin', 'Superadmin']))
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Perusahaan') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $strategi->perusahaan->nama ?? 'Tidak Terkait' }}</p>
                    </div>
                @endif

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Deskripsi') }}</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $strategi->deskripsi ?? '-' }}</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Dokumen Terkait') }}</label>
                    @if($strategi->dokumen)
                        <a href="{{ asset('storage/' . $strategi->dokumen) }}" download="{{ basename($strategi->dokumen) }}" target="_blank" class="mt-1 inline-flex items-center text-blue-500 hover:underline">
                            <i class="bi bi-file-arrow-down-fill mr-1"></i> {{ __('Unduh Dokumen') }}
                        </a>
                    @else
                        <p class="mt-1 text-gray-900 dark:text-gray-100">- {{ __('Tidak ada dokumen') }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Dibuat Pada') }}</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $strategi->created_at->format('d M Y, H:i') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Terakhir Diperbarui') }}</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $strategi->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <a href="{{ route('admin.strategis.index') }}" class="btn-warning">
                    <i class="bi bi-arrow-left-circle mr-2"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>
                {{-- Tombol Edit hanya untuk Admin/Super Admin  --}}
                @if (auth()->user()->hasRole(['Admin', 'Superadmin']))
                    <a href="{{ route('admin.strategis.edit', $strategi->id) }}" class="btn-success">
                        <i class="bi bi-pencil mr-2"></i>
                        {{ __('Edit Strategi') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
