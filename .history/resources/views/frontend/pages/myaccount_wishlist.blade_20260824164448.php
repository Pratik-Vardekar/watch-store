@extends('layouts.front')
@section('content')
<main class="pageWrapper">

   <section class="py-5 bgGray">
      <div class="container">

          <div class="row justify-content-between mb-5">
              <div class="col-lg-3 mb-lg-4 d-none d-lg-block">

                  <div class="myAccLinkWrap shadow-lg rounded overflow-hidden">
                     <div class="d-flex p-3 mb-4 w-100 bg-black text-white">
                        <div class="col">
                           <small class="d-block">Welcome,</small>
                           <strong id="userName">{{ Auth::user()->name ?? 'User' }}</strong>
                        </div>
                     </div>
                     <ul class="myAccLinks p-3 d-flex flex-column gap-4 mb-4">
                        <li><a href="{{ route('myaccount.profile') }}">Profile</a></li>
                        <li><a href="{{ route('myaccount.orders') }}">Orders</a></li>
                        <li><a href="{{ route('myaccount.wishlist') }}" class="active">Wishlist</a></li>
                        <li><a href="{{ route('myaccount.addresses') }}">Addresses</a></li>
                        <li><a href="{{ route('myaccount.changepassword') }}">Change Password</a></li>
                     </ul>
                     <div class="p-3 text-center w-100">
                        <form action="{{ route('logout') }}" method="POST">
                           @csrf
                           <button type="submit" class="ctaBtn w-100 p-2">Logout</button>
                        </form>
                     </div>
                  </div>
              </div>
              <div class="col-lg-9 mb-lg-4">

                  <h2 class="sectTitle lh-1 mb-4">My Wishlist</h2>

                  <div id="wishlistContainer" class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2">
                     <div class="col-12 text-center py-5" id="wishlistLoader" style="display: none;">
                        <div class="spinner-border text-dark" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                     </div>
                     <!-- Wishlist items will be injected here -->
                  </div>

              </div>
          </div>

      </div>
   </section>
</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const wishlistContainer = document.getElementById('wishlistContainer');
    const wishlistLoader = document.getElementById('wishlistLoader');
    // Hide any old static content
    wishlistContainer.innerHTML = '';
    // Show the loader while fetching
    wishlistContainer.appendChild(wishlistLoader);
    wishlistLoader.style.display = 'block';

    // Fetch wishlist data dynamically via AJAX
    fetch('{{ route('api.myaccount.wishlist') }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content
        },
        credentials: 'same-origin'
    })
    .then(res => res.json())
    .then(data => {
        wishlistLoader.style.display = 'none';
        wishlistLoader.remove();
        wishlistContainer.innerHTML = '';

        if (!data || !data.length) {
            wishlistContainer.innerHTML = `
                <div class="col-12">
                   <div class="alert alert-info text-center">Your wishlist is empty.</div>
                </div>
            `;
            return;
        }

        data.forEach(wish => {
            const p = wish.product;
            if (!p) return;

            let priceHtml = '';
            if (parseFloat(p.price) > parseFloat(p.selling_price)) {
                priceHtml += `<small class="pip_strike me-2">₹ ${parseFloat(p.price).toLocaleString(undefined,{minimumFractionDigits:2})}</small>`;
            }
            priceHtml += `<strong class="pip_ProdPrice fw-bolder">₹ ${parseFloat(p.selling_price).toLocaleString(undefined,{minimumFractionDigits:2})}</strong>`;

            const badgeHtml = `
                ${(p.is_new ? `<div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>` : '')}
                ${(+p.discount ? `<div class="pi_offer d-flex flex-column justify-content-center">
                    ${p.discount}% <small>Off</small>
                </div>` : '')}
            `;

            const brandName = p.brand?.name ?? '';
            const ratingStars = Math.round((+p.averageRating || 0) * 2) / 2;
            const reviewsCount = p.reviews_count ?? 0;
            const productDetailsUrl = "{{ url('product') }}/" + encodeURIComponent(p.slug);

            // HTML Card
            const html = `
            <div class="col">
              <div class="productItem text-center animateThis fadeIn">
                <div class="pi_topBar d-flex justify-content-between">
                  <div class="col-auto">
                    ${badgeHtml}
                  </div>
                  <div class="col-auto">
                    <form class="wishlist-remove-form" data-id="${wish.id}" method="POST" action="{{ route('wishlist.remove', '_WISH_ID_') }}">
                      <input type="hidden" name="_token" value="{{ csrf_token() }}">
                      <input type="hidden" name="_method" value="DELETE">
                      <button type="submit" class="wishlistBtn saved" title="Remove"></button>
                    </form>
                  </div>
                </div>
                <div class="pi_imgbox mb-4">
                  <img src="${p.thumbnail_url || p.thumbnail || ''}" alt="${p.name}" class="pi_img">
                </div>
                <div class="pi_details">
                  <h2 class="pi_prodBrand fw-bold mb-2">${brandName}</h2>
                  <h3 class="pi_prodName mb-3" title="${p.name}">${p.name}</h3>
                  <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                     <div class="ratingBox"> 
                        <div class="ratedStars star${ratingStars}"></div>
                     </div>
                     <div class="pi_reviewCount fw-semibold">(${reviewsCount})</div>
                  </div>
                  <div class="pi_price">${priceHtml}</div>
                </div>
                <a href="${productDetailsUrl}" class="stretched-link z-3" title="${p.name}"></a>
                <div class="pt-3 position-relative z-3">
                  <form class="wishlist-remove-form" data-id="${wish.id}" method="POST" action="{{ route('wishlist.remove', '_WISH_ID_') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="olBtn rounded mx-auto">Remove</button>
                  </form>
                </div>
              </div>
            </div>
            `.replace(/_WISH_ID_/g, wish.id);

            wishlistContainer.insertAdjacentHTML('beforeend', html);
        });


        // Attach AJAX remove handlers
        wishlistContainer.querySelectorAll('.wishlist-remove-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const wishId = form.getAttribute('data-id');
                if (!confirm('Are you sure you want to remove this product from your wishlist?')) return;
                fetch(form.action.replace('_WISH_ID_', wishId), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')||{}).content
                    },
                    credentials: 'same-origin',
                    body: `_token={{ csrf_token() }}&_method=DELETE`
                })
                .then(res => res.ok ? res.json() : Promise.reject(res))
                .then(resp => {
                    // Remove wishlist item from DOM
                    form.closest('.col').remove();
                    // If wishlist is empty after removal
                    if (!wishlistContainer.querySelector('.col')) {
                        wishlistContainer.innerHTML = `
                            <div class="col-12">
                               <div class="alert alert-info text-center">Your wishlist is empty.</div>
                            </div>
                        `;
                    }
                })
                .catch(() => {
                    alert('Sorry, failed to remove the item. Try again.');
                });
            });
        });

    })
    .catch(() => {
        wishlistLoader.style.display = 'none';
        wishlistLoader.remove();
        wishlistContainer.innerHTML = `
            <div class="col-12">
               <div class="alert alert-danger text-center">Failed to load wishlist. Please refresh.</div>
            </div>
        `;
    });
});
</script>
@endpush
