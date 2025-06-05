@php
use App\Services\ImageService;
use App\Services\CacheService;

$imageService = app(ImageService::class);
$cacheService = app(CacheService::class);

// Default values
$lazy = $lazy ?? true;
$responsive = $responsive ?? true;
$webp = $webp ?? true;
$placeholder = $placeholder ?? true;
$quality = $quality ?? 'high';
$aspectRatio = $aspectRatio ?? null;
$classes = $classes ?? '';
$alt = $alt ?? '';

// Generate cache key for image data
$cacheKey = "optimized_image:" . md5($src . $quality . ($responsive ? 'resp' : '') . ($webp ? 'webp' : ''));

// Get or generate optimized image data
$imageData = $cacheService->remember($cacheKey, function() use ($imageService, $src, $responsive, $webp, $placeholder, $quality) {
    $data = [
        'original' => [
            'src' => $src,
            'url' => asset($src)
        ]
    ];
    
    // Generate WebP version if enabled
    if ($webp) {
        try {
            $webpPath = $imageService->convertToWebP($src);
            if ($webpPath) {
                $data['webp'] = [
                    'src' => $webpPath,
                    'url' => asset($webpPath)
                ];
            }
        } catch (\Exception $e) {
            \Log::warning("WebP conversion failed for {$src}: " . $e->getMessage());
        }
    }
    
    // Generate responsive versions if enabled
    if ($responsive) {
        try {
            $data['responsive'] = $imageService->generateResponsive($src);
        } catch (\Exception $e) {
            \Log::warning("Responsive generation failed for {$src}: " . $e->getMessage());
        }
    }
    
    // Generate placeholder if enabled
    if ($placeholder) {
        try {
            $data['placeholder'] = $imageService->generatePlaceholder($src);
        } catch (\Exception $e) {
            \Log::warning("Placeholder generation failed for {$src}: " . $e->getMessage());
        }
    }
    
    return $data;
}, 1440); // Cache for 24 hours

// Build attributes for the image
$attributes = [
    'alt' => $alt,
    'loading' => $lazy ? 'lazy' : 'eager',
    'class' => $classes
];

// Add aspect ratio class if specified
if ($aspectRatio) {
    $attributes['class'] .= ' aspect-' . $aspectRatio;
}

// If lazy loading is enabled, use data attributes
if ($lazy) {
    $attributes['data-lazy'] = '';
    $attributes['data-src'] = $imageData['original']['url'];
    
    if (isset($imageData['webp'])) {
        $attributes['data-webp'] = $imageData['webp']['url'];
    }
    
    if (isset($imageData['responsive']) && !empty($imageData['responsive'])) {
        $attributes['data-responsive'] = json_encode($imageData['responsive']);
    }
    
    if (isset($imageData['placeholder'])) {
        $attributes['data-placeholder'] = $imageData['placeholder'];
        $attributes['style'] = 'background-image: url(' . $imageData['placeholder'] . '); background-size: cover; background-position: center;';
    }
    
    // Set a 1x1 transparent pixel as initial src
    $attributes['src'] = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"><rect width="1" height="1" fill="transparent"/></svg>');
} else {
    // For eager loading, set the actual src
    $attributes['src'] = $imageData['original']['url'];
}

// Build srcset for responsive images if not using lazy loading
if (!$lazy && isset($imageData['responsive']) && !empty($imageData['responsive'])) {
    $srcset = [];
    foreach ($imageData['responsive'] as $size => $data) {
        $srcset[] = $data['url'] . ' ' . $data['width'] . 'w';
    }
    if (!empty($srcset)) {
        $attributes['srcset'] = implode(', ', $srcset);
        $attributes['sizes'] = '(max-width: 640px) 100vw, (max-width: 768px) 50vw, 33vw';
    }
}
@endphp

<div class="optimized-image-wrapper{{ $aspectRatio ? ' aspect-' . $aspectRatio : '' }}">
    @if (!$lazy && isset($imageData['webp']))
        {{-- Use picture element for non-lazy WebP support --}}
        <picture>
            <source srcset="{{ $imageData['webp']['url'] }}" type="image/webp">
            <img @foreach($attributes as $key => $value)
                {{ $key }}="{{ $value }}"
            @endforeach>
        </picture>
    @else
        {{-- Standard img element --}}
        <img @foreach($attributes as $key => $value)
            {{ $key }}="{{ $value }}"
        @endforeach>
    @endif
    
    {{-- Loading indicator for lazy loaded images --}}
    @if ($lazy)
        <noscript>
            {{-- Fallback for users with JavaScript disabled --}}
            <img src="{{ $imageData['original']['url'] }}" alt="{{ $alt }}" class="{{ $classes }} critical-image">
        </noscript>
    @endif
</div>

{{-- Performance monitoring (development only) --}}
@if (app()->environment('local') && config('app.debug'))
    <script>
        document.addEventListener('lazyImageLoaded', function(e) {
            const img = e.detail.img;
            const loadTime = performance.now();
            
            // Add performance indicator
            const indicator = document.createElement('div');
            indicator.className = 'perf-indicator ' + (loadTime < 500 ? 'fast' : loadTime < 1000 ? 'medium' : 'slow');
            indicator.textContent = Math.round(loadTime) + 'ms';
            
            const wrapper = img.closest('.optimized-image-wrapper');
            if (wrapper) {
                wrapper.style.position = 'relative';
                wrapper.appendChild(indicator);
                
                // Remove indicator after 3 seconds
                setTimeout(() => {
                    indicator.remove();
                }, 3000);
            }
        });
    </script>
@endif 