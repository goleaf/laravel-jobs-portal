<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByNoticeboard" id="searchByNoticeboard"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @forelse($noticeboards as $noticeboard)
            @include('noticeboards.noticeboard_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByNoticeboard)
                        {{ __('messages.noticeboard.no_noticeboard_found') }}
                    @else
                        {{ __('messages.noticeboard.no_noticeboard_available') }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                @if($noticeboards->count() > 0)
                    {{$noticeboards->links()}}
                @endif
            </div>
        </div>
    </div>
</div>
