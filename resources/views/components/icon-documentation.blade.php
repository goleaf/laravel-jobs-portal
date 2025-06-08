
@push('styles')
    @vite('resources/css/components/icon-documentation.css')
@endpush
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Icon Documentation') }} - {{ config('app.name') }}</title>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border border border-gray-300 -gray-300 -gray-200 bg-white shadow -expand-lg bg-white shadow-sm dark bg-indigo-600 -600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <a class="bg-white shadow-sm brand" href="{{ route('front.home') }}">
                <i class="fas fa-briefcase me-2"></i>
                {{ config('app.name') }}
            </a>
            <div class="bg-white shadow-sm nav ms-auto">
                <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium" href="{{ route('front.home') }}">
                    <i class="fas fa-home me-1"></i>
                    {{ __('Home') }}
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid py-4">
        <div class="flex flex-wrap">
            <!-- Sidebar with Search -->
            <div class="flex-1 md-3">
                <div class="search-box">
                    <div class="bg-white shadow rounded -lg overflow-hidden">
                        <div class="bg-white shadow rounded -lg overflow-hidden body">
                            <h5 class="bg-white shadow rounded -lg overflow-hidden title">
                                <i class="fas fa-search me-2"></i>
                                {{ __('Icon Search') }}
                            </h5>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500" id="iconSearch" placeholder="{{ __('Search icons...') }}">
                            
                            <hr>
                            
                            <h6>{{ __('Categories') }}</h6>
                            <div class="px-4 py-2 rounded font-medium transition-colors group-vertical grid gap-1" role="group">
                                <button type="button" class="rounded-md transition" data-category="all">
                                    {{ __('All Icons') }}
                                </button>
                                <button type="button" class="rounded-md transition" data-category="ui">
                                    {{ __('UI Icons') }}
                                </button>
                                <button type="button" class="rounded-md transition" data-category="business">
                                    {{ __('Business') }}
                                </button>
                                <button type="button" class="rounded-md transition" data-category="social">
                                    {{ __('Social Media') }}
                                </button>
                                <button type="button" class="rounded-md transition" data-category="file">
                                    {{ __('Files & Docs') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 md-9">
                <div class="mb-4">
                    <h1 class="display-5 fw-bold">
                        <i class="fas fa-icons text-indigo-600 -600 me-3"></i>
                        {{ __('Icon Documentation') }}
                    </h1>
                    <p class="lead text-gray-500">
                        {{ __('Complete reference of all available FontAwesome icons used in the job portal') }}
                    </p>
                </div>

                <!-- Usage Instructions -->
                <div class="bg-white shadow rounded -lg overflow-hidden mb-4">
                    <div class="bg-white shadow rounded -lg overflow-hidden header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            {{ __('How to Use Icons') }}
                        </h5>
                    </div>
                    <div class="bg-white shadow rounded -lg overflow-hidden body">
                        <p>{{ __('To use any icon in your Blade templates, simply copy the HTML code and paste it into your view:') }}</p>
                        <div class="bg-gray-100 p-3 rounded">
                            <code>&lt;i class="fas fa-user"&gt;&lt;/i&gt; {{ __('User Icon') }}</code>
                        </div>
                        <p class="mt-3 mb-0">
                            <strong>{{ __('Icon Sizing:') }}</strong>
                            <code>fa-xs</code>, <code>fa-sm</code>, <code>fa-lg</code>, <code>fa-xl</code>, 
                            <code>fa-2x</code>, <code>fa-3x</code>, <code>fa-4x</code>, <code>fa-5x</code>
                        </p>
                    </div>
                </div>

                <!-- Icon Grid -->
                <div class="flex flex-wrap" id="iconGrid">
                    
                    @php
                    $icons = [
                        // UI Icons
                        ['icon' => 'fas fa-home', 'name' => 'Home', 'category' => 'ui'],
                        ['icon' => 'fas fa-search', 'name' => 'Search', 'category' => 'ui'],
                        ['icon' => 'fas fa-bell', 'name' => 'Notifications', 'category' => 'ui'],
                        ['icon' => 'fas fa-cog', 'name' => 'Settings', 'category' => 'ui'],
                        ['icon' => 'fas fa-bars', 'name' => 'Menu', 'category' => 'ui'],
                        ['icon' => 'fas fa-times', 'name' => 'Close', 'category' => 'ui'],
                        ['icon' => 'fas fa-plus', 'name' => 'Add', 'category' => 'ui'],
                        ['icon' => 'fas fa-minus', 'name' => 'Remove', 'category' => 'ui'],
                        ['icon' => 'fas fa-edit', 'name' => 'Edit', 'category' => 'ui'],
                        ['icon' => 'fas fa-trash', 'name' => 'Delete', 'category' => 'ui'],
                        ['icon' => 'fas fa-save', 'name' => 'Save', 'category' => 'ui'],
                        ['icon' => 'fas fa-download', 'name' => 'Download', 'category' => 'ui'],
                        ['icon' => 'fas fa-upload', 'name' => 'Upload', 'category' => 'ui'],
                        ['icon' => 'fas fa-eye', 'name' => 'View', 'category' => 'ui'],
                        ['icon' => 'fas fa-eye-slash', 'name' => 'Hide', 'category' => 'ui'],
                        ['icon' => 'fas fa-lock', 'name' => 'Lock', 'category' => 'ui'],
                        ['icon' => 'fas fa-unlock', 'name' => 'Unlock', 'category' => 'ui'],
                        
                        // Business Icons
                        ['icon' => 'fas fa-briefcase', 'name' => 'Job/Business', 'category' => 'business'],
                        ['icon' => 'fas fa-building', 'name' => 'Company', 'category' => 'business'],
                        ['icon' => 'fas fa-user', 'name' => 'User', 'category' => 'business'],
                        ['icon' => 'fas fa-users', 'name' => 'Users/Team', 'category' => 'business'],
                        ['icon' => 'fas fa-user-tie', 'name' => 'Professional', 'category' => 'business'],
                        ['icon' => 'fas fa-user-graduate', 'name' => 'Graduate', 'category' => 'business'],
                        ['icon' => 'fas fa-handshake', 'name' => 'Partnership', 'category' => 'business'],
                        ['icon' => 'fas fa-chart-line', 'name' => 'Growth', 'category' => 'business'],
                        ['icon' => 'fas fa-dollar-sign', 'name' => 'Salary', 'category' => 'business'],
                        ['icon' => 'fas fa-calendar', 'name' => 'Schedule', 'category' => 'business'],
                        ['icon' => 'fas fa-clock', 'name' => 'Time', 'category' => 'business'],
                        ['icon' => 'fas fa-star', 'name' => 'Rating', 'category' => 'business'],
                        ['icon' => 'fas fa-award', 'name' => 'Achievement', 'category' => 'business'],
                        ['icon' => 'fas fa-certificate', 'name' => 'Certificate', 'category' => 'business'],
                        
                        // Social Media Icons
                        ['icon' => 'fab fa-facebook', 'name' => 'Facebook', 'category' => 'social'],
                        ['icon' => 'fab fa-twitter', 'name' => 'Twitter', 'category' => 'social'],
                        ['icon' => 'fab fa-linkedin', 'name' => 'LinkedIn', 'category' => 'social'],
                        ['icon' => 'fab fa-instagram', 'name' => 'Instagram', 'category' => 'social'],
                        ['icon' => 'fab fa-youtube', 'name' => 'YouTube', 'category' => 'social'],
                        ['icon' => 'fab fa-github', 'name' => 'GitHub', 'category' => 'social'],
                        
                        // File Icons
                        ['icon' => 'fas fa-file', 'name' => 'File', 'category' => 'file'],
                        ['icon' => 'fas fa-file-pdf', 'name' => 'PDF', 'category' => 'file'],
                        ['icon' => 'fas fa-file-word', 'name' => 'Word Document', 'category' => 'file'],
                        ['icon' => 'fas fa-file-excel', 'name' => 'Excel', 'category' => 'file'],
                        ['icon' => 'fas fa-file-image', 'name' => 'Image', 'category' => 'file'],
                        ['icon' => 'fas fa-folder', 'name' => 'Folder', 'category' => 'file'],
                        
                        // Communication Icons
                        ['icon' => 'fas fa-envelope', 'name' => 'Email', 'category' => 'ui'],
                        ['icon' => 'fas fa-phone', 'name' => 'Phone', 'category' => 'ui'],
                        ['icon' => 'fas fa-comment', 'name' => 'Comment', 'category' => 'ui'],
                        ['icon' => 'fas fa-comments', 'name' => 'Comments', 'category' => 'ui'],
                        ['icon' => 'fas fa-paper-plane', 'name' => 'Send', 'category' => 'ui']
                    ];
                    @endphp

                    @foreach($icons as $iconData)
                    <div class="md:w-4/12 flex-1 lg-3 mb-3 icon-item" data-category="{{ $iconData['category'] }}" data-name="{{ strtolower($iconData['name']) }}">
                        <div class="bg-white rounded-lg shadow-md border border-gray-300 border border border-gray-300 -gray-300 -gray-200 h-full icon- bg-white shadow rounded -lg overflow-hidden">
                            <div class="bg-white shadow rounded -lg overflow-hidden body text-center">
                                <div class="icon-preview">
                                    <i class="{{ $iconData['icon'] }}"></i>
                                </div>
                                <h6 class="bg-white shadow rounded -lg overflow-hidden title">{{ $iconData['name'] }}</h6>
                                <div class="icon-code" onclick="copyToClipboard('{{ $iconData['icon'] }}')">
                                    <i class="{{ $iconData['icon'] }}"></i>
                                </div>
                                <small class="text-gray-500 block mt-1">
                                    {{ __('Click to copy') }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- No Results Message -->
                <div id="noResults" class="text-center py-5" style="display: none;">
                    <i class="fas fa-search fa-3x text-gray-500 mb-3"></i>
                    <h4 class="text-gray-500">{{ __('No icons found') }}</h4>
                    <p class="text-gray-500">{{ __('Try adjusting your search terms or category filter') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast for Copy Confirmation -->
    <div class="toast- max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fixed bottom-0 end-0 p-3">
        <div id="copyToast" class="toast" role="alert">
            <div class="toast-header">
                <i class="fas fa-copy text-green-600 me-2"></i>
                <strong class="me-auto">{{ __('Copied!') }}</strong>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                {{ __('Icon code copied to clipboard') }}
            </div>
        </div>
    </div>
</body>
</html> 
@push('scripts')
    @vite('resources/js/components/icon-documentation.js')
@endpush
