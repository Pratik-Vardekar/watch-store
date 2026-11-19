@extends('layouts.front')

@section('title', 'Set New Password')

@section('content')

<main class="pageWrapper d-flex">

    <section class="w-100 d-flex position-relative">

        <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

        <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

            <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

                <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center">
                    <h1 class="sectTitle mb-4 d-inline-flex align-items-center gap-3">Set New Password</h1>
                    <p class="text-muted mb-4">Enter your email and choose a new password for your account.</p>

                    <form method="POST" action="{{ route('password.store') }}" class="row g-4 formFormat">
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
                            <div class="position-relative">
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
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
                            <div class="position-relative">
                                <input
                                    type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
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
                            <a href="{{ route('login') }}" class="fw-semibold">Back to Sign In</a>
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
    // Simple thin/regular eye icon SVG (not bold/broad stroke)
    const eyeSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z\'/%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'3.5\'/%3E%3C/svg%3E") center/contain no-repeat';

    // Simple thin/regular eye-slash icon SVG (not bold)
    const eyeSlashSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%23666\' stroke-width=\'1.5\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94\'/%3E%3Cpath d=\'M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19M14.11 14.12a3 3 0 1 1-4.24-4.24\'/%3E%3Cline x1=\'1\' y1=\'1\' x2=\'23\' y2=\'23\'/%3E%3C/svg%3E") center/contain no-repeat';

    document.addEventListener('DOMContentLoaded', function() {
        const passwordButtons = document.querySelectorAll('.passwordBtn');
        
        // Simple style for all buttons (not bold)
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
                // Find the password input in the same parent container
                const input = this.closest('.position-relative').querySelector('input[type="password"], input[type="text"]');
                if (input) {
                    // Toggle the input type
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.add('active');
                        // Set simple eye open icon
                        this.style.background = eyeSvg;
                    } else {
                        input.type = 'password';
                        this.classList.remove('active');
                        // Set simple eye closed icon
                        this.style.background = eyeSlashSvg;
                    }
                }
            });
        });
    });
</script>

<style>
    .loginWrap {
        max-width: 1000px;
        width: 100%;
    }

    .loginImg {
        min-height: 400px;
        background: #f8f9fa;
    }

    .watchFrame {
        width: 100%;
        height: 450px;
        border: none;
        background: transparent;
    }

    .formFormat .form-control {
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .formFormat .form-control:focus {
        border-color: #d4a843;
        box-shadow: 0 0 0 0.2rem rgba(212, 168, 67, 0.25);
    }

    .formFormat .form-control.is-invalid {
        border-color: #dc3545;
    }

    .formFormat .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .formFormat .position-relative {
        position: relative;
    }

    .passwordBtn {
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        z-index: 3;
        transition: opacity 0.2s ease;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 0.7;
    }

    .passwordBtn:hover {
        opacity: 1;
    }

    .ctaBtn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 35px;
        background: #d4a843;
        color: #fff;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        letter-spacing: 1px;
        transition: all 0.3s ease;
        text-transform: uppercase;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
    }

    .ctaBtn:hover {
        background: #b8922f;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(212, 168, 67, 0.3);
    }

    .arw {
        display: inline-block;
        width: 20px;
        height: 20px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cline x1='5' y1='12' x2='19' y2='12'/%3E%3Cpolyline points='12 5 19 12 12 19'/%3E%3C/svg%3E") center/contain no-repeat;
        transition: transform 0.3s ease;
    }

    .ctaBtn:hover .arw {
        transform: translateX(5px);
    }

    .sectTitle {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        color: #1a1a1a;
    }

    .progress {
        height: 20px;
        opacity: 0.3;
    }

    .pageWrapper {
        min-height: 100vh;
        background: #f5f5f5;
    }

    @media (max-width: 767px) {
        .loginImg {
            min-height: 250px;
        }
        
        .watchFrame {
            height: 250px;
        }
        
        .sectTitle {
            font-size: 1.5rem;
        }
        
        .loginWrap {
            margin: 15px;
        }
    }

    @media (max-width: 576px) {
        .loginImg {
            min-height: 200px;
        }
        
        .watchFrame {
            height: 200px;
        }
    }
</style>

@endsection