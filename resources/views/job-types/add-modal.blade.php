<div 
    x-data="{ show: false }" 
    x-show="show" 
    x-on:open-job-type-modal.window="show = true" 
    x-on:close-job-type-modal.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    class="fixed inset-0 z-50 overflow-y-auto" 
    style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div 
            x-show="show" 
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity" 
            aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal panel -->
        <div 
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            
            <form id="addJobTypeForm" class="bg-white">
                <!-- Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('messages.job_type.new_job_type') }}</h3>
                    <button 
                        type="button" 
                        @click="show = false" 
                        class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">{{ __('messages.common.close') }}</span>
                        <x-icons.x class="h-6 w-6" />
                    </button>
                </div>
                
                <!-- Body -->
                <div class="px-6 py-4">
                    <x-form id="addJobTypeForm" class="bg-white">
                        <x-input
                            name="name"
                            id="name"
                            label="{{ __('messages.job_type.name') }} <span class='text-red-500'>*</span>"
                            required="true"
                        />
                        <div id="nameError" class="text-red-500 text-sm mt-1 hidden"></div>
                        
                        <x-input
                            type="textarea"
                            name="description"
                            id="description"
                            label="{{ __('messages.job_type.description') }}"
                        />
                        <div id="descriptionError" class="text-red-500 text-sm mt-1 hidden"></div>
                        
                        <!-- Footer -->
                        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-2">
                            <x-button
                                type="button"
                                value="{{ __('messages.common.cancel') }}"
                                @click="show = false"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            />
                            <x-submit-button
                                value="{{ __('messages.common.save') }}"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                            />
                        </div>
                    </x-form>
                </div>
            </form>
        </div>
    </div>
</div> 