@extends('layouts.app')
@section('title')
    {{ __('messages.transactions') }}
@endsection
@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:transaction-table/>
        </div>
    </div>
@endsection
{{ --@push('scripts')-- }}
{{ --    <script src="{{ asset('js/currency.js') }}"></script>--}}
{{ --    <script src="{{mix('assets/js/transactions/transactions.js') }}"></script>--}}
{{ --@endpush-- }}
