@extends('employer.layouts.app')
@section('title')
    {{ __('messages.company.edit_employer') }}
@endsection
@push('css')
    {{--    <link href="{{ asset('assets/css/summernote.min.css') }}" rel="stylesheet" type="text/css"/>--}}
    {{--    <link href="{{ asset('assets/css/select2.min.css') }}" rel="stylesheet" type="text/css"/>--}}
    <link rel="stylesheet" href="{{ asset('assets/css/inttel/css/intlTelInput.css') }}">
@endpush
@section('content')
    <div class="flex flex-col">
        <div class="flex flex-wrap">
            <div class="flex-1 -12">
                @include('layouts.errors')
                @include('flash::message')
                <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger  hide hidden" id="editValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
            </div>
        </div>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-white shadow rounded-lg overflow-hidden -body">
                {{ Form::model($user, ['route' => ['company.update.form', $company->id], 'method' => 'put','id'=>'editCompanyForm']) }}
                @if($isFeaturedEnable)
                    <div class="flex justify-end">
                        @if($company->activeFeatured)
                            <div class="badge badge-info inline-block rounded">
                                {{ __('messages.front_settings.featured') }}
                                {{ __('messages.front_settings.exipre_on') }}
                                {{ (new Carbon\Carbon($company->activeFeatured->end_time))->format('d/m/y') }}</div>
                        @else
                            @if($isFeaturedAvilabal)
                                <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded font-medium transition-colors -sm"
                                   id="makeFeatured">{{ __('messages.front_settings.make_featured') }}</a>
                                {{--                                @else--}}
                                {{--                                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-blue-500 text-white hover:bg-blue-600 px-4 py-2 rounded font-medium transition-colors -sm" data-bs-toggle="tooltip"--}}
                                {{--                                            data-bs-placement="bottom"--}}
                                {{--                                            title="{{ __('messages.front_settings.featured_employer_not_available') }}">--}}
                                {{--                                        {{ __('messages.front_settings.make_featured') }}</button>--}}
                            @endif
                        @endif
                    </div>
                @endif
                @include('employer.companies.edit_fields')
                {{ Form::close() }}
                {{ Form::hidden('countryId',$company->user->country_id,['id' => 'countryId']) }}
                {{ Form::hidden('stateId',$company->user->state_id,['id' => 'stateId']) }}
                {{ Form::hidden('cityId',$company->user->city_id,['id' => 'cityId']) }}
                {{ Form::hidden('companyId',$company->id,['id' => 'employerCompanyId']) }}
                    {{ Form::hidden('employerPanel',true,['class'=>'employerPanel'])}}
                    {{Form::hidden('isEdit', true, ['class' => 'isEdit'])}}

                </div>
            </div>
        </div>
@endsection

@push('scripts')
{{--    <script src="https://js.stripe.com/v3/"></script>--}}
    <script>
        var phoneNo = "{{ old('region_code').old('phone') }}";
    </script>
    {{--    <script src="{{mix('assets/js/companies/create-edit.js')}}"></script>--}}
    {{--    <script src="{{ asset('assets/js/companies/companies_stripe_payment.js') }}"></script>--}}
    {{--    <script src="{{ mix('assets/js/custom/phone-number-country-code.js') }}"></script>--}}
@endpush
