<div class="flex items-center">
    <div class="image image-circle image-mini me-3">
        <img src="{{ $row->candidate->candidate_url }}" alt="" class="user-img">
    </div>
    <div class="flex flex-col">
        <a href="javascript:void(0)" class="mb-1 show-candidate-modal- px-4 py-2 rounded font-medium transition-colors text-decoration-none"
           data-id="{{ $row->id }}">{{ $row->candidate->user->full_name }}</a>
    </div>
</div>
