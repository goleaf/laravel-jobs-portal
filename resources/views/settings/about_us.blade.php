@extends('settings.index')
@section('title')
    {{ __('messages.setting.about_us') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update', 'id' => 'aboutUsForm']) }}
    {{ Form::hidden('sectionName', $sectionName) }}
    <div class="flex-wrap mt-3 flex">
        <div class="flex-1 sm-12 my-0">
            {{ Form::label('about_us', __('messages.about_us').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
            <span class="required"></span>
            {{-- {{ Form::textarea('about_us', $setting['about_us'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-75', 'id' => 'aboutUs', 'flex flex-wrap -mx-4s' => '5']) }} --}}
            <div id="aboutUs"></div>
            {{ Form::hidden('about_us', $setting['about_us'], ['id' => 'aboutUsData']) }}
        </div>
    </div>
    <div class="mb-5 mt-4">
        <div class="flex justify-end">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200AboutUs']) }}
            <a href="{{ route('admin.dashboard', ['section' => 'about_us']) }}"
               class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
        </div>
    </div>
    {{ Form::close() }}
@endsection
@push('scripts')
    
@endpush

@push('scripts')
    @vite('resources/js/components/about_us.js')
@endpush
