<div class="flex justify-center">
    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" 
               name="Is Suspended" 
               class="sr-only peer isSuspended" 
               data-id="{{ $row->id }}" 
               {{ $row->is_suspended ? 'checked' : '' }}>
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
    </label>
</div>
