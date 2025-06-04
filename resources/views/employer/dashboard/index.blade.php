@extends('employer.layouts.app')
@section('title')
    {{ __('messages.employer_dashboard.dashboard')  }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css')  }}">
@endpush
@section('content')
    <div class="flex flex-wrap">
        <div class="col-xl-4 flex-1 -sm-6 widget">
            <a href="{{ route('job.index')  }}" class=" text-decoration-none">
            <div class="bg-green-600 shadow-md rounded-10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                <div class="bg-green-300 widget-icon rounded-10 me-2 flex items-center justify-center">
                    <i class="fas fa-briefcase text-white fs-1-xl fa-4x"></i>
                </div>
                <div class="text-end text-white">
                    <h2 class="fs-1-xxl fw-bolder text-white">{{ isset($totalJobs)?numberFormatShort($totalJobs):'0'  }}</h2>
                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.employer_menu.total_jobs')  }}</h3>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-4 flex-1 -sm-6 widget">
            <a href="{{ route('job.index')  }}" class=" text-decoration-none">
            <div class="bg-primary-600 shadow-md rounded-10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                <div class="bg-cyan-300 widget-icon rounded-10 me-2 flex items-center justify-center">
                    <i class="far fa-clock text-white fs-1-xl fa-4x"></i>
                </div>
                <div class="text-end text-white">
                    <h2 class="fs-1-xxl fw-bolder text-white">{{ isset($jobCount)?numberFormatShort($jobCount):'0'  }}</h2>
                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.employer_menu.live_jobs')  }}</h3>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-4 flex-1 -sm-6 widget">
            <a href="{{ route('job.index')  }}" class=" text-decoration-none">
            <div class="bg-yellow-500 shadow-md rounded-10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                <div class="bg-yellow-300 widget-icon rounded-10 me-2 flex items-center justify-center">
                    <i class="fas fa-pause-circle text-white fs-1-xl fa-4x"></i>
                </div>
                <div class="text-end text-white">
                    <h2 class="fs-1-xxl fw-bolder text-white">{{ isset($pausedJobCount)?numberFormatShort($pausedJobCount):'0'  }}</h2>
                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.employer_menu.paused_jobs')  }}</h3>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-4 flex-1 -sm-6 widget">
            <div class="bg-red-600 shadow-md rounded-10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                <div
                        class="bg-red-300 widget-icon rounded-10 me-2 flex items-center justify-center">
                    <i class="fas fa-window-close text-white fs-1-xl fa-4x"></i>
                </div>
                <div class="text-end text-white">
                    <h2 class="fs-1-xxl fw-bolder text-white">{{ isset($closedJobCount)?numberFormatShort($closedJobCount):'0'  }}</h2>
                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.employer_menu.closed_jobs')  }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-4 flex-1 -sm-6 widget">
            <a href="{{ route('followers.index')  }}" class=" text-decoration-none">
            <div class="bg-blue-500 shadow-md rounded-10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                <div
                        class="bg-blue-300 widget-icon rounded-10 me-2 flex items-center justify-center">
                    <i class="far fa-user text-white fs-1-xl fa-4x"></i>
                </div>
                <div class="text-end text-white">
                    <h2 class="fs-1-xxl fw-bolder text-white">{{ isset($followersCount)?numberFormatShort($followersCount):'0'  }}</h2>
                    <h3 class="mb-0 fs-4 fw-light">{{ __('messages.employer_menu.followers')  }}</h3>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-4 flex-1 -sm-6 widget">
            <div class="bg-gray-800 shadow-md rounded-10 p-xxl-10 px-5 py-10 flex items-center justify-between my-sm-3 my-2">
                <div
                        class="bg-gray-700 widget-icon rounded-10 me-2 flex items-center justify-center">
                    <i class="fas fa-file fa-4x fs-1-xl {{ getLoggedInUser()->theme_mode ? 'text-muted' : 'text-white' }}"></i>
                </div>
                <div class="text-end {{ getLoggedInUser()->theme_mode ? 'text-muted' : 'text-white' }}">
                    <h2 class="fs-1-xxl fw-bolder text-light"> {{ isset($jobApplicationsCount) ? numberFormatShort($jobApplicationsCount) : '0'  }}</h2>
                    <h3 class="mb-0 fs-4 fw-light text-light">{{ __('messages.employer_menu.total_job_applications')  }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md border border-gray-300 border-gray-200 bg-white shadow rounded-lg overflow-hidden -xl-stretch mb-xl-8">
        <!--begin::Header-->
        <div class="bg-white shadow rounded-lg overflow-hidden -header border-0 pt-5">
            <h3 class="bg-white shadow rounded-lg overflow-hidden -title items-start flex-col">
                                    <span
                                            class="bg-white shadow rounded-lg overflow-hidden -label fs-3 mb-1">{{ __('messages.job_applications')  }}</span>
            </h3>
            <div class="lg:w-8/12 px-2 md:w-8/12 flex-1 -sm-12">
                <div class="flex flex-wrap justify-end">
                    <div class="lg:w-4/12 px-2 md:w-4/12 col-xl-3 flex-1 -sm-4 mt-3 mt-md-0">
                        <div class="bg-white shadow rounded-lg overflow-hidden -header-action w-full">
                            {{ Form::select('jobs', $jobStatus, null, ['id' => 'jobStatus', 'class' => 'form-control status-filter', 'placeholder' => __('messages.flash.select_job')])  }}
                        </div>
                    </div>
                    <div class="lg:w-4/12 px-2 md:w-4/12 col-xl-3 flex-1 -sm-4 mt-3 mt-md-0">
                        <div class="bg-white shadow rounded-lg overflow-hidden -header-action w-full">
                            {{ Form::select('gender', $gender, null, ['id' => 'gender', 'class' => 'form-control status-filter', 'placeholder' => __('messages.company.select_gender')])  }}
                        </div>
                    </div>
                    <div class="lg:w-4/12 px-2 md:w-4/12 col-xl-4 flex-1 -sm-4 mt-0">
                        <div id="timeRange" class="time_range time_range_width w-30 border border-gray-300 rounded-2 p-3">
                            <i class="far fa-calendar-alt"
                               aria-hidden="true"></i>&nbsp;&nbsp;<span></span> <b
                                    class="caret"></b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="jobContainer" class="bg-white shadow rounded-lg overflow-hidden -body">
            <canvas id="employerDashboardChart" width="400" height="400"></canvas>
        </div>
    </div>
    <div class="flex flex-wrap">
        <!--begin::Col-->
        <div class="flex-1 -xl-6 ps-0">
            <!--begin::Tables Widget 1-->
            <div class="mb-xl-8">
                <!--begin::Header-->
                <div class="flex justify-between border-0 pt-5">
                    <h3 class="items-start flex-col">
                        <span class="fs-3 mb-1">{{ __('messages.employer_menu.recent_jobs')  }}</span>
                    </h3>
                    <!--begin::Menu-->
                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                    <span>
                         <a href="{{ route('job.index')  }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -info">{{ __('messages.common.view_more')  }} <i
                                     class="fas fa-chevron-right"></i></a>
                    </span>
                    <!--end::Svg Icon-->
                    <!--end::Menu-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="py-3">
                    <!--begin::Table container-->
                    <div class="w-full divide-y divide-gray-200 -responsive">
                        <!--begin::Table-->
                        <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 -striped align-middle gs-0 gy-5">
                            <!--begin::Table head-->
                            <thead>
                            <tr class="text-start text-gray-500 fs-7 text-uppercase gs-0">
                                <th class="">{{ __('messages.job.job_title')  }}</th>
                                <th class="">{{ __('messages.employer_menu.expires_on')  }}</th>
                                <th class="text-center">{{ __('messages.common.status')  }}</th>
                            </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-bold">
                            @if(count($recentJobs) > 0)
                                @foreach($recentJobs as $recentJob)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('front.job.details',$recentJob->job_id)  }}"
                                               class="text-decoration-none"
                                               data-turbo="false">{{ html_entity_decode($recentJob->job_title)  }}</a>
                                        </td>
                                        <td>
                                            {{ Carbon\Carbon::parse($recentJob->job_expiry_date)->translatedFormat('jS M, Y')  }}
                                        </td>
                                        <td class="text-center">
                                            <div
                                                    class="badge w-auto bg-{{ \App\Models\Job::STATUS_COLOR[$recentJob->status] }}">
                                                <span
                                                        class="px-3">{{ \App\Models\Job::STATUS[$recentJob->status]  }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table container-->
                </div>
            </div>
            <!--endW::Tables Widget 1-->
        </div>
        <!--end::Col-->
        <div class="flex-1 -xl-6 pe-0">
            <!--begin::Tables Widget 1-->
            <div class="mb-xl-8">
                <!--begin::Header-->
                <div class="flex justify-between border-0 pt-5">
                    <h3 class="items-start flex-col">
                        <span class=" fs-3 mb-1">{{ __('messages.employer_menu.recent_follower')  }}</span>
                    </h3>
                    <!--begin::Menu-->
                    <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                    <span>
                         <a href="{{ route('followers.index')  }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -info">{{ __('messages.common.view_more')  }} <i
                                     class="fas fa-chevron-right"></i></a>
                    </span>
                    <!--end::Svg Icon-->
                    <!--end::Menu-->
                </div>
                <!--end::Header-->
                <!--begin::Body-->
                <div class="py-3">
                    <!--begin::Table container-->
                    <div class="w-full divide-y divide-gray-200 -responsive">
                        <!--begin::Table-->
                        <table class="min-w-full divide-y divide-gray-200 w-full divide-y divide-gray-200 -striped align-middle gs-0 gy-5">
                            <!--begin::Table head-->
                            <thead>
                            <tr class="text-start text-gray-500 fs-7 text-uppercase gs-0">
                                <th class="">{{ __('messages.company.candidate_name')  }}</th>
                                <th class="">{{ __('messages.company.candidate_phone')  }}</th>
                                <th class="text-center">{{ __('messages.company.candidate_email')  }}</th>
                            </tr>
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600">
                            @if(count($recentFollowers) > 0)
                                @foreach($recentFollowers as $recentFollower)
                                    <tr>
                                        <td class="ps-3">
                                            {{ html_entity_decode($recentFollower->$user->full_name)  }}
                                        </td>
                                        <td>
                                            {{ empty($recentFollower->$user->phone) ? __('messages.common.n/a') : $recentFollower->$user->phone  }}
                                        </td>
                                        <td>
                                            {{ $recentFollower->$user->email  }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="text-center">
                                        <span>{{ __('messages.employer_menu.no_data_available')  }}.</span>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                            <!--end::Table body-->
                        </table>
                        <!--end::Table-->
                    </div>
                    <!--end::Table container-->
                </div>
            </div>
            <!--endW::Tables Widget 1-->
        </div>

    </div>
@endsection
