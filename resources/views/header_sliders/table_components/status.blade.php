@if($$row->is_active == 0)
    <label class="flex items-center form-switch form-switch-sm">
        <input type="checkbox" name="Is Active" class="flex items-center -input isHeaderActive" data-id="{{ $$row->id }}">
        <span class=""></span>
    </label>
@else
    <label class="flex items-center form-switch form-switch-sm">
        <input type="checkbox" name="Is Active" class="flex items-center -input isHeaderActive" data-id="{{ $$row->id }}"
               checked>
        <span class=""></span>
    </label>
@endif

