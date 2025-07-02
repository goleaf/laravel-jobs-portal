@extends('layouts.admin')

@section('title', __('taxonomy.create'))

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('taxonomy.create') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                {{ __('taxonomy.create_description') }}
            </p>
        </div>
        <a 
            href="{{ route('admin.taxonomies.index') }}" 
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors"
        >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('taxonomy.back_to_list') }}
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <form action="{{ route('admin.taxonomies.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('taxonomy.fields.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name') }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror"
                        placeholder="{{ __('taxonomy.name_placeholder') }}"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('taxonomy.fields.slug') }}
                    </label>
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug" 
                        value="{{ old('slug') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('slug') border-red-500 @enderror"
                        placeholder="{{ __('taxonomy.slug_placeholder') }}"
                    >
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('taxonomy.slug_help') }}
                    </p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('taxonomy.fields.type') }} <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="type" 
                        name="type" 
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('type') border-red-500 @enderror"
                    >
                        <option value="">{{ __('taxonomy.select_type') }}</option>
                        <option value="job_category" {{ old('type') === 'job_category' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.job_category') }}
                        </option>
                        <option value="job_type" {{ old('type') === 'job_type' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.job_type') }}
                        </option>
                        <option value="skill" {{ old('type') === 'skill' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.skill') }}
                        </option>
                        <option value="location" {{ old('type') === 'location' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.location') }}
                        </option>
                        <option value="industry" {{ old('type') === 'industry' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.industry') }}
                        </option>
                        <option value="experience_level" {{ old('type') === 'experience_level' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.experience_level') }}
                        </option>
                        <option value="education_level" {{ old('type') === 'education_level' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.education_level') }}
                        </option>
                        <option value="company_size" {{ old('type') === 'company_size' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.company_size') }}
                        </option>
                        <option value="benefits" {{ old('type') === 'benefits' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.benefits') }}
                        </option>
                        <option value="custom" {{ old('type') === 'custom' ? 'selected' : '' }}>
                            {{ __('taxonomy.types.custom') }}
                        </option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('taxonomy.fields.status') }}
                    </label>
                    <div class="flex items-center">
                        <input 
                            type="hidden" 
                            name="is_active" 
                            value="0"
                        >
                        <input 
                            type="checkbox" 
                            id="is_active" 
                            name="is_active" 
                            value="1"
                            {{ old('is_active', '1') ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ __('taxonomy.active_taxonomy') }}
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
                    {{ __('taxonomy.fields.description') }}
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror"
                    placeholder="{{ __('taxonomy.description_placeholder') }}"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Configuration -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ __('taxonomy.configuration') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Hierarchical -->
                    <div>
                        <div class="flex items-center">
                            <input 
                                type="hidden" 
                                name="is_hierarchical" 
                                value="0"
                            >
                            <input 
                                type="checkbox" 
                                id="is_hierarchical" 
                                name="is_hierarchical" 
                                value="1"
                                {{ old('is_hierarchical') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <label for="is_hierarchical" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('taxonomy.hierarchical') }}
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('taxonomy.hierarchical_help') }}
                        </p>
                        @error('is_hierarchical')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Public -->
                    <div>
                        <div class="flex items-center">
                            <input 
                                type="hidden" 
                                name="is_public" 
                                value="0"
                            >
                            <input 
                                type="checkbox" 
                                id="is_public" 
                                name="is_public" 
                                value="1"
                                {{ old('is_public', '1') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >
                            <label for="is_public" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('taxonomy.public') }}
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('taxonomy.public_help') }}
                        </p>
                        @error('is_public')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6" id="metadata-section">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ __('taxonomy.metadata') }}
                </h3>

                <div id="metadata-fields" class="space-y-4">
                    <!-- Dynamic metadata fields will be added here based on type -->
                </div>

                <button 
                    type="button" 
                    onclick="addMetadataField()"
                    class="mt-4 inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    {{ __('taxonomy.add_metadata') }}
                </button>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <a 
                    href="{{ route('admin.taxonomies.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    {{ __('taxonomy.cancel') }}
                </a>
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ __('taxonomy.create') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let metadataCounter = 0;

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    document.getElementById('slug').value = slug;
});

// Type-specific metadata fields
const typeMetadata = {
    job_category: [
        { key: 'icon', label: 'Icon Class', type: 'text', placeholder: 'fas fa-code' },
        { key: 'color', label: 'Color', type: 'color', placeholder: '#3B82F6' },
        { key: 'sort_order', label: 'Sort Order', type: 'number', placeholder: '0' }
    ],
    job_type: [
        { key: 'color', label: 'Color', type: 'color', placeholder: '#10B981' },
        { key: 'is_remote_allowed', label: 'Remote Work Allowed', type: 'checkbox' },
        { key: 'sort_order', label: 'Sort Order', type: 'number', placeholder: '0' }
    ],
    skill: [
        { key: 'category', label: 'Skill Category', type: 'select', options: ['technical', 'soft', 'language', 'certification'] },
        { key: 'proficiency_levels', label: 'Proficiency Levels', type: 'textarea', placeholder: 'Beginner, Intermediate, Advanced, Expert' },
        { key: 'sort_order', label: 'Sort Order', type: 'number', placeholder: '0' }
    ],
    location: [
        { key: 'country_code', label: 'Country Code', type: 'text', placeholder: 'US' },
        { key: 'timezone', label: 'Timezone', type: 'text', placeholder: 'America/New_York' },
        { key: 'coordinates', label: 'Coordinates', type: 'text', placeholder: '40.7128,-74.0060' }
    ]
};

document.getElementById('type').addEventListener('change', function() {
    const type = this.value;
    const metadataFields = document.getElementById('metadata-fields');
    
    // Clear existing fields
    metadataFields.innerHTML = '';
    metadataCounter = 0;
    
    // Add type-specific fields
    if (typeMetadata[type]) {
        typeMetadata[type].forEach(field => {
            addMetadataField(field.key, field.label, field.type, field.placeholder, field.options);
        });
    }
});

function addMetadataField(key = '', label = '', type = 'text', placeholder = '', options = []) {
    const metadataFields = document.getElementById('metadata-fields');
    const fieldId = `metadata_${metadataCounter}`;
    
    let fieldHtml = `
        <div class="grid grid-cols-12 gap-4 items-end" id="${fieldId}">
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Key</label>
                <input 
                    type="text" 
                    name="meta_keys[]" 
                    value="${key}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="metadata_key"
                >
            </div>
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Label</label>
                <input 
                    type="text" 
                    name="meta_labels[]" 
                    value="${label}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                            placeholder="{{ __('admin.display_label') }}"
                >
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                <select 
                    name="meta_types[]"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="text" ${type === 'text' ? 'selected' : ''}>Text</option>
                    <option value="textarea" ${type === 'textarea' ? 'selected' : ''}>Textarea</option>
                    <option value="number" ${type === 'number' ? 'selected' : ''}>Number</option>
                    <option value="select" ${type === 'select' ? 'selected' : ''}>Select</option>
                    <option value="checkbox" ${type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                    <option value="color" ${type === 'color' ? 'selected' : ''}>Color</option>
                </select>
            </div>
            <div class="col-span-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Value/Options</label>
                <input 
                    type="text" 
                    name="meta_values[]" 
                    value="${options.length ? options.join(',') : ''}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="${placeholder || 'Default value or comma-separated options'}"
                >
            </div>
            <div class="col-span-1">
                <button 
                    type="button" 
                    onclick="removeMetadataField('${fieldId}')"
                    class="w-full px-3 py-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    metadataFields.insertAdjacentHTML('beforeend', fieldHtml);
    metadataCounter++;
}

function removeMetadataField(fieldId) {
    document.getElementById(fieldId).remove();
}
</script>
@endpush
@endsection 