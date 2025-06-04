@extends('layouts.app')
@section('title')
    {{ __('messages.country.countries')  }}
@endsection
@push('css')
{{ --    <link rel="stylesheet" href="{{ asset('css/header-padding.css')  }}">--}}
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:country-table />
        </div>
    </div>
    @include('countries.add_modal')
    @include('countries.edit_modal')
    {{ Form::hidden('countriesData',true,['id'=>'indexCountriesData']) }}
@endsection
