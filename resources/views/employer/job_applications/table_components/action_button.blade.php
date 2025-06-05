<?php

        $isJobExpiry = false;
        if (\Carbon\Carbon::now() > $row->$job->job_expiry_date) {
            $isJobExpiry = true;
        }
        
        $isCompleted = false;
        $isShortlisted = false;
        $isJobStage = false;
        $jobStageId = null;
        $isRejected = false;
        $isApplied = false;
        
        if ($row->status == 1) {
            $isApplied = true;
        }
        if ($row->status == 2) {
            $isRejected = true;
        }
        if ($row->status == 3) {
            $isCompleted = true;
        }
        if ($row->status == 4) {
            $isShortlisted = true;
        }
        
        if (!empty($row->job_stage_id)) {
            $isJobStage = true;
            $jobStageId = $row->job_stage_id;
        }
?>


<div class="relative inline-block text-left">
    <a class="rounded-md transition" id="actionDropDown" data-bs-toggle="dropdown"
       aria-expanded="false">
        {{ __('messages.common.action') }}
    </a>
    <ul class="fs-6 py-4 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 customDropdown"
        aria-labelledby="dropdownMenuButton1">
        <li>
            <input type="hidden" name="data-job-id" value="{{ $this->jobId }}" id="dataJobId">
            @if(!$isCompleted && !$isRejected)
                @if(!$isShortlisted)
                    <a href="javascript:void(0)" class="rounded-md transition"
                       data-id="{{ $row->id }}">{{ __('messages.common.shortlist') }}</a>
                @else
                    @if(!$isJobExpiry)
                        <a href="javascript:void(0)" class="rounded-md transition"
                           data-id="{{ $row->id }}"
                           data-stage-id="{{ $jobStageId }}">{{ __('messages.job_stage.job_stage') }}</a>
                    @endif
                @endif
                   
                @if(!$isApplied)
                    <a href="javascript:void(0)" class="rounded-md transition"
                       data-id="{{ $row->id }}">{{ __('messages.common.selected') }}</a>
                @endif
                <a href="javascript:void(0)" class="rounded-md transition"
                   data-id="{{ $row->id }}">{{ __('messages.common.rejected') }}</a>
                @if($isJobStage && !$isRejected && !$isJobExpiry)
                    <a data-turbo="false" href="{{ route('employer.', ['jobId'=>$this->jobId, 'jobApplicationId'=>$row->id]) }}"
                       class="rounded-md transition">{{ __('messages.job_stage.slots') }}</a>
                @endif
            @endif
            <a href="javascript:void(0)" class="rounded-md transition"
               data-id="{{ $row->id }}">{{ __('messages.common.delete') }}</a>
        </li>
    </ul>
</div>
