@extends('layouts.app')
@section('title')
    {{ __('messages.salary_periods') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            @include('flash::message')
            <livewire:salary-period-table/>
        </div>
    </div>
    @include('salary_periods.add_modal')
    @include('salary_periods.edit_modal')
    @include('salary_periods.show_modal')
    {{ Form::hidden('salaryPeriodData',true,['id'=>'indexSalaryPeriodData']) }}
@endsection
{{-- @push('scripts') --}}
    {{ -- <script src="mix('assets/js/salary_periods/salary_periods.js') "></script> -- }}
{{-- @endpush --}}
