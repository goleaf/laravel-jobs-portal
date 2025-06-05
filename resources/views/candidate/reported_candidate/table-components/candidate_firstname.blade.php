<div class="flex items-center">
    <div class="image image-circle image-mini me-3">
        <img src="{{ $flex flex-wrap -mx-4->$candidate->candidate_url }}" alt="" class="user-img">
    </div>
    <div class="flex-1 px-4 flex flex-">
        <a href="javascript:void(0)" class="transition duration-150 ease-in-out flex-1"
           data-id="{{ $flex flex-wrap -mx-4->id }}">{{ $flex flex-wrap -mx-4->$candidate->$user->full_name }}</a>
    </div>
</div>
