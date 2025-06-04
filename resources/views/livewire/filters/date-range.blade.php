<div x-data="{
    from: '{{ $activeFilters[$name]['from'] ?? ''  }}',
    to: '{{ $activeFilters[$name]['to'] ?? ''  }}',
    updateFilter() {
        $wire.setFilter('{{ $name  }}', { from: this.from, to: this.to });
    }
}">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label  }}
    </label>
    
    <div class="flex flex- flex-1 space-y-2">
        <div class="flex items-center space-x-2">
            <div class="w-full">
                <label for="filter-{{ $name  }}-from" class="block text-xs font-medium text-gray-500 mb-1">
                    {{ __('From')  }}
                </label>
                <input
                    type="date"
                    id="filter-{{ $name  }}-from"
                    x-model="from"
                    @change="updateFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
            </div>
            
            <div class="w-full">
                <label for="filter-{{ $name  }}-to" class="block text-xs font-medium text-gray-500 mb-1">
                    {{ __('To')  }}
                </label>
                <input
                    type="date"
                    id="filter-{{ $name  }}-to"
                    x-model="to"
                    @change="updateFilter"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                >
            </div>
        </div>
        
        @if(!empty($activeFilters[$name]['from']) || !empty($activeFilters[$name]['to']))
            <div class="flex justify-end">
                <button
                    type="button"
                    @click="from = ''; to = ''; updateFilter()"
                    class="text-xs text-blue-600 hover:text-blue-800"
                >
                    {{ __('Clear')  }}
                </button>
            </div>
        @endif
    </div>
</div> 