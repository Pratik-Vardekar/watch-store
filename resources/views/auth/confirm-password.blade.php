@extends('layouts.front')

@section('title', 'Confirm Password')

@section('content')

<main class="pageWrapper d-flex">

    <section class="w-100 d-flex position-relative">

        <div class="w-100 h-25 position-absolute top-0 bottom-0 m-auto bg-secondary-subtle rounded-0 progress progress-bar-striped progress-bar-animated"></div>

        <div class="container d-flex py-sm-5 py-4 justify-content-center align-items-center position-relative z-3">

            <div class="loginWrap bg-white shadow-lg d-flex flex-wrap justify-content-between rounded-3 overflow-hidden">

                <div class="col-md-6 col-12 px-md-5 px-4 py-4 align-self-center" style="background: #fff;">
                    <h1 class="resetTitle mb-3 d-inline-flex align-items-center gap-3">Confirm Password</h1>
                    <small class="mb-4 d-block lh-sm custom-desc">
                        This is a secure area. Please confirm your password before continuing.
                    </small>

                    @if (session('status'))
                        <div class="alert alert-success mb-3" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.confirm') }}" class="row g-3 formFormat" id="confirmPasswordForm">
                        @csrf

                        <div class="col-12">
                            <div class="position-relative">
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror emailInputBox"
                                    name="password"
                                    id="password"
                                    placeholder="Password"
                                    required
                                    autocomplete="current-password"
                                    style="background:#e8f0fe;"
                                />
                                <button type="button" class="passwordToggle" aria-label="Show/Hide password"></button>
                            </div>
                            @error('password')
                                <div class="text-danger mt-1" style="font-size:.875rem;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="ctaBtn">Confirm</button>
                        </div>

                        <div class="col-12 mt-3 text-center">
                            <a href="{{ route('login') }}" class="fw-medium">Back to Sign In</a>
                        </div>
                    </form>
                </div>

                <div class="col-md-6 col-12 d-flex justify-content-center align-items-center bgGray loginImg">
                    <iframe src="{{ asset('frontend/assets/watch/index.html') }}" class="watchFrame" title="Watch Display"></iframe>
                </div>

            </div>

        </div>

    </section>

</main>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password toggle functionality
        const toggleButtons = document.querySelectorAll('.passwordToggle');
        
        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="password"], input[type="text"]');
                if (input) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.classList.add('show');
                    } else {
                        input.type = 'password';
                        this.classList.remove('show');
                    }
                }
            });
        });

        // Form validation
        const form = document.getElementById('confirmPasswordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password');

                // Validate password is not empty
                if (password && password.value.trim() === '') {
                    e.preventDefault();
                    password.classList.add('is-invalid');
                    alert('Please enter your password.');
                    password.focus();
                    return false;
                }

                return true;
            });
        }

        // Clear validation on input
        document.querySelectorAll('.emailInputBox').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    });
</script>
@endpush