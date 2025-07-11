@extends('layouts.app')

@section('title', __('terms.title'))
@section('description', __('terms.meta_description'))

@section('content')
    <!-- Hero Section -->
    <section class="bg-blue-700 dark:bg-blue-900 text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl font-extrabold leading-tight mb-4">
                {{ __('terms.hero_heading') }}
            </h1>
            <p class="text-xl font-light mb-8 max-w-3xl mx-auto">
                {{ __('terms.hero_subheading') }}
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-16 bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto prose dark:prose-invert lg:prose-lg">
            <h2>{{ __('terms.introduction_title') }}</h2>
            <p>{{ __('terms.introduction_paragraph_1') }}</p>
            <p>{{ __('terms.introduction_paragraph_2') }}</p>

            <h3>{{ __('terms.user_obligations_title') }}</h3>
            <ul>
                <li>{{ __('terms.obligation_1') }}</li>
                <li>{{ __('terms.obligation_2') }}</li>
                <li>{{ __('terms.obligation_3') }}</li>
            </ul>

            <h3>{{ __('terms.intellectual_property_title') }}</h3>
            <p>{{ __('terms.intellectual_property_paragraph_1') }}</p>

            <h3>{{ __('terms.disclaimer_warranties_title') }}</h3>
            <p>{{ __('terms.disclaimer_warranties_paragraph_1') }}</p>

            <h3>{{ __('terms.limitation_liability_title') }}</h3>
            <p>{{ __('terms.limitation_liability_paragraph_1') }}</p>

            <h3>{{ __('terms.governing_law_title') }}</h3>
            <p>{{ __('terms.governing_law_paragraph_1') }}</p>

            <h3>{{ __('terms.changes_to_terms_title') }}</h3>
            <p>{{ __('terms.changes_to_terms_paragraph_1') }}</p>

            <h3>{{ __('terms.contact_us_title') }}</h3>
            <p>{{ __('terms.contact_us_paragraph_1') }} <a href="mailto:{{ __('terms.contact_email') }}" class="text-blue-600 hover:underline">{{ __('terms.contact_email') }}</a>.</p>
        </div>
    </section>
@endsection 