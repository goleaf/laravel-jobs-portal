@props(['icon' => 'information-circle', 'title' => '', 'description' => ''])

<div class="text-center bg-white dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8">
  <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700">
    <x-icon :name="$icon" class="h-6 w-6 text-gray-500 dark:text-gray-300" />
  </div>
  <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
  <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $description }}</p>
  <div class="mt-6">
    {{ $slot }}
  </div>
</div>
