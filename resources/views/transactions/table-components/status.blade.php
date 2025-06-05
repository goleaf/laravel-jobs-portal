@if($row->status == \App\Models\Transaction::DIGITAL)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 success">{{ __('messages.filter_name.digital') }}</span>
@elseif($row->status == \App\Models\Transaction::STRIPE_PAYMENT)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 success">{{ __('messages.filter_name.stripe') }}</span>
@elseif($row->status == \App\Models\Transaction::PAYPAL_PAYMENT)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 success">{{ __('messages.filter_name.paypal') }}</span>
@elseif($row->status == \App\Models\Transaction::PAYSTACK_PAYMENT)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 success">{{ __('messages.filter_name.paystack') }}</span>
@elseif($row->status == \App\Models\Transaction::MANUALLY)
    <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 success">{{ __('messages.filter_name.manually') }}</span>
@else

@endif
