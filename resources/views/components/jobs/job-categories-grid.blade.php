@props(['categories'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
  @forelse($categories as $category)
    <a href="{{ route('jobs.index', ['category' => $category->slug ?? $category->id]) }}" class="block group bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition">
      <div class="flex items-center gap-4">
        <div class="p-3 rounded-lg bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-300">
          <x-icon name="briefcase" class="h-6 w-6" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $category->name }}</h3>
          @if(isset($category->jobs_count))
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ trans_choice('jobs.count', $category->jobs_count, ['count' => $category->jobs_count]) }}</p>
          @endif
        </div>
      </div>
    </a>
  @empty
    <x-ui.empty-state :title="__('jobs.no_categories')" :description="__('jobs.check_back_later')" icon="briefcase" />
  @endforelse
</div>
