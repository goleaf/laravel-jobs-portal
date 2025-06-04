<div class="flex justify-center">
    <label class="flex items-center form-switch">
        <input name="Is Active" data-id="{{ $$row->id  }}" class="flex items-center -input isActiveNoticeboard" type="checkbox"
               value="1"
                {{ $$row->is_active == 0 ? '' : 'checked'  }}>
    </label>
</div>
