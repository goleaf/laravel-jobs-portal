@extends('layouts.admin')

@section('title', 'Create Term')

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('admin.terms.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                Terms
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                Create
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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Term</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Add a new term to organize and categorize content.
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.terms.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.terms.store') }}" method="POST" class="px-6 py-6 space-y-6">
            @csrf

            <!-- Taxonomy Selection -->
            <div>
                <label for="taxonomy_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Taxonomy <span class="text-red-500">*</span>
                </label>
                <select 
                    id="taxonomy_id" 
                    name="taxonomy_id" 
                    required
                    onchange="updateParentOptions()"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('taxonomy_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                >
                    <option value="">Select a taxonomy</option>
                    @foreach($taxonomies as $taxonomy)
                        <option value="{{ $taxonomy->id }}" {{ old('taxonomy_id', request('taxonomy')) == $taxonomy->id ? 'selected' : '' }}>
                            {{ $taxonomy->name }} ({{ ucfirst($taxonomy->type) }})
                        </option>
                    @endforeach
                </select>
                @error('taxonomy_id')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

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
                        value="{{ old('name') }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="Enter term name"
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
                        value="{{ old('slug') }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('slug') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="term-slug"
                    >
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        URL-friendly version of the name. Leave blank to auto-generate.
                    </p>
                </div>
            </div>

            <!-- Parent Term and Status -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Parent Term -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Parent Term
                    </label>
                    <select 
                        id="parent_id" 
                        name="parent_id"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('parent_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                    >
                        <option value="">No parent (root term)</option>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Select a parent term to create a hierarchical structure.
                    </p>
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
                                {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="is_active" 
                                value="0" 
                                {{ old('is_active') == '0' ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactive</span>
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
                    placeholder="Enter term description (optional)"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sort Order
                    </label>
                    <input 
                        type="number" 
                        id="sort_order" 
                        name="sort_order" 
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('sort_order') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="0"
                    >
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Lower numbers appear first. Default is 0.
                    </p>
                </div>
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
                    @if(old('metadata_keys'))
                        @foreach(old('metadata_keys') as $index => $key)
                        <div class="metadata-field flex items-center space-x-3">
                            <input 
                                type="text" 
                                name="metadata_keys[]" 
                                value="{{ $key }}"
                                placeholder="Field name"
                                class="w-1/3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            >
                            <input 
                                type="text" 
                                name="metadata_values[]" 
                                value="{{ old('metadata_values')[$index] ?? '' }}"
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
                        @endforeach
                    @endif
                </div>
                
                @error('metadata_keys')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                @error('metadata_values')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Add custom metadata fields for this term.
                </p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.terms.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
                        Create Term
                    </button>
                </div>
            </div>
        </form>
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

// Taxonomy data for parent term population
const taxonomyTerms = @json($taxonomyTerms ?? []);

// Update parent options when taxonomy changes
function updateParentOptions() {
    const taxonomyId = document.getElementById('taxonomy_id').value;
    const parentSelect = document.getElementById('parent_id');
    
    // Clear existing options except the first one
    parentSelect.innerHTML = '<option value="">No parent (root term)</option>';
    
    if (taxonomyId && taxonomyTerms[taxonomyId]) {
        taxonomyTerms[taxonomyId].forEach(term => {
            const option = document.createElement('option');
            option.value = term.id;
            option.textContent = term.name;
            parentSelect.appendChild(option);
        });
    }
}

// Initialize parent options if taxonomy is pre-selected
document.addEventListener('DOMContentLoaded', function() {
    updateParentOptions();
});

// Metadata field management
function addMetadataField() {
    const container = document.getElementById('metadata-fields');
    
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
</script>
@endpush
@endsection 