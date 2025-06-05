<div class="fixed inset-0 z-50 overflow-y-auto fade" tabindex="-1" role="dialog" id="showModal" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4" role="document">
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full">
            <div class="border border px-6 py-4 -b -gray-200">
                <h3 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.job.notes') }}</h3>
                <button type="button" aria-label="Close" class="transition duration-150 ease-in-out flex-1"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showForm']) }}
            <div class="px-6 py-4">
                <div class="flex-wrap flex details-page">
                    <div class="flex-1 sm-12">
                        <span id="showNote" class="fs-6 text-gray-600"></span>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
