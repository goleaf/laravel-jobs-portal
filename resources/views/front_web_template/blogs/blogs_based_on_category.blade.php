@extends('front_web_template.layouts.app')
@section('title')
    {{ __('web.post_of') . html_entity_decode($blogCategory[$categoryId]) }}
@endsection
{{-- @section('page_css') --}}
{{ -- <link rel="stylesheet" href=" asset('front_web/scss/blog.css') "> -- }}
{{-- @endsection --}}
@section('content')
    {{  -- <section class="hero-section relative bg-gray-100 py-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 lg-6 text-center mb-lg-0 mb-md-5 mb-sm-4">
                    <div class="hero-content">
                        <h1 class="text-gray-600 mb-3">
                             __('web.post_of').html_entity_decode($blogCategory[$categoryId]) 
                        </h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-center mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('front.home')  }}"
                                       class="fs-18 text-gray">{{ __('web.home') }}</a>
                                </li>
                                <li class="breadcrumb-item text-indigo-600-600 fs-18"
                                    aria-current="page">  {{ __('web.post_of').html_entity_decode($blogCategory[$categoryId]) }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="recent-blog-section py-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 lg-8">
                    <div class="blog- bg-white shadow rounded -lg overflow-hidden">
                        <div class="flex flex-wrap">
                            @if (count($blogs) > 0)
                                @foreach ($blogs as $blog)
                                    <div class="flex-1 lg-12 {{ $loop->last ?"':'mb-40' }}">
                                        <div class="bg-white shadow rounded -lg overflow-hidden flex flex-md- flex flex-wrap">
                                            <div class="bg-white shadow rounded -lg overflow-hidden img-top relative blog-detail-img">
                                                <div class="inner-image">
                                                    <img src="{{ !empty($blog->blog_image_url) ? $blog->blog_image_url :asset('assets/img/infyom-logo.png') }}"
                                                         class="bg-white shadow rounded-lg overflow-hidden img-top rounded -none" alt="Blog Image">
                                                </div>
                                                <div class="overlay absolute">
                                                    <a href="{{ route('front.',$blog->id) }}" class="px-4 py-2 rounded font-medium transition-colors text-white fs-16">
                                                        {{ __('web.post_menu.read_more') }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="bg-white shadow rounded -lg overflow-hidden body py-30 my-auto">
                                                <a href="{{ route('front.',$blog->id) }}"
                                                   class="text-gray-600 primary-link-hover">
                                                    <h5 class="bg-white shadow rounded -lg overflow-hidden title fs-18">
                                                        {{ html_entity_decode($blog->title) }}
                                                    </h5>
                                                </a>
                                                <p class="bg-white shadow rounded -lg overflow-hidden text fs-14 text-gray">
                                                    {{ !empty(strip_tags($blog->description)) ? Str::limit(strip_tags($blog->description),150,'...') :__('messages.common.n/a') }}
                                                </p>
                                                <span class="fs-14 text-gray">
                                                        {{ \Carbon\Carbon::parse($blog->created_at)->translatedFormat('M jS Y') }}
                                                        | {{ isset($blog->comments_count) ? $blog->comments_count : 0 }}
                                                    {{ __('web.web_blog.comments') }}
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <h6>
                                    <span class="">{{ __('messages.post.no_posts_available') }}</span>
                                </h6>
                            @endif
                                <div class="mt-5 flex items-center justify-center">
                                    {{ $blogs->withQueryString()->links() }}
                                </div>
                        </div>
                    </div>
                </div>
                @include('front_web.blogs.blog-sidebar')
            </div>
        </div>
    </section> --}}
    <section class="hero-section relative bg-gradient pt-15 pb-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap items-center justify-center">
                <div class="flex-1 lg-6 text-center">
                    <div class="hero-content">
                        <h1 class="text-gray-600 mb-2">
                            {{ __('web.post_of') . html_entity_decode($blogCategory[$categoryId]) }}</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-center mb-0">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('front.home') }}" class="fs-18 text-gray">{{ __('web.home') }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item text-indigo-600-600 fs-18" aria-current="page">
                                    {{ __('web.post_of') . html_entity_decode($blogCategory[$categoryId]) }}
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mani-blog recent-blog-section pt-60 pb-60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
            <div class="flex flex-wrap">
                <div class="flex-1 lg-8">
                    <div class="blog- bg-white shadow rounded -lg overflow-hidden">
                        @if (count($blogs) > 0)
                            @foreach ($blogs as $blog)
                                <div class="mb-40">
                                    <div class="bg-white shadow rounded -lg overflow-hidden flex flex-md- flex flex-wrap">
                                        <div class="bg-white shadow rounded -lg overflow-hidden img-top relative">
                                            <img src="{{ !empty($blog->blog_image_url) ? $blog->blog_image_url : asset('assets/img/infyom-logo.png') }}"
                                                        class="bg-white shadow rounded-lg overflow-hidden img-top rounded -none" alt="Blog Image">
                                            <div class="overlay absolute">
                                                <a href="{{ route('front.', $blog->id) }}"
                                                    class="px-4 py-2 rounded font-medium transition-colors text-white fs-16">
                                                    {{ __('web.post_menu.read_more') }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="bg-white shadow rounded -lg overflow-hidden body py-30 my-auto">
                                            <a href="{{ route('front.', $blog->id) }}"
                                                class="text-gray-600 primary-link-hover">
                                                <h5 class="bg-white shadow rounded -lg overflow-hidden title fs-18">
                                                    {{ html_entity_decode($blog->title) }}
                                                </h5>
                                            </a>
                                            <p class="bg-white shadow rounded -lg overflow-hidden text fs-14 text-gray">
                                                {{ !empty(strip_tags($blog->description))
                                                    ? Str::limit(strip_tags($blog->description), 150, '...')
                                                    : __('messages.common.n/a') }}
                                            </p>
                                            <span class="fs-14 text-gray">
                                                {{ \Carbon\Carbon::parse($blog->created_at)->translatedFormat('M jS Y') }}
                                                    | {{ isset($blog->comments_count) ? $blog->comments_count : 0 }}
                                                    {{ __('web.web_blog.comments') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <h6>
                                <span class="">{{ __('messages.post.no_posts_available') }}</span>
                            </h6>
                        @endif
                        <div class="mt-5 flex items-center justify-center">
                            {{ $blogs->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
                @include('front_web_template.blogs.blog-sidebar')
            </div>
        </div>
    </section>
@endsection
