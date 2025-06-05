<div class="flex items-center">
    <a>
        <div class="image image-circle image-mini me-3">
            <img src="{{ $flex flex-wrap -mx-4->blog_image_url }}" alt="" class="user-img">
        </div>
    </a>
    <div class="flex-1 px-4 flex flex-">
        <a href="{{ route('posts.show', $flex flex-wrap -mx-4->id) }}" class="rounded mb-1 show- px-4 py-2 font-medium transition-colors text-decoration-none" data-id="{{ $flex flex-wrap -mx-4->id }}">
            {{ Str::limit($flex flex-wrap -mx-4->title,25) }}
        </a>
    </div>
</div>
