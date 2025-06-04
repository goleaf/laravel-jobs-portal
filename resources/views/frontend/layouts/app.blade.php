<x-shared.components.layout.base :title="$title ?? config('app.name')" :seoTitle="$seoTitle ?? null" :seoDescription="$seoDescription ?? null" :seoKeywords="$seoKeywords ?? null">
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo and primary navigation -->
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}">
                                <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
                            </a>
                        </div>
                        
                        <!-- Desktop navigation -->
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('home') }}" 
                               class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('home') ? 'border-blue-500 text-gray-900' : '' }}">
                                {{ __('messages.home') }}
                            </a>
                            
                            <a href="{{ route('jobs.index') }}" 
                               class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('jobs.*') ? 'border-blue-500 text-gray-900' : '' }}">
                                {{ __('messages.find_jobs') }}
                            </a>
                            
                            <a href="{{ route('companies.index') }}" 
                               class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('companies.*') ? 'border-blue-500 text-gray-900' : '' }}">
                                {{ __('messages.companies') }}
                            </a>
                            
                            <a href="{{ route('candidates.index') }}" 
                               class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('candidates.*') ? 'border-blue-500 text-gray-900' : '' }}">
                                {{ __('messages.candidates') }}
                            </a>
                            
                            <a href="{{ route('blog.index') }}" 
                               class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('blog.*') ? 'border-blue-500 text-gray-900' : '' }}">
                                {{ __('messages.blog') }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right side navigation -->
                    <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-4">
                        <!-- Language switcher -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                </svg>
                                {{ strtoupper(app()->getLocale()) }}
                            </button>
                            <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('locale.change', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">English</a>
                                    <a href="{{ route('locale.change', 'ar') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">العربية</a>
                                    <a href="{{ route('locale.change', 'es') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Español</a>
                                    <a href="{{ route('locale.change', 'fr') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Français</a>
                                </div>
                            </div>
                        </div>
                        
                        @guest
                            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium">
                                {{ __('messages.login') }}
                            </a>
                            
                            <x-shared.components.forms.button href="{{ route('register') }}" variant="primary" size="sm">
                                {{ __('messages.sign_up') }}
                            </x-shared.components.forms.button>
                        @else
                            <!-- User dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ auth()->user()->name }}">
                                    <span class="ml-2 text-gray-700">{{ auth()->user()->name }}</span>
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                    <div class="py-1">
                                        @if(auth()->user()->hasRole('candidate'))
                                            <a href="{{ route('candidate.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ __('messages.dashboard') }}
                                            </a>
                                        @elseif(auth()->user()->hasRole('employer'))
                                            <a href="{{ route('employer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ __('messages.dashboard') }}
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('messages.profile') }}
                                        </a>
                                        
                                        <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('messages.settings') }}
                                        </a>
                                        
                                        <div class="border-t border-gray-100"></div>
                                        
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                {{ __('messages.logout') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endguest
                    </div>
                    
                    <!-- Mobile menu button -->
                    <div class="sm:hidden flex items-center">
                        <button x-data="{ open: false }" @click="open = !open" class="text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 p-2 rounded-md">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </nav>
        </header>
        
        <!-- Breadcrumbs -->
        @if(isset($breadcrumbs))
            <div class="bg-gray-50 border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <x-shared.components.navigation.breadcrumbs :breadcrumbs="$breadcrumbs" />
                </div>
            </div>
        @endif
        
        <!-- Main content -->
        <main>
            {{ $slot }}
        </main>
        
        <!-- Footer -->
        <footer class="bg-gray-800">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
                <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                    <div class="space-y-8 xl:col-span-1">
                        <img class="h-10" src="{{ asset('images/logo-white.png') }}" alt="{{ config('app.name') }}">
                        <p class="text-gray-300 text-base">
                            {{ __('messages.footer_description') }}
                        </p>
                        <div class="flex space-x-6">
                            <a href="#" class="text-gray-400 hover:text-gray-300">
                                <span class="sr-only">Facebook</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-gray-300">
                                <span class="sr-only">Twitter</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                </svg>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-gray-300">
                                <span class="sr-only">LinkedIn</span>
                                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M19 0H5a5 5 0 00-5 5v14a5 5 0 005 5h14a5 5 0 005-5V5a5 5 0 00-5-5zM8 19H5V8h3v11zM6.5 6.732c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zM20 19h-3v-5.604c0-3.368-4-3.113-4 0V19h-3V8h3v1.765c1.396-2.586 7-2.777 7 2.476V19z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">{{ __('messages.for_candidates') }}</h3>
                                <ul class="mt-4 space-y-4">
                                    <li><a href="{{ route('jobs.index') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.browse_jobs') }}</a></li>
                                    <li><a href="{{ route('candidate.register') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.create_account') }}</a></li>
                                    <li><a href="{{ route('companies.index') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.browse_companies') }}</a></li>
                                </ul>
                            </div>
                            <div class="mt-12 md:mt-0">
                                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">{{ __('messages.for_employers') }}</h3>
                                <ul class="mt-4 space-y-4">
                                    <li><a href="{{ route('employer.register') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.post_job') }}</a></li>
                                    <li><a href="{{ route('candidates.index') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.browse_candidates') }}</a></li>
                                    <li><a href="{{ route('pricing') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.pricing') }}</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">{{ __('messages.company') }}</h3>
                                <ul class="mt-4 space-y-4">
                                    <li><a href="{{ route('about') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.about_us') }}</a></li>
                                    <li><a href="{{ route('contact') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.contact_us') }}</a></li>
                                    <li><a href="{{ route('blog.index') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.blog') }}</a></li>
                                </ul>
                            </div>
                            <div class="mt-12 md:mt-0">
                                <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">{{ __('messages.legal') }}</h3>
                                <ul class="mt-4 space-y-4">
                                    <li><a href="{{ route('privacy') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.privacy_policy') }}</a></li>
                                    <li><a href="{{ route('terms') }}" class="text-base text-gray-300 hover:text-white">{{ __('messages.terms_conditions') }}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-12 border-t border-gray-700 pt-8">
                    <p class="text-base text-gray-400 xl:text-center">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') }}
                    </p>
                </div>
            </div>
        </footer>
    </div>
    
    @push('scripts')
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-shared.components.layout.base> 