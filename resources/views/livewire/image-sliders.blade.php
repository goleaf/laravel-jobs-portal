<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @forelse($imageSliders as $imageSlider)
            @include('image_sliders.image_slider_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    {{ __('messages.image_slider.no_image_slider_available')  }}
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($imageSliders->count() > 0)
                    {{ $imageSliders->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
