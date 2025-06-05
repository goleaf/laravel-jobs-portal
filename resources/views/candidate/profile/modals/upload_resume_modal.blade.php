<div id="candidateResumeModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border border border-gray-300 -gray-300 px-6 py-4 -b -gray-200">
                <h3>{{ __('messages.candidate_profile.upload_resume') }}</h3>
                <button type="button" class="transition duration-150 ease-in-out flex-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id'=>'addCandidateResumeForm']) }}
            <div class="px-6 py-4">
                <div class="rounded border p-4 mb-4 rounded border mb-4 border border-gray-300 -gray-300 px-4 py-3 -md -gray-300 -md danger hide hidden" id="validationErrorsBox">
                    <i class='flex-wrap fa-solid fa-face-fflex -mx-4n me-4'></i>
                </div>
                <div class="mb-5">
                    {{ Form::label('title',__('messages.candidate_profile.title').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <span class="required"></span>
                    {{ Form::text('title', null, [ 'id'=>"uploadResumeTitle",'class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','maxlength'=>'150','placeholder'=>__('messages.candidate_profile.title')]) }}
                </div>
                <div class="mb-5">
                    <div>
                        {{ Form::label('customFile',__('messages.common.choose_file').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                        <span class="required"></span>
                        <input type="file" class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 custom-file-input" id="customFile" name="file" required>
                    </div>
                </div>
                <div>
                    {{ Form::label('is_default', __('messages.job_experience.is_default').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                    <br>
                    <div class="flex items-center form-switch">
                        <input class="flex items-center input" name="is_default" type="checkbox"
                               value="1" id="default">
                    </div>
                </div>
            </div>
            <div class="border pt-0 border border border-gray-300 -gray-300 px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors m-0','id' => 'candidateSaveBtn','data-loading-text' =>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')]) }}
                <button type="button" class="border border-gray-300 bg-transparent"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}

