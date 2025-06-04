<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($maritalStatuses) > 0 || $searchByMaritalStatus != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByMaritalStatus" id="searchByMaritalStatus"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search')  }}" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($maritalStatuses as $maritalStatus)
            @include('marital_status.marital_status_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByMaritalStatus)
                        {{ __('messages.marital_status.no_marital_status_found')  }}
                    @else
                        {{ __('messages.marital_status.no_marital_status_available')  }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($maritalStatuses->count() > 0)
                    {{ $maritalStatuses->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

