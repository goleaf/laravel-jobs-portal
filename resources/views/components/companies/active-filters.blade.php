@props(['filters' => []])
@if(!empty($filters))
  <div class="flex flex-wrap items-center gap-2">
    @foreach($filters as $label => $value)
      @if(filled($value))
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
          {{ __($label) }}: {{ is_array($value) ? implode(', ', $value) : $value }}
        </span>
      @endif
    @endforeach
    <x-ui.button href="{{ route('companies.index') }}" variant="link">{{ __('companies.clear_filters') }}</x-ui.button>
  </div>
@endif
