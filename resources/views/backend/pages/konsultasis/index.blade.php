@extends('backend.layouts.app')

@section('title')
    {{ __('Daftar Konsultasi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Daftar Konsultasi') }}
        </h2>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                        {{ __('Home') }}
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Konsultasi') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        @include('backend.layouts.partials.messages')

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="px-5 py-4 sm:px-6 sm:py-5 flex justify-between items-center">
                <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Riwayat Permintaan Konsultasi') }}</h3>

                {{-- Tombol "Ajukan Konsultasi" hanya untuk Perusahaan/Karyawan --}}
                @if (!auth()->user()->hasRole(['Admin', 'Super Admin']))
                    <a href="{{ route('admin.konsultasis.create') }}" class="btn-success">
                        <i class="bi bi-plus-circle mr-2"></i>
                        {{ __('Ajukan Konsultasi Baru') }}
                    </a>
                @endif
            </div>

            <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto">
                <table class="w-full dark:text-gray-400">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('No.') }}</th>
                            @if (auth()->user()->hasRole(['Admin', 'Superadmin']))
                                <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Perusahaan') }}</th>
                            @endif
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Topik') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Waktu Diajukan') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Waktu & Lokasi Disetujui') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Status') }}</th>
                            <th class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($konsultasis as $konsultasi)
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <td class="px-5 py-4 sm:px-6">{{ $loop->iteration }}</td> {{-- Use iteration for consistent numbering --}}
                            @if (auth()->user()->hasRole(['Admin', 'Superadmin']))
                                <td class="px-5 py-4 sm:px-6">{{ $konsultasi->perusahaan->nama ?? 'N/A' }}</td>
                            @endif
                            <td class="px-5 py-4 sm:px-6">{{ $konsultasi->topik }}</td>
                            <td class="px-5 py-4 sm:px-6">
                                @if($konsultasi->waktu_diajukan) {{ $konsultasi->waktu_diajukan->format('d M Y, H:i') }} <br> @endif
                                @if($konsultasi->lokasi_diajukan) ({{ $konsultasi->lokasi_diajukan }}) @endif
                                @if(!$konsultasi->waktu_diajukan && !$konsultasi->lokasi_diajukan) - @endif
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if($konsultasi->waktu_disetujui) {{ $konsultasi->waktu_disetujui->format('d M Y, H:i') }} <br> @endif
                                @if($konsultasi->lokasi_disetujui) ({{ $konsultasi->lokasi_disetujui }}) @endif
                                @if(!$konsultasi->waktu_disetujui && !$konsultasi->lokasi_disetujui) - @endif
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                @if($konsultasi->status == 'pending')
                                    <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">{{ __('Pending') }}</span>
                                @elseif($konsultasi->status == 'diterima')
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-inset ring-green-600/20">{{ __('Diterima') }}</span>
                                @else
                                    <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-600/20">{{ __('Ditolak') }}</span>
                                @endif
                            </td>
                            <td class="flex px-5 py-4 sm:px-6 text-center gap-1">
                                <a class="btn-info !p-3" href="{{ route('admin.konsultasis.show', $konsultasi->id) }}">
                                    <i class="bi bi-eye text-sm"></i>
                                </a>

                                {{-- Edit & Hapus hanya untuk Admin/Super Admin --}}
                                @if (auth()->user()->hasRole(['Admin', 'Superadmin']))

                                    <a data-modal-target="delete-modal-{{ $konsultasi->id }}" data-modal-toggle="delete-modal-{{ $konsultasi->id }}" class="btn-danger !p-3 cursor-pointer">
                                        <i class="bi bi-trash text-sm"></i>
                                    </a>

                                    {{-- Modal Delete --}}
                                    <div id="delete-modal-{{ $konsultasi->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center">
                                        <div class="relative p-4 w-full max-w-md bg-white rounded-lg shadow-lg dark:bg-gray-700 z-60">
                                            <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="delete-modal-{{ $konsultasi->id }}">
                                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                                </svg>
                                                <span class="sr-only">{{ __('Close modal') }}</span>
                                            </button>
                                            <div class="p-4 md:p-5 text-center">
                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                                </svg>
                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('Anda yakin ingin menghapus permintaan konsultasi ini?') }}</h3>
                                                <form id="delete-form-{{ $konsultasi->id }}" action="{{ route('admin.konsultasis.destroy', $konsultasi->id) }}" method="POST">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                                                        {{ __('Ya, Konfirmasi') }}
                                                    </button>
                                                    <button data-modal-hide="delete-modal-{{ $konsultasi->id }}" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ __('Tidak, Batal') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole(['Admin', 'Superadmin']) ? 7 : 6 }}" class="px-5 py-4 sm:px-6 text-center text-gray-500 dark:text-gray-400">{{ __('Belum ada permintaan konsultasi.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="my-4 px-4 sm:px-6">
                    {{ $konsultasis->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
