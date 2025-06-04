<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($salaryCurrencies) > 0 || $searchSalaryCurrencies != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchSalaryCurrencies" id="searchSalaryCurrencies"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search')  }}" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($salaryCurrencies as $salaryCurrency)
            @include('salary_currencies.salary_currencies_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchSalaryCurrencies)
                        {{ __('messages.salary_currency.no_salary_currency_found')  }}
                    @else
                        {{ __('messages.salary_currency.no_salary_currency_available')  }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($salaryCurrencies->count() > 0)
                    {{ $salaryCurrencies->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

