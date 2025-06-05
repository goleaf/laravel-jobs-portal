<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($degreeLevels) > 0 || $searchByDegreeLevel != '')
            <div class="flex-1 md-12">
                <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByDegreeLevel" id="searchByDegreeLevel"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($degreeLevels as $degreeLevel)
            @include('required_degree_levels.required_degree_level_card')
        @empty
            <div class="flex-1 md-12">
                <h5 class="text-black text-center">
                    @if ($searchByDegreeLevel)
                        {{ __('messages.required_degree_level.no_degree_level_found') }}
                    @else
                        {{ __('messages.required_degree_level.no_degree_level_available') }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($degreeLevels->count() > 0)
                    {{ $degreeLevels->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

