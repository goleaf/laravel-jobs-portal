@props(['stats' => null])

@php
$stats = $stats ?? [
  ['icon' => 'users', 'label' => __('home.stats_candidates'), 'value' => '24,582'],
  ['icon' => 'briefcase', 'label' => __('home.stats_jobs'), 'value' => '1,203'],
  ['icon' => 'building-office', 'label' => __('home.stats_companies'), 'value' => '534'],
  ['icon' => 'map-pin', 'label' => __('home.stats_locations'), 'value' => '128'],
];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
  @foreach($stats as $stat)
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex items-center gap-4">
    <div class="p-3 rounded-lg bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300">
      <x-icon :name="$stat['icon']" class="h-6 w-6" />
    </div>
    <div>
      <div class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $stat['value'] }}</div>
      <div class="text-sm text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</div>
    </div>
  </div>
  @endforeach
</div>
