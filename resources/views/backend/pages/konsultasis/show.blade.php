@extends('backend.layouts.app')

@section('title')
    {{ __('Detail Konsultasi') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
{{-- Perbaikan: Mengubah max-w-(--breakpoint-2xl) menjadi max-w-screen-xl --}}
<div class="p-4 mx-auto max-w-screen-xl md:p-6">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
            {{ __('Detail Konsultasi') }}
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
                <li class="text-sm text-gray-800 dark:text-white/90">{{ __('Detail') }}</li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        @include('backend.layouts.partials.messages')

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <h3 class="text-lg font-medium text-gray-800 dark:text-white/90 mb-4">{{ __('Informasi Lengkap Konsultasi') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if (auth()->user()->hasRole(['Admin', 'Superadmin']))
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Perusahaan Pengaju') }}</label>
                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->perusahaan->nama ?? 'N/A' }}</p>
                </div>
                @endif

                <div class="md:col-span-2">
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                    <p class="mt-1">
                        @if($konsultasi->status == 'pending')
                            <span class="inline-flex items-center rounded-md bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">{{ __('Pending') }}</span>
                        @elseif($konsultasi->status == 'diterima')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-inset ring-green-600/20">{{ __('Diterima') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-600/20">{{ __('Ditolak') }}</span>
                        @endif
                    </p>
                </div>

                @if($konsultasi->status == 'diterima' || $konsultasi->status == 'ditolak')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Waktu Disetujui/Ditolak') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->waktu_disetujui ? $konsultasi->waktu_disetujui->format('d M Y, H:i') : '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Lokasi Disetujui') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->lokasi_disetujui ?? '-' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Catatan Admin') }}</label>
                        <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $konsultasi->catatan_admin ?? '-' }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-2">
                {{-- Tombol Kembali (Biru) --}}
                <a href="{{ route('admin.konsultasis.index') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    <i class="bi bi-arrow-left-circle mr-2"></i>
                    {{ __('Kembali ke Daftar') }}
                </a>

                {{-- Hanya Admin/Super Admin yang bisa mengelola (edit/setujui/tolak) --}}
                @if (Auth::check() && Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Superadmin'))
                    @if($konsultasi->status == 'pending')
                        {{-- Tombol Setujui (Hijau) - Membuka Modal --}}
                        <button type="button" data-modal-target="approve-modal-{{ $konsultasi->id }}" data-modal-toggle="approve-modal-{{ $konsultasi->id }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <i class="bi bi-check-circle mr-2"></i>
                            {{ __('Setujui') }}
                        </button>
                        {{-- Tombol Tolak/Kelola (Kuning) --}}
                        <a href="{{ route('admin.konsultasis.edit', $konsultasi->id) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <i class="bi bi-pencil mr-2"></i>
                            {{ __('Tolak/Kelola') }}
                        </a>
                    @else
                        {{-- Tombol Kelola Konsultasi (Kuning) --}}
                        <a href="{{ route('admin.konsultasis.edit', $konsultasi->id) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                            <i class="bi bi-pencil mr-2"></i>
                            {{ __('Kelola Konsultasi') }}
                        </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Persetujuan --}}
{{-- Pastikan ini berada di luar div utama admin-content agar tidak terpengaruh oleh max-w dan layout lainnya, --}}
{{-- tetapi masih di dalam @section('admin-content') atau di bagian bawah file layout utama Anda. --}}
<div id="approve-modal-{{ $konsultasi->id }}" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="relative p-4 w-full max-w-md bg-white rounded-lg shadow-lg dark:bg-gray-700 z-60">
        {{-- Tombol Tutup Modal --}}
        <button type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="approve-modal-{{ $konsultasi->id }}">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
            <span class="sr-only">{{ __('Close modal') }}</span>
        </button>
        <div class="p-4 md:p-5 text-center">
            {{-- Ikon Konfirmasi --}}
            <svg class="mx-auto mb-4 text-green-400 w-12 h-12 dark:text-green-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
            {{-- Judul Konfirmasi --}}
            <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ __('Anda yakin ingin menyetujui permintaan konsultasi ini?') }}</h3>
            {{-- Form Persetujuan --}}
            <form id="approve-form-{{ $konsultasi->id }}" action="{{ route('admin.konsultasis.approve', $konsultasi->id) }}" method="POST">
                @csrf
                <button type="submit" class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                    {{ __('Ya, Setujui') }}
                </button>
                <button data-modal-hide="approve-modal-{{ $konsultasi->id }}" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">{{ __('Tidak, Batal') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
