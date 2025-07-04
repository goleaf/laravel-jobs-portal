<template>
  <nav 
    class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6" 
    aria-label="Pagination"
  >
    <div class="hidden sm:block">
      <p class="text-sm text-gray-700">
        Showing
        <span class="font-medium">{{ startItem }}</span>
        to
        <span class="font-medium">{{ endItem }}</span>
        of
        <span class="font-medium">{{ totalItems }}</span>
        results
      </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end">
      <button
        :disabled="currentPage === 1"
        :class="[
          'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50',
          currentPage === 1 ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
        ]"
        @click="$emit('page-change', currentPage - 1)"
      >
        Previous
      </button>
      
      <div class="flex items-center mx-4 space-x-2">
        <template v-for="page in visiblePages" :key="page">
          <button
            v-if="typeof page === 'number'"
            :class="[
              'relative inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md',
              page === currentPage 
                ? 'z-10 bg-blue-500 border-blue-500 text-white' 
                : 'border-gray-300 text-gray-500 hover:bg-gray-50'
            ]"
            @click="$emit('page-change', page)"
          >
            {{ page }}
          </button>
          <span 
            v-else 
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
          >
            ...
          </span>
        </template>
      </div>
      
      <button
        :disabled="currentPage === totalPages"
        :class="[
          'relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50',
          currentPage === totalPages ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
        ]"
        @click="$emit('page-change', currentPage + 1)"
      >
        Next
      </button>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  currentPage: number
  totalItems: number
  itemsPerPage: number
  maxVisiblePages?: number
}

const props = withDefaults(defineProps<Props>(), {
  maxVisiblePages: 5
})

const emit = defineEmits(['page-change'])

const totalPages = computed(() => Math.ceil(props.totalItems / props.itemsPerPage))

const startItem = computed(() => (props.currentPage - 1) * props.itemsPerPage + 1)
const endItem = computed(() => Math.min(props.currentPage * props.itemsPerPage, props.totalItems))

const visiblePages = computed(() => {
  const { currentPage, maxVisiblePages } = props
  const total = totalPages.value

  // If total pages is less than or equal to max visible pages, show all
  if (total <= maxVisiblePages) {
    return Array.from({ length: total }, (_, i) => i + 1)
  }

  const halfVisible = Math.floor(maxVisiblePages / 2)
  let start = Math.max(1, currentPage - halfVisible)
  let end = Math.min(total, start + maxVisiblePages - 1)

  // Adjust start and end to maintain consistent number of pages
  if (end - start + 1 < maxVisiblePages) {
    start = Math.max(1, end - maxVisiblePages + 1)
  }

  const pages: (number | string)[] = []

  // First page
  if (start > 1) {
    pages.push(1)
    if (start > 2) pages.push('...')
  }

  // Middle pages
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }

  // Last page
  if (end < total) {
    if (end < total - 1) pages.push('...')
    pages.push(total)
  }

  return pages
})
</script>

<style scoped>
/* Additional scoped styles if needed */
</style> 