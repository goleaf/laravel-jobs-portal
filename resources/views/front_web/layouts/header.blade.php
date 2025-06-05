<!-- start header section -->

<header class="bg-color-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto">
        <div class="flex flex-wrap items-center">
            <div class="lg:w-1/12 px-2 flex-1 -4">
                <a href="{{ url('/') }}" class="header-logo">
                    <img src="{{ asset($settings['logo']) }}" alt="Jobs" class="img-fluid"/>
                </a>
            </div>
            <div class="flex-1 -lg-11 flex-1 -8">
                <nav class="bg-white shadow-sm border-b border border border-gray-300 -gray-300 -gray-200 bg-white shadow -expand-lg bg-white shadow-sm light justify-end py-0">
                    <button class="bg-white shadow-sm toggler border border border-gray-300 -gray-300 -0 p-0" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                            aria-label="Toggle navigation">
                        <span class="bg-white shadow-sm toggler-icon"></span>
                    </button>
                    <div class="collapse bg-white shadow-sm collapse justify-end" id="navbarNav">
                        <ul class="bg-white shadow-sm nav items-center py-2 py-lg-0">
                            <li class="">
                                <a class="header- bg-white shadow-sm color text-gray-900 text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("/') ? 'header-navbar-color-active' : '' }}" aria-current="page"
                                   href="{{ route('front.home') }}">{{ __('web.home') }}</a>
                            </li>
                            <li class="">
                                <a class="header- bg-white shadow-sm color text-gray-900 text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("search-jobs') || Request::is('job-details*') ? 'header-navbar-color-active' : '' }}"
                                   href="{{ route('front.') }}">{{ __('web.jobs') }}</a>
                            </li>
                            <li class="">
                                <a class="header- bg-white shadow-sm color text-gray-900 text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("company-lists') || Request::is('company-details*') ? 'header-navbar-color-active' : '' }}"
                                   href="{{ route('front.') }}">{{ __('web.companies') }}</a>
                            </li>
                            @auth
                                @role('Employer|Admin')
                                <li class="">
                                    <a class="header- bg-white shadow-sm color text-gray-900 text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("candidate-lists') || Request::is('candidate-details*') ? 'header-navbar-color-active' : '' }}"
                                       href="{{ route('candidate.') }}">{{ __('web.job_seekers') }}</a>
                                </li>
                                @endrole
                            @endauth
                            <li class="">
                                <a class="header- bg-white shadow-sm color text-gray-900 text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("about-us') ? 'header-navbar-color-active' : '' }}"
                                   href="{{ route('front.') }}">{{ __('web.about_us') }}</a>
                            </li>
                            <li class="">
                                <a class="header- bg-white shadow-sm color text-gray-900 text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("contact-us') ? 'header-navbar-color-active' : '' }}"
                                   href="{{ route('front.') }}">{{ __('web.contact_us') }}</a>
                            </li>
                            <li class="">
                                <a class="header- bg-white shadow-sm color text-gray-900 text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("posts*') ? 'header-navbar-color-active' : '' }}"
                                   href="{{ route('front.') }}">{{ __('messages.post.blog') }}</a>
                            </li>
                            <li class="">
                                    <div class="px-1 flex">
                                        <img class="country-flag" style="width:28px;" src="{{ getCurrentLanguageImage() }}" />
                                        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray-900" href="javascript:void(0)">
                                             {{ getCurrentLanguageName() }}</a>
                                    </div>
                                <ul class="flex space-x-1 submenu language-menu">
                                    @foreach(getUserLanguages() as $key => $value)
                                        <li class="languageSelection {{ (checkLanguageSession() == $key) ? 'active' : '' }}"
                                            data-prefix-value="{{ $key }}" style="max-height: 40px">
                                            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ (checkLanguageSession() == $key) ?"active' : '' }}"
                                               href="javascript:void(0)">
                                                @if(array_key_exists($key,\App\Models\User::LANGUAGES_IMAGE))
                                                    @foreach(\App\Models\User::LANGUAGES_IMAGE as $imageKey=> $imageValue)
                                                        @if($imageKey == $key)
                                                            <img class="me-2 country-flag" style="width: 20px;"
                                                                 src="{{ asset($imageValue) }}"/>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <i class="fa fa-flag me-2 fs-7 text-red-600" aria-hidden="true"
                                                       style="width: 20px;"></i>
                                                @endif
                                                {{ $value }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @if(!Auth::check())
                            <div class="text-lg-end header- px-4 py-2 rounded font-medium transition-colors grp ms-xxl-5 ms-lg-3">
                                <ul class="bg-white shadow-sm nav items-center py-2 py-lg-0">
                                    <li class="">
                                        <a href="{{ route('candidate.') }}" class="border border-gray-300 bg-transparent">{{ __('web.login') }}</a>
                                        <ul class="flex space-x-1 submenu">
                                            <li class="">
                                                <a href="{{ route('candidate.') }}"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.notification_settings.candidate') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('front.') }}"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.company.employer') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="">
                                        <a href="{{ route('candidate.register') }}" class="border border-gray-300 bg-transparent">{{ __('web.register') }}</a>
                                        <ul class="flex space-x-1 submenu">
                                            <li class="">
                                                <a href="{{ route('candidate.register') }}"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.notification_settings.candidate') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('employer.register') }}"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.company.employer') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="text-lg-end header- px-4 py-2 rounded font-medium transition-colors grp ms-xxl-5 ms-lg-3">
                                <ul class="bg-white shadow-sm nav items-center py-2 py-lg-0">
                                    <li class="">
                                        <a href="javascript:void(0)" class="mb-3 mb-lg-0 user-logo flex items-center" >
                                            <img src="{{ getLoggedInUser()->avatar }}" width="50" class="rounded object-cover"/>&nbsp;&nbsp;
                                            <span class="text-truncate"> {{ __('messages.common.hi') }}, {{ getLoggedInUser()->full_name }}</span>
                                        </a>
                                        <ul class="flex space-x-1 submenu" style="text-align: initial;">
                                            <li class="">
                                                <a href="{{ dashboardURL() }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('web.go_to_dashboard') }}
                                                </a>
                                            </li>
                                            @role('Candidate')
                                            <li class="">
                                                <a href="{{ route('candidate.') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('web.my_profile') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('favourite.jobs') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.favourite_jobs') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('favourite.companies') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.candidate_dashboard.followings') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('candidate.') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.applied_job.applied_jobs') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('candidate.') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.job.job_alert') }}
                                                </a>
                                            </li>
                                            @endrole
                                            @role('Employer')
                                            <li class="">
                                                <a href="{{ route('company.edit', \Illuminate\Support\Facades\Auth::user()->owner_id) }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('web.my_profile') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('jobs.index') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.employer_menu.jobs') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('followers.index') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.employer_menu.followers') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('subscription.index') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.employer_menu.manage_subscriptions') }}
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="{{ route('transactions.index') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center">
                                                    {{ __('messages.employer_menu.transactions') }}
                                                </a>
                                            </li>
                                            @endrole
                                            <li class="">
                                                <a href="{{ url('logout') }}" data-turbo="false"
                                                   class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center"
                                                   onclick="event.preventDefault(); localStorage.clear();  document.getElementById('logout-form').submit();">
                                                    {{ __('web.logout') }}
                                                </a>
                                                <form id="logout-form" action="{{ url('/logout') }}" method="POST"
                                                      class="hidden">
                                                    
    @csrf
{{ csrf_field() }}
                                                </form>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </nav>
            </div>
        </div>
        </div>
</header>
<!-- end header section -->
