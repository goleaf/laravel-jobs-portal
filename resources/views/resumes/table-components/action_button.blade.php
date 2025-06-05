<div class="flex justify-center">
    <a href="{{ route('admin.', $row->id) }}" data-turbo="false" class="download-link"
       data-bs-toggle="tooltip" title={{ __('messages.common.download') }}>
        <i class="fas fa-download download-margin text-primary-600 fs-3"></i>
    </a>
</div>
