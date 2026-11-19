@extends('layouts.front')

@section('content')
<main class="pageWrapper">

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="pt-5">
        <div class="container">

            <div class="text-center mb-4 animateThis slideTop">
                <nav class="breadcrumb justify-content-center mb-0 fw-semibold">
                    <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                    <a class="breadcrumb-item" href="{{ route('gallery') }}">Gallery</a>
                    <span class="breadcrumb-item active" aria-current="page">
                        {{ $gallery->title }}
                    </span>
                </nav>

                <h1 class="sectTitle fw-bold txtred animateThis slideTop">
                    {{ $gallery->title }}
                </h1>
            </div>

            <div class="row g-md-5 mb-5">

                <!-- Left Content -->
                <div class="col-md-auto col-12">

                    <div class="bgTeal text-white position-sticky galleryIntro p-4 rounded-4 mb-4 animateThis slideTop" style="top:150px;">

                        @if($gallery->image)
                            <img
                                src="{{ asset('backend/assets/images/gallery/'.$gallery->image) }}"
                                alt="{{ $gallery->title }}"
                                class="gbbImg w-75 mx-auto d-block mb-4">
                        @endif

                        <div>

                            <h3>{{ $gallery->subtitle }}</h3>

                            <p class="mb-0">
                                {!! $gallery->content !!}
                            </p>

                        </div>

                    </div>

                </div>

                <!-- Right Images -->
                <div class="col-md col-12 ps-xl-5 galleryList">

                    <div class="row g-2 row-cols-1 row-cols-lg-2">

                        @forelse($gallery->images as $image)

                            <div class="col">
                                <div class="prodZoomWrap galleryZoomWrap animateThis fadeIn">
                                    <img
                                        src="{{ asset('backend/assets/images/gallery/'.$image->image) }}"
                                        alt="{{ $gallery->title }}"
                                        class="prodZoomImg rounded-4">
                                    <span class="prodZoomHint d-none d-lg-flex">Hover to zoom</span>
                                </div>
                            </div>

                        @empty

                            <div class="col-12">
                                <p class="text-center">No images available.</p>
                            </div>

                        @endforelse

                    </div>

                </div>

            </div>

        </div>
    </section>

</main>
@endsection

@push('styles')
<style>
    .galleryList .prodZoomWrap {
        position: relative;
        width: 100%;
        max-width: 100%;
        aspect-ratio: 12 / 9;
        overflow: hidden;
        border-radius: .5rem;
        background: #fff;
        cursor: zoom-in;
        isolation: isolate;
    }

    .galleryList .prodZoomWrap .prodZoomImg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        transform-origin: center center;
        transform: scale(1);
        transition: transform .45s cubic-bezier(.22, 1, .36, 1);
        will-change: transform;
        user-select: none;
        -webkit-user-drag: none;
    }

    .galleryList .prodZoomWrap::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        border-radius: inherit;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.04);
        transition: box-shadow .35s ease, background .35s ease;
        z-index: 2;
    }

    .galleryList .prodZoomWrap.is-zooming::after {
        box-shadow: inset 0 0 40px rgba(0, 0, 0, 0.06);
        background: radial-gradient(circle at var(--zoom-x, 50%) var(--zoom-y, 50%), transparent 18%, rgba(0, 0, 0, 0.045) 70%);
    }

    .galleryList .prodZoomWrap.is-zooming .prodZoomImg {
        transform: scale(2.15);
        transition: transform .08s linear;
    }

    .galleryList .prodZoomHint {
        position: absolute;
        left: 50%;
        bottom: 1rem;
        transform: translateX(-50%);
        z-index: 3;
        align-items: center;
        gap: .4rem;
        padding: .45rem .85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.92);
        color: #444;
        font-size: .75rem;
        font-weight: 600;
        letter-spacing: .02em;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        pointer-events: none;
        opacity: 1;
        transition: opacity .3s ease, transform .3s ease;
    }

    .galleryList .prodZoomWrap.is-zooming .prodZoomHint,
    .galleryList .prodZoomWrap:hover .prodZoomHint {
        opacity: 0;
        transform: translateX(-50%) translateY(.35rem);
    }

    .galleryList .prodZoomLens {
        position: absolute;
        width: 7.5rem;
        height: 7.5rem;
        border-radius: 50%;
        border: 1.5px solid rgba(255, 255, 255, 0.95);
        box-shadow:
            0 0 0 1px rgba(0, 0, 0, 0.1),
            0 10px 28px rgba(0, 0, 0, 0.14),
            inset 0 0 0 1px rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(0.5px);
        pointer-events: none;
        opacity: 0;
        transform: translate(-50%, -50%) scale(.85);
        transition: opacity .2s ease, transform .2s ease;
        z-index: 4;
    }

    .galleryList .prodZoomWrap.is-zooming .prodZoomLens {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }

    @media (max-width: 991.98px) {
        .galleryList .prodZoomWrap {
            cursor: default;
        }

        .galleryList .prodZoomWrap.is-zooming .prodZoomImg {
            transform: scale(1);
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function canZoom() {
        return window.matchMedia('(min-width: 992px) and (hover: hover)').matches;
    }

    function initGalleryZoom() {
        document.querySelectorAll('.galleryList .prodZoomWrap').forEach(function (wrap) {
            if (wrap.dataset.zoomReady === '1') {
                return;
            }

            wrap.dataset.zoomReady = '1';

            const img = wrap.querySelector('.prodZoomImg');
            if (!img) {
                return;
            }

            let lens = wrap.querySelector('.prodZoomLens');
            if (!lens) {
                lens = document.createElement('span');
                lens.className = 'prodZoomLens';
                wrap.appendChild(lens);
            }

            wrap.addEventListener('mousemove', function (event) {
                if (!canZoom()) {
                    return;
                }

                const rect = wrap.getBoundingClientRect();
                const x = Math.min(Math.max(event.clientX - rect.left, 0), rect.width);
                const y = Math.min(Math.max(event.clientY - rect.top, 0), rect.height);
                const xPercent = (x / rect.width) * 100;
                const yPercent = (y / rect.height) * 100;

                wrap.classList.add('is-zooming');
                wrap.style.setProperty('--zoom-x', xPercent + '%');
                wrap.style.setProperty('--zoom-y', yPercent + '%');
                img.style.transformOrigin = xPercent + '% ' + yPercent + '%';
                lens.style.left = x + 'px';
                lens.style.top = y + 'px';
            });

            wrap.addEventListener('mouseleave', function () {
                wrap.classList.remove('is-zooming');
                img.style.transformOrigin = 'center center';
            });
        });
    }

    initGalleryZoom();
});
</script>
@endpush