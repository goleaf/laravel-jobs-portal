@extends('layouts.app')
@section('title')
    {{ __('messages.job_tags') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">
@endpush
@section('content')
<div class="container mx-auto px-4 mx-auto fluid">
    <div class="flex flex-col">
        @include('flash::message')
        <livewire:job-tag-table />
    </div>
</div>

@include('job_tags.add_modal')
@include('job_tags.edit_modal')
@include('job_tags.show_modal')

{{ Form::hidden('indexJobTagData',true,['id'=>'indexJobTagData']) }}
    
@endsection
