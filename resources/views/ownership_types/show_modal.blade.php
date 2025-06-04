<div class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog" id="showOwnershipModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4" role="document">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="modal-title">{{ __('messages.ownership_type.ownership_type_detail')  }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            <div class="px-6 py-4">
                    <div class="mb-5">
                        {{ Form::label('name',__('messages.common.name').':', ['class' => 'form-label'])  }}
                        <br>
                        <p id="showOwnershipName" class="text-gray-600"></p>
                    </div>
                    <div class="mb-5">
                        {{ Form::label('description',__('messages.common.description').':', ['class' => 'form-label'])  }}
                        <br>
                        <div class="reported-note">
                            <p id="showOwnershipDescription" class="text-gray-600"></p>
                        </div>
                    </div>
                </div>
            {{ Form::close()  }}
        </div>
    </div>
</div>
