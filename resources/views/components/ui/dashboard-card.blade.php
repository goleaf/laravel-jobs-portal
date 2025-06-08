{{-- Universal Dashboard Card Component --}}
@props([
    'title' => '',
    'subtitle' => '',
    'value' => '',
    'change' => null,
    'changeType' => 'neutral', // positive, negative, neutral
    'icon' => null,
    'gradient' => false,
    'clickable' => false,
    'animate' => true,
    'href' => null
])

@php
    $baseClasses = 'dashboard-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all duration-200';
    $cardClasses = $baseClasses;
    
    if ($clickable || $href) {
        $cardClasses .= ' hover:shadow-md hover:-translate-y-1 cursor-pointer';
    }
    
    if ($gradient) {
        $cardClasses .= ' bg-gradient-to-br from-primary-500 to-primary-700 text-white border-transparent';
    }
    
    if ($animate) {
        $cardClasses .= ' animate-fade-in';
    }
    
    $changeClasses = 'metric-change text-sm font-medium inline-flex items-center';
    
    switch($changeType) {
        case 'positive':
            $changeClasses .= ' text-green-600 dark:text-green-400';
            break;
        case 'negative':
            $changeClasses .= ' text-red-600 dark:text-red-400';
            break;
        default:
            $changeClasses .= ' text-gray-600 dark:text-gray-400';
    }
@endphp

<div {{ $attributes->merge(['class' => $cardClasses]) }}
     data-animate="{{ $animate ? 'true' : 'false' }}"
     {{ $href ? 'onclick=window.location.href="' . $href . '"' : '' }}>
    
    <div class="flex items-center justify-between">
        <div class="flex-1">
            {{-- Title and Subtitle --}}
            <div class="mb-3">
                @if($subtitle)
                    <p class="metric-label text-sm {{ $gradient ? 'text-white/80' : 'text-gray-500 dark:text-gray-400' }} mb-1">
                        {{ $subtitle }}
                    </p>
                @endif
                
                @if($title)
                    <h3 class="text-lg font-semibold {{ $gradient ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                        {{ $title }}
                    </h3>
                @endif
            </div>
            
            {{-- Main Value --}}
            @if($value)
                <div class="metric-value text-3xl font-bold {{ $gradient ? 'text-white' : 'text-gray-900 dark:text-white' }} mb-2"
                     data-counter="{{ is_numeric($value) ? $value : 0 }}">
                    {{ $value }}
                </div>
            @endif
            
            {{-- Change Indicator --}}
            @if($change !== null)
                <div class="{{ $changeClasses }}">
                    @if($changeType === 'positive')
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L10 4.414 4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @elseif($changeType === 'negative')
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L10 15.586l5.293-5.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                    
                    <span>{{ $change }}</span>
                </div>
            @endif
        </div>
        
        {{-- Icon --}}
        @if($icon)
            <div class="flex-shrink-0 ml-4">
                <div class="w-12 h-12 {{ $gradient ? 'bg-white/20' : 'bg-primary-100 dark:bg-primary-900/20' }} rounded-lg flex items-center justify-center">
                    @if(is_string($icon))
                        <i class="{{ $icon }} text-xl {{ $gradient ? 'text-white' : 'text-primary-600 dark:text-primary-400' }}"></i>
                    @else
                        {{ $icon }}
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    {{-- Progress Bar (if slot content) --}}
    @if($slot->isNotEmpty())
        <div class="mt-4 pt-4 border-t {{ $gradient ? 'border-white/20' : 'border-gray-200 dark:border-gray-700' }}">
            {{ $slot }}
        </div>
    @endif
</div>

{{-- Dashboard Card Specific Styles --}}
<style>
/* Hover effects for clickable cards */
.dashboard-card:hover .metric-value {
    transform: scale(1.05);
    transition: transform 0.2s ease-in-out;
}

/* Progress bar styling */
.dashboard-card .progress-bar {
    height: 6px;
    background-color: rgba(229, 231, 235, 1);
    border-radius: 9999px;
    overflow: hidden;
}

.dark .dashboard-card .progress-bar {
    background-color: rgba(55, 65, 81, 1);
}

.dashboard-card .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #3b82f6, #1d4ed8);
    border-radius: 9999px;
    transition: width 1.5s ease-in-out;
    width: 0%;
}

/* Gradient card specific styles */
.dashboard-card.bg-gradient-to-br .progress-bar {
    background-color: rgba(255, 255, 255, 0.2);
}

.dashboard-card.bg-gradient-to-br .progress-fill {
    background: white;
}

/* Animation for counters */
@keyframes countUp {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.metric-value {
    animation: countUp 0.8s ease-out;
}

/* Responsive text sizing */
@media (max-width: 640px) {
    .metric-value {
        font-size: 1.875rem; /* text-3xl -> text-2xl on mobile */
        line-height: 2.25rem;
    }
}
</style> 