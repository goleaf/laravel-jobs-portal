<div class="lg:w-full px-2 flex-1 md-12">
{{-- @if(session()->has('message')) --}}
{{-- <div class="px-4 py-3 rounded-md border border border-gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 success"> --}}
{{-- {{ session('message') }} --}}
{{-- </div> --}}
{{-- @endif --}}
    @if(count($appliedJobs) > 0 || $searchByAppliedJob != '' || $jobApplicationStatus != '')
        <div class="flex flex-wrap mb-3 justify-end">
            <div class="flex-1 md-3">
                {{ Form::select('job-application-status', $jobApplicationStatusArr, null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'jobApplicationStatus','placeholder' => __('messages.common.all'), 'wire:model' =>"jobApplicationStatus"]) }}
            </div>
            <div class="flex-1 md-3">
                <input wire:model.debounce.100ms.live="searchByAppliedJob" type="search"
                       id="searchByAppliedJob"
                       placeholder="{{ __('web.job_menu.search_applied_job') }}"
                       class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 search-box-placeholder">
            </div>
        </div>
    @endif
    @if(count($appliedJobs) > 0)
        <div class="content1 with-padding">
            <div class="flex flex-wrap mt-5 relative">
                @foreach($appliedJobs as $appliedJob)
                   <div class="w-full flex-1 -sm-6 md:w-6/12 flex-1 xl-6 mb-4">
                       <div class="h-full shadow rounded bg-white shadow rounded -lg overflow-hidden">
                           <div class="bg-white shadow rounded -lg overflow-hidden body p-5">
                               <div class="flex justify-end">
                                   <div class="relative inline-block text-left">
                                       <button type="button" title="{{ __('messages.common.action') }}"
                                               class="inline-flex justify-center w-full rounded-md border border-gray-300 border border-gray-300 -gray-300 shadow-sm px-4 py-2 rounded font-medium transition-colors text-indigo-600 -600 p-0"
                                               id="dropdownMenuButton1" data-bs-toggle="dropdown"
                                               data-bs-boundary="viewport" aria-expanded="false">
                                           <i class="fa-solid fa-ellipsis-vertical"></i>
                                       </button>
                                       <ul class="origin-top-right absolute right-0 mt-2 w-56 rounded -md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 min-width-220 customDropdown"
                                           aria-labelledby="dropdownMenuButton1" style="">
                                           <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 apply-job-note"
                                                  href="javascript:void(0)"
                                                  data-id="{{ $appliedJob->id }}">{{ __('messages.common.view') }}</a>
                                            </li>
                                            @if(\App\Models\JobApplicationSchedule::whereJobApplicationId($appliedJob->id)->exists() && !($appliedJob->status == \App\Models\JobApplication::REJECTED) && !($appliedJob->status == \App\Models\JobApplication::STATUS_APPLIED) && !($appliedJob->status == \App\Models\JobApplication::COMPLETE))
                                                <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 schedule-slot-book" href="javascript:void(0)"   data-id="{{ $appliedJob->id }}">{{ __('messages.job_stage.slots') }}</a></li>
                                            @endif
                                            <li><a class="block px-4 py-2 rounded font-medium transition-colors remove-applied-jobs" href="javascript:void(0)" data-id="{{ $appliedJob->id }}">{{ __('messages.common.delete') }}</a></li>
                                        </ul>

                                    </div>
                                </div>
                                <div class="flex">
                                    <div class="mb-auto">
                                        <h4>
                                            <i class="fas fa-briefcase fs-3 me-1 text-gray-500"></i> &nbsp;<a
                                                    href="{{ route('front.',$appliedJob->$job->job_id) }}"
                                                    target="_blank" class="text-decoration-none">{{ Str::limit($appliedJob->$job->job_title,25,'...') }}</a>
                                            <div
                                                    class="ms-2 inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 -{{ \App\Models\JobApplication::STATUS_COLOR[$appliedJob->status] }}">
                                                @if(\App\Models\JobApplication::STATUS[$appliedJob->status] == 'Drafted')
                                                    {{ __('messages.common.drafted') }}
                                                @elseif(\App\Models\JobApplication::STATUS[$appliedJob->status] == 'Applied')
                                                    {{ __('messages.common.applied') }}
                                                @elseif(\App\Models\JobApplication::STATUS[$appliedJob->status] == 'Declined')
                                                    {{ __('messages.common.declined') }}
                                                @elseif(\App\Models\JobApplication::STATUS[$appliedJob->status] == 'Hired')
                                                    {{ __('messages.common.hired') }}
                                                @else
                                                    {{ __('messages.common.ongoing') }}
                                                @endif

                                            </div>
                                        </h4>
                                        <h4>
                                            <i class="far fa-clock fs-3 me-1 text-gray-500"></i>
                                            &nbsp;<label
                                                    class="text-gray-500 mb-3">{{ __('messages.common.applied_on') }}
                                                :</label>
                                            {{ (!empty($appliedJob->created_at)) ? \Carbon\Carbon::parse($appliedJob->created_at)->translatedFormat('dS M, Y')  : __('messages.common.n/a') }}
                                        </h4>
                                        <h4>
                                            <i class="fas fa-money-check-alt fs-3 me-1 text-gray-500"></i>
                                            &nbsp;{{ (!empty($appliedJob->expected_salary)) ? number_format($appliedJob->expected_salary)   : __('messages.common.n/a') }} {{ $appliedJob->$job->currency->currency_icon }}
                                        </h4>
                                        @isset($appliedJob->jobStage->name)
                                            <h4>
                                                <i class="fab fa-usps fs-3 me-1 text-gray-500"></i>
                                                &nbsp;{{ $appliedJob->jobStage->name }}
                                            </h4>
                                        @endisset
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                @endforeach
            </div>
            <div class="flex justify-center my-2">
                @if($appliedJobs->count() > 0)
                    {{ $appliedJobs->links() }}
                @endif
            </div>
        </div>
    @else
        @if($searchByAppliedJob == null || empty($searchByAppliedJob))
        <div class="lg:w-full px-2 flex-1 md-12 flex justify-center my-9 job-titile">
            <h5>{{ __('messages.job.no_applied_job_found') }} </h5>
        </div>
        @else
        <div class="lg:w-full px-2 flex-1 md-12 flex justify-center my-9 job-titile">
            <h5>{{ __('messages.job.applies_job_not_found') }} </h5>
        </div>
        @endif
    @endif
</div>
