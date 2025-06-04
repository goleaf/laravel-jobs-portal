<div id="flash-overlay-modal" class="fixed inset-0 z-50 overflow-y-auto fade {{ isset($modalClass) ? $modalClass : "'  }}">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                <h4 class="modal-title">{{ $title  }}</h4>
            </div>

            <div class="px-6 py-4">
                <p>{!! $body !!}</p>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
