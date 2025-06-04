<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Icon Library - {{ config('app.name') }}</title>

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto mx-auto py-8 px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Icon Library</h1>
            <p class="text-gray-600">A reference for all available icon components</p>
        </div>

        <x-icon-viewer />
        
        <div class="mt-8 p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Usage Examples</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium mb-2">Basic Usage</h3>
                    <pre class="bg-gray-100 p-4 rounded-md overflow-x-auto"><code>&lt;x-icons.home /&gt;</code></pre>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">With Custom Class</h3>
                    <pre class="bg-gray-100 p-4 rounded-md overflow-x-auto"><code>&lt;x-icons.user class="w-8 h-8 text-blue-500" /&gt;</code></pre>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">With Additional Attributes</h3>
                    <pre class="bg-gray-100 p-4 rounded-md overflow-x-auto"><code>&lt;x-icons.bell class="text-red-500" id="notification-icon" data-count="5" /&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 