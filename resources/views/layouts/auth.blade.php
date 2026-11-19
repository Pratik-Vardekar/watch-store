<!doctype html>
<html lang="en">
    <head>
        <title>Admin Login | The Watch Store</title>
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <!-- App favicon -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend/assets/images/favicon.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('frontend/assets/images/favicon.png') }}">
 <!-- Theme Config Js -->
<script src="{{asset('backend/assets/js/config.js')}}"></script>

<!-- Vendor css -->
<link href="{{asset('backend/assets/css/vendors.min.css')}}" rel="stylesheet" type="text/css" />

<!-- App css -->
<link id="app-style" href="{{asset('backend/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" />

    </head>

    <body style="background-color: #ffffda;">
        {{-- <div class="position-absolute top-0 end-0">
            <img src="assets/images/auth-card-bg.svg')}}" class="auth-card-bg-img" alt="auth-card-bg" />
        </div>
        <div class="position-absolute bottom-0 start-0" style="transform: rotate(180deg)">
            <img src="{{asset('admin/assets/images/auth-card-bg.svg')}}" class="auth-card-bg-img" alt="auth-card-bg" />
        </div> --}}
       @yield('content')
        <!-- end auth-fluid-->

        <!-- Vendor js -->
        <script src="{{asset('backend/assets/js/vendors.min.js')}}"></script>

        <!-- App js -->
        <script src="{{asset('backend/assets/js/app.js')}}"></script>


        <!-- Password Suggestion Js -->
        <script src="{{asset('backend/assets/js/pages/auth-password.js')}}"></script>
    </body>
</html>
