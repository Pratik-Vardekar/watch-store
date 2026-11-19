@extends('layouts.front')
@section('content')
<style>/* ========================================
    Feature Icon Styles - Matching Design
    ======================================== */
 
 /* Feature Icon Wrapper - Circle with semi-transparent background */
 .feature-icon-wrapper {
     width: 60px;
     height: 60px;
     border-radius: 50%;
     background: rgba(255, 255, 255, 0.12);
     border: 2px solid rgba(255, 255, 255, 0.25);
     display: flex;
     align-items: center;
     justify-content: center;
     transition: all 0.3s ease;
     backdrop-filter: blur(2px);
     flex-shrink: 0;
 }
 
 /* Hover Effect */
 .uspItem:hover .feature-icon-wrapper {
     transform: scale(1.08);
     background: rgba(255, 255, 255, 0.2);
     border-color: rgba(255, 255, 255, 0.5);
     box-shadow: 0 0 30px rgba(255, 215, 0, 0.15);
 }
 
 /* SVG Icons inside circle */
 .feature-icon-svg {
     width: 32px;
     height: 32px;
     fill: none;
     stroke: #ffffff;
     stroke-width: 2px;
     stroke-linecap: round;
     stroke-linejoin: round;
     filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
 }
 
 /* For custom SVG with fill */
 .feature-icon-svg[fill] {
     fill: #ffffff;
     stroke: none;
 }
 
 /* Image icons */
 .feature-icon-img {
     width: 32px;
     height: 32px;
     object-fit: contain;
     filter: brightness(0) invert(1);
 }
 
 /* USP Item */
 .uspItem {
     transition: all 0.3s ease;
     padding: 5px 10px;
     border-radius: 8px;
     min-width: 140px;
 }
 
 /* Vertical Divider */
 .vr {
     opacity: 0.2;
     height: 60px;
 }
 
 /* ========================================
    Responsive Adjustments
    ======================================== */
 
 @media (max-width: 767px) {
     .uspItem {
         flex-direction: column !important;
         text-align: center !important;
         padding: 10px 5px;
         min-width: auto;
     }
 
     .feature-icon-wrapper {
         width: 50px !important;
         height: 50px !important;
     }
 
     .feature-icon-svg,
     .feature-icon-img {
         width: 28px !important;
         height: 28px !important;
     }
 
     .uspItem strong {
         font-size: 0.75rem !important;
     }
 
     .uspItem span {
         font-size: 0.7rem !important;
     }
 
     .vr {
         display: none !important;
     }
 }
 
 @media (min-width: 768px) and (max-width: 991px) {
     .uspItem {
         flex-direction: column !important;
         text-align: center !important;
         padding: 10px 5px;
         min-width: auto;
     }
 
     .feature-icon-wrapper {
         width: 55px !important;
         height: 55px !important;
     }
 
     .vr {
         display: none !important;
     }
 }
 
 /* ========================================
    Gold Text Color
    ======================================== */
 .txtGold {
     color: #d4a843;
 }
 
 .sectSubTxt {
     font-size: 0.85rem;
     letter-spacing: 2px;
     text-transform: uppercase;
 }
 
 .titleFont {
     font-family: 'Playfair Display', serif;
 }
 
 /* ========================================
    CTA Button
    ======================================== */
 .ctaBtn {
     display: inline-block;
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
     text-decoration: none;
 }
 
 /* ========================================
    Offer Section
    ======================================== */
 .offerSection {
     position: relative;
     overflow: hidden;
 }
 
 .parallax {
     position: absolute;
     top: 0;
     left: 0;
     width: 100%;
     height: 100%;
     background-size: cover;
     background-position: center;
     background-attachment: fixed;
     z-index: 0;
 }
 
 .offerContent {
     position: relative;
     z-index: 3;
     padding: 80px 0 40px;
 }
 
 /* ========================================
    Animations
    ======================================== */
 .animateThis {
     opacity: 0;
     transform: translateY(30px);
     animation: fadeInUp 0.6s ease forwards;
 }
 
 .fadeIn {
     animation-name: fadeInUp;
 }
 
 @keyframes fadeInUp {
     from {
         opacity: 0;
         transform: translateY(30px);
     }
     to {
         opacity: 1;
         transform: translateY(0);
     }
 }
 
 .slideRight {
     animation-name: slideRight;
 }
 
 @keyframes slideRight {
     from {
         opacity: 0;
         transform: translateX(-50px);
     }
     to {
         opacity: 1;
         transform: translateX(0);
     }
 }
 </style>
<main class="pageWrapper">

   {{-- Slider --}}
   <section class="homeBanner swiper overflow-hidden mb-4">
      <div class="swiper-wrapper">

          @forelse($sliders as $slider)
              <div class="swiper-slide">
                  <picture>
                      <source srcset="{{ $slider->image_url }}" media="(min-width:768px)">
                      <img src="{{ $slider->image_url }}"
                           alt="{{ $slider->title }}"
                           class="hbImg"
                           data-swiper-parallax="50%"
                           data-swiper-parallax-scale="1.5">
                  </picture>

                  <div class="heroContent text-white">
                      <div class="container h-100 d-flex"
                           data-swiper-parallax-x="100%"
                           data-swiper-parallax-opacity="0">

                          <div class="col-xl-6 col-md-7 pe-xxl-5 align-self-md-center align-self-end d-flex flex-column gap-md-4 gap-3">

                              <div class="hbHead titleFont">
                                  @if($slider->subtitle)
                                      <span class="txtGold d-block">
                                          {{ $slider->title }}
                                      </span>
                                  @endif

                                  <strong>{{ $slider->subtitle }}</strong>
                              </div>

                              @if($slider->description)
                                  <div class="hbPara">
                                      {{ $slider->description }}
                                  </div>
                              @endif

                              @if($slider->link)
                                  <div>
                                      <a href="{{ $slider->link }}" class="ctaBtn">
                                          Shop Now
                                      </a>
                                  </div>
                              @endif

                          </div>
                      </div>
                  </div>
              </div>
          @empty
              <div class="swiper-slide">
                  <picture>
                      <img src="{{ asset('frontend/assets/images/heroBanner_1.jpg') }}"
                           class="hbImg"
                           alt="No Slider">
                  </picture>

                  <div class="heroContent text-white">
                      <div class="container h-100 d-flex">
                          <div class="col-xl-6 col-md-7 align-self-center">
                              <div class="hbHead titleFont">
                                  <span class="txtGold d-block">
                                      Welcome
                                  </span>
                                  <strong>No Slider Available</strong>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          @endforelse

      </div>

      <div class="hbPagination swiper-pagination text-end text-md-start container mx-auto"></div>
  </section>

{{-- Our Brands --}}
<section class="py-md-5 py-4 overflow-hidden">
   <h2 class="sectTitle text-center mb-4">Our Brands</h2>
   @php
       $half = ceil($brands->count() / 2);
   @endphp

   <!-- First Row -->
   <div class="swiper marqueStrip overflow-visible">
       <div class="swiper-wrapper">
           @foreach($brands->slice(0, $half) as $brand)
               <div class="swiper-slide">
                   <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" title="View {{ $brand->name }} products">
                       <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}">
                   </a>
               </div>
           @endforeach
       </div>
   </div>

   <!-- Second Row -->
   <div class="swiper marqueStrip overflow-visible" dir="rtl">
       <div class="swiper-wrapper">
           @foreach($brands->slice($half) as $brand)
               <div class="swiper-slide">
                   <a href="{{ route('product.listing', ['brand' => [$brand->id]]) }}" title="View {{ $brand->name }} products">
                       <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}">
                   </a>
               </div>
           @endforeach
       </div>
   </div>

   <!-- Fallback if no brands exist -->
   @if($brands->isEmpty())
       <div class="text-center my-5">
           <p>No brands available at the moment.</p>
       </div>
   @endif
</section>

    <section class="py-md-5 py-4 mb-sm-5 mb-4 overflow-hidden">
       <div class="container">

          <div class="row g-lg-5 g-4">
             @forelse($watchBanners as $index => $banner)
                @php
                    $animation = $index % 2 === 0 ? 'slideLeft' : 'slideRight';
                    $titleParts = preg_split('/\s+/', trim((string) $banner->name), 2);
                    $labelSmall = count($titleParts) > 1 ? $titleParts[0] : 'Watches';
                    $labelLarge = count($titleParts) > 1 ? $titleParts[1] : $banner->name;
                    // Keep classic "For Him / For Her" layout when name is only that phrase.
                    if (preg_match('/^for\s+(him|her)$/i', (string) $banner->name)) {
                        $labelSmall = 'Watches';
                        $labelLarge = $banner->name;
                    }
                @endphp
                <div class="col-md animateThis {{ $animation }}">
                   <div class="catgBox rounded-3 animateThis">
                      <img src="{{ $banner->image_url }}" alt="{{ $banner->name }}" class="catgImg">
                      <div class="p-xl-5 p-4 d-flex justify-content-between align-items-center text-white w-100 position-absolute bottom-0">
                         <h2 class="catgHead mb-0">
                            <small class="fw-light">{{ $labelSmall }}</small>
                            <span class="titleFont fst-italic d-block">{{ $labelLarge }}</span>
                         </h2>
                         <a href="{{ $banner->listing_url }}" class="ctaBtn ghost mt-3">Shop Now</a>
                      </div>
                   </div>
                </div>
             @empty
                <div class="col-md animateThis slideLeft">
                   <div class="catgBox rounded-3 animateThis">
                      <img src="{{ asset('frontend/assets/images/catg_Mens.jpg') }}" alt="Watches For Him" class="catgImg">
                      <div class="p-xl-5 p-4 d-flex justify-content-between align-items-center text-white w-100 position-absolute bottom-0">
                         <h2 class="catgHead mb-0">
                            <small class="fw-light">Watches</small>
                            <span class="titleFont fst-italic d-block">For Him</span>
                         </h2>
                         <a href="{{ route('product.listing', ['gender' => 'men']) }}" class="ctaBtn ghost mt-3">Shop Now</a>
                      </div>
                   </div>
                </div>
                <div class="col-md animateThis slideRight">
                   <div class="catgBox rounded-3 animateThis">
                      <img src="{{ asset('frontend/assets/images/catg_Womens.jpg') }}" alt="Watches For Her" class="catgImg">
                      <div class="p-xl-5 p-4 d-flex justify-content-between align-items-center text-white w-100 position-absolute bottom-0">
                         <h2 class="catgHead mb-0">
                            <small class="fw-light">Watches</small>
                            <span class="titleFont fst-italic d-block">For Her</span>
                         </h2>
                         <a href="{{ route('product.listing', ['gender' => 'women']) }}" class="ctaBtn ghost mt-3">Shop Now</a>
                      </div>
                   </div>
                </div>
             @endforelse
          </div>

       </div>
    </section>

    <section class="bg-black">
       <div class="servBanWrap overflow-hidden py-sm-4">
          <img src="{{ asset('frontend/assets/images/textureBg.jpg') }}" alt="" class="servBg">

          <div class="container py-5 position-relative z-1 text-center animateThis slideTop">
             <div class="txtGold sectSubTxt mb-3">Authorised Service Center</div>
             <h2 class="sectTitle text-white">Expert Care. Original Precision.</h2>

             <div class="mt-5 mx-auto servCanvas position-relative">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 548.5 376.4" class="svgRoute">
                   <path class="travelPath" fill-rule="evenodd" clip-rule="evenodd" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="15" stroke-linecap="round" stroke-miterlimit="10" d="M411.2,123.3c0,0-171-138.5-210.6-109.3c-39.3,29,36.7,79.1,96.9,125.5c65.2,50.2,299.5,144.7,227.3,215c-76.1,74.1-299.4-221.4-410.4-306.1c-111-84.7-147.5,28.1-40,106.6s284.1,198.3,284.1,198.3"/>
                </svg>
                <img src="{{ asset('frontend/assets/images/servWatch_1.png') }}" alt="" class="servWatch_1 servWatch animateThis">
                <img src="{{ asset('frontend/assets/images/servWatch_2.png') }}" alt="" class="servWatch_2 servWatch animateThis">
             </div>
          </div>
       </div>
       <div class="servList row row-cols-lg-4 row-cols-sm-2 row-cols-2 g-0 text-white text-center">
          @forelse(($homeServiceItems ?? collect()) as $index => $serviceItem)
          <div class="col animateThis fadeIn" @if($index > 0) style="transition-delay:{{ 0.3 + ($index * 0.1) }}s;" @endif>
             <div class="servItem d-flex flex-column">
                <div class="servImgBox">
                   <img src="{{ $serviceItem->image_url }}"
                        alt="{{ $serviceItem->title }}"
                        class="servImg"
                        onerror="this.onerror=null;this.src='{{ $serviceItem->default_image_url }}';">
                   <div class="servHead">{{ $serviceItem->title }}</div>
                </div>
                <div class="servText flex-grow-1">
                   {{ $serviceItem->description }}
                </div>
             </div>
          </div>
          @empty
          <div class="col-12 py-4 text-center text-white-50">Service items coming soon.</div>
          @endforelse
       </div>
    </section>

    <section class="py-md-5 py-4 overflow-hidden bgGray">
       <div class="container py-4">
          <h2 class="sectTitle text-center mb-4">Our Bestsellers</h2>

          <div class="swiper productSlider pb-sm-5 pb-3 mb-4">
             <div class="swiper-wrapper">
                @forelse($bestSellingProducts as $product)
                   @include('frontend.partials.home-product-slide', ['product' => $product])
                @empty
                   <div class="swiper-slide">
                      <div class="productItem text-center py-5">
                         <p class="mb-0 text-muted">No bestsellers available yet.</p>
                      </div>
                   </div>
                @endforelse
             </div>
             <div class="swiper-pagination"></div>
          </div>

          <div class="text-center">
             <a href="{{ route('product.listing', ['filter' => 'best_selling']) }}" class="ctaBtn">View All</a>
          </div>
       </div>
    </section>

    {{-- More Than Just Watches --}}
    <section class="py-md-5 py-4">
      <div class="container py-4">
          <h2 class="sectTitle text-center mb-2">
              More Than Just Watches
          </h2>
  
          <div class="text-center sectSubTxt">
              Explore our extended range of products and services.
          </div>
      </div>
  
      @php
          $homepageCards = ($homeHighlights ?? collect())
              ->where('status', true)
              ->sortBy('sort_order')
              ->values();
      @endphp
  
      @if($homepageCards->isNotEmpty())
          <div class="osWrap text-white text-center row row-cols-lg-4 row-cols-sm-2 row-cols-2 g-0 pb-4">
  
              @foreach($homepageCards as $card)
                  <div class="col animateThis fadeGrow">
                      <div class="osBox d-flex flex-column justify-content-center">
  
                          <img
                              src="{{ $card->image_url }}"
                              alt="{{ $card->title }}"
                              class="osImg"
                          >
  
                          <div class="col-auto osHead d-flex align-items-center justify-content-center">
                              {{ $card->title }}
                          </div>
  
                      </div>
                  </div>
              @endforeach
  
          </div>
      @else
          <div class="osWrap text-white text-center row row-cols-lg-4 row-cols-sm-2 row-cols-2 g-0 pb-4">
              @foreach(\App\Models\HomeHighlight::DEFAULT_CARDS as $card)
                  <div class="col animateThis fadeGrow">
                      <div class="osBox d-flex flex-column justify-content-center">
                          <img
                              src="{{ asset($card['default_image']) }}"
                              alt="{{ $card['title'] }}"
                              class="osImg"
                          >
                          <div class="col-auto osHead d-flex align-items-center justify-content-center">
                              {{ $card['title'] }}
                          </div>
                      </div>
                  </div>
              @endforeach
          </div>
      @endif
    </section>

    <section class="py-md-5 py-4 overflow-hidden bgGray">
       <div class="container py-sm-4">
          <h2 class="sectTitle text-center mb-4">New Arrivals</h2>

          <div class="swiper productSlider pb-sm-5 pb-3 mb-4">
             <div class="swiper-wrapper">
                @forelse($newArrivalsProducts as $product)
                   @include('frontend.partials.home-product-slide', ['product' => $product, 'showNewBadge' => true])
                @empty
                   <div class="swiper-slide">
                      <div class="productItem text-center py-5">
                         <p class="mb-0 text-muted">No new arrivals available yet.</p>
                      </div>
                   </div>
                @endforelse
             </div>
             <div class="swiper-pagination"></div>
          </div>

          <div class="text-center">
             <a href="{{ route('product.listing', ['filter' => 'new_arrival']) }}" class="ctaBtn">View All</a>
          </div>
       </div>
    </section>

    {{-- @if($specialOffer)
    <section class="offerSection overflow-hidden">
    
        <div class="parallax"
            style="
                background-image:url('{{ $specialOffer->image_url ?? asset('frontend/assets/images/offerImg.jpg') }}');
                {{ $specialOffer->bg_color ? 'background-color:'.$specialOffer->bg_color.';' : '' }}
            ">
        </div>
    
        <div class="container">
    
            <div class="row offerContent position-relative z-3">
    
                <div class="col-xl-4 col-lg-6 col-sm-6 col-9 animateThis slideRight">
    
                    @if($specialOffer->badge_text)
                        <div class="txtGold sectSubTxt fw-medium mb-3">
                            {{ $specialOffer->badge_text }}
                        </div>
                    @endif
    
                    <h2 class="sectTitle titleFont mb-4"
                        style="color:{{ $specialOffer->text_color ?? '#fff' }}">
                        {{ $specialOffer->title }}
                    </h2>

                    @if($specialOffer->subtitle)
                        <p class="mb-3 fw-medium"
                           style="color:{{ $specialOffer->text_color ?? '#fff' }};">
                            {{ $specialOffer->subtitle }}
                        </p>
                    @endif
    
                    @if($specialOffer->description)
                        <p class="offerTxt lh-lg mb-4"
                           style="color:{{ $specialOffer->text_color ?? '#fff' }};">
                            {{ $specialOffer->description }}
                        </p>
                    @endif
    
                    @if($specialOffer->button_link)
                        <a href="{{ $specialOffer->button_link }}"
                           class="ctaBtn">
                            {{ $specialOffer->button_text }}
                        </a>
                    @endif
    
                </div>
    
            </div>
    
            @if(!empty($specialOffer->features))
                <div class="row justify-content-between g-md-0 gx-3 gy-4 py-xl-5 py-md-4 pb-4 mt-0 border-top border-white text-white position-relative z-2"
                    style="--bs-border-opacity:.5;">
    
                    @foreach($specialOffer->features as $feature)
    
                        <div class="col-md-auto col-6 py-1">
    
                            <div class="uspItem d-flex flex-lg-row flex-md-column flex-sm-row flex-column gap-3 align-items-center">
    
                                <div class="col-auto">
    
                                    @if(!empty($feature['icon']))
                                        {!! $feature['icon'] !!}
                                    @endif
    
                                </div>
    
                                <div class="col text-lg-start text-md-center text-sm-start text-center">
    
                                    <strong class="d-block fw-bold text-uppercase lh-lg">
                                        {{ $feature['title'] ?? '' }}
                                    </strong>
    
                                    {{ $feature['description'] ?? $feature['text'] ?? '' }}
    
                                </div>
    
                            </div>
    
                        </div>
    
                        @if(!$loop->last)
                            <div class="col-auto vr border-white d-none d-md-block"></div>
                        @endif
    
                    @endforeach
    
                </div>
            @endif
    
        </div>
    
    </section>
    @endif --}}
    @if($specialOffer)
    <section class="offerSection overflow-hidden">
        <div class="parallax"
            style="
                background-image:url('{{ $specialOffer->image_url ?? asset('frontend/assets/images/offerImg.jpg') }}');
                {{ $specialOffer->bg_color ? 'background-color:'.$specialOffer->bg_color.';' : '' }}
            ">
        </div>
        <div class="container">

            <div class="row offerContent position-relative z-3">
                <div class="col-xl-4 col-lg-6 col-sm-6 col-9 animateThis slideRight">

                    {{-- Display Main Logo --}}
                    @if($specialOffer->logo_url)
                        <div class="mb-3">
                            <img
                                src="{{ $specialOffer->logo_url }}"
                                alt="Offer Logo"
                                style="max-height:80px; width:auto; object-fit:contain;"
                            >
                        </div>
                    @endif

                    @if($specialOffer->badge_text)
                        <div class="txtGold sectSubTxt fw-medium mb-3">
                            {{ $specialOffer->badge_text }}
                        </div>
                    @endif

                    <h2 class="sectTitle titleFont mb-4"
                        style="color:{{ $specialOffer->text_color ?? '#fff' }}">
                        {{ $specialOffer->title }}
                    </h2>

                    @if($specialOffer->subtitle)
                        <p class="mb-3 fw-medium"
                           style="color:{{ $specialOffer->text_color ?? '#fff' }};">
                            {{ $specialOffer->subtitle }}
                        </p>
                    @endif

                    @if($specialOffer->description)
                        <p class="offerTxt lh-lg mb-4"
                           style="color:{{ $specialOffer->text_color ?? '#fff' }};
                                  opacity: 0.85;">
                            {{ $specialOffer->description }}
                        </p>
                    @endif

                    @if($specialOffer->button_link)
                        <a href="{{ $specialOffer->button_link }}"
                           class="ctaBtn">
                            {{ $specialOffer->button_text }}
                        </a>
                    @endif

                </div>
            </div>

            @if(!empty($specialOffer->features))
                <div class="row justify-content-between g-md-0 gx-3 gy-4 py-xl-5 py-md-4 pb-4 mt-0 border-top border-white text-white position-relative z-2"
                    style="--bs-border-opacity:.5;">

                    @foreach($specialOffer->features as $index => $feature)
                        @php
                            $icon = $specialOffer->feature_icons[$index] ?? null;
                            $animationDelay = 0.2 + ($index * 0.2);
                        @endphp

                        <div class="col-md-auto col-6 py-1">
                            <div class="uspItem d-flex flex-lg-row flex-md-column flex-sm-row flex-column gap-3 align-items-center animateThis fadeIn"
                                 style="transition-delay: {{ $animationDelay }}s;">

                                {{-- Feature Icon with Circle Background --}}
                                <div class="col-auto">
                                    <div class="feature-icon-wrapper">
                                        @if(!empty($icon))
                                            @if(filter_var($icon, FILTER_VALIDATE_URL) || str_starts_with($icon, 'uploads/'))
                                                <img src="{{ asset($icon) }}"
                                                     alt="{{ $feature['title'] ?? 'Feature' }}"
                                                     class="feature-icon-img"
                                                     onerror="this.style.display='none'">
                                            @elseif(str_starts_with($icon, '<svg'))
                                                <div class="feature-icon-svg">
                                                    {!! $icon !!}
                                                </div>
                                            @else
                                                <span class="text-white-50" style="font-size: 12px;">{{ $icon }}</span>
                                            @endif
                                        @else
                                            {{-- Default icons matching the exact design --}}
                                            @switch($index)
                                                @case(0)
                                                    {{-- Free Shipping / Delivery Truck Icon --}}
                                                    <svg class="feature-icon-svg" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="m507.778 255.778-86.049-88.909c-2.825-2.92-6.715-4.568-10.778-4.568h-54.987v-64.716c0-8.284-6.716-15-15-15h-278.605c-8.284 0-15 6.716-15 15v45.014h-11.354c-8.284 0-15 6.716-15 15s6.716 15 15 15h11.354v30.014h-32.359c-8.284 0-15 6.716-15 15s6.716 15 15 15h32.359v137.757c0 8.284 6.716 15 15 15h31.929c2.908 9.68 8.181 18.55 15.529 25.898 11.702 11.703 27.261 18.147 43.81 18.147s32.107-6.444 43.81-18.147c7.436-7.436 12.574-16.31 15.432-25.733h143.245c2.858 9.423 7.996 18.297 15.432 25.733 11.702 11.703 27.261 18.147 43.81 18.147s32.107-6.444 43.81-18.147c7.436-7.436 12.574-16.31 15.432-25.733h22.402c8.284 0 15-6.716 15-15v-104.325c0-3.894-1.514-7.634-4.222-10.432zm-46.17-4.568h-106.065v-58.909h49.051zm-384.249-18.598h78.073c8.284 0 15-6.716 15-15s-6.716-15-15-15h-78.073v-30.014h89.076c8.284 0 15-6.716 15-15s-6.716-15-15-15h-89.076v-30.014h89.076 159.528l-.421 242.949h-111.138c-2.275-11.692-7.93-22.851-16.967-31.889-11.702-11.703-27.261-18.147-43.81-18.147s-32.107 6.444-43.81 18.147c-8.8 8.8-14.625 19.781-16.974 31.724h-15.484zm98.865 157.443c-6.036 6.036-14.062 9.36-22.597 9.36s-16.561-3.324-22.597-9.36-9.36-14.062-9.36-22.598c0-8.537 3.324-16.563 9.36-22.599s14.062-9.36 22.597-9.36 16.561 3.324 22.597 9.36c12.46 12.461 12.46 32.736 0 45.197zm261.727 0c-6.036 6.036-14.062 9.36-22.597 9.36s-16.561-3.324-22.597-9.36c-12.46-12.461-12.46-32.735 0-45.196 6.036-6.036 14.062-9.36 22.597-9.36s16.561 3.324 22.597 9.36 9.359 14.062 9.359 22.599c.001 8.535-3.323 16.561-9.359 22.597zm38.181-34.521c-2.275-11.692-7.93-22.851-16.968-31.889-11.702-11.703-27.261-18.147-43.81-18.147s-32.107 6.444-43.81 18.147c-7.968 7.969-13.291 17.589-16.002 27.767v-70.202h126.458v74.324z"/>
                                                    </svg>
                                                    @break
                                                @case(1)
                                                    {{-- Free Returns / Exchange Icon --}}
                                                    <svg class="feature-icon-svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="m98 50c0 26.467-21.533 48-48 48s-48-21.533-48-48c0-1.658 1.342-3 3-3s3 1.342 3 3c0 23.159 18.841 42 42 42s42-18.841 42-42-18.841-42-42-42c-11.163 0-21.526 4.339-29.322 12h11.322c1.658 0 3 1.342 3 3s-1.342 3-3 3h-18c-1.658 0-3-1.342-3-3v-18c0-1.658 1.342-3 3-3s3 1.342 3 3v10.234c8.851-8.448 20.481-13.234 33-13.234 26.467 0 48 21.533 48 48zm-21-12v27c0 1.251-.776 2.37-1.945 2.81l-24 9c-.34.126-.698.19-1.055.19s-.715-.064-1.055-.19l-24-9c-1.169-.44-1.945-1.559-1.945-2.81v-27c0-1.251.776-2.37 1.945-2.81l24-9c.68-.252 1.43-.252 2.109 0l24 9c1.17.44 1.946 1.559 1.946 2.81zm-42.457 0 15.457 5.795 15.457-5.795-15.457-5.795zm-5.543 24.92 18 6.75v-20.59l-18-6.75zm42 0v-20.59l-18 6.75v20.59z"/>
                                                    </svg>
                                                    @break
                                                @case(2)
                                                    {{-- Secure Shopping / Shield Icon --}}
                                                    <svg class="feature-icon-svg" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="m505.841 183.659-91.442-134.144c-6.607-9.698-17.561-15.488-29.301-15.488h-258.203c-11.74 0-22.694 5.79-29.299 15.485l-91.454 134.16c-9.288 13.659-7.914 31.938 3.255 44.019l220.526 238.852c14.032 15.257 38.151 15.216 52.126.021l220.539-238.865c10.756-11.635 12.903-29.85 3.253-44.04zm-366.57 36.895 78.143 188.216-173.775-188.216zm138.353-156.527 61.17 126.526h-165.597l61.17-126.526zm62.615 156.527-84.243 202.906-84.242-202.907h168.485zm32.482 0h95.632l-173.775 188.216zm16.888-154.144 84.625 124.144h-102.117l-61.17-126.526h74.152c1.807-.001 3.492.889 4.51 2.382zm-267.223-.003c1.015-1.49 2.7-2.379 4.508-2.379h74.152l-61.17 126.526h-102.118z"/>
                                                    </svg>
                                                    @break
                                                @case(3)
                                                    {{-- Over 10,000 Styles / Watch Icon --}}
                                                    <svg class="feature-icon-svg" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="m24.099 62.474c-.628 0-1.264-.086-1.904-.294-2.438-.792-3.797-2.895-4.997-4.75-.634-.979-1.289-1.993-1.903-2.439-.63-.459-1.809-.775-2.949-1.081-2.12-.568-4.523-1.212-6.012-3.26-1.477-2.028-1.352-4.5-1.241-6.682.06-1.19.122-2.421-.126-3.185-.225-.694-.974-1.614-1.697-2.503-1.334-1.64-2.995-3.68-2.995-6.281s1.66-4.641 2.994-6.28c.724-.889 1.473-1.809 1.698-2.503.248-.765.186-1.995.126-3.186-.11-2.181-.235-4.654 1.241-6.683 1.489-2.046 3.892-2.69 6.011-3.259 1.141-.306 2.32-.622 2.951-1.081.614-.447 1.27-1.46 1.903-2.439 1.2-1.855 2.559-3.958 4.996-4.749 2.351-.763 4.644.111 6.667.884 1.131.432 2.302.88 3.138.88s2.007-.447 3.14-.879c2.022-.772 4.313-1.646 6.666-.884 2.438.791 3.797 2.894 4.996 4.75.634.98 1.289 1.993 1.902 2.439.632.459 1.811.775 2.951 1.081 2.119.568 4.521 1.212 6.012 3.259 1.477 2.03 1.352 4.501 1.24 6.683-.06 1.19-.122 2.421.126 3.185.226.694.974 1.614 1.697 2.503 1.335 1.639 2.995 3.679 2.995 6.281 0 2.603-1.661 4.643-2.995 6.281-.724.89-1.472 1.809-1.697 2.503-.248.764-.186 1.994-.126 3.185.111 2.182.236 4.653-1.24 6.683-1.49 2.047-3.893 2.69-6.012 3.259-1.141.306-2.32.622-2.951 1.081-.613.447-1.269 1.46-1.902 2.439-1.199 1.855-2.56 3.958-4.996 4.75-2.35.764-4.644-.111-6.666-.884-1.132-.433-2.303-.88-3.14-.88-.836 0-2.007.447-3.139.88-1.471.56-3.086 1.176-4.762 1.176zm.009-55.949c-.14 0-.264.016-.37.05-.747.242-1.594 1.552-2.341 2.708-.882 1.364-1.793 2.774-3.16 3.768-1.381 1.005-3.09 1.463-4.598 1.868-1.319.354-2.814.754-3.263 1.372-.437.6-.359 2.134-.291 3.488.079 1.566.169 3.341-.364 4.982-.514 1.582-1.562 2.87-2.576 4.115-.92 1.131-1.872 2.3-1.872 3.124s.952 1.994 1.873 3.125c1.013 1.245 2.062 2.532 2.575 4.115.533 1.64.443 3.415.364 4.981-.069 1.354-.146 2.887.29 3.486.45.618 1.945 1.02 3.265 1.373 1.507.404 3.215.862 4.597 1.867 1.366.994 2.278 2.404 3.159 3.768.748 1.156 1.595 2.467 2.342 2.709.67.218 2.088-.322 3.338-.799 1.483-.567 3.165-1.209 4.923-1.209s3.44.643 4.924 1.209c1.25.477 2.661 1.013 3.338.799.747-.242 1.594-1.553 2.341-2.709.882-1.363 1.794-2.773 3.159-3.768 1.383-1.005 3.091-1.463 4.599-1.867 1.319-.354 2.814-.755 3.264-1.372.437-.599.358-2.133.29-3.487-.079-1.566-.169-3.342.363-4.982.515-1.582 1.563-2.869 2.575-4.114.921-1.131 1.873-2.301 1.873-3.125s-.952-1.994-1.872-3.125c-1.014-1.245-2.062-2.533-2.576-4.115-.532-1.641-.442-3.416-.363-4.983.068-1.353.146-2.887-.29-3.487-.449-.617-1.944-1.018-3.264-1.371-1.507-.404-3.216-.862-4.598-1.868-1.366-.994-2.277-2.404-3.159-3.768-.747-1.156-1.595-2.466-2.342-2.708-.674-.218-2.089.322-3.338.799-1.483.567-3.164 1.209-4.923 1.209s-3.44-.642-4.923-1.208c-1.053-.403-2.223-.85-2.969-.85z"/>
                                                        <circle cx="32" cy="32" r="17.571" fill="none" stroke="#fff"/>
                                                        <path d="m29.875 38.129c-.64 0-1.279-.244-1.768-.732l-3.009-3.008c-.977-.977-.977-2.559 0-3.536s2.559-.976 3.536 0l1.241 1.241 5.491-5.491c.977-.976 2.559-.976 3.535 0 .977.976.977 2.559 0 3.536l-7.259 7.258c-.487.488-1.127.732-1.767.732z"/>
                                                    </svg>
                                                    @break
                                                @default
                                                    {{-- Default Star Icon --}}
                                                    <svg class="feature-icon-svg" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                    </svg>
                                            @endswitch
                                        @endif
                                    </div>
                                </div>

                                {{-- Feature Content --}}
                                <div class="col text-lg-start text-md-center text-sm-start text-center">
                                    <strong class="d-block fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 0.5px; line-height: 1.4;">
                                        {{ $feature['title'] ?? '' }}
                                    </strong>
                                    <span style="font-size: 0.8rem; opacity: 0.85; line-height: 1.4;">
                                        {{ $feature['description'] ?? $feature['text'] ?? '' }}
                                    </span>
                                </div>

                            </div>
                        </div>

                        @if(!$loop->last)
                            <div class="col-auto vr border-white d-none d-md-block" style="opacity: 0.2; height: 60px;"></div>
                        @endif

                    @endforeach

                </div>
            @endif

        </div>
    </section>
@endif
    {{-- <section class="py-md-5 py-4 overflow-hidden bgGray">
       <div class="container py-4 animateThis slideTop">
          <h2 class="sectTitle text-center mb-3">Watch lovers, loves us too!</h2>
          <div class="text-center sectSubTxt mb-5">Read their sweet stories about our timepieces and heartwarming service.</div>

          @if(($homeTestimonials ?? collect())->isNotEmpty())
          <div class="swiper pb-sm-4 pb-3 testimSwiper" data-slides="{{ $homeTestimonials->count() }}">
             <div class="swiper-wrapper">
                @foreach($homeTestimonials as $testimonial)
                   @php
                      $testimonialRating = (float) ($testimonial->rating ?? 0);
                      $testimonialRatingClass = match (true) {
                         $testimonialRating >= 4.75 => 'star5',
                         $testimonialRating >= 4.25 => 'star4h',
                         $testimonialRating >= 3.75 => 'star4',
                         $testimonialRating >= 3.25 => 'star3h',
                         $testimonialRating >= 2.75 => 'star3',
                         $testimonialRating >= 2.25 => 'star2h',
                         $testimonialRating >= 1.75 => 'star2',
                         $testimonialRating >= 1.25 => 'star1h',
                         $testimonialRating >= 0.75 => 'star1',
                         default => 'star0',
                      };
                      $avatarUrl = optional($testimonial->user)->avatar_url
                         ?? asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg');
                   @endphp
                   <div class="swiper-slide">
                      <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4 h-100">
                         <div class="ti_rating">
                            <div class="ratingBox"><div class="ratedStars {{ $testimonialRatingClass }}"></div></div>
                         </div>
                         <div class="ti_review">
                            <p class="mb-0">{{ $testimonial->review }}</p>
                         </div>
                         <div class="ti_user pt-4 d-flex align-items-center gap-3 mt-auto">
                            <div class="ti_userImg rounded-circle overflow-hidden flex-shrink-0">
                               <img src="{{ $avatarUrl }}" alt="{{ $testimonial->user->name ?? 'Customer' }}" class="w-100 h-100" style="object-fit:cover;">
                            </div>
                            <div class="ti_userInfo">
                               <strong class="d-block fw-bold">{{ $testimonial->user->name ?? 'Customer' }}</strong>
                               <small>
                                  @if($testimonial->product)
                                     {{ \Illuminate\Support\Str::limit($testimonial->product->name, 40) }}
                                  @else
                                     Verified Buyer
                                  @endif
                               </small>
                            </div>
                         </div>
                      </div>
                   </div>
                @endforeach
             </div>
             <div class="swiper-pagination"></div>
          </div>
          @else
          <div class="text-center text-secondary py-4">Customer reviews will appear here once approved from Admin → Reviews.</div>
          @endif
       </div>
    </section> --}}

    <section class="py-md-5 py-4 overflow-hidden bgGray">
        <div class="container py-4 animateThis slideTop">
    
            <h2 class="sectTitle text-center mb-3">
                Watch lovers, loves us too!
            </h2>
    
            <div class="text-center sectSubTxt mb-5">
                Read their sweet stories about our timepieces and heartwarming service.
            </div>
    
            @if(($homeTestimonials ?? collect())->isNotEmpty())
    
                <div class="swiper pb-sm-4 pb-3 testimSwiper">
    
                    <div class="swiper-wrapper">
    
                        @foreach($homeTestimonials as $testimonial)
    
                            @php
                                $rating = (float) ($testimonial->rating ?? 0);
    
                                $ratingClass = match (true) {
                                    $rating >= 4.75 => 'star5',
                                    $rating >= 4.25 => 'star4h',
                                    $rating >= 3.75 => 'star4',
                                    $rating >= 3.25 => 'star3h',
                                    $rating >= 2.75 => 'star3',
                                    $rating >= 2.25 => 'star2h',
                                    $rating >= 1.75 => 'star2',
                                    $rating >= 1.25 => 'star1h',
                                    $rating >= 0.75 => 'star1',
                                    default => 'star0',
                                };
    
                                $user = $testimonial->user;
    
                                $avatar = optional($user)->avatar_url
                                    ?? asset('backend/assets/images/pngtree-default-male-avatar-png-image_2811083.jpg');
    
                                $userName = optional($user)->name ?? 'Customer';
                            @endphp
    
                            <div class="swiper-slide">
    
                                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
    
                                    {{-- Rating --}}
                                    <div class="ti_rating">
                                        <div class="ratingBox">
                                            <div class="ratedStars {{ $ratingClass }}"></div>
                                        </div>
                                    </div>
    
                                    {{-- Quote --}}
                                    <div class="ti_review">
    
                                        <p class="mb-0">
                                            {{ $testimonial->review }}
                                        </p>
    
                                    </div>
    
                                    {{-- Customer --}}
                                    <div class="ti_user pt-4 d-flex align-items-center gap-3">
    
                                        <div class="ti_userImg rounded-circle overflow-hidden">
    
                                            <img
                                                src="{{ $avatar }}"
                                                alt="{{ $userName }}"
                                                class="w-100 mt-auto"
                                                style="object-fit: cover;"
                                            >+
    
                                        </div>
    
                                        <div class="ti_userInfo">
    
                                            <strong class="d-block fw-bold">
                                                {{ $userName }}
                                            </strong>
    
                                            <small>
                                                Verified Buyer
                                            </small>
    
                                        </div>
    
                                    </div>
    
                                </div>
    
                            </div>
    
                        @endforeach
    
                    </div>
    
                    {{-- Pagination --}}
                    <div class="swiper-pagination"></div>
    
                </div>
    
            @endif
    
        </div>
    </section>

 </main>
@endsection
