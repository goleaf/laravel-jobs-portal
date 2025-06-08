@extends('layouts.app')
@section('title')
    {{ __('messages.setting.notification_settings') }}
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
                    {{ Form::open(['route' => 'notification.settings.update']) }}
                    @include('notification_settings.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection

