@extends('auth.layouts.app')

@section('title')
    {{ __('Reset Password') }} | {{ config('app.name') }}
@endsection

@section('admin-content')
<div class="bg-white p-10 rounded-md shadow-md border border-blue-200 w-[400px]">
    <!-- Logo & Title -->
    <div class="text-center mb-6">
        <!-- Logo & Title -->
        <div class="text-center mb-6">
            {{-- ID 'app-logo' ditambahkan untuk memudahkan akses dengan JavaScript --}}
            <img id="app-logo" src="{{ asset('images/ns-longl-color.png') }}" alt="Logo" class="mx-auto w-50 mb-2">
        </div>

        <!-- Heading -->
        <div class="mb-5 text-center">
            <h2 class="text-lg font-bold text-gray-800 ">{{ __('Reset Password') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Enter your email address to receive a password reset link.') }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="space-y-5">
                {{-- Pesan Status (Sukses) --}}
                @if (session('status'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 ">
                        {{ __('E-Mail Address') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="Email anda"
                        class="h-10 w-full rounded border border-gray-300 px-3 text-sm placeholder:text-gray-400
                               focus:border-emerald-600 focus:ring focus:ring-emerald-200
                               @error('email') border-red-500 @enderror">
                    @error('email')
                        <span class="text-red-500 text-sm mt-1" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Send Password Reset Link Button -->
                <div>
                    <button type="submit"
                        class="w-full bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700 transition">
                        {{ __('Send Password Reset Link') }}
                    </button>
                </div>
            </div>
        </form>

        {{-- Tombol untuk mengganti tema (opsional, untuk demonstrasi) --}}

    </div>
</div>


@endsection
