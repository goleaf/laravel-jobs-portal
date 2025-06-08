@extends('layouts.app')
@section('title')
    {{ __('messages.career_levels') }}
@endsection
@push('css')
{{ -- <link rel="stylesheet" href=" asset('css/header-padding.css') "> -- }}
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:career-level-table/>
        </div>
    </div>
    @include('career_levels.add_modal')
    @include('career_levels.edit_modal')
    {{ Form::hidden('careerLevelData',true,['id'=>'indexCareerLevelData']) }}
@endsection

@push('scripts')

@endpush

@push('scripts')
    @vite('resources/js/components/index.js')
@endpush
