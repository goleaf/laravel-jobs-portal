<!-- Featured Job Categories -->
<section class="py-100 bg-gray">
    <div class="container mx-auto px-4 mx-auto">
        <div class="flex flex-wrap justify-center">
            <div class="flex-1 -12">
                <div class="section-heading text-center">
                    <h2 class="text-gray-600 fs-40 pb-3">{{ __('web.home.featured_categories') }}</h2>
                    <p class="text text-gray">{{ __('web.home.featured_categories_desc') }}</p>
                </div>
            </div>
        </div>
        
        <div class="featured-jobs-categories relative mt-60">
            <div class="flex flex-wrap">
                @foreach($featuredCategories as $featuredCategory)
                    <div class="lg:w-3/12 px-2 flex-1 md-6 px-xl-3 mb-40">
                        <div class="bg-white shadow rounded-lg overflow-hidden py-30 px-30 h-full border-0">
                            <div class="icon-box flex items-center justify-center mb-20">
                                <div class="icon-wrap bg-gray flex items-center justify-center br-5">
                                    @if($loop->index == 0)
                                        <x-icons.briefcase class="w-10 h-10 text-primary-600" />
                                    @elseif($loop->index == 1)
                                        <x-icons.graduation-cap class="w-10 h-10 text-primary-600" />
                                    @elseif($loop->index == 2)
                                        <x-icons.factory class="w-10 h-10 text-primary-600" />
                                    @elseif($loop->index == 3)
                                        <x-icons.badge class="w-10 h-10 text-primary-600" />
                                    @elseif($loop->index == 4)
                                        <x-icons.money class="w-10 h-10 text-primary-600" />
                                    @else
                                        <x-icons.puzzle class="w-10 h-10 text-primary-600" />
                                    @endif
                                </div>
                            </div>
                            <div class="bg-white shadow rounded-lg overflow-hidden body p-0 text-center">
                                <a href="{{ route('front.search.jobs', array('categories' => $featuredCategory->id)) }}"
                                   class="text-gray-600 fw-medium fs-20 mb-2 block">{{ html_entity_decode($featuredCategory->name) }}</a>
                                <p class="fs-14 text-gray mb-0">{{ $featuredCategory->jobs_count.' '.($featuredCategory->jobs_count > 1 ? __('web.common.open_positions') : __('web.common.open_position')) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section> 