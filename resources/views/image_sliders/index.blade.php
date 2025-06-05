@extends('layouts.app')
@section('title')
    {{ __('messages.image_sliders') }}
@endsection
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto fluid">
        @include('flash::message')
        <div class="flex-1 px-4 flex flex-">
            <div class="flex-1 lg-12">
                <div class="flex-wrap flex">
                    <div class="flex-1 lg-12">
                        <form method="post" id="searchIsActive" class="lg:flex m-6">
                            @csrf
                            <div class="mb-5 lg:w-6/12 px-2 flex-1 sm-12 flex items-center">
                                <div class="mb-0 flex items-center form-switch">
                                    <input class="flex items-center input isFullSlider" type="checkbox"
                                           name="is_active" {{ ($settings['is_full_slider'] == 1) ? 'checked' : '' }}>
                                </div>
                                <label class="mb-1 mb-0 mb-1 block text-sm font-medium text-gray-700 fs-5 text-gray-600 me-5">
                                    {{ __('messages.image_slider.slider') }}
                                    <span data-bs-toggle="tooltip"
                                          title="{{ __('messages.image_slider.slider_title') }}">
                                        <i class="ml-1 fas fa-question-circle"></i>
                                    </span>

                                </label>
                            </div>
                            <div class="mb-5 lg:w-6/12 px-2 flex-1 sm-12 flex items-center">
                                <div class="mb-0 flex items-center form-switch">
                                    <input class="flex items-center input isSliderActive" type="checkbox"
                                           name="is_active" {{ ($settings['is_slider_active'] == 1) ? 'checked' : '' }}>
                                </div>
                                <label class="mb-1 mb-0 mb-1 block text-sm font-medium text-gray-700 fs-5 text-gray-600 me-5">
                                    {{ __('messages.image_slider.slider_active') }}
                                    <span data-bs-toggle="tooltip"
                                          title="{{ __('messages.image_slider.slider_active_title') }}">
                                        <i class="ml-1 fas fa-question-circle"></i></span>
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 px-4 flex flex-">
            <livewire:image-slider-min-w-full divide-y divide-gray-200/>
        </div>

    </div>
    @include('image_sliders.add_modal')
    @include('image_sliders.edit_modal')
    @include('image_sliders.show_modal')
                
    {{ Form::hidden('default_document_imageUrl',asset('assets/img/infyom-logo.png'),['id' => 'defaultDocumentImageUrl']) }}
    {{ Form::hidden('view',__('messages.common.view'), ['id' => 'view']) }}
    {{ Form::hidden('header-size-message',__('messages.image_slider.image_size_message'),['id' => 'imageSizeMessage']) }}
    {{ Form::hidden('header-extension-message',__('messages.image_slider.image_extension_message'),['id' => 'imageExtensionMessage']) }}
@endsection
{{-- @push('scripts') --}}
{{-- <script src="{{mix('assets/js/image_slider/image_slider.js') }}"></script> --}}
{{-- @endpush --}}
