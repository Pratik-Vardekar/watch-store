@extends('layouts.front')

@section('title', 'Login')

@section('content')

<main class="pageWrapper d-flex">

    <section class="w-100 d-flex position-relative">

        <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

        <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

            <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

                <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center">
                    <h1 class="sectTitle mb-4 d-inline-flex align-items-center gap-3">Login</h1>

                    <form method="POST" action="{{ route('login') }}" class="row g-4 formFormat">
                        @csrf

                        <div class="col-12">
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                id="email"
                                placeholder="Email Id"
                                value="{{ old('email') }}"
                                required
                                autofocus
                            />
                            @error('email')
                                <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="position-relative">
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password"
                                    id="password"
                                    placeholder="Password"
                                    required
                                    autocomplete="current-password"
                                />
                                <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" aria-label="Show/Hide password"></button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                            @enderror
                            <small><a href="{{ route('password.request') }}" class="mt-1 float-end">Forgot Password?</a></small>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="ctaBtn">
                                <span class="btnTxt">Login</span>
                                <span class="arw"></span>
                            </button>
                        </div>

                        <div class="col-12 mt-5 text-center">
                            Didn't have an account?
                            <a href="{{ route('register') }}" class="fw-semibold">Signup</a>
                        </div>
                    </form>

                </div>

                <div class="col-md-6 col-12 d-flex py-5 justify-content-center align-items-center bgGray loginImg">
                    <iframe src="{{ asset('frontend/assets/watch/index.html') }}" class="watchFrame" title="Watch Display"></iframe>
                </div>

            </div>

        </div>

    </section>

</main>
@endsection