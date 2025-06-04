<div class="flex items-center">
    <a>
        <div class="image image-circle image-mini me-3 ">
            <img src="{{$row->blog_image_url}}" alt="" class="user-img">
        </div>
    </a>
    <div class="flex flex-column">
        <a href="{{route('posts.show', $row->id)}}" class="mb-1 show- px-4 py-2 rounded font-medium transition-colors text-decoration-none" data-id="{{$row->id}}">
            {{ Str::limit($row->title,25) }}
        </a>
    </div>
</div>
