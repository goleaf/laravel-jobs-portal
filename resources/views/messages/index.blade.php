@extends('layouts.app')

@section('title', __('messages.messages'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen">
        <!-- Sidebar - Conversations List -->
        <div class="w-1/3 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ __('messages.messages') }}
                    </h1>
                    
                    <x-ui.button 
                        data-action="openNewMessageModal" 
                        variant="primary" 
                        size="sm"
                        icon="plus"
                    >
                        {{ __('messages.new') }}
                    </x-ui.button>
                </div>
                
                <!-- Search -->
                <div class="mt-4">
                    <x-ui.input
                        type="text"
                        id="conversation-search"
                        placeholder="{{ __('messages.search_conversations') }}"
                        icon="magnifying-glass"
                    />
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="px-6 py-2 border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-4">
                    <button 
                        data-filter="all"
                        class="conversation-filter active text-sm font-medium text-blue-600 dark:text-blue-400"
                    >
                        {{ __('messages.all') }}
                    </button>
                    <button 
                        data-filter="unread"
                        class="conversation-filter text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        {{ __('messages.unread') }}
                        @if($unreadCount > 0)
                            <span class="ml-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </button>
                    <button 
                        data-filter="archived"
                        class="conversation-filter text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                    >
                        {{ __('messages.archived') }}
                    </button>
                </nav>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto" id="conversations-list">
                @if($conversations && count($conversations) > 0)
                    @foreach($conversations as $conversation)
                        <div class="conversation-item border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer {{ $conversation['unread'] ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                             data-conversation-id="{{ $conversation['id'] }}"
                             data-unread="{{ $conversation['unread'] ? 'true' : 'false' }}"
                             data-archived="{{ $conversation['archived'] ? 'true' : 'false' }}">
                            <div class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($conversation['participant']['avatar'])
                                            <img class="h-10 w-10 rounded-full" src="{{ $conversation['participant']['avatar'] }}" alt="{{ $conversation['participant']['name'] }}">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                    {{ substr($conversation['participant']['name'], 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        
                                        @if($conversation['participant']['online'])
                                            <div class="absolute -mt-2 -mr-1 h-3 w-3 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                                        @endif
                                    </div>
                                    
                                    <!-- Conversation Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $conversation['participant']['name'] }}
                                            </h3>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $conversation['last_message_time'] }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                @if($conversation['last_message']['sender_id'] === auth()->id())
                                                    <span class="text-gray-500">{{ __('messages.you') }}:</span>
                                                @endif
                                                {{ $conversation['last_message']['content'] }}
                                            </p>
                                            
                                            @if($conversation['unread'])
                                                <div class="flex-shrink-0">
                                                    <div class="h-2 w-2 bg-blue-600 rounded-full"></div>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- Job Context -->
                                        @if(isset($conversation['job']))
                                            <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
                                                <x-icon name="briefcase" class="h-3 w-3 mr-1" />
                                                {{ $conversation['job']['title'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="px-6 py-12 text-center">
                        <x-icon name="chat-bubble-left-right" class="mx-auto h-12 w-12 text-gray-400" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            {{ __('messages.no_conversations') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('messages.start_conversation_description') }}
                        </p>
                        <div class="mt-6">
                            <x-ui.button 
                                data-action="openNewMessageModal" 
                                variant="primary"
                            >
                                {{ __('messages.start_conversation') }}
                            </x-ui.button>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col" id="chat-area">
            @if($activeConversation)
                <!-- Chat Header -->
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <!-- Participant Avatar -->
                            <div class="flex-shrink-0">
                                @if($activeConversation['participant']['avatar'])
                                    <img class="h-10 w-10 rounded-full" src="{{ $activeConversation['participant']['avatar'] }}" alt="{{ $activeConversation['participant']['name'] }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ substr($activeConversation['participant']['name'], 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                
                                @if($activeConversation['participant']['online'])
                                    <div class="absolute -mt-2 -mr-1 h-3 w-3 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></div>
                                @endif
                            </div>
                            
                            <!-- Participant Info -->
                            <div>
                                <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $activeConversation['participant']['name'] }}
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($activeConversation['participant']['online'])
                                        {{ __('messages.online') }}
                                    @else
                                        {{ __('messages.last_seen') }} {{ $activeConversation['participant']['last_seen'] }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Chat Actions -->
                        <div class="flex items-center space-x-2">
                            @if(isset($activeConversation['job']))
                                <x-ui.button 
                                    href="{{ route('jobs.show', $activeConversation['job']['id']) }}" 
                                    variant="ghost" 
                                    size="sm"
                                    icon="briefcase"
                                >
                                    {{ __('messages.view_job') }}
                                </x-ui.button>
                            @endif
                            
                            <x-ui.button 
                                data-action="archiveConversation" 
                                data-conversation-id="{{ $activeConversation['id'] }}"
                                variant="ghost" 
                                size="sm"
                                icon="archive-box"
                            >
                                {{ __('messages.archive') }}
                            </x-ui.button>
                            
                            <x-ui.button 
                                data-action="deleteConversation" 
                                data-conversation-id="{{ $activeConversation['id'] }}"
                                variant="ghost" 
                                size="sm"
                                icon="trash"
                            >
                                {{ __('messages.delete') }}
                            </x-ui.button>
                        </div>
                    </div>
                    
                    <!-- Job Context Banner -->
                    @if(isset($activeConversation['job']))
                        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <x-icon name="briefcase" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                                <div>
                                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-200">
                                        {{ $activeConversation['job']['title'] }}
                                    </h4>
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        {{ $activeConversation['job']['company'] }} • {{ $activeConversation['job']['location'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-container">
                    @if($activeConversation['messages'] && count($activeConversation['messages']) > 0)
                        @foreach($activeConversation['messages'] as $message)
                            <div class="message-item flex {{ $message['sender_id'] === auth()->id() ? 'justify-end' : 'justify-start' }}"
                                 data-message-id="{{ $message['id'] }}">
                                <div class="max-w-xs lg:max-w-md">
                                    <div class="flex items-end space-x-2 {{ $message['sender_id'] === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                        <!-- Avatar -->
                                        @if($message['sender_id'] !== auth()->id())
                                            <div class="flex-shrink-0">
                                                @if($activeConversation['participant']['avatar'])
                                                    <img class="h-6 w-6 rounded-full" src="{{ $activeConversation['participant']['avatar'] }}" alt="{{ $activeConversation['participant']['name'] }}">
                                                @else
                                                    <div class="h-6 w-6 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                                                            {{ substr($activeConversation['participant']['name'], 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <!-- Message Bubble -->
                                        <div class="relative px-4 py-2 rounded-lg {{ $message['sender_id'] === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white' }}">
                                            <p class="text-sm">{{ $message['content'] }}</p>
                                            
                                            @if($message['attachments'])
                                                <div class="mt-2 space-y-2">
                                                    @foreach($message['attachments'] as $attachment)
                                                        <div class="flex items-center space-x-2 p-2 bg-black bg-opacity-10 rounded">
                                                            <x-icon name="paper-clip" class="h-4 w-4" />
                                                            <a href="{{ $attachment['url'] }}" target="_blank" class="text-sm underline">
                                                                {{ $attachment['name'] }}
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            
                                            <!-- Message Time -->
                                            <div class="mt-1 text-xs opacity-75">
                                                {{ $message['created_at'] }}
                                                @if($message['sender_id'] === auth()->id())
                                                    @if($message['read_at'])
                                                        <x-icon name="check-circle" class="inline h-3 w-3 ml-1" />
                                                    @else
                                                        <x-icon name="check" class="inline h-3 w-3 ml-1" />
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <x-icon name="chat-bubble-left-right" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('messages.no_messages') }}
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('messages.start_conversation_now') }}
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Message Input -->
                <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    <form data-action="sendMessage" class="flex items-end space-x-3">
                        <!-- File Upload -->
                        <div class="flex-shrink-0">
                            <input type="file" id="message-attachment" multiple class="hidden" onchange="handleFileUpload(this.files)">
                            <button 
                                type="button" 
                                data-action="openFileUpload"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <x-icon name="paper-clip" class="h-5 w-5" />
                            </button>
                        </div>
                        
                        <!-- Message Input -->
                        <div class="flex-1">
                            <textarea 
                                id="message-input"
                                rows="1"
                                placeholder="{{ __('messages.type_message') }}"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500 resize-none"
                            ></textarea>
                            
                            <!-- File Preview -->
                            <div id="file-preview" class="hidden mt-2 space-y-2"></div>
                        </div>
                        
                        <!-- Send Button -->
                        <div class="flex-shrink-0">
                            <x-ui.button 
                                type="submit" 
                                variant="primary"
                                icon="paper-airplane"
                                id="send-button"
                                disabled
                            >
                                {{ __('messages.send') }}
                            </x-ui.button>
                        </div>
                    </form>
                    
                    <!-- Typing Indicator -->
                    <div id="typing-indicator" class="hidden mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <span class="typing-dots">
                            {{ $activeConversation['participant']['name'] }} {{ __('messages.is_typing') }}
                            <span class="animate-pulse">...</span>
                        </span>
                    </div>
                </div>
            @else
                <!-- No Conversation Selected -->
                <div class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                    <div class="text-center">
                        <x-icon name="chat-bubble-left-right" class="mx-auto h-16 w-16 text-gray-400" />
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                            {{ __('messages.select_conversation') }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('messages.select_conversation_description') }}
                        </p>
                        <div class="mt-6">
                            <x-ui.button 
                                data-action="openNewMessageModal" 
                                variant="primary"
                            >
                                {{ __('messages.start_new_conversation') }}
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Message Modal -->
<x-ui.modal id="new-message-modal" size="lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ __('messages.new_message') }}
        </h3>
        
        <form data-action="createNewConversation">
            <!-- Recipient Selection -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('messages.recipient') }}
                </label>
                <x-ui.select
                    name="recipient_id"
                    id="recipient_id"
                    :options="$availableRecipients ?? []"
                    searchable="true"
                    required
                />
            </div>
            
            <!-- Job Context (Optional) -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('messages.job_context') }} ({{ __('messages.optional') }})
                </label>
                <x-ui.select
                    name="job_id"
                    id="job_id"
                    :options="$availableJobs ?? []"
                    searchable="true"
                />
            </div>
            
            <!-- Subject -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('messages.subject') }}
                </label>
                <x-ui.input
                    type="text"
                    name="subject"
                    id="subject"
                    placeholder="{{ __('messages.enter_subject') }}"
                    required
                />
            </div>
            
            <!-- Message -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('messages.message') }}
                </label>
                <x-ui.textarea
                    name="message"
                    id="new_message"
                    rows="4"
                    placeholder="{{ __('messages.enter_message') }}"
                    required
                />
            </div>
            
            <div class="flex justify-end space-x-3">
                <x-ui.button 
                    type="button"
                    data-action="closeModal" 
                    variant="secondary"
                >
                    {{ __('messages.cancel') }}
                </x-ui.button>
                
                <x-ui.button 
                    type="submit" 
                    variant="primary"
                >
                    {{ __('messages.send_message') }}
                </x-ui.button>
            </div>
        </form>
    </div>
</x-ui.modal>
@endsection

@push('scripts')
<script>
let currentConversationId = null;
let typingTimer = null;
let isTyping = false;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize messaging system
    initializeMessaging();
    
    // Auto-refresh messages every 10 seconds
    setInterval(refreshMessages, 10000);
    
    // Initialize real-time features if available
    if (window.Echo) {
        initializeRealTimeMessaging();
    }
});

function initializeMessaging() {
    // Auto-resize message input
    const messageInput = document.getElementById('message-input');
    if (messageInput) {
        messageInput.addEventListener('input', function() {
            autoResizeTextarea(this);
            toggleSendButton();
        });
    }
    
    // Scroll to bottom of messages
    scrollToBottom();
}

function searchConversations(query) {
    const conversations = document.querySelectorAll('.conversation-item');
    
    conversations.forEach(conversation => {
        const name = conversation.querySelector('h3').textContent.toLowerCase();
        const lastMessage = conversation.querySelector('p').textContent.toLowerCase();
        
        if (name.includes(query.toLowerCase()) || lastMessage.includes(query.toLowerCase())) {
            conversation.style.display = 'block';
        } else {
            conversation.style.display = 'none';
        }
    });
}

function filterConversations(filter) {
    // Update active filter
    document.querySelectorAll('.conversation-filter').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'dark:text-blue-400');
        btn.classList.add('text-gray-500', 'dark:text-gray-400');
    });
    
    const activeFilter = document.querySelector(`[data-filter="${filter}"]`);
    activeFilter.classList.add('active', 'text-blue-600', 'dark:text-blue-400');
    activeFilter.classList.remove('text-gray-500', 'dark:text-gray-400');
    
    // Filter conversations
    const conversations = document.querySelectorAll('.conversation-item');
    conversations.forEach(conversation => {
        const isUnread = conversation.dataset.unread === 'true';
        const isArchived = conversation.dataset.archived === 'true';
        
        let show = false;
        
        switch(filter) {
            case 'all':
                show = !isArchived;
                break;
            case 'unread':
                show = isUnread && !isArchived;
                break;
            case 'archived':
                show = isArchived;
                break;
        }
        
        conversation.style.display = show ? 'block' : 'none';
    });
}

function openConversation(conversationId) {
    currentConversationId = conversationId;
    
    // Update active conversation in sidebar
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('bg-blue-100', 'dark:bg-blue-900');
    });
    
    const activeItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
    activeItem.classList.add('bg-blue-100', 'dark:bg-blue-900');
    
    // Load conversation
    fetch(`/messages/conversations/${conversationId}`)
    .then(response => response.text())
    .then(html => {
        document.getElementById('chat-area').innerHTML = html;
        scrollToBottom();
        
        // Mark as read
        markConversationAsRead(conversationId);
    })
    .catch(error => {
        console.error('Error loading conversation:', error);
    });
}

function sendMessage(event) {
    event.preventDefault();
    
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if (!message && !hasAttachments()) {
        return;
    }
    
    const formData = new FormData();
    formData.append('conversation_id', currentConversationId);
    formData.append('content', message);
    
    // Add attachments
    const attachments = document.querySelectorAll('#file-preview .file-item');
    attachments.forEach((item, index) => {
        const file = item.dataset.file;
        if (file) {
            formData.append(`attachments[${index}]`, file);
        }
    });
    
    fetch('/messages/send', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear input
            messageInput.value = '';
            clearFilePreview();
            autoResizeTextarea(messageInput);
            toggleSendButton();
            
            // Add message to chat
            addMessageToChat(data.message);
            scrollToBottom();
            
            // Update conversation list
            updateConversationList(data.conversation);
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
    });
}

function handleMessageKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage(event);
    }
    
    // Handle typing indicator
    if (!isTyping) {
        isTyping = true;
        sendTypingIndicator(true);
    }
    
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        isTyping = false;
        sendTypingIndicator(false);
    }, 1000);
}

function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
}

function toggleSendButton() {
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    
    if (messageInput.value.trim() || hasAttachments()) {
        sendButton.disabled = false;
    } else {
        sendButton.disabled = true;
    }
}

function handleFileUpload(files) {
    const filePreview = document.getElementById('file-preview');
    filePreview.classList.remove('hidden');
    
    Array.from(files).forEach(file => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item flex items-center justify-between p-2 bg-gray-100 dark:bg-gray-700 rounded';
        fileItem.dataset.file = file;
        
        fileItem.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300">${file.name}</span>
                <span class="text-xs text-gray-500">(${formatFileSize(file.size)})</span>
            </div>
            <button type="button" onclick="removeFile(this)" class="text-red-500 hover:text-red-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        filePreview.appendChild(fileItem);
    });
    
    toggleSendButton();
}

function removeFile(button) {
    button.closest('.file-item').remove();
    
    const filePreview = document.getElementById('file-preview');
    if (filePreview.children.length === 0) {
        filePreview.classList.add('hidden');
    }
    
    toggleSendButton();
}

function clearFilePreview() {
    const filePreview = document.getElementById('file-preview');
    filePreview.innerHTML = '';
    filePreview.classList.add('hidden');
}

function hasAttachments() {
    return document.querySelectorAll('#file-preview .file-item').length > 0;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function addMessageToChat(message) {
    const messagesContainer = document.getElementById('messages-container');
    const messageElement = createMessageElement(message);
    messagesContainer.appendChild(messageElement);
}

function createMessageElement(message) {
    const div = document.createElement('div');
    div.className = `message-item flex ${message.sender_id === {{ auth()->id() }} ? 'justify-end' : 'justify-start'}`;
    div.dataset.messageId = message.id;
    
    // Create message HTML (simplified version)
    div.innerHTML = `
        <div class="max-w-xs lg:max-w-md">
            <div class="relative px-4 py-2 rounded-lg ${message.sender_id === {{ auth()->id() }} ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white'}">
                <p class="text-sm">${message.content}</p>
                <div class="mt-1 text-xs opacity-75">
                    ${message.created_at}
                </div>
            </div>
        </div>
    `;
    
    return div;
}

function scrollToBottom() {
    const messagesContainer = document.getElementById('messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

function markConversationAsRead(conversationId) {
    fetch(`/messages/conversations/${conversationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update conversation item
            const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            conversationItem.classList.remove('bg-blue-50', 'dark:bg-blue-900/20');
            conversationItem.dataset.unread = 'false';
            
            // Remove unread indicator
            const unreadIndicator = conversationItem.querySelector('.h-2.w-2.bg-blue-600');
            if (unreadIndicator) {
                unreadIndicator.remove();
            }
        }
    });
}

function archiveConversation(conversationId) {
    if (!confirm('{{ __("messages.confirm_archive") }}')) return;
    
    fetch(`/messages/conversations/${conversationId}/archive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove from current view or move to archived
            const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            conversationItem.dataset.archived = 'true';
            
            // Refresh current filter
            const activeFilter = document.querySelector('.conversation-filter.active').dataset.filter;
            filterConversations(activeFilter);
            
            showNotification('{{ __("messages.conversation_archived") }}', 'success');
        }
    });
}

function deleteConversation(conversationId) {
    if (!confirm('{{ __("messages.confirm_delete") }}')) return;
    
    fetch(`/messages/conversations/${conversationId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove conversation from list
            const conversationItem = document.querySelector(`[data-conversation-id="${conversationId}"]`);
            conversationItem.remove();
            
            // Clear chat area if this was the active conversation
            if (currentConversationId === conversationId) {
                document.getElementById('chat-area').innerHTML = `
                    <div class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                {{ __('messages.conversation_deleted') }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('messages.select_another_conversation') }}
                            </p>
                        </div>
                    </div>
                `;
                currentConversationId = null;
            }
            
            showNotification('{{ __("messages.conversation_deleted") }}', 'success');
        }
    });
}

function openNewMessageModal() {
    openModal('new-message-modal');
}

function createNewConversation(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('/messages/conversations', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal('new-message-modal');
            
            // Refresh conversations list
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error creating conversation:', error);
    });
}

function refreshMessages() {
    if (!currentConversationId) return;
    
    fetch(`/messages/conversations/${currentConversationId}/messages`)
    .then(response => response.json())
    .then(data => {
        // Update messages if there are new ones
        if (data.messages && data.messages.length > 0) {
            const lastMessage = document.querySelector('.message-item:last-child');
            const lastMessageId = lastMessage ? lastMessage.dataset.messageId : 0;
            
            data.messages.forEach(message => {
                if (message.id > lastMessageId) {
                    addMessageToChat(message);
                }
            });
            
            scrollToBottom();
        }
    });
}

function sendTypingIndicator(isTyping) {
    if (!currentConversationId) return;
    
    fetch('/messages/typing', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            conversation_id: currentConversationId,
            is_typing: isTyping
        })
    });
}

function updateConversationList(conversation) {
    // Move conversation to top of list
    const conversationItem = document.querySelector(`[data-conversation-id="${conversation.id}"]`);
    const conversationsList = document.getElementById('conversations-list');
    
    if (conversationItem) {
        conversationsList.insertBefore(conversationItem, conversationsList.firstChild);
        
        // Update last message
        const lastMessageElement = conversationItem.querySelector('p');
        lastMessageElement.textContent = conversation.last_message.content;
        
        // Update time
        const timeElement = conversationItem.querySelector('.text-xs');
        timeElement.textContent = conversation.last_message_time;
    }
}

function initializeRealTimeMessaging() {
    // Listen for new messages
    Echo.private(`user.${{{ auth()->id() }}}`)
        .listen('NewMessage', (e) => {
            if (e.message.conversation_id === currentConversationId) {
                addMessageToChat(e.message);
                scrollToBottom();
            }
            
            updateConversationList(e.conversation);
        })
        .listen('UserTyping', (e) => {
            if (e.conversation_id === currentConversationId) {
                const typingIndicator = document.getElementById('typing-indicator');
                if (e.is_typing) {
                    typingIndicator.classList.remove('hidden');
                } else {
                    typingIndicator.classList.add('hidden');
                }
            }
        });
}

function showNotification(message, type = 'info') {
    // Create and show a toast notification
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