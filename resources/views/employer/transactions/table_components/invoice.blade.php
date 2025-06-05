@if($row->invoice_id != null)
    <div class="flex justify-center">
        <a data-bs-toggle="tooltip" title="{{ __('messages.common.show') }}" class="rounded-md transition"view-invoice' : 'N/A' }}"
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
