<div class="flex justify-center">
    @if($$row->is_active == 0)
        <label class="flex items-center form-switch form-switch-sm justify-center">
            <input type="checkbox" name="Is isActive" class="flex items-center -input isActiveBrandingSlider" data-id="{{ $$row->id }}">
            <span class=""></span>
        </label>
    @else
        <label class="flex items-center form-switch form-switch-sm justify-center">
            <input type="checkbox" name="Is isActive" class="flex items-center -input isActiveBrandingSlider" data-id="{{ $$row->id }}"
                   checked>
            <span class=""></span>
        </label>
    @endif
</div>
