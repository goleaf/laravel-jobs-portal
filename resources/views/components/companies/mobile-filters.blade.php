@props(['industries' => [], 'companySizes' => [], 'locations' => [], 'filters' => []])

<form method="GET" action="{{ route('companies.index') }}" class="space-y-4">
  <div>
    <label for="q_m" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('companies.search_label') }}</label>
    <x-ui.input id="q_m" name="q" type="text" value="{{ request('q') }}" placeholder="{{ __('companies.search_placeholder') }}" />
  </div>
  <div>
    <label for="industry_m" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('companies.industry') }}</label>
    <x-ui.select id="industry_m" name="industry">
      <option value="">{{ __('common.all') }}</option>
      @foreach(($industries ?? []) as $id => $name)
        <option value="{{ $id }}" @selected(request('industry') == $id)>{{ $name }}</option>
      @endforeach
    </x-ui.select>
  </div>
  <div>
    <label for="size_m" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('companies.size') }}</label>
    <x-ui.select id="size_m" name="size">
      <option value="">{{ __('common.all') }}</option>
      @foreach(($companySizes ?? []) as $id => $name)
        <option value="{{ $id }}" @selected(request('size') == $id)>{{ $name }}</option>
      @endforeach
    </x-ui.select>
  </div>
  <div>
    <label for="location_m" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('companies.location') }}</label>
    <x-ui.select id="location_m" name="location">
      <option value="">{{ __('common.all') }}</option>
      @foreach(($locations ?? []) as $id => $name)
        <option value="{{ $id }}" @selected(request('location') == $id)>{{ $name }}</option>
      @endforeach
    </x-ui.select>
  </div>
  <div class="flex items-center gap-3">
    <x-ui.button type="submit" variant="primary">{{ __('common.apply_filters') }}</x-ui.button>
    <x-ui.button href="{{ route('companies.index') }}" variant="secondary">{{ __('common.clear') }}</x-ui.button>
  </div>
</form>
