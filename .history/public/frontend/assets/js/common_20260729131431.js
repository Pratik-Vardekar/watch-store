var $animation_elements = $('.animateThis');
var $window = $(window);

function check_if_in_view() {
   var window_height = $window.height();
   var window_top_position = $window.scrollTop();
   var window_bottom_position = (window_top_position + window_height);

   $.each($animation_elements, function () {
      var $element = $(this);
      var element_height = $element.outerHeight();
      var element_top_position = $element.offset().top + 100;
      var element_bottom_position = (element_top_position + element_height);

      //check to see if this current container is within viewport ----           
      // if ((element_top_position <= window_bottom_position) && (element_bottom_position >= window_top_position)) {

      if ((element_top_position <= window_bottom_position)) {
         $element.addClass('in-view');
      } else {
         $element.removeClass('in-view');
      }
   });
}
$window.on('scroll resize', check_if_in_view);
$window.trigger('scroll');

/*  ---------- Hide Header on on scroll down ------------  */
var didScroll;
var lastScrollTop = 0;
var delta = 0;
var navbarHeight = $('header').outerHeight();

$(window).scroll(function(event){
  didScroll = true;
});

setInterval(function() {
  if (didScroll) {
    hasScrolled();
    didScroll = false;
  }
}, 250);

function hasScrolled() {
  var st = $(this).scrollTop();
  // Make sure they scroll more than delta
  if(Math.abs(lastScrollTop - st) <= delta)
    return;
  // If they scrolled down and are past the navbar, add class .nav-up.
  // This is necessary so you never see what is "behind" the navbar.
  if (st > lastScrollTop && st > navbarHeight - 170){
    // Scroll Down
    $('body').removeClass('nav-down').addClass('nav-up');
  } else {
    // Scroll Up
    if(st + $(window).height() < $(document).height()) {
      $('body').removeClass('nav-up').addClass('nav-down');
    }
  }
  lastScrollTop = st;
}

if ($(window).width() < 992) {

  $('.menuBtn').on('click',function(){
      $('body').toggleClass('overflow-hidden');
      $('.siteNaviMain, .menuBtn').toggleClass('open');
      postCancel();
  });
  
  $('.siteNavi_L1 > li').find('.siteNavi_L2').siblings('a').removeAttr('href').attr('type','button');

  $('.siteNavi_L1 > li > a').on('click',function() {
      $(this).parent().siblings().find('.siteNavi_L2').fadeOut();
      $(this).siblings('.siteNavi_L2').fadeToggle();
      $('.siteNavi_L1').addClass('shift');
      $('.menuBackBtn').fadeIn();
  });

  $('.menuBackBtn').on('click',function(){
    postCancel();
  });

  $('.csmHead').attr('role','button');
  $('.csmHead').on('click',function() {
    $(this).parent().siblings().find('.csmList').slideUp();
    $(this).next('.csmList').slideToggle();
    $(this).parent().siblings().find('.csmHead').removeClass('active');
    $(this).toggleClass('active');
  });
  
  function postCancel() {
    $('.NavigHolder').animate({scrollTop:0},function() {
      $('.siteNavi_L1').removeClass('shift');
    });
    $('.siteNavi_L2, .menuBackBtn, .csmHead + .csmList').fadeOut();
    $('.csmHead').removeClass('active');
  }
}


const suggestTagListEl = document.querySelector('.suggestTagList');
if (suggestTagListEl) {
  new Swiper('.suggestTagList', {
    slidesPerView:"auto",spaceBetween:10,freeMode:true,mousewheel: true,slidesOffsetAfter:40,
    breakpoints: {
      768 : {spaceBetween:20},
    },
  });
}

$('.wishlistBtn').on('click', function(e){
   if ($(this).closest('form').length || $(this).is('a') || $(this).hasClass('prodDetWish') || $(this).hasClass('js-wishlist-toggle')) {
      return;
   }
   e.preventDefault();
   e.stopPropagation();
   $(this).toggleClass('saved');
});

const yearEl = document.getElementById('currentYear');
if (yearEl) yearEl.textContent = new Date().getFullYear();

$('.gotoTarget').on('click',function(){
  var gotoTarget = $(this).attr('data-bs-target');
  $('html, body').animate({scrollTop: $(gotoTarget).offset().top - 100},'500');
});

function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    || document.querySelector('input[name="_token"]')?.value
    || '';
}

function updateCartBadge(count) {
  count = parseInt(count, 10) || 0;
  document.querySelectorAll('.cart-count').forEach(function (badge) {
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
  });
}

function updateWishlistBadge(count) {
  count = parseInt(count, 10) || 0;
  document.querySelectorAll('.wishlist-count').forEach(function (badge) {
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
  });
}

function setButtonLabel(button, text) {
  const label = button.querySelector('.btnTxt');
  if (label) {
    label.textContent = text;
  } else {
    button.textContent = text;
  }
}

function getButtonLabel(button) {
  const label = button.querySelector('.btnTxt');
  return label ? label.textContent.trim() : button.textContent.trim();
}

document.addEventListener('click', function (event) {
  const wishlistBtn = event.target.closest('.js-wishlist-toggle');
  if (wishlistBtn) {
    event.preventDefault();
    event.stopPropagation();

    if (wishlistBtn.disabled) return;

    const productId = wishlistBtn.getAttribute('data-product-id');
    const url = wishlistBtn.getAttribute('data-toggle-url') || ('/wishlist/toggle/' + productId);
    if (!productId && !wishlistBtn.getAttribute('data-toggle-url')) return;

    wishlistBtn.disabled = true;

    fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({}),
    })
    .then(function (response) {
      return response.json().then(function (data) {
        return { ok: response.ok, status: response.status, data: data };
      }).catch(function () {
        return { ok: false, status: response.status, data: {} };
      });
    })
    .then(function (result) {
      if (result.status === 401 || result.data.login_required) {
        if (typeof window.showToast === 'function') {
          window.showToast('Please login to manage wishlist', 'warning');
        }
        window.location.href = result.data.redirect || '/login';
        return;
      }

      if (!result.ok || !result.data.success) {
        throw new Error(result.data.message || 'Could not update wishlist');
      }

      const inWishlist = !!result.data.in_wishlist;
      wishlistBtn.classList.toggle('saved', inWishlist);
      wishlistBtn.setAttribute('title', inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist');

      const addLabel = wishlistBtn.getAttribute('data-label-add');
      const removeLabel = wishlistBtn.getAttribute('data-label-remove');
      if (addLabel && removeLabel) {
        setButtonLabel(wishlistBtn, inWishlist ? removeLabel : addLabel);
      }

      if (typeof result.data.wishlist_count !== 'undefined') {
        updateWishlistBadge(result.data.wishlist_count);
      }

      if (typeof window.showToast === 'function') {
        window.showToast(result.data.message || (inWishlist ? 'Added to wishlist' : 'Removed from wishlist'), 'success');
      }
    })
    .catch(function (error) {
      if (typeof window.showToast === 'function') {
        window.showToast(error.message || 'Could not update wishlist', 'error');
      } else {
        alert(error.message || 'Could not update wishlist');
      }
    })
    .finally(function () {
      wishlistBtn.disabled = false;
    });

    return;
  }

  const button = event.target.closest('.js-add-to-cart');
  if (!button) return;

  event.preventDefault();
  event.stopPropagation();

  const productId = button.getAttribute('data-product-id');
  if (!productId) return;

  const url = button.getAttribute('data-add-url') || ('/cart/add/' + productId);
  const originalText = getButtonLabel(button);
  button.disabled = true;

  fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ qty: 1 }),
  })
  .then(function (response) {
    return response.json().then(function (data) {
      return { ok: response.ok, data: data };
    }).catch(function () {
      return { ok: false, data: {} };
    });
  })
  .then(function (result) {
    if (!result.ok || !result.data.success) {
      throw new Error(result.data.message || 'Could not add to cart');
    }

    if (typeof result.data.cart_count !== 'undefined') {
      updateCartBadge(result.data.cart_count);
    }

    setButtonLabel(button, result.data.already_in_cart ? 'Already in Cart' : 'Added');
    button.setAttribute('data-in-cart', '1');

    if (typeof window.showToast === 'function') {
      window.showToast(result.data.message || 'Added to cart', 'success');
    }

    setTimeout(function () {
      setButtonLabel(button, originalText);
      button.disabled = false;
    }, 1500);
  })
  .catch(function (error) {
    if (typeof window.showToast === 'function') {
      window.showToast(error.message || 'Could not add to cart. Please try again.', 'error');
    } else {
      alert(error.message || 'Could not add to cart. Please try again.');
    }
    button.disabled = false;
    setButtonLabel(button, originalText);
  });
});