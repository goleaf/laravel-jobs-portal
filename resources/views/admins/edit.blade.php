@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.edit_admin') }}
@endsection
@section('header_toolbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('admin.index') }}"
                   class="border border-gray-300 bg-transparent">{{ __('messages.common.back') }}</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="flex flex- flex-1">
            <div class="flex flex-wrap">
                <div class="flex-1 -12">
                    @include('layouts.errors')
                </div>
            </div>
            <div class="bg-white shadow rounded -lg overflow-hidden">
                <div class="bg-white shadow rounded -lg overflow-hidden body">
                    {{ Form::model($user, ['route' => ['admin.update', $user->id], 'method' => 'put', 'id' => 'editAdminForm', 'files' => 'true']) }}

                    @include('admins.edit_fields')

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    
    {{ -- <script src="mix('assets/js/custom/input_price_format.js') "></script> -- }}
    {{ -- <script src="mix('assets/js/candidate/create-edit.js') "></script> -- }}
    {{ -- <script src=" mix('assets/js/custom/phone-number-country-code.js') "></script> -- }}
@endpush

@push('scripts')
    @vite('resources/js/admin/edit.js')
@endpush
