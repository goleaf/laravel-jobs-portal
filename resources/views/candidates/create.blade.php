@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.new_candidate') }}
@endsection
@section('header_toolbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="mb-5 md:flex items-center justify-between">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="mt-4 text-end mt-md-0">
                <a href="{{ route('admin.candidates.index') }}" class="border border-gray-300 bg-transparent">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        <div class="flex-1 px-4 flex flex-">
            <div class="flex-wrap flex">
                <div class="flex-1 -12">
                    @include('layouts.errors')
                </div>
            </div>
            <div class="overflow-hidden shadow rounded bg-white -lg">
                <div class="overflow-hidden shadow rounded bg-white -lg body">
                    {{ Form::open(['route' => 'admin.candidates.store', 'id' => 'createCandidatesForm']) }}
                    @include('candidates.fields')
                    {{ Form::close() }}
                </div>
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
        {{ Form::hidden('companyStateUrl', route('states-list'), ['id' => 'companyStateUrl']) }}
        {{ Form::hidden('companyCityUrl', route('cities-list'), ['id' => 'companyCityUrl']) }}
        {{ Form::hidden('employerPanel',false,['class'=>'employerPanel']) }}
        {{ Form::hidden('isEdit', false, ['id' => 'isEdit','class'=>'isEdit']) }}
        {{ Form::hidden('createCompaniesForm', true, ['id' => 'createCompaniesForm']) }}
    </div>
@endsection
@push('scripts')
    
    {{-- <script src="{{mix('assets/js/custom/input_price_format.js') }}"></script> --}}
    {{-- <script src="{{mix('assets/js/candidate/create-edit.js') }}"></script> --}}
    {{-- <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script> --}}
@endpush

@push('scripts')
    @vite('resources/js/components/create.js')
@endpush
