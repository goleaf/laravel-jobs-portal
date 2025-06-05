@props(['count'])

<div class="bg-amber-100 text-amber-800 p-4 rounded-lg shadow-sm mb-6" x-data="{ show: true }" x-show="show">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <x-icons.alert class="h-6 w-6 mr-2" />
            <div>
                <div class="font-medium">{{ __('Translation Notice') }}</div>
                <p class="text-sm">
                    {{ __('This page contains :count untranslated strings. Please help translate them by using our translation tool.', ['count' => $count]) }}
                </p>
            </div>
        </div>
        <div>
            <button @click="show = false" class="text-amber-800 hover:text-amber-600">
                <span class="sr-only">{{ __('Dismiss') }}</span>
                <x-icons.x class="h-5 w-5" />
            </button>
        </div>
    </div>
</div> 