@props(['categories' => [], 'locations' => [], 'companies' => [], 'filters' => []])

<form method="GET" action="{{ route('jobs.index') }}" class="space-y-4">
  <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('jobs.category') }}</label>
    <select name="category" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
      <option value="">{{ __('ui.any') }}</option>
      @foreach($categories as $category)
        <option value="{{ $category->slug ?? $category->id }}" {{ (request('category') == ($category->slug ?? $category->id)) ? 'selected' : '' }}>{{ $category->name }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('jobs.location') }}</label>
    <select name="location" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
      <option value="">{{ __('ui.any') }}</option>
      @foreach($locations as $loc)
        <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('jobs.company') }}</label>
    <select name="company" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
      <option value="">{{ __('ui.any') }}</option>
      @foreach($companies as $company)
        <option value="{{ $company->slug ?? $company->id }}" {{ request('company') == ($company->slug ?? $company->id) ? 'selected' : '' }}>{{ $company->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="flex justify-end gap-3">
    <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200">{{ __('jobs.clear_filters') }}</a>
    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-md bg-primary-600 text-white hover:bg-primary-700">{{ __('jobs.apply_filters') }}</button>
  </div>
</form>
