<header class="bg-gradient">
    <nav class="bg-white shadow-sm border-b border border border-gray-300 -gray-300 -gray-200 bg-white shadow-sm expand-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <a class="bg-white shadow-sm brand" href="{{ url('/') }}">
                <img src="{{ asset($settings['logo']) }}" alt="" class="inline-block img-fluid h-full" />
            </a>
            <div class="flex items-center">
                <button class="bg-white shadow-sm toggler border border border-gray-300 -gray-300 -0 p-0" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <div class="bg-white shadow-sm toggler-icon" id="toggler-icon">
                        <span class="icon-bar top-bar"></span>
                        <span class="icon-bar middle-bar"></span>
                        <span class="icon-bar bottom-bar"></span>
                    </div>
                </button>
                <div class="collapse bg-white shadow-sm collapse justify-content-lg-between justify-end" id="navbarNav">
                    <ul class="bg-white shadow-sm nav flex justify-end align-items-lg-center w-full">
                        <li class="">
                            <a class="header- bg-white shadow-sm color text-gray text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("/') ? 'header-navbar-color-active' : '' }}"
                                aria-current="page" href="{{ route('front.home') }}">{{ __('web.home') }}</a>
                        </li>
                        <li class="">
                            <a class="header- bg-white shadow-sm color text-gray text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("search-jobs') || Request::is('job-details*') ? 'header-navbar-color-active' : '' }}"
                                href="{{ route('front.home') }}">{{ __('web.jobs') }}</a>
                        </li>
                        <li class="">
                            <a class="header- bg-white shadow-sm color text-gray text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("company-lists') || Request::is('company-details*') ? 'header-navbar-color-active' : '' }}"
                                href="{{ route('front.home') }}">{{ __('web.companies') }}</a>
                        </li>

                        @auth
                            @role('Employer|Admin')
                                <li class="">
                                    <a class="header- bg-white shadow-sm color text-gray text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("candidate-lists') || Request::is('candidate-details*') ? 'header-navbar-color-active' : '' }}"
                                        href="{{ route('candidate.dashboard') }}">{{ __('web.job_seekers') }}</a>
                                </li>
                            @endrole
                        @endauth

                        <li class="">
                            <a class="header- bg-white shadow-sm color text-gray text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("about-us') ? 'header-navbar-color-active' : '' }}"
                                href="{{ route('front.home') }}">{{ __('web.about_us') }}</a>
                        </li>
                        <li class="">
                            <a class="header- bg-white shadow-sm color text-gray text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("contact-us') ? 'header-navbar-color-active' : '' }}"
                                href="{{ route('front.home') }}">{{ __('web.contact_us') }}</a>
                        </li>
                        <li class="">
                            <a class="header- bg-white shadow-sm color text-gray text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium {{ Request::is("posts*') ? 'header-navbar-color-active' : '' }}"
                                href="{{ route('front.home') }}">{{ __('messages.post.blog') }}</a>
                        </li>
                        <li class="">
                            <div class="relative inline-block text-left">
                                <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium text-gray inline-flex justify-center w-full rounded-md border border-gray-300 border border border-gray-300 -gray-300 -gray-300 shadow-sm px-4 py-2 rounded font-medium transition-colors" type="button"
                                     aria-expanded="false">
                                    {{ getCurrentLanguageName() }}
                                </a>
                                <ul class="language- relative inline-block text-left -menu language-menu">
                                    @foreach (getUserLanguages() as $key => $value)
                                        <li class="languageSelection {{ checkLanguageSession() == $key ? 'languageSelection-active' : '' }}"
                                            data-prefix-value="{{ $key }}">
                                            <a href="javascript:void(0)"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 text-gray flex items-center {{ checkLanguageSession() == $key ?"active' : '' }}">
                                                @if (array_key_exists($key, \App\Models\User::LANGUAGES_IMAGE))
                                                    @foreach (\App\Models\User::LANGUAGES_IMAGE as $imageKey => $imageValue)
                                                        @if ($imageKey == $key)
                                                            <img class="me-2 country-flag"
                                                                src="{{ asset($imageValue) }}" />
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <i class="fa fa-flag me-2 fs-7 text-red-600" aria-hidden="true"
                                                        style="width: 20px;"></i>
                                                @endif
                                                {{ $value }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>

                        {{ -- <div class="flex items-center gap-xl-4 gap-3 mt-lg-0 mt-2 ms-xl-3 ms-lg-2">
                            <button class="border border-gray-300 bg-transparent" type="submit">Login</button>
                            <button class="border border-gray-300 bg-transparent" type="submit">
                                Register
                            </button>
                        </div> -- }}
                        @if (!Auth::check())
                            <div class="flex items-center gap-xl-4 gap-3 mt-lg-0 mt-2 ms-xl-3 ms-lg-2">
                                <ul class="bg-white shadow-sm nav flex flex- flex flex-wrap items-center py-2 py-lg-0">
                                    <li class="login_btn">
                                        <a href="{{ route('candidate.dashboard') }}"
                                            class="border border-gray-300 bg-transparent">{{ __('web.login') }}</a>
                                        <ul class="flex space-x-1 submenu">
                                            <li class="mb-3 mt-2">
                                                <a href="{{ route('candidate.dashboard') }}"
                                                    class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                    {{ __('messages.notification_settings.candidate') }}
                                                </a>
                                            </li>
                                            <li class="mb-3">
                                                <a href="{{ route('front.home') }}"
                                                    class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                    {{ __('messages.company.employer') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="register_btn">
                                        <a href="{{ route('candidate.register') }}"
                                            class="border border-gray-300 bg-transparent">{{ __('web.register') }}</a>
                                        <ul class="flex space-x-1 submenu">
                                            <li class="mb-3 mt-2">
                                                <a href="{{ route('candidate.register') }}"
                                                    class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                    {{ __('messages.notification_settings.candidate') }}
                                                </a>
                                            </li>
                                            <li class="mb-2">
                                                <a href="{{ route('employer.register') }}"
                                                    class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                    {{ __('messages.company.employer') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="flex items-center gap-xl-4 gap-3 mt-lg-0 mt-2 ms-xl-3 ms-lg-2">
                                <ul class="bg-white shadow-sm nav items-center py-2 py-lg-0">
                                    <li class="">
                                        <a href="javascript:void(0)"
                                            class="mb-3 mb-lg-0 user-logo flex items-center">
                                            <img src="{{ getLoggedInUser()->avatar }}"
                                                style="height: 44px;width: 45px;object-fit: cover;"
                                                class="rounded object-cover" />&nbsp;&nbsp;
                                            <span class="text-truncate"> {{ __('messages.common.hi') }},
                                                {{ getLoggedInUser()->full_name }}</span>
                                        </a>
                                        <ul class="flex space-x-1 submenu" style="text-align: initial;">
                                            <li class="mb-3 mt-2">
                                                <a href="{{ dashboardURL() }}" data-turbo="false"
                                                    class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                    {{ __('web.go_to_dashboard') }}
                                                </a>
                                            </li>
                                            @role('Candidate')
                                                <li class="mb-3">
                                                    <a href="{{ route('candidate.dashboard') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('web.my_profile') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('favourite.jobs') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.favourite_jobs') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('favourite.companies') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.candidate_dashboard.followings') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('candidate.dashboard') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.applied_job.applied_jobs') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('candidate.dashboard') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.job.job_alert') }}
                                                    </a>
                                                </li>
                                            @endrole
                                            @role('Employer')
                                                <li class="mb-3">
                                                    <a href="{{ route('company.edit', \Illuminate\Support\Facades\Auth::user()->owner_id) }}"
                                                        data-turbo="false" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('web.my_profile') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('jobs.index') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.employer_menu.jobs') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('followers.index') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.employer_menu.followers') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('subscription.index') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.employer_menu.manage_subscriptions') }}
                                                    </a>
                                                </li>
                                                <li class="mb-3">
                                                    <a href="{{ route('transactions.index') }}" data-turbo="false"
                                                        class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center">
                                                        {{ __('messages.employer_menu.transactions') }}
                                                    </a>
                                                </li>
                                            @endrole
                                            <li class="mb-2">
                                                <a href="{{ url('logout') }}" data-turbo="false"
                                                    class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium text-gray flex items-center"
                                                    onclick="event.preventDefault(); localStorage.clear();  document.getElementById('logout-form').submit();">
                                                    {{ __('web.logout') }}
                                                </a>
                                                @formOpen(['id' => 'logout-form', 'url' => url('/logout'), 'class' => 'd-none'])
                                                    {{ csrf_field() }}
                                                @formClose()
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>
