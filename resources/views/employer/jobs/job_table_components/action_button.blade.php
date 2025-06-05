<div class="flex justify-center">
    <?php
    $isJobClosed = false;
    $isJobPause = false;
    $isJobDraft = false;

    if ($row->status == 2) {
        $isJobClosed = true;
    }
    if ($row->status == 3) {
        $isJobPause = true;
    }
    if ($row->status == 0) {
        $isJobDraft = true;
    }
    ?>
    @if(!$isJobClosed)
        @if(!$isJobPause && !$isJobDraft)
            <a data-turbo="false" href="{{ route('admin.', $row->id) }}" title="{{ __('messages.job_applications') }}"
               class="px-4 py-2 rounded font-medium transition-colors px-2 text-blue-500 fs-3 pe-0" data-bs-toggle="tooltip"
               data-placement="bottom">
        <span class="svg-icon svg-icon-3">
            <i class="fa fa-users"></i>
        </span>
            </a>
            @endif
        <a href="{{ route('admin.', $row->id) }}" title="{{ __('messages.common.edit') }}"
           class="rounded-md transition" data-bs-toggle="tooltip"
           data-placement="bottom">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
        @endif

        <a title="{{ (__('messages.tooltip.copy_preview_link')) }}"
           class="rounded-md transition"
           data-job-id="{{ $row->job_id }}" data-bs-toggle="tooltip" data-placement="bottom">
        <span class="svg-icon svg-icon-3">
          <i class="fa fa-copy"></i>
        </span>
    </a>
        <a title="{{ __('messages.common.delete') }}" data-id="{{ $row->id }}"
           class="employer-job-delete-inline-flex items-center px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" data-bs-toggle="tooltip">
            <i class="fa-solid fa-trash"></i>
        </a>
</div>
