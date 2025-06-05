<div class="ms-auto">
    <div class="relative inline-block text-left flex items-center me-4 me-md-2">
        <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out btn-icon px-4 py-2 rounded font-medium transition-colors primary text-white inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hide-arrow ps-2 pe-0" type="button"
            id="candidateFilterBtn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
            <p class="text-center">
                <i class='fas fa-filter'></i>
            </p>
        </button>
        <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="candidateFilterBtn">
            <div class="text-start border-bottom py-4 px-7">
                <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
            </div>
            <div class="p-5">
                <div class="mb-5">
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.common.status') }}:</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" wire:change="changeStatusFilter($event.target.value)">
                        <option value="2">{{ __('messages.common.all') }}</option>
                        <option value="1">{{ __('messages.common.active') }}</option>
                        <option value="0">{{ __('messages.common.de_active') }}</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="immediateFilter" class="block text-sm font-medium text-gray-700 mb-1">{{ __('messages.candidate.immediate_available') }}:</label>
                    <select id="immediateFilter" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" wire:change="changeImmediateFilter($event.target.value)">
                        <option value="2">{{ __('messages.common.all') }}</option>
                        <option value="1">{{ __('messages.candidate.immediate_available') }}</option>
                        <option value="0">{{ __('messages.candidate.not_immediate_available') }}</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary" wire:click="$refresh">
                        {{ __('messages.common.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
