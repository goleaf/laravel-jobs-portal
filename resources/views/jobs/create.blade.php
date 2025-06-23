@extends('layouts.app')

@section('title', __('jobs.post_new_job'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('jobs.post_new_job') }}
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                {{ __('jobs.post_job_description') }}
            </p>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <nav aria-label="{{ __('jobs.progress') }}">
                <ol role="list" class="border border-gray-300 dark:border-gray-600 rounded-md divide-y divide-gray-300 dark:divide-gray-600 md:flex md:divide-y-0">
                    <li class="relative md:flex-1 md:flex">
                        <div class="group flex items-center w-full">
                            <span class="px-6 py-4 flex items-center text-sm font-medium">
                                <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-blue-600 rounded-full group-hover:bg-blue-800">
                                    <span class="text-white">1</span>
                                </span>
                                <span class="ml-4 text-sm font-medium text-gray-900 dark:text-white">{{ __('jobs.job_details') }}</span>
                            </span>
                        </div>
                    </li>
                    
                    <li class="relative md:flex-1 md:flex">
                        <div class="group flex items-center">
                            <span class="px-6 py-4 flex items-center text-sm font-medium">
                                <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center border-2 border-gray-300 dark:border-gray-600 rounded-full">
                                    <span class="text-gray-500 dark:text-gray-400">2</span>
                                </span>
                                <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.requirements') }}</span>
                            </span>
                        </div>
                    </li>
                    
                    <li class="relative md:flex-1 md:flex">
                        <div class="group flex items-center">
                            <span class="px-6 py-4 flex items-center text-sm font-medium">
                                <span class="flex-shrink-0 w-10 h-10 flex items-center justify-center border-2 border-gray-300 dark:border-gray-600 rounded-full">
                                    <span class="text-gray-500 dark:text-gray-400">3</span>
                                </span>
                                <span class="ml-4 text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('jobs.review_publish') }}</span>
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <form action="{{ route('jobs.store') }}" method="POST" enctype="multipart/form-data" id="job-form">
            @csrf
            
            <!-- Step 1: Job Details -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8" id="step-1">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.job_details') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('jobs.basic_information_about_job') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Job Title -->
                        <div class="sm:col-span-2">
                            <x-ui.input
                                name="title"
                                id="title"
                                :label="__('jobs.job_title')"
                                :placeholder="__('jobs.job_title_placeholder')"
                                :value="old('title')"
                                required
                                :error="$errors->first('title')"
                                :hint="__('jobs.job_title_hint')"
                            />
                        </div>

                        <!-- Company (if admin) -->
                        @if(auth()->user()->hasRole('admin'))
                            <div class="sm:col-span-2">
                                <x-ui.select
                                    name="company_id"
                                    id="company_id"
                                    :label="__('jobs.company')"
                                    :options="$companies ?? []"
                                    :selected="old('company_id')"
                                    required
                                    :error="$errors->first('company_id')"
                                />
                            </div>
                        @endif

                        <!-- Job Category -->
                        <div>
                            <x-ui.select
                                name="job_category_id"
                                id="job_category_id"
                                :label="__('jobs.category')"
                                :options="$jobCategories ?? []"
                                :selected="old('job_category_id')"
                                required
                                :error="$errors->first('job_category_id')"
                            />
                        </div>

                        <!-- Job Type -->
                        <div>
                            <x-ui.select
                                name="job_type_id"
                                id="job_type_id"
                                :label="__('jobs.job_type')"
                                :options="$jobTypes ?? []"
                                :selected="old('job_type_id')"
                                required
                                :error="$errors->first('job_type_id')"
                            />
                        </div>

                        <!-- Location -->
                        <div>
                            <x-ui.input
                                name="location"
                                id="location"
                                :label="__('jobs.location')"
                                :placeholder="__('jobs.location_placeholder')"
                                :value="old('location')"
                                required
                                :error="$errors->first('location')"
                                icon="map-pin"
                            />
                        </div>

                        <!-- Remote Work -->
                        <div>
                            <x-ui.select
                                name="remote_option"
                                id="remote_option"
                                :label="__('jobs.remote_option')"
                                :options="[
                                    'no' => __('jobs.office_only'),
                                    'partial' => __('jobs.hybrid'),
                                    'yes' => __('jobs.fully_remote')
                                ]"
                                :selected="old('remote_option', 'no')"
                                :error="$errors->first('remote_option')"
                            />
                        </div>

                        <!-- Salary Range -->
                        <div>
                            <x-ui.input
                                name="salary_min"
                                id="salary_min"
                                type="number"
                                :label="__('jobs.salary_min')"
                                :placeholder="__('jobs.salary_min_placeholder')"
                                :value="old('salary_min')"
                                :error="$errors->first('salary_min')"
                                icon="currency-dollar"
                                step="1000"
                                min="0"
                            />
                        </div>

                        <div>
                            <x-ui.input
                                name="salary_max"
                                id="salary_max"
                                type="number"
                                :label="__('jobs.salary_max')"
                                :placeholder="__('jobs.salary_max_placeholder')"
                                :value="old('salary_max')"
                                :error="$errors->first('salary_max')"
                                icon="currency-dollar"
                                step="1000"
                                min="0"
                            />
                        </div>

                        <!-- Experience Level -->
                        <div>
                            <x-ui.select
                                name="career_level_id"
                                id="career_level_id"
                                :label="__('jobs.experience_level')"
                                :options="$careerLevels ?? []"
                                :selected="old('career_level_id')"
                                :error="$errors->first('career_level_id')"
                            />
                        </div>

                        <!-- Education Level -->
                        <div>
                            <x-ui.select
                                name="degree_level_id"
                                id="degree_level_id"
                                :label="__('jobs.education_required')"
                                :options="$degreeLevels ?? []"
                                :selected="old('degree_level_id')"
                                :error="$errors->first('degree_level_id')"
                            />
                        </div>

                        <!-- Job Shift -->
                        <div>
                            <x-ui.select
                                name="job_shift_id"
                                id="job_shift_id"
                                :label="__('jobs.job_shift')"
                                :options="$jobShifts ?? []"
                                :selected="old('job_shift_id')"
                                :error="$errors->first('job_shift_id')"
                            />
                        </div>

                        <!-- Application Deadline -->
                        <div>
                            <x-ui.input
                                name="deadline"
                                id="deadline"
                                type="date"
                                :label="__('jobs.application_deadline')"
                                :value="old('deadline')"
                                :error="$errors->first('deadline')"
                                icon="calendar"
                                :min="date('Y-m-d')"
                            />
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div>
                        <x-ui.textarea
                            name="description"
                            id="description"
                            :label="__('jobs.job_description')"
                            :placeholder="__('jobs.job_description_placeholder')"
                            :value="old('description')"
                            required
                            :error="$errors->first('description')"
                            rows="8"
                            maxlength="5000"
                            showCounter="true"
                            :hint="__('jobs.job_description_hint')"
                        />
                    </div>
                </div>
            </div>

            <!-- Step 2: Requirements & Skills -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8" id="step-2">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.requirements_and_skills') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('jobs.specify_requirements_and_skills') }}
                    </p>
                </div>
                
                <div class="px-6 py-6 space-y-6">
                    <!-- Requirements -->
                    <div>
                        <x-ui.textarea
                            name="requirements"
                            id="requirements"
                            :label="__('jobs.requirements')"
                            :placeholder="__('jobs.requirements_placeholder')"
                            :value="old('requirements')"
                            :error="$errors->first('requirements')"
                            rows="6"
                            maxlength="3000"
                            showCounter="true"
                            :hint="__('jobs.requirements_hint')"
                        />
                    </div>

                    <!-- Benefits -->
                    <div>
                        <x-ui.textarea
                            name="benefits"
                            id="benefits"
                            :label="__('jobs.benefits')"
                            :placeholder="__('jobs.benefits_placeholder')"
                            :value="old('benefits')"
                            :error="$errors->first('benefits')"
                            rows="6"
                            maxlength="3000"
                            showCounter="true"
                            :hint="__('jobs.benefits_hint')"
                        />
                    </div>

                    <!-- Required Skills -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('jobs.required_skills') }}
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <x-ui.select
                                name="skills[]"
                                id="skills"
                                :options="$skills ?? []"
                                multiple="true"
                                searchable="true"
                                :selected="old('skills', [])"
                                :error="$errors->first('skills')"
                                :hint="__('jobs.select_relevant_skills')"
                            />
                        </div>
                    </div>

                    <!-- Years of Experience -->
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <x-ui.input
                                name="experience_min"
                                id="experience_min"
                                type="number"
                                :label="__('jobs.min_experience_years')"
                                :placeholder="__('jobs.min_experience_placeholder')"
                                :value="old('experience_min', 0)"
                                :error="$errors->first('experience_min')"
                                min="0"
                                max="50"
                            />
                        </div>

                        <div>
                            <x-ui.input
                                name="experience_max"
                                id="experience_max"
                                type="number"
                                :label="__('jobs.max_experience_years')"
                                :placeholder="__('jobs.max_experience_placeholder')"
                                :value="old('experience_max')"
                                :error="$errors->first('experience_max')"
                                min="0"
                                max="50"
                            />
                        </div>
                    </div>

                    <!-- Job Features -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">
                            {{ __('jobs.job_features') }}
                        </label>
                        
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="flex items-center">
                                <input 
                                    id="is_featured" 
                                    name="is_featured" 
                                    type="checkbox" 
                                    value="1"
                                    {{ old('is_featured') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="is_featured" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('jobs.featured_job') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="is_urgent" 
                                    name="is_urgent" 
                                    type="checkbox" 
                                    value="1"
                                    {{ old('is_urgent') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="is_urgent" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('jobs.urgent_hiring') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="hide_salary" 
                                    name="hide_salary" 
                                    type="checkbox" 
                                    value="1"
                                    {{ old('hide_salary') ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="hide_salary" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('jobs.hide_salary') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Review & Publish -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8" id="step-3">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ __('jobs.review_and_publish') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('jobs.review_job_details_before_publishing') }}
                    </p>
                </div>
                
                <div class="px-6 py-6">
                    <!-- Job Preview -->
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 bg-gray-50 dark:bg-gray-700">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            {{ __('jobs.job_preview') }}
                        </h4>
                        
                        <div id="job-preview" class="space-y-4">
                            <!-- Preview content will be populated via JavaScript -->
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="mt-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input 
                                    id="agree_terms" 
                                    name="agree_terms" 
                                    type="checkbox" 
                                    required
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                >
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="agree_terms" class="text-gray-700 dark:text-gray-300">
                                    {{ __('jobs.i_agree_to') }}
                                    <a href="{{ route('terms') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" target="_blank">
                                        {{ __('jobs.terms_of_service') }}
                                    </a>
                                    {{ __('jobs.and') }}
                                    <a href="{{ route('posting-guidelines') }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300" target="_blank">
                                        {{ __('jobs.job_posting_guidelines') }}
                                    </a>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between">
                <x-ui.button 
                    href="{{ route('employer.dashboard') }}" 
                    variant="secondary"
                >
                    {{ __('jobs.cancel') }}
                </x-ui.button>

                <div class="flex space-x-3">
                    <x-ui.button 
                        type="button" 
                        variant="secondary"
                        id="save-draft"
                    >
                        {{ __('jobs.save_as_draft') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                        id="publish-job"
                    >
                        {{ __('jobs.publish_job') }}
                    </x-ui.button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('job-form');
    const steps = ['step-1', 'step-2', 'step-3'];
    let currentStep = 0;

    // Form validation and preview
    function updateJobPreview() {
        const previewContainer = document.getElementById('job-preview');
        const formData = new FormData(form);
        
        let previewHTML = `
            <div class="space-y-3">
                <div>
                    <h5 class="font-medium text-gray-900 dark:text-white">${formData.get('title') || '{{ __("jobs.job_title") }}'}</h5>
                    <p class="text-sm text-gray-600 dark:text-gray-400">${formData.get('location') || '{{ __("jobs.location") }}'}</p>
                </div>
                
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <h6 class="font-medium">{{ __('jobs.description') }}</h6>
                    <p>${(formData.get('description') || '{{ __("jobs.no_description") }}').substring(0, 200)}...</p>
                </div>
                
                <div class="flex flex-wrap gap-2">
                    ${formData.get('salary_min') ? `
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            $${formData.get('salary_min')}${formData.get('salary_max') ? ' - $' + formData.get('salary_max') : '+'}
                        </span>
                    ` : ''}
                    
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        ${formData.get('remote_option') === 'yes' ? '{{ __("jobs.remote") }}' : (formData.get('remote_option') === 'partial' ? '{{ __("jobs.hybrid") }}' : '{{ __("jobs.on_site") }}')}
                    </span>
                </div>
            </div>
        `;
        
        previewContainer.innerHTML = previewHTML;
    }

    // Update preview when form fields change
    form.addEventListener('input', updateJobPreview);
    form.addEventListener('change', updateJobPreview);

    // Salary validation
    const salaryMin = document.getElementById('salary_min');
    const salaryMax = document.getElementById('salary_max');

    function validateSalary() {
        const min = parseInt(salaryMin.value) || 0;
        const max = parseInt(salaryMax.value) || 0;
        
        if (min && max && min > max) {
            salaryMax.setCustomValidity('{{ __("jobs.max_salary_must_be_greater") }}');
        } else {
            salaryMax.setCustomValidity('');
        }
    }

    salaryMin.addEventListener('input', validateSalary);
    salaryMax.addEventListener('input', validateSalary);

    // Experience validation
    const expMin = document.getElementById('experience_min');
    const expMax = document.getElementById('experience_max');

    function validateExperience() {
        const min = parseInt(expMin.value) || 0;
        const max = parseInt(expMax.value) || 0;
        
        if (min && max && min > max) {
            expMax.setCustomValidity('{{ __("jobs.max_experience_must_be_greater") }}');
        } else {
            expMax.setCustomValidity('');
        }
    }

    expMin.addEventListener('input', validateExperience);
    expMax.addEventListener('input', validateExperience);

    // Save draft functionality
    document.getElementById('save-draft').addEventListener('click', function() {
        const draftInput = document.createElement('input');
        draftInput.type = 'hidden';
        draftInput.name = 'save_as_draft';
        draftInput.value = '1';
        form.appendChild(draftInput);
        
        form.submit();
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        const submitButton = document.getElementById('publish-job');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <div class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ __('jobs.publishing') }}...
            </div>
        `;
    });

    // Initialize preview
    updateJobPreview();
});
</script>
@endpush 