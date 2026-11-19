@extends('layouts.front')
@section('content')
<main class="pageWrapper">         

    <div class="cloudWrap position-relative d-none d-lg-block">
       <div class="container clouds position-relative"></div>
    </div>
    
    <section class="py-5 overflow-hidden">
       <div class="container d-flex justify-content-center">

          <div class="col-xxl-8 col-md-10 text-center">
             <nav class="breadcrumb justify-content-center mb-4 fw-semibold">
                <a class="breadcrumb-item" href="index.html">Home</a>
                <a class="breadcrumb-item" href="blogs.html">Blogs</a>
                <span class="breadcrumb-item active" aria-current="page">How Word Hunt Challenge Enhances Vocabulary</span>
             </nav>

             @php
             $image = $blog->image
                 ? asset('backend/assets/images/blog/' . $blog->image)
                 : asset('frontend/assets/images/no-image.png');
         @endphp
         
         <div class="blogContent text-start">
         
             <div class="p-4 rounded-4 shadow-lg blogPgTitle bgPurple">
                 <div class="blogDate bgRed mb-2">
                     {{ \Carbon\Carbon::parse($blog->blog_date)->format('d-m-Y') }}
                 </div>
         
                 <h1 class="sectTitle mb-0 fw-bold text-white">
                     {{ $blog->title }}
                 </h1>
             </div>
         
             <div class="blogBanner rounded-4 border mb-5 overflow-hidden animateThis fadeIn">
                 <img src="{{ $image }}" class="w-100" alt="{{ $blog->title }}">
             </div>
         
             {!! $blog->description !!}
         </div>



          </div>

       </div>
    </section>

 </main>

@endsection