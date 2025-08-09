@props(['company', 'layout' => 'grid'])

@php
  $wrapper = $layout === 'grid' ? 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5 h-full flex flex-col' : 'bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-5';
@endphp

<div class="{{ $wrapper }}">
  <div class="flex items-center gap-4">
    <div class="shrink-0 h-12 w-12 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
      <x-icon name="building-office" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
    </div>
    <div class="min-w-0">
      <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $company->name }}</h3>
      <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $company->location ?? '-' }}</p>
    </div>
  </div>

  <div class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-3">
    {{ $company->details ?? $company->description ?? '' }}
  </div>

  <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
    <span>{{ __('companies.jobs_count_short', ['count' => $company->jobs_count ?? 0]) }}</span>
    <x-ui.button href="{{ route('companies.show', $company->id) }}" variant="link">{{ __('companies.view_company') }}</x-ui.button>
  </div>
</div>
