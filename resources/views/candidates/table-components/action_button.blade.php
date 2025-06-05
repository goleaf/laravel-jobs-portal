<div class="flex justify-center">
    <a href="{{ route('admin.candidates.edit', $row->id) }}" 
       title="{{ __('messages.common.edit') }}"
       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out mr-2" 
       data-bs-toggle="tooltip">
        <i class="fas fa-edit mr-1"></i>
        {{ __('messages.common.edit') }}
    </a>
    
    <a href="{{ route('admin.candidates.show', $row->id) }}" 
       title="{{ __('messages.common.view') }}"
       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
       data-bs-toggle="tooltip">
        <i class="fas fa-eye mr-1"></i>
        {{ __('messages.common.view') }}
    </a>
</div>
