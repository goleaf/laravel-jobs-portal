@extends('layouts.app')

@section('title', __('messaging.title'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="flex h-screen">
        {{-- Conversation Sidebar --}}
        <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
            {{-- Header --}}
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">{{ __('messaging.title') }}</h2>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white p-2 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </button>
                </div>
                {{-- Search --}}
                <div class="mt-4">
                    <div class="relative">
                        <input type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="{{ __('messaging.search_conversations') }}">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Conversations List --}}
            <div class="flex-1 overflow-y-auto">
                {{-- Active Conversation --}}
                <div class="conversation-item bg-indigo-50 border-l-4 border-indigo-500 p-4 cursor-pointer hover:bg-indigo-100">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Wilson&color=7c3aed&background=ede9fe" alt="Sarah Wilson" class="w-12 h-12 rounded-full">
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ __('messaging.sender_name') }}</h3>
                                <span class="text-xs text-gray-500">2m</span>
                            </div>
                            <p class="text-sm text-gray-600 truncate">{{ __('messaging.conversation_types.hr_manager') }}</p>
                            <div class="flex items-center mt-1">
                                <span class="bg-indigo-500 text-white text-xs px-2 py-0.5 rounded-full">2</span>
                                <span class="ml-2 text-xs text-gray-500">{{ __('messaging.conversation_types.hr_manager') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Other Conversations --}}
                <div class="conversation-item p-4 cursor-pointer hover:bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&color=059669&background=d1fae5" alt="John Doe" class="w-12 h-12 rounded-full">
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-gray-400 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">John Doe</h3>
                                <span class="text-xs text-gray-500">1h</span>
                            </div>
                            <p class="text-sm text-gray-600 truncate">{{ __('messaging.conversation_types.senior_developer') }}</p>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-gray-500">{{ __('messaging.conversation_types.senior_developer') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="conversation-item p-4 cursor-pointer hover:bg-gray-50 border-b border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=Tech+Corp&color=dc2626&background=fee2e2" alt="Tech Corp" class="w-12 h-12 rounded-full">
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">Tech Corp</h3>
                                <span class="text-xs text-gray-500">3h</span>
                            </div>
                            <p class="text-sm text-gray-600 truncate">{{ __('messaging.conversation_types.company') }}</p>
                            <div class="flex items-center mt-1">
                                <span class="text-xs text-gray-500">{{ __('messaging.conversation_types.company') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Chat Area --}}
        <div class="flex-1 flex flex-col">
            {{-- Chat Header --}}
            <div class="bg-white p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <img src="https://ui-avatars.com/api/?name=Sarah+Wilson&color=7c3aed&background=ede9fe" alt="Sarah Wilson" class="w-10 h-10 rounded-full">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Sarah Wilson</h3>
                            <p class="text-xs text-green-600">{{ __('messaging.online') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Messages Area --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messagesContainer">
                {{-- Received Message --}}
                <div class="flex items-start space-x-3">
                    <img src="https://ui-avatars.com/api/?name=Sarah+Wilson&color=7c3aed&background=ede9fe" alt="Sarah Wilson" class="w-8 h-8 rounded-full">
                    <div class="flex-1">
                        <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                            <p class="text-sm text-gray-900">{{ __('messaging.conversation_types.hr_manager') }}</p>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messaging.time_ago.minutes', ['count' => 2]) }}</p>
                    </div>
                </div>

                {{-- Sent Message --}}
                <div class="flex items-start space-x-3 justify-end">
                    <div class="flex-1 flex justify-end">
                        <div class="bg-indigo-600 rounded-lg p-3 max-w-xs">
                            <p class="text-sm text-white">{{ __('messaging.conversation_types.senior_developer') }}</p>
                        </div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=You&color=ffffff&background=6366f1" alt="You" class="w-8 h-8 rounded-full">
                </div>

                {{-- System Message --}}
                <div class="flex justify-center">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2 px-3">
                        <p class="text-xs text-yellow-700">{{ __('messaging.typing', ['name' => 'Sarah Wilson']) }}</p>
                    </div>
                </div>

                {{-- File Message --}}
                <div class="flex items-start space-x-3">
                    <img src="https://ui-avatars.com/api/?name=Sarah+Wilson&color=7c3aed&background=ede9fe" alt="Sarah Wilson" class="w-8 h-8 rounded-full">
                    <div class="flex-1">
                        <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                            <div class="flex items-center space-x-2">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('messaging.attachment_name', ['name' => 'job_description.pdf']) }}</p>
                                    <p class="text-xs text-gray-500">245 KB</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messaging.time_ago.minutes', ['count' => 1]) }}</p>
                    </div>
                </div>
            </div>

            {{-- Message Input --}}
            <div class="bg-white border-t border-gray-200 p-4">
                <div class="flex items-end space-x-3">
                    <div class="flex-1">
                        <div class="border border-gray-300 rounded-lg">
                            <textarea class="w-full p-3 border-0 rounded-lg resize-none focus:ring-0 focus:border-transparent" rows="2" placeholder="{{ __('messaging.type_message') }}"></textarea>
                            <div class="flex items-center justify-between p-3 border-t border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <button class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                    </button>
                                    <button class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-4 7v2a1 1 0 01-1 1H8a1 1 0 01-1-1v-2H7a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-1z"/>
                                        </svg>
                                    </button>
                                    <button class="p-1 text-gray-400 hover:text-gray-600 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </div>
                                <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                    {{ __('messaging.send') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to bottom of messages
    const messagesContainer = document.getElementById('messagesContainer');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Handle conversation switching
    const conversationItems = document.querySelectorAll('.conversation-item');
    conversationItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active state from all items
            conversationItems.forEach(i => {
                i.classList.remove('bg-indigo-50', 'border-l-4', 'border-indigo-500');
                i.classList.add('hover:bg-gray-50');
            });
            
            // Add active state to clicked item
            this.classList.add('bg-indigo-50', 'border-l-4', 'border-indigo-500');
            this.classList.remove('hover:bg-gray-50');
        });
    });
    
    // Handle send message
    const sendButton = document.querySelector('button:contains("Send")');
    const messageInput = document.querySelector('textarea');
    
    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            // Add message to chat (simplified)
            const messageElement = document.createElement('div');
            messageElement.className = 'flex items-start space-x-3 justify-end';
            messageElement.innerHTML = `
                <div class="flex-1 flex justify-end">
                    <div class="bg-indigo-600 rounded-lg p-3 max-w-xs">
                        <p class="text-sm text-white">${message}</p>
                    </div>
                </div>
                <img src="https://ui-avatars.com/api/?name=You&color=ffffff&background=6366f1" alt="You" class="w-8 h-8 rounded-full">
            `;
            
            messagesContainer.appendChild(messageElement);
            messageInput.value = '';
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // Send on button click
    document.querySelector('button:last-child').addEventListener('click', sendMessage);
    
    // Send on Enter (but allow Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
});
</script>
@endpush
@endsection
