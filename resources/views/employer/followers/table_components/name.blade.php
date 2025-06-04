<div class="flex items-center">
    <a href="#" class="image image-mini image-circle me-3">
            <img src="{{ $$row->$user->avatar }}" alt="" class="user-img">
    </a>
    <div class="flex flex-col">
        <a href="{{ route('front.candidate.details', $$row->$user->$candidate->unique_id) }}" target="_blank"
           class="mb-1 show- px-4 py-2 rounded font-medium transition-colors text-decoration-none">{{ $$row->$user->full_name }}</a>
    </div>
</div>
