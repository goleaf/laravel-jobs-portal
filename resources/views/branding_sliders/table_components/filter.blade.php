<div class="ms-auto" wire:ignore>
         <div class="text-left relative inline-block flex items-center me-4 me-md-2">
             <button class="border border-gray-300 bg-transparent" type="button"
                 id="brandingSliderBtn"data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                 <p class="text-center">
                     <i class='fas fa-filter'></i>
                 </p>
             </button>
             <div class="shadow rounded mt-2 bg-white origin-top-right absolute right-0 w-56 -md -lg ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="brandingSliderBtn">
                 <div class="border text-start -bottom py-4 px-7">
                     <h3 class="mb-0 text-gray-900">{{ __('messages.common.filter_options') }}</h3>
                 </div>
                 <div class="p-5">
                       <div class="mb-5">
                                <label for="filterBtn" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.status') }}:</label>
                                {{ Form::select('status',collect($filterHeads[0])->sortBy('key')->toArray(),null,['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm io-select2', 'data-control' => 'select2', 'id' => 'brandingSlider']) }}
                       </div>
                     <div class="flex justify-end">
                         <button type="reset" class="border border-gray-300 bg-transparent"
                             id="brandingSlider-ResetFilter">{{ __('messages.common.reset') }}</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
