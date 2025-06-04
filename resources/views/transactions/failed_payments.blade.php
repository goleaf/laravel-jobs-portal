@extends('employer.layouts.app')
@section('title')
    Payment Failed
@endsection 
@section('content')
    <section class="section">
        <div class="section-body">
            <div class="bg-white shadow rounded-lg overflow-hidden shadow-danger">
                <div class="bg-white shadow rounded-lg overflow-hidden -body pt-0">
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
                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary" href="{{ route('manage-subscription.index') }}">{{ __('messages.see_all_plans') }}</a>
            </div>
        </div>
    </section>
@endsection
