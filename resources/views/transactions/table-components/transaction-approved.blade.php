@php
    $approved = __('messages.transaction.approved');
    $denied =  __('messages.transaction.denied');
    $selectManualPayment = __('messages.transaction.select_manual_payment');
@endphp

    @if ($row->is_approved == \App\Models\Transaction::PENDING && $row->status == \App\Models\Transaction::MANUALLY)
        <div class="flex items-center">
            <select class="w-full px-3 py-2 border border-gray-300 border border-gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 io-select2 approve-status transaction-approve"
                    data-id="{{ $row->id }}" data-control="select2">
                <option selected="selected" value="">{{ $selectManualPayment }}</option>
                <option value="{{ \App\Models\Transaction::APPROVED }}">{{ $approved }}</option>
                <option value="{{ \App\Models\Transaction::REJECTED }}">{{ $denied }}</option>
            </select>
        </div>
    @elseif ($row->is_approved == \App\Models\Transaction::APPROVED )
        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 success">{{ $approved }}</span>
    @elseif ($row->is_approved == \App\Models\Transaction::REJECTED )
        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 danger">{{ $denied }}</span>
    @else
        {{ __('messages.common.n/a') }}
    @endif
