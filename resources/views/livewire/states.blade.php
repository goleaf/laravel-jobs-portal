<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($states) > 0 || $searchByState != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByState" id="searchByState"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search')  }}" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($states as $state)
            @include('states.state_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByState)
                        {{ __('messages.state.no_state_found')  }}
                    @else
                        {{ __('messages.state.no_state_available')  }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($states->count() > 0)
                    {{ $states->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

