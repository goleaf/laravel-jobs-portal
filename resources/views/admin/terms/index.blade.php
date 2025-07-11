@extends('layouts.app')

@section('title', 'Terms Management')

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                Terms
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.terms.management_title') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage taxonomy terms across all taxonomies.
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.terms.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Term
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('admin.terms.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Search
                        </label>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Search terms..."
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <!-- Taxonomy Filter -->
                    <div>
                        <label for="taxonomy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Taxonomy
                        </label>
                        <select 
                            id="taxonomy" 
                            name="taxonomy"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">{{ __('admin.terms.all_taxonomies') }}</option>
                            @foreach($taxonomies as $taxonomy)
                                <option value="{{ $taxonomy->id }}" {{ request('taxonomy') == $taxonomy->id ? 'selected' : '' }}>
                                    {{ $taxonomy->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Status
                        </label>
                        <select 
                            id="status" 
                            name="status"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">{{ __('admin.terms.all_statuses') }}</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin.terms.status_active') }}</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin.terms.status_inactive') }}</option>
                        </select>
                    </div>

                    <!-- Parent Filter -->
                    <div>
                        <label for="parent" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Parent
                        </label>
                        <select 
                            id="parent" 
                            name="parent"
                            class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">{{ __('admin.terms.all_terms') }}</option>
                            <option value="root" {{ request('parent') === 'root' ? 'selected' : '' }}>{{ __('admin.terms.root_terms_only') }}</option>
                            <option value="child" {{ request('parent') === 'child' ? 'selected' : '' }}>{{ __('admin.terms.child_terms_only') }}</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button 
                            type="submit" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Filter
                        </button>
                        <a 
                            href="{{ route('admin.terms.index') }}" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Clear
                        </a>
                    </div>
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $terms->total() }} terms found
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Terms Table -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        @if($terms->count() > 0)
        <!-- Bulk Actions -->
        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <input 
                        type="checkbox" 
                        id="select-all" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                        onchange="toggleAllCheckboxes(this)"
                    >
                    <label for="select-all" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Select All
                    </label>
                </div>
                
                <div class="flex items-center space-x-2">
                    <select 
                        id="bulk-action" 
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">{{ __('admin.terms.bulk_actions') }}</option>
                        <option value="activate">{{ __('admin.terms.activate_selected') }}</option>
                        <option value="deactivate">{{ __('admin.terms.deactivate_selected') }}</option>
                        <option value="delete">{{ __('admin.terms.delete_selected') }}</option>
                    </select>
                    <button 
                        type="button" 
                        onclick="executeBulkAction()" 
                        class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Apply
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <span class="sr-only">{{ __('admin.terms.select') }}</span>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Term
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Taxonomy
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Parent
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Usage
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Created
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">{{ __('admin.terms.actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($terms as $term)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input 
                                type="checkbox" 
                                name="selected_terms[]" 
                                value="{{ $term->id }}" 
                                class="term-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600 rounded"
                            >
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($term->parent_id)
                                    <span class="text-gray-400 mr-2">└─</span>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $term->name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $term->slug }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $term->taxonomy->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst($term->taxonomy->type) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($term->parent)
                                <div class="text-sm text-gray-900 dark:text-white">{{ $term->parent->name }}</div>
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('admin.terms.root_term') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $term->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $term->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $term->usage_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $term->created_at->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.terms.show', $term) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    View
                                </a>
                                <a href="{{ route('admin.terms.edit', $term) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Edit
                                </a>
                                <button 
                                    onclick="toggleTermStatus({{ $term->id }})" 
                                    class="text-{{ $term->is_active ? 'red' : 'green' }}-600 hover:text-{{ $term->is_active ? 'red' : 'green' }}-900 dark:text-{{ $term->is_active ? 'red' : 'green' }}-400"
                                >
                                    {{ $term->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $terms->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                {{ __('admin.terms.no_terms_found') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if(request()->hasAny(['search', 'taxonomy', 'status', 'parent']))
                    Try adjusting your search criteria or filters.
                @else
                    Get started by creating your first term.
                @endif
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.terms.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add First Term
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Checkbox management
function toggleAllCheckboxes(selectAllCheckbox) {
    const checkboxes = document.querySelectorAll('.term-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

// Bulk actions
async function executeBulkAction() {
    const action = document.getElementById('bulk-action').value;
    const selectedTerms = Array.from(document.querySelectorAll('.term-checkbox:checked')).map(cb => cb.value);
    
    if (!action) {
        showToast('error', 'Please select an action');
        return;
    }
    
    if (selectedTerms.length === 0) {
        showToast('error', 'Please select at least one term');
        return;
    }
    
    if (action === 'delete' && !confirm('Are you sure you want to delete the selected terms? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch('/admin/terms/bulk-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: action,
                terms: selectedTerms
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('success', data.message);
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showToast('error', data.message || 'An error occurred');
        }
    } catch (error) {
        showToast('error', 'An error occurred while processing the bulk action');
    }
}

// Toggle term status
async function toggleTermStatus(termId) {
    try {
        const response = await fetch(`/admin/terms/${termId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const data = await response.json();
        
        if (data.success) {
            showToast('success', data.message);
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