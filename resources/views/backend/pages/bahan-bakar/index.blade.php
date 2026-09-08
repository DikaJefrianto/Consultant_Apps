@extends('backend.layouts.app')

@section('title')
    {{ __('Bahan Bakar') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-screen-2xl md:p-6"> {{-- Perbaiki '--breakpoint-2xl' menjadi 'screen-2xl' --}}
        <div x-data="{ pageName: '{{ __('Bahan Bakar') }}' }"> {{-- Terjemahkan di sini --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Bahan Bakar') }}</h2>
                {{-- Terjemahkan di sini --}}
                <div class="flex items-center gap-2">
                    @if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Superadmin')))
                        <a href="{{ route('admin.bahan-bakar.create') }}" class="btn-success">
                            <i class="bi bi-plus-circle mr-2"></i> {{ __('Tambah Bahan Bakar') }} {{-- Terjemahkan di sini --}}
                        </a>
                    @endif
                </div>
            </div>

        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Daftar Bahan Bakar') }}</h3>
                    {{-- Terjemahkan di sini --}}
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <button id="kategoriDropdownButton" data-dropdown-toggle="kategoriDropdown"
                                class="btn-default flex items-center justify-center gap-2" type="button">
                                <i class="bi bi-funnel-fill"></i>
                                {{ __('Filter Kategori') }} {{-- Terjemahkan di sini --}}
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <div id="kategoriDropdown"
                                class="z-10 hidden w-48 p-3 mt-2 bg-white rounded-lg shadow dark:bg-gray-700 absolute right-0">
                                <ul class="space-y-2">
                                    <li class="cursor-pointer text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded"
                                        onclick="handleKategoriFilter('')">
                                        {{ __('Semua Kategori') }} {{-- Terjemahkan di sini --}}
                                    </li>
                                    @foreach (['Darat', 'Laut', 'Udara'] as $kategori)
                                        <li class="cursor-pointer text-sm text-gray-700 dark:text-white hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded {{ request('kategori') === $kategori ? 'bg-gray-200 dark:bg-gray-600' : '' }}"
                                            onclick="handleKategoriFilter('{{ $kategori }}')">
                                            {{ __($kategori) }} {{-- Terjemahkan kategori jika perlu --}}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <script>
                            function handleKategoriFilter(kategori) {
                                const url = new URL(window.location.href);
                                if (kategori) {
                                    url.searchParams.set('kategori', kategori);
                                } else {
                                    url.searchParams.delete('kategori');
                                }
                                window.location.href = url.toString();
                            }
                        </script>

                        @include('backend.partials.search-form', [
                            'placeholder' => __('Cari berdasarkan kategori atau nama bahan bakar'),
                        ])
                    </div>
                </div>

                <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                    @include('backend.layouts.partials.messages')
                    <table class="w-full dark:text-gray-400">
                        <thead class="bg-light">
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left bg-gray-50 dark:bg-gray-800 dark:text-white">
                                    {{ __('No.') }}</th> {{-- Terjemahkan di sini --}}
                                <th class="px-5 py-3 text-left bg-gray-50 dark:bg-gray-800 dark:text-white">
                                    {{ __('Kategori') }}</th> {{-- Terjemahkan di sini --}}
                                <th class="px-5 py-3 text-left bg-gray-50 dark:bg-gray-800 dark:text-white">
                                    {{ __('Nama Bahan Bakar') }}</th> {{-- Terjemahkan di sini --}}
                                <th class="px-5 py-3 text-left bg-gray-50 dark:bg-gray-800 dark:text-white">
                                    {{ __('Faktor Emisi') }}</th> {{-- Terjemahkan di sini --}}
                                <th class="px-5 py-3 text-left bg-gray-50 dark:bg-gray-800 dark:text-white">
                                    {{ __('Action') }}</th> {{-- Terjemahkan di sini --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bahan_bakars as $item)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-4">{{ __($item->kategori) }}</td> {{-- Terjemahkan jika kategori perlu diterjemahkan --}}
                                    <td class="px-5 py-4">{{ __($item->Bahan_bakar) }}</td> {{-- Terjemahkan jika nama bahan bakar perlu diterjemahkan --}}
                                    <td class="px-5 py-4">{{ number_format($item->factorEmisi, 4) }}</td>
                                    <td class="px-5 py-4 sm:px-6 flex gap-2">
                                        {{-- Tombol Edit hanya muncul jika user adalah 'karyawan' --}}
                                        @if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Superadmin')))
                                            {{-- Otorisasi Edit berdasarkan Policy --}}
                                            <a href="{{ route('admin.bahan-bakar.edit', $item) }}"
                                                class="btn-default !p-2">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        {{-- Tombol Hapus hanya muncul jika user adalah 'karyawan' --}}
                                        @if ((Auth::check() && Auth::user()->hasRole('Admin')) || Auth::user()->hasRole('Superadmin'))
                                            {{-- Otorisasi Hapus berdasarkan Policy --}}
                                            <button data-modal-target="delete-modal-{{ $item->id }}"
                                                data-modal-toggle="delete-modal-{{ $item->id }}"
                                                class="btn-danger !p-2">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                        {{ __('Tidak ada data bahan bakar') }}</td> {{-- Terjemahkan di sini --}}
                                </tr>
                            @endforelse
                            @foreach ($bahan_bakars as $item)
                                <div id="delete-modal-{{ $item->id }}" tabindex="-1" aria-hidden="true"
                                    class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full">
                                    <div class="relative w-full max-w-md h-full md:h-auto">
                                        <div class="relative p-4 bg-white rounded-lg shadow dark:bg-gray-700">
                                            <button type="button"
                                                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                                                data-modal-hide="delete-modal-{{ $item->id }}">
                                                <svg class="w-3 h-3" aria-hidden="true" fill="none" viewBox="0 0 14 14"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 1l6 6m0 0l6 6M7 7l6-6M7 7L1 13" />
                                                </svg>
                                                <span class="sr-only">Close modal</span>
                                            </button>
                                            <div class="p-4 md:p-5 text-center">
                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200"
                                                    fill="none" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                                                    {{ __('Yakin ingin menghapus data ini?') }} {{-- Terjemahkan di sini --}}
                                                </h3>
                                                <form action="{{ route('admin.bahan-bakar.destroy', $item) }}"
                                                    method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                        {{ __('Ya, Hapus') }} {{-- Terjemahkan di sini --}}
                                                    </button>
                                                    <button data-modal-hide="delete-modal-{{ $item->id }}"
                                                        type="button"
                                                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                                        {{ __('Batal') }} {{-- Terjemahkan di sini --}}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="my-4 px-4 sm:px-6">
                        {{ $bahan_bakars->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
