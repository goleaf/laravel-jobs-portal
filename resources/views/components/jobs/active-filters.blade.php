@props(['filters' => []])

@if($filters && count($filters) > 0)
  <div class="flex flex-wrap items-center gap-3">
    @foreach($filters as $label => $value)
      <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-sm text-gray-700 dark:text-gray-200">
        <span class="font-medium">{{ $label }}:</span>
        <span>{{ is_array($value) ? implode(', ', $value) : $value }}</span>
        <a href="{{ request()->fullUrlWithQuery([$label => null]) }}" class="ml-1 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="{{ __('ui.remove') }}">
          <x-icon name="x-mark" class="h-4 w-4" />
        </a>
      </span>
    @endforeach
    <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-3 py-1 rounded-full bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-200 text-sm hover:bg-primary-200 dark:hover:bg-primary-800">
      {{ __('jobs.clear_all') }}
    </a>
  </div>
@endif
