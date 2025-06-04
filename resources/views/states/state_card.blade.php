<div class="col-xl-4 flex-1 -md-6 candidate- bg-white shadow rounded-lg overflow-hidden">
    <div class="hover-effect-border relative mb-5 border-hover-primary employee-border">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex-col">
                <div class="mb-auto w-full employee-data mt-4">
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.state.state_name')  }}:</label>
                        <label class="text-decoration-none text-color-gray">{{ $state->name  }}</label>
                    </div>
                    <div class="text-center">
                        <label class="employee-label">{{ __('messages.state.country_name')  }}:</label>
                        <label class="text-decoration-none text-color-gray">{{ $state->country->name  }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="employee-action- px-4 py-2 rounded font-medium transition-colors">
            <a title="{{ __('messages.common.edit')  }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-yellow-500 text-white hover:bg-yellow-600 action-btn edit-action-btn edit- px-4 py-2 rounded font-medium transition-colors" data-id="{{ $state->id  }}"
               href="#">
                <i class="fa fa-edit"></i>
            </a>
            <a title="{{ __('messages.common.delete')  }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-red-600 text-white hover:bg-red-700 action-btn delete-action-btn delete- px-4 py-2 rounded font-medium transition-colors" data-id="{{ $state->id  }}"
               href="#">
                <i class="fa fa-trash"></i>
            </a>
        </div>
    </div>
</div>
