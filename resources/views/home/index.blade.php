@extends('layouts.app')

@section('title', __('home.page_title'))
@section('description', __('home.page_description'))

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-r from-blue-600 to-blue-800 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="text-center">
            <h1 class="text-5xl md:text-6xl font-extrabold leading-tight mb-4">
                {{ __('home.hero_title') }}
            </h1>
            <p class="text-xl opacity-90 mb-8 max-w-3xl mx-auto">
                {{ __('home.hero_subtitle') }}
            </p>
            
            <!-- Job Search Form -->
            <div class="max-w-2xl mx-auto flex rounded-md overflow-hidden shadow-lg">
                <x-forms.job-search-form 
                    class="flex-1 p-3 text-gray-900"
                />
                {{-- The button inside x-forms.job-search-form might need internal styling adjustment to match bg-blue-800 hover:bg-blue-900 --}}
            </div>
            
            <!-- Call to Action Buttons -->
            <div class="flex justify-center gap-4 mt-8">
                <x-button 
                    href="{{ route('register') }}?type=candidate" 
                    class="bg-white text-blue-600 px-6 py-3 rounded-md font-semibold hover:bg-gray-100"
                >
                    {{ __('home.join_as_candidate') }}
                </x-button>
                
                <x-button 
                    href="{{ route('register') }}?type=employer" 
                    class="border border-white text-white px-6 py-3 rounded-md font-semibold hover:bg-white hover:text-blue-600"
                >
                    {{ __('home.join_as_employer') }}
                </x-button>
            </div>
        </div>
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute top-0 right-0 w-1/3 h-full opacity-10">
        <svg viewBox="0 0 400 400" class="w-full h-full">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <x-ui.stats-grid />
    </div>
</section>

<!-- Featured Jobs Section -->
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                {{ __('home.featured_jobs_title') }}
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                {{ __('home.featured_jobs_subtitle') }}
            </p>
        </div>

        <x-jobs.featured-jobs-grid :jobs="$featuredJobs" />

        <div class="text-center mt-12">
            <x-button 
                href="{{ route('jobs.index') }}" 
                class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-semibold rounded-full shadow-lg hover:bg-blue-700 transition duration-300"
            >
                {{ __('home.view_all_jobs') }}
                <x-icon name="arrow-right" class="ml-3 h-5 w-5" />
            </x-button>
        </div>
    </div>
</section>

<!-- Featured Companies Section -->
<section class="py-16 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                {{ __('home.featured_companies_title') }}
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                {{ __('home.featured_companies_subtitle') }}
            </p>
        </div>

        <x-companies.featured-companies-grid :companies="$featuredCompanies" />

        <div class="text-center mt-12">
            <x-button 
                href="{{ route('companies.index') }}" 
                class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-semibold rounded-full shadow-lg hover:bg-blue-700 transition duration-300"
            >
                {{ __('home.view_all_companies') }}
                <x-icon name="arrow-right" class="ml-3 h-5 w-5" />
            </x-button>
        </div>
    </div>
</section>

<!-- Job Categories Section -->
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                {{ __('home.job_categories_title') }}
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                {{ __('home.job_categories_subtitle') }}
            </p>
        </div>

        <x-jobs.job-categories-grid :categories="$jobCategories" />
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                {{ __('home.how_it_works_title') }}
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                {{ __('home.how_it_works_subtitle') }}
            </p>
        </div>

        <x-ui.how-it-works-steps />
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-blue-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-extrabold mb-12">
            {{ __('home.testimonials_title') }}
        </h2>
        {{-- Testimonial Carousel (conceptual) --}}
        <div class="max-w-4xl mx-auto italic text-xl md:text-2xl mb-8 leading-relaxed">
            "{{ __('home.testimonial_quote_1') }}"
        </div>
        <p class="font-semibold text-lg">{{ __('home.testimonial_author_1') }}</p>
    </div>
</section>

<!-- Recent Blog Posts / News -->
@if($blogPosts && $blogPosts->count() > 0)
<section class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4">
                {{ __('home.latest_news_title') }}
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                {{ __('home.latest_news_subtitle') }}
            </p>
        </div>

        <x-blog.posts-grid :posts="$blogPosts" />

        <div class="text-center mt-12">
            <x-button 
                href="{{ route('blog.index') }}" 
                class="inline-flex items-center px-8 py-4 bg-blue-600 text-white font-semibold rounded-full shadow-lg hover:bg-blue-700 transition duration-300"
            >
                {{ __('home.view_all_posts') }}
                <x-icon name="arrow-right" class="ml-3 h-5 w-5" />
            </x-button>
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
    @vite('resources/js/home.js')
@endpush 