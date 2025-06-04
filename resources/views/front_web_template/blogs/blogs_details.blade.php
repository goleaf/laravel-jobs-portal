@extends('front_web_template.layouts.app')
@section('title')
    {{ __('messages.post.post_details') }}
@endsection
{{-- @section('page_css') --}}
{{--    <link href="{{asset('front_web/scss/blog-details.css')}}" rel="stylesheet" type="text/css"> --}}
{{-- @endsection --}}
@section('content')
    <div class="Blog Detail-page">
        <!-- start hero section -->
        <section class="hero-section position-relative bg-gradient pt-15 pb-40">
            <div class="container mx-auto">
                <div class="flex flex-wrap items-center justify-center">
                    <div class="flex-1 -lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="text-secondary mb-2">@lang('web.blog_detail')</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-center mb-0">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('front.home') }}" class="fs-18 text-gray">@lang('web.home')
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">
                                        {{ __('messages.post.blog') }}
                                    </li>
                                    <li class="breadcrumb-item text-primary-600 fs-18" aria-current="page">
                                        @lang('web.blog_detail')
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end hero section -->

        <!-- start-blog-details-section -->
        <section class="blog-detail-section">
            <div class="container mx-auto">
                <div class="flex flex-wrap justify-center">
                    <div class="flex-1 -lg-8">
                        <div class="blog-detail py-60">
                            <h5 class="fs-4 mb-3 text-secondary">
                                {{ html_entity_decode($blog->title) }}
                            </h5>
                            <div class="designer-details flex mb-40">
                                <div class="img me-3">
                                    <img src="{{ isset($blog->user->avatar) ? $blog->user->avatar : asset('front_web/images/job-categories.png') }}"
                                        style="border-radius: 50%;height: 45px;width: 45px;object-fit: cover;" />

                                </div>
                                <div class="flex flex-wrap gap-sm-3 gap-2">
                                    <p class="fs-16 text-gray mb-0">{{ $blog->user->full_name }}</p>
                                    <span class="text-primary-600"> | </span>
                                    <p class="fs-16 text-gray mb-0">
                                        {{ \Carbon\Carbon::parse($blog->created_at)->translatedFormat('M jS Y') }}</p>
                                    <span class="text-primary-600"> | </span>
                                    <p class="fs-16 text-gray mb-0">{{ isset($comments) ? count($comments) : 0 }}
                                        @lang('web.web_blog.comments')</p>
                                </div>
                            </div>
                            @role('Candidate')
                                <div class="designer-details flex flex-wrap pb-3">
                                    <a href="{{ $url['facebook'] }}" title="@lang('web.web_jobs.facebook')" target="_blank"
                                        class="flex me-2">
                                        <div class="badge bg-primary-600 py-1 px-2">
                                            <i class="fa-brands fa-facebook fs-18"></i>
                                        </div>
                                    </a>
                                    <a href="{{ $url['twitter'] }}" title="@lang('web.web_jobs.twitter')" target="_blank"
                                        class="flex me-2">
                                        <div class="badge bg-primary-600 py-1 px-2">
                                            <i class="fa-brands fa-twitter fs-18"></i>
                                        </div>
                                    </a>
                                    <a href="{{ $url['gmail'] }}" title="@lang('web.web_jobs.google')" target="_blank"
                                        class="flex me-2">
                                        <div class="badge bg-primary-600 py-1 px-2">
                                            <i class="fa-brands fa-google fs-18"></i>
                                        </div>
                                    </a>
                                    <a href="{{ $url['pinterest'] }}" title="@lang('web.web_jobs.pinterest')" target="_blank"
                                        class="flex me-2">
                                        <div class="badge bg-primary-600 py-1 px-2">
                                            <i class="fa-brands fa-pinterest fs-18"></i>
                                        </div>
                                    </a>
                                    <a href="{{ $url['linkedin'] }}" title="@lang('web.web_jobs.linkedin')" target="_blank" class="flex">
                                        <div class="badge bg-primary-600 py-1 px-2">
                                            <i class="fa-brands fa-linkedin fs-18"></i>
                                        </div>
                                    </a>
                                </div>
                            @endrole
                            <div class="blog-img mb-40">
                                <img
                                    src="{{ !empty($blog->blog_image_url) ? $blog->blog_image_url : asset('web/img/blog_default_image.jpg') }}">
                            </div>
                            @php
                                $assignCategories = $blog->postAssignCategories->pluck('name')->toArray();
                            @endphp
                            @if (count($assignCategories) > 0)
                                <div class="designer-details flex flex-wrap mb-3">
                                    @forelse($assignCategories as $categoryBadges)
                                        <span
                                            class="p-2 m-1  badge bg-{{ getJobOtherColor($loop->index) }}">{{ $categoryBadges }}</span>
                                    @empty
                                        <span> {{ __('messages.employer_menu.no_data_available') }} </span>
                                    @endforelse
                                </div>
                            @endif
                            <div class="dark-mode mb-40 text-break">
                                <p class="fs-16 text-gray mb-0 ">
                                    {!! !empty($blog->description) ? nl2br($blog->description) : __('messages.common.n/a') !!}
                                </p>
                            </div>
                            <div class="designer-details flex justify-between">
                                <div class="prev-post mb-2">
                                    @if ($prevPost)
                                        <div class="bg-white shadow rounded-lg overflow-hidden p-3">
                                            <a href="{{ route('front.posts.details', $prevPost->id) }}"
                                                class="text-gray primary-link-hover">

                                                    <small><i class="fa fa-angle-left"></i></small>
                                                    {{ __('messages.post.previous_post') }}

                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="next-post mb-2">
                                    @if ($nextPost)
                                        <div class="bg-white shadow rounded-lg overflow-hidden p-3">
                                            <a href="{{ route('front.posts.details', $nextPost->id) }}"
                                                class="text-gray primary-link-hover">

                                                    {{ __('messages.post.next_post') }} <small><i
                                                            class="fa fa-angle-right"></i></small>

                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="comments py-60">
                            <h5 class="comment-lable fs-4 mb-3 text-secondary @if (count($comments) == 0) hidden @endif">@lang('web.web_blog.comments') <span class="comment-count"
                                    id="post-comment">({{ count($comments) }})</span></h5>
                            <div class="flex flex-wrap comment-box">
                                @foreach ($comments as $commentRecord)
                                    <div class="comment-card bg-white shadow rounded-lg overflow-hidden py-20 {{ $loop->last ?"' : 'mb-40' }}">
                                        <div class="flex flex-sm- flex flex-wrap justify-between align-items-start">
                                            <div class="flex items-center me-2">
                                                <div class="bg-white shadow rounded-lg overflow-hidden -img me-4">
                                                    @if (isset($commentRecord->user_id))
                                                        <img class="bg-white shadow rounded-lg overflow-hidden -img" src="{{ $commentRecord->user->avatar }}"
                                                            alt="user-image">
                                                    @else
                                                        <img class="bg-white shadow rounded-lg overflow-hidden -img"
                                                            src="{{ asset('front_web/images/job-categories.png') }}"
                                                            alt="user-image">
                                                    @endif
                                                </div>
                                                <div class="">
                                                    <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                                                        <h5 class="bg-white shadow rounded-lg overflow-hidden -title w-full fs-16 text-secondary text-break">
                                                            {{ $commentRecord->name }}
                                                        </h5>
                                                        <p class="fs-16 text-gray mb-0 text-break"
                                                            id="comment-{{ $commentRecord->id }}">
                                                            {{ $commentRecord->comment }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="">
                                                @if ($commentRecord->user_id == getLoggedInUserId() && getLoggedInUser())
                                                    <div class="d-inline-flex ms-2">
                                                        <a href="javascript:void(0)"
                                                            title="{{ __('messages.common.edit') }}"
                                                            class="edit-comment-btn action- px-4 py-2 rounded font-medium transition-colors"
                                                            data-id="{{ $commentRecord->id }}">
                                                            <div class="badge text-primary-600 py-2 ms-1"
                                                                data-text="Edit Comment">
                                                                <span class="fa fa-pencil"></span>
                                                            </div>
                                                        </a>
                                                        <a href="javascript:void(0)"
                                                            title="{{ __('messages.common.delete') }}"
                                                            class="action-btn delete-comment- px-4 py-2 rounded font-medium transition-colors float-right"
                                                            data-id="{{ $commentRecord->id }}">
                                                            <div class="badge text-red-600 py-2 ms-1"
                                                                data-text="Delete Comment">
                                                                <span class="fa fa-trash"></span>
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end text-nowrap">
                                            <span class="fs-14 text-gray">{{ \Carbon\Carbon::parse($commentRecord->created_at)->translatedFormat('d, M Y g:i a') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="leave-comment py-60">
                            <h5 class="fs-4 mb-3 text-secondary mb-4">@lang('messages.post.post_a_comments')</h5>
                            {{ Form::open(['id' => 'commentForm']) }}
                            {{ Form::token() }}
                            {{ Form::hidden('comment-id', null, ['class' => 'comment-id', 'value' => '']) }}
                            <div class="flex flex-wrap">
                                @if (!Auth::check())
                                    <div class="flex-1 -md-6">
                                        <div class="form-group mb-md-4 mb-3">
                                            <label for="" class="fs-16 text-secondary mb-3">{{ __('web.your_name') }}</label>
                                            <span class="text-red-600">*</span></label>
                                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white  br-10 p-3 comment-name"
                                                name="name" placeholder="{{ __('web.web_blog.your_name') }}">
                                        </div>
                                    </div>
                                    <div class="flex-1 -md-6">
                                        <div class="form-group mb-md-4 mb-3">
                                            <label for="" class="fs-16 text-secondary mb-3">{{ __('web.your_email') }}</label>
                                            <span class="text-red-600">*</span></label>
                                            <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white  br-10 p-3 comment-email"
                                                name="email" placeholder="{{ __('web.web_blog.your_email') }}">
                                        </div>
                                    </div>
                                @endif
                                <div class="flex-1 -md-12">
                                    <div class="form-group">
                                        <label for="" class="fs-16 text-secondary mb-2">{{__('web.your_comment')}}</label>
                                         <span class="text-red-600">*</span></label>
                                        <textarea id="comment-field" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10 comment"
                                            placeholder="{{ __('web.web_blog.add_your_comment') }}" rows="3" name="comment"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap justify-center">
                                <div class="flex-1 -sm-6 mb-40 text-center">
                                    <button type="submit" id="submitBtn" class="btn bg-primary-600 text-white hover: bg-primary-600 -700 px-4 py-2 rounded font-medium transition-colors -primary-register mt-5"
                                        data-loading-text="<span class='spinner-border spinner-border-sm'></span> {{ __('messages.common.process') }}">
                                        @lang('messages.post_comment.post_comment')</button>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end-blog-details-section -->
        {{ Form::hidden('blogComment', route('front.blog.comment.store', $blog->id), ['id' => 'blogComment']) }}
        {{ Form::hidden('defaultBlogImage', asset('front_web/images/job-categories.png'), ['id' => 'defaultBlogImage']) }}
    </div>
@endsection
{{-- @section('page_scripts') --}}
{{--    <script> --}}
{{--        let blogComment = "{{ route('front.blog.comment.store', $blog->id) }}"; --}}
{{--        let commentUrl = "{{ url('post-comments') }}"; --}}
{{--        let editCommentUrl = "{{ '/edit' }}"; --}}
{{--        let defaultImage = "{{ asset('front_web/images/job-categories.png') }}"; --}}
{{--    </script> --}}
{{-- @endsection --}}
