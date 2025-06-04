<div class="fixed inset-0 z-50 overflow-y-auto fade" id="cvModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 modal-xl" role="document">
        <div class="bg-white rounded-lg shadow-xl max-w-lg w-full resumes-width">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="modal-title">{{ __('messages.your_cv')  }}</h2>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <div class="px-6 py-4 mx-5 mx-xl-15 my-7 cv-download-content" id="cvTemplate">
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary me-3 printCV">{{ __('messages.common.print')  }}</button>
                <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary me-3"
                        id="downloadPDF">{{ __('messages.common.download').' '.__('messages.pdf')  }}</button>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary me-2"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel')  }}</button>
            </div>
        </div>
    </div>
</div>
