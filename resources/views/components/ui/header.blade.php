<nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
        <!-- Logo and primary navigation -->
        <div class="flex items-center">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <img class="h-8 w-auto" src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name') }}">
                    <span class="hidden sm:block text-xl font-bold text-gray-900 dark:text-white">
                        {{ config('app.name') }}
                    </span>
                </a>
            </div>

            <!-- Primary Navigation -->
            <div class="hidden lg:ml-8 lg:flex lg:space-x-1">
                <x-ui.nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                    {{ __('navigation.home') }}
                </x-ui.nav-link>
                
                <x-ui.nav-link href="{{ route('jobs.index') }}" :active="request()->routeIs('jobs.*')">
                    {{ __('navigation.jobs') }}
                </x-ui.nav-link>
                
                <x-ui.nav-link href="{{ route('companies.index') }}" :active="request()->routeIs('companies.*')">
                    {{ __('navigation.companies') }}
                </x-ui.nav-link>
                
                <x-ui.nav-link href="{{ route('candidates.index') }}" :active="request()->routeIs('candidates.*')">
                    {{ __('navigation.candidates') }}
                </x-ui.nav-link>
                
                <x-ui.nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                    {{ __('navigation.about') }}
                </x-ui.nav-link>
                
                <x-ui.nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')">
                    {{ __('navigation.contact') }}
                </x-ui.nav-link>
            </div>
        </div>

        <!-- Right side: Search, Language, Theme, User menu -->
        <div class="flex items-center space-x-4">
            <!-- Quick Job Search -->
            <div class="hidden md:block">
                <x-ui.quick-search />
            </div>

            <!-- Language Switcher -->
            <x-ui.language-switcher />

            <!-- Theme Toggle -->
            <x-ui.theme-toggle />

            <!-- User Menu -->
            @auth
                <x-ui.user-menu />
            @else
                <div class="flex items-center space-x-2">
                    <x-ui.button 
                        href="{{ route('login') }}" 
                        variant="outline" 
                        size="sm"
                        class="hidden sm:inline-flex"
                    >
                        {{ __('auth.login') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        href="{{ route('register') }}" 
                        variant="primary" 
                        size="sm"
                    >
                        {{ __('auth.register') }}
                    </x-ui.button>
                </div>
            @endauth

            <!-- Mobile menu button -->
            <button 
                type="button" 
                class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700"
                aria-controls="mobile-menu" 
                aria-expanded="false"
                id="mobile-menu-button"
            >
                <span class="sr-only">{{ __('navigation.open_menu') }}</span>
                <!-- Menu icon -->
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="lg:hidden" id="mobile-menu" style="display: none;">
        <div class="pt-2 pb-3 space-y-1">
            <x-ui.mobile-nav-link href="{{ route('home') }}" :active="request()->routeIs('home')">
                {{ __('navigation.home') }}
            </x-ui.mobile-nav-link>
            
            <x-ui.mobile-nav-link href="{{ route('jobs.index') }}" :active="request()->routeIs('jobs.*')">
                {{ __('navigation.jobs') }}
            </x-ui.mobile-nav-link>
            
            <x-ui.mobile-nav-link href="{{ route('companies.index') }}" :active="request()->routeIs('companies.*')">
                {{ __('navigation.companies') }}
            </x-ui.mobile-nav-link>
            
            <x-ui.mobile-nav-link href="{{ route('candidates.index') }}" :active="request()->routeIs('candidates.*')">
                {{ __('navigation.candidates') }}
            </x-ui.mobile-nav-link>
            
            <x-ui.mobile-nav-link href="{{ route('about') }}" :active="request()->routeIs('about')">
                {{ __('navigation.about') }}
            </x-ui.mobile-nav-link>
            
            <x-ui.mobile-nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')">
                {{ __('navigation.contact') }}
            </x-ui.mobile-nav-link>
        </div>

        <!-- Mobile search -->
        <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
            <x-ui.quick-search />
        </div>

        @guest
            <!-- Mobile auth buttons -->
            <div class="pt-4 pb-3 border-t border-gray-200 dark:border-gray-700">
                <div class="space-y-2">
                    <x-ui.mobile-nav-link href="{{ route('login') }}">
                        {{ __('auth.login') }}
                    </x-ui.mobile-nav-link>
                    <x-ui.mobile-nav-link href="{{ route('register') }}">
                        {{ __('auth.register') }}
                    </x-ui.mobile-nav-link>
                </div>
            </div>
        @endguest
    </div>
</nav> 