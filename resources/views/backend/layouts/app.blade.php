<x-shared.components.layout.base :title="$title ?? __('messages.admin_panel')" bodyClass="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 overflow-y-auto lg:static lg:inset-0">
            <div class="flex items-center justify-center h-16 bg-gray-900">
                <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}">
                <span class="ml-2 text-white text-lg font-semibold">{{ __('messages.admin_panel') }}</span>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs("admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        </svg>
                        {{ __('messages.dashboard') }}
                    </a>
                    
                    <!-- Users Management -->
                    <div class="space-y-1">
                        <button class="group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white"
                                x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.candidates.*') || request()->routeIs('admin.employers.*') ? 'true' : 'false' }} }"
                                @click="open = !open">
                            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            {{ __('messages.user_management') }}
                            <svg class="ml-auto h-5 w-5 transform transition-transform" :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open" class="space-y-1 ml-8">
                            <a href="{{ route('admin.candidates.index') }}" 
                               class="group flex items-center px-2 py-2 text-sm text-gray-300 hover:text-white {{ request()->routeIs("admin.candidates.*') ? 'text-white' : '' }}">
                                {{ __('messages.candidates') }}
                            </a>
                            <a href="{{ route('admin.') }}" 
                               class="group flex items-center px-2 py-2 text-sm text-gray-300 hover:text-white {{ request()->routeIs("admin.employers.*') ? 'text-white' : '' }}">
                                {{ __('messages.employers') }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Jobs Management -->
                    <a href="{{ route('admin.jobs.index') }}" 
                       class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs("admin.jobs.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                        <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z" />
                        </svg>
                        {{ __('messages.jobs') }}
                    </a>
                    
                    <!-- Settings -->
                    <div class="space-y-1">
                        <button class="group w-full flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white"
                                x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }"
                                @click="open = !open">
                            <svg class="mr-3 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ __('messages.settings') }}
                            <svg class="ml-auto h-5 w-5 transform transition-transform" :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open" class="space-y-1 ml-8">
                            <a href="{{ route('admin.') }}" 
                               class="group flex items-center px-2 py-2 text-sm text-gray-300 hover:text-white">
                                {{ __('messages.general_settings') }}
                            </a>
                            <a href="{{ route('admin.') }}" 
                               class="group flex items-center px-2 py-2 text-sm text-gray-300 hover:text-white">
                                {{ __('messages.email_settings') }}
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        
        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center">
                        <button class="text-gray-500 hover:text-gray-600 lg:hidden">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        
                        <!-- Breadcrumbs -->
                        @if(isset($breadcrumbs))
                            <x-shared.components.navigation.breadcrumbs :breadcrumbs="$breadcrumbs" />
                        @endif
                    </div>
                    
                    <!-- User menu -->
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM12 17.5a6.5 6.5 0 110-13 6.5 6.5 0 010 13z" />
                            </svg>
                        </button>
                        
                        <!-- Profile dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <img class="h-8 w-8 rounded-full" src="{{ auth()->user()->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ auth()->user()->name }}">
                            </button>
                            
                            <div x-show="open" @click.away="open = false" 
                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    <a href="{{ route('admin.') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('messages.profile') }}
                                    </a>
                                    <a href="{{ route('admin.') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('messages.settings') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ __('messages.logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-y-auto">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        @if(isset($header))
                            <div class="mb-6">
                                <h1 class="text-2xl font-semibold text-gray-900">{{ $header }}</h1>
                                @if(isset($description))
                                    <p class="mt-1 text-sm text-gray-600">{{ $description }}</p>
                                @endif
                            </div>
                        @endif
                        
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    @push('scripts')
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endpush
</x-shared.components.layout.base> 