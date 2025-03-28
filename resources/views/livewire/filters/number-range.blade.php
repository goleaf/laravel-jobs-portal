<div x-data="{
    min: '{{ $activeFilters[$name]['min'] ?? '' }}',
    max: '{{ $activeFilters[$name]['max'] ?? '' }}',
    updateFilter() {
        $wire.setFilter('{{ $name }}', { min: this.min, max: this.max });
    }
}">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
    </label>
    
    <div class="flex flex-col space-y-2">
        <div class="flex items-center space-x-2">
            <div class="w-full">
                <label for="filter-{{ $name }}-min" class="block text-xs font-medium text-gray-500 mb-1">
                    {{ __('Min') }}
                </label>
                <input
                    type="number"
                    id="filter-{{ $name }}-min"
                    x-model="min"
                    @change="updateFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
            </div>
            
            <div class="w-full">
                <label for="filter-{{ $name }}-max" class="block text-xs font-medium text-gray-500 mb-1">
                    {{ __('Max') }}
                </label>
                <input
                    type="number"
                    id="filter-{{ $name }}-max"
                    x-model="max"
                    @change="updateFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
            </div>
        </div>
        
        @if(!empty($activeFilters[$name]['min']) || !empty($activeFilters[$name]['max']))
            <div class="flex justify-end">
                <button
                    type="button"
                    @click="min = ''; max = ''; updateFilter()"
                    class="text-xs text-blue-600 hover:text-blue-800"
                >
                    {{ __('Clear') }}
                </button>
            </div>
        @endif
    </div>
</div> 