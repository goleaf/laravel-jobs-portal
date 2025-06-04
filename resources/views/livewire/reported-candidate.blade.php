<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($reportedCandidates) > 0 || $searchByCandidate != '' || $filterByReportedDate != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByCandidate" id="searchByCandidate"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($reportedCandidates as $reportedCandidate)
            @include('candidate.reported_candidate.reported_candidate_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByCandidate)
                        {{ __('messages.candidate.no_reported_candidates_found') }}
                    @else
                        {{ __('messages.candidate.no_reported_candidates_available') }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($reportedCandidates->count() > 0)
                    {{$reportedCandidates->links()}}
                @endif
            </div>
        </div>
    </div>
</div>
