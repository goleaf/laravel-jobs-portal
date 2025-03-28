<div class="filter-container">
    <label for="filter-{{ $filter->getKey() }}" class="form-label">{{ $filter->getName() }}</label>
    <div class="position-relative">
        <select id="filter-{{ $filter->getKey() }}" 
                wire:model.live="filters.{{ $filter->getKey() }}" 
                class="form-select form-select-sm" 
                multiple>
            @foreach($filter->getOptions() as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach
        </select>
        @if(!empty($filters[$filter->getKey()]))
            <button 
                wire:click="resetFilter('{{ $filter->getKey() }}')" 
                class="btn btn-sm btn-link position-absolute top-0 end-0 text-danger"
                title="{{ __('messages.common.clear') }}">
                <i class="fa fa-times"></i>
            </button>
        @endif
    </div>
</div> 