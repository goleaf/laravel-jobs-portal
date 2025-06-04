@extends('layouts.app')
@section('title')
    {{ __('messages.image_sliders') }}
@endsection
@section('content')
    <div class="container mx-auto -fluid">
        @include('flash::message')
        <div class="flex flex-column">
            <div class="flex-1 -lg-12">
                <div class="flex flex-wrap">
                    <div class="flex-1 -lg-12">
                        <form method="post" id="searchIsActive" class="d-lg-flex m-6">
                            @csrf
                            <div class="col-lg-6 flex-1 -sm-12 mb-5 flex items-center">
                                <div class="flex items-center form-switch mb-0">
                                    <input class="flex items-center -input isFullSlider" type="checkbox"
                                           name="is_active" {{ ($settings['is_full_slider'] == 1) ? 'checked' : '' }}>
                                </div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 fs-5 text-gray-600 me-5 mb-0 mb-1">
                                    {{ __('messages.image_slider.slider') }}
                                    <span data-bs-toggle="tooltip"
                                          title="{{ __('messages.image_slider.slider_title') }}">
                                        <i class="fas fa-question-circle ml-1"></i>
                                    </span>

                                </label>
                            </div>
                            <div class="col-lg-6 flex-1 -sm-12 mb-5 flex items-center">
                                <div class="flex items-center form-switch mb-0">
                                    <input class="flex items-center -input isSliderActive" type="checkbox"
                                           name="is_active" {{ ($settings['is_slider_active'] == 1) ? 'checked' : '' }}>
                                </div>
                                <label class="block text-sm font-medium text-gray-700 mb-1 fs-5 text-gray-600 me-5 mb-0 mb-1">
                                    {{ __('messages.image_slider.slider_active') }}
                                    <span data-bs-toggle="tooltip"
                                          title="{{ __('messages.image_slider.slider_active_title') }}">
                                        <i class="fas fa-question-circle ml-1"></i></span>
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-column">
            <livewire:image-slider-table/>
        </div>

    </div>
    @include('image_sliders.add_modal')
    @include('image_sliders.edit_modal')
    @include('image_sliders.show_modal')
                
    {{Form::hidden('default_document_imageUrl',asset('assets/img/infyom-logo.png'),['id' => 'defaultDocumentImageUrl'])}}
    {{Form::hidden('view',__('messages.common.view'), ['id' => 'view'])}}
    {{Form::hidden('header-size-message',__('messages.image_slider.image_size_message'),['id' => 'imageSizeMessage'])}}
    {{Form::hidden('header-extension-message',__('messages.image_slider.image_extension_message'),['id' => 'imageExtensionMessage'])}}
@endsection
{{--@push('scripts')--}}
{{--    <script src="{{mix('assets/js/image_slider/image_slider.js')}}"></script>--}}
{{--@endpush--}}
