<div class="ms-auto" wire:ignore>
         <div class="relative inline-block text-left flex items-center me-4 me-md-2">
             <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out btn-icon px-4 py-2 rounded font-medium transition-colors -primary text-white inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hide-arrow ps-2 pe-0" type="button"
                 id="selectStateBtn"data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                 <p class="text-center">
                     <i class='fas fa-filter'></i>
                 </p>
             </button>
             <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="selectState">
                 <div class="text-start border-bottom py-4 px-7">
                     <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options')  }}</h3>
                 </div>
                 <div class="p-5">
                     <div class="mb-5">
                         <label for="selectCity" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.job.city')  }}:</label>
                         {{ Form::select('state',['0' => 'Select State'] + getStateFilter(), null, ['class' => 'form-select', 'id' => 'selectState', 'data-control' => 'select2'])  }}
                     </div>
                     <div class="flex justify-end">
                         <button type="reset" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary"
                             id="state-ResetFilter">{{ __('messages.common.reset')  }}</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
