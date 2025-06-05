<div class="flex items-center">
    <a href="{{ route('admin.candidates.show', $row->id) }}">
        <div class="image image-circle image-mini me-3">
            <img src="{{ $row->user->profile_image_url }}" alt="user" class="object-cover w-12 h-12 rounded-full">
        </div>
    </a>
    <div class="flex flex-col">
        <a href="{{ route('admin.candidates.show', $row->id) }}"
           class="mb-1 text-decoration-none fs-6">
            <span class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $row->user->full_name }}</span>
        </a>
        <span class="text-sm text-gray-500">{{ $row->user->email }}</span>
    </div>
</div>
