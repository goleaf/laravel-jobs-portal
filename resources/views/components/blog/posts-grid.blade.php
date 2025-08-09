@props(['posts'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  @forelse($posts as $post)
    <article class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <a href="{{ route('blog.show', $post) }}" class="block">
        @if($post->cover_image_url)
          <img src="{{ $post->cover_image_url }}" alt="{{ $post->title }}" class="w-full h-48 object-cover" />
        @endif
        <div class="p-6">
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $post->title }}</h3>
          <p class="mt-2 text-gray-600 dark:text-gray-400 line-clamp-3">{{ Str::limit(strip_tags($post->excerpt ?? $post->content), 140) }}</p>
          <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ $post->created_at->diffForHumans() }}</div>
        </div>
      </a>
    </article>
  @empty
    <x-ui.empty-state :title="__('blog.no_posts')" :description="__('blog.check_back_later')" icon="document" />
  @endforelse
</div>
