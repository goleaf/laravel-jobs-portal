@extends('layouts.auth')
@section('title')
    @lang('web.reset_password.forgot_password')
@endsection
@section('content')
<div class="flex flex-col flex-column-fluid items-center justify-center p-0">
    <div class="flex-1 -12 text-center">
        <a href="{{ route('front.home')  }}" class="image mb-7 mb-sm-10" data-turbo="false">
            <img alt="Logo" src="{{ asset(getSettingValue('logo'))  }}" class="img-fluid logo-fix-size">
        </a>
    </div>
    <div class="width-540">
        @if (session('status'))
            <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -success">
                {{ session('status')  }}
            </div>
        @endif
    </div>
    <div class="bg-theme-white rounded-15 shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
        <div class="text-center">
            <h1 class="text-center mb-7">@lang('web.reset_password.reset_password')</h1>
            <div class="mb-4">
                @lang('web.reset_password.email_to_reset_your_password')
            </div>
        </div>
        @formOpen(['url' => route('password.email'), 'method' => 'POST'])
            @csrf
            <div class="mb-sm-7 mb-4">
                {{ Form::label('email', __('web.common.email').':', ['class' => 'form-label'])  }}
                <span class="required"></span>
                {{ Form::email('email', old('email'), ['class' => 'form-control', 'placeholder' => __('web.reset_password.your_email'), 'required', 'autocomplete' => 'off'])  }}
            </div>

            <div class="flex justify-center">
                {{ Form::submit(__('web.reset_password.email_password_reset_link'), ['class' => 'btn btn-primary'])  }}
                <a href="{{ route('front.home')  }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary ms-3" data-turbo="false">@lang('web.reset_password.cancel')</a>
            </div>
        @formClose()
    </div>
</div>

@endsection
