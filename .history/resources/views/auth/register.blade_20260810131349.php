@extends('layouts.front')

@section('title', 'Sign Up')

@section('content')

<main class="pageWrapper d-flex">

    <section class="w-100 d-flex position-relative">

        <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

        <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

            <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

                <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center">
                    <h1 class="sectTitle mb-4 d-inline-flex align-items-center gap-3">Sign Up</h1>

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 mb-3" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success py-2 mb-3" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" class="row g-3 formFormat">
                        @csrf

                        <div class="col-12">
                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                id="name"
                                placeholder="Full Name*"
                                value="{{ old('name') }}"
                                required
                            />
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                id="email"
                                placeholder="Email Id*"
                                value="{{ old('email') }}"
                                required
                            />
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <input
                                type="tel"
                                class="form-control @error('phone') is-invalid @enderror"
                                name="phone"
                                id="phone"
                                placeholder="Mobile No.*"
                                value="{{ old('phone') ? ltrim(preg_replace('/\D+/', '', preg_replace('/^(\+?91)/', '', old('phone'))), '0') : '' }}"
                                inputmode="numeric"
                                maxlength="10"
                            />
                            @error('phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 position-relative">
                            <input
                                type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                id="password"
                                placeholder="Password*"
                                required
                                autocomplete="new-password"
                            />
                            <!-- Eye icon button only shows when password is visible! -->
                            <button type="button" class="passwordBtn" style="display:none;" aria-label="Hide password"></button>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 position-relative">
                            <input
                                type="password"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation"
                                id="password_confirmation"
                                placeholder="Confirm Password*"
                                required
                                autocomplete="new-password"
                            />
                            <!-- Eye icon button only shows when password is visible! -->
                            <button type="button" class="passwordBtn" style="display:none;" aria-label="Hide password"></button>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <small>By continuing, you agree to our <a href="#" class="fw-medium">Terms of Service</a> and <a href="#" class="fw-medium">Privacy Policy</a>.</small>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="ctaBtn">
                                <span class="btnTxt">Continue</span>
                                <span class="arw"></span>
                            </button>
                        </div>

                        <div class="col-12 mt-4 text-center">
                            Already have an account?
                            <a href="{{ route('login') }}" class="fw-medium">Back to Login</a>
                        </div>
                    </form>

                </div>

                <div class="col-md-6 col-12 py-5 d-flex justify-content-center align-items-center bgGray loginImg">
                    <iframe src="{{ asset('frontend/assets/watch/index.html') }}" class="watchFrame" title="Watch Display"></iframe>
                </div>

            </div>

        </div>

    </section>

</main>
<script>
    // Eye icon for show password (only show when password visible)
    const eyeSvg =
        "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z'/%3E%3Ccircle cx='12' cy='12' r='3'/%3E%3C/svg%3E\") center/contain no-repeat";

    document.addEventListener('DOMContentLoaded', function() {
        // Target all password toggle buttons on the page and pair with input
        const passwordFields = document.querySelectorAll('.position-relative');
        passwordFields.forEach(field => {
            const input = field.querySelector('input[type="password"], input[type="text"]');
            const btn = field.querySelector('.passwordBtn');
            if (!input || !btn) return;

            // Remove default styles
            btn.style.background = '';
            btn.style.width = '32px';
            btn.style.height = '32px';
            btn.style.border = 'none';
            btn.style.position = 'absolute';
            btn.style.top = '50%';
            btn.style.right = '10px';
            btn.style.transform = 'translateY(-50%)';
            btn.style.zIndex = '3';
            btn.style.cursor = 'pointer';
            btn.style.backgroundColor = 'transparent';
            btn.style.padding = '0';
            btn.style.display = 'none'; // Hide by default (when password hidden)

            // Only show the button when the field type is 'text' (visible)
            input.addEventListener('input', function() {
                if (input.type === 'text') {
                    btn.style.display = 'block';
                    btn.style.background = eyeSvg;
                } else {
                    btn.style.display = 'none';
                }
            });

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.classList.add('active');
                    btn.setAttribute('aria-label', 'Hide password');
                    btn.style.display = 'block';
                    btn.style.background = eyeSvg;
                } else {
                    input.type = 'password';
                    btn.classList.remove('active');
                    btn.setAttribute('aria-label', 'Hide password');
                    btn.style.display = 'none'; // Hide the icon when password is hidden
                }
            });
        });
    });
</script>
@endsection