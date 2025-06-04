<div class="flex justify-center">
    <a href="{{ route('candidate.download-resume', $$row->id) }}" data-turbo="false" class="download-link px-4 py-2 rounded font-medium transition-colors px-2 text-primary-600 fs-3 ps-0"><i class="fas fa-download download-margin"></i></a>
    <button type="button" title="{{ __('messages.common.delete') }}" data-id="{{ $$row->id  }}"
            class="delete-resume px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0" data-bs-toggle="tooltip">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
