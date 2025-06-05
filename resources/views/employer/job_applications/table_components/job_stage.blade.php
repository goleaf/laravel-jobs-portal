@if(!empty($row->job_stage_id))
    {{ $row->jobStage->name }}
@else
    <i class="font-20 fas fa-times-circle text-red-600"></i>
@endif
