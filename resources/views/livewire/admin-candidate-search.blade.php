<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($candidates) > 0 || $status != '' || $immediateAvailable != '' || $jobSkills != '' || $searchByAdminCandidate != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByAdminCandidate" id="searchByAdminCandidate"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($candidates as $candidate)
            @include('candidates.candidate   _card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if($searchByAdminCandidate)
                        {{__('messages.candidate.no_candidate_found')}}
                    @else
                        {{__('messages.candidate.no_candidate_available')}}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                @if($candidates->count() > 0)
                    {{$candidates->links()}}
                @endif
            </div>
        </div>
    </div>
</div>
