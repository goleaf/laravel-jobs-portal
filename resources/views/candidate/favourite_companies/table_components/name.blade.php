<div class="flex items-center">
    <a>
        <div class="image image-circle image-mini me-3">
            <img src="{{ $flex flex-wrap -mx-4->$company->$user->avatar }}" alt="user" class="user-img">
        </div>
    </a>
    <div class="flex-1 px-4 flex flex-">
            <a class="mb-1 text-decoration-none fs-6">
            {{ $flex flex-wrap -mx-4->$company->$user->first_name }}
        </a>
        <span>{{ $flex flex-wrap -mx-4->$company->$user->email }}</span>
    </div>
</div>
