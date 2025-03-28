<div class="ms-auto">
    <div class="dropdown d-flex align-items-center me-4 me-md-2">
        <button class="btn btn btn-icon btn-primary text-white dropdown-toggle hide-arrow ps-2 pe-0" type="button"
            id="candidateFilterBtn" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
            <p class="text-center">
                <i class='fas fa-filter'></i>
            </p>
        </button>
        <div class="dropdown-menu py-0" aria-labelledby="candidateFilterBtn">
            <div class="text-start border-bottom py-4 px-7">
                <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
            </div>
            <div class="p-5">
                <div class="mb-5">
                    <label for="statusFilter" class="form-label">{{ __('messages.common.status') }}:</label>
                    <select id="statusFilter" class="form-select" wire:change="changeStatusFilter($event.target.value)">
                        <option value="2">{{ __('messages.common.all') }}</option>
                        <option value="1">{{ __('messages.common.active') }}</option>
                        <option value="0">{{ __('messages.common.de_active') }}</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="immediateFilter" class="form-label">{{ __('messages.candidate.immediate_available') }}:</label>
                    <select id="immediateFilter" class="form-select" wire:change="changeImmediateFilter($event.target.value)">
                        <option value="2">{{ __('messages.common.all') }}</option>
                        <option value="1">{{ __('messages.candidate.immediate_available') }}</option>
                        <option value="0">{{ __('messages.candidate.not_immediate_available') }}</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" wire:click="$refresh">
                        {{ __('messages.common.reset') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
