<div class="filter- container mx-auto px-4 mx-auto">
    <label for="filter-{{ $filter->getKey()  }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $filter->getName()  }}</label>
    <select id="filter-{{ $filter->getKey()  }}" 
            wire:model.live="filters.{{ $filter->getKey()  }}" 
            class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 -sm">
        <option value="">{{ __('messages.common.select')  }}</option>
        @foreach($filter->getOptions() as $key => $value)
            <option value="{{ $key  }}">{{ $value  }}</option>
        @endforeach
    </select>
</div> 