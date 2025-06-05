@extends('employer.layouts.app')
@section('title')
    {{ __('messages.transactions') }}
@endsection
@section('content')
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:employer-transaction-table/>
        </div>
@endsection
