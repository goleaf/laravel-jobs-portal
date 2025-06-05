<div class="pt-2 mt-4 flex-1 lg-4 mt-lg-0 mt-md-5 pt-md-0">
    <div class="flex-1 -12">
        <div class="flex-1 -12 mb-40">
            <div class="overflow-hidden shadow rounded-lg rounded bg-white overflow-hidden shadow bg-white job- -lg py-30">
                <div class="flex-wrap flex flex justify-content-lg-between">
                    <h5 class="mb-4 fs-18 text-gray-600">
                        {{ __('web.post_menu.categories') }}</h5>
                    @foreach($blogCategories as $blogCategory)
                        @if($blogCategory->post_assign_categories_count > 0)
                            <p>
                                <a class="fs-14 text-gray" href="{{ route('front.',$blogCategory->id) }}">
                                    {{ ($blogCategory->post_assign_categories_count > 0) ? html_entity_decode($blogCategory->name): '' }}
                                    {{ ($blogCategory->post_assign_categories_count > 0)? '('.$blogCategory->post_assign_categories_count.')':'' }}
                                </a>
                            </p>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="flex-1 -12">
        <div class="flex-1 -12 mb-40">
            <div class="overflow-hidden shadow rounded-lg rounded bg-white overflow-hidden shadow bg-white job- -lg py-30">
                <div class="flex-wrap flex flex justify-content-lg-between">
                    <h5 class="mb-4 fs-18 text-gray-600">
                        {{ __('web.web_blog.recent_posts') }}</h5>
                    @foreach($popularBlogs as $popularBlog)
                        <div class="recent-post flex {{ $loop->last?"':'mb-40' }}">
                            <div class="img">
                                <a href="{{ route('front.',$popularBlog->id) }}">
                                    <img src="{{ !empty($popularBlog->blog_image_url)?$popularBlog->blog_image_url:asset('assets/img/infyom-logo.png') }}" class="recent-post-img">
                                </a>
                            </div>
                            <div class="desc ms-4">
                                <p class="mb-0">
                                    <a href="{{ route('front.',$popularBlog->id) }}" class="fs-14 text-gray-600">
                                        {{ html_entity_decode($popularBlog->title) }}
                                    </a>
                                </p>
                                <span class="fs-14 text-gray">{{ \Carbon\Carbon::parse($popularBlog->created_at)->translatedFormat('M jS Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
