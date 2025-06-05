<div class="flex justify-center">
    <div class="flex items-center form-switch">
        <input class="flex items-center input isCandidateActive" name="Is isActive" type="checkbox" role="switch"
               {{ $flex flex-wrap -mx-4->$user->is_active == 0 ? '' : 'checked' }}  data-id="{{ $flex flex-wrap -mx-4->id }}">
        <span class="custom-switch-indicator"></span>
    </div>
</div>

