<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($jobTags) > 0 || $searchByJobTags != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByJobTags" id="searchByJobTags"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($jobTags as $jobTag)
            @include('job_tags.job_tags_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByJobTags)
                        {{ __('messages.job_tag.no_job_tag_found') }}
                    @else
                        {{ __('messages.job_tag.no_job_tag_available') }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                @if($jobTags->count() > 0)
                    {{$jobTags->links()}}
                @endif
            </div>
        </div>
    </div>
</div>
