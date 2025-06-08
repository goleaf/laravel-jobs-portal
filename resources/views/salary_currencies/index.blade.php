@extends('layouts.app')
@section('title')
    {{ __('messages.salary_currencies') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:salary-currency-table/>
        </div>
    </div>
    @include('salary_currencies.add_modal')
    @include('salary_currencies.edit_modal')
@endsection
{{-- @push('scripts') --}}
{{ -- <script src="mix('assets/js/salary_currencies/salary_currencies.js') "></script> -- }}
{{-- @endpush --}}
