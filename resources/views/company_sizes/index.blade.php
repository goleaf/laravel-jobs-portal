@extends('layouts.app')
@section('title')
    {{ __('messages.company_sizes') }}
@endsection
@push('css')
{{-- <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/> --}}
{{-- <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}"> --}}
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:company-size-table/>
        </div>
    </div>
    @include('company_sizes.add_modal')
    @include('company_sizes.edit_modal')
    {{ Form::hidden('companySizeData',url('update-language'),['id'=>'indexCompanySizeData']) }}
@endsection
