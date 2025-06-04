@extends('settings.index')
@section('title')
    {{ __('messages.setting.about_us') }}
@endsection
@section('section')
    {{ Form::open(['route' => 'settings.update', 'id' => 'aboutUsForm']) }}
    {{ Form::hidden('sectionName', $sectionName) }}
    <div class="flex flex-wrap mt-3">
        <div class="flex-1 -sm-12 my-0">
            {{ Form::label('about_us', __('messages.about_us').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{--            {{ Form::textarea('about_us', $setting['about_us'], ['class' => 'form-control h-75', 'id' => 'aboutUs', 'rows' => '5']) }}--}}
            <div id="aboutUs"></div>
            {{ Form::hidden('about_us', $setting['about_us'], ['id' => 'aboutUsData']) }}
        </div>
    </div>
    <div class="mt-4 mb-5">
        <div class="flex justify-end">
            {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary me-3','id' => 'btnAboutUs']) }}
            <a href="{{ route('admin.dashboard', ['section' => 'about_us']) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary me-2">{{__('messages.common.cancel')}}</a>
        </div>
    </div>
    {{ Form::close() }}
@endsection
@push('scripts')
    <script>
        let aboutUsData = `{{$setting['about_us']}}`;
    </script>
@endpush
