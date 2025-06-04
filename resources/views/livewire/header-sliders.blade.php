<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @forelse($headerSliders as $headerSlider)
            @include('header_sliders.header_sliders_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    {{ __('messages.header_slider.no_header_slider_available') }}
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-content-end flex-wrap">
                @if($headerSliders->count() > 0)
                    {{$headerSliders->links()}}
                @endif
            </div>
        </div>
    </div>
</div>
