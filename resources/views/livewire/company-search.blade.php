<div>
    <section class="find-job-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap justify-between">
                <div class="flex-1 -12">
                    <div class="find-job relative bg-white">
                        <div class="flex flex-wrap items-center justify-content-around m-0">
                            <div class="flex-1 lg-3 br-2 px-20">
                                <h3 class="fs-16 text-gray-600 mb-0">@lang('messages.notification.company')</h3>
                                <input type="search" class="fs-14 text-gray mb-0"
                                    wire:model.debounce.100ms.live="searchByCompany" autocomplete="off" id="searchByCompany"
                                    placeholder="@lang('web.web_company.search_company')">
                            </div>
                            <div class="flex-1 lg-3 br-2 px-20">
                                <h3 class="fs-16 text-gray-600 mb-0">@lang('messages.company.location')</h3>
                                <input type="search" class="fs-14 text-gray mb-0"
                                    wire:model.debounce.100ms.live="searchByCity" id="searchByCity"
                                    placeholder="@lang('web.web_company.search_city')">
                            </div>
                            <div class="flex-1 lg-3 br-2 px-20">
                                <h3 class="fs-16 text-gray-600 mb-0">@lang('messages.company.industry')</h3>
                                <input type="search" class="fs-14 text-gray mb-0"
                                    wire:model.debounce.100ms.live="searchByIndustry" id="searchByIndustry"
                                    placeholder="@lang('web.web_company.search_by_industry')">
                            </div>
                            <div class="flex-1 -xl-2 flex-1 lg-3 text-center p-xl-1 px-20">
                                <a href="#" wire:click="resetFilter()" class="border border-gray-300 bg-transparent">{{ __('web.reset_filter') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="latest-job-section py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="job- bg-white shadow rounded -lg overflow-hidden">
                <div class="flex flex-wrap">
                    @forelse($companies as $company)
                        @include('front_web_template.common.company_card')
                    @empty
                        <div class="flex-1 md-12 text-center text-gray">
                            {{ __('web.companies_menu.no_company_found') }}
                        </div>
                    @endforelse
                </div>
            </div>
            @if ($companies->count() > 0)
                <div class="pagination-section pt-lg-5 pt-3">
                    {{ $companies->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
