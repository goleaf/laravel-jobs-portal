@extends('layouts.app')

@section('title', 'Edit Taxonomy')

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('admin.taxonomies.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                Taxonomies
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('admin.taxonomies.show', $taxonomy) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                {{ $taxonomy->name }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                Edit
            </span>
        </div>
    </li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('admin.taxonomies.edit_title') }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Update the taxonomy information and settings.
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.taxonomies.show', $taxonomy) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.taxonomies.update', $taxonomy) }}" method="POST" class="px-6 py-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $taxonomy->name) }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="{{ __('admin.enter_taxonomy_name') }}"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Slug <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug" 
                        value="{{ old('slug', $taxonomy->slug) }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('slug') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="taxonomy-slug"
                    >
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        URL-friendly version of the name. Leave blank to auto-generate.
                    </p>
                </div>
            </div>

            <!-- Type and Status -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="type" 
                        name="type" 
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('type') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                    >
                        <option value="">{{ __('admin.taxonomies.select_type') }}</option>
                        <option value="category" {{ old('type', $taxonomy->type) === 'category' ? 'selected' : '' }}>
                            {{ __('admin.taxonomies.types.category') }}
                        </option>
                        <option value="tag" {{ old('type', $taxonomy->type) === 'tag' ? 'selected' : '' }}>
                            {{ __('admin.taxonomies.types.tag') }}
                        </option>
                        <option value="skill" {{ old('type', $taxonomy->type) === 'skill' ? 'selected' : '' }}>
                            {{ __('admin.taxonomies.types.skill') }}
                        </option>
                        <option value="location" {{ old('type', $taxonomy->type) === 'location' ? 'selected' : '' }}>
                            {{ __('admin.taxonomies.types.location') }}
                        </option>
                        <option value="industry" {{ old('type', $taxonomy->type) === 'industry' ? 'selected' : '' }}>
                            {{ __('admin.taxonomies.types.industry') }}
                        </option>
                        <option value="custom" {{ old('type', $taxonomy->type) === 'custom' ? 'selected' : '' }}>
                            {{ __('admin.taxonomies.types.custom') }}
                        </option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="is_active" 
                                value="1" 
                                {{ old('is_active', $taxonomy->is_active) == '1' ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ __('admin.taxonomies.status_active') }}
                            </span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="is_active" 
                                value="0" 
                                {{ old('is_active', $taxonomy->is_active) == '0' ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ __('admin.taxonomies.status_inactive') }}
                            </span>
                        </label>
                    </div>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                    placeholder="Enter taxonomy description (optional)"
                >{{ old('description', $taxonomy->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Metadata Fields -->
            <div id="metadata-section">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Metadata Fields
                    </label>
                    <button 
                        type="button" 
                        onclick="addMetadataField()" 
                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Field
                    </button>
                </div>
                
                <div id="metadata-fields" class="space-y-3">
                    @if(old('metadata') || ($taxonomy->metadata && count($taxonomy->metadata) > 0))
                        @php
                            $metadata = old('metadata', $taxonomy->metadata ?? []);
                        @endphp
                        @foreach($metadata as $key => $value)
                        <div class="metadata-field flex items-center space-x-3">
                            <input 
                                type="text" 
                                name="metadata[{{ $key }}]" 
                                value="{{ $value }}"
                                placeholder="Field value"
                                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            >
                            <input 
                                type="hidden" 
                                name="metadata_keys[]" 
                                value="{{ $key }}"
                            >
                            <span class="text-sm text-gray-500 dark:text-gray-400 min-w-0 flex-shrink-0">{{ $key }}</span>
                            <button 
                                type="button" 
                                onclick="removeMetadataField(this)" 
                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    @endif
                </div>
                
                @error('metadata')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Add custom metadata fields for this taxonomy type.
                </p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.taxonomies.show', $taxonomy) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Taxonomy
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-8 bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-red-200 dark:border-red-700">
        <div class="px-6 py-4 border-b border-red-200 dark:border-red-700">
            <h3 class="text-lg font-medium text-red-900 dark:text-red-200">
                {{ __('admin.taxonomies.danger_zone') }}
            </h3>
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                These actions cannot be undone. Please be careful.
            </p>
        </div>
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('admin.taxonomies.delete_taxonomy') }}
                    </h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Permanently delete this taxonomy and all its terms. This action cannot be undone.
                    </p>
                </div>
                <button 
                    type="button" 
                    onclick="confirmDelete()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Delete Taxonomy
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    document.getElementById('slug').value = slug;
});

// Metadata field management
function addMetadataField() {
    const container = document.getElementById('metadata-fields');
    const fieldCount = container.children.length;
    
    const fieldHtml = `
        <div class="metadata-field flex items-center space-x-3">
            <input 
                type="text" 
                name="metadata_keys[]" 
                placeholder="Field name"
                class="w-1/3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            >
            <input 
                type="text" 
                name="metadata_values[]" 
                placeholder="Field value"
                class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
            >
            <button 
                type="button" 
                onclick="removeMetadataField(this)" 
                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', fieldHtml);
}

function removeMetadataField(button) {
    button.closest('.metadata-field').remove();
}

// Delete confirmation
function confirmDelete() {
    if (confirm('Are you sure you want to delete this taxonomy? This action cannot be undone and will also delete all associated terms.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.taxonomies.destroy", $taxonomy) }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection 