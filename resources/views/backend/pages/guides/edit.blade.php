@extends('backend.layouts.app') {{-- Sesuaikan ini dengan layout dashboard admin Anda --}}

@section('title')
    {{ __('edit_guide') }} - {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="container mx-auto px-4 py-8 dark:bg-gray-900 dark:text-white">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{__("edit_guide_title", ['title' => $guide->title])}}</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
                <ul class="mt-3 list-disc list-inside text-sm text-red-600 dark:text-red-200">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-lg dark:bg-gray-800">
            <form action="{{ route('admin.guides.update', $guide->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- Penting untuk metode PUT/PATCH --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{__("title")}} <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $guide->title) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-emerald-400">
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{__("category")}}</label>
                        <input type="text" name="category" id="category" value="{{ old('category', $guide->category) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-emerald-400">
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{__("short_description")}}</label>
                        <textarea name="description" id="description" rows="3"
                                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500
                                     dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-emerald-400">{{ old('description', $guide->description) }}</textarea>
                    </div>
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('guide_file_label') }}</label>
                        <input type="file" name="file" id="file"
                            class="mt-1 py-0.5 ps-3 block w-full text-sm
                                   border border-gray-300 rounded-md
                                   text-gray-500 file:mr-4 file:py-2 file:px-4
                                   file:rounded-md file:text-sm file:font-semibold
                                   file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100
                                   dark:text-gray-400 dark:file:bg-emerald-800 dark:file:text-emerald-100 dark:hover:file:bg-emerald-700
                                   dark:border-gray-700">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{__('main_file_description')}}
                            @if($guide->file_path)
                                <span class="block mt-1">{{ __('current_file') }}: <a href="{{ Storage::url($guide->file_path) }}" target="_blank" class="text-emerald-500 hover:underline">{{ basename($guide->file_path) }}</a></span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label for="thumbnail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{__('thumbnail_image_label')}}</label>
                        <input type="file" name="thumbnail" id="thumbnail"
                               class="mt-1 py-0.5 ps-3 block w-full text-sm
                                    border border-gray-300 rounded-md
                                    text-gray-500 file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:text-sm file:font-semibold
                                    file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100
                                    dark:text-gray-400 dark:file:bg-emerald-800 dark:file:text-emerald-100 dark:hover:file:bg-emerald-700
                                    dark:border-gray-700">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{__('thumbnail_description')}}
                            @if($guide->thumbnail_path)
                                <span class="block mt-1">{{ __('current_thumbnail') }}: <a href="{{ Storage::url($guide->thumbnail_path) }}" target="_blank" class="text-emerald-500 hover:underline">{{ basename($guide->thumbnail_path) }}</a></span>
                                <img src="{{ Storage::url($guide->thumbnail_path) }}" alt="{{ $guide->thumbnail_alt ?? 'Thumbnail' }}" class="mt-2 h-20 w-auto object-cover rounded-md">
                            @endif
                        </p>
                    </div>
                    <div>
                        <label for="thumbnail_alt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{__('thumbnail_alt_text')}}</label>
                        <input type="text" name="thumbnail_alt" id="thumbnail_alt" value="{{ old('thumbnail_alt', $guide->thumbnail_alt) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-emerald-400">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <a href="{{ route('admin.guides.index') }}" class="px-4 py-2 text-gray-600 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors mr-2">{{__('cancel')}}</a>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors">
                        {{__('update_guide')}}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
