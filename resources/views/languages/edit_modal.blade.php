<div id="editLanguageModal" class="fixed inset-0 z-10 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="w-full mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            {{ __('messages.language.edit_language') }}
                        </h3>
                        <div class="mt-4">
                            <form id="editLanguageForm">
                                <div id="editValidationErrorsBox" class="hidden p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg"></div>
                                
                                <input type="hidden" id="languageId" name="languageId">
                                
                                <div class="mb-4">
                                    <label for="editLanguage" class="block text-sm font-medium text-gray-700">
                                        {{ __('messages.language.language') }}
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="language" id="editLanguage" required 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                        placeholder="{{ __('messages.language.language') }}">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="editIso" class="block text-sm font-medium text-gray-700">
                                        {{ __('messages.language.iso_code') }}
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="iso_code" id="editIso" required 
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                        placeholder="{{ __('messages.language.iso_code') }}">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="btnEditSave" 
                    class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-primary-600 border border-transparent rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('messages.common.save') }}
                </button>
                <button type="button" id="btnEditCancel"
                    class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{ __('messages.common.cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>

