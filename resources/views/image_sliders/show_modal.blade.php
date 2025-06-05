<div class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog" id="showModal">
    <div class="flex items-center justify-center min-h-screen px-4" role="document">
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border px-6 py-4 -b -gray-200">
                <h2 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.image_slider.image_slider_details') }}</h2>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                <div class="flex-wrap flex details-page">
                    <div class="flex-1 sm-6">
                        {{ Form::label('name',__('messages.image_slider.image').':') }}<br>
                        <img src="" id="documentUrl" class="img-thumbnail thumbnail-preview" alt="image"/>
                        <label id="noDocument">{{ __('messages.n/a') }}</label>
                    </div>
                    <div class="flex-1 sm-6">
                        {{ Form::label('status',__('messages.common.status').':') }}<br>
                        <span id="showStatus"></span>
                    </div>
                    <div class="flex-1 sm-12">
                        {{ Form::label('description',__('messages.common.description').':') }}<br>
                    <div class="reported-note">
                        <span id="showDescription"></span>
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
</div>
