@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.favourite_companies') }}
@endsection
@section('content')
    <div class="flex flex-column">
        <livewire:favourite-company-table/>
    </div>
@endsection
