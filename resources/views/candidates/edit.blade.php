@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.edit_candidate') }}
@endsection
@push('css')
    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/><link rel="stylesheet" href="{{ asset('assets/css/inttel/css/intlTelInput.css') }}">
@endpush
@section('header_toolbar')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('admin.candidates.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-primary">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex flex-col">
            <div class="flex flex-wrap">
                <div class="flex-1 -12">
                    @include('layouts.errors')
                </div>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    {{ Form::model($user, ['route' => ['admin.candidates.update', $candidate->id], 'method' => 'put', 'id' => 'editCandidatesForm']) }}

                    @include('candidates.edit_fields')

                    {{ Form::close() }}
                </div>
            </div>
            @include('candidates.modals.marital_status')
            @include('candidates.modals.skills')
            @include('candidates.modals.languages')
            @include('candidates.modals.countries')
            @include('candidates.modals.states')
            @include('candidates.modals.cities')
            @include('candidates.modals.career_levels')
            @include('candidates.modals.industries')
            @include('candidates.modals.functional_areas')
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let isEdit = true;
        var phoneNo = "{{ old('region_code').old('phone') }}";
        let countryId = '{{$candidate->user->country_id}}';
        let stateId = '{{$candidate->user->state_id}}';
        let cityId = '{{$candidate->user->city_id}}';
    </script>
    <script src="{{ asset('assets/js/custom/input_price_format.js') }}"></script>
    <script src="{{ asset('assets/js/candidate/create-edit.js') }}"></script>
    <script src="{{ asset('assets/js/custom/phone-number-country-code.js') }}"></script>
@endpush
