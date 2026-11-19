@extends('layouts.admin')

<main class="pageWrapper">

    <section class="py-5">
       <div class="container d-flex justify-content-center">
          <div class="col-lg-9">
             
             <div class="mx-auto generalContent text-center">

                <div class="position-relative rounded-4 mb-4 overflow-hidden">
                   <img src="images/heroBanner_2.jpg" alt="" class="w-100">
                   <h1 class="sectTitle text-center text-white position-absolute top-50 start-50 translate-middle">
                    {{ $aboutSection->title ?? 'About Us' }}
                </h1>
                
                <div class="mt-4">
                    {!! nl2br(e($aboutSection->content ?? '')) !!}
                </div>
                
             </div>

          </div>
       </div>
    </section>
    
    <section class="py-4 overflow-hidden">
       <h2 class="sectTitle text-center mb-4">We Caters <span class="txtRed">20+ International</span> Brands</h2>

       <div class="swiper marqueStrip overflow-visible">
          <div class="swiper-wrapper">
             <div class="swiper-slide">
                <a href=""><img src="images/brands/anne_kle.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/armaniex_change.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/casio.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/ciga_design.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/citizen.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/daniel_klein.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/daniel_wellington.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/diesel.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/dsigner.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/emporio_armani.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/fastrack.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/fossil.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/g_shok.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/giordano.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/guess.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/ingersoll.jpg" alt=""></a>
             </div>
          </div>
       </div>
       <div class="swiper marqueStrip overflow-visible" dir="rtl">
          <div class="swiper-wrapper">
             <div class="swiper-slide">
                <a href=""><img src="images/brands/invicta.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/just_cavalli.jpg" alt="">
             </a></div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/logues.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/mathey.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/michael_kors.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/police.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/rado.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/reberto_cavalli.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/santa.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/seiko.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/skagen.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/sonata.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/timex.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/tissot.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/titan.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/tommy_hilfiger.jpg" alt=""></a>
             </div>
             <div class="swiper-slide">
                <a href=""><img src="images/brands/vintage.jpg" alt=""></a>
             </div>
          </div>
       </div>

 </section>

 

 <section class="py-4 overflow-hidden bgGray">
    <div class="container py-4 animateThis slideTop">
       <h2 class="sectTitle text-center mb-3">Watch lovers, loves us too!</h2>
       <div class="text-center sectSubTxt mb-5">Read their sweet stories about our timepieces and heartwarming service.</div>
    
       <div class="swiper pb-sm-4 pb-3 testimSwiper">
          <div class="swiper-wrapper">

             <div class="swiper-slide">
                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
                   <div class="ti_rating">
                      <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                   </div>
                   <div class="ti_review">
                      <p>I was impressed with the design and finish of the watch. It looks premium and works perfectly. Fast delivery too!</p>
                   </div>
                   <div class="ti_user pt-4 d-flex align-items-center gap-3">
                      <div class="ti_userImg rounded-circle overflow-hidden">
                         <img src="images/user_1.jpg" alt="" class="ratio ratio-1x1">
                      </div>
                      <div class="ti_userInfo">
                         <strong class="d-block fw-bold">Shubh Jain</strong>
                         <small>Verified Buyer</small>
                      </div>
                   </div>
                </div>
             </div>

             <div class="swiper-slide">
                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
                   <div class="ti_rating">
                      <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                   </div>
                   <div class="ti_review">
                      <p>I was impressed with the design and finish of the watch. It looks premium and works perfectly. Fast delivery too!</p>
                   </div>
                   <div class="ti_user pt-4 d-flex align-items-center gap-3">
                      <div class="ti_userImg rounded-circle overflow-hidden">
                         <img src="images/user_1.jpg" alt="" class="ratio ratio-1x1">
                      </div>
                      <div class="ti_userInfo">
                         <strong class="d-block fw-bold">Shubh Jain</strong>
                         <small>Verified Buyer</small>
                      </div>
                   </div>
                </div>
             </div>

             <div class="swiper-slide">
                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
                   <div class="ti_rating">
                      <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                   </div>
                   <div class="ti_review">
                      <p>I was impressed with the design and finish of the watch. It looks premium and works perfectly. Fast delivery too!</p>
                   </div>
                   <div class="ti_user pt-4 d-flex align-items-center gap-3">
                      <div class="ti_userImg rounded-circle overflow-hidden">
                         <img src="images/user_1.jpg" alt="" class="ratio ratio-1x1">
                      </div>
                      <div class="ti_userInfo">
                         <strong class="d-block fw-bold">Shubh Jain</strong>
                         <small>Verified Buyer</small>
                      </div>
                   </div>
                </div>
             </div>

             <div class="swiper-slide">
                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
                   <div class="ti_rating">
                      <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                   </div>
                   <div class="ti_review">
                      <p>I was impressed with the design and finish of the watch. It looks premium and works perfectly. Fast delivery too!</p>
                   </div>
                   <div class="ti_user pt-4 d-flex align-items-center gap-3">
                      <div class="ti_userImg rounded-circle overflow-hidden">
                         <img src="images/user_1.jpg" alt="" class="ratio ratio-1x1">
                      </div>
                      <div class="ti_userInfo">
                         <strong class="d-block fw-bold">Shubh Jain</strong>
                         <small>Verified Buyer</small>
                      </div>
                   </div>
                </div>
             </div>

             <div class="swiper-slide">
                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
                   <div class="ti_rating">
                      <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                   </div>
                   <div class="ti_review">
                      <p>I was impressed with the design and finish of the watch. It looks premium and works perfectly. Fast delivery too!</p>
                   </div>
                   <div class="ti_user pt-4 d-flex align-items-center gap-3">
                      <div class="ti_userImg rounded-circle overflow-hidden">
                         <img src="images/user_1.jpg" alt="" class="ratio ratio-1x1">
                      </div>
                      <div class="ti_userInfo">
                         <strong class="d-block fw-bold">Shubh Jain</strong>
                         <small>Verified Buyer</small>
                      </div>
                   </div>
                </div>
             </div>

             <div class="swiper-slide">
                <div class="testimItem p-md-5 p-sm-4 p-3 d-flex flex-column gap-4">
                   <div class="ti_rating">
                      <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                   </div>
                   <div class="ti_review">
                      <p>I was impressed with the design and finish of the watch. It looks premium and works perfectly. Fast delivery too!</p>
                   </div>
                   <div class="ti_user pt-4 d-flex align-items-center gap-3">
                      <div class="ti_userImg rounded-circle overflow-hidden">
                         <img src="images/user_1.jpg" alt="" class="ratio ratio-1x1">
                      </div>
                      <div class="ti_userInfo">
                         <strong class="d-block fw-bold">Shubh Jain</strong>
                         <small>Verified Buyer</small>
                      </div>
                   </div>
                </div>
             </div>

          </div>
          <div class="swiper-pagination"></div>
       </div>
    
    </div>
 </section>

 </main>

@endsection
