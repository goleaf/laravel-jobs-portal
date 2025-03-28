<div 
    x-data="{ 
        notifications: [],
        add(message, type = 'success') {
            const id = Date.now();
            this.notifications.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(notification => notification.id !== id);
        }
    }"
    @show-notification.window="add($event.detail.message, $event.detail.type)"
    class="fixed top-5 right-5 z-50 flex flex-col space-y-3 w-80">
    
    <template x-for="notification in notifications" :key="notification.id">
        <div 
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-8"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-8"
            :class="{
                'bg-green-50 border-green-400 text-green-800': notification.type === 'success',
                'bg-red-50 border-red-400 text-red-800': notification.type === 'error',
                'bg-blue-50 border-blue-400 text-blue-800': notification.type === 'info',
                'bg-yellow-50 border-yellow-400 text-yellow-800': notification.type === 'warning'
            }"
            class="relative border-l-4 p-4 rounded-md shadow-md">
            
            <div class="flex items-start justify-between">
                <p class="text-sm" x-text="notification.message"></p>
                <button 
                    @click="remove(notification.id)" 
                    class="ml-4 inline-flex text-gray-400 hover:text-gray-500">
                    <span class="sr-only">{{ __('messages.common.close') }}</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div> 