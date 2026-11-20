@php
    $active = $active ?? '';
    $userName = $user->name ?? 'User';
    $userEmail = $user->email ?? '';
    $userInitials = strtoupper(substr(trim($userName), 0, 1) ?: 'U');
@endphp

{{-- Mobile account navigation --}}
<div class="col-12 d-lg-none mb-4">
    <div class="myAccMobileNav">
        <div class="myAccMobileScroll">
            <a href="{{ route('profile.profile') }}" class="myAccMobileLink {{ $active === 'profile' ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span>Profile</span>
            </a>
            <a href="{{ route('profile.orders') }}" class="myAccMobileLink {{ $active === 'orders' ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span>Orders</span>
            </a>
            <a href="{{ route('wishlist') }}" class="myAccMobileLink {{ $active === 'wishlist' ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                <span>Wishlist</span>
            </a>
            <a href="{{ route('profile.address') }}" class="myAccMobileLink {{ $active === 'address' ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span>Addresses</span>
            </a>
            <a href="{{ route('profile.changepassword') }}" class="myAccMobileLink {{ $active === 'password' ? 'active' : '' }}">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                <span>Password</span>
            </a>
        </div>
    </div>
</div>

{{-- Desktop account sidebar --}}
<div class="col-lg-3 mb-4 mb-lg-0 d-none d-lg-block">
    <div class="myAccSidebarCard myAccLinkWrap">
        {{-- User info banner --}}
        <div class="myAccUserBanner">
            <div class="myAccAvatar">{{ $userInitials }}</div>
            <div class="myAccUserInfo">
                <h4 class="myAccUserName" title="{{ $userName }}">{{ $userName }}</h4>
                <span class="myAccUserSub" title="{{ $userEmail }}">{{ $userEmail }}</span>
            </div>
        </div>

        {{-- Nav list --}}
        <ul class="myAccNavList myAccLinks">
            <li class="myAccNavItem">
                <a href="{{ route('profile.profile') }}" class="{{ $active === 'profile' ? 'active' : '' }}">
                    <span class="navItemLeft">
                        <span class="navItemIcon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </span>
                        <span>My Profile</span>
                    </span>
                    <span class="navItemChevron">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </a>
            </li>
            <li class="myAccNavItem">
                <a href="{{ route('profile.orders') }}" class="{{ $active === 'orders' ? 'active' : '' }}">
                    <span class="navItemLeft">
                        <span class="navItemIcon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <path d="M16 10a4 4 0 0 1-8 0"></path>
                            </svg>
                        </span>
                        <span>Orders</span>
                    </span>
                    <span class="navItemChevron">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </a>
            </li>
            <li class="myAccNavItem">
                <a href="{{ route('wishlist') }}" class="{{ $active === 'wishlist' ? 'active' : '' }}">
                    <span class="navItemLeft">
                        <span class="navItemIcon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </span>
                        <span>Wishlist</span>
                    </span>
                    <span class="navItemChevron">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </a>
            </li>
            <li class="myAccNavItem">
                <a href="{{ route('profile.address') }}" class="{{ $active === 'address' ? 'active' : '' }}">
                    <span class="navItemLeft">
                        <span class="navItemIcon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </span>
                        <span>Addresses</span>
                    </span>
                    <span class="navItemChevron">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </a>
            </li>
            <li class="myAccNavItem">
                <a href="{{ route('profile.changepassword') }}" class="{{ $active === 'password' ? 'active' : '' }}">
                    <span class="navItemLeft">
                        <span class="navItemIcon">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <span>Change Password</span>
                    </span>
                    <span class="navItemChevron">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                </a>
            </li>
        </ul>

        {{-- Logout footer --}}
        <div class="myAccNavFooter">
            <form id="sidebarLogoutForm" action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="myAccNavItem w-100 p-0 border-0 bg-transparent text-start">
                    <span class="myAccLogoutBtn w-100 d-flex align-items-center justify-content-between p-2 rounded-2">
                        <span class="navItemLeft">
                            <span class="navItemIcon text-danger">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                            </span>
                            <span class="fw-semibold text-danger">Logout</span>
                        </span>
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
