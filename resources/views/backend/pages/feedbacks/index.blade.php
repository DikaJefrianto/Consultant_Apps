@extends('backend.layouts.app')

@section('title')
    {{ __('Feedback') }} | {{ config('app.name') }}
@endsection

@section('admin-content')

<div class="p-4 mx-auto max-w-screen-2xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Daftar Feedback') }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Feedback') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Feedback Pengguna') }}</h3>

                {{-- Form pencarian untuk feedback (saat ini dikomentari karena belum ada implementasi search di controller) --}}
                {{-- @include('backend.partials.search-form', [
                    'placeholder' => __('Cari feedback'),
                ]) --}}

                {{-- Tidak ada tombol "Tambah Feedback" karena feedback dikirim dari frontend --}}
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                {{-- Pesan Sukses atau Error --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($feedbacks->isEmpty())
                    <p class="p-6 text-gray-600 dark:text-gray-400 text-center">{{ __('Belum ada feedback yang masuk.') }}</p>
                @else
                    <table class="w-full dark:text-gray-400">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('No.') }}</th>
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Nama Pengirim') }}</th>
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Email') }}</th>
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Subjek') }}</th>
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Pesan') }}</th> {{-- Kolom Pesan Baru --}}
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Dikirim Pada') }}</th>
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($feedbacks as $feedback)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 sm:px-6">{{ $loop->iteration }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $feedback->name }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ $feedback->email }}</td>
                                <td class="px-5 py-4 sm:px-6">{{ Str::limit($feedback->subject, 30) }}</td> {{-- Batasi subjek agar tidak terlalu panjang --}}
                                <td class="px-5 py-4 sm:px-6">{{ Str::limit($feedback->message, 50) }}</td> {{-- Menampilkan pesan dengan batasan karakter --}}
                                <td class="px-5 py-4 sm:px-6">{{ $feedback->created_at->format('d M Y, H:i') }}</td>
                                <td class="flex px-5 py-4 sm:px-6 text-center gap-1">
                                    {{-- Tombol Detail --}}
                                    <a class="btn-info !p-3" href="{{ route('admin.feedbacks.show', $feedback->id) }}">
                                        <i class="bi bi-eye text-sm"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <a data-modal-target="delete-modal-{{ $feedback->id }}" data-modal-toggle="delete-modal-{{ $feedback->id }}" class="btn-danger !p-3 cursor-pointer">
                                        <i class="bi bi-trash text-sm"></i>
                                    </a>

                                    {{-- Modal Delete --}}
                                    <div id="delete-modal-{{ $feedback->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center">
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
                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('Are you sure you want to delete this feedback?') }}</h3>
                                                <form id="delete-form-{{ $feedback->id }}" action="{{ route('admin.feedbacks.destroy', $feedback->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf

                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                        {{ __('Yes, Confirm') }}
                                                    </button>
                                                    <button data-modal-hide="delete-modal-{{ $feedback->id }}" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ __('No, cancel') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="my-4 px-4 sm:px-6">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
