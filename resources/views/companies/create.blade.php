@extends('layouts.app')
@section('title')
    {{ __('messages.company.new_employer')  }}
@endsection
@push('css')
{{ --    <link href="{{ asset('assets/css/summernote.min.css')  }}" rel="stylesheet" type="text/css"/>--}}
    {{ --    <link href="{{ asset('assets/css/select2.min.css')  }}" rel="stylesheet" type="text/css"/>--}}
{{ --    <link rel="stylesheet" href="{{ asset('assets/css/inttel/css/intlTelInput.css')  }}">--}}
@endpush
@section('header_toolbar')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('company.index')  }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-primary">{{ __('messages.common.back')  }}</a>
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
                    {{ Form::open(['route' => 'company.store', 'files' => 'true', 'id' => 'addCompanyForm'])  }}
                    @include('companies.fields')
                    {{ Form::close()  }}
                </div>
            </div>
        </div>
        @include('companies.modals.industries')
        @include('companies.modals.ownership_types')
        @include('companies.modals.countries')
        @include('companies.modals.states')
        @include('companies.modals.cities')
        @include('companies.modals.company_sizes')
        {{ Form::hidden('companyStateUrl', route('states-list'), ['id' => 'companyStateUrl']) }}
        {{ Form::hidden('companyCityUrl', route('cities-list'), ['id' => 'companyCityUrl']) }}
        {{ Form::hidden('employerPanel',false,['class'=>'employerPanel']) }}
        {{ Form::hidden('isEdit', false, ['id' => 'isEdit','class'=>'isEdit']) }}
        {{ Form::hidden('createCompaniesForm', true, ['id' => 'createCompaniesForm']) }}

    </div>
@endsection
@push('scripts')
    <script>
        var phoneNo = "{{ old('region_code').old('phone')  }}";
    </script>
@endpush
