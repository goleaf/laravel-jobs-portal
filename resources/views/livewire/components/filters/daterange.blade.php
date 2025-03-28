<div class="filter-container">
    <label for="filter-{{ $filter->getKey() }}" class="form-label">{{ $filter->getName() }}</label>
    <div class="d-flex align-items-center">
        <input type="date" 
               wire:model.live="filters.{{ $filter->getKey() }}.from" 
               id="filter-{{ $filter->getKey() }}-from"
               class="form-control form-control-sm me-2" 
               placeholder="{{ __('messages.common.from_date') }}">

        <input type="date" 
               wire:model.live="filters.{{ $filter->getKey() }}.to" 
               id="filter-{{ $filter->getKey() }}-to"
               class="form-control form-control-sm" 
               placeholder="{{ __('messages.common.to_date') }}">
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