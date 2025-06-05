<div class="flex items-center">
    <a>
        <div class="image image-circle image-mini me-3">
            <img src="{{ $row->$company->company_url }}" alt="" class="user-img">
        </div>
    </a>
    <div class="flex flex-col">
        <a href="javascript:void(0)" class="mb-1 text-decoration-none fs-6 show-employer-detail- px-4 py-2 rounded font-medium transition-colors" data-id="{{ $row->id }}">
            {{ $row->$company->$user->full_name }}
        </a>
    </div>
</div>
