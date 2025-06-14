<template>
  <div class="enhanced-data-table relative">
    <!-- Table Header -->
    <div class="bg-white shadow rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-medium text-gray-900">{{ title }}</h3>
          <div class="flex items-center space-x-3">
            <!-- Search Input -->
            <div class="relative" v-if="searchable">
              <input
                v-model="searchQuery"
                type="text"
                :placeholder="$t('common.search')"
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
            <!-- Add Button -->
            <button
              v-if="addable"
              @click="$emit('add-item')"
              class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
              <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              {{ $t('common.add') }}
            </button>
          </div>
        </div>
      </div>

      <!-- Table Content -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th
                v-for="column in columns"
                :key="column.key"
                scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                @click="sortBy(column.key)"
              >
                <div class="flex items-center space-x-1">
                  <span>{{ column.label }}</span>
                  <div class="flex flex-col" v-if="column.sortable">
                    <svg class="h-3 w-3 text-gray-400" :class="{ 'text-indigo-600': sortColumn === column.key && sortDirection === 'asc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                    </svg>
                    <svg class="h-3 w-3 text-gray-400 -mt-1" :class="{ 'text-indigo-600': sortColumn === column.key && sortDirection === 'desc' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </div>
                </div>
              </th>
              <th scope="col" class="relative px-6 py-3" v-if="hasActions">
                <span class="sr-only">{{ $t('common.actions') }}</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="(item, index) in paginatedItems"
              :key="getItemId(item, index)"
              class="hover:bg-gray-50 transition-colors duration-150"
            >
              <td
                v-for="column in columns"
                :key="column.key"
                class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
              >
                <!-- Custom slot for column content -->
                <slot
                  :name="`column-${column.key}`"
                  :item="item"
                  :value="getColumnValue(item, column.key)"
                  :index="index"
                >
                  <!-- Default column rendering -->
                  <span>{{ getColumnValue(item, column.key) }}</span>
                </slot>
              </td>
              <!-- Actions Column -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" v-if="hasActions">
                <div class="flex items-center justify-end space-x-2">
                  <!-- Edit Button -->
                  <button
                    v-if="editable"
                    @click="$emit('edit-item', item)"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                    :title="$t('common.edit')"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <!-- Delete Button -->
                  <button
                    v-if="deletable"
                    @click="confirmDelete(item)"
                    class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                    :title="$t('common.delete')"
                    :disabled="deleting"
                  >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                  <!-- Custom Actions Slot -->
                  <slot name="actions" :item="item" :index="index" />
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6" v-if="paginated && totalPages > 1">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ $t('common.previous') }}
          </button>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ $t('common.next') }}
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              {{ $t('common.showing') }}
              <span class="font-medium">{{ startIndex + 1 }}</span>
              {{ $t('common.to') }}
              <span class="font-medium">{{ endIndex }}</span>
              {{ $t('common.of') }}
              <span class="font-medium">{{ filteredItems.length }}</span>
              {{ $t('common.results') }}
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="previousPage"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                  page === currentPage
                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div
      v-if="loading"
      class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg"
    >
      <div class="flex items-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <span class="ml-2 text-sm text-gray-600">{{ $t('common.loading') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch, useSlots } from 'vue'

// Enhanced Interfaces
interface EnhancedColumn {
  key: string
  label: string
  type?: 'text' | 'date' | 'badge' | 'switch' | 'image' | 'custom'
  sortable?: boolean
  searchable?: boolean
  formatter?: (value: any) => string
}

interface EnhancedTableProps {
  title: string
  items: any[]
  columns: EnhancedColumn[]
  searchable?: boolean
  addable?: boolean
  editable?: boolean
  deletable?: boolean
  paginated?: boolean
  pageSize?: number
  loading?: boolean
  deleteEndpoint?: string
}

// Props & Emits
const props = withDefaults(defineProps<EnhancedTableProps>(), {
  searchable: true,
  addable: true,
  editable: true,
  deletable: true,
  paginated: true,
  pageSize: 10,
  loading: false
})

const emit = defineEmits<{
  'add-item': []
  'edit-item': [item: any]
  'delete-item': [item: any]
  'refresh': []
}>()

// Reactive State
const searchQuery = ref('')
const sortColumn = ref('')
const sortDirection = ref<'asc' | 'desc'>('asc')
const currentPage = ref(1)
const deleting = ref(false)

// Computed Properties
const filteredItems = computed(() => {
  let filtered = [...props.items]

  // Apply search filter
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(item =>
      props.columns.some(column => {
        if (column.searchable !== false) {
          const value = getColumnValue(item, column.key)
          return String(value).toLowerCase().includes(query)
        }
        return false
      })
    )
  }

  // Apply sorting
  if (sortColumn.value) {
    filtered.sort((a, b) => {
      const aValue = getColumnValue(a, sortColumn.value)
      const bValue = getColumnValue(b, sortColumn.value)
      
      if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
      if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
      return 0
    })
  }

  return filtered
})

const paginatedItems = computed(() => {
  if (!props.paginated) return filteredItems.value
  
  const start = (currentPage.value - 1) * props.pageSize
  const end = start + props.pageSize
  return filteredItems.value.slice(start, end)
})

const totalPages = computed(() => 
  Math.ceil(filteredItems.value.length / props.pageSize)
)

const startIndex = computed(() => 
  (currentPage.value - 1) * props.pageSize
)

const endIndex = computed(() => 
  Math.min(startIndex.value + props.pageSize, filteredItems.value.length)
)

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

const slots = useSlots()
const hasActions = computed(() => 
  props.editable || props.deletable || !!slots.actions
)

// Methods
const getColumnValue = (item: any, key: string) => {
  return key.split('.').reduce((obj, k) => obj?.[k], item)
}

const getItemId = (item: any, index: number) => {
  return item.id || item.uuid || index
}

const sortBy = (column: string) => {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'asc'
  }
}

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const goToPage = (page: number) => {
  currentPage.value = page
}

const confirmDelete = async (item: any) => {
  if (deleting.value) return

  // Simple confirmation for now - can be enhanced with SweetAlert2
  const confirmed = confirm('Are you sure you want to delete this item?')

  if (confirmed) {
    await performDelete(item)
  }
}

const performDelete = async (item: any) => {
  deleting.value = true

  try {
    if (props.deleteEndpoint) {
      // Make API call to delete
      const response = await fetch(props.deleteEndpoint.replace(':id', item.id), {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('Delete failed')
      }

      emit('refresh')
    } else {
      // Emit delete event for parent to handle
      emit('delete-item', item)
    }
  } catch (error) {
    console.error('Delete error:', error)
    alert('Delete failed. Please try again.')
  } finally {
    deleting.value = false
  }
}

// Watch for items changes to reset pagination
watch(() => props.items, () => {
  currentPage.value = 1
})

// Watch for search changes to reset pagination
watch(searchQuery, () => {
  currentPage.value = 1
})
</script>

<style scoped>
.enhanced-data-table {
  @apply relative;
}
</style> 