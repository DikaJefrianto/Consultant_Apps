@extends('backend.layouts.app')

@section('title')
    {{ __('Edit Bahan Bakar') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: '{{ __('Bahan Bakar') }}' }">
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ __('Edit Bahan Bakar') }}</h2>
                <a href="{{ route('admin.bahan-bakar.index') }}" class="btn-success">
                    <i class="bi bi-arrow-left mr-2"></i> {{ __('Kembali') }}
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6">
            @include('backend.layouts.partials.messages')

            <form action="{{ route('admin.bahan-bakar.update', $bahanBakar) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="kategori" class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                        {{ __('Kategori') }}
                    </label>
                    <select name="kategori" id="kategori" required
                        class="h-11 w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800
                   shadow-sm focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50
                   dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                   <option value="" disabled selected>{{ __('Pilih Kategori') }}</option>
                        @foreach (['Darat', 'Laut', 'Udara'] as $option)
                            <option value="{{ $option }}"
                                {{ old('kategori', $kategori) == $option ? 'selected' : '' }}>
                                {{ __($option) }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="Bahan_bakar"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Nama Bahan Bakar') }}</label>
                    <input type="text" name="Bahan_bakar" id="Bahan_bakar"
                        value="{{ old('Bahan_bakar', $bahanBakar->Bahan_bakar) }}" required maxlength="255"
                        class="h-11 w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50
                 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder-white/30" />
                    @error('bahan_bakar')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="factorEmisi"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Faktor Emisi') }}</label>
                    <input type="number" step="0.0001" name="factorEmisi" id="factorEmisi"
                        value="{{ old('factorEmisi', $bahanBakar->factorEmisi) }}" required
                        class="h-11 w-full rounded-md border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 shadow-sm focus:border-primary-500 focus:ring-primary-200 focus:ring-opacity-50
                 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder-white/30" />
                    @error('factorEmisi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="btn-success">
                        {{ __('Perbarui') }}
                    </button>
                    <a href="{{ route('admin.bahan-bakar.index') }}" class="btn-default">
                        {{ __('Batal') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
