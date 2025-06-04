<footer class="footer bg-gradient">
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-between">
            <div class="col-xxl-3 col-xl-4 flex-1 -lg-6 mb-xl-0 mb-5">
                <div class="footer-logo">
                    <a href="{{ route('front.home')  }}">
                        <img src="{{ asset($settings['footer_logo'])  }}" alt="jobs-landing" class="img-fluid"
                            style="width: 80px" />
                    </a>
                </div>
                <p class="block text-gray my-4">
                    {{ __('web.footer.newsletter_text')  }}
                </p>
                @formOpen(['id' => 'newsLetterForm'])
                    <div class="email flex">
                        {{ Form::email('email', null, [
                            'id' => 'mc-email',
                            'placeholder' => __('web.enter_your_mail'),
                            'class' => 'text-gray'
                        ])  }}
                        <div class="icon flex justify-center items-center bg-primary-600">
                            {{ Form::button('<i class="fa-solid fa-paper-plane text-white"></i>', [
                                'class' => 'icon d-flex justify-content-center align-items-center bg-primary border-0 btnLetterSave',
                                'title' => 'Subscribe'
                            ])  }}
                        </div>
                    </div>
                @formClose()
            </div>
            <div class="col-xl-2 col-lg-5 flex-1 -md-6 mb-3 ps-xl-5">
                <h3 class="mb-3 text-gray-600 fs-18">{{ __('web.footer.useful_links')  }}</h3>
                <ul class="ps-0">
                    <li>
                        <a href="{{ url('/')  }}"
                            class="text-decoration-none {{ Request::is('/') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} mb-3 d-block fs-14">{{ __('web.home')  }}</a>
                    </li>
                    <li>
                        <a href="{{ route('front.search.jobs')  }}"
                            class="text-decoration-none {{ Request::is('search-jobs') || Request::is('job-details*') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} mb-3 d-block fs-14">{{ __('web.jobs')  }}</a>
                    </li>
                    <li>
                        <a href="{{ route('front.company.lists')  }}"
                            class="text-decoration-none {{ Request::is('company-lists') || Request::is('company-details*') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} mb-3 d-block fs-14">{{ __('web.companies')  }}</a>
                    </li>
                </ul>
            </div>
            <div class="col-xl-2 lg:w-6/12 px-2 flex-1 -md-6 mb-3">
                <h3 class="mb-3 text-gray-600 fs-18">{{ __('web.web_home.helpful_resources')  }}</h3>
                <ul class="ps-0">
                    <li>
                        <a href="{{ route('front.about.us')  }}"
                            class="text-decoration-none mb-3 block {{ Request::is("about-us') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} fs-14">{{ __('web.about_us')  }}</a>
                    </li>
                    <li>
                        <a href="{{ route('front.contact')  }}"
                            class="text-decoration-none mb-3 block {{ Request::is("contact-us') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} fs-14">{{ __('web.contact_us')  }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('front.post.lists')  }}"
                            class="text-decoration-none mb-3 block {{ Request::is("posts*') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} fs-14">
                            {{ __('messages.post.blog')  }}</a>
                    </li>
                    <li>
                        <a href="{{ route('privacy.policy.list')  }}"
                            class="text-decoration-none mb-3 block {{ Request::is("privacy-policy-list') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} fs-14">{{ __('messages.setting.privacy_policy')  }}</a>
                    </li>
                    <li>
                        <a href="{{ route('terms.conditions.list')  }}"
                            class="text-decoration-none mb-3 block {{ Request::is("terms-conditions-list') ? 'footer-navbar-color-active text-dark' : 'text-gray'  }} fs-14">{{ __('messages.setting.terms_conditions')  }}</a>
                    </li>
                </ul>
            </div>
            <div class="col-xl-3 flex-1 -lg-5">
                <h3 class="mb-3 text-gray-600 fs-18">{{ __('web.contact_us')  }}</h3>
                <div class="footer-info">
                    <div class="desc flex mb-3">
                        <div class="me-3 w-20">
                            <x-icons.phone class="w-full" />
                        </div>
                        <a href="tel:{{ $settings['phone']  }}" class="fs-14 text-gray">
                            {{ $settings['phone']  }}
                        </a>
                    </div>
                    <div class="desc flex mb-3">
                        <div class="me-3 w-20">
                            <x-icons.location class="w-full" />
                        </div>
                        <p class="fs-14 text-gray mb-0">
                            {{ $settings['address']  }}
                        </p>
                    </div>
                    <div class="desc flex mb-5">
                        <div class="me-3 w-20">
                            <x-icons.mail class="w-full" />
                        </div>
                        <a href="mailto:{{ $settings['email']  }}" class="fs-14 text-gray">
                            {{ $settings['email']  }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="flex-1 -12 text-center mt-lg-5 mt-4 copy-right">
                <p class="pt-4 pb-4 text-gray fs-13">
                    &copy;{{ date('Y')  }}
                    <a href="{{ getSettingValue('company_url')  }}" class="text-primary-600">
                        {{ html_entity_decode($settings['application_name'])  }}</a>.
                    {{ __('web.footer.all_rights_reserved')  }}.
                </p>
            </div>
        </div>
    </div>
</footer>
