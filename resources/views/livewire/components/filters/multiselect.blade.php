<div class="filter- max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mx-auto px-4 mx-auto px-4 mx-auto px-4 mx-auto">
    <label for="filter-{{ $filter->getKey() }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $filter->getName() }}</label>
    <div class="relative">
        <select id="filter-{{ $filter->getKey() }}" 
                wire:model.live="filters.{{ $filter->getKey() }}" 
                class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 sm" 
                multiple>
            @foreach($filter->getOptions() as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        @if(!empty($filters[$filter->getKey()]))
            <button 
                wire:click="resetFilter('{{ $filter->getKey() }}')" 
                class="border border-gray-300 bg-transparent"
                title="{{ __('messages.common.clear') }}">
                <i class="fa fa-times"></i>
            </button>
        @endif
    </div>
</div> 