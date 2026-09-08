@extends('backend.layouts.app')

@section('title')
    {{ __('Employee Create') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('New Employee') }}' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('New Employee') }}</h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.dashboard') }}">
                                {{ __('Home') }}<i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.karyawans.index') }}">
                                {{ __('Karyawans') }}<i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">{{ __('New Employee') }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Create New Employee') }}</h3>
                </div>
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('admin.karyawans.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{-- Nama Lengkap --}}
                            <div>
                                <label for="nama_lengkap"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Nama Lengkap') }}</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                    value="{{ old('nama_lengkap') }}" required autofocus
                                    placeholder="{{ __('Enter Full Name') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800
                                       h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                       text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            {{-- Email --}}
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Email') }}</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    placeholder="{{ __('Enter Email') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800
                                       h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                       text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Password') }}</label>
                                <input type="password" name="password" id="password" required
                                    placeholder="{{ __('Enter Password') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Confirm Password') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    placeholder="{{ __('Confirm Password') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            {{-- No HP --}}
                            <div>
                                <label for="no_hp"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('No HP') }}</label>
                                <input type="text" name="no_hp" id="no_hp" value="{{ old('no_hp') }}" required
                                    placeholder="{{ __('Enter Phone Number') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800
                                       h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                       text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            {{-- Alamat --}}
                            <div>
                                <label for="alamat"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Alamat') }}</label>
                                <input type="text" name="alamat" id="alamat" value="{{ old('alamat') }}" required
                                    placeholder="{{ __('Enter Address') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800
                                       h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                       text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            {{-- Foto --}}
                            {{-- <div>
                            <label for="foto" class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Foto (opsional)') }}</label>
                            <input type="file" name="foto" id="foto"
                                class="mt-1 block w-full text-gray-700 dark:text-gray-300">
                        </div> --}}
                            {{-- Jabatan --}}
                            <div>
                                <label for="jabatan"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Jabatan (opsional)') }}</label>
                                <input type="text" name="jabatan" id="jabatan" value="{{ old('jabatan') }}"
                                    placeholder="{{ __('Enter Position') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800
                                       h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
                                       text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
                                       dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                            </div>
                            {{-- User Select --}}

                            {{-- Perusahaan Select --}}
                            @role(['Admin', 'Superadmin'])
                                <div>
                                    <label for="perusahaan_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Pilih Perusahaan') }}</label>
                                    <select name="perusahaan_id" id="perusahaan_id" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800
               h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm
               text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden
               dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                                        <option value="">{{ __('Select Perusahaan') }}</option>
                                        @foreach ($perusahaans as $perusahaan)
                                            <option value="{{ $perusahaan->id }}"
                                                {{ old('perusahaan_id') == $perusahaan->id ? 'selected' : '' }}>
                                                {{ $perusahaan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="perusahaan_id" value="{{ auth()->user()->perusahaan->id }}">
                            @endrole

                        </div>

                        {{-- Buttons --}}
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
