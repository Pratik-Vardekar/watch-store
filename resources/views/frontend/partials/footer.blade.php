<style>
    .pageFooter .footLinks li,
    .pageFooter .footLinks li a {
        color: #777 !important;
        font-weight: 400;
    }

    .pageFooter .footLinks li a {
        text-decoration: none;
    }

    .pageFooter .footLinks li a:hover {
        color: #bf9a7c !important;
    }
</style>

@php
    $fs = $footerContactSetting ?? null;

    $footerPhone = $fs?->contact_phone ?: '+91 98922 51015';
    $footerEmail = $fs?->contact_email ?: 'watchinnovativeventures@gmail.com';
    $footerAddress =
        $fs?->contact_address ?: 'D-201, AcharatBaug, Phirozshah Mehta Road, Santacruz West, Mumbai - 400054.';
    $footerTelHref = preg_replace('/[^\d+]/', '', $footerPhone);

    $kidsImage = asset($fs?->foot_kids_image ?: 'frontend/assets/images/footKids.png');
    $kidsShadow = asset($fs?->foot_kids_shadow_image ?: 'frontend/assets/images/footKidshadow.png');

    $formTitle = $fs?->form_title ?: 'Reach out to us!';
    $formSubtitle = $fs?->form_subtitle ?: "Start your child's journey of fun-filled learning with us";

    $enrollSmall = $fs?->enroll_title_small ?: 'Enroll Your Child in';
    $enrollHighlight = $fs?->enroll_title_highlight ?: 'The Word Hunt';
    $enrollSuffix = $fs?->enroll_title_suffix ?: 'Challenge Today!';
    $enrollBtnText = $fs?->enroll_button_text ?: 'Enroll Now';
    $enrollBtnUrl = $fs?->enroll_button_url ?: '#';
    $enrollRibbon = asset($fs?->enroll_ribbon_image ?: 'assets/frontend/images/enrollRibbon.png');
    $enrollProduct = asset($fs?->enroll_product_image ?: 'assets/frontend/images/enrollImg.png');

    $connectTag = asset($fs?->connect_tag_image ?: 'assets/frontend/images/connectTag.svg');

    // SOCIAL MEDIA LINKS - DYNAMIC
    $facebookUrl = $fs?->facebook_url ?: '#';
    $twitterUrl = $fs?->twitter_url ?: '#';
    $instagramUrl = $fs?->instagram_url ?: '#';
    $youtubeUrl = $fs?->youtube_url ?: 'https://www.youtube.com/@WatchInnvovativeVentures';
    $linkedinUrl =
        $fs?->linkedin_url ?:
        ($fs?->twitter_url && str_contains($fs->twitter_url, 'linkedin')
            ? $fs->twitter_url
            : 'https://www.linkedin.com/');
    $amazonUrl = $fs?->amazon_url ?: '#';
    $amazonLogo = asset($fs?->amazon_logo_image ?: 'assets/frontend/images/amazonlogo.svg');

    // Dynamic WhatsApp Number & URL resolution
    $rawWhatsapp = $fs?->whatsapp_number;
    if ($rawWhatsapp && str_contains($rawWhatsapp, 'twitter.com')) {
        $rawWhatsapp = null;
    }
    $whatsappDisplayNumber = $rawWhatsapp ?: $footerPhone;

    $whatsappDigits = preg_replace('/[^\d]/', '', $whatsappDisplayNumber);
    if (strlen($whatsappDigits) === 10) {
        $whatsappDigits = '91' . $whatsappDigits;
    }
    $whatsappUrl = !empty($whatsappDigits) ? 'https://wa.me/' . $whatsappDigits : null;

    $copyrightText = $fs?->copyright_text ?: '© 2026 Watch. All Rights Reserved.';
@endphp

<footer class="pageFooter">
    <div class="container py-md-4 animateThis fadeIn">
        <div class="row py-5">
            <div class="col-xl-8 pe-xl-5">
                <div class="row justify-content-md-between mb-5 mb-xl-0" id="footLinks">
                    <!-- Products -->
                    <div class="col-md-auto col-sm-6 mb-lg-0 mb-sm-4 mb-3">
                        <div class="footLinkBox pb-2">
                            <h4 class="footLinkHead" data-bs-toggle="collapse" href="#footLinks_1" role="button"
                                aria-expanded="false">Products</h4>
                            <ul class="footLinks collapse d-sm-block" id="footLinks_1" data-bs-parent="#footLinks">
                                <li><a href="{{ route('product.listing', ['filter' => 'new_arrival']) }}">New
                                        Arrivals</a></li>
                                <li><a href="{{ route('product.listing', ['gender' => 'women']) }}">Women Watches</a>
                                </li>
                                <li><a href="{{ route('product.listing', ['gender' => 'men']) }}">Men Watches</a></li>
                                <li><a href="{{ route('product.listing', ['filter' => 'offers']) }}">Offers</a></li>
                                <li><a href="{{ route('brands') }}">Brands</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Services -->
                    <div class="col-md-auto col-sm-6 mb-lg-0 mb-sm-4 mb-3">
                        <div class="footLinkBox pb-2">
                            <h4 class="footLinkHead" data-bs-toggle="collapse" href="#footLinks_2" role="button"
                                aria-expanded="false">Services</h4>
                            <ul class="footLinks collapse d-sm-block" id="footLinks_2" data-bs-parent="#footLinks">
                                <li>Watch Repairs</li>
                                <li>Corporate Gifting</li>
                                <li>Battery</li>
                                <li>Glass</li>
                                <li>Straps</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="col-md-auto col-sm-6 mb-lg-0 mb-sm-4 mb-3">
                        <div class="footLinkBox pb-2">
                            <h4 class="footLinkHead" data-bs-toggle="collapse" href="#footLinks_3" role="button"
                                aria-expanded="false">Additional Info</h4>
                            <ul class="footLinks collapse d-sm-block" id="footLinks_3" data-bs-parent="#footLinks">
                                <li><a href="{{ route('about') }}">About Us</a></li>
                                <li><a href="{{ route('our.stores') }}">Our Stores</a></li>
                                <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                                <li><a href="{{ route('faqs.center') }}">FAQs</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Useful Links -->
                    <div class="col-md-auto col-sm-6 mb-lg-0 mb-sm-4 mb-3">
                        <div class="footLinkBox pb-2">
                            <h4 class="footLinkHead" data-bs-toggle="collapse" href="#footLinks_4" role="button"
                                aria-expanded="false">Useful Links</h4>
                            <ul class="footLinks collapse d-sm-block" id="footLinks_4" data-bs-parent="#footLinks">
                                <li><a href="{{ route('terms.and.conditions') }}">Terms & Conditions</a></li>
                                <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                                <li><a href="{{ route('return.policy') }}">Returns & Refunds</a></li>
                                <li><a href="{{ route('warranties.guarantees') }}">Warranties/ Guarantees</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 ps-xxl-5">
                <div class="row justify-content-between g-lg-4 gy-5">
                    <!-- Newsletter -->
                    <div class="col-xl-12 col-lg-6 col-12 pe-lg-5 pe-xl-3">
                        <form class="newsletterForm" id="footerNewsletterForm" action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf
                            <h4 class="footLinkHead">Join our newsletter</h4>
                            <div id="newsletterAlertContainer">
                                @if (session('newsletter_success'))
                                    <div class="alert alert-success py-2 px-3 mb-2"
                                        style="font-size:13px;border-radius:6px;">
                                        {{ session('newsletter_success') }}
                                    </div>
                                @endif
                                @if (session('newsletter_info'))
                                    <div class="alert alert-info py-2 px-3 mb-2" style="font-size:13px;border-radius:6px;">
                                        {{ session('newsletter_info') }}
                                    </div>
                                @endif
                                @error('email')
                                    <div class="text-danger mb-1" style="font-size:12px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="input-group nlField rounded overflow-hidden">
                                <input type="email" name="email" id="newsletterEmailInput"
                                    class="form-control bg-transparent nlInput border-0 text-white"
                                    placeholder="Email Address" aria-label="Email Address"
                                    aria-describedby="newsletterSignup" required>
                                <button class="ctaBtn nlBtn d-inline-flex align-items-center justify-content-center" type="submit" id="newsletterSignup">
                                    <span class="nlBtnSpinner spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" style="width: 14px; height: 14px; border-width: 2px;"></span>
                                    <span class="nlBtnText">Subscribe</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Connect With Us - DYNAMIC -->
                    <div class="col-xl-12 col-lg-6 col-12 ps-lg-5 ps-xl-3">
                        <h4 class="footLinkHead">Connect With Us</h4>

                        <div class="d-flex flex-wrap flex-row align-items-center gap-xxl-4 gap-2 mb-2 fw-bold w-100">
                            <div class="col-sm col-12 d-flex flex-column gap-2">
                                <!-- Dynamic Phone -->
                                <a href="tel:{{ $footerTelHref }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                                    </svg>
                                    {{ $footerPhone }}
                                </a>

                                <!-- Dynamic WhatsApp -->
                                @if ($whatsappDisplayNumber)
                                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                            <path
                                                d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.016-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.707 1.916.81 2.049c.098.133 1.39 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                                        </svg>
                                        {{ $whatsappDisplayNumber }}
                                    </a>
                                @endif

                                <!-- Dynamic Email -->
                                <a href="mailto:{{ $footerEmail }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                        fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                        <path
                                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                                    </svg>
                                    {{ $footerEmail }}
                                </a>
                            </div>

                            <div class="vr d-none d-sm-block"></div>

                            <!-- Dynamic Social Links -->
                            <div class="col-sm-auto col-12">
                                <ul class="socialLinks d-flex gap-2">
                                    @if ($facebookUrl && $facebookUrl != '#')
                                        <li>
                                            <a href="{{ $facebookUrl }}" title="Facebook" target="_blank"
                                                rel="noopener noreferrer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($instagramUrl && $instagramUrl != '#')
                                        <li>
                                            <a href="{{ $instagramUrl }}" title="Instagram" target="_blank"
                                                rel="noopener noreferrer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif

                                    @if ($youtubeUrl && $youtubeUrl != '#')
                                        <li>
                                            <a href="{{ $youtubeUrl }}" title="YouTube" target="_blank"
                                                rel="noopener noreferrer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- LinkedIn Icon (Replaced WhatsApp Icon) --}}
                                    @if ($linkedinUrl && $linkedinUrl != '#')
                                        <li>
                                            <a href="{{ $linkedinUrl }}" title="LinkedIn" target="_blank"
                                                rel="noopener noreferrer">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z" />
                                                </svg>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright - DYNAMIC -->
        <div class="d-flex flex-wrap justify-content-between footBorder text-center gap-2 py-4 mx-0">
            <div class="col-md-auto col-12">
                {!! $copyrightText !!}
            </div>
            <div class="col-md-auto col-12">
                Designed & Developed by <a href="https://www.goyalinfotech.com" target="_blank">Goyal Infotech</a>
            </div>
        </div>
    </div>

    <a role="button" class="gotoTopBtn gotoTarget" data-bs-target=".pageWrapper">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-7.5 3.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707z" />
        </svg>
    </a>
</footer>

<script>
    // Set current year in copyright
    document.getElementById('currentYear')?.textContent = new Date().getFullYear();

    // Newsletter submit with loader
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('footerNewsletterForm');
        if (!form) return;

        const emailInput = document.getElementById('newsletterEmailInput');
        const submitBtn = document.getElementById('newsletterSignup');
        const spinner = submitBtn ? submitBtn.querySelector('.nlBtnSpinner') : null;
        const btnText = submitBtn ? submitBtn.querySelector('.nlBtnText') : null;
        const alertBox = document.getElementById('newsletterAlertContainer');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const emailVal = emailInput ? emailInput.value.trim() : '';
            if (!emailVal) {
                if (emailInput) emailInput.focus();
                return;
            }

            // Show loader state
            if (spinner) spinner.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Sending...';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.85';
                submitBtn.style.pointerEvents = 'none';
            }
            if (emailInput) emailInput.readOnly = true;
            if (alertBox) alertBox.innerHTML = '';

            const tokenInput = form.querySelector('input[name="_token"]');
            const token = tokenInput ? tokenInput.value : '';

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ email: emailVal })
            })
            .then(async function(res) {
                const data = await res.json().catch(function() { return {}; });
                if (!res.ok) {
                    var errorMsg = data.message || (data.errors && data.errors.email ? data.errors.email[0] : 'Unable to subscribe. Please try again.');
                    throw new Error(errorMsg);
                }
                return data;
            })
            .then(function(data) {
                if (data.status === 'info') {
                    if (alertBox) {
                        alertBox.innerHTML = '<div class="alert alert-info py-2 px-3 mb-2" style="font-size:13px;border-radius:6px;">' + data.message + '</div>';
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast(data.message, 'info');
                    }
                } else {
                    var msg = data.message || 'Thank you for subscribing! Check your email for a confirmation.';
                    if (alertBox) {
                        alertBox.innerHTML = '<div class="alert alert-success py-2 px-3 mb-2" style="font-size:13px;border-radius:6px;">' + msg + '</div>';
                    }
                    if (typeof window.showToast === 'function') {
                        window.showToast(msg, 'success');
                    }
                    form.reset();
                }
            })
            .catch(function(err) {
                var errText = err.message || 'Something went wrong. Please try again.';
                if (alertBox) {
                    alertBox.innerHTML = '<div class="alert alert-danger py-2 px-3 mb-2" style="font-size:13px;border-radius:6px;">' + errText + '</div>';
                }
                if (typeof window.showToast === 'function') {
                    window.showToast(errText, 'error');
                }
            })
            .finally(function() {
                if (spinner) spinner.classList.add('d-none');
                if (btnText) btnText.textContent = 'Subscribe';
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '';
                    submitBtn.style.pointerEvents = '';
                }
                if (emailInput) emailInput.readOnly = false;
            });
        });
    });
</script>
