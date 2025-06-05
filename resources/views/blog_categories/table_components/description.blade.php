@if(is_null($flex flex-wrap -mx-4->description))
    {{ __('messages.n/a') }}
@else
    {{ nl2br( \Illuminate\Support\Str::limit($flex flex-wrap -mx-4->description,200) ) }}
@endif

