@extends('privacy_policy.index')
@section('title')
    {{ __('messages.setting.terms_conditions') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'privacy.policy.update', 'id' => 'termsConditions']) }}
    <div class="flex flex-wrap">
        <div class="flex-1 sm-12 my-0">
            {{ Form::label('terms_conditions', __('messages.setting.terms_conditions').':') }}<span
                    class="text-red-600">*</span>
            {{ Form::textarea('terms_conditions', $privacyPolicy['terms_conditions'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-75', 'id' => 'description']) }}
        </div>
    </div>
    <div class="flex flex-wrap mt-4">
        <div class="flex-1 sm-12">
            {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors']) }}
        </div>
    </div>
    {{ Form::close() }}
@endsection
