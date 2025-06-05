@extends('employer.layouts.app')
@section('title')
    {{ __('messages.company.edit_employer') }}
@endsection
@push('css')
    {{-- <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/> --}}
    {{-- <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/> --}}
    <link rel="stylesheet" href="{{ asset('assets/css/inttel/css/intlTelInput.css') }}">
@endpush
@section('content')
    <div class="flex flex- flex-1">
        <div class="flex flex-wrap">
            <div class="flex-1 -12">
                @include('layouts.errors')
                @include('flash::message')
                <div class="px-4 py-3 rounded-md border border border border-gray-300 -gray-300 -gray-300 mb-4 p-4 rounded -md mb-4 danger  hide hidden" id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
            </div>
        </div>
        <div class="bg-white shadow rounded -lg overflow-hidden">
            <div class="bg-white shadow rounded -lg overflow-hidden body">
                {{ Form::model($user, ['route' => ['company.update.form', $company->id], 'method' => 'put','id'=>'editCompanyForm']) }}
                @if($isFeaturedEnable)
                    <div class="flex justify-end">
                        @if($company->activeFeatured)
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium -info inline-block rounded">
                                {{ __('messages.front_settings.featured') }}
                                {{ __('messages.front_settings.exipre_on') }}
                                {{ (new Carbon\Carbon($company->activeFeatured->end_time))->format('d/m/y') }}</div>
                        @else
                            @if($isFeaturedAvilabal)
                                <a class="border border-gray-300 bg-transparent"
                                   id="makeFeatured">{{ __('messages.front_settings.make_featured') }}</a>
                                {{-- @else --}}
                                {{-- <button class="border border-gray-300 bg-transparent" data-bs-toggle="tooltip" --}}
                                {{-- data-bs-placement="bottom" --}}
                                {{-- title="{{ __('messages.front_settings.featured_employer_not_available') }}"> --}}
                                {{-- {{ __('messages.front_settings.make_featured') }}</button> --}}
                            @endif
                        @endif
                    </div>
                @endif
                @include('employer.companies.edit_fields')
                {{ Form::close() }}
                {{ Form::hidden('countryId',$company->$user->country_id,['id' => 'countryId']) }}
                {{ Form::hidden('stateId',$company->$user->state_id,['id' => 'stateId']) }}
                {{ Form::hidden('cityId',$company->$user->city_id,['id' => 'cityId']) }}
                {{ Form::hidden('companyId',$company->id,['id' => 'employerCompanyId']) }}
                    {{ Form::hidden('employerPanel',true,['class'=>'employerPanel']) }}
                    {{ Form::hidden('isEdit', true, ['class' => 'isEdit']) }}

                </div>
            </div>
        </div>
@endsection

@push('scripts')
{{-- <script src="https://js.stripe.com/v3/"></script> --}}
    
    {{-- <script src="{{mix('assets/js/companies/create-edit.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/companies/companies_stripe_payment.js') }}"></script> --}}
    {{-- <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script> --}}
@endpush

@push('scripts')
    @vite('resources/js/components/edit.js')
@endpush
