@props([
  'id' => 'share-modal',
  'url' => url('/'),
  'title' => '',
  'description' => '',
])

<x-ui.modal :id="$id" :title="__('ui.share')" size="md">
  <div class="space-y-4">
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.link') }}</label>
      <div class="mt-1 flex rounded-md shadow-sm">
        <input type="text" readonly class="flex-1 block w-full rounded-none rounded-l-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" value="{{ $url }}">
        <button type="button" class="-ml-px inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-r-md hover:bg-gray-100 dark:hover:bg-gray-600" onclick="navigator.clipboard.writeText('{{ $url }}')">
          <x-icon name="check" class="h-4 w-4" />
          <span>{{ __('ui.copy') }}</span>
        </button>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <a target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}&text={{ urlencode($title) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-blue-500 text-white hover:bg-blue-600">
        X/Twitter
      </a>
      <a target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}" class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-blue-700 text-white hover:bg-blue-800">
        Facebook
      </a>
    </div>
  </div>

  @slot('footer')
    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-gray-100 px-4 py-2 text-gray-700 hover:bg-gray-200 sm:mt-0 sm:w-auto" data-modal-close="{{ $id }}">{{ __('ui.close') }}</button>
  @endslot
</x-ui.modal>
