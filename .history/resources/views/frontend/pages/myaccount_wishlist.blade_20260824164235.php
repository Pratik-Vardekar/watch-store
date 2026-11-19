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
                           <strong>Mittal Solanki</strong>
                        </div>
                     </div>
                     <ul class="myAccLinks p-3 d-flex flex-column gap-4 mb-4">
                        <li><a href="myaccount_profile.html">Profile</a></li>
                        <li><a href="myaccount_orders.html">Orders</a></li>
                        <li><a href="myaccount_wishlist.html" class="active">Wishlist</a></li>
                        <li><a href="myaccount_addresses.html">Addresses</a></li>
                        <li><a href="myaccount_changepassword.html">Change Password</a></li>
                     </ul>
                     <div class="p-3 text-center w-100">
                        <button type="button" class="ctaBtn w-100 p-2">Logout</button>
                     </div>
                  </div>

              </div>
              <div class="col-lg-9 mb-lg-4">

                  <h2 class="sectTitle lh-1 mb-4">My Wishlist</h2>

                  <div class="productListing mb-5 row g-xl-4 g-md-3 g-1 row-cols-xl-4 row-cols-md-3 row-cols-2">

                     <div class="col">
                        <div class="productItem text-center animateThis fadeIn">
                           <div class="pi_topBar d-flex justify-content-between">
                              <div class="col-auto">
                                 <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                 <div class="pi_offer d-flex flex-column justify-content-center">30% <small>Off</small></div>
                              </div>
                              <div class="col-auto">
                                 <button type="button" class="wishlistBtn saved" title="Remove"></button>
                              </div>
                           </div>
                           <div class="pi_imgbox mb-4">
                              <img src="images/product_1.jpg" alt="" class="pi_img">
                           </div>
                           <div class="pi_details">
                              <h2 class="pi_prodBrand fw-bold mb-2">Titan</h2>
                              <h3 class="pi_prodName mb-3" title="Product Name">Product Name </h3>
                              <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                 <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                                 <div class="pi_reviewCount fw-semibold">(2)</div>
                              </div>
                              <div class="pi_price">
                                 <small class="pip_strike me-2">₹ 2456.00</small>
                                 <strong class="pip_ProdPrice fw-bolder">₹ 1,750.00</strong>
                              </div>
                           </div>
                           <a href="product_details.html" class="stretched-link z-3" title="Product Name"></a>
                           <div class="pt-3 position-relative z-3">
                              <button class="olBtn rounded mx-auto">Remove</button>
                           </div>
                        </div>
                     </div>
   
                     <div class="col">
                        <div class="productItem text-center animateThis fadeIn">
                           <div class="pi_topBar d-flex justify-content-between">
                              <div class="col-auto">
                                 <!-- <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                 <div class="pi_offer d-flex flex-column justify-content-center">30% <small>Off</small></div> -->
                              </div>
                              <div class="col-auto">
                                 <button type="button" class="wishlistBtn saved" title="Remove"></button>
                              </div>
                           </div>
                           <div class="pi_imgbox mb-4">
                              <img src="images/product_2.jpg" alt="" class="pi_img">
                           </div>
                           <div class="pi_details">
                              <h2 class="pi_prodBrand fw-bold mb-2">Tommy Hilfiger</h2>
                              <h3 class="pi_prodName mb-3" title="Product Name">Product Name </h3>
                              <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                 <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                                 <div class="pi_reviewCount fw-semibold">(2)</div>
                              </div>
                              <div class="pi_price">
                                 <!-- <small class="pip_strike me-2">₹ 2456.00</small> -->
                                 <strong class="pip_ProdPrice fw-bolder">₹ 1,750.00</strong>
                              </div>
                           </div>
                           <a href="product_details.html" class="stretched-link z-3" title="Product Name"></a>
                           <div class="pt-3 position-relative z-3">
                              <button class="olBtn rounded mx-auto">Remove</button>
                           </div>
                        </div>
                     </div>
   
                     <div class="col">
                        <div class="productItem text-center animateThis fadeIn">
                           <div class="pi_topBar d-flex justify-content-between">
                              <div class="col-auto">
                                 <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                 <!-- <div class="pi_offer d-flex flex-column justify-content-center">30% <small>Off</small></div> -->
                              </div>
                              <div class="col-auto">
                                 <button type="button" class="wishlistBtn saved" title="Remove"></button>
                              </div>
                           </div>
                           <div class="pi_imgbox mb-4">
                              <img src="images/product_1.jpg" alt="" class="pi_img">
                           </div>
                           <div class="pi_details">
                              <h2 class="pi_prodBrand fw-bold mb-2">Titan</h2>
                              <h3 class="pi_prodName mb-3" title="Product Name">Product Name </h3>
                              <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                 <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                                 <div class="pi_reviewCount fw-semibold">(2)</div>
                              </div>
                              <div class="pi_price">
                                 <!-- <small class="pip_strike me-2">₹ 2456.00</small> -->
                                 <strong class="pip_ProdPrice fw-bolder">₹ 1,750.00</strong>
                              </div>
                           </div>
                           <a href="product_details.html" class="stretched-link z-3" title="Product Name"></a>
                           <div class="pt-3 position-relative z-3">
                              <button class="olBtn rounded mx-auto">Remove</button>
                           </div>
                        </div>
                     </div>
   
                     <div class="col">
                        <div class="productItem text-center animateThis fadeIn">
                           <div class="pi_topBar d-flex justify-content-between">
                              <div class="col-auto">
                                 <!-- <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div> -->
                                 <div class="pi_offer d-flex flex-column justify-content-center">30% <small>Off</small></div>
                              </div>
                              <div class="col-auto">
                                 <button type="button" class="wishlistBtn saved" title="Remove"></button>
                              </div>
                           </div>
                           <div class="pi_imgbox mb-4">
                              <img src="images/product_2.jpg" alt="" class="pi_img">
                           </div>
                           <div class="pi_details">
                              <h2 class="pi_prodBrand fw-bold mb-2">Tommy Hilfiger</h2>
                              <h3 class="pi_prodName mb-3" title="Product Name">Product Name </h3>
                              <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                 <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                                 <div class="pi_reviewCount fw-semibold">(2)</div>
                              </div>
                              <div class="pi_price">
                                 <small class="pip_strike me-2">₹ 2456.00</small>
                                 <strong class="pip_ProdPrice fw-bolder">₹ 1,750.00</strong>
                              </div>
                           </div>
                           <a href="product_details.html" class="stretched-link z-3" title="Product Name"></a>
                           <div class="pt-3 position-relative z-3">
                              <button class="olBtn rounded mx-auto">Remove</button>
                           </div>
                        </div>
                     </div>
   
                     <div class="col">
                        <div class="productItem text-center animateThis fadeIn">
                           <div class="pi_topBar d-flex justify-content-between">
                              <div class="col-auto">
                                 <div class="pi_newBadge txtRed fw-medium mb-2">New Arrival</div>
                                 <div class="pi_offer d-flex flex-column justify-content-center">30% <small>Off</small></div>
                              </div>
                              <div class="col-auto">
                                 <button type="button" class="wishlistBtn saved" title="Remove"></button>
                              </div>
                           </div>
                           <div class="pi_imgbox mb-4">
                              <img src="images/product_1.jpg" alt="" class="pi_img">
                           </div>
                           <div class="pi_details">
                              <h2 class="pi_prodBrand fw-bold mb-2">Titan</h2>
                              <h3 class="pi_prodName mb-3" title="Product Name">Product Name </h3>
                              <div class="pi_rating mb-2 d-flex justify-content-center align-items-center gap-1">
                                 <div class="ratingBox"> <div class="ratedStars star4h"></div> </div>
                                 <div class="pi_reviewCount fw-semibold">(2)</div>
                              </div>
                              <div class="pi_price">
                                 <small class="pip_strike me-2">₹ 2456.00</small>
                                 <strong class="pip_ProdPrice fw-bolder">₹ 1,750.00</strong>
                              </div>
                           </div>
                           <a href="product_details.html" class="stretched-link z-3" title="Product Name"></a>
                           <div class="pt-3 position-relative z-3">
                              <button class="olBtn rounded mx-auto">Remove</button>
                           </div>
                        </div>
                     </div>
   
                  </div>

              </div>
          </div>

      </div>
   </section>

</main>
 @endsection
