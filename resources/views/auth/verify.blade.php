@extends('auth.layouts.app')
@section('title')
    {{ __('Verify Email') }} | {{ config('app.name') }}
@endsection
@section('admin-content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="bg-white p-10 rounded-md shadow-md border border-blue-200 w-[400px]">
                    <div class="card-header">{{ __("Verify Your Email Address") }}</div>

                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __("A fresh verification link has been sent to your email address.") }}
                            </div>
                        @endif

                        {{ __("Before proceeding, please check your email for a verification link.") }}
                        {{ __("If you did not receive the email .") }}<br><br>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf

                            <button type="submit" class="btn btn-success btn-link p-0 m-0 align-baseline">
                                {{ __("Click Here to Resend Verification Link") }}
                            </button>.
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
