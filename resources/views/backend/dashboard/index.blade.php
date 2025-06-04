<x-backend.layouts.app title="{{ __('messages.dashboard') }}" header="{{ __('messages.dashboard') }}" :breadcrumbs="[
    ['title' => __('messages.dashboard'), 'url' => route('admin.dashboard')]
]">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">{{ __('messages.total_users') }}</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['users'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.users.index') }}" class="font-medium text-blue-700 hover:text-blue-900">
                        {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Jobs -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">{{ __('messages.total_jobs') }}</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['jobs'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.jobs.index') }}" class="font-medium text-blue-700 hover:text-blue-900">
                        {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Companies -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">{{ __('messages.total_companies') }}</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['companies'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.companies.index') }}" class="font-medium text-blue-700 hover:text-blue-900">
                        {{ __('messages.view_all') }}
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Monthly Revenue -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">{{ __('messages.monthly_revenue') }}</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($stats['revenue'] ?? 0, 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-5 py-3">
                <div class="text-sm">
                    <a href="{{ route('admin.revenue.index') }}" class="font-medium text-blue-700 hover:text-blue-900">
                        {{ __('messages.view_details') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('messages.recent_users') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($recentUsers) && $recentUsers->count() > 0)
                    <x-shared.components.table.index 
                        :headers="[
                            ['label' => __('messages.name')],
                            ['label' => __('messages.email')],
                            ['label' => __('messages.role')],
                            ['label' => __('messages.created_at')]
                        ]"
                        :rows="$recentUsers->map(function($user) {
                            return [
                                $user->name,
                                $user->email,
                                $user->role,
                                $user->created_at->format('M d, Y')
                            ];
                        })->toArray()"
                        :actions="[
                            [
                                'type' => 'link',
                                'label' => __('messages.view'),
                                'url' => fn($row, $index) => route('admin.users.show', $recentUsers[$index]->id),
                                'color' => 'blue'
                            ]
                        ]"
                        :striped="true"
                        :hoverable="true"
                    />
                @else
                    <p class="text-gray-500 text-center py-4">{{ __('messages.no_recent_users') }}</p>
                @endif
            </div>
        </div>
        
        <!-- Recent Jobs -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('messages.recent_jobs') }}</h3>
            </div>
            <div class="p-6">
                @if(isset($recentJobs) && $recentJobs->count() > 0)
                    <x-shared.components.table.index 
                        :headers="[
                            ['label' => __('messages.title')],
                            ['label' => __('messages.company')],
                            ['label' => __('messages.status')],
                            ['label' => __('messages.posted_date')]
                        ]"
                        :rows="$recentJobs->map(function($job) {
                            return [
                                $job->title,
                                $job->company->name ?? 'N/A',
                                '<span class=\'px-2 py-1 text-xs rounded-full bg-' . ($job->is_active ? 'green' : 'red') . '-100 text-' . ($job->is_active ? 'green' : 'red') . '-800\'>' . ($job->is_active ? __('messages.active') : __('messages.inactive')) . '</span>',
                                $job->created_at->format('M d, Y')
                            ];
                        })->toArray()"
                        :actions="[
                            [
                                'type' => 'link',
                                'label' => __('messages.view'),
                                'url' => fn($row, $index) => route('admin.jobs.show', $recentJobs[$index]->id),
                                'color' => 'blue'
                            ]
                        ]"
                        :striped="true"
                        :hoverable="true"
                    />
                @else
                    <p class="text-gray-500 text-center py-4">{{ __('messages.no_recent_jobs') }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="mt-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">{{ __('messages.quick_actions') }}</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-shared.components.forms.button 
                        href="{{ route('admin.users.create') }}" 
                        variant="primary" 
                        class="w-full">
                        {{ __('messages.add_user') }}
                    </x-shared.components.forms.button>
                    
                    <x-shared.components.forms.button 
                        href="{{ route('admin.jobs.create') }}" 
                        variant="success" 
                        class="w-full">
                        {{ __('messages.add_job') }}
                    </x-shared.components.forms.button>
                    
                    <x-shared.components.forms.button 
                        href="{{ route('admin.companies.create') }}" 
                        variant="info" 
                        class="w-full">
                        {{ __('messages.add_company') }}
                    </x-shared.components.forms.button>
                    
                    <x-shared.components.forms.button 
                        href="{{ route('admin.settings.index') }}" 
                        variant="secondary" 
                        class="w-full">
                        {{ __('messages.settings') }}
                    </x-shared.components.forms.button>
                </div>
            </div>
        </div>
    </div>
</x-backend.layouts.app> 