<div class="ms-auto" wire:ignore>
    <div class="relative inline-block text-left flex items-center me-4 me-md-2">
        <button class="rounded-md transition" type="button"
            id="jobsFilterBtn"data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
            <p class="text-center">
                <i class='fas fa-filter'></i>
            </p>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="jobsFilterBtn">
            <div class="text-start border-bottom py-4 px-7">
                <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
            </div>
            <div class="p-5">
                <div class="mb-5">
                    <label for="filterBtn" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_job') }}:</label>
                    {{ Form::select('featured_job',collect($filterHeads[0][0])->sortBy('key')->toArray(),null,['class' => 'form-select io-select2 abc', 'data-control' => 'select2', 'id' => 'jobFeatured']) }}
                </div>
                <div class="mb-5">
                    <label for="filterBtn" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.job.is_suspended') }}:</label>
                    {{ Form::select('is_suspended',collect($filterHeads[0][1])->sortBy('key')->toArray(),null,['class' => 'form-select io-select2 abc', 'data-control' => 'select2', 'id' => 'jobSuspended']) }}
                </div>
                <div class="mb-5">
                    <label for="filterBtn" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.job.is_freelance') }}:</label>
                    {{ Form::select('is_freelance',collect($filterHeads[0][2])->sortBy('key')->toArray(),null,['class' => 'form-select io-select2 abc', 'data-control' => 'select2', 'id' => 'Jobfreelance']) }}
                </div>
                <div class="mb-5">
                    <label for="filterBtn" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.filter_name.job_status') }}:</label>
                    {{ Form::select('is_freelance',collect($filterHeads[0][3])->sortBy('key')->toArray(),null,['class' => 'form-select io-select2 abc', 'data-control' => 'select2', 'id' => 'JobStatus']) }}
                </div>
                <div class="flex justify-end">
                    <button type="reset" class="rounded-md transition"
                        id="job-ResetFilter">{{ __('messages.common.reset') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
