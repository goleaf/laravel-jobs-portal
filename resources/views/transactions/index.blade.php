@extends('layouts.app')
@section('title')
    {{ __('messages.transactions') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:transaction-table/>
        </div>
    </div>
@endsection
{{-- @push('scripts') --}}
{{ -- <script src=" asset('js/currency.js') "></script> -- }}
{{ -- <script src="mix('assets/js/transactions/transactions.js') "></script> -- }}
{{-- @endpush --}}
