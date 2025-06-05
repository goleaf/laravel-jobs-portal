<div id="flash-overlay-modal" class="fixed inset-0 z-50 overflow-y-auto fade {{ isset($modalClass) ? $modalClass :"' }}">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border border border-gray-300 -gray-300 -gray-200">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                <h4 class="fixed inset-0 z-50 overflow-y-auto -title">{{ $title }}</h4>
            </div>

            <div class="px-6 py-4">
                <p>{{ $$body }}</p>
            </div>

            <div class="px-6 py-4 border-t border border border-gray-300 -gray-300 -gray-200 flex justify-end space-x-2">
                <button type="button" class="border border-gray-300 bg-transparent" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
