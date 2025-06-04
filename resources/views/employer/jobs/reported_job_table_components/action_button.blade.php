<div class="flex justify-center">
    <a href="javascript:void(0)" title="{{__('messages.common.show')}}" class="showReportedJobModal px-4 py-2 rounded font-medium transition-colors px-1 text-blue-500 fs-3"
       data-id={{ $row->id }} data-bs-toggle="tooltip">
        <i class="fas fa-eye fs-4"></i>
    </a>
    <button type="button" title="{{__('messages.common.delete')}}" data-id="{{ $row->id }}"
            class="reported-job-delete-btn px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" id="deleteUser" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
