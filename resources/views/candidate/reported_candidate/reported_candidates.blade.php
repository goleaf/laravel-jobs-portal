@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.reported_candidates') }}
@endsection
@push('css')
{{ -- <link rel="stylesheet" href=" asset('css/header-padding.css') "> -- }}
@endpush
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            <livewire:reported-candidate-min-w-full divide-y divide-gray-200/>
        </div>
    </div>
    @include('candidate.reported_candidate.reported_candidate_show_modal')
@endsection

