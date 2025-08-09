<aside class="space-y-6">
  <div>
    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('companies.industry') }}</h3>
    <ul class="space-y-1 max-h-56 overflow-auto">
      @foreach(($industries ?? []) as $id => $name)
        <li>
          <a href="{{ request()->fullUrlWithQuery(['industry' => $id]) }}" class="text-sm {{ request('industry') == $id ? 'text-blue-600 dark:text-blue-300 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
            {{ $name }}
          </a>
        </li>
      @endforeach
    </ul>
  </div>

  <div>
    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('companies.size') }}</h3>
    <ul class="space-y-1 max-h-56 overflow-auto">
      @foreach(($companySizes ?? []) as $id => $name)
        <li>
          <a href="{{ request()->fullUrlWithQuery(['size' => $id]) }}" class="text-sm {{ request('size') == $id ? 'text-blue-600 dark:text-blue-300 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
            {{ $name }}
          </a>
        </li>
      @endforeach
    </ul>
  </div>

  <div>
    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('companies.location') }}</h3>
    <ul class="space-y-1 max-h-56 overflow-auto">
      @foreach(($locations ?? []) as $id => $name)
        <li>
          <a href="{{ request()->fullUrlWithQuery(['location' => $id]) }}" class="text-sm {{ request('location') == $id ? 'text-blue-600 dark:text-blue-300 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
            {{ $name }}
          </a>
        </li>
      @endforeach
    </ul>
  </div>
</aside>
