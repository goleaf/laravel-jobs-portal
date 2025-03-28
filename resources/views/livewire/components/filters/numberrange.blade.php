<div class="filter-container">
    <label for="filter-{{ $filter->getKey() }}" class="form-label">{{ $filter->getName() }}</label>
    <div class="d-flex align-items-center">
        <input type="number" 
               wire:model.live="filters.{{ $filter->getKey() }}.min" 
               id="filter-{{ $filter->getKey() }}-min"
               class="form-control form-control-sm me-2" 
               placeholder="{{ __('messages.common.min') }}">

        <input type="number" 
               wire:model.live="filters.{{ $filter->getKey() }}.max" 
               id="filter-{{ $filter->getKey() }}-max"
               class="form-control form-control-sm" 
               placeholder="{{ __('messages.common.max') }}">
    </div>
    @if(!empty($filters[$filter->getKey()]))
        <button 
            wire:click="resetFilter('{{ $filter->getKey() }}')" 
            class="btn btn-sm btn-link position-absolute top-0 end-0 text-danger"
            title="{{ __('messages.common.clear') }}">
            <i class="fa fa-times"></i>
        </button>
    @endif
</div> 