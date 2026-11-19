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

                    <form method="POST" action="{{ route('register') }}" class="row g-3 formFormat" id="registerForm">
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
                                placeholder="Password* (exactly 8 characters)"
                                required
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="8"
                            />
                            <button type="button" class="passwordBtn" aria-label="Show/Hide password"></button>
                            <small class="text-muted d-block mt-1">Password must be exactly 8 characters long.</small>
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
                                minlength="8"
                                maxlength="8"
                            />
                            <button type="button" class="passwordBtn" aria-label="Show/Hide password"></button>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div id="passwordInfo" class="mb-2">
                                <small id="charCount" class="text-muted">Characters: 0/8</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <small>By continuing, you agree to our <a href="#" class="fw-medium">Terms of Service</a> and <a href="#" class="fw-medium">Privacy Policy</a>.</small>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="ctaBtn" id="registerBtn">
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
    // Simple thin/regular eye icon SVG
    const eyeSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z\'/%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'3.5\'/%3E%3C/svg%3E") center/contain no-repeat';
    const eyeSlashSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94\'/%3E%3Cpath d=\'M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M14.11 14.12a3 3 0 1 1-4.24-4.24\'/%3E%3Cline x1=\'1\' y1=\'1\' x2=\'23\' y2=\'23\'/%3E%3C/svg%3E") center/contain no-repeat';

    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const passwordFields = document.querySelectorAll('.position-relative');
        passwordFields.forEach(field => {
            const input = field.querySelector('input[type="password"], input[type="text"]');
            const btn = field.querySelector('.passwordBtn');
            if (!input || !btn) return;

            btn.style.background = eyeSlashSvg;
            btn.style.width = "24px";
            btn.style.height = "24px";
            btn.style.border = "none";
            btn.style.position = "absolute";
            btn.style.top = "50%";
            btn.style.right = "5px";
            btn.style.transform = "translateY(-50%)";
            btn.style.zIndex = "3";
            btn.style.cursor = "pointer";
            btn.style.backgroundColor = "transparent";
            btn.style.padding = "0";
            btn.style.display = "block";

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="password"], input[type="text"]');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.style.background = eyeSvg;
                    } else {
                        input.type = 'password';
                        this.style.background = eyeSlashSvg;
                    }
                }
            });
        });

        // Password character counter
        const passwordInput = document.getElementById('password');
        const charCount = document.getElementById('charCount');

        if (passwordInput && charCount) {
            passwordInput.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = 'Characters: ' + length + '/8';
                
                if (length === 8) {
                    charCount.style.color = '#28a745';
                } else if (length > 8) {
                    charCount.style.color = '#dc3545';
                } else {
                    charCount.style.color = '#6c757d';
                }
            });
        }

        // Form validation before submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            let isValid = true;

            // Check exact length
            if (password.value.length !== 8) {
                alert('Password must be exactly 8 characters long.');
                password.focus();
                e.preventDefault();
                return;
            }

            // Check if passwords match
            if (password.value !== confirm.value) {
                alert('Passwords do not match.');
                confirm.focus();
                e.preventDefault();
                return;
            }
        });
    });
</script>

<style>
    .passwordBtn {
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z'/%3E%3Ccircle cx='12' cy='12' r='3.5'/%3E%3C/svg%3E") center/contain no-repeat;
        width: 24px;
        height: 24px;
        border: none;
        position: absolute;
        top: 50%;
        right: 5px;
        transform: translateY(-50%);
        z-index: 3;
        cursor: pointer;
        background-color: transparent;
        padding: 0;
        display: block;
    }
</style>
@endsection