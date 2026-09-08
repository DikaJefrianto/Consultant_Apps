@extends('auth.layouts.app')

@section('title')
    {{ __('Sign In') }} | {{ config('app.name') }}
@endsection

@section('admin-content')

    <div class="bg-white p-10 rounded-md shadow-md border border-blue-200 w-[400px]">
        <!-- Logo & Title -->
        <div class="text-center mb-6">
            {{-- ID 'app-logo' ditambahkan untuk memudahkan akses dengan JavaScript --}}
            <img id="app-logo" src="{{ asset('images/ns-longl-color.png') }}" alt="Logo" class="mx-auto w-50 mb-2">
        </div>

        <!-- Heading -->
        <div class="mb-5 text-center">
            <h2 class="text-lg font-bold text-gray-800">{{ __('Login') }}</h2>
            <p class="text-sm text-gray-500">{{ __('Log in to Naima Sustainability') }}</p>
        </div>

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="space-y-5">
                @include('backend.layouts.partials.messages')

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ __('Email') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="email" name="email" autocomplete="username"
                        placeholder="Email anda"
                        class="h-10 w-full rounded border border-gray-300 px-3 text-sm placeholder:text-gray-400 focus:border-emerald-600 focus:ring focus:ring-emerald-200">
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        {{ __('Password') }} <span class="text-red-500">*</span>
                    </label>
                    <div x-data="{ showPassword: false }" class="relative">
                        <input :type="showPassword ? 'text' : 'password'" autocomplete="current-password" name="password"
                            placeholder="Password anda"
                            class="h-10 w-full rounded border border-gray-300 px-3 text-sm placeholder:text-gray-400 focus:border-emerald-600 focus:ring focus:ring-emerald-200"
                            type="password">
                        <span @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 cursor-pointer">
                            <!-- Icon Mata -->
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.956 9.956 0 012.122-3.568m3.493-2.249A9.956 9.956 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.953 9.953 0 01-4.307 5.222M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m3-3L3 3">
                                </path>
                            </svg>
                        </span>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-sm">
                    <div x-data="{ checkboxToggle: false }" class="flex items-center">
                        <input type="checkbox" id="checkboxLabelOne" name="remember"
                            class="mr-2 accent-emerald-600 border-gray-300 rounded">
                        <label for="checkboxLabelOne" class="text-gray-700">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                    <a href="{{ route('admin.password.request') }}" class="text-emerald-600 hover:underline">
                        {{ __('Forgot password?') }}
                    </a>
                </div>

                <!-- Login Button -->
                <div>
                    <button type="submit"
                        class="w-full bg-emerald-600 text-black py-2 rounded hover:bg-emerald-700 transition">
                        {{ __('Log In') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
