<div class="ms-auto" wire:ignore>
         <div class="relative inline-block text-left flex items-center me-4 me-md-2">
             <button class="rounded-md transition" type="button"
                 id="jobCategoryFilterBtn"data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                 <p class="text-center">
                     <i class='fas fa-filter'></i>
                 </p>
             </button>
             <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="jobCategoryFilterBtn">
                 <div class="text-start border-bottom py-4 px-7">
                     <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
                 </div>
                 <div class="p-5">
                       <div class="mb-5">
                                <label for="filterBtn" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.common.status') }}:</label>
                                {{ Form::select('status',collect($filterHeads[0])->sortBy('key')->toArray(),null,['class' => 'form-select io-select2 abc', 'data-control' => 'select2', 'id' => 'jobCategoryFilter']) }}
                       </div>
                     <div class="flex justify-end">
                         <button type="reset" class="rounded-md transition"
                             id="jobCategory-ResetFilter">{{ __('messages.common.reset') }}</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
