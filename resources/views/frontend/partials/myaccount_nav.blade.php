@php
    $active = $active ?? '';
    $userName = $user->name ?? 'User';
@endphp

{{-- Mobile account navigation --}}
<div class="col-12 d-lg-none mb-3">
    <div class="myAccMobileNav">

        <div class="myAccMobileScroll">
            <a href="{{ route('profile.profile') }}" class="myAccMobileLink {{ $active === 'profile' ? 'active' : '' }}">Profile</a>
            <a href="{{ route('profile.orders') }}" class="myAccMobileLink {{ $active === 'orders' ? 'active' : '' }}">Orders</a>
            <a href="{{ route('wishlist') }}" class="myAccMobileLink {{ $active === 'wishlist' ? 'active' : '' }}">Wishlist</a>
            <a href="{{ route('profile.address') }}" class="myAccMobileLink {{ $active === 'address' ? 'active' : '' }}">Addresses</a>
            <a href="{{ route('profile.changepassword') }}" class="myAccMobileLink {{ $active === 'password' ? 'active' : '' }}">Password</a>
        </div>
    </div>
</div>

{{-- Desktop account sidebar --}}
<div class="col-lg-auto mb-lg-4 d-none d-lg-block">
    <div class="myAccLinkWrap">
        <div class="d-flex mb-5 px-3 py-2 rounded-3 w-100 bgOrange text-white profileWrap">
            <div class="col-auto"></div>
            <div class="w-100">
                <strong class="d-block">Welcome, {{ $userName }}</strong>
               
            </div>
        </div>
        <ul class="myAccLinks d-flex flex-column gap-4 mb-5">
            <li><a href="{{ route('profile.profile') }}" class="{{ $active === 'profile' ? 'active' : '' }}">Profile</a></li>
            <li><a href="{{ route('profile.orders') }}" class="{{ $active === 'orders' ? 'active' : '' }}">Orders</a></li>
            <li><a href="{{ route('wishlist') }}" class="{{ $active === 'wishlist' ? 'active' : '' }}">Wishlist</a></li>
            <li><a href="{{ route('profile.address') }}" class="{{ $active === 'address' ? 'active' : '' }}">Addresses</a></li>
            <li><a href="{{ route('profile.changepassword') }}" class="{{ $active === 'password' ? 'active' : '' }}">Change Password</a></li>
        </ul>
    </div>
</div>
