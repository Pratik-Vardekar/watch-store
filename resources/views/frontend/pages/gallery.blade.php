@extends('layouts.front')

@section('content')
<main class="pageWrapper">

    <div class="cloudWrap position-relative d-none d-lg-block">
        <div class="container clouds position-relative"></div>
    </div>

    <section class="pt-5 overflow-hidden">
        <div class="container">

            <div class="text-center mb-5 animateThis slideTop">
                <nav class="breadcrumb justify-content-center mb-0 fw-semibold">
                    <a class="breadcrumb-item" href="{{ route('home') }}">Home</a>
                    <span class="breadcrumb-item active">Gallery</span>
                </nav>

                <h1 class="sectTitle fw-bold txtred animateThis slideTop">
                    Gallery
                </h1>
            </div>

            <div class="row g-md-5 g-4 mb-5">

                @forelse($galleries as $gallery)

                    <div class="col-lg-6 animateThis fadeIn">

                        <div class="p-md-5 p-3 rounded-sm-5 rounded-4 d-flex flex-wrap h-100 galleryBox
                            {{ $loop->odd ? 'bgTeal' : 'bgOrange' }}">

                            <div class="col-sm-6 col-5 gbImgBox order-sm-last">

                                <img
                                    src="{{ asset('backend/assets/images/gallery/'.$gallery->image) }}"
                                    alt="{{ $gallery->title }}"
                                    class="gbImg img-fluid">

                            </div>

                            <div class="col-sm-6 col-12 text-white d-flex flex-column">

                                <div class="col">

                                    <h2 class="gbHead">
                                        {{ $gallery->title }}
                                    </h2>

                                    @if($gallery->subtitle)
                                        <h3 class="gbSubhead">
                                            {{ $gallery->subtitle }}
                                        </h3>
                                    @endif

                                    @if($gallery->content)
                                        <p class="gbTxt">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($gallery->content),120) }}
                                        </p>
                                    @endif

                                </div>

                                <div class="col-auto mt-3">

                                    <a href="{{ route('gallery.details',$gallery->slug) }}"
                                        class="ctaBtn">

                                        <span class="btnTxt">
                                            View Gallery
                                        </span>

                                        <span class="arw"></span>

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="col-12">

                        <div class="alert alert-warning text-center">

                            No gallery found.

                        </div>

                    </div>

                @endforelse

            </div>

        </div>
    </section>

</main>
@endsection