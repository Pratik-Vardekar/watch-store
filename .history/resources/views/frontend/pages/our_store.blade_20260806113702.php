@extends('layouts.front')
@section('content')
<main class="pageWrapper">

    <section class="py-5 bgGray overflow-hidden">
       <div class="container">
          <h1 class="sectTitle text-center mb-4">Our Stores</h1>

          <div class="row row-cols-md-2 justify-content-center g-5">
             <div class="col">
                <div class="storeCard rounded d-flex flex-column justify-content-between mx-auto">
                   <div class="stcContent d-flex flex-column gap-3">
                      <h4 class="stcName">The Watch Store - Mira Road</h4>
                      <div class="stcInfo stcLocation">
                         <address class="mb-0 lh-base">
                            9 & 10, B-69, Sector-1, Beside Bata Showroom, Opp. TMT Bus Stop, Shanti Nagar, Mira Road (E), Thane - 401107, Maharashtra.
                            <a href="https://www.google.com/maps/dir/Current+Location/The+Watch+Store,+Building+no.+B%2F69,+Shop+Number+9+%26+10,+opposite+TMT+Bus+Stop,+next+to+Patanjali+Store,+Sector+1,+Shanti+Nagar,+Mira+Road+East,+Thane,+Maharashtra+401107/@19.2292768,72.8132783,13z/data=!3m2!4b1!5s0x3be7b04e7a522297:0xcfbea2a033f37062!4m18!1m8!3m7!1s0x3be7b1ec36bec749:0x151a7cc1470a1f69!2sThe+Watch+Store!8m2!3d19.281327!4d72.8590166!15sChl0aGUgd2F0Y2ggc3RvcmUgbWlyYSByb2FkWhsiGXRoZSB3YXRjaCBzdG9yZSBtaXJhIHJvYWSSAQt3YXRjaF9zdG9yZeABAA!16s%2Fg%2F11g8_1fr2b!4m8!1m1!4e1!1m5!1m1!1s0x3be7b1ec36bec749:0x151a7cc1470a1f69!2m2!1d72.8590166!2d19.281327?entry=ttu&g_ep=EgoyMDI2MDcwOC4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="d-block stcDirLink">[Get Direction]</a>
                         </address>
                      </div>
                      <div class="stcInfo stcContactNo">
                         <a href="tel:+919987050065">+91 99870 50065</a>
                      </div>
                      <div class="stcInfo stcTime">Mon - Sun, 10.50 am - 10.00 pm</div>
                   </div>
                   <div class="stcImgBox">
                      <div class="swiper storeSlider">
                         <div class="swiper-wrapper">
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/stores/miraroad/1.webp') }}" />
                            </div>
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/miraroad/2.webp') }}" />
                            </div>
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/miraroad/3.webp') }}" />
                            </div>
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/miraroad/4.webp') }}" />
                            </div>
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/miraroad/5.webp') }}" />
                            </div>
                         </div>
                         <div class="swiper-pagination stcCount rounded"></div>
                      </div>
                   </div>
                </div>
             </div>
             <div class="col">
                <div class="storeCard rounded d-flex flex-column justify-content-between mx-auto">
                   <div class="stcContent d-flex flex-column gap-3">
                      <h4 class="stcName">The Watch Store - Bhayander</h4>
                      <div class="stcInfo stcLocation">
                         <address class="mb-0 lh-base">
                            10 & 11, Saroj Plaza, Next to Croma, Opp. Maxus Mall, Bhayander (W), Thane - 401101, Maharashtra.
                            <a href="https://www.google.com/maps/dir/Current+Location/The+Watch+Store,+Vishal+Building+Croma,+11%2F12,+saroj+plaza,+150,+Besides,+Bhayandar+Flyover,+opp.+Maxus+Mall,+Bhayandar+West,+Maharashtra+401101/@19.2445146,72.7766107,12z/data=!3m1!4b1!4m17!1m7!3m6!1s0x3be7b1cf89860bc1:0x574cccbb5917df4d!2sThe+Watch+Store!8m2!3d19.2951855!4d72.8481294!16s%2Fg%2F11h12hfd2w!4m8!1m1!4e1!1m5!1m1!1s0x3be7b1cf89860bc1:0x574cccbb5917df4d!2m2!1d72.8481294!2d19.2951855?entry=ttu&g_ep=EgoyMDI2MDcwOC4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="d-block stcDirLink">[Get Direction]</a>
                         </address>
                      </div>
                      <div class="stcInfo stcContactNo">
                         <a href="tel:+918452852600">+91 84528 52600</a>
                      </div>
                      <div class="stcInfo stcTime">Mon - Sun, 10.50 am - 10.00 pm</div>
                   </div>
                   <div class="stcImgBox">
                      <div class="swiper storeSlider">
                         <div class="swiper-wrapper">
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/bhayander/1.webp') }}" />
                            </div>
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/bhayander/2.webp') }}" />
                            </div>
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/bhayander/3.webp') }}" />
                            </div>
                            <div class="swiper-slide">
                               <img class="stcImg" src="{{ asset('frontend/images/store/bhayander/4.webp') }}" />
                            </div>
                         </div>
                         <div class="swiper-pagination stcCount rounded"></div>
                      </div>
                   </div>
                </div>
             </div>
          </div>
          
       </div>
    </section>         

 </main>

@endsection