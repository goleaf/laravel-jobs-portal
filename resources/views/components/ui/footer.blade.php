<footer class="bg-gray-900 dark:bg-gray-950 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
            <!-- Company Info -->
            <div class="space-y-8 xl:col-span-1">
                <div>
                    <div class="flex items-center space-x-2">
                        <img class="h-8 w-auto" src="{{ asset('images/logo-white.svg') }}" alt="{{ config('app.name') }}">
                        <span class="text-xl font-bold">{{ config('app.name') }}</span>
                    </div>
                    <p class="text-gray-300 text-sm mt-4 max-w-md">
                        {{ __('footer.company_description') }}
                    </p>
                </div>
                
                <!-- Social Links -->
                <div class="flex space-x-6">
                    @if(config('social.facebook'))
                        <a href="{{ config('social.facebook') }}" class="text-gray-400 hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                            <span class="sr-only">{{ __('social.facebook') }}</span>
                            <x-icon name="globe-alt" class="h-6 w-6" />
                        </a>
                    @endif
                    
                    @if(config('social.twitter'))
                        <a href="{{ config('social.twitter') }}" class="text-gray-400 hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                            <span class="sr-only">{{ __('social.twitter') }}</span>
                            <x-icon name="globe-alt" class="h-6 w-6" />
                        </a>
                    @endif
                    
                    @if(config('social.linkedin'))
                        <a href="{{ config('social.linkedin') }}" class="text-gray-400 hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                            <span class="sr-only">{{ __('social.linkedin') }}</span>
                            <x-icon name="globe-alt" class="h-6 w-6" />
                        </a>
                    @endif
                    
                    @if(config('social.instagram'))
                        <a href="{{ config('social.instagram') }}" class="text-gray-400 hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                            <span class="sr-only">{{ __('social.instagram') }}</span>
                            <x-icon name="globe-alt" class="h-6 w-6" />
                        </a>
                    @endif
                </div>

                <!-- Contact Info -->
                <div class="space-y-2 text-sm text-gray-300">
                    @if(config('contact.email'))
                        <div class="flex items-center">
                            <x-icon name="envelope" class="h-4 w-4 mr-2" />
                            <a href="mailto:{{ config('contact.email') }}" class="hover:text-white transition-colors">
                                {{ config('contact.email') }}
                            </a>
                        </div>
                    @endif
                    
                    @if(config('contact.phone'))
                        <div class="flex items-center">
                            <x-icon name="phone" class="h-4 w-4 mr-2" />
                            <a href="tel:{{ config('contact.phone') }}" class="hover:text-white transition-colors">
                                {{ config('contact.phone') }}
                            </a>
                        </div>
                    @endif
                    
                    @if(config('contact.address'))
                        <div class="flex items-start">
                            <x-icon name="map-pin" class="h-4 w-4 mr-2 mt-0.5" />
                            <span>{{ config('contact.address') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Links Grid -->
            <div class="mt-12 grid grid-cols-2 gap-8 xl:mt-0 xl:col-span-2">
                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <!-- For Job Seekers -->
                    <div>
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">
                            {{ __('footer.for_job_seekers') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('jobs.index') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.browse_jobs') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('companies.index') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.browse_companies') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register', ['type' => 'candidate']) }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.create_profile') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('candidate.dashboard') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.candidate_dashboard') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.job_search_tips') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- For Employers -->
                    <div class="mt-12 md:mt-0">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">
                            {{ __('footer.for_employers') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('employer.job.create') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.post_job') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('candidates.index') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.browse_candidates') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('register', ['type' => 'employer']) }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.employer_signup') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('employer.dashboard') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.employer_dashboard') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.hiring_solutions') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <!-- Company -->
                    <div>
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">
                            {{ __('footer.company') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('aboutus.index') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.about_us') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.contact_us') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('posts.index') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.blog') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.careers') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.press') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div class="mt-12 md:mt-0">
                        <h3 class="text-sm font-semibold text-white tracking-wider uppercase">
                            {{ __('footer.support') }}
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.help_center') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('contact') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.faq') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('privacy.policy.list') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.privacy_policy') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('terms.conditions.list') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.terms_of_service') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sitemap') }}" class="text-sm text-gray-300 hover:text-white transition-colors">
                                    {{ __('footer.sitemap') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter Signup -->
        <div class="mt-12 pt-8 border-t border-gray-800">
            <div class="xl:grid xl:grid-cols-3 xl:gap-8 xl:items-center">
                <div class="xl:col-span-1">
                    <h3 class="text-lg font-medium text-white">
                        {{ __('footer.newsletter_title') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-300">
                        {{ __('footer.newsletter_description') }}
                    </p>
                </div>
                
                <div class="mt-4 xl:mt-0 xl:col-span-2">
                    <form class="sm:flex sm:max-w-md xl:max-w-lg" action="{{ route('news-letter.create') }}" method="POST">
                        @csrf
                        <label for="newsletter-email" class="sr-only">{{ __('footer.email_address') }}</label>
                        <input 
                            id="newsletter-email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            class="appearance-none min-w-0 w-full bg-gray-800 border border-gray-700 rounded-md py-2 px-4 text-base text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                            placeholder="{{ __('footer.enter_email') }}"
                        >
                        <div class="mt-3 rounded-md sm:mt-0 sm:ml-3 sm:flex-shrink-0">
                            <x-ui.button type="submit" variant="primary" class="w-full">
                                {{ __('footer.subscribe') }}
                            </x-ui.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-8 pt-8 border-t border-gray-800">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex items-center space-x-6">
                    <p class="text-sm text-gray-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('footer.all_rights_reserved') }}
                    </p>
                    
                    <!-- Mobile App Links -->
                    <div class="hidden sm:flex items-center space-x-4">
                        @if(config('app.ios_link'))
                            <a href="{{ config('app.ios_link') }}" class="text-gray-400 hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                                <span class="sr-only">{{ __('footer.download_ios') }}</span>
                                <x-icon name="device-phone-mobile" class="h-5 w-5" />
                            </a>
                        @endif
                        
                        @if(config('app.android_link'))
                            <a href="{{ config('app.android_link') }}" class="text-gray-400 hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                                <span class="sr-only">{{ __('footer.download_android') }}</span>
                                <x-icon name="device-phone-mobile" class="h-5 w-5" />
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Language Selector -->
                <div class="mt-4 md:mt-0">
                    <x-ui.language-switcher variant="footer" />
                </div>
            </div>
        </div>
    </div>
</footer> 