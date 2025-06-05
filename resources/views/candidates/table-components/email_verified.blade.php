<div class="flex justify-center">
    @if(!$row->user->email_verified_at)
        <label class="flex items-center form-switch form-switch-sm justify-center">
            <input type="checkbox" 
                   name="email_verified" 
                   class="email-verified-toggle focus:ring-indigo-500 h-4 w-4 text-indigo-600 border border-gray-300 -gray-300 rounded" 
                   value="1" 
                   data-id="{{ $row->id }}" 
                   {{ ($row->user->email_verified_at) ? 'checked' : '' }}>
            <span class="ml-2 text-sm text-gray-700">{{ __('messages.common.verified') }}</span>
        </label>
    @else
        <div class="border border-gray-300 bg-transparent">
            <i class="fas fa-check-circle mr-1"></i>
            {{ __('messages.common.verified') }}
        </div>
    @endif
</div>
