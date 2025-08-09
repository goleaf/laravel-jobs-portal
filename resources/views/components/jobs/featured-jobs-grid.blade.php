@props(['jobs'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  @forelse($jobs as $job)
    <x-jobs.job-card :job="$job" />
  @empty
    <x-ui.empty-state :title="__('jobs.no_featured')" :description="__('jobs.check_back_later')" icon="briefcase">
      <x-slot:actions>
        <x-ui.button href="{{ route('jobs.index') }}" variant="primary">{{ __('jobs.browse_all') }}</x-ui.button>
      </x-slot:actions>
    </x-ui.empty-state>
  @endforelse
</div>
