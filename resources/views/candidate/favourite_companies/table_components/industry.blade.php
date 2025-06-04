@if(!empty($$row->$company->industry->name))
<div class="badge bg-gray-100 -info">
    <div> {{ $$row->$company->industry->name }}</div>
</div>
@else
    <div class="badge bg-gray-100 -info">
        <div>{{ __('messages.n/a')  }}</div>
    </div>
@endif
