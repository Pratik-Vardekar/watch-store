@extends('layouts.front')

@section('title', 'Set New Password')

@section('content')
<main class="pageWrapper loginMainWrap">         

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 mb-5 overflow-hidden">
        <div class="container py-5">
            <div class="rounded-4 animateThis slideTop loginWrap shadow-lg p-5 mx-auto">
                
                <h1 class="sectTitle mb-3 lh-1 fw-bold animateThis slideTop">Set New Password</h1>
                <p>Enter your email and choose a new password for your account.</p>

                <form method="POST" action="{{ route('password.store') }}" class="row g-3 formFormat animateThis fadeGrow">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="col-12">
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            id="email"
                            placeholder="Email Id"
                            value="{{ old('email', $request->email) }}"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        @error('email')
                            <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="passwordField position-relative">
                            <input
                                type="password"
                                class="form-control @error('password') border-danger @enderror"
                                name="password"
                                id="password"
                                placeholder="New Password"
                                required
                                autocomplete="new-password"
                            />
                            <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" aria-label="Show/Hide password"></button>
                        </div>
                        @error('password')
                            <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div class="passwordField position-relative">
                            <input
                                type="password"
                                class="form-control @error('password_confirmation') border-danger @enderror"
                                name="password_confirmation"
                                id="password_confirmation"
                                placeholder="Confirm New Password"
                                required
                                autocomplete="new-password"
                            />
                            <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" aria-label="Show/Hide password"></button>
                        </div>
                        @error('password_confirmation')
                            <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="ctaBtn">
                            <span class="btnTxt">Reset Password</span>
                            <span class="arw"></span>
                        </button>
                    </div>

                    <div class="col-12 mt-4 text-center">
                        <a href="{{ route('login') }}" class="fw-medium">Back to Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>
@endsection
