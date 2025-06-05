<div class="flex flex-wrap mainJobNotification">
    <div class="form-group col-xl-3 md:w-3/12 flex-1 sm-12 select-candidate-width">
        {{ Form::label('candidate_id', __('messages.front_home.candidates').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('candidate_id[]',$candidates, null, ['class' => 'form-select status-filter select2-hidden-accessible data-allow-clear="true"','id'=>'candidateId','data-control'=>'select2','multiple'=>true,'required', 'data-placeholder'=> __('messages.candidate.select_candidate') ]) }}

        <div class="my-5">
            {{ --            <label>{{__('messages.job_notification.select_all_jobs') }}: </label>--}}
            {{ --            <input type="checkbox" class="form-group ml-2 notification_select_all" id="ckbCheckAll">-- }}
            <div class="flex items-center form-switch form-switch-sm">
                <input type="checkbox" id="ckbCheckAll"
                       class="flex items-center input notification_select_all"
                       value="">
                <span class="custom-switch-indicator"></span>

                <label class="flex items-center label" for="ckbCheckAll">
                    {{ __('messages.job_notification.select_all_jobs') }}
                </label>
            </div>
        </div>
    </div>

    <div class="form-group col-xl-9 md:w-9/12 flex-1 sm-12">
        <ul class="list-unstyled job-notification-ul ml-5">
            @forelse($jobs as $key => $job)
                <li class="media mt-4 notification rounded shadow p-4">
                    <div class="form-group md:w-4/12 flex-1 sm-12 mb-0 pt-1">
                        <label class="flex items-center form-switch form-switch-sm">
                            <input type="checkbox" name="job_id[]"
                                   class="flex items-center input notification__checkbox jobCheck"
                                   value="{{ $job->id }}">
                            <a href="{{ route('admin.jobs.show',$job->id) }}" target="_blank"
                               class="media-title mb-1 notification__title flex items-center label ms-5 text-decoration-none">{{ \Illuminate\Support\Str::limit($job->job_title,65) }}</a>
                        </label>
                        <div class="text-time flex items-center label ms-15">{{ $job->created_at->diffForHumans() }}</div>
                    </div>
                </li>
            @empty
                <h4 class="text-center mt-9">{{ __('messages.job_notification.no_jobs_available') }}.</h4>
            @endforelse
            {{ --            <li class="no-job-available"><h4-- }}
            {{ --                    class="text-center mt-9">{{__('messages.job_notification.no_jobs_available') }}.</h4></li>--}}
        </ul>
    </div>

    <!-- Submit Field -->
    <div class="flex justify-end">
        {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','name' => 'save', 'id' => 'saveJobNotification']) }}
        <a href="{{ route('job-notification.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</a>
    </div>
</div>

