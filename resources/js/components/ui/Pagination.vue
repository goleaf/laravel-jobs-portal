<template>
  <nav
    v-if="totalPages > 1"
    class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
    :class="containerClasses"
  >
    <!-- Mobile Pagination -->
    <div class="flex flex-1 justify-between sm:hidden">
      <button
        @click="previousPage"
        :disabled="currentPage === 1"
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Previous
      </button>
      <button
        @click="nextPage"
        :disabled="currentPage === totalPages"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Next
      </button>
    </div>

    <!-- Desktop Pagination -->
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <!-- Results Info -->
      <div>
        <p class="text-sm text-gray-700">
          Showing
          <span class="font-medium">{{ startItem }}</span>
          to
          <span class="font-medium">{{ endItem }}</span>
          of
          <span class="font-medium">{{ total.toLocaleString() }}</span>
          {{ itemName }}{{ total !== 1 ? 's' : '' }}
        </p>
      </div>

      <!-- Pagination Controls -->
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <!-- Previous Button -->
          <button
            @click="previousPage"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span class="sr-only">Previous</span>
            <ChevronLeftIcon class="h-5 w-5" />
          </button>

          <!-- First Page -->
          <button
            v-if="showFirstPage"
            @click="goToPage(1)"
            :class="getPageClasses(1)"
          >
            1
          </button>

          <!-- First Ellipsis -->
          <span
            v-if="showFirstEllipsis"
            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
          >
            ...
          </span>

          <!-- Page Numbers -->
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="getPageClasses(page)"
          >
            {{ page }}
          </button>

          <!-- Last Ellipsis -->
          <span
            v-if="showLastEllipsis"
            class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
          >
            ...
          </span>

          <!-- Last Page -->
          <button
            v-if="showLastPage"
            @click="goToPage(totalPages)"
            :class="getPageClasses(totalPages)"
          >
            {{ totalPages }}
          </button>

          <!-- Next Button -->
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span class="sr-only">Next</span>
            <ChevronRightIcon class="h-5 w-5" />
          </button>
        </nav>
      </div>
    </div>

    <!-- Page Size Selector -->
    <div v-if="showPageSizeSelector" class="hidden lg:flex items-center space-x-2 ml-6">
      <label for="page-size" class="text-sm text-gray-700">Show:</label>
      <select
        id="page-size"
        v-model="selectedPageSize"
        @change="handlePageSizeChange"
        class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      >
        <option
          v-for="size in pageSizeOptions"
          :key="size"
          :value="size"
        >
          {{ size }}
        </option>
      </select>
      <span class="text-sm text-gray-700">per page</span>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/24/outline'

interface Props {
  currentPage: number
  totalPages: number
  total: number
  perPage?: number
  itemName?: string
  maxVisiblePages?: number
  showPageSizeSelector?: boolean
  pageSizeOptions?: number[]
  variant?: 'default' | 'simple'
}

const props = withDefaults(defineProps<Props>(), {
  perPage: 20,
  itemName: 'result',
  maxVisiblePages: 7,
  showPageSizeSelector: false,
  pageSizeOptions: () => [10, 20, 50, 100],
  variant: 'default'
})

const emit = defineEmits<{
  'page-change': [page: number]
  'page-size-change': [pageSize: number]
}>()

// State
const selectedPageSize = ref(props.perPage)

// Computed
const startItem = computed(() => {
  return (props.currentPage - 1) * props.perPage + 1
})

const endItem = computed(() => {
  return Math.min(props.currentPage * props.perPage, props.total)
})

const containerClasses = computed(() => {
  return props.variant === 'simple' ? 'rounded-md' : ''
})

// Pagination logic
const visiblePages = computed(() => {
  const pages: number[] = []
  const maxVisible = props.maxVisiblePages
  const total = props.totalPages
  const current = props.currentPage

  if (total <= maxVisible) {
    // Show all pages if total is less than max visible
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    // Calculate start and end of visible range
    let start = Math.max(1, current - Math.floor(maxVisible / 2))
    let end = Math.min(total, start + maxVisible - 1)

    // Adjust start if we're near the end
    if (end - start + 1 < maxVisible) {
      start = Math.max(1, end - maxVisible + 1)
    }

    for (let i = start; i <= end; i++) {
      pages.push(i)
    }
  }

  return pages
})

const showFirstPage = computed(() => {
  return !visiblePages.value.includes(1) && props.totalPages > 1
})

const showLastPage = computed(() => {
  return !visiblePages.value.includes(props.totalPages) && props.totalPages > 1
})

const showFirstEllipsis = computed(() => {
  return showFirstPage.value && visiblePages.value[0] > 2
})

const showLastEllipsis = computed(() => {
  return showLastPage.value && visiblePages.value[visiblePages.value.length - 1] < props.totalPages - 1
})

// Methods
const getPageClasses = (page: number) => {
  const baseClasses = 'relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0'
  
  if (page === props.currentPage) {
    return `${baseClasses} z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600`
  }
  
  return `${baseClasses} text-gray-900 hover:bg-gray-50`
}

const goToPage = (page: number) => {
  if (page >= 1 && page <= props.totalPages && page !== props.currentPage) {
    emit('page-change', page)
  }
}

const previousPage = () => {
  if (props.currentPage > 1) {
    goToPage(props.currentPage - 1)
  }
}

const nextPage = () => {
  if (props.currentPage < props.totalPages) {
    goToPage(props.currentPage + 1)
  }
}

const handlePageSizeChange = () => {
  emit('page-size-change', selectedPageSize.value)
}

// Watch for prop changes
watch(() => props.perPage, (newValue) => {
  selectedPageSize.value = newValue
})
</script> 