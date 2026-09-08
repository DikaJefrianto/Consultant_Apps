@extends('backend.layouts.app')

@section('title')
    {{ __('Edit Employee') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="p-4 mx-auto max-w-7xl md:p-6">
    <div x-data="{ pageName: '{{ __('Edit Employee') }}' }">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Edit Employee') }}</h2>
            <nav>
                <ol class="flex items-center gap-1.5">
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.dashboard') }}">
                            {{ __('Home') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li>
                        <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400" href="{{ route('admin.karyawans.index') }}">
                            {{ __('Karyawans') }}
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    <li class="text-sm text-gray-800 dark:text-white/90">
                        {{ __('Edit Karyawan') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="px-5 py-2.5 sm:px-6 sm:py-5">
                <h3 class="text-base font-medium text-gray-800 dark:text-white">{{ __('Edit Employee') }} - {{ $karyawan->nama_lengkap }}</h3>
            </div>
            <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                @include('backend.layouts.partials.messages')

                <form action="{{ route('admin.karyawans.update', $karyawan->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Nama Lengkap') }}</label>
                            <input type="text" name="nama_lengkap" id="nama_lengkap" required value="{{ old('nama_lengkap', $karyawan->nama_lengkap) }}" placeholder="{{ __('Enter Full Name') }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('nama_lengkap') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email (dari relasi user) --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Email') }}</label>
                            <input type="email" name="email" id="email" required value="{{ old('email', $karyawan->user->email ?? '') }}" placeholder="{{ __('Enter Email') }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Password (Optional)') }}</label>
                            <input type="password" name="password" id="password" placeholder="{{ __('Enter Password') }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Confirm Password (Optional)') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('Confirm Password') }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('password_confirmation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Perusahaan --}}
                        <div>
                            <label for="perusahaan_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Perusahaan') }}</label>
                            <select name="perusahaan_id" id="perusahaan_id" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                <option value="">{{ __('Select Perusahaan') }}</option>
                                @foreach ($perusahaans as $perusahaan)
                                    <option value="{{ $perusahaan->id }}" {{ old('perusahaan_id', $karyawan->perusahaan_id) == $perusahaan->id ? 'selected' : '' }}>
                                        {{ $perusahaan->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('perusahaan_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jabatan --}}
                        <div>
                            <label for="jabatan" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Jabatan') }}</label>
                            <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan', $karyawan->jabatan) }}" placeholder="{{ __('Enter Jabatan') }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('jabatan') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- No HP --}}
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('No HP') }}</label>
                            <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" placeholder="{{ __('Enter No HP') }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('no_hp') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Alamat') }}</label>
                            <input type="text" name="alamat" id="alamat" value="{{ old('alamat', $karyawan->alamat) }}" placeholder="{{ __('Enter Alamat') }}"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('alamat') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Foto --}}
                        {{-- <div>
                            <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Foto') }}</label>
                            <input type="file" name="foto" id="foto" accept="image/*"
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            @error('foto') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            @if($karyawan->foto)
                                <img src="{{ asset('storage/' . $karyawan->foto) }}" alt="Foto Karyawan" class="mt-3 w-20 h-20 object-cover rounded-md">
                            @endif
                        </div> --}}

                    </div>
                    <div class="mt-6 flex justify-start gap-4">
                        <button type="submit" class="btn-success">{{ __('Save') }}</button>
                        <a href="{{ route('admin.karyawans.index') }}" class="btn-default">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
