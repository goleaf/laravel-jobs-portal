<div class="company-card bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-200">
    <!-- Banner/Header Image -->
    @if($hasBanner)
        <div class="h-32 rounded-t-lg bg-cover bg-center relative" style="background-image: url('{{ $banner() }}')">
            <div class="absolute inset-0 bg-black bg-opacity-20 rounded-t-lg"></div>
        </div>
    @else
        <div class="h-32 rounded-t-lg bg-gradient-to-r from-blue-500 to-purple-600"></div>
    @endif

    <div class="p-6 relative">
        <!-- Company Logo -->
        @if($hasLogo)
            <div class="absolute -top-8 left-6">
                <img src="{{ $logo() }}" 
                     alt="{{ $name }} logo" 
                     class="w-16 h-16 rounded-lg border-4 border-white shadow-md object-cover bg-white">
            </div>
        @endif

        <!-- Company Header -->
        <div class="mt-8">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        <a href="{{ $url() }}" class="hover:text-blue-600 transition-colors">
                            {{ $displayName }}
                        </a>
                    </h3>
                    
                    <div class="flex items-center mb-2">
                        {!! $verificationBadge() !!}
                        <span class="ml-2 text-sm text-gray-600">{{ $location }}</span>
                    </div>

                    @if($industryName)
                        <div class="text-sm text-gray-600 mb-2">
                            <span class="font-medium">{{ __('company.industry') }}:</span> {{ $industryName }}
                        </div>
                    @endif
                </div>

                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadgeClass }}">
                    {{ $isActive ? __('common.active') : __('common.inactive') }}
                </span>
            </div>

            <!-- Company Description -->
            @if($shortDescription)
                <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                    {{ $shortDescription }}
                </p>
            @endif

            <!-- Company Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                @foreach($statisticsSummary() as $key => $stat)
                    <div class="text-center">
                        <div class="text-lg font-semibold text-gray-900">
                            {{ $stat['icon'] }} {{ $formatNumber($stat['count']) }}
                        </div>
                        <div class="text-xs text-gray-500">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Company Details -->
            <div class="space-y-2 mb-4">
                @if($sizeDescription())
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="font-medium w-20">{{ __('company.size') }}:</span>
                        <span>{{ $sizeDescription() }}</span>
                    </div>
                @endif

                @if($age())
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="font-medium w-20">{{ __('company.founded') }}:</span>
                        <span>{{ $ageDescription() }}</span>
                    </div>
                @endif
            </div>

            <!-- Contact Information -->
            @if(!empty($contactInfo()))
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('company.contact_information') }}</h4>
                    <div class="space-y-1">
                        @foreach($contactInfo() as $info)
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="mr-2">{{ $info['icon'] }}</span>
                                @if($info['url'])
                                    <a href="{{ $info['url'] }}" 
                                       class="hover:text-blue-600 transition-colors"
                                       target="_blank" rel="noopener">
                                        {{ $info['value'] }}
                                    </a>
                                @else
                                    <span>{{ $info['value'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Social Media Links -->
            @if(!empty($socialLinks()))
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('company.follow_us') }}</h4>
                    <div class="flex items-center space-x-3">
                        @foreach($socialLinks() as $platform => $social)
                            <a href="{{ $social['url'] }}" 
                               target="_blank" 
                               rel="noopener"
                               class="text-gray-400 hover:text-blue-600 transition-colors"
                               title="{{ $social['name'] }}">
                                <i class="{{ $social['icon'] }} text-lg"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Footer Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-500">
                    {{ __('company.member_since') }} {{ $formatDate($createdAt, 'M Y') }}
                </div>
                
                <div class="flex items-center space-x-3">
                    @if($activeJobsCount > 0)
                        <a href="{{ $jobsUrl() }}" 
                           class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            {{ $formatNumber($activeJobsCount) }} {{ $activeJobsCount === 1 ? __('jobs.job') : __('jobs.jobs') }}
                        </a>
                    @endif
                    
                    <a href="{{ $url() }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        {{ __('company.view_profile') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Structured Data for SEO -->
<script type="application/ld+json">
{!! json_encode($structuredData()) !!}
</script> 