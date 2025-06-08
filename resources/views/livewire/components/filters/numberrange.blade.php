<div class="filter- max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
    <label for="filter-{{ $filter->getKey() }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $filter->getName() }}</label>
    <div class="flex items-center">
        <input type="number" 
               wire:model.live="filters.{{ $filter->getKey() }}.min" 
               id="filter-{{ $filter->getKey() }}-min"
               class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 sm me-2" 
               placeholder="{{ __('messages.common.min') }}">

        <input type="number" 
               wire:model.live="filters.{{ $filter->getKey() }}.max" 
               id="filter-{{ $filter->getKey() }}-max"
               class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 sm" 
               placeholder="{{ __('messages.common.max') }}">
    </div>
    @if(!empty($filters[$filter->getKey()]))
        <button 
            wire:click="resetFilter('{{ $filter->getKey() }}')" 
            class="border border-gray-300 bg-transparent"
            title="{{ __('messages.common.clear') }}">
            <i class="fa fa-times"></i>
        </button>
    @endif
</div> 