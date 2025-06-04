<div class="ms-auto" wire:ignore>
         <div class="dropdown flex items-center me-4 me-md-2">
             <button class="btn btn btn-icon px-4 py-2 rounded font-medium transition-colors -primary text-white dropdown-toggle hide-arrow ps-2 pe-0" type="button"
                 id="employeeFeaturedFilterBtn"data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                 <p class="text-center">
                     <i class='fas fa-filter'></i>
                 </p>
             </button>
             <div class="dropdown-menu py-0" aria-labelledby="employeeFeaturedFilterBtn">
                 <div class="text-start border-bottom py-4 px-7">
                     <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
                 </div>
                 <div class="p-5">
                     <div class="mb-5">
                         <label for="employeeFeatured" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.front_settings.featured_job') }}:</label>
                         {{ Form::select('featured_job',collect($filterHeads[0])->sortBy('key')->toArray(),null,['class' => 'form-select io-select2 abc', 'data-control' => 'select2', 'id' => 'jobFeatured']) }}
                     </div>
                     <div class="mb-5">
                           <label for="employeeFeatured" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.common.status') }}:</label>
                           {{ Form::select('status',collect($filterHeads[1])->sortBy('key')->toArray(),null,['class' => 'form-select io-select2 abc', 'data-control' => 'select2', 'id' => 'employeeJobStatus']) }}
                       </div>
                     <div class="flex justify-content-end">
                         <button type="reset" class="btn px-4 py-2 rounded font-medium transition-colors -secondary"
                             id="employeeJob-ResetFilter">{{ __('messages.common.reset') }}</button>
                     </div>
                 </div>
             </div>
         </div>
     </div>
