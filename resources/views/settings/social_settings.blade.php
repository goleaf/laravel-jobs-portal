@extends('settings.index')
@section('title')
    {{ __('messages.setting.social_settings')  }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update','id'=>'editSocialSettingForm'])  }}
    {{ Form::hidden('sectionName', $sectionName)  }}
    <div class="flex flex-wrap mt-3">
        <div class="flex-1 -sm-6 mt-5">
            {{ Form::label('facebook_url', __('messages.setting.facebook_url').':', ['class' => 'form-label'])  }}
            <div class="flex">
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                    <i class="fab fa-facebook-f facebook-fa-icon text-primary-600"></i>
                </div>
                {{ Form::text('facebook_url', $setting['facebook_url'], ['class' => 'form-control','id'=>'facebookUrl','placeholder' => __('messages.setting.facebook_url')])  }}
            </div>
        </div>
        <div class="flex-1 -sm-6 mt-5">
            {{ Form::label('twitter_url', __('messages.setting.twitter_url').':', ['class' => 'form-label'])  }}
            <div class="flex">
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                    <i class="fab fa-twitter twitter-fa-icon text-primary-600"></i>
                </div>
                {{ Form::text('twitter_url', $setting['twitter_url'], ['class' => 'form-control','id'=>'twitterUrl', 'placeholder' => __('messages.setting.twitter_url')])  }}
            </div>
        </div>
        <div class="flex-1 -sm-6 mt-5">
            {{ Form::label('google_plus_url', __('messages.setting.google_plus_url').':', ['class' => 'form-label'])  }}
            <div class="flex">
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                    <i class="fab fa-google-plus-g google-plus-fa-icon text-red-600"></i>
                </div>
                {{ Form::text('google_plus_url', $setting['google_plus_url'], ['class' => 'form-control','id'=>'googlePlusUrl', 'placeholder' => __('messages.setting.google_plus_url')])  }}
            </div>
        </div>
        <div class="flex-1 -sm-6 mt-5">
            {{ Form::label('linkedIn_url', __('messages.setting.linkedIn_url').':', ['class' => 'form-label'])  }}
            <div class="flex">
                <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                    <i class="fab fa-linkedin-in linkedin-fa-icon text-primary-600"></i>
                </div>
                {{ Form::text('linkedIn_url', $setting['linkedIn_url'], ['class' => 'form-control','id'=>'linkedInUrl', 'placeholder' => __('messages.setting.linkedIn_url')])  }}
            </div>
        </div>
    </div>
    <div class="flex flex-wrap mt-4 mb-5">
        <div class="flex justify-end">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary me-3','id' => 'submitId'])  }}
            <a href="{{ route('admin.dashboard', ['section' => 'social_settings'])  }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary me-2">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
    {{ Form::close()  }}
@endsection
