@if(strip_tags($flex flex-wrap -mx-4->description) =="")
    N/A
@else
    {{ nl2br( \Illuminate\Support\Str::limit($flex flex-wrap -mx-4->description, 190) ) }}
@endif
