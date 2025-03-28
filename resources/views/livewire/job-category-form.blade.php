<div>
    @if($showModal)
        <div class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            
            <div class="relative bg-white rounded-lg max-w-md w-full mx-auto shadow-lg z-50">
                <!-- Header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $isEditing ? __('messages.common.edit') : __('messages.common.add') }} {{ __('messages.job_category.job_category') }}
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" wire:click="closeModal">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <!-- Body -->
                <div class="p-4">
                    <form wire:submit.prevent="save">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('messages.job_category.name') }}
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                wire:model="name" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('name') border-red-500 @enderror"
                                placeholder="{{ __('messages.job_category.name') }}"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('messages.common.image') }}
                            </label>
                            <div class="mt-1 flex items-center">
                                <input 
                                    type="file" 
                                    id="image" 
                                    wire:model="image" 
                                    class="hidden"
                                    accept="image/*"
                                >
                                <label for="image" class="cursor-pointer px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ __('messages.common.choose_file') }}
                                </label>
                                <span class="ml-2 text-sm text-gray-500">
                                    {{ $image ? $image->getClientOriginalName() : ($existingImage ? __('messages.common.change_image') : __('messages.common.no_file_chosen')) }}
                                </span>
                            </div>
                            
                            @if($image || $existingImage)
                                <div class="mt-2">
                                    <div class="relative h-24 w-24 overflow-hidden rounded-lg border border-gray-200">
                                        @if($image)
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="h-full w-full object-cover">
                                        @elseif($existingImage)
                                            <img src="{{ $existingImage }}" alt="{{ $name }}" class="h-full w-full object-cover">
                                        @endif
                                        <button 
                                            type="button" 
                                            class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-bl"
                                            wire:click="$set('{{ $image ? 'image' : 'existingImage' }}', null)"
                                        >
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="is_featured" 
                                    wire:model="is_featured" 
                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                >
                                <label for="is_featured" class="ml-2 block text-sm text-gray-900">
                                    {{ __('messages.job_category.is_featured') }}
                                </label>
                            </div>
                            @error('is_featured')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mt-5 flex justify-end space-x-3">
                            <button 
                                type="button" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                wire:click="closeModal"
                            >
                                {{ __('messages.common.cancel') }}
                            </button>
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                {{ $isEditing ? __('messages.common.update') : __('messages.common.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div> 