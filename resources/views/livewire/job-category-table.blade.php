<div>
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <!-- Table Header -->
        <div class="px-4 py-5 sm:px-6 flex flex-wrap items-center justify-between bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                {{ __('messages.job_category.job_categories') }}
            </h3>
            
            <div class="flex space-x-3">
                <div>
                    <button wire:click="$dispatch('showJobCategoryModal')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('messages.common.add') }}
                    </button>
                </div>
                
                <div>
                    <button x-data type="button" x-on:click="$refs.filterPanel.classList.toggle('hidden')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        {{ __('messages.common.filter') }}
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Filters Panel -->
        <div x-ref="filterPanel" class="hidden px-4 py-3 border-t border-b border-gray-200 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">{{ __('messages.common.search') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input 
                            type="text" 
                            id="search" 
                            wire:model.live.debounce.300ms="search" 
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md" 
                            placeholder="{{ __('messages.common.search') }}"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
                
                <!-- Featured Filter -->
                <div>
                    <label for="featured" class="block text-sm font-medium text-gray-700">{{ __('messages.job_category.is_featured') }}</label>
                    <select 
                        id="featured" 
                        wire:model.live="filters.featured" 
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option value="">{{ __('messages.common.all') }}</option>
                        <option value="1">{{ __('messages.common.yes') }}</option>
                        <option value="0">{{ __('messages.common.no') }}</option>
                    </select>
                </div>
                
                <!-- Date Range Filter -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">{{ __('messages.common.date_range') }}</label>
                    <div class="mt-1 grid grid-cols-2 gap-2">
                        <div>
                            <input 
                                type="date" 
                                wire:model.live="filters.date_range.start" 
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            >
                        </div>
                        <div>
                            <input 
                                type="date" 
                                wire:model.live="filters.date_range.end" 
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                            >
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 flex justify-end">
                <button 
                    type="button" 
                    wire:click="resetFilters" 
                    class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    {{ __('messages.common.reset') }}
                </button>
            </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('name')">
                            <div class="flex items-center">
                                {{ __('messages.job_category.name') }}
                                @if($sortField === 'name')
                                    <svg class="h-4 w-4 ml-1 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('is_featured')">
                            <div class="flex items-center">
                                {{ __('messages.job_category.is_featured') }}
                                @if($sortField === 'is_featured')
                                    <svg class="h-4 w-4 ml-1 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" wire:click="sortBy('created_at')">
                            <div class="flex items-center">
                                {{ __('messages.common.created_date') }}
                                @if($sortField === 'created_at')
                                    <svg class="h-4 w-4 ml-1 {{ $sortDirection === 'asc' ? '' : 'transform rotate-180' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('messages.common.action') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobCategories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($category->image_url)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full shadow object-cover" src="{{ $category->image_url }}" alt="{{ $category->name }}">
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 hover:text-blue-600 cursor-pointer" wire:click="$dispatch('editJobCategory', {id: {{ $category->id }}})">
                                            {{ $category->name }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($category->is_featured)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ __('messages.common.yes') }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ __('messages.common.no') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->created_at->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    <button wire:click="$dispatch('editJobCategory', {id: {{ $category->id }}})" class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="$dispatch('deleteJobCategory', {id: {{ $category->id }}})" class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-500 text-center">
                                {{ __('messages.common.no_data_available') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 bg-white">
            {{ $jobCategories->links() }}
        </div>
    </div>
    
    <!-- Job Category Form Modal -->
    @livewire('job-category-form')
</div> 