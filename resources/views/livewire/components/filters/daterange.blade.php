<div class="filter- container mx-auto">
    <label for="filter-{{ $filter->getKey() }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $filter->getName() }}</label>
    <div class="flex items-center">
        <input type="date" 
               wire:model.live="filters.{{ $filter->getKey() }}.from" 
               id="filter-{{ $filter->getKey() }}-from"
               class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -sm me-2" 
               placeholder="{{ __('messages.common.from_date') }}">

        <input type="date" 
               wire:model.live="filters.{{ $filter->getKey() }}.to" 
               id="filter-{{ $filter->getKey() }}-to"
               class="form-control w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -sm" 
               placeholder="{{ __('messages.common.to_date') }}">
    </div>
    @if(!empty($filters[$filter->getKey()]))
        <button 
            wire:click="resetFilter('{{ $filter->getKey() }}')" 
            class="btn px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -link position-absolute top-0 end-0 text-red-600"
            title="{{ __('messages.common.clear') }}">
            <i class="fa fa-times"></i>
        </button>
    @endif
</div> 