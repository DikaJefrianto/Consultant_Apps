@extends('backend.layouts.app')

@section('title')
    {{ __('Biaya') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('Biaya') }}' }">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Tambah Biaya') }}</h2>
                <a href="{{ route('admin.biaya.index') }}" class="btn-success">
                    <i class="bi bi-arrow-left mr-2"></i> {{ __('Kembali') }}
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
            @include('backend.layouts.partials.messages')

            <form action="{{ route('admin.biaya.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Kategori') }}
                    </label>
                    <select name="kategori" id="kategori" required
                        class="h-11 w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800
                   shadow-sm focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50
                   dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder-white/30">
                        <option value="" disabled selected>{{ __('Pilih Kategori') }}</option>
                        <option value="Darat" {{ old('kategori') == 'Darat' ? 'selected' : '' }}>{{ __('Darat') }}</option>
                        <option value="Laut" {{ old('kategori') == 'Laut' ? 'selected' : '' }}>{{ __('Laut') }}</option>
                        <option value="Udara" {{ old('kategori') == 'Udara' ? 'selected' : '' }}>{{ __('Udara') }}</option>
                    </select>
                    @error('kategori')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <div>
                    <label for="jenisKendaraan"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Jenis Biaya') }}</label>
                    <input type="text" name="jenisKendaraan" id="jenisKendaraan" value="{{ old('jenisKendaraan') }}"
                        required maxlength="255" placeholder="{{ __('Masukkan jenis biaya yang di keluarkan') }}"
                        class="h-11 w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50
                 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder-white/30" />
                    @error('jenisKendaraan')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="factorEmisi"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Faktor Emisi') }}</label>
                    <input type="factorEmisi" step="0.0001" name="factorEmisi" id="factorEmisi"
                        value="{{ old('factorEmisi') }}" required placeholder="{{ __('Faktor Emisi') }}"
                        class="h-11 w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50
                 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder-white/30" />
                    @error('factorEmisi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn-success">
                        {{ __('Simpan') }}
                    </button>
                    <a href="{{ route('admin.biaya.index') }}" class="btn-default">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
