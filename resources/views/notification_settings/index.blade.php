@extends('layouts.app')
@section('title')
    {{ __('messages.setting.notification_settings') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="container mx-auto -fluid">
        <div class="flex flex-column">
            @include('flash::message')
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    {{ Form::open(['route' => 'notification.settings.update']) }}
                    @include('notification_settings.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@endsection

