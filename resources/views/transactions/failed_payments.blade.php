@extends('employer.layouts.app')
@section('title')
    Payment Failed
@endsection 
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-danger">
                <div class="bg-white shadow rounded-lg overflow-hidden body pt-0">
                    <div class="flex flex-wrap">
                        <div class="flex-1 -12 flex justify-between flex-wrap">
                            <div class="flex-1 -12 text-red-600 m-2">
                                <div class="flex items-center">
                                    <h6 class="mb-0">{{ __('messages.flash.payment_failed_try_again') }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center">
                <a class="rounded-md transition" href="{{ route('subscription.index') }}">{{ __('messages.see_all_plans') }}</a>
            </div>
        </div>
    </section>
@endsection
