@extends('layouts.app')
@section('title')
    {{ __('messages.city.cities') }}
@endsection
@push('css')
{{-- <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}"> --}}
{{-- <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/> --}}
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:city-table/>
        </div>
    </div>
    @include('cities.add_modal')
    @include('cities.edit_modal')
    {{ Form::hidden('citiesData',true,['id'=>'indexCitiesData']) }}
@endsection

