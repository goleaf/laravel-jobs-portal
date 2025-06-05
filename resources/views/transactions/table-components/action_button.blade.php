@if($row->invoice_id != null)
    <div class="flex justify-center">
        <a class="rounded-md transition"admin-view-invoice' : 'N/A' }}"
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
