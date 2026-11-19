@extends('layouts.front')
@section('content')
<main class="pageWrapper">
    <section class="py-5 bgGray overflow-hidden">
        <div class="container">
            <h1 class="sectTitle text-center mb-4">Our Stores</h1>
            <div class="row row-cols-md-2 justify-content-center g-5">
                @forelse($stores as $store)
                <div class="col">
                    <div class="storeCard rounded d-flex flex-column justify-content-between mx-auto">
                        <div class="stcContent d-flex flex-column gap-3">
                            <h4 class="stcName">{{ $store->name }}</h4>
                            <div class="stcInfo stcLocation">
                                <address class="mb-0 lh-base">
                                    {{ $store->address }}
                                    @if($store->google_maps_link)
                                        <a href="{{ $store->google_maps_link }}" target="_blank" class="d-block stcDirLink">[Get Direction]</a>
                                    @endif
                                </address>
                            </div>
                            <div class="stcInfo stcContactNo">
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $store->phone) }}">{{ $store->phone }}</a>
                            </div>
                            <div class="stcInfo stcTime">{{ $store->timing }}</div>
                        </div>
                        <div class="stcImgBox">
                            <div class="swiper storeSlider">
                                <div class="swiper-wrapper">
                                    @php $images = $store->images ?? []; @endphp
                                    @forelse($images as $image)
                                    <div class="swiper-slide">
                                        <img class="stcImg" src="{{ asset('storage/' . $image) }}" alt="{{ $store->name }} - Image {{ $loop->iteration }}" />
                                    </div>
                                    @empty
                                    <div class="swiper-slide">
                                        <img class="stcImg" src="{{ asset('frontend/assets/images/store/placeholder.webp') }}" alt="No image available" />
                                    </div>
                                    @endforelse
                                </div>
                                <div class="swiper-pagination stcCount rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No stores available at the moment.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection