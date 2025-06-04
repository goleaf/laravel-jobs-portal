@props(['breadcrumbs' => []])

@if(count($breadcrumbs) > 0)
    @php
        $structuredData = \App\Services\SEOService::generateBreadcrumbs($breadcrumbs);
    @endphp
    
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="flex items-center">
                    @if($index > 0)
                        <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                    @if(isset($breadcrumb['url']) && $index < count($breadcrumbs) - 1)
                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-blue-600 transition-colors">
                            {{ $breadcrumb['name'] }}
                        </a>
                    @else
                        <span class="text-gray-900 font-medium">{{ $breadcrumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    
    <script type="application/ld+json">
    {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif