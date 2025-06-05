<div class="fixed inset-0 z-50 overflow-y-auto fade" id="cvModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 fixed inset-0 z-50 overflow-y-auto-xl" role="document">
        <div class="shadow rounded bg-white -lg -xl max-w-lg w-full resumes-width">
            <div class="border border px-6 py-4 -b -gray-200">
                <h2 class="fixed inset-0 z-50 overflow-y-auto -title">{{ __('messages.your_cv') }}</h2>
                <button type="button" class="transition duration-150 ease-in-out flex-1" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="px-6 py-4 mx-5 mx-xl-15 my-7 cv-download-content" id="cvTemplate">
            </div>
            <div class="border border px-6 py-4 -t -gray-200 flex justify-end space-x-2">
                <button type="button" class="border border-gray-300 bg-transparent">{{ __('messages.common.print') }}</button>
                <button class="border border-gray-300 bg-transparent"
                        id="downloadPDF">{{ __('messages.common.download').' '.__('messages.pdf') }}</button>
                <button type="button" class="border border-gray-300 bg-transparent"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
        </div>
    </div>
</div>
