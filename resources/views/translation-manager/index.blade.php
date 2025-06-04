@extends('layouts.app')
@section('title')
    {{ __('messages.translation_manager') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            @include('error')
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    {{ Form::open(['route' => 'translation-manager.update','method'=>'post']) }}

                    {{Form::hidden('language-name',$selectedLang,['id' => 'languageName'])}}
                    {{Form::hidden('fileName',$selectedFile, ['id' => 'fileName'])}}

                    @include('translation-manager.fields')
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    @include('translation-manager.create')
@endsection
{{--@push('scripts')--}}
{{--    <script src="{{mix('assets/js/language_translate/language_translate.js')}}"></script>--}}
{{--@endpush--}}

