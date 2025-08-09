@props([
  'action' => route('jobs.index'),
  'class' => ''
])

<form method="GET" action="{{ $action }}" {{ $attributes->merge(['class' => 'flex w-full '.$class]) }}>
  <label for="q" class="sr-only">{{ __('jobs.search_keywords') }}</label>
  <input id="q" name="q" type="text" value="{{ request('q') }}" placeholder="{{ __('jobs.search_placeholder') }}" class="flex-1 px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500" />

  <label for="location" class="sr-only">{{ __('jobs.location') }}</label>
  <input id="location" name="location" type="text" value="{{ request('location') }}" placeholder="{{ __('jobs.location_placeholder') }}" class="hidden md:block w-56 px-4 py-3 text-gray-900 placeholder-gray-500 border border-l-0 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500" />

  <button type="submit" class="px-5 md:px-6 bg-blue-800 hover:bg-blue-900 text-white font-semibold">{{ __('common.search') }}</button>
</form>
