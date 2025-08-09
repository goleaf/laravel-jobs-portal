@props(['companies'])

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  @forelse($companies as $company)
    <x-companies.company-card :company="$company" />
  @empty
    <x-ui.empty-state :title="__('companies.no_featured')" :description="__('companies.check_back_later')" icon="building-office">
      <x-slot:actions>
        <x-ui.button href="{{ route('companies.index') }}" variant="primary">{{ __('companies.browse_all') }}</x-ui.button>
      </x-slot:actions>
    </x-ui.empty-state>
  @endforelse
</div>
