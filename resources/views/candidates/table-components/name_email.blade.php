<div class="flex items-center">
    <a href="{{ route('admin.candidates.show', $row->id) }}">
        <div class="image image-circle image-mini me-3">
            <img src="{{ $row->candidate_url }}" alt="" class="user-img">
        </div>
    </a>
    <div class="flex flex-col">
        <a href="{{ route('admin.candidates.show', $row->id) }}"
           class="mb-1 text-decoration-none fs-6">
            {{ $row->user->full_name }}
        </a>
        <span class="fs-6">{{ $row->user->email }}</span>
    </div>
</div>
