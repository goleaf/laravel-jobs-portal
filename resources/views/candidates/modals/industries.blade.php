<div id="createCandidateIndustriesModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.industry.new_industry') }}</h3>
                <button type="button" aria-label="Close" class="transition duration-150 ease-in-out flex-1"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id'=>'createCandidateNewIndustryForm']) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger fs-4 text-white flex items-center hidden"
                     id="validationErrorsBox">
                    <i class="flex-wrap fa-solid fa-face-fflex -mx-4n me-5"></i>
                </div>
                <div class="mb-5">
                    {{ Form::label('name', __('messages.industry.name').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::text('name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder'=>__('messages.industry.name')]) }}
                </div>
                <div class="mb-5">
                    {{ Form::label('description', __('messages.industry.description').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{-- {{ Form::textarea('description', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid','text-red-500', 'id'=>'description']) }} --}}
                    <div id="addIndustryDescriptionQuillData"></div>
                    {{ Form::hidden('description', null, ['id' => 'industry_desc']) }}
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors m-0','id' => 'industriesSaveBtn','data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        id="maritalStatusBtnCancel"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
