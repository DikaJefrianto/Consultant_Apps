@extends('backend.layouts.app')

@section('title')
    {{ __('Strategi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Strategi') }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Strategi') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Daftar Strategi') }}</h3>

                @include('backend.partials.search-form', [
                    'placeholder' => __('Search Strategy'),
                ])

                {{-- Tombol "Tambah Strategi" hanya untuk Admin/Super Admin --}}
                @if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Superadmin')))
                    <a href="{{ route('admin.strategis.create') }}" class="btn-success">
                        <i class="bi bi-plus-circle mr-2"></i>
                        {{ __('Tambah Strategi') }}
                    </a>
                @endif
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                <table class="w-full dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('No.') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Nama Program') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Deskripsi') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Dokumen') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Status') }}</th>
                            {{-- Hanya tampilkan kolom Perusahaan jika user adalah Admin/Super Admin --}}
                            @if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin')))
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Perusahaan') }}</th>
                            @endif
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($strategis as $strategi)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 sm:px-6">{{ $loop->index + 1 }}</td>
                            <td class="px-5 py-4 sm:px-6">{{ $strategi->nama_program }}</td>
                            <td class="px-5 py-4 sm:px-6">{{ $strategi->deskripsi }}</td>
                            <td class="px-5 py-4 sm:px-6">
                                @if($strategi->dokumen)
                                    <a href="{{ asset('storage/' . $strategi->dokumen) }}" download="{{ basename($strategi->dokumen) }}" target="_blank" class="text-blue-500 hover:underline">Unduh</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-4 sm:px-6">{{ __($strategi->status) }}</td>
                            {{-- Tampilkan nama perusahaan jika user adalah Admin/Super Admin --}}
                            @if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Super Admin')))
                                <td class="px-5 py-4 sm:px-6">{{ $strategi->perusahaan->nama ?? 'N/A' }}</td>
                            @endif
                            <td class="flex px-5 py-4 sm:px-6 text-center gap-1">
                                @if (auth()->user()->hasRole(['Admin', 'Superadmin']))
                                    {{-- Tombol Detail untuk Admin/Super Admin --}}
                                    <a class="btn-info !p-3" href="{{ route('admin.strategis.show', $strategi->id) }}">
                                        <i class="bi bi-eye text-sm"></i>
                                    </a>
                                    {{-- Tombol Edit untuk Admin/Super Admin --}}
                                    <a class="btn-default !p-3" href="{{ route('admin.strategis.edit', $strategi->id) }}">
                                        <i class="bi bi-pencil text-sm"></i>
                                    </a>

                                    {{-- Tombol Hapus untuk Admin/Super Admin --}}
                                    <a data-modal-target="delete-modal-{{ $strategi->id }}" data-modal-toggle="delete-modal-{{ $strategi->id }}" class="btn-danger !p-3 cursor-pointer">
                                        <i class="bi bi-trash text-sm"></i>
                                    </a>

                                    {{-- Modal Delete (tetap sama) --}}
                                    <div id="delete-modal-{{ $strategi->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                                        <div class="relative p-4 w-full max-w-md bg-white rounded-lg shadow-lg dark:bg-gray-700 z-60">
                                            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal-{{ $strategi->id }}">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                                <span class="sr-only">{{ __('Close modal') }}</span>
                                            </button>
                                            <div class="p-4 md:p-5 text-center">
                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('Are you sure you want to delete this strategi?') }}</h3>
                                                <form id="delete-form-{{ $strategi->id }}" action="{{ route('admin.strategis.destroy', $strategi->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf

                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                        {{ __('Yes, Confirm') }}
                                                    </button>
                                                    <button data-modal-hide="delete-modal-{{ $strategi->id }}" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ __('No, cancel') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Tombol Lihat Detail untuk user selain Admin/Super Admin --}}
                                    <a class="btn-info !p-3" href="{{ route('admin.strategis.show', $strategi->id) }}">
                                        <i class="bi bi-eye text-sm mr-1"></i> {{ __('Detail') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    {{ $strategis->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
