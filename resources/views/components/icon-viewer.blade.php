@php
    // Get all icon components from the components/icons directory
    $iconNames = collect(glob(resource_path('views/components/icons/*.blade.php')))
        ->map(function ($path) {
            return basename($path, '.blade.php');
        })
        ->sort()
        ->toArray();
@endphp

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Available Icons</h2>
    
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($iconNames as $iconName)
            <div class="flex flex- flex-1 items-center justify-center p-4 border border-gray-300 rounded-lg hover:bg-gray-50">
                <div class="flex items-center justify-center w-12 h-12 mb-2 rounded-full bg-gray-100">
                    @php $componentName = "icons.{$iconName}" @endphp
                    <x-dynamic-component :component="$componentName" class="w-6 h-6 text-blue-600" />
                </div>
                <span class="text-sm text-gray-700">{{ $iconName  }}</span>
            </div>
        @endforeach
    </div>
</div> 