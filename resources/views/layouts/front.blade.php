<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
    <title>
        @hasSection('title')
            @yield('title') | The Watch Store
        @else
            The Watch Store | Authorized Watches & Expert Service
        @endif
    </title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('frontend/assets/images/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('frontend/assets/images/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('frontend/assets/images/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('frontend/assets/images/favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />

    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}?v=26">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style1.css') }}?v=24">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/search-suggest.css') }}?v=5">

    <style>
        #toastContainer {
            top: 1rem;

            right: 1rem;
            left: auto;
            transform: none;
            width: min(380px, calc(100% - 2rem));
            padding: 0;
            z-index: 11000;
        }

        .tws-toast {
            width: 100% !important;
            max-width: 100% !important;
            border: 0 !important;
            border-radius: 14px !important;
            overflow: hidden;
            background: transparent !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .18);
        }

        .tws-toast .toast-inner {
            display: flex !important;
            align-items: center !important;
            gap: .75rem;
            padding: .85rem 1rem;
            color: #fff !important;
            font-weight: 600;
            font-size: .95rem;
            border-radius: 14px !important;
        }

        /* Success */
        .tws-toast-success .toast-inner {
            background: #198754 !important;
        }

        /* Error */
        .tws-toast-error .toast-inner {
            background: #dc3545 !important;
        }

        /* Warning */
        .tws-toast-warning .toast-inner {
            background: #E85D04 !important;
        }

        /* Info */
        .tws-toast-info .toast-inner {
            background: #0d6efd !important;
        }

        .tws-toast-body {
            flex: 1;
            line-height: 1.35;
            color: #fff !important;
        }

        .tws-toast-close {
            width: 28px;
            height: 28px;
            border: 0 !important;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, .2) !important;
            color: #fff !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.1rem;
            line-height: 1;
            cursor: pointer;
            padding: 0;
        }

        .tws-toast-close:hover {
            background: rgba(255, 255, 255, .35) !important;
        }

        /* Centered Header Bag & Wishlist Count Badges */
        .siteMenuBtn .badge,
        .siteMenuBtn .hnbBadge {
            position: absolute !important;
            right: 0 !important;
            top: 0 !important;
            z-index: 2 !important;
            min-width: 18px !important;
            height: 18px !important;
            padding: 0 4px !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #e60000 !important;
            color: #ffffff !important;
            border-radius: 999px !important;
            font-size: 11px !important;
            line-height: 1 !important;
            font-weight: 700 !important;
            text-align: center !important;
            transform: translate(30%, -30%) !important;
            box-sizing: border-box !important;
            letter-spacing: 0 !important;
            vertical-align: middle !important;
        }

        .siteMenuBtn .badge[style*="display: none"],
        .siteMenuBtn .hnbBadge[style*="display: none"],
        .siteMenuBtn .badge[style*="display:none"],
        .siteMenuBtn .hnbBadge[style*="display:none"] {
            display: none !important;
        }
    </style>

    <!-- Facebook Pixel Code -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1796916864696577');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1"
            src="https://www.facebook.com/tr?id=1796916864696577&ev=PageView
&noscript=1" />
    </noscript>
    <!-- End Facebook Pixel Code -->

    @stack('styles')
</head>

<body>
    @include('frontend.partials.header')
    @yield('content')
    @include('frontend.partials.footer')
    @include('frontend.partials.mobile-bottom-menu')

    <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script src="{{ asset('frontend/assets/js/common.js') }}?v=7"></script>
    <script src="{{ asset('frontend/assets/js/search-suggest.js') }}?v=5"></script>
    <script src="{{ asset('frontend/assets/js/range-slider.js') }}"></script>

    <script>
        window.showToast = function(message, type) {
            type = type || 'success';
            const container = document.getElementById('toastContainer');
            if (!container || !message) {
                return;
            }

            const toastType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'success';
            const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);

            // Clear any existing toast so multiple toasts don't stack/conflict
            container.innerHTML = '';

            container.insertAdjacentHTML('beforeend',
                '<div id="' + toastId + '" class="toast tws-toast tws-toast-' + toastType +
                ' border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
                '<div class="toast-inner">' +
                '<div class="tws-toast-body">' + message + '</div>' +
                '<button type="button" class="tws-toast-close" data-bs-dismiss="toast" aria-label="Close">&times;</button>' +
                '</div>' +
                '</div>'
            );
            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl, {
                animation: true,
                autohide: true,
                delay: 1500
            });
            toast.show();
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        };

        window.updateCartBadge = function(count) {
            if (count === undefined || count === null) return;
            var num = parseInt(count, 10);
            if (isNaN(num)) num = 0;
            document.querySelectorAll('.cart-count, .cartCount').forEach(function(el) {
                el.textContent = num;
                if (num > 0) {
                    el.style.display = 'flex';
                } else {
                    el.style.display = 'none';
                }
            });
        };

        window.updateWishlistBadge = function(count) {
            if (count === undefined || count === null) return;
            var num = parseInt(count, 10);
            if (isNaN(num)) num = 0;
            document.querySelectorAll('.wishlist-count, .wishlistCount').forEach(function(el) {
                el.textContent = num;
                if (num > 0) {
                    el.style.display = 'flex';
                } else {
                    el.style.display = 'none';
                }
            });
        };

        @if (session('success'))
            window.showToast(@json(session('success')), 'success');
        @endif

        @if (session('error'))
            window.showToast(@json(session('error')), 'error');
        @endif

        @if (session('status'))
            window.showToast(@json(session('status')), 'success');
        @endif

        @if (session('login_required'))
            window.showToast({!! json_encode(
                'Please Login First. <a href="' . route('login') . '">Login</a> or <a href="' . route('register') . '">Sign Up</a>',
            ) !!},
                'warning'
            );
        @endif

        @if ($errors->any() && !session('error'))
            window.showToast(@json($errors->first()), 'error');
        @endif

        document.addEventListener('click', function(e) {
            const oosBtn = e.target.closest('.js-out-of-stock-btn, .js-out-of-stock');
            if (oosBtn) {
                e.preventDefault();
                e.stopPropagation();
                var msg = oosBtn.getAttribute('data-oos-message') || 'Product is out of stock';
                if (typeof window.showToast === 'function') {
                    window.showToast(msg, 'error');
                } else {
                    alert(msg);
                }
            }
        });

        var homeBannerEl = document.querySelector('.homeBanner');
        if (homeBannerEl) {
            var homeSlideCount = homeBannerEl.querySelectorAll('.swiper-slide').length;
            new Swiper(homeBannerEl, {
                slidesPerView: 1,
                spaceBetween: 0,
                speed: 650,
                loop: homeSlideCount > 1,
                grabCursor: true,
                autoplay: homeSlideCount > 1 ? {
                    delay: 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                } : false,
                pagination: {
                    el: ".hbPagination",
                    clickable: true,
                    dynamicBullets: homeSlideCount > 6,
                },
                parallax: true,
                touchEventsTarget: 'wrapper',
                passiveListeners: true,
                touchReleaseOnEdges: true,
            });
        }

        if (document.querySelector('.marqueStrip')) {
            document.querySelectorAll('.marqueStrip').forEach(function(el) {
                new Swiper(el, {
                    slidesPerView: 3,
                    spaceBetween: 1,
                    speed: 6000,
                    loop: true,
                    grabCursor: false,
                    allowTouchMove: false,
                    autoplay: {
                        delay: 0,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    },
                    breakpoints: {
                        576: {
                            slidesPerView: 4
                        },
                        768: {
                            slidesPerView: 5
                        },
                        992: {
                            slidesPerView: 6
                        },
                        1200: {
                            slidesPerView: 8
                        },
                        1400: {
                            slidesPerView: 10
                        },
                    },
                });
            });
        }

        if (document.querySelector('.productSlider')) {
            document.querySelectorAll('.productSlider').forEach(function(sliderEl) {
                new Swiper(sliderEl, {
                    slidesPerView: 1.5,
                    spaceBetween: 0,
                    speed: 1000,
                    centeredSlides: true,
                    pagination: {
                        el: sliderEl.querySelector('.swiper-pagination') || ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        576: {
                            slidesPerView: 2,
                            slidesPerGroup: 2,
                            centeredSlides: false,
                        },
                        992: {
                            slidesPerView: 3,
                            slidesPerGroup: 3,
                            centeredSlides: false,
                        },
                        1400: {
                            slidesPerView: 4,
                            slidesPerGroup: 4,
                            centeredSlides: false,
                        },
                    },
                });
            });
        }

        if (document.querySelector('.testimSwiper')) {
            document.querySelectorAll('.testimSwiper').forEach(function(testimEl) {
                var testimSlideCount = testimEl.querySelectorAll('.swiper-slide').length;

                new Swiper(testimEl, {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    speed: 1000,
                    loop: testimSlideCount > 3,
                    centeredSlides: false,
                    grabCursor: true,
                    autoplay: testimSlideCount > 1 ? {
                        delay: 3500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    } : false,
                    pagination: {
                        el: testimEl.querySelector('.swiper-pagination') || ".swiper-pagination",
                        clickable: true
                    },
                    breakpoints: {
                        576: {
                            slidesPerView: 1,
                            spaceBetween: 20
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        992: {
                            slidesPerView: 3,
                            spaceBetween: 24
                        },
                        1200: {
                            slidesPerView: 3,
                            spaceBetween: 30
                        },
                    },
                });
            });
        }
        /* Path Animation */
        let ticking = false;
        let last_known_scroll_position = 0;
        let updatePath = false;
        const element = document.querySelector('.svgRoute');
        const path = element ? element.querySelector('.travelPath') : null;
        let totalLength = 0;

        if (element && path) {
            totalLength = path.getTotalLength();
            path.style.strokeDasharray = `${totalLength}`;
            path.style.strokeDashoffset = totalLength;

            let observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    updatePath = entry.isIntersecting;
                });
            }, {
                rootMargin: "0px 0px 50% 50%"
            });
            observer.observe(element);

            function doSomething() {
                if (!updatePath) {
                    return;
                }
                window.requestAnimationFrame(() => {
                    const center = window.innerHeight / 1.1;
                    const boundaries = path.getBoundingClientRect();
                    const top = boundaries.top;
                    const height = boundaries.height;
                    const percentage = (center - top) / height;
                    const drawLength = percentage > 0 ? totalLength * percentage : 0;
                    path.style.strokeDashoffset = drawLength < totalLength ? totalLength - drawLength : 0;
                })
            }

            window.addEventListener('scroll', function() {
                last_known_scroll_position = window.scrollY;
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        doSomething(last_known_scroll_position);
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }

        const bgBox = document.querySelector('.offerSection');
        const bg = document.querySelector('.parallax');
        if (bgBox && bg) {
            const ww = window.innerWidth / 2;
            const wh = window.innerHeight / 2;
            bgBox.addEventListener('mousemove', e => {
                bg.style.transform = `translate3d(-${e.clientX / ww}%, ${e.clientY / wh}%, 0)`;
            });
        }
    </script>

    @stack('scripts')
    @stack('custom-scripts')
</body>

</html>
