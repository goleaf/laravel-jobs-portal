@extends('privacy_policy.index')
@section('title')
    {{ __('messages.setting.terms_conditions') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'privacy.policy.update', 'id' => 'termsConditions']) }}
    <div class="flex flex-wrap">
        <div class="flex-1 -sm-12 my-0">
            {{ Form::label('terms_conditions', __('messages.setting.terms_conditions').':') }}<span
                    class="text-red-600">*</span>
            {{ Form::textarea('terms_conditions', $privacyPolicy['terms_conditions'], ['class' => 'form-control h-75', 'id' => 'description']) }}
        </div>
    </div>
    <div class="flex flex-wrap mt-4">
        <div class="flex-1 -sm-12">
            {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary']) }}
        </div>
    </div>
    {{ Form::close() }}
@endsection
