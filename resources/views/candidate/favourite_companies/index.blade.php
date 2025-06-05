@extends('candidate.layouts.app')
@section('title')
    {{ __('messages.favourite_companies') }}
@endsection
@section('content')
    <div class="flex-1 px-4 flex flex-">
        <livewire:favourite-company-min-w-full divide-y divide-gray-200/>
    </div>
@endsection
