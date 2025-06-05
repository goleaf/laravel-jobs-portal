@extends('layouts.app')
@section('title')
    {{ __('messages.required_degree_levels') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            @include('flash::message')
                <livewire:degree-level-min-w-full divide-y divide-gray-200/>
        </div>
        @include('required_degree_levels.add_modal')
        @include('required_degree_levels.edit_modal')
    </div>
    {{ Form::hidden('requiredDegreeLevel',true,['id'=>'indexRequiredDegreeLevel']) }}
@endsection
