@extends('layouts.app')
@section('title')
    {{ __('messages.candidates') }}
@endsection
@push('css')
{{ -- <link rel="stylesheet" href=" asset('css/header-padding.css') "> -- }}
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            @include('flash::message')
            <livewire:candidate-min-w-full divide-y divide-gray-200/>
        </div>
    </div>
    {{ Form::hidden('candidateData',true,['id'=>'indexCandidateData']) }}
@endsection
