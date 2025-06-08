
@push('styles')
    @vite('resources/css/pages/index.css')
@endpush
@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.contact_us') }}
@endsection
@section('page_css')
    
@endsection
@section('content')
    <div class="Blog-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-gradient pt-15 pb-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 lg-6 text-center">
                        <div class="hero-content">
                            <h1 class="text-gray-600 mb-2">{{ __('web.contact_us') }}</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }}
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">
                                        {{ __('web.contact_us') }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!--start contact-us-section-->
        <section class="contact-us-section py-60 mb-5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
                <div class="contact-us bg-gray-100 br-10">
                    <div class="flex flex-wrap">
                        <div class="flex-1 lg-3 lg:block hidden text-end">
                            <div class="contact-img mt-5">
                                <img src="{{ asset('img_template/contact-page.png') }}">
                            </div>
                        </div>
                        <div class="flex-1 lg-9">
                            <div class="contact-form">
                                <div class="section-heading mb-40">
                                    <h2 class="fs-40 text-gray-600 fw-bold mb-0">
                                        {{ __('web.home_menu.contact_us') }}
                                    </h2>
                                </div>
                                @formOpen(['url' => route('front.home'), 'id' => 'contactForm'])
                                    @csrf
                                    <div class="flex flex-wrap">
                                        <div class="flex-1 md-6">
                                            <div class="mb-4 mb-4">
                                                {{ Form::label('name', __('web.common.name').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                                <span class="text-red-600">*</span>
                                                {{ Form::text('name', old('name'), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10', 'text-red-500', 'placeholder' => __('web.contact_menu.enter_your_name')]) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 md-6">
                                            <div class="mb-4 mb-4">
                                                {{ Form::label('email', __('web.common.email').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                                <span class="text-red-600">*</span>
                                                {{ Form::email('email', old('email'), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10', 'text-red-500', 'placeholder' => __('web.common.email')]) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 md-6">
                                            <div class="mb-4 mb-4">
                                                {{ Form::label('subject', __('web.contact_menu.subject').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                                <span class="text-red-600">*</span>
                                                {{ Form::text('subject', old('subject'), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10', 'text-red-500', 'placeholder' => __('web.contact_menu.subject')]) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 md-6">
                                            <div class="mb-4 mb-4">
                                                {{ Form::label('phone_no', __('web.web_contact.phone_number').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                                {{ Form::tel('phone_no', old('phone_no'), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10', 'placeholder' => __('web.web_contact.phone_number')]) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 -12">
                                            <div class="mb-4 mb-4">
                                                {{ Form::label('message', __('web.contact_menu.message').':', ['class' => 'fs-16 text-secondary mb-2']) }}
                                                <span class="text-red-600">*</span>
                                                {{ Form::textarea('message', old('message'), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm fs-14 text-gray br-10', 'rows' => 5, 'text-red-500', 'placeholder' => __('web.contact_menu.type_message')]) }}
                                            </div>
                                        </div>
                                        <div class="flex-1 -12 mb-4">
                                            <div class="g-recaptcha" data-sitekey="{{ config('app.google_recaptcha_site_key') }}"></div>
                                            <div id="g-recaptcha-error"></div>
                                        </div>
                                        <div class="flex-1 -12 text-center">
                                            {{ Form::submit(__('web.common.send_message'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors fs-14 py-3 px-5']) }}
                                        </div>
                                    </div>
                                @formClose()
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--end contact-us-section-->
    </div>
@endsection

{{-- @section('page_scripts') --}}
{{--  --}}

{{ -- -- CDN JS removed - now using local assets -- -- }}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/index.js')
@endpush
