@extends('layouts.app')
@section('title')
    {{ __('messages.email_template.edit_email_template') }}
@endsection
@section('header_toolbar')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto fluid">
        <div class="d-md-flex items-center justify-between mb-5">
            <h1 class="mb-0">@yield('title')</h1>
            <div class="text-end mt-4 mt-md-0">
                <a href="{!! URL::previous() !!}" class="border border-gray-300 bg-transparent">{{ __('messages.common.back') }}</a>
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
                {{ Form::model($emailTemplate, ['route' => ['email.template.update', $emailTemplate->id], 'method' => 'put', 'id' => 'editEmailTemplateForm', 'files' => 'true']) }}
                <div class="section-body">
                    <div class="bg-white shadow rounded -lg overflow-hidden mt-2">
                        <div class="bg-white shadow rounded -lg overflow-hidden body">
                            <div class="flex flex-wrap">
                                <div class="flex-1 sm-12 mb-5">
                                    {{ Form::label('template_name',__('messages.email_template.template_name').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                    {{ Form::text('template_name', null, ['id'=>'editEmailTemplate','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm']) }}
                                </div>
                                <div class="flex-1 sm-12 mb-5">
                                    {{ Form::label('subject',__('messages.email_template.subject').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                    <span class="required"></span>
                                    {{ Form::text('subject', null, ['id'=>'editSubject','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500','placeholder' => __('messages.email_template.subject')]) }}
                                </div>
                                <div class="flex-1 sm-12 mb-5">
                                    {{ Form::label('body', __('messages.email_template.body').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                    <span class="required"></span>
                                    {{ Form::hidden('body', null, ['id' => 'editTemplateDescription']) }}
                                    <div id="emailTemplateEditBodyQuillData"> {!! $emailTemplate->body??null !!} </div>
                                </div>
                                <div class="flex-1 sm-12 mb-5">
                                    {{ Form::label('variables',__('messages.email_template.short_code').(':'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                    {{ Form::text('variables', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','readonly']) }}
                                </div>

                                <div class="flex justify-end mt-5">
                                    {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3']) }}
                                    <a href="{{ route('admin.dashboard') }}"
                                       class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
        {{ Form::hidden('emailBody',json_encode($emailTemplate->body),['id'=>'editEmailBody']) }}
    </div>
@endsection
