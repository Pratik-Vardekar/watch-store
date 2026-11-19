
<!doctype html>
<html lang="en" data-bs-theme="light">
   <head>
      <title>The Watch Store Mira Road & Bhayender | Authorized Watches & Expert Service | 25 Years Experience</title>
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="icon" href="images/favicon.png">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
      <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css')}}?v=18">

      @stack('styles')
      @stack('custom-styles')
   </head>

   <body>
    @include('frontend.partials.header')

     @yield('content')

    @include('frontend.partials.footer')
      
      <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
      <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
      <script src="{{ asset('frontend/assets/js/common.js')}}?v=2"></script>
      <script src="{{ asset('frontend/assets/js/range-slider.js') }}"></script>
      <script>

         const homeBanner = new Swiper(".homeBanner", {
            slidesPerView:1,spaceBetween:0,speed:1000,loop:true,grabCursor:true,
            autoplay: {delay:5000,disableOnInteraction:false},
            pagination: {el: ".swiper-pagination",clickable: true,},
            parallax: true,
         });

         const marqueStrip = new Swiper(".marqueStrip", {
            slidesPerView:3,spaceBetween:1,speed:6000,loop:true,grabCursor:false,allowTouchMove: false,
            autoplay: {delay:0,disableOnInteraction:false},
            breakpoints: {
               576:{slidesPerView:4},
               768:{slidesPerView:5},
               992:{slidesPerView:6},
               1200:{slidesPerView:8},
               1400:{slidesPerView:10},
            },
         });

         const productSlider = new Swiper(".productSlider", {
            slidesPerView:1.5,spaceBetween:0,speed:1000,centeredSlides:true,
            pagination: {el: ".swiper-pagination",clickable: true,},
            breakpoints: {
               576:{slidesPerView:2,slidesPerGroup:2,centeredSlides:false,},
               992:{slidesPerView:3,slidesPerGroup:3,centeredSlides:false,},
               1400:{slidesPerView:4,slidesPerGroup:4,centeredSlides:false,},
            },
         });

         const testimSwiper = new Swiper(".testimSwiper", {
            slidesPerView:1,spaceBetween:20,speed:1000,loop:true,grabCursor:true,centeredSlides:true,
            autoplay: {delay:5000,disableOnInteraction:false},
            pagination: {el: ".swiper-pagination",clickable: true,},
            breakpoints: {
               576:{slidesPerView:1.2},
               768:{slidesPerView:1.5},
               1200:{slidesPerView:3},
            },
         });

         /* Path Animation */

         let ticking = false;
         let last_known_scroll_position = 0;
         let updatePath = false;

         const element = document.querySelector('.svgRoute');
         const path = element.querySelector('.travelPath')
         let totalLength = 0;

         initPath(path)

         function initPath(path) {
            totalLength = path.getTotalLength();
            path.style.strokeDasharray = `${totalLength}`;
            path.style.strokeDashoffset = totalLength;
         }

         let observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                  updatePath = entry.isIntersecting;
            });
         }, { rootMargin: "0px 0px 50% 50%" });
         observer.observe(element);

         function doSomething(scroll_pos) {
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

         window.addEventListener('scroll', function (e) {
            last_known_scroll_position = window.scrollY;
            if (!ticking) {
               window.requestAnimationFrame(function () {
                  doSomething(last_known_scroll_position);
                  ticking = false;
               });
               ticking = true;
            }
         });

         /* Path Animation Ends */

         const bgBox = document.querySelector('.offerSection');
         const bg    = document.querySelector('.parallax');
         if (bgBox && bg) {
            const ww = window.innerWidth  / 2;
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
   