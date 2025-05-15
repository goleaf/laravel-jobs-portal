<div class="filter-container">
    <label for="filter-{{ $filter->getKey() }}" class="form-label">{{ $filter->getName() }}</label>
    <select id="filter-{{ $filter->getKey() }}" 
            wire:model.live="filters.{{ $filter->getKey() }}" 
            class="form-select form-select-sm">
        <option value="">{{ __('messages.common.select') }}</option>
        @foreach($filter->getOptions() as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
</div> 