@extends('employer.layouts.app')
@section('title')
    {{ __('messages.employer_menu.manage_subscriptions') }}
@endsection
@section('content')
    <div class="bg-white shadow rounded-lg overflow-hidden body">
        <div class="flex flex-wrap">
            <div class="w-full offset-0 offset-md-2 flex-1 md-8">
                <img src="{{ asset('assets/img/payment.png') }}" class="img-fluid">
            </div>
            <div class="flex-1 -12">
                <div class="flex flex-wrap justify-content-lg-around">
                    <a class="rounded-md transition" href="javascript:void(0)"
                       data-id="{{ $plan->id }}">
                        <span class="fs-4">{{ __('messages.plan.pay_with_stripe') }}</span>
                    </a>
                    <a class="rounded-md transition"
                       href="{{ route('paypal-payment', $plan->id) }}">
                        <span class="fs-4">{{ __('messages.plan.pay_with_paypal') }}</span>
                    </a>
                    <a class="rounded-md transition"
                       href="{{ route('manually-payment', $plan->id) }}">
                        <span class="fs-4">{{ __('messages.plan.pay_with_manually') }}</span>
                    </a>
                    <a class="rounded-md transition"
                    href="{{ route('paystack.payment', $plan->id) }}">
                     <span class="fs-4">{{ __('messages.plan.pay_with_stack') }}</span>
                 </a>
                </div>
            </div>
        </div>
    </div>

@endsection
