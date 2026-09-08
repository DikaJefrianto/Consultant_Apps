@extends('backend.layouts.app')

@section('title')
    {{ __('Edit Strategi') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('Edit Strategy') }}' }">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Edit Strategi') }}</h2>
                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.dashboard') }}">
                                {{ __('Home') }}
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.strategis.index') }}">
                                {{ __('Strategi') }}
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90">
                            {{ __('Edit Strategi') }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90">{{ __('Perbarui Strategi') }}</h3>
                </div>

                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    @include('backend.layouts.partials.messages')

                    <form action="{{ route('admin.strategis.update', $strategi->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{--pilihan perusahaan--}}
                            <div class="sm:col-span-2">
                                    <label for="perusahaan_id"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ __('Perusahaan') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="perusahaan_id" id="perusahaan_id" required
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                                        <option value="">-- Pilih Perusahaan --</option>
                                        {{-- $perusahaans will only be available if the user is an admin or superadmin --}}
                                        @if (isset($perusahaans))
                                            @foreach($perusahaans as $perusahaan)
                                            <option value="{{ $perusahaan->id }}" {{ old('user_id') == $perusahaan->id ? 'selected' : '' }}>
                                                {{ $perusahaan->nama }}
                                            </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('perusahaan_id')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            {{-- Nama Program --}}
                            <div class="sm:col-span-2">
                                <label for="nama_program"
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('Nama Program') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nama_program" id="nama_program"
                                    value="{{ old('nama_program', $strategi->nama_program) }}" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                @error('nama_program')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="sm:col-span-2">
                                <label for="deskripsi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('Deskripsi') }}
                                </label>
                                <textarea name="deskripsi" id="deskripsi" rows="4"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">{{ old('deskripsi', $strategi->deskripsi) }}</textarea>
                                @error('deskripsi')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div>
                                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('Status Program') }}
                                </label>
                                <select name="status" id="status" required
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="aktif" {{ old('status', $strategi->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $strategi->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Upload Dokumen --}}
                            <div>
                                <label for="dokumen" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('Dokumen Pendukung') }}
                                </label>
                                <input type="file" name="dokumen" id="dokumen" accept=".pdf,.doc,.docx"
                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600">
                                @if ($strategi->dokumen)
                                    <p class="text-sm mt-1 dark:text-white">
                                        Dokumen saat ini: <a href="{{ asset('storage/' . $strategi->dokumen) }}" target="_blank" class="text-blue-500 underline">Lihat Dokumen</a>
                                    </p>
                                @endif
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maks. 2MB. Format: PDF, DOC, DOCX</p>
                                @error('dokumen')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Perusahaan (hanya admin) --}}
                            @role('admin')
                                <div class="sm:col-span-2">
                                    <label for="perusahaan_id"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ __('Pilih Perusahaan') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="perusahaan_id" id="perusahaan_id" required
                                        class="w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                        <option value="">-- Pilih Perusahaan --</option>
                                        @foreach ($perusahaans as $perusahaan)
                                            <option value="{{ $perusahaan->id }}"
                                                {{ old('perusahaan_id', $strategi->perusahaan_id) == $perusahaan->id ? 'selected' : '' }}>
                                                {{ $perusahaan->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('perusahaan_id')
                                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endrole
                        </div>

                        <div class="mt-6 flex justify-start gap-4">
                            <button type="submit" class="btn-success">{{ __('Perbarui') }}</button>
                            <a href="{{ route('admin.strategis.index') }}" class="btn-default">{{ __('Batal') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
