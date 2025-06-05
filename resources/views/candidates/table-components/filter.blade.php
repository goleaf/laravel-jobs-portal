<div class="ms-auto">
    <div class="text-left relative inline-block flex items-center me-4 me-md-2">
        <button class="border border-gray-300 bg-transparent" type="button"
            id="candidateFilterBtn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
            <p class="text-center">
                <i class='fas fa-filter'></i>
            </p>
        </button>
        <div class="shadow rounded mt-2 bg-white origin-top-right absolute right-0 w-56 -md -lg ring-1 ring-black ring-opacity-5 z-50 py-0" aria-labelledby="candidateFilterBtn">
            <div class="border text-start -bottom py-4 px-7">
                <h3 class="mb-0 text-gray-900">{{ __('messages.common.filter_options') }}</h3>
            </div>
            <div class="p-5">
                <div class="mb-5">
                    <label for="statusFilter" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.common.status') }}:</label>
                    <select id="statusFilter" class="rounded border border border w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500" wire:change="changeStatusFilter($event.target.value)">
                        <option value="2">{{ __('messages.common.all') }}</option>
                        <option value="1">{{ __('messages.common.active') }}</option>
                        <option value="0">{{ __('messages.common.de_active') }}</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="immediateFilter" class="mb-1 block text-sm font-medium text-gray-700">{{ __('messages.candidate.immediate_available') }}:</label>
                    <select id="immediateFilter" class="rounded border border border w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500" wire:change="changeImmediateFilter($event.target.value)">
                        <option value="2">{{ __('messages.common.all') }}</option>
                        <option value="1">{{ __('messages.candidate.immediate_available') }}</option>
                        <option value="0">{{ __('messages.candidate.not_immediate_available') }}</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="button" class="border border-gray-300 bg-transparent" wire:click="$refresh">
                        {{ __('messages.common.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
