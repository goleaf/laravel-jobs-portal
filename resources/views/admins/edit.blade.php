@extends('layouts.app')
@section('title')
    {{ __('messages.candidate.edit_admin')  }}
@endsection
@section('header_toolbar')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{{ route('admin.index')  }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -outline-primary">{{ __('messages.common.back')  }}</a>
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
                    {{ Form::model($user, ['route' => ['admin.update', $$user->id], 'method' => 'put', 'id' => 'editAdminForm', 'files' => 'true'])  }}

                    @include('admins.edit_fields')

                    {{ Form::close()  }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let isEdit = true;
        var phoneNo = "{{ old('region_code').old('phone')  }}";
    </script>
    {{ --    <script src="{{mix('assets/js/custom/input_price_format.js') }}"></script>--}}
    {{ --    <script src="{{mix('assets/js/candidate/create-edit.js') }}"></script>--}}
    {{ --    <script src="{{ mix('assets/js/custom/phone-number-country-code.js')  }}"></script>--}}
@endpush
