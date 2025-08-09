@props(['company'])

<div class="bg-white dark:bg-gray-800 rounded-lg border border-yellow-300 dark:border-yellow-600 p-5 h-full flex flex-col">
  <div class="flex items-center gap-3">
    <x-icon name="star" class="h-5 w-5 text-yellow-500" />
    <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $company->name }}</h3>
  </div>
  <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">{{ $company->details ?? '' }}</p>
  <div class="mt-4">
    <x-ui.button href="{{ route('companies.show', $company->id) }}" variant="primary">{{ __('companies.view_company') }}</x-ui.button>
  </div>
</div>
