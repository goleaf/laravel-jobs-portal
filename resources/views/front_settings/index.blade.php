@extends('layouts.app')
@section('title')
    {{ __('messages.setting.front_settings') }}
@endsection
@push('css')
    <link href="{{ asset('css/header-padding.css') }}" rel="stylesheet" type="text/css"/>
@endpush
@section('content')
    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger hidden" id="validationErrorsBox">
        <i class="fa-solid fa-face-frown me-5"></i>
    </div>
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            @include('layouts.errors')
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    {{ Form::open(['route' => 'front.settings.update','files' => true,]) }}
                    @include('front_settings.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
{{--@push('scripts')--}}
{{--    <script src="{{mix('assets/js/web/front_settings/front_settings.js')}}"></script>--}}
{{--@endpush--}}
