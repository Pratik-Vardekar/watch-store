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

                <form method="POST" action="{{ route('password.store') }}" class="row g-3 formFormat animateThis fadeGrow" id="resetPasswordForm">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="col-12">
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email"
                            id="email"
                            placeholder="Email Id*"
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
                                placeholder="New Password* (exactly 8 characters)"
                                required
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="8"
                            />
                            <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" aria-label="Show/Hide password"></button>
                        </div>
                        <small class="text-muted d-block mt-1">Password must be exactly 8 characters long.</small>
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
                                placeholder="Confirm New Password*"
                                required
                                autocomplete="new-password"
                                minlength="8"
                                maxlength="8"
                            />
                            <button type="button" class="passwordBtn" data-bs-toggle="button" tabindex="-1" aria-label="Show/Hide password"></button>
                        </div>
                        @error('password_confirmation')
                            <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <div id="passwordInfo" class="mb-2">
                            <small id="charCount" class="text-muted">Characters: 0/8</small>
                        </div>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="ctaBtn" id="resetBtn">
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
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');

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