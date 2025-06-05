@extends('layouts.app')
@section('title')
    {{ __('messages.dashboard') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            <div class="flex flex-wrap">
                <div class="flex-1 -12 mb-4">
                    <div class="flex flex-wrap">
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <a href="{{ route('admin.candidates.index') }}" class="text-decoration-none">
                            <div class="bg-indigo-600 -600 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                <div class="bg-cyan-300 widget-icon rounded -10 me-2 flex items-center justify-center">
                                    <i class="fa-solid fa-users fs-1-xl text-white"></i>
                                </div>
                                <div class="text-end text-white">
                                    <h2 class="fs-1-xxl fw-bolder text-white">{{ numberFormatShort($data['dashboardData']['totalCandidates']) }}</h2>
                                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.admin_dashboard.total_candidates') }}</h3>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <a href="{{ route('company.index') }}" class="text-decoration-none">
                            <div class="bg-green-600 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                <div class="bg-green-300 widget-icon rounded -10 me-2 flex items-center justify-center">
                                    <i class="fa-solid fa-user-shield fs-1-xl text-white"></i>
                                </div>
                                <div class="text-end text-white">
                                    <h2 class="fs-1-xxl fw-bolder text-white">{{ numberFormatShort($data['dashboardData']['totalEmployers']) }}</h2>
                                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.admin_dashboard.total_employers') }}</h3>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <a href="{{ route('admin.jobs.index') }}" class="text-decoration-none">
                            <div class="bg-blue-500 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                <div class="bg-blue-300 widget-icon rounded -10 me-2 flex items-center justify-center">
                                    <i class="fa-solid fa-list-alt fs-1-xl text-white"></i>
                                </div>
                                <div class="text-end text-white">
                                    <h2 class="fs-1-xxl fw-bolder text-white">{{ numberFormatShort($data['dashboardData']['totalActiveJobs']) }}</h2>
                                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.admin_dashboard.total_active_jobs') }}</h3>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <a href="{{ route('admin.jobs.index') }}" class="text-decoration-none">
                            <div class="bg-yellow-500 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                <div class="bg-yellow-300 widget-icon rounded -10 me-2 flex items-center justify-center">
                                    <i class="fa-brands fa-foursquare fs-1-xl text-white"></i>
                                </div>
                                <div class="text-end text-white">
                                    <h2 class="fs-1-xxl fw-bolder text-white">{{ numberFormatShort($data['dashboardData']['featuredJobs']) }}</h2>
                                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.admin_dashboard.featured_jobs') }}</h3>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <a href="{{ route('company.index') }}" class="text-decoration-none">
                                <div class="bg-gray-600 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                    <div class="bg-gray-600 widget-icon rounded -10 me-2 flex items-center justify-center">
                                        <i class="fa-solid fa-user-tag fs-1-xl text-white"></i>
                                    </div>
                                    <div class="text-end text-white">
                                        <h2 class="fs-1-xxl fw-bolder text-white">{{ numberFormatShort($data['dashboardData']['featuredEmployers']) }}</h2>
                                        <h3 class="mb-0 fs-4 fw-light">{{ __('messages.admin_dashboard.featured_employers') }}</h3>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <div class="bg-red-600 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                <div class="bg-red-300 widget-icon rounded -10 me-2 flex items-center justify-center">
                                    <i class="fa-solid fa-money-check fs-1-xl text-white"></i>
                                </div>
                                <div class="text-end text-white">
                                    <h2 class="fs-1-xxl fw-bolder text-white">{{ numberFormatShort($data['dashboardData']['featuredJobsIncomes']) }}</h2>
                                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.admin_dashboard.featured_jobs_incomes') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <div class="bg-gray-800 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                <div class="bg-gray-700 widget-icon rounded -10 me-2 flex items-center justify-center">
                                    <i class="fa-solid fa-money-check-alt fs-1-xl text-light"></i>
                                </div>
                                <div class="text-end text-white">
                                    <h2 class="fs-1-xxl fw-bolder text-light">{{ numberFormatShort($data['dashboardData']['featuredCompanysIncomes']) }}</h2>
                                    <h3 class="mb-0 fs-4 fw-light text-light">{{ __('messages.admin_dashboard.featured_employers_incomes') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 px-4-xxl-3 flex-1 -xl-4 flex-1 sm-6 widget">
                            <a href="{{ route('admin.') }}" class="text-decoration-none">
                                <div class="bg-indigo-600 -600 shadow-md rounded -10  px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                                    <div class="bg-cyan-300 widget-icon rounded -10 me-2 flex items-center justify-center">
                                        <i class="fa-solid fa-money-bill fs-1-xl text-white"></i>
                                    </div>
                                    <div class="text-end text-white">
                                        <h2 class="fs-1-xxl fw-bolder text-white">{{ numberFormatShort($data['dashboardData']['subscriptionIncomes']) }}</h2>
                                        <h3 class="mb-0 fs-4 fw-light">{{ __('messages.admin_dashboard.subscription_incomes') }}</h3>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -xxl-6 mb-7 flex-1 -12">
                    <div class="bg-white shadow rounded -lg overflow-hidden">
                        <div class="bg-white shadow rounded -lg overflow-hidden header pb-0 px-10">
                            <h3 class="mb-0 p-2">{{ __('messages.admin_dashboard.post_statistics') }}</h3>
                        </div>
                        <div class="bg-white shadow rounded -lg overflow-hidden body pt-7" id="postStatisticsChartContainer">
                            <canvas id="postStatisticsChart" width="515" height="400"></canvas>
                        </div>
                    </div>
                </div>
                <div class="flex-1 -xxl-6 mb-7 flex-1 -12">
                    <div class="bg-white shadow rounded -lg overflow-hidden">
                        <div class="bg-white shadow rounded -lg overflow-hidden header pb-0 px-10">
                            <h3 class="mb-0">{{ __('messages.admin_dashboard.weekly_users') }}</h3>
                            <div id="timeRange" class="time_range time_range_width w-30 border border border-gray-300 -gray-300 rounded -2 p-2">
                                <i class="far fa-calendar-alt"
                                   aria-hidden="true"></i>&nbsp;&nbsp<span></span> <b
                                        class="caret"></b>
                            </div>
                        </div>
                        <div class="bg-white shadow rounded -lg overflow-hidden body pt-7" id="weeklyUserBarChartContainer">
                            <canvas id="weeklyUserBarChart" width="515" height="400"></canvas>
                        </div>
                    </div>
                </div>

                <!-- recent registered candidates starts -->
                <div class="flex-1 -xxl-6 flex-1 -12 mb-7">
                    <div class="flex justify-between pb-0">
                        <h3 class="mb-0 mt-2">{{ __('messages.admin_dashboard.recent_candidates') }}</h3>
                        <div class="">
                            <a href="{{ route('admin.candidates.index') }}"
                               class="border border-gray-300 bg-transparent">{{ __('messages.common.view_more') }} <i
                                        class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="pt-7">
                        <div class="w-full divide-y divide-gray-200 responsive">
                            <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 striped mb-0">
                                <thead>
                                <tr class="">
                                    <th scope="flex-1 px-4">{{ __('messages.common.name') }}</th>
                                    <th scope="flex-1 px-4">{{ __('messages.common.created_date') }}</th>
                                    <th scope="flex-1 px-4">{{ __('messages.candidate.immediate_available') }}</th>
                                    <th scope="flex-1 px-4">{{ __('messages.candidate.is_verified') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($data['registerCandidatesData'] as $registeredCandidates)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.', $registeredCandidates->id) }}"
                                               class="text-decoration-none">
                                                {{ $registeredCandidates->$user->full_name }}</a>
                                        </td>
                                        <td>{{ $registeredCandidates->created_at->diffForhumans() }}</td>
                                        <td>
                                            <i class="pl-5 {{ ($registeredCandidates->immediate_available) ? 'fas fa-check-circle text-green-600 ' : 'fas fa-times-circle text-red-600 ' }}"></i>
                                        </td>
                                        <td>
                                            <i class="pl-4 {{ ($registeredCandidates->$user->is_verified) ? 'fas fa-check-circle text-green-600 ' : 'fas fa-times-circle text-red-600 ' }}"></i>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td flex-1 px-4span="6"
                                                class="text-center">{{ __('messages.employer_menu.no_data_available') }}
                                                .
                                            </td>
                                        </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- recent registered candidates ends -->

                <!-- recent registered employers starts -->
                <div class="flex-1 -xxl-6 flex-1 -12 mb-7">
                    <div class="flex justify-between pb-0">
                        <h3 class="mb-0 mt-2">{{ __('messages.admin_dashboard.recent_employers') }}</h3>
                        <div>
                            <a href="{{ route('company.index') }}"
                               class="border border-gray-300 bg-transparent">{{ __('messages.common.view_more') }} <i
                                        class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="pt-7">
                        <div class="w-full divide-y divide-gray-200 responsive">
                            <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 striped mb-0">
                                <thead>
                                <tr class="">
                                    <th scope="flex-1 px-4">{{ __('messages.common.name') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.common.created_date') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.company.website') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.company.location') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.company.is_featured') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($data['registerEmployersData'] as $registeredEmployers)
                                        <tr>
                                            <td>
                                                <a class="text-decoration-none"
                                                   href="{{ route('company.show', $registeredEmployers->id) }}">{{ $registeredEmployers->$user->full_name }}</a>
                                            </td>
                                            <td>{{ $registeredEmployers->created_at->diffForhumans() }}</td>
                                            <td>
                                                @if($registeredEmployers->website !== null)
                                                    <a href="{{ (!str_contains($registeredEmployers->website,'https://') 
                                                    ? 'https://'.$registeredEmployers->website
                                                    : $registeredEmployers->website) }}" class="text-decoration-none"
                                                       target="_blank">{{ Str::limit($registeredEmployers->website,25,'...') }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                {{ $registeredEmployers->location != '' ? $registeredEmployers->location : 'N/A' }}
                                            </td>
                                            <td>
                                                <i class="pl-4 {{ ($registeredEmployers->activeFeatured) ? 'fas fa-check-circle text-green-600 ' : 'fas fa-times-circle text-red-600 ' }}"></i>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td flex-1 px-4span="6"
                                                class="text-center">{{ __('messages.employer_menu.no_data_available') }}
                                                .
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- recent registered employers ends -->

                <!-- recent jobs starts -->
                <div class="flex-1 -12">
                    <div class="flex justify-between pb-0">
                        <h3 class="mb-0 mt-2">{{ __('messages.admin_dashboard.recent_jobs') }}</h3>
                        <div>
                            <a href="{{ route('admin.jobs.index') }}"
                               class="border border-gray-300 bg-transparent">{{ __('messages.common.view_more') }} <i
                                        class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <div class="pt-7">
                        <div class="w-full divide-y divide-gray-200 responsive">
                            <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 striped">
                                <thead>
                                <tr class="">
                                    <th scope="flex-1 px-4">{{ __('messages.job.job_title') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.company.employer_name') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.common.created_date') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.job_category.job_category') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.job.job_type') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.job.job_shift') }}</th>
                                        <th scope="flex-1 px-4">{{ __('messages.job.is_featured') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($data['recentJobsData'] as $recentJobs)
                                        <tr>
                                            <td>
                                                <a class="text-decoration-none"
                                                   href="{{ route('admin.', $recentJobs->id) }}">{{ $recentJobs->job_title }}</a>
                                            </td>
                                            <td>
                                                <a class="text-decoration-none"
                                                   href="{{ route('company.show', $recentJobs->company_id) }}">{{ $recentJobs->$company->$user->full_name }}</a>
                                            </td>
                                            <td>{{ $recentJobs->created_at->diffForhumans() }}</td>
                                            <td>{{ $recentJobs->jobCategory->name }}</td>
                                            <td>{{ Str::limit($recentJobs->jobType->name,50,'...') }}</td>
                                            <td>{{ (!empty($recentJobs->jobShift)) ? $recentJobs->jobShift->shift : 'N/A' }}</td>
                                            <td>
                                                <i class="pl-4 {{ ($recentJobs->activeFeatured) ? 'fas fa-check-circle text-green-600 ' : 'fas fa-times-circle text-red-600 ' }}"></i>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td flex-1 px-4span="6"
                                                class="text-center">{{ __('messages.employer_menu.no_data_available') }}
                                                .
                                            </td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <!-- recent jobs ends -->
            </div>
        </div>
    </div>
@endsection
