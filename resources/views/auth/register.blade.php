@extends('layouts.auth')

@section('title')
    {{ __('web.register') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="mx-auto" style="max-width: 500px;">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">{{ __('web.register') }}</h1>
                        </div>

                        {{ Aire::open()->route('register')->id('register-form') }}
                            @csrf
                            <div class="form-group mb-3">
                                {{ Aire::input('first_name', __('web.common.first_name'))
                                    ->required()
                                    ->placeholder(__('web.common.first_name'))
                                    ->value(old('first_name'))
                                    ->groupClass('form-group')
                                    ->class(['form-control', 'is-invalid' => $errors->has('first_name')])
                                }}
                                @if ($errors->has('first_name'))
                                    <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Aire::input('last_name', __('web.common.last_name'))
                                    ->required()
                                    ->placeholder(__('web.common.last_name'))
                                    ->value(old('last_name'))
                                    ->groupClass('form-group')
                                    ->class(['form-control', 'is-invalid' => $errors->has('last_name')])
                                }}
                                @if ($errors->has('last_name'))
                                    <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Aire::email('email', __('web.common.email'))
                                    ->required()
                                    ->placeholder(__('web.common.email'))
                                    ->value(old('email'))
                                    ->groupClass('form-group')
                                    ->class(['form-control', 'is-invalid' => $errors->has('email')])
                                }}
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Aire::password('password', __('web.common.password'))
                                    ->required()
                                    ->placeholder(__('web.common.password'))
                                    ->groupClass('form-group')
                                    ->class(['form-control', 'is-invalid' => $errors->has('password')])
                                }}
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                {{ Aire::password('password_confirmation', __('web.common.confirm_password'))
                                    ->required()
                                    ->placeholder(__('web.common.confirm_password'))
                                    ->groupClass('form-group')
                                    ->class('form-control')
                                }}
                            </div>

                            <div class="form-group mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label for="user_type">{{ __('web.common.user_type') }}<span class="text-danger">*</span></label>
                                </div>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        {{ Aire::radio('user_type', __('web.common.candidate'))
                                            ->id('candidate')
                                            ->value('1')
                                            ->checked(old('user_type') == '1')
                                            ->class('form-check-input')
                                        }}
                                    </div>
                                    <div class="form-check">
                                        {{ Aire::radio('user_type', __('web.common.employer'))
                                            ->id('employer')
                                            ->value('2')
                                            ->checked(old('user_type') == '2')
                                            ->class('form-check-input')
                                        }}
                                    </div>
                                </div>
                                @if ($errors->has('user_type'))
                                    <span class="text-danger">{{ $errors->first('user_type') }}</span>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    {{ Aire::checkbox('privacy_policy', __('web.agree_to_terms'))
                                        ->id('privacy_policy')
                                        ->value('1')
                                        ->checked(old('privacy_policy'))
                                        ->class('form-check-input')
                                    }}
                                    <span class="text-danger">*</span>
                                </div>
                                @if ($errors->has('privacy_policy'))
                                    <span class="text-danger">{{ $errors->first('privacy_policy') }}</span>
                                @endif
                            </div>

                            {{ Aire::submit(__('web.register'))->class('btn btn-primary btn-block') }}

                            <div class="text-center mt-3">
                                <span>{{ __('web.already_have_an_account') }}</span>
                                <a class="small" href="{{ route('login') }}">
                                    {{ __('web.sign_in') }}
                                </a>
                            </div>
                        {{ Aire::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
