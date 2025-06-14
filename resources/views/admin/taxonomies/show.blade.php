@extends('layouts.admin')

@section('title', __('taxonomy.show_title', ['name' => $taxonomy->name]))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('admin.taxonomies.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                {{ __('taxonomy.title') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ $taxonomy->name }}
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $taxonomy->name }}</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('taxonomy.type') }}: {{ ucfirst(str_replace('_', ' ', $taxonomy->type)) }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $taxonomy->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                        {{ $taxonomy->is_active ? __('taxonomy.active') : __('taxonomy.inactive') }}
                    </span>
                    
                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.taxonomies.edit', $taxonomy) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ __('taxonomy.edit') }}
                        </a>
                        
                        <button onclick="toggleStatus({{ $taxonomy->id }})" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white {{ $taxonomy->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $taxonomy->is_active ? __('taxonomy.deactivate') : __('taxonomy.activate') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Taxonomy Details -->
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('taxonomy.fields.slug') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $taxonomy->slug }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('taxonomy.fields.type') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $taxonomy->type)) }}</dd>
                </div>
                
                @if($taxonomy->description)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('taxonomy.fields.description') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $taxonomy->description }}</dd>
                </div>
                @endif
                
                @if($taxonomy->metadata && count($taxonomy->metadata) > 0)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('taxonomy.fields.metadata') }}</dt>
                    <dd class="mt-1">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-md p-3">
                            <pre class="text-xs text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ json_encode($taxonomy->metadata, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </dd>
                </div>
                @endif
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('taxonomy.created_at') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $taxonomy->created_at->format('M j, Y g:i A') }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('taxonomy.updated_at') }}</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $taxonomy->updated_at->format('M j, Y g:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Terms -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('taxonomy.total_terms') }}</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $taxonomy->terms_count ?? $taxonomy->terms()->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Terms -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('taxonomy.active_terms') }}</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $taxonomy->terms()->where('is_active', true)->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Count -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-purple-100 dark:bg-purple-900 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('taxonomy.usage_count') }}</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $taxonomy->usage_count ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hierarchical Terms -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-yellow-100 dark:bg-yellow-900 rounded-md flex items-center justify-center">
                            <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ __('taxonomy.hierarchical_terms') }}</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $taxonomy->terms()->whereNotNull('parent_id')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms List -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                    {{ __('taxonomy.terms') }}
                </h3>
                <a href="{{ route('admin.terms.create', ['taxonomy' => $taxonomy->id]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    {{ __('taxonomy.add_term') }}
                </a>
            </div>
        </div>
        
        @if($taxonomy->terms->count() > 0)
        <div class="overflow-hidden">
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($taxonomy->terms()->whereNull('parent_id')->with('children')->get() as $term)
                    @include('admin.taxonomies.partials.term-item', ['term' => $term, 'level' => 0])
                @endforeach
            </ul>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('taxonomy.no_terms') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('taxonomy.no_terms_description') }}</p>
            <div class="mt-6">
                <a href="{{ route('admin.terms.create', ['taxonomy' => $taxonomy->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    {{ __('taxonomy.add_first_term') }}
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
async function toggleStatus(taxonomyId) {
    try {
        const response = await fetch(`/admin/taxonomies/${taxonomyId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (data.success) {
            showToast('success', data.message);
            // Reload page to update UI
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast('error', data.message || 'An error occurred');
        }
    } catch (error) {
        showToast('error', 'An error occurred while updating the status');
    }
}
</script>
@endpush
@endsection 