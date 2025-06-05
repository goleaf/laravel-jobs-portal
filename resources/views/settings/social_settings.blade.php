@extends('settings.index')
@section('title')
    {{ __('messages.setting.social_settings') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update','id'=>'editSocialSettingForm']) }}
    {{ Form::hidden('sectionName', $sectionName) }}
    <div class="flex-wrap mt-3 flex">
        <div class="mt-5 flex-1 sm-6">
            {{ Form::label('facebook_url', __('messages.setting.facebook_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <div class="flex">
                <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                    <i class="text-indigo-600 fab fa-facebook-f facebook-fa-icon -600"></i>
                </div>
                {{ Form::text('facebook_url', $setting['facebook_url'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'facebookUrl','placeholder' => __('messages.setting.facebook_url')]) }}
            </div>
        </div>
        <div class="mt-5 flex-1 sm-6">
            {{ Form::label('twitter_url', __('messages.setting.twitter_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <div class="flex">
                <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                    <i class="text-indigo-600 fab fa-twitter twitter-fa-icon -600"></i>
                </div>
                {{ Form::text('twitter_url', $setting['twitter_url'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'twitterUrl', 'placeholder' => __('messages.setting.twitter_url')]) }}
            </div>
        </div>
        <div class="mt-5 flex-1 sm-6">
            {{ Form::label('google_plus_url', __('messages.setting.google_plus_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <div class="flex">
                <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                    <i class="fab fa-google-plus-g google-plus-fa-icon text-red-600"></i>
                </div>
                {{ Form::text('google_plus_url', $setting['google_plus_url'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'googlePlusUrl', 'placeholder' => __('messages.setting.google_plus_url')]) }}
            </div>
        </div>
        <div class="mt-5 flex-1 sm-6">
            {{ Form::label('linkedIn_url', __('messages.setting.linkedIn_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <div class="flex">
                <div class="rounded border border border border border px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                    <i class="text-indigo-600 fab fa-linkedin-in linkedin-fa-icon -600"></i>
                </div>
                {{ Form::text('linkedIn_url', $setting['linkedIn_url'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'linkedInUrl', 'placeholder' => __('messages.setting.linkedIn_url')]) }}
            </div>
        </div>
    </div>
    <div class="flex-wrap mb-5 mt-4 flex">
        <div class="flex justify-end">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-flex-1 px-4ors me-3','id' => 'submitId']) }}
            <a href="{{ route('admin.dashboard', ['section' => 'social_settings']) }}"
               class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
    {{ Form::close() }}
@endsection
