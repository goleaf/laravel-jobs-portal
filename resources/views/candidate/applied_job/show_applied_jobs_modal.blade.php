<div class="modal fade" tabindex="-1" role="dialog" id="showModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.job.notes') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="modal-body">
                <div class="flex flex-wrap details-page">
                    <div class="flex-1 -sm-12">
                        <span id="showNote" class="fs-6 text-gray-600"></span>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
