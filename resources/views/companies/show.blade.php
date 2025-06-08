@extends('layouts.app')
@section('title')
    {{ __('messages.company.employer_details') }}
@endsection
@section('header_toolbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a  href="{{ route('company.edit',$company->id) }}" class="border border-gray-300 bg-transparent">{{ __('messages.common.edit') }}</a>
                <a href="{!! URL::previous() !!}" class="border border-gray-300 bg-transparent">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    <div class="flex flex-wrap">
                        @include('companies.show_fields')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
