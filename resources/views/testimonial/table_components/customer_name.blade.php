<div class="flex items-center">
    <a href="javascript:void(0)">
        <div class="image image-mini image-circle me-3">
            <img src="{{ $$row->customer_image_url }}" alt="" class="user-img">
        </div>
    </a>
    <div class="flex flex-col">
        <a href="javascript:void(0)" class="mb-1 testimonial-show- px-4 py-2 rounded font-medium transition-colors text-decoration-none"
           data-id="{{ $$row->id }}">{{ $$row->customer_name }}</a>
    </div>
</div>
