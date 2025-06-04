@extends('layouts.app')
@section('title')
    {{ __('messages.company.reported_employers')  }}
@endsection
@push('css')
{{ --    <link rel="stylesheet" href="{{ asset('css/header-padding.css')  }}">--}}
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:reported-employer-table />
        </div>
    </div>
    @include('employer.companies.reported_companies_show_modal')
@endsection

