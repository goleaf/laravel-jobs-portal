<div>
    <div class="section gray padding-bottom-50">
        <div class="container mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="lg:w-full px-2 flex-1 -md-12">
                    @if(session()->has('message'))
                        <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -success">
                            {{ session('message')  }}
                        </div>
                    @endif
                </div>
                <div class="lg:w-full px-2 flex-1 -md-12">
                    @if(count($favouriteCompanies) > 0 || $searchByFavouriteCompanies != '')
                        <div class="flex flex-wrap mb-2 justify-end">
                            <div class="flex-1 -md-3">
                                <input wire:model.debounce.100ms="searchByFavouriteCompanies" type="search"
                                       id="searchByFavouriteCompanies"
                                       placeholder="{{ __('web.job_menu.search_followings')  }}"
                                       class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 search-box-placeholder">
                            </div>
                        </div>
                    @endif
                    @if(count($favouriteCompanies) > 0)
                        <div class="favorite-company-dashboard-box">
                            <div class="flex flex-wrap relative">
                                @foreach($favouriteCompanies as $favouriteCompany)
                                    <div class="w-full col-sm-6 md:w-6/12 flex-1 -xl-4 favorite-job-details mb-5">
                                        <div class="hover-effect-favorite-company min-height-200 relative {{ $loop->odd ? "blue-color' : 'black-color'  }}">
                                            @if(!empty($favouriteCompany->$company->no_of_offices))
                                            <div class="ribbon float-right ribbon-primary favorite-companies-ribbon">
                                                {{ $favouriteCompany->$company->no_of_offices .' '. __('messages.company.offices')  }}
                                            </div>
                                            @endif
                                            <div class="job-listing-details nopadding">
                                                <div class="flex job-listing-description">
                                                    <div class="pl-0 mb-auto float-left favourite-companies-avatar">
                                                        <img src="{{ $favouriteCompany->$company->$user->avatar  }}"
                                                             class="img-responsive favorite-company-image mr-2">
                                                    </div>
                                                    <div class="mb-auto w-full favorite-company-data favourite-companies-data">
                                                        <h4 class="job-listing-favorite-company d-inline-flex mb-2">
                                                            {{ (!empty($favouriteCompany->$company->$user->first_name)) ? $favouriteCompany->$company->$user->first_name : __('messages.common.n/a')   }}
                                                        </h4>
                                                        <h3 class="job-listing-title-favorite-company margin-bottom-5">
                                                            <i class="fas fa-phone-alt"></i>
                                                            {{ (!empty($favouriteCompany->$company->$user->phone)) ? '+'.$favouriteCompany->$company->$user->region_code.' '.$favouriteCompany->$company->$user->phone  : __('messages.common.n/a')  }}
                                                        </h3>
                                                        <h3 class="job-listing-title-favorite-company margin-bottom-5">
                                                            <i class="fas fa-envelope"></i>
                                                            <span data-toggle="tooltip" data-placement="bottom"
                                                                  title="{{ $favouriteCompany->$company->$user->email }}">
                                            {{ (!empty($favouriteCompany->$company->$user->email)) ? Str::limit($favouriteCompany->$company->$user->email, 20) : __('messages.common.n/a') }}</span>
                                                        </h3>
                                                        <h3 class="job-listing-title-favorite-company favourite-companies-margin mb-5 two-line-ellip">
                                                            <i class="fas fa-industry"></i> {{ (!empty($favouriteCompany->$company->industry->name)) ? $favouriteCompany->$company->industry->name : __('messages.common.n/a')  }}
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <a title="Delete"
                                               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-red-600 text-white hover:bg-red-700 action-btn delete- px-4 py-2 rounded font-medium transition-colors favorite-companies-delete"
                                               data-id="{{ $favouriteCompany->id }}"
                                               href="#">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="float-right my-2">
                                @if($favouriteCompanies->count() > 0)
                                    {{ $favouriteCompanies->links()  }}
                                @endif
                            </div>
                        </div>
                        @else
                            @if($searchByFavouriteCompanies == null || empty($searchByFavouriteCompanies))
                                <div class="lg:w-full px-2 flex-1 -md-12 flex justify-center">
                                    <h5>{{ __('messages.job.no_following_companies_found')  }} </h5>
                                </div>
                            @else
                                <div class="lg:w-full px-2 flex-1 -md-12 flex justify-center mt-4">
                                    <h5>{{ __('messages.job.following_company_not_found')  }} </h5>
                                </div>
                            @endif
                        @endif
                </div>
            </div>
        </div>
    </div>
</div>
