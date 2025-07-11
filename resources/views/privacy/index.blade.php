@extends('layouts.app')

@section('title', __('privacy.title'))
@section('description', __('privacy.meta_description'))

@section('content')
    <!-- Hero Section -->
    <section class="bg-blue-700 dark:bg-blue-900 text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl font-extrabold leading-tight mb-4">
                {{ __('privacy.hero_heading') }}
            </h1>
            <p class="text-xl font-light mb-8 max-w-3xl mx-auto">
                {{ __('privacy.hero_subheading') }}
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-16 bg-white dark:bg-gray-800 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto prose dark:prose-invert lg:prose-lg">
            <h2>{{ __('privacy.introduction_title') }}</h2>
            <p>{{ __('privacy.introduction_paragraph_1') }}</p>
            <p>{{ __('privacy.introduction_paragraph_2') }}</p>

            <h3>{{ __('privacy.information_collection_title') }}</h3>
            <p>{{ __('privacy.information_collection_paragraph_1') }}</p>
            <ul>
                <li><strong>{{ __('privacy.personal_data_heading') }}:</strong> {{ __('privacy.personal_data_description') }}</li>
                <li><strong>{{ __('privacy.usage_data_heading') }}:</strong> {{ __('privacy.usage_data_description') }}</li>
            </ul>

            <h3>{{ __('privacy.how_we_use_info_title') }}</h3>
            <ul>
                <li>{{ __('privacy.use_case_1') }}</li>
                <li>{{ __('privacy.use_case_2') }}</li>
                <li>{{ __('privacy.use_case_3') }}</li>
                <li>{{ __('privacy.use_case_4') }}</li>
            </ul>

            <h3>{{ __('privacy.data_sharing_title') }}</h3>
            <p>{{ __('privacy.data_sharing_paragraph_1') }}</p>

            <h3>{{ __('privacy.data_security_title') }}</h3>
            <p>{{ __('privacy.data_security_paragraph_1') }}</p>

            <h3>{{ __('privacy.your_rights_title') }}</h3>
            <p>{{ __('privacy.your_rights_paragraph_1') }}</p>

            <h3>{{ __('privacy.changes_to_policy_title') }}</h3>
            <p>{{ __('privacy.changes_to_policy_paragraph_1') }}</p>

            <h3>{{ __('privacy.contact_us_title') }}</h3>
            <p>{{ __('privacy.contact_us_paragraph_1') }} <a href="mailto:{{ __('privacy.contact_email') }}" class="text-blue-600 hover:underline">{{ __('privacy.contact_email') }}</a>.</p>
        </div>
    </section>
@endsection 