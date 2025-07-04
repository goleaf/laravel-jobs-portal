@extends('layouts.admin')

@section('title', __('terms.create'))

@section('breadcrumbs')
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <a href="{{ route('admin.terms.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                {{ __('terms.back_to_list') }}
            </a>
        </div>
    </li>
    <li>
        <div class="flex items-center">
            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">
                {{ __('terms.create') }}
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
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('terms.create') }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('terms.create_description') }}
                    </p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.terms.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('terms.buttons.cancel') }}
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
                    {{ __('terms.fields.taxonomy') }} <span class="text-red-500">*</span>
                </label>
                <select 
                    id="taxonomy_id" 
                    name="taxonomy_id" 
                    required
                    onchange="updateParentOptions()"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('taxonomy_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                >
                    <option value="">{{ __('terms.select_options.select_taxonomy') }}</option>
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
                        {{ __('terms.fields.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="{{ __('terms.placeholders.name') }}"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('terms.fields.slug') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug" 
                        value="{{ old('slug') }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('slug') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="{{ __('terms.placeholders.slug') }}"
                    >
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('terms.help_texts.slug') }}
                    </p>
                </div>
            </div>

            <!-- Parent Term and Status -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Parent Term -->
                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('terms.fields.parent_term') }}
                    </label>
                    <select 
                        id="parent_id" 
                        name="parent_id"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('parent_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                    >
                        <option value="">{{ __('terms.select_options.no_parent') }}</option>
                        <!-- Options will be populated by JavaScript -->
                    </select>
                    @error('parent_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('terms.help_texts.parent_term') }}
                    </p>
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('terms.fields.status') }}
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
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('common.active') }}</span>
                        </label>
                        <label class="flex items-center">
                            <input 
                                type="radio" 
                                name="is_active" 
                                value="0" 
                                {{ old('is_active') == '0' ? 'checked' : '' }}
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600"
                            >
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('common.inactive') }}</span>
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
                    {{ __('terms.fields.description') }}
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('description') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                    placeholder="{{ __('terms.placeholders.description') }}"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('terms.fields.sort_order') }}
                    </label>
                    <input 
                        type="number" 
                        id="sort_order" 
                        name="sort_order" 
                        value="{{ old('sort_order', 0) }}"
                        min="0"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white @error('sort_order') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        placeholder="{{ __('terms.placeholders.sort_order') }}"
                    >
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('terms.help_texts.sort_order') }}
                    </p>
                </div>
            </div>

            <!-- Metadata Fields -->
            <div id="metadata-section">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('terms.fields.metadata') }}
                    </label>
                    <button 
                        type="button" 
                        onclick="addMetadataField()" 
                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        {{ __('terms.buttons.add_metadata_field') }}
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
                                placeholder="{{ __('terms.placeholders.metadata_key') }}"
                                class="w-1/3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            >
                            <input 
                                type="text" 
                                name="metadata_values[]" 
                                value="{{ old('metadata_values')[$index] ?? '' }}"
                                placeholder="{{ __('terms.placeholders.metadata_value') }}"
                                class="w-2/3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                            >
                            <button 
                                type="button" 
                                onclick="removeMetadataField(this)" 
                                class="text-red-500 hover:text-red-700"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                <button 
                    type="submit" 
                    class="w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    {{ __('terms.buttons.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function addMetadataField() {
    const container = document.getElementById('metadata-fields');
    const newField = document.createElement('div');
    newField.className = 'metadata-field flex items-center space-x-3';
    newField.innerHTML = `
        <input 
            type="text" 
            name="metadata_keys[]" 
            placeholder="{{ __('terms.placeholders.metadata_key') }}"
            class="w-1/3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
        >
        <input 
            type="text" 
            name="metadata_values[]" 
            placeholder="{{ __('terms.placeholders.metadata_value') }}"
            class="w-2/3 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
        >
        <button 
            type="button" 
            onclick="removeMetadataField(this)" 
            class="text-red-500 hover:text-red-700"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(newField);
}

function removeMetadataField(button) {
    button.closest('.metadata-field').remove();
}

function updateParentOptions() {
    const taxonomyId = document.getElementById('taxonomy_id').value;
    const parentSelect = document.getElementById('parent_id');
    
    // Clear existing options
    parentSelect.innerHTML = `<option value="">{{ __('terms.select_options.no_parent') }}</option>`;
    
    // Fetch parent terms for the selected taxonomy
    if (taxonomyId) {
        fetch(`/admin/terms/parents/${taxonomyId}`)
        .then(response => response.json())
        .then(data => {
            data.forEach(term => {
                const option = document.createElement('option');
                option.value = term.id;
                option.textContent = term.name;
                parentSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error fetching parent terms:', error);
        });
    }
}
</script>
@endpush
@endsection 