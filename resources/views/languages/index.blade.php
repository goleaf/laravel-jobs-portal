@extends('layouts.app')
@section('title')
    {{ __('messages.language.languages') }}
@endsection
@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col">
            @include('flash::message')
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <livewire:language-table />
            </div>
        </div>
    </div>
    @include('languages.add_modal')
    @include('languages.edit_modal')
    <input type="hidden" id="indexSetLanguageData" value="true">
@endsection
{{--@push('scripts')--}}
{{--    <script src="{{mix('assets/js/languages/languages.js')}}"></script>--}}
{{--@endpush--}}
