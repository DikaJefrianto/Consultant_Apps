@extends('backend.layouts.app')

@section('title')
    {{ __('Detail Feedback') }} | {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-screen-2xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Detail Feedback') }}
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
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.feedbacks.index') }}">
                        {{ __('Feedback') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Detail') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 sm:p-8">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-4">{{ __('Informasi Feedback') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Nama Pengirim:') }}</p>
                    <p class="text-gray-900 dark:text-white/90 text-base mt-1">{{ $feedback->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Email:') }}</p>
                    <p class="text-gray-900 dark:text-white/90 text-base mt-1">{{ $feedback->email }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Subjek:') }}</p>
                    <p class="text-gray-900 dark:text-white/90 text-base mt-1">{{ $feedback->subject }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Dikirim Pada:') }}</p>
                    <p class="text-gray-900 dark:text-white/90 text-base mt-1">{{ $feedback->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Pesan:') }}</p>
                    <p class="text-gray-900 dark:text-white/90 text-base mt-1 leading-relaxed">{{ $feedback->message }}</p>
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <a href="{{ route('admin.feedbacks.index') }}" class="btn-default inline-flex items-center">
                    <i class="bi bi-arrow-left mr-2"></i> {{ __('Kembali ke Daftar Feedback') }}
                </a>
                {{-- Tombol Hapus juga ditambahkan di detail view untuk kemudahan --}}
                <a data-modal-target="delete-modal-{{ $feedback->id }}" data-modal-toggle="delete-modal-{{ $feedback->id }}" class="btn-danger inline-flex items-center cursor-pointer">
                    <i class="bi bi-trash mr-2"></i> {{ __('Hapus Feedback') }}
                </a>

                {{-- Modal Delete (sama dengan yang ada di index) --}}
                <div id="delete-modal-{{ $feedback->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="relative p-4 w-full max-w-md bg-white rounded-lg shadow-lg dark:bg-gray-700 z-60">
                        <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal-{{ $feedback->id }}">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">{{ __('Close modal') }}</span>
                        </button>
                        <div class="p-4 md:p-5 text-center">
                            <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('Apakah Anda yakin ingin menghapus feedback ini?') }}</h3>
                            <form id="delete-form-{{ $feedback->id }}" action="{{ route('admin.feedbacks.destroy', $feedback->id) }}" method="POST">
                                @method('DELETE')
                                @csrf

                                <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                    {{ __('Ya, Konfirmasi') }}
                                </button>
                                <button data-modal-hide="delete-modal-{{ $feedback->id }}" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ __('Tidak, Batalkan') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
