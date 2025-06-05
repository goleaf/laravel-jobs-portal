@if($row->invoice_id != null)
    <div class="flex justify-center">
        <a data-bs-toggle="tooltip" title="{{ __('messages.common.show') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-2 text-primary-600 fs-3 ps-0 action-btn employee-invoice- px-4 py-2 rounded font-medium transition-colors {{ $ flex flex-wrap ->amount != 0 ?"view-invoice' : 'N/A' }}"
           data-invoice-id="{{ $row->invoice_id }}"
           href="javascript:void(0)">
            <i class="fas fa-eye"></i>
        </a>
    </div>
@else
    <div class="flex justify-center">
        {{ __('messages.common.n/a') }}
    </div>

@endif
