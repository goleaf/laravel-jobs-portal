@extends('layouts.app')

@section('title', __('help.help_support'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('help.how_can_we_help') }}
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-3xl mx-auto">
                {{ __('help.support_description') }}
            </p>
            
            <!-- Search Bar -->
            <div class="mt-8 max-w-2xl mx-auto">
                <div class="relative">
                    <x-ui.input
                        type="text"
                        id="help-search"
                        placeholder="{{ __('help.search_help_articles') }}"
                        icon="magnifying-glass"
                        class="text-lg py-4"
                        onkeyup="searchHelpArticles(this.value)"
                    />
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                        <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg dark:bg-gray-600 dark:text-gray-100 dark:border-gray-500">
                            ⌘K
                        </kbd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-12">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="openTicketModal()">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <x-icon name="ticket" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('help.submit_ticket') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('help.get_personalized_help') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="startLiveChat()">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                            <x-icon name="chat-bubble-left-right" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('help.live_chat') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('help.chat_with_support') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showSection('video-tutorials')">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <x-icon name="play" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('help.video_tutorials') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('help.watch_how_to_guides') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-shadow cursor-pointer" onclick="showSection('api-docs')">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                            <x-icon name="code-bracket" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('help.api_documentation') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('help.developer_resources') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Popular Articles -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                            {{ __('help.popular_articles') }}
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4" id="popular-articles">
                            @foreach($popularArticles ?? [] as $article)
                                <div class="flex items-start space-x-3 p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="openArticle('{{ $article['id'] }}')">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                            <x-icon name="document-text" class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $article['title'] }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $article['excerpt'] }}
                                        </p>
                                        <div class="flex items-center mt-2 text-xs text-gray-400">
                                            <span>{{ $article['views'] }} {{ __('help.views') }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $article['updated_at'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            <x-ui.button 
                                href="{{ route('help.articles') }}" 
                                variant="ghost" 
                                class="w-full"
                            >
                                {{ __('help.view_all_articles') }}
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Categories Sidebar -->
            <div class="space-y-6">
                <!-- Help Categories -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('help.browse_by_category') }}
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <nav class="space-y-2">
                            <a href="#" onclick="filterByCategory('getting-started')" class="flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <x-icon name="rocket-launch" class="h-4 w-4 mr-3" />
                                    {{ __('help.getting_started') }}
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">12</span>
                            </a>
                            
                            <a href="#" onclick="filterByCategory('job-posting')" class="flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <x-icon name="briefcase" class="h-4 w-4 mr-3" />
                                    {{ __('help.job_posting') }}
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">8</span>
                            </a>
                            
                            <a href="#" onclick="filterByCategory('applications')" class="flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <x-icon name="document-text" class="h-4 w-4 mr-3" />
                                    {{ __('help.managing_applications') }}
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">15</span>
                            </a>
                            
                            <a href="#" onclick="filterByCategory('billing')" class="flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <x-icon name="credit-card" class="h-4 w-4 mr-3" />
                                    {{ __('help.billing_payments') }}
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">6</span>
                            </a>
                            
                            <a href="#" onclick="filterByCategory('account')" class="flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <x-icon name="user-circle" class="h-4 w-4 mr-3" />
                                    {{ __('help.account_settings') }}
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">10</span>
                            </a>
                            
                            <a href="#" onclick="filterByCategory('troubleshooting')" class="flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center">
                                    <x-icon name="wrench-screwdriver" class="h-4 w-4 mr-3" />
                                    {{ __('help.troubleshooting') }}
                                </div>
                                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">9</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('help.contact_support') }}
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="flex items-center space-x-3">
                            <x-icon name="envelope" class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('help.email_support') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">support@jobportal.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <x-icon name="phone" class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('help.phone_support') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">+1 (555) 123-4567</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <x-icon name="clock" class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('help.support_hours') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('help.business_hours') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('help.system_status') }}
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('help.all_systems_operational') }}</span>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('help.website') }}</span>
                                <span class="text-green-600 dark:text-green-400">{{ __('help.operational') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('help.api') }}</span>
                                <span class="text-green-600 dark:text-green-400">{{ __('help.operational') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('help.database') }}</span>
                                <span class="text-green-600 dark:text-green-400">{{ __('help.operational') }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <x-ui.button 
                                href="{{ route('help.status') }}" 
                                variant="ghost" 
                                size="sm" 
                                class="w-full"
                            >
                                {{ __('help.view_status_page') }}
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-12">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    {{ __('help.frequently_asked_questions') }}
                </h2>
            </div>
            
            <div class="p-6">
                <div class="space-y-4" id="faq-container">
                    @foreach($faqs ?? [] as $index => $faq)
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg">
                            <button 
                                onclick="toggleFAQ({{ $index }})"
                                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg"
                            >
                                <span class="font-medium text-gray-900 dark:text-white">{{ $faq['question'] }}</span>
                                <x-icon name="chevron-down" class="h-5 w-5 text-gray-400 transform transition-transform" id="faq-icon-{{ $index }}" />
                            </button>
                            <div id="faq-answer-{{ $index }}" class="hidden px-6 pb-4">
                                <p class="text-gray-600 dark:text-gray-400">{{ $faq['answer'] }}</p>
                                @if(isset($faq['helpful_links']))
                                    <div class="mt-3">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">{{ __('help.helpful_links') }}:</p>
                                        <ul class="space-y-1">
                                            @foreach($faq['helpful_links'] as $link)
                                                <li>
                                                    <a href="{{ $link['url'] }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                                        {{ $link['title'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Still Need Help -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg">
            <div class="px-6 py-8 sm:px-8 sm:py-12">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-white mb-4">
                        {{ __('help.still_need_help') }}
                    </h2>
                    <p class="text-blue-100 mb-8 max-w-2xl mx-auto">
                        {{ __('help.contact_support_description') }}
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <x-ui.button 
                            onclick="openTicketModal()" 
                            variant="secondary"
                            class="bg-white text-blue-600 hover:bg-gray-50"
                        >
                            {{ __('help.submit_support_ticket') }}
                        </x-ui.button>
                        
                        <x-ui.button 
                            onclick="startLiveChat()" 
                            variant="ghost"
                            class="text-white border-white hover:bg-white hover:text-blue-600"
                        >
                            {{ __('help.start_live_chat') }}
                        </x-ui.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Support Ticket Modal -->
<x-ui.modal id="support-ticket-modal" size="lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
            {{ __('help.submit_support_ticket') }}
        </h3>
        
        <form onsubmit="submitSupportTicket(event)">
            <div class="space-y-6">
                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('help.priority') }}
                    </label>
                    <x-ui.select
                        name="priority"
                        id="priority"
                        :options="[
                            'low' => __('help.low_priority'),
                            'medium' => __('help.medium_priority'),
                            'high' => __('help.high_priority'),
                            'urgent' => __('help.urgent_priority')
                        ]"
                        required
                    />
                </div>
                
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('help.category') }}
                    </label>
                    <x-ui.select
                        name="category"
                        id="category"
                        :options="[
                            'technical' => __('help.technical_issue'),
                            'billing' => __('help.billing_question'),
                            'account' => __('help.account_issue'),
                            'feature' => __('help.feature_request'),
                            'other' => __('help.other')
                        ]"
                        required
                    />
                </div>
                
                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('help.subject') }}
                    </label>
                    <x-ui.input
                        type="text"
                        name="subject"
                        id="subject"
                        placeholder="{{ __('help.brief_description') }}"
                        required
                    />
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('help.description') }}
                    </label>
                    <x-ui.textarea
                        name="description"
                        id="description"
                        rows="6"
                        placeholder="{{ __('help.detailed_description') }}"
                        required
                    />
                </div>
                
                <!-- Attachments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('help.attachments') }}
                    </label>
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
                        <input type="file" id="ticket-attachments" multiple class="hidden" onchange="handleTicketAttachments(this.files)">
                        <x-icon name="cloud-arrow-up" class="mx-auto h-12 w-12 text-gray-400" />
                        <div class="mt-4">
                            <button type="button" onclick="document.getElementById('ticket-attachments').click()" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                {{ __('help.upload_files') }}
                            </button>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('help.file_requirements') }}
                            </p>
                        </div>
                        <div id="attachment-preview" class="mt-4 space-y-2"></div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8">
                <x-ui.button 
                    type="button"
                    onclick="closeModal('support-ticket-modal')" 
                    variant="secondary"
                >
                    {{ __('help.cancel') }}
                </x-ui.button>
                
                <x-ui.button 
                    type="submit" 
                    variant="primary"
                >
                    {{ __('help.submit_ticket') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>

<!-- Live Chat Widget -->
<div id="live-chat-widget" class="fixed bottom-4 right-4 z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 w-80 h-96 flex flex-col">
        <!-- Chat Header -->
        <div class="px-4 py-3 bg-blue-600 text-white rounded-t-lg flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                <span class="font-medium">{{ __('help.live_support') }}</span>
            </div>
            <button onclick="closeLiveChat()" class="text-white hover:text-gray-200">
                <x-icon name="x-mark" class="h-5 w-5" />
            </button>
        </div>
        
        <!-- Chat Messages -->
        <div class="flex-1 p-4 overflow-y-auto" id="chat-messages">
            <div class="space-y-3">
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">S</span>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 max-w-xs">
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ __('help.chat_welcome_message') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Chat Input -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex space-x-2">
                <input 
                    type="text" 
                    id="chat-input"
                    placeholder="{{ __('help.type_message') }}"
                    class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                    onkeypress="handleChatKeypress(event)"
                >
                <button 
                    onclick="sendChatMessage()"
                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    <x-icon name="paper-airplane" class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize help system
    initializeHelpSystem();
    
    // Keyboard shortcut for search
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('help-search').focus();
        }
    });
});

function initializeHelpSystem() {
    // Load popular articles
    loadPopularArticles();
    
    // Initialize FAQ data
    initializeFAQs();
    
    // Check for live chat availability
    checkLiveChatAvailability();
}

function searchHelpArticles(query) {
    if (query.length < 2) {
        loadPopularArticles();
        return;
    }
    
    fetch(`/help/search?q=${encodeURIComponent(query)}`)
    .then(response => response.json())
    .then(data => {
        displaySearchResults(data.articles);
    })
    .catch(error => {
        console.error('Error searching articles:', error);
    });
}

function displaySearchResults(articles) {
    const container = document.getElementById('popular-articles');
    
    if (articles.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20.4a7.962 7.962 0 01-8-7.109C4 11.764 7.69 8 12 8s8 3.764 8 5.291z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('help.no_results_found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400">{{ __('help.try_different_keywords') }}</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = articles.map(article => `
        <div class="flex items-start space-x-3 p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" onclick="openArticle('${article.id}')">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                    <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20.4a7.962 7.962 0 01-8-7.109C4 11.764 7.69 8 12 8s8 3.764 8 5.291z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">${article.title}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">${article.excerpt}</p>
                <div class="flex items-center mt-2 text-xs text-gray-400">
                    <span>${article.views} {{ __('help.views') }}</span>
                    <span class="mx-2">•</span>
                    <span>${article.updated_at}</span>
                </div>
            </div>
        </div>
    `).join('');
}

function loadPopularArticles() {
    // Reset to default popular articles view
    const container = document.getElementById('popular-articles');
    // This would typically reload the original content
}

function filterByCategory(category) {
    fetch(`/help/category/${category}`)
    .then(response => response.json())
    .then(data => {
        displaySearchResults(data.articles);
    })
    .catch(error => {
        console.error('Error filtering by category:', error);
    });
}

function openArticle(articleId) {
    window.location.href = `/help/articles/${articleId}`;
}

function toggleFAQ(index) {
    const answer = document.getElementById(`faq-answer-${index}`);
    const icon = document.getElementById(`faq-icon-${index}`);
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        answer.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function initializeFAQs() {
    // Initialize FAQ data if not provided by server
    if (!document.querySelector('#faq-container .border')) {
        const defaultFAQs = [
            {
                question: '{{ __("help.how_to_post_job") }}',
                answer: '{{ __("help.job_posting_answer") }}'
            },
            {
                question: '{{ __("help.how_to_apply") }}',
                answer: '{{ __("help.application_answer") }}'
            },
            {
                question: '{{ __("help.payment_methods") }}',
                answer: '{{ __("help.payment_answer") }}'
            }
        ];
        
        // Render default FAQs
        renderFAQs(defaultFAQs);
    }
}

function renderFAQs(faqs) {
    const container = document.getElementById('faq-container');
    container.innerHTML = faqs.map((faq, index) => `
        <div class="border border-gray-200 dark:border-gray-600 rounded-lg">
            <button 
                onclick="toggleFAQ(${index})"
                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg"
            >
                <span class="font-medium text-gray-900 dark:text-white">${faq.question}</span>
                <svg class="h-5 w-5 text-gray-400 transform transition-transform" id="faq-icon-${index}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="faq-answer-${index}" class="hidden px-6 pb-4">
                <p class="text-gray-600 dark:text-gray-400">${faq.answer}</p>
            </div>
        </div>
    `).join('');
}

function openTicketModal() {
    openModal('support-ticket-modal');
}

function submitSupportTicket(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('/help/tickets', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('support-ticket-modal');
            showNotification('{{ __("help.ticket_submitted") }}', 'success');
            
            // Reset form
            event.target.reset();
            document.getElementById('attachment-preview').innerHTML = '';
        } else {
            showNotification(data.message || '{{ __("help.ticket_failed") }}', 'error');
        }
    })
    .catch(error => {
        console.error('Error submitting ticket:', error);
        showNotification('{{ __("help.ticket_error") }}', 'error');
    });
}

function handleTicketAttachments(files) {
    const preview = document.getElementById('attachment-preview');
    preview.innerHTML = '';
    
    Array.from(files).forEach(file => {
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-700 rounded text-sm';
        div.innerHTML = `
            <span class="text-gray-700 dark:text-gray-300">${file.name}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        preview.appendChild(div);
    });
}

function startLiveChat() {
    document.getElementById('live-chat-widget').classList.remove('hidden');
    
    // Initialize chat connection
    initializeLiveChat();
}

function closeLiveChat() {
    document.getElementById('live-chat-widget').classList.add('hidden');
}

function initializeLiveChat() {
    // Connect to live chat service
    fetch('/help/chat/connect', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Chat connected successfully
            console.log('Live chat connected');
        }
    })
    .catch(error => {
        console.error('Error connecting to live chat:', error);
    });
}

function handleChatKeypress(event) {
    if (event.key === 'Enter') {
        sendChatMessage();
    }
}

function sendChatMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add message to chat
    addChatMessage(message, 'user');
    
    // Clear input
    input.value = '';
    
    // Send to server
    fetch('/help/chat/message', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        if (data.response) {
            addChatMessage(data.response, 'support');
        }
    })
    .catch(error => {
        console.error('Error sending chat message:', error);
    });
}

function addChatMessage(message, sender) {
    const messagesContainer = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    
    if (sender === 'user') {
        messageDiv.className = 'flex items-start space-x-2 justify-end';
        messageDiv.innerHTML = `
            <div class="bg-blue-600 text-white rounded-lg p-3 max-w-xs">
                <p class="text-sm">${message}</p>
            </div>
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                <span class="text-gray-700 text-sm font-medium">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
            </div>
        `;
    } else {
        messageDiv.className = 'flex items-start space-x-2';
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-medium">S</span>
            </div>
            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 max-w-xs">
                <p class="text-sm text-gray-900 dark:text-white">${message}</p>
            </div>
        `;
    }
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function checkLiveChatAvailability() {
    fetch('/help/chat/availability')
    .then(response => response.json())
    .then(data => {
        if (!data.available) {
            // Hide live chat option or show offline message
            console.log('Live chat is currently offline');
        }
    })
    .catch(error => {
        console.error('Error checking chat availability:', error);
    });
}

function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>
@endpush
