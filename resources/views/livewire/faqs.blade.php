<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($faqs) > 0 || $searchByFaq != '')
            <div class="flex-1 md-12">
                <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByFaq" id="searchByFaq"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search') }}" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($faqs as $faq)
            @include('faqs.faq_card')
        @empty
            <div class="flex-1 md-12">
                <h5 class="text-black text-center">
                    @if ($searchByFaq)
                        {{ __('messages.faq.no_faq_found') }}
                    @else
                        {{ __('messages.faq.no_faq_available') }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($faqs->count() > 0)
                    {{ $faqs->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
