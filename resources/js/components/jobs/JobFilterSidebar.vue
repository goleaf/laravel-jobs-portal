<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
    <!-- Filter Header -->
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
        <button
          v-if="hasActiveFilters"
          @click="clearAllFilters"
          class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
        >
          Clear all
        </button>
      </div>
      
      <!-- Active Filter Count -->
      <div v-if="activeFilterCount > 0" class="mt-2">
        <span class="text-sm text-gray-600">
          {{ activeFilterCount }} filter{{ activeFilterCount !== 1 ? 's' : '' }} applied
        </span>
      </div>
    </div>

    <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
      <!-- Job Type -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Job Type</h4>
        <div class="space-y-2">
          <label
            v-for="type in jobTypes"
            :key="type.value"
            class="flex items-center"
          >
            <input
              v-model="filters.jobTypes"
              :value="type.value"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              @change="updateFilters"
            />
            <span class="ml-2 text-sm text-gray-700">{{ type.label }}</span>
            <span v-if="type.count" class="ml-auto text-xs text-gray-500">({{ type.count }})</span>
          </label>
        </div>
      </div>

      <!-- Experience Level -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Experience Level</h4>
        <div class="space-y-2">
          <label
            v-for="level in experienceLevels"
            :key="level.value"
            class="flex items-center"
          >
            <input
              v-model="filters.experienceLevels"
              :value="level.value"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              @change="updateFilters"
            />
            <span class="ml-2 text-sm text-gray-700">{{ level.label }}</span>
            <span v-if="level.count" class="ml-auto text-xs text-gray-500">({{ level.count }})</span>
          </label>
        </div>
      </div>

      <!-- Salary Range -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Salary Range</h4>
        <div class="space-y-3">
          <!-- Custom Range -->
          <div class="grid grid-cols-2 gap-2">
            <BaseInput
              v-model="filters.salaryMin"
              type="number"
              placeholder="Min"
              size="sm"
              @input="debouncedUpdateFilters"
            />
            <BaseInput
              v-model="filters.salaryMax"
              type="number"
              placeholder="Max"
              size="sm"
              @input="debouncedUpdateFilters"
            />
          </div>
          
          <!-- Predefined Ranges -->
          <div class="space-y-2">
            <label
              v-for="range in salaryRanges"
              :key="range.value"
              class="flex items-center"
            >
              <input
                v-model="filters.salaryRange"
                :value="range.value"
                type="radio"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                @change="updateFilters"
              />
              <span class="ml-2 text-sm text-gray-700">{{ range.label }}</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Location -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Location</h4>
        <div class="space-y-2">
          <BaseInput
            v-model="filters.location"
            type="text"
            placeholder="City, state, or remote"
            size="sm"
            :left-icon="MapPinIcon"
            @input="debouncedUpdateFilters"
          />
          
          <!-- Popular Locations -->
          <div class="space-y-2 mt-3">
            <label
              v-for="location in popularLocations"
              :key="location.value"
              class="flex items-center"
            >
              <input
                v-model="filters.selectedLocations"
                :value="location.value"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                @change="updateFilters"
              />
              <span class="ml-2 text-sm text-gray-700">{{ location.label }}</span>
              <span v-if="location.count" class="ml-auto text-xs text-gray-500">({{ location.count }})</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Company Size -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Company Size</h4>
        <div class="space-y-2">
          <label
            v-for="size in companySizes"
            :key="size.value"
            class="flex items-center"
          >
            <input
              v-model="filters.companySizes"
              :value="size.value"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              @change="updateFilters"
            />
            <span class="ml-2 text-sm text-gray-700">{{ size.label }}</span>
            <span v-if="size.count" class="ml-auto text-xs text-gray-500">({{ size.count }})</span>
          </label>
        </div>
      </div>

      <!-- Posted Date -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Posted</h4>
        <div class="space-y-2">
          <label
            v-for="date in postedDates"
            :key="date.value"
            class="flex items-center"
          >
            <input
              v-model="filters.postedDate"
              :value="date.value"
              type="radio"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
              @change="updateFilters"
            />
            <span class="ml-2 text-sm text-gray-700">{{ date.label }}</span>
          </label>
        </div>
      </div>

      <!-- Special Options -->
      <div>
        <h4 class="text-sm font-medium text-gray-900 mb-3">Special Options</h4>
        <div class="space-y-2">
          <label class="flex items-center">
            <input
              v-model="filters.remoteOk"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              @change="updateFilters"
            />
            <span class="ml-2 text-sm text-gray-700">Remote work available</span>
          </label>
          <label class="flex items-center">
            <input
              v-model="filters.urgentHiring"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              @change="updateFilters"
            />
            <span class="ml-2 text-sm text-gray-700">Urgent hiring</span>
          </label>
          <label class="flex items-center">
            <input
              v-model="filters.featuredOnly"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              @change="updateFilters"
            />
            <span class="ml-2 text-sm text-gray-700">Featured jobs only</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Apply Button -->
    <div class="p-6 border-t border-gray-200">
      <BaseButton
        variant="primary"
        size="md"
        :full-width="true"
        @click="applyFilters"
        :loading="isApplying"
      >
        Apply Filters
      </BaseButton>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { debounce } from 'lodash'
import { MapPinIcon } from '@heroicons/vue/24/outline'
import BaseInput from '../ui/BaseInput.vue'
import BaseButton from '../ui/BaseButton.vue'

interface FilterData {
  jobTypes: string[]
  experienceLevels: string[]
  salaryMin: string
  salaryMax: string
  salaryRange: string
  location: string
  selectedLocations: string[]
  companySizes: string[]
  postedDate: string
  remoteOk: boolean
  urgentHiring: boolean
  featuredOnly: boolean
}

interface Props {
  modelValue?: Partial<FilterData>
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:modelValue': [filters: Partial<FilterData>]
  'apply': [filters: Partial<FilterData>]
}>()

// State
const isApplying = ref(false)

const filters = ref<FilterData>({
  jobTypes: [],
  experienceLevels: [],
  salaryMin: '',
  salaryMax: '',
  salaryRange: '',
  location: '',
  selectedLocations: [],
  companySizes: [],
  postedDate: '',
  remoteOk: false,
  urgentHiring: false,
  featuredOnly: false,
  ...props.modelValue
})

// Filter options
const jobTypes = ref([
  { value: 'full-time', label: 'Full-time', count: 1234 },
  { value: 'part-time', label: 'Part-time', count: 567 },
  { value: 'contract', label: 'Contract', count: 890 },
  { value: 'freelance', label: 'Freelance', count: 234 },
  { value: 'internship', label: 'Internship', count: 123 }
])

const experienceLevels = ref([
  { value: 'entry', label: 'Entry Level', count: 456 },
  { value: 'mid', label: 'Mid Level', count: 789 },
  { value: 'senior', label: 'Senior Level', count: 345 },
  { value: 'executive', label: 'Executive', count: 67 }
])

const salaryRanges = ref([
  { value: '0-30000', label: '$0 - $30,000' },
  { value: '30000-50000', label: '$30,000 - $50,000' },
  { value: '50000-75000', label: '$50,000 - $75,000' },
  { value: '75000-100000', label: '$75,000 - $100,000' },
  { value: '100000+', label: '$100,000+' }
])

const popularLocations = ref([
  { value: 'remote', label: 'Remote', count: 234 },
  { value: 'new-york', label: 'New York, NY', count: 567 },
  { value: 'san-francisco', label: 'San Francisco, CA', count: 345 },
  { value: 'seattle', label: 'Seattle, WA', count: 234 },
  { value: 'chicago', label: 'Chicago, IL', count: 189 }
])

const companySizes = ref([
  { value: '1-10', label: '1-10 employees', count: 123 },
  { value: '11-50', label: '11-50 employees', count: 234 },
  { value: '51-200', label: '51-200 employees', count: 345 },
  { value: '201-1000', label: '201-1000 employees', count: 456 },
  { value: '1000+', label: '1000+ employees', count: 567 }
])

const postedDates = ref([
  { value: '1', label: 'Last 24 hours' },
  { value: '7', label: 'Last 7 days' },
  { value: '30', label: 'Last 30 days' },
  { value: '', label: 'Any time' }
])

// Computed
const hasActiveFilters = computed(() => {
  return activeFilterCount.value > 0
})

const activeFilterCount = computed(() => {
  let count = 0
  
  if (filters.value.jobTypes.length > 0) count++
  if (filters.value.experienceLevels.length > 0) count++
  if (filters.value.salaryMin || filters.value.salaryMax || filters.value.salaryRange) count++
  if (filters.value.location || filters.value.selectedLocations.length > 0) count++
  if (filters.value.companySizes.length > 0) count++
  if (filters.value.postedDate) count++
  if (filters.value.remoteOk || filters.value.urgentHiring || filters.value.featuredOnly) count++
  
  return count
})

// Methods
const updateFilters = () => {
  emit('update:modelValue', { ...filters.value })
}

const debouncedUpdateFilters = debounce(updateFilters, 300)

const clearAllFilters = () => {
  filters.value = {
    jobTypes: [],
    experienceLevels: [],
    salaryMin: '',
    salaryMax: '',
    salaryRange: '',
    location: '',
    selectedLocations: [],
    companySizes: [],
    postedDate: '',
    remoteOk: false,
    urgentHiring: false,
    featuredOnly: false
  }
  updateFilters()
}

const applyFilters = async () => {
  isApplying.value = true
  
  try {
    await new Promise(resolve => setTimeout(resolve, 300))
    emit('apply', { ...filters.value })
  } finally {
    isApplying.value = false
  }
}

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    filters.value = { ...filters.value, ...newValue }
  }
}, { deep: true })
</script> 