<div id="candidateResumeModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3>{{ __('messages.candidate_profile.upload_resume') }}</h3>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            {{ Form::open(['id'=>'addCandidateResumeForm']) }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hide hidden" id="validationErrorsBox">
                    <i class='fa-solid fa-face-frown me-4'></i>
                </div>
                <div class="mb-5">
                    {{ Form::label('title',__('messages.candidate_profile.title').(':'), ['class' => 'form-label']) }}
                    <span class="required"></span>
                    {{ Form::text('title', null, [ 'id'=>"uploadResumeTitle",'class' => 'form-control','required','maxlength'=>'150','placeholder'=>__('messages.candidate_profile.title')]) }}
                </div>
                <div class="mb-5">
                    <div>
                        {{ Form::label('customFile',__('messages.common.choose_file').(':'), ['class' => 'form-label']) }}
                        <span class="required"></span>
                        <input type="file" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 custom-file-input" id="customFile" name="file" required>
                    </div>
                </div>
                <div>
                    {{ Form::label('is_default', __('messages.job_experience.is_default').':', ['class' => 'form-label']) }}
                    <br>
                    <div class="flex items-center form-switch">
                        <input class="flex items-center -input" name="is_default" type="checkbox"
                               value="1" id="default">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary m-0','id' => 'candidateSaveBtn','data-loading-text' => "<span class="spinner-border spinner-border-sm"></span> ".__('messages.common.process')]) }}
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary my-0 ms-5 me-0"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
{{ Form::close() }}

