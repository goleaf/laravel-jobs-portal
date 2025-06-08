@extends('layouts.app')
@section('title')
    {{ __('messages.setting.privacy_policy') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    @include('privacy_policy.privacy_policy')
                    {{-- @include('privacy_policy.terms_conditions') --}}
                </div>
            </div>
        </div>
    </div>
    {{ Form::hidden('termConditionData', $privacyPolicy['terms_conditions'], ['id' => 'termConditionData']) }}
    {{ Form::hidden('privacyPolicyData', $privacyPolicy['privacy_policy'], ['id' => 'privacyPolicyData']) }}
@endsection
@push('scripts')
{{ -- <script src=" mix('assets/js/privacy_policy/privacy_policy.js') "></script> -- }}
@endpush
