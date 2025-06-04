<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($ownershipTypes) > 0 || $searchByOwnershipType != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByOwnershipType" id="searchByOwnershipType"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($ownershipTypes as $ownershipType)
            @include('ownership_types.ownership_type_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByOwnershipType)
                        {{ __('messages.ownership_type.no_ownership_type_found') }}
                    @else
                        {{ __('messages.ownership_type.no_ownership_type_available') }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                @if($ownershipTypes->count() > 0)
                    {{$ownershipTypes->links()}}
                @endif
            </div>
        </div>
    </div>
</div>
