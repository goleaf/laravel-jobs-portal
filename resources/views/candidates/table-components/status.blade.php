<div class="flex justify-center">
    <div class="flex items-center form-switch">
        <input class="flex items-center -input isCandidateActive" name="Is isActive" type="checkbox" role="switch"
               {{ $$row->$user->is_active == 0 ? '' : 'checked' }}  data-id="{{ $$row->id }}">
        <span class="custom-switch-indicator"></span>
    </div>
</div>

