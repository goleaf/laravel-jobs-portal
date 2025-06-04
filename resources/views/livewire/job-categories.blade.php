<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($jobCategories) > 0 || $filterFeatured != '' || $searchByJobCategory != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByJobCategory" id="searchByJobCategory"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($jobCategories as $jobCategory)
            @include('job_categories.job_categories_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByJobCategory)
                        {{ __('messages.job_category.no_job_category_found') }}
                    @else
                        {{ __('messages.job_category.no_job_category_available') }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                @if($jobCategories->count() > 0)
                    {{$jobCategories->links()}}
                @endif
            </div>
        </div>
    </div>
</div>

