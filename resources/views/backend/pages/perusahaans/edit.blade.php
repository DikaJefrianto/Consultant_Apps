@extends('backend.layouts.app')

@section('title')
    {{ __('Perusahaans') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-7xl md:p-6">
    <div x-data="{ pageName: '{{ __('Edit Perusahaan') }}' }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Edit Perusahaan') }}</h2>
            <a href="{{ route('admin.perusahaans.index') }}" class="btn-success inline-flex items-center gap-2">
                <i class="bi bi-arrow-left"></i> {{ __('Kembali') }}
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6">
        @include('backend.layouts.partials.messages')

        <form action="{{ route('admin.perusahaans.update', $perusahaan) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Nama') }}</label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $perusahaan->nama) }}" required maxlength="255" placeholder="{{ __('Masukkan Nama Perusahaan') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('nama') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Username') }}</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $perusahaan->user->username ?? '') }}" required maxlength="255" placeholder="{{ __('Masukkan Username') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('username') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $perusahaan->email) }}" required maxlength="255" placeholder="{{ __('Masukkan Email') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Password Baru (opsional)') }}</label>
                    <input type="password" name="password" id="password" minlength="8" placeholder="{{ __('Masukkan Password Baru') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Konfirmasi Password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="8" placeholder="{{ __('Konfirmasi Password Baru') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>

                <div class="sm:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Alamat') }}</label>
                    <input type="text" name="alamat" id="alamat" value="{{ old('alamat', $perusahaan->alamat) }}" required maxlength="255" placeholder="{{ __('Masukkan Alamat') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('alamat') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="keterangan" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Keterangan') }}</label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan', $perusahaan->keterangan) }}" required maxlength="255" placeholder="{{ __('Masukkan Keterangan') }}"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 dark:placeholder:text-white/30" />
                    @error('keterangan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- <div class="sm:col-span-2">
                    <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Logo (opsional)') }}</label>
                    @if ($perusahaan->logo)
                        <div class="mb-2">
                            <img src="{{ Storage::url($perusahaan->logo) }}" alt="Logo" class="w-20 h-20 object-cover rounded">
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/*"
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 focus:ring-3 focus:outline-none dark:border-gray-700 dark:text-white/90" />
                    @error('logo') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div> --}}
            </div>

            <div class="mt-6 flex justify-start gap-4">
                <button type="submit" class="btn-success">{{ __('Simpan') }}</button>
                <a href="{{ route('admin.perusahaans.index') }}" class="btn-default">{{ __('Batal') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
