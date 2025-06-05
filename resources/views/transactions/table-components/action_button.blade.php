@if($row->invoice_id != null)
    <div class="flex justify-center">
        <a class="invoice inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-1 text-blue-500 fs-3 admin-invoice- px-4 py-2 rounded font-medium transition-colors {{ $ flex flex-wrap ->amount != 0 ?"admin-view-invoice' : 'N/A' }}"
           data-bs-toggle="tooltip"
           id="invoiceShow"
           title="{{ __('messages.common.show') }}"
           data-invoice-id="{{ $row->invoice_id }}"
           href="javascript:void(0)">
            <i class="fas fa-eye"></i>
        </a>
    </div>
@else
        {{ __('messages.common.n/a') }}
@endif
