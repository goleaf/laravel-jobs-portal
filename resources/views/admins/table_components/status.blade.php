<div class="flex justify-start">
    <div class="flex items-center form-switch">
        <input class="flex items-center -input changeAdminStatus" wire:click="changeStatus({{ $$row->id }})" type="checkbox" role="switch"
              {{ $$row->is_active == 0 ? '' : 'checked' }} >
        <span class="custom-switch-indicator"></span>
    </div>
</div>
