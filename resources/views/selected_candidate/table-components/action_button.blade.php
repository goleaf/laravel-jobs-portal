<div class="flex justify-center">
    <a class="px-4 py-2 rounded font-medium transition-colors px-1 text-info fs-3" target="_blank" data-bs-toggle="tooltip" title="{{__('messages.common.show')}}"
       href="{{ route('front.job.details', $row->job->job_id) }}" data-turbo="false">
        <i class="fas fa-eye fs-4"></i>
    </a>
</div>
