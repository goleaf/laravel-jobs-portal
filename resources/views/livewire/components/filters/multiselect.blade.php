<div class="filter- container mx-auto">
    <label for="filter-{{ $filter->getKey() }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $filter->getName() }}</label>
    <div class="position-relative">
        <select id="filter-{{ $filter->getKey() }}" 
                wire:model.live="filters.{{ $filter->getKey() }}" 
                class="form-select w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -sm" 
                multiple>
            @foreach($filter->getOptions() as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        @if(!empty($filters[$filter->getKey()]))
            <button 
                wire:click="resetFilter('{{ $filter->getKey() }}')" 
                class="btn px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -link position-absolute top-0 end-0 text-red-600"
                title="{{ __('messages.common.clear') }}">
                <i class="fa fa-times"></i>
            </button>
        @endif
    </div>
</div> 