@extends('layouts.auth')
@section('title')
    {{ __('auth.admin_login')  }}
@endsection
@section('content')
    <!--begin::Main-->
    <div class="flex flex-col flex-column-fluid items-center justify-center p-0">
        <div class="flex-1 -12 text-center">
            <a href="{{ route('front.home')  }}" class="image mb-7 mb-sm-10" data-turbo="false">
                <img alt="Logo" src="{{ asset(getSettingValue('logo'))  }}" class="img-fluid logo-fix-size">
            </a>
        </div>
        <div class="width-540">
            @if(\Illuminate\Support\Facades\Session::has('status'))
                <p class="px-4 py-3 rounded-md border border-gray-300 mb-4 bg-green-50 text-green-800">{{ \Illuminate\Support\Facades\Session::get('status')  }}</p>
            @endif
            @include('flash::message')
            @include('layouts.errors')
        </div>
        <div class="bg-white rounded-lg shadow-md width-540 px-5 px-sm-7 py-10 mx-auto">
            <h1 class="text-center mb-7 text-2xl font-bold text-gray-900">{{ __('auth.admin_login')  }}</h1>
            <form method="POST" action="{{ route('login')  }}">
                @csrf
                <div class="mb-sm-7 mb-4">
                    <label for="formInputEmail" class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('auth.email')  }}:<span class="text-red-500">*</span>
                    </label>
                    <input class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                           id="formInputEmail" 
                           type="email" 
                           placeholder="{{ __('auth.enter_email')  }}" 
                           name="email" 
                           value="{{ old('email')  }}"
                           required 
                           autocomplete="off" 
                           autofocus>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message  }}</p>
                    @enderror
                </div>
                <div class="mb-sm-7 mb-4 relative">
                    <div class="flex justify-between">
                        <label for="formInputPassword" class="block text-sm font-medium text-gray-700 mb-1">{{ __('auth.password')  }}:<span class="text-red-500">*</span></label>
                        <a href="{{ route('password.request')  }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            {{ __('auth.forgot_password')  }}
                        </a>
                    </div>
                    <input type="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                           id="formInputPassword"
                           placeholder="{{ __('auth.enter_password')  }}" 
                           name="password" 
                           required 
                           autocomplete="off">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message  }}</p>
                    @enderror
                </div>
                <div class="mb-sm-7 mb-4 flex items-center">
                    <input type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                           id="formCheck" 
                           name="remember"
                           {{ (Cookie::get('remember') !== null) ? 'checked' : ''  }}>
                    <label class="ml-2 block text-sm text-gray-900" for="formCheck">{{ __('auth.remember_me')  }}</label>
                </div>
                <div class="w-full">
                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150 ease-in-out" 
                            data-turbo="false">{{ __('auth.login')  }}</button>
                </div>
            </form>
        </div>
    </div>
    <!--end::Main-->
@endsection
