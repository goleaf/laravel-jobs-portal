@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.reported_candidates') }}
@endsection
@push('css')
{{ --    <link rel="stylesheet" href="{{ asset('css/header-padding.css') }}">--}}
@endpush
@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        <div class="flex flex-col">
            <livewire:reported-candidate-table/>
        </div>
    </div>
    @include('candidate.reported_candidate.reported_candidate_show_modal')
@endsection

