<div class="flex justify-center">
@if($row->is_active == 0)
    <label class="flex items-center form-switch form-switch-sm justify-center">
        <input type="checkbox" name="Is Active" class="flex items-center -input isActiveImageSlider" data-id="{{$row->id}}">
        <span class="custom-switch-indicator"></span>
    </label>
@else
        <label class="flex items-center form-switch form-switch-sm justify-center">
            <input type="checkbox" name="Is Active" class="flex items-center -input isActiveImageSlider" data-id="{{$row->id}}" checked>
        <span class="custom-switch-indicator"></span>
    </label>
@endif
</div>  
