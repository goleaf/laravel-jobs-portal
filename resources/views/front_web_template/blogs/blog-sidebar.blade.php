<div class="flex-1 lg-4">
    <div class="Categories br-10 px-40 bg-gray-100 mb-40">
        <h5 class="fs-18 text-gray-600 mb-4">{{ __('web.post_menu.categories') }}</h5>
        @foreach ($blogCategories as $blogCategory)
            @if ($blogCategory->post_assign_categories_count > 0)
                <p>
                    <a class="fs-14 text-gray" href="{{ route('front.', $blogCategory->id) }}">
                        {{ $blogCategory->post_assign_categories_count > 0 ? html_entity_decode($blogCategory->name) : '' }}
                        {{ $blogCategory->post_assign_categories_count > 0 ? '( ' . $blogCategory->post_assign_categories_count . ' )' : '' }}
                    </a>
                </p>
            @endif
        @endforeach
    </div>
    <div class="recent-post-section br-10 px-40 bg-gray-100">
        <h5 class="fs-18 text-gray-600 mb-4">{{ __('web.web_blog.recent_posts') }}</h5>
        @foreach ($popularBlogs as $popularBlog)
            <div class="recent-post flex mb-40">
                <div class="img">
                    <a href="{{ route('front.',$popularBlog->id) }}">
                        <img src="{{ !empty($popularBlog->blog_image_url)?$popularBlog->blog_image_url:asset('assets/img/infyom-logo.png') }}" class="recent-post-img">
                    </a>
                </div>
                <div class="desc ms-4">
                    <p class="fs-14 text-gray-600 mb-0">
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
