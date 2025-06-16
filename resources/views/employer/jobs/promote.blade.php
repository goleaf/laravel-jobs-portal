@extends('layouts.app')

@section('title', __('jobs.promote_job') . ' - ' . $job->title)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4 mb-4">
                <x-ui.button 
                    href="{{ route('employer.jobs.index') }}" 
                    variant="ghost"
                    icon="arrow-left"
                >
                    {{ __('jobs.back_to_jobs') }}
                </x-ui.button>
                
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('jobs.promote_job') }}
                    </h1>
                    <p class="mt-1 text-gray-600 dark:text-gray-400">
                        {{ $job->title }}
                    </p>
                </div>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="information-circle" class="h-5 w-5 text-blue-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                            {{ __('jobs.promotion_benefits') }}
                        </h3>
                        <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                            <ul class="list-disc list-inside space-y-1">
                                <li>{{ __('jobs.increase_visibility_up_to_10x') }}</li>
                                <li>{{ __('jobs.appear_top_search_results') }}</li>
                                <li>{{ __('jobs.featured_homepage_listings') }}</li>
                                <li>{{ __('jobs.priority_candidate_matching') }}</li>
                                <li>{{ __('jobs.detailed_analytics_insights') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Job Status -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('jobs.current_job_status') }}
                </h3>
            </div>
            
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($job->views_count ?? 0) }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('jobs.total_views') }}
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($job->applications_count ?? 0) }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('jobs.applications_received') }}
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($job->conversion_rate ?? 0, 1) }}%
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('jobs.conversion_rate') }}
                        </div>
                    </div>
                </div>

                <!-- Current Promotion Status -->
                @if($job->is_promoted)
                    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center">
                            <x-icon name="star" class="h-5 w-5 text-green-400 mr-2" />
                            <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ __('jobs.currently_promoted') }}
                            </span>
                        </div>
                        <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                            {{ __('jobs.promotion_expires') }}: {{ $job->promotion_expires_at->format('M d, Y \a\t h:i A') }}
                        </div>
                    </div>
                @else
                    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                        <div class="flex items-center">
                            <x-icon name="clock" class="h-5 w-5 text-gray-400 mr-2" />
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('jobs.not_currently_promoted') }}
                            </span>
                        </div>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('jobs.promote_to_increase_visibility') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Promotion Plans -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('jobs.choose_promotion_plan') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('jobs.select_plan_boost_visibility') }}
                </p>
            </div>
            
            <div class="p-6">
                <form action="{{ route('employer.jobs.promote.purchase', $job) }}" method="POST" id="promotion-form">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <!-- Basic Promotion -->
                        <div class="relative border border-gray-200 dark:border-gray-600 rounded-lg p-6 hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer" onclick="selectPlan('basic')">
                            <input type="radio" name="plan" value="basic" id="plan-basic" class="sr-only">
                            <div class="plan-content">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ __('jobs.basic_promotion') }}
                                    </h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ __('jobs.popular') }}
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($plans['basic']['price'] ?? 29, 0) }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        / {{ __('jobs.7_days') }}
                                    </span>
                                </div>
                                
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.featured_in_search_results') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.2x_visibility_boost') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.basic_analytics') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.email_support') }}
                                        </span>
                                    </li>
                                </ul>
                                
                                <div class="plan-badge hidden absolute top-4 right-4">
                                    <x-icon name="check-circle" class="h-6 w-6 text-blue-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Premium Promotion -->
                        <div class="relative border-2 border-blue-500 dark:border-blue-400 rounded-lg p-6 cursor-pointer" onclick="selectPlan('premium')">
                            <input type="radio" name="plan" value="premium" id="plan-premium" class="sr-only" checked>
                            <div class="plan-content">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ __('jobs.premium_promotion') }}
                                    </h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        {{ __('jobs.recommended') }}
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($plans['premium']['price'] ?? 79, 0) }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        / {{ __('jobs.14_days') }}
                                    </span>
                                </div>
                                
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.featured_homepage_listing') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.5x_visibility_boost') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.priority_candidate_matching') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.advanced_analytics') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.priority_support') }}
                                        </span>
                                    </li>
                                </ul>
                                
                                <div class="plan-badge absolute top-4 right-4">
                                    <x-icon name="check-circle" class="h-6 w-6 text-blue-500" />
                                </div>
                            </div>
                        </div>

                        <!-- Enterprise Promotion -->
                        <div class="relative border border-gray-200 dark:border-gray-600 rounded-lg p-6 hover:border-blue-500 dark:hover:border-blue-400 transition-colors cursor-pointer" onclick="selectPlan('enterprise')">
                            <input type="radio" name="plan" value="enterprise" id="plan-enterprise" class="sr-only">
                            <div class="plan-content">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ __('jobs.enterprise_promotion') }}
                                    </h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                        {{ __('jobs.maximum_reach') }}
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                        ${{ number_format($plans['enterprise']['price'] ?? 149, 0) }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">
                                        / {{ __('jobs.30_days') }}
                                    </span>
                                </div>
                                
                                <ul class="space-y-3 mb-6">
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.premium_homepage_placement') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.10x_visibility_boost') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.ai_powered_candidate_matching') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.comprehensive_analytics') }}
                                        </span>
                                    </li>
                                    <li class="flex items-center">
                                        <x-icon name="check" class="h-4 w-4 text-green-500 mr-2" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ __('jobs.dedicated_account_manager') }}
                                        </span>
                                    </li>
                                </ul>
                                
                                <div class="plan-badge hidden absolute top-4 right-4">
                                    <x-icon name="check-circle" class="h-6 w-6 text-blue-500" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-8">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            {{ __('jobs.additional_boost_options') }}
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <div class="flex items-center">
                                    <input 
                                        id="urgent_hiring" 
                                        name="add_ons[]" 
                                        type="checkbox" 
                                        value="urgent_hiring"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <div class="ml-3">
                                        <label for="urgent_hiring" class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('jobs.urgent_hiring_badge') }}
                                        </label>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('jobs.urgent_hiring_description') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    +${{ number_format($addOns['urgent_hiring'] ?? 15, 0) }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <div class="flex items-center">
                                    <input 
                                        id="social_media_boost" 
                                        name="add_ons[]" 
                                        type="checkbox" 
                                        value="social_media_boost"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <div class="ml-3">
                                        <label for="social_media_boost" class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('jobs.social_media_promotion') }}
                                        </label>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('jobs.social_media_promotion_description') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    +${{ number_format($addOns['social_media_boost'] ?? 25, 0) }}
                                </div>
                            </div>

                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <div class="flex items-center">
                                    <input 
                                        id="email_campaign" 
                                        name="add_ons[]" 
                                        type="checkbox" 
                                        value="email_campaign"
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <div class="ml-3">
                                        <label for="email_campaign" class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ __('jobs.targeted_email_campaign') }}
                                        </label>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('jobs.email_campaign_description') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    +${{ number_format($addOns['email_campaign'] ?? 35, 0) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Promotion Summary -->
                    <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            {{ __('jobs.promotion_summary') }}
                        </h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('jobs.selected_plan') }}:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" id="selected-plan-name">
                                    {{ __('jobs.premium_promotion') }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('jobs.base_price') }}:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" id="base-price">
                                    ${{ number_format($plans['premium']['price'] ?? 79, 0) }}
                                </span>
                            </div>
                            
                            <div id="add-ons-summary" class="hidden">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('jobs.add_ons') }}:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="add-ons-price">
                                        $0
                                    </span>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 dark:border-gray-600 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-base font-medium text-gray-900 dark:text-white">{{ __('jobs.total_price') }}:</span>
                                    <span class="text-base font-bold text-gray-900 dark:text-white" id="total-price">
                                        ${{ number_format($plans['premium']['price'] ?? 79, 0) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mt-8">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            {{ __('jobs.payment_method') }}
                        </h4>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input 
                                    id="payment_credit_card" 
                                    name="payment_method" 
                                    type="radio" 
                                    value="credit_card"
                                    checked
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <label for="payment_credit_card" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('jobs.credit_debit_card') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="payment_paypal" 
                                    name="payment_method" 
                                    type="radio" 
                                    value="paypal"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                >
                                <label for="payment_paypal" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('jobs.paypal') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="payment_company_credits" 
                                    name="payment_method" 
                                    type="radio" 
                                    value="company_credits"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                    {{ ($companyCredits ?? 0) < ($plans['premium']['price'] ?? 79) ? 'disabled' : '' }}
                                >
                                <label for="payment_company_credits" class="ml-3 block text-sm {{ ($companyCredits ?? 0) < ($plans['premium']['price'] ?? 79) ? 'text-gray-400' : 'text-gray-900 dark:text-gray-300' }}">
                                    {{ __('jobs.company_credits') }} 
                                    <span class="text-xs">({{ __('jobs.available') }}: ${{ number_format($companyCredits ?? 0, 0) }})</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mt-8">
                        <div class="flex items-center">
                            <input 
                                id="agree_terms" 
                                name="agree_terms" 
                                type="checkbox" 
                                required
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="agree_terms" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                {{ __('jobs.agree_to') }} 
                                <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('jobs.terms_conditions') }}
                                </a> 
                                {{ __('jobs.and') }} 
                                <a href="{{ route('privacy') }}" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ __('jobs.privacy_policy') }}
                                </a>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex justify-end space-x-3">
                        <x-ui.button 
                            href="{{ route('employer.jobs.show', $job) }}" 
                            variant="secondary"
                        >
                            {{ __('jobs.cancel') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            type="submit" 
                            variant="primary"
                            id="promote-button"
                        >
                            {{ __('jobs.promote_job_now') }}
                        </x-ui.button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Expected Results -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('jobs.expected_results') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('jobs.based_on_similar_jobs') }}
                </p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $expectedResults['views_increase'] ?? '300' }}%
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            {{ __('jobs.increase_in_views') }}
                        </div>
                    </div>
                    
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $expectedResults['applications_increase'] ?? '250' }}%
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            {{ __('jobs.more_applications') }}
                        </div>
                    </div>
                    
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $expectedResults['quality_improvement'] ?? '40' }}%
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            {{ __('jobs.better_candidate_quality') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Plan prices
    const planPrices = {
        basic: {{ $plans['basic']['price'] ?? 29 }},
        premium: {{ $plans['premium']['price'] ?? 79 }},
        enterprise: {{ $plans['enterprise']['price'] ?? 149 }}
    };
    
    const planNames = {
        basic: '{{ __("jobs.basic_promotion") }}',
        premium: '{{ __("jobs.premium_promotion") }}',
        enterprise: '{{ __("jobs.enterprise_promotion") }}'
    };
    
    const addOnPrices = {
        urgent_hiring: {{ $addOns['urgent_hiring'] ?? 15 }},
        social_media_boost: {{ $addOns['social_media_boost'] ?? 25 }},
        email_campaign: {{ $addOns['email_campaign'] ?? 35 }}
    };
    
    // Update pricing when plan changes
    document.querySelectorAll('input[name="plan"]').forEach(radio => {
        radio.addEventListener('change', updatePricing);
    });
    
    // Update pricing when add-ons change
    document.querySelectorAll('input[name="add_ons[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', updatePricing);
    });
    
    // Initial pricing update
    updatePricing();
});

function selectPlan(planType) {
    // Remove selected state from all plans
    document.querySelectorAll('.plan-content').forEach(content => {
        content.parentElement.classList.remove('border-blue-500', 'dark:border-blue-400');
        content.parentElement.classList.add('border-gray-200', 'dark:border-gray-600');
        content.querySelector('.plan-badge').classList.add('hidden');
    });
    
    // Add selected state to clicked plan
    const selectedPlan = document.getElementById(`plan-${planType}`);
    selectedPlan.checked = true;
    selectedPlan.closest('.relative').classList.remove('border-gray-200', 'dark:border-gray-600');
    selectedPlan.closest('.relative').classList.add('border-blue-500', 'dark:border-blue-400');
    selectedPlan.closest('.relative').querySelector('.plan-badge').classList.remove('hidden');
    
    updatePricing();
}

function updatePricing() {
    const selectedPlan = document.querySelector('input[name="plan"]:checked').value;
    const selectedAddOns = Array.from(document.querySelectorAll('input[name="add_ons[]"]:checked')).map(cb => cb.value);
    
    // Update plan name and base price
    document.getElementById('selected-plan-name').textContent = planNames[selectedPlan];
    document.getElementById('base-price').textContent = '$' + planPrices[selectedPlan].toLocaleString();
    
    // Calculate add-ons total
    let addOnsTotal = 0;
    selectedAddOns.forEach(addOn => {
        addOnsTotal += addOnPrices[addOn] || 0;
    });
    
    // Show/hide add-ons summary
    const addOnsSummary = document.getElementById('add-ons-summary');
    if (addOnsTotal > 0) {
        addOnsSummary.classList.remove('hidden');
        document.getElementById('add-ons-price').textContent = '$' + addOnsTotal.toLocaleString();
    } else {
        addOnsSummary.classList.add('hidden');
    }
    
    // Update total price
    const totalPrice = planPrices[selectedPlan] + addOnsTotal;
    document.getElementById('total-price').textContent = '$' + totalPrice.toLocaleString();
    
    // Update company credits payment option availability
    const companyCredits = {{ $companyCredits ?? 0 }};
    const creditsRadio = document.getElementById('payment_company_credits');
    const creditsLabel = creditsRadio.nextElementSibling;
    
    if (companyCredits >= totalPrice) {
        creditsRadio.disabled = false;
        creditsLabel.classList.remove('text-gray-400');
        creditsLabel.classList.add('text-gray-900', 'dark:text-gray-300');
    } else {
        creditsRadio.disabled = true;
        creditsRadio.checked = false;
        creditsLabel.classList.add('text-gray-400');
        creditsLabel.classList.remove('text-gray-900', 'dark:text-gray-300');
        
        // Select credit card if credits was selected
        document.getElementById('payment_credit_card').checked = true;
    }
}

// Form submission with loading state
document.getElementById('promotion-form').addEventListener('submit', function() {
    const submitButton = document.getElementById('promote-button');
    const originalText = submitButton.textContent;
    
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('jobs.processing_payment') }}...
        </div>
    `;
});
</script>
@endpush 