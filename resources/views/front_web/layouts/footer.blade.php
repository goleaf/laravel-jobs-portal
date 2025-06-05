<!-- start footer section -->
<footer class="footer bg-color-light">
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-between">
            <div class="col-xxl-3 col-xl-4 flex-1 lg-5 mb-xl-0 mb-5">
                <div class="footer-logo">
                    <a href="{{ route('front.home') }}">
                        <img src="{{ asset($settings['footer_logo']) }}" alt="jobs-landing" class="img-fluid"/>
                    </a>
                </div>
                <p class="block text-gray my-4">
                    {{ __('web.footer.newsletter_text') }}
                </p>
                <form id="newsLetterForm">
                    <div class="email flex">
                        <div class="response"></div>
                        <input type="email" id="mc-email" name="email" placeholder="{{ __('web.enter_your_mail') }}" class="footer- bg-white shadow-sm color"
                               autocomplete="off" required>
                        <button
                            class="icon flex justify-center items-center bg-primary-600 border-0 btnLetterSave"
                            title="Subscribe">
                            <i class="fa-solid fa-paper-plane text-white"></i>
                        </button>
                    </div>
                </form>
                
            </div>
            <div class="col-xl-2 col-lg-5 flex-1 md-6 mb-3 ps-xl-5">
                <h3 class="mb-3 fs-18 text-red-600">{{ __('web.footer.useful_links') }}</h3>
                <ul class="ps-0">
                    <li><a href="{{ url('/') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("/') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">{{ __('web.home') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('front.search.jobs') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("search-jobs') || Request::is('job-details*') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">{{ __('web.jobs') }}</a>
                    </li>
                    <li>
                        <a href="{{ route('front.company.lists') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("company-lists') || Request::is('company-details*') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">{{ __('web.companies') }}</a>
                    </li>
                </ul>
            </div>
            <div class="col-xl-2 col-lg-5 flex-1 md-6 mb-3">
                <h3 class="mb-3 fs-18 text-red-600">{{ __('web.web_home.helpful_resources') }}</h3>
                <ul class="ps-0">
                    <li>
                        <a href="{{ route('front.about.us') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("about-us') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">
                            {{ __('web.about_us') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.contact') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("contact-us') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">
                            {{ __('web.contact_us') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.post.lists') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("posts') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">
                            {{ __('messages.post.blog') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('privacy.policy.list') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("privacy-policy-list') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">
                            {{ __('messages.setting.privacy_policy') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('terms.conditions.list') }}"
                           class="text-decoration-none mb-3 block footer- bg-white shadow-sm color {{ Request::is("terms-conditions-list') ? 'footer-navbar-color-active' : 'text-dark' }} fs-14">
                            {{ __('messages.setting.terms_conditions') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-xl-3 flex-1 lg-5">
                <h3 class="mb-3 fs-18 text-red-600">{{ __('web.contact_us') }}</h3>
                <div class="footer-info">
                    <div class="flex footer-info__block mb-3">
                        <span class="f-icon-box"><i class="fa-solid fa-phone me-3 fs-14"></i></span>
                        <a href="tel:{{ '+'.$settings['region_code'].' '.$settings['phone'] }}"
                           class="text-decoration-none footer- bg-white shadow-sm color text-gray-900 fs-14">
                            {{ $settings['region_code'].' '.$settings['phone'] }}
                        </a>
                    </div>
                    <div class="flex footer-info__block mb-3">
                        <span class="f-icon-box"><i class="fa-solid fa-location-dot me-3 text-gray-900 fs-14"></i></span>
                        <p class="text-gray-900 mb-0 fs-14">
                            {{ $settings['address'] }}
                        </p>
                    </div>
                    <div class="flex footer-info__block mb-3">
                        <span class="f-icon-box"><i class="fa-solid fa-at me-3 mt-2  text-gray-900"></i></span>
                        <a href="mailto:{{ $settings['email'] }}" class="text-decoration-none footer- bg-white shadow-sm color text-gray-900">
                            {{ $settings['email'] }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="flex-1 -12 text-center mt-lg-5 mt-4                        copy-right">
                <p class="pt-4 pb-4 fs-14">
                    &copy;{{ date('Y') }}
                    <a href="{{ getSettingValue('company_url') }}">
                        {{ html_entity_decode($settings['application_name']) }}</a>.
                    {{ __('web.footer.all_rights_reserved') }}.
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- end footer section -->
