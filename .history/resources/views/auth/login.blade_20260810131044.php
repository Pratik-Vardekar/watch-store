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
<script>
    // Eye SVG (show) and Eye-slash SVG (hide) as data URL for button backgrounds
    const eyeSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'24\' height=\'24\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23000\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3E%3Cpath d=\'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z\'/%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'3\'/%3E%3C/svg%3E") center/contain no-repeat';

    // The image you provided (as base64 SVG)
    const eyeSlashSvg = 'url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'38\' height=\'38\' fill=\'none\' viewBox=\'0 0 38 38\'%3E%3Crect width=\'26.899\' height=\'26.899\' x=\'5.551\' y=\'5.551\' stroke=\'%23000\' stroke-width=\'2.6\' rx=\'4.803\'/%3E%3Cpath stroke=\'%23000\' stroke-linecap=\'round\' stroke-width=\'2.6\' d=\'m12.527 18.795 2.074-2.287c1.379-1.566 3.74-1.646 5.203-.167a3.386 3.386 0 0 1 .11 4.737l-1.394 1.391a3.386 3.386 0 0 1-4.944-4.01l.06-.086ZM13.82 13.818l10.36 10.36\'/%3E%3C/svg%3E") center/contain no-repeat';

    document.addEventListener('DOMContentLoaded', function() {
        const passwordButtons = document.querySelectorAll('.passwordBtn');
        
        // Initial icon for all buttons (eye closed at first!)
        passwordButtons.forEach(button => {
            button.style.background = eyeSlashSvg;
            button.style.width = "36px";
            button.style.height = "36px";
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
                        // Set eye open icon
                        this.style.background = eyeSvg;
                    } else {
                        input.type = 'password';
                        this.classList.remove('active');
                        // Set eye closed icon (provided design)
                        this.style.background = eyeSlashSvg;
                    }
                }
            });
        });
    });
</script>
@endsection