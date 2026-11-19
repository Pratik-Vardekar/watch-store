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

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 mb-3" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger py-2 mb-3" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="row g-4 formFormat" id="loginForm">
                        @csrf

                        <div class="col-12">
                            <input
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email"
                                id="email"
                                placeholder="Email Id*"
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
                                    placeholder="Password* (min 8 characters)"
                                    required
                                    autocomplete="current-password"
                                    minlength="8"
                                />
                                <button type="button" class="passwordBtn" tabindex="-1" aria-label="Show/Hide password"></button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                            @enderror
                            <small><a href="{{ route('password.request') }}" class="mt-1 float-end">Forgot Password?</a></small>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="ctaBtn" id="loginBtn">
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

<script>
    // Simple thin/regular eye icon SVG
    const eyeSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z\'/%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'3.5\'/%3E%3C/svg%3E") center/contain no-repeat';
    const eyeSlashSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94\'/%3E%3Cpath d=\'M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M14.11 14.12a3 3 0 1 1-4.24-4.24\'/%3E%3Cline x1=\'1\' y1=\'1\' x2=\'23\' y2=\'23\'/%3E%3C/svg%3E") center/contain no-repeat';

    document.addEventListener('DOMContentLoaded', function() {
        const passwordButtons = document.querySelectorAll('.passwordBtn');

        passwordButtons.forEach(button => {
            button.style.background = eyeSlashSvg;
            button.style.width = "24px";
            button.style.height = "24px";
            button.style.border = "none";
            button.style.position = "absolute";
            button.style.top = "50%";
            button.style.right = "10px";
            button.style.transform = "translateY(-50%)";
            button.style.zIndex = "3";
            button.style.cursor = "pointer";
            button.style.backgroundColor = "transparent";
            button.style.padding = "0";
        });

        passwordButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="password"], input[type="text"]');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.add('active');
                        this.style.background = eyeSvg;
                    } else {
                        input.type = 'password';
                        this.classList.remove('active');
                        this.style.background = eyeSlashSvg;
                    }
                }
            });
        });

        // Form validation for login
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password');
            
            // Check minimum length
            if (password.value.length < 8) {
                alert('Password must be at least 8 characters long.');
                password.focus();
                e.preventDefault();
                return;
            }
        });
    });
</script>

<style>
    .passwordBtn {
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' fill='none' viewBox='0 0 24 24' stroke='%23666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94'/%3E%3Cpath d='M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M14.11 14.12a3 3 0 1 1-4.24-4.24'/%3E%3Cline x1='1' y1='1' x2='23' y2='23'/%3E%3C/svg%3E") center/contain no-repeat;
        width: 24px;
        height: 24px;
        border: none;
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        z-index: 3;
        cursor: pointer;
        background-color: transparent;
        padding: 0;
    }
</style>
@endsection