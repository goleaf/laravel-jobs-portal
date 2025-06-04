<div id="showJobStageModal" class="fixed inset-0 z-50 overflow-y-auto fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <!-- Modal content-->
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3>{{ __('messages.job_stage.job_stage_detail')  }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm'])  }}
            <div class="px-6 py-4">
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hide hidden" id="maritalStatusValidationErrorsBox"></div>
                <div class="flex flex-wrap">
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('name',__('messages.job_tag.name').(':'), ['class' => 'pb-2 fs-5 text-gray-600'])  }}
                        <p id="showName" class="fs-5 text-gray-800"></p>
                    </div>
                    <div class="flex-1 -sm-12 mb-5">
                        {{ Form::label('description',__('messages.job_tag.description').(':'),['class' => 'pb-2 fs-5 text-gray-600'])  }}
                        <p id="showDescription" class="fs-5 text-gray-800"></p>
                    </div>

                </div>
            </div>
            {{ Form::close()  }}
        </div>
    </div>
</div>

