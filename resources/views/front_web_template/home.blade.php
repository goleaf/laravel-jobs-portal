<!-- Featured Job Categories -->
<section class="py-100 bg-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="section-heading text-center">
                    <h2 class="text-secondary fs-40 pb-3">{{ __('web.home.featured_categories') }}</h2>
                    <p class="text text-gray">{{ __('web.home.featured_categories_desc') }}</p>
                </div>
            </div>
        </div>
        
        <div class="featured-jobs-categories position-relative mt-60">
            <div class="row">
                @foreach($featuredCategories as $featuredCategory)
                    <div class="col-lg-3 col-md-6 px-xl-3 mb-40">
                        <div class="card py-30 px-30 h-100 border-0">
                            <div class="icon-box d-flex align-items-center justify-content-center mb-20">
                                <div class="icon-wrap bg-gray d-flex align-items-center justify-content-center br-5">
                                    @if($loop->index == 0)
                                        <x-icons.briefcase class="w-10 h-10 text-primary" />
                                    @elseif($loop->index == 1)
                                        <x-icons.graduation-cap class="w-10 h-10 text-primary" />
                                    @elseif($loop->index == 2)
                                        <x-icons.factory class="w-10 h-10 text-primary" />
                                    @elseif($loop->index == 3)
                                        <x-icons.badge class="w-10 h-10 text-primary" />
                                    @elseif($loop->index == 4)
                                        <x-icons.money class="w-10 h-10 text-primary" />
                                    @else
                                        <x-icons.puzzle class="w-10 h-10 text-primary" />
                                    @endif
                                </div>
                            </div>
                            <div class="card-body p-0 text-center">
                                <a href="{{ route('front.search.jobs', array('categories' => $featuredCategory->id)) }}"
                                   class="text-secondary fw-medium fs-20 mb-2 d-block">{{ html_entity_decode($featuredCategory->name) }}</a>
                                <p class="fs-14 text-gray mb-0">{{ $featuredCategory->jobs_count.' '.($featuredCategory->jobs_count > 1 ? __('web.common.open_positions') : __('web.common.open_position')) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section> 