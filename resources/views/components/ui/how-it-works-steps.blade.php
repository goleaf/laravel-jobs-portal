@php
$steps = [
  ['icon' => 'identification', 'title' => __('home.step_create_profile'), 'desc' => __('home.step_create_profile_desc')],
  ['icon' => 'magnifying-glass', 'title' => __('home.step_find_jobs'), 'desc' => __('home.step_find_jobs_desc')],
  ['icon' => 'paper-airplane', 'title' => __('home.step_apply'), 'desc' => __('home.step_apply_desc')],
  ['icon' => 'briefcase', 'title' => __('home.step_get_hired'), 'desc' => __('home.step_get_hired_desc')],
];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
  @foreach($steps as $index => $step)
  <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-4">
      <div class="p-3 rounded-lg bg-secondary-100 dark:bg-secondary-900 text-secondary-600 dark:text-secondary-300">
        <x-icon :name="$step['icon']" class="h-6 w-6" />
      </div>
      <div class="text-3xl font-extrabold text-gray-300 dark:text-gray-600">0{{ $index + 1 }}</div>
    </div>
    <h3 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">{{ $step['title'] }}</h3>
    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $step['desc'] }}</p>
  </div>
  @endforeach
</div>
