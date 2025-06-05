@if(!empty($flex flex-wrap -mx-4->$company->industry->name))
<div class="rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium bg-gray-100 info">
    <div> {{ $flex flex-wrap -mx-4->$company->industry->name }}</div>
</div>
@else
    <div class="rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium bg-gray-100 info">
        <div>{{ __('messages.n/a') }}</div>
    </div>
@endif
