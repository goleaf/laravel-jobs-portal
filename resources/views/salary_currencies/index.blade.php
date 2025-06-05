@extends('layouts.app')
@section('title')
    {{ __('messages.salary_currencies') }}
@endsection
@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        <div class="flex flex-col">
            @include('flash::message')
            <livewire:salary-currency-table/>
        </div>
    </div>
    @include('salary_currencies.add_modal')
    @include('salary_currencies.edit_modal')
@endsection
{{ --@push('scripts')-- }}
{{ --    <script src="{{mix('assets/js/salary_currencies/salary_currencies.js') }}"></script>--}}
{{ --@endpush-- }}
