@extends('front_web.layouts.app')
@section('title')
    {{ __('messages.post.post_details') }}
@endsection
{{-- @section('page_css') --}}
{{ -- <link href="asset('front_web/scss/blog-details.css') " rel="stylesheet" type="text/css"> -- }}
{{-- @endsection --}}
@section('content')
    <div class="Blog Detail-page">
        <!-- start hero section -->
        <section class="hero-section relative bg-color-light py-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto">
                <div class="flex-wrap flex items-center justify-center">
                    <div class="text-center flex-1 lg-6 mb-lg-0 mb-md-5 mb-sm-4">
                        <div class="hero-content">
                            <h1 class="mb-3 text-gray-600">
                                @lang('web.blog_detail')
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="mb-0 flex space-x-2 text-sm justify-center">
                                    <li class="flex space-x-2 text-sm -item">
                                        <a href="{{ route('front.home') }}" class="fs-18 text-gray">
                                            @lang('web.home')</a>
                                    </li>
                                    <li class="flex space-x-2 text-sm -item text-indigo-600-600 fs-18"
                                        aria-current="page">{{ __('messages.post.blog') }}</li>
                                    <li class="flex space-x-2 text-sm -item text-indigo-600-600 fs-18" aria-current="page">@lang('web.blog_detail')</li>
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto">
                <div class="flex-wrap flex justify-center">
                    <div class="flex-1 lg-8">
                        <div class="blog-detail py-60">
                            <h5 class="mb-3 fs-4 text-gray-600">
                                {{ html_entity_decode($blog->title) }}</h5>
                            <div class="flex-wrap designer-details flex">
                                <div class="me-4">
                                    <img src="{{ isset($blog->$user->avatar) ? $blog->$user->avatar : asset('front_web/images/job-categories.png') }}"
                                         class="rounded img object-fit-cover">
                                </div>
                                <p class="fs-16 text-gray me-3">
                                    {{ $blog->$user->full_name }}</p>
                                <span class="text-indigo-600 -600 me-3"> | </span>
                                <p class="fs-16 text-gray me-3">
                                    {{ \Carbon\Carbon::parse($blog->created_at)->translatedFormat('M jS Y') }}</p>
                                <span class="text-indigo-600 -600 me-3"> | </span>
                                <p class="fs-16 text-gray me-3">
                                    {{ isset($comments) ? count($comments) : 0 }} Comment</p>
                            </div>
                            @role('Candidate')
                            <div class="flex-wrap designer-details flex">
                                <a href="{{ $url['facebook'] }}" title="@lang('web.web_jobs.facebook')" target="_blank" class="flex me-2">
                                    <div class="rounded bg-indigo-600 inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium -600 py-1 px-2">
                                        <i class="fa-brands fa-facebook fs-18"></i>
                                    </div>
                                </a>
                                <a href="{{ $url['twitter'] }}" title="@lang('web.web_jobs.twitter')" target="_blank" class="flex me-2">
                                    <div class="rounded bg-indigo-600 inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium -600 py-1 px-2">
                                        <i class="fa-brands fa-twitter fs-18"></i>
                                    </div>
                                </a>
                                <a href="{{ $url['gmail'] }}" title="@lang('web.web_jobs.google')" target="_blank" class="flex me-2">
                                    <div class="rounded bg-indigo-600 inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium -600 py-1 px-2">
                                        <i class="fa-brands fa-google fs-18"></i>
                                    </div>
                                </a>
                                <a href="{{ $url['pinterest'] }}" title="@lang('web.web_jobs.pinterest')" target="_blank" class="flex me-2">
                                    <div class="rounded bg-indigo-600 inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium -600 py-1 px-2">
                                        <i class="fa-brands fa-pinterest fs-18"></i>
                                    </div>
                                </a>
                                <a href="{{ $url['linkedin'] }}" title="@lang('web.web_jobs.linkedin')" target="_blank" class="flex">
                                    <div class="rounded bg-indigo-600 inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium -600 py-1 px-2">
                                        <i class="fa-brands fa-linkedin fs-18"></i>
                                    </div>
                                </a>
                            </div>
                            @endrole
                            <div class="blog-img mt-40 mb-40">
                                <img src="{{ !empty($blog->blog_image_url)?$blog->blog_image_url:asset('web/img/blog_default_image.jpg') }}">
                            </div>
                            @php
                                $assignCategories = $blog->postAssignCategories->pluck('name')->toArray();
                            @endphp
                            @if(count($assignCategories) > 0)
                                <div class="mb-3 designer-details flex">
                                    @forelse($assignCategories as $categoryBadges)
                                        <span class="rounded p-2 inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium me-2 bg-{{ getJobOtherColor($loop->index) }}">{{ $categoryBadges }}</span>
                                    @empty
                                        <span> {{ __('messages.employer_menu.no_data_available') }} </span>
                                    @endforelse
                                </div>
                            @endif
                            <div class="mb-40 blog-description">
                                {{ !empty($blog->description)? nl2br(($blog->description)):__('messages.common.n/a') }}
                            </div>
                            <div class="designer-details flex justify-between">
                                <div class="prev-post">
                                    @if(count($prevPost) >0 )
                                        <div class="overflow-hidden shadow rounded p-3 bg-white -lg">
                                            @foreach($prevPost as $post)
                                                <a href="{{ route('front.',$post->id) }}"
                                                   class="text-gray primary-link-hover">
                                                    <h5>
                                                        <small><i class="fa fa-angle-left"></i></small> {{ __('messages.post.previous_post') }}
                                                    </h5>
                                                    <h6 class="mb-0">{{ $post->title }}</h6>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="next-post">
                                    @if(count($nextPost) >0 )
                                        <div class="overflow-hidden shadow rounded p-3 bg-white -lg">
                                            @foreach($nextPost as $post)
                                                <a href="{{ route('front.',$post->id) }}"
                                                   class="text-gray primary-link-hover">
                                                    <h5>
                                                        {{ __('messages.post.next_post') }} <small><i class="fa fa-angle-right"></i></small>
                                                    </h5>
                                                    <h6 class="mb-0">{{ $post->title }}</h6>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="comments py-60">
                            <h5 class="mb-3 fs-4 text-gray-600">
                                @lang('web.web_blog.comments') <span class="comment-count" id="post-comment">({{ count($comments) }})</span>
                            </h5>
                            <div class="flex-wrap flex comment-box">
                                @foreach($comments as $commentRecord)
                                    <div class="overflow-hidden shadow-sm shadow rounded-lg rounded bg-white bg-white comment- -lg py-20 {{ $loop->last?"':'mb-40' }}">
                                        <div class="flex-wrap flex justify-between">
                                            <div class="xl:w-1/12 px-4 flex-1 px-4 -sm-2 flex-1 -3">
                                                <div class="">
                                                    @if(isset($commentRecord->user_id))
                                                        <img class="overflow-hidden shadow rounded bg-white -lg img"
                                                             src="{{ $commentRecord->$user->avatar }}"
                                                             alt="user-image">
                                                    @else
                                                        <img class="overflow-hidden shadow rounded bg-white -lg img"
                                                             src="{{ asset('front_web/images/job-categories.png') }}"
                                                             alt="user-image">
                                                    @endif
                                            </div>
                                        </div>
                                        <div class="xl:w-6/12 px-4 flex-1 px-4 -sm-5 flex-1 -9 ps-xl-4">
                                            <div class="overflow-hidden shadow rounded bg-white -lg body ps-0">
                                                <h5 class="overflow-hidden shadow rounded bg-white -lg title fs-16 text-gray-600">
                                                    {{ $commentRecord->name }}
                                                    @if($commentRecord->user_id == getLoggedInUserId() && getLoggedInUser())
                                                        <div class="inline -flex ms-2">
                                                            <a href="javascript:void(0)" title="{{ __('messages.common.edit') }}"
                                                                   class="rounded rounded edit-comment-inline-flex items-center px-4 py-2 font-medium transition-colors" data-id="{{ $commentRecord->id }}">
                                                                <div class="rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium bg-indigo-600-600 py-2 ms-1" data-text="Edit Comment">
                                                                    <span class="fa fa-pencil"></span>
                                                                </div>
                                                            </a>
                                                           <a href="javascript:void(0)" title="{{ __('messages.common.delete') }}"
                                                                   class="float-right rounded rounded action-inline-flex items-center px-4 py-2 font-medium transition-colors"
                                                                   data-id="{{ $commentRecord->id }}">
                                                                <div class="rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium bg-indigo-600-600 py-2 ms-1" data-text="Delete Comment">
                                                                    <span class="fa fa-trash"></span>
                                                                </div>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </h5>
                                                <p class="fs-16 text-gray" id="comment-{{ $commentRecord->id }}">
                                                    {{ $commentRecord->comment }}</p>
                                            </div>
                                        </div>
                                        <div class="flex-1 sm-5 text-end">
                                            <span class="fs-14 text-gray">
                                                 {{ \Carbon\Carbon::parse($commentRecord->created_at)->translatedFormat('d, M Y g:i a') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                        <div class="leave-comment py-60">
                            <h5 class="mb-4 mb-3 fs-4 text-gray-600">@lang('messages.post.post_a_comments')</h5>
                            {{ Form::open(['id' => 'commentForm']) }}
                            {{ Form::token() }}
                            {{ Form::hidden('comment-id', null, ['class' => 'comment-id','value' => '']) }}
                                <div class="flex-wrap clear-both flex mb-40">
                                    @if(!Auth::check())
                                        <div class="mb-4 flex-1 md-6">
                                            <div class="mb-4">
                                                <label for="" class="mb-2 fs-16 text-gray-600">{{ __('web.your_name') }}</label>
                                                <input type="text" name="name" class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10 comment-name"
                                                       placeholder="{{ __('web.web_blog.your_name') }}">
                                            </div>
                                        </div>
                                        <div class="mb-4 flex-1 md-6">
                                            <div class="mb-4">
                                                <label for="" class="mb-2 fs-16 text-gray-600">{{ __('web.your_email') }}</label>
                                                <input type="email" name="email" class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10 comment-email"
                                                       placeholder="{{ __('web.web_blog.your_email') }}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex-1 md-12">
                                        <div class="mb-4">
                                            <label for="" class="mb-2 fs-16 text-gray-600">{{ __('web.your_comment') }}</label>
                                            <textarea id="comment-field" class="rounded border border border border border-gray-300 -gray-300 w-full px-3 py-2 -gray-300 -gray-300 -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray br-10 comment"
                                                      placeholder="{{ __('web.web_blog.add_your_comment') }}" rows="3" name="comment"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-wrap flex justify-center">
                                    <div class="text-center flex-1 sm-6 mb-40">
                                        <button type="submit" id="submitBtn" class="border border-gray-300 bg-transparent"
                                                data-loading-text="<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span> {{ __('messages.common.process') }}">
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
        {{ Form::hidden('blogComment',route('front.', $blog->id),['id'=>'blogComment']) }}
        {{ Form::hidden('defaultBlogImage',asset('front_web/images/job-categories.png'),['id'=>'defaultBlogImage']) }}
    </div>
@endsection
{{-- @section('page_scripts') --}}
{{--  --}}
{{-- @endsection --}}

@push('scripts')
    @vite('resources/js/pages/blogs_details.js')
@endpush
