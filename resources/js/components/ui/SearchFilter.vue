<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Filter Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
      <button
        v-if="hasActiveFilters"
        @click="clearAllFilters"
        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
      >
        Clear all
      </button>
    </div>

    <!-- Active Filters -->
    <div v-if="activeFilters.length > 0" class="mb-6">
      <h4 class="text-sm font-medium text-gray-700 mb-2">Active Filters</h4>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="filter in activeFilters"
          :key="filter.key"
          class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
        >
          {{ filter.label }}
          <button
            @click="removeFilter(filter.key)"
            class="ml-1 text-indigo-600 hover:text-indigo-800"
          >
            <XMarkIcon class="w-3 h-3" />
          </button>
        </span>
      </div>
    </div>

    <!-- Location Filter -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Location
      </label>
      <BaseInput
        v-model="filters.location"
        type="text"
        placeholder="City, state, or remote"
        :left-icon="MapPinIcon"
        @input="onFilterChange"
      />
    </div>

    <!-- Employment Type Filter -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Employment Type
      </label>
      <div class="space-y-2">
        <label
          v-for="type in employmentTypes"
          :key="type.value"
          class="flex items-center"
        >
          <input
            v-model="filters.employmentType"
            :value="type.value"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            @change="onFilterChange"
          />
          <span class="ml-2 text-sm text-gray-700">{{ type.label }}</span>
          <span v-if="type.count" class="ml-auto text-xs text-gray-500">({{ type.count }})</span>
        </label>
      </div>
    </div>

    <!-- Experience Level Filter -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Experience Level
      </label>
      <div class="space-y-2">
        <label
          v-for="level in experienceLevels"
          :key="level.value"
          class="flex items-center"
        >
          <input
            v-model="filters.experienceLevel"
            :value="level.value"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            @change="onFilterChange"
          />
          <span class="ml-2 text-sm text-gray-700">{{ level.label }}</span>
          <span v-if="level.count" class="ml-auto text-xs text-gray-500">({{ level.count }})</span>
        </label>
      </div>
    </div>

    <!-- Salary Range Filter -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Salary Range
      </label>
      <div class="space-y-3">
        <div class="grid grid-cols-2 gap-3">
          <BaseInput
            v-model="filters.salaryMin"
            type="number"
            placeholder="Min"
            :left-icon="CurrencyDollarIcon"
            @input="onFilterChange"
          />
          <BaseInput
            v-model="filters.salaryMax"
            type="number"
            placeholder="Max"
            :left-icon="CurrencyDollarIcon"
            @input="onFilterChange"
          />
        </div>
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
              @change="onFilterChange"
            />
            <span class="ml-2 text-sm text-gray-700">{{ range.label }}</span>
          </label>
        </div>
      </div>
    </div>

    <!-- Industry Filter (for companies) -->
    <div v-if="showIndustryFilter" class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Industry
      </label>
      <select
        v-model="filters.industry"
        @change="onFilterChange"
        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      >
        <option value="">All Industries</option>
        <option
          v-for="industry in industries"
          :key="industry.value"
          :value="industry.value"
        >
          {{ industry.label }}
        </option>
      </select>
    </div>

    <!-- Company Size Filter (for companies) -->
    <div v-if="showCompanySizeFilter" class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Company Size
      </label>
      <div class="space-y-2">
        <label
          v-for="size in companySizes"
          :key="size.value"
          class="flex items-center"
        >
          <input
            v-model="filters.companySize"
            :value="size.value"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            @change="onFilterChange"
          />
          <span class="ml-2 text-sm text-gray-700">{{ size.label }}</span>
        </label>
      </div>
    </div>

    <!-- Date Posted Filter -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Date Posted
      </label>
      <div class="space-y-2">
        <label
          v-for="date in datePostedOptions"
          :key="date.value"
          class="flex items-center"
        >
          <input
            v-model="filters.datePosted"
            :value="date.value"
            type="radio"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
            @change="onFilterChange"
          />
          <span class="ml-2 text-sm text-gray-700">{{ date.label }}</span>
        </label>
      </div>
    </div>

    <!-- Special Filters -->
    <div class="mb-6">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Special Options
      </label>
      <div class="space-y-2">
        <label class="flex items-center">
          <input
            v-model="filters.remoteOk"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            @change="onFilterChange"
          />
          <span class="ml-2 text-sm text-gray-700">Remote work available</span>
        </label>
        <label class="flex items-center">
          <input
            v-model="filters.featuredOnly"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            @change="onFilterChange"
          />
          <span class="ml-2 text-sm text-gray-700">Featured only</span>
        </label>
        <label v-if="showCompanyFilters" class="flex items-center">
          <input
            v-model="filters.verifiedOnly"
            type="checkbox"
            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
            @change="onFilterChange"
          />
          <span class="ml-2 text-sm text-gray-700">Verified companies only</span>
        </label>
      </div>
    </div>

    <!-- Apply Filters Button -->
    <div class="pt-4 border-t border-gray-200">
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
import {
  XMarkIcon,
  MapPinIcon,
  CurrencyDollarIcon
} from '@heroicons/vue/24/outline'
import BaseInput from './BaseInput.vue'
import BaseButton from './BaseButton.vue'

interface FilterData {
  location: string
  employmentType: string[]
  experienceLevel: string[]
  salaryMin: string
  salaryMax: string
  salaryRange: string
  industry: string
  companySize: string[]
  datePosted: string
  remoteOk: boolean
  featuredOnly: boolean
  verifiedOnly: boolean
}

interface Props {
  modelValue?: Partial<FilterData>
  showIndustryFilter?: boolean
  showCompanySizeFilter?: boolean
  showCompanyFilters?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showIndustryFilter: false,
  showCompanySizeFilter: false,
  showCompanyFilters: false
})

const emit = defineEmits<{
  'update:modelValue': [filters: Partial<FilterData>]
  'apply': [filters: Partial<FilterData>]
}>()

// State
const isApplying = ref(false)

const filters = ref<FilterData>({
  location: '',
  employmentType: [],
  experienceLevel: [],
  salaryMin: '',
  salaryMax: '',
  salaryRange: '',
  industry: '',
  companySize: [],
  datePosted: '',
  remoteOk: false,
  featuredOnly: false,
  verifiedOnly: false,
  ...props.modelValue
})

// Options
const employmentTypes = ref([
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

const industries = ref([
  { value: 'technology', label: 'Technology' },
  { value: 'healthcare', label: 'Healthcare' },
  { value: 'finance', label: 'Finance' },
  { value: 'education', label: 'Education' },
  { value: 'marketing', label: 'Marketing' },
  { value: 'sales', label: 'Sales' }
])

const companySizes = ref([
  { value: '1-10', label: '1-10 employees' },
  { value: '11-50', label: '11-50 employees' },
  { value: '51-200', label: '51-200 employees' },
  { value: '201-1000', label: '201-1000 employees' },
  { value: '1000+', label: '1000+ employees' }
])

const datePostedOptions = ref([
  { value: '1', label: 'Last 24 hours' },
  { value: '7', label: 'Last 7 days' },
  { value: '30', label: 'Last 30 days' },
  { value: '', label: 'Any time' }
])

// Computed
const activeFilters = computed(() => {
  const active: Array<{ key: string; label: string }> = []
  
  if (filters.value.location) {
    active.push({ key: 'location', label: `Location: ${filters.value.location}` })
  }
  
  filters.value.employmentType.forEach(type => {
    const option = employmentTypes.value.find(t => t.value === type)
    if (option) {
      active.push({ key: `employmentType.${type}`, label: option.label })
    }
  })
  
  filters.value.experienceLevel.forEach(level => {
    const option = experienceLevels.value.find(l => l.value === level)
    if (option) {
      active.push({ key: `experienceLevel.${level}`, label: option.label })
    }
  })
  
  if (filters.value.salaryRange) {
    const option = salaryRanges.value.find(r => r.value === filters.value.salaryRange)
    if (option) {
      active.push({ key: 'salaryRange', label: `Salary: ${option.label}` })
    }
  }
  
  if (filters.value.industry) {
    const option = industries.value.find(i => i.value === filters.value.industry)
    if (option) {
      active.push({ key: 'industry', label: `Industry: ${option.label}` })
    }
  }
  
  if (filters.value.remoteOk) {
    active.push({ key: 'remoteOk', label: 'Remote OK' })
  }
  
  if (filters.value.featuredOnly) {
    active.push({ key: 'featuredOnly', label: 'Featured Only' })
  }
  
  return active
})

const hasActiveFilters = computed(() => activeFilters.value.length > 0)

// Methods
const onFilterChange = () => {
  emit('update:modelValue', { ...filters.value })
}

const removeFilter = (key: string) => {
  if (key.includes('.')) {
    const [filterKey, value] = key.split('.')
    if (filterKey === 'employmentType') {
      filters.value.employmentType = filters.value.employmentType.filter(t => t !== value)
    } else if (filterKey === 'experienceLevel') {
      filters.value.experienceLevel = filters.value.experienceLevel.filter(l => l !== value)
    }
  } else {
    (filters.value as any)[key] = Array.isArray((filters.value as any)[key]) ? [] : 
                                 typeof (filters.value as any)[key] === 'boolean' ? false : ''
  }
  onFilterChange()
}

const clearAllFilters = () => {
  filters.value = {
    location: '',
    employmentType: [],
    experienceLevel: [],
    salaryMin: '',
    salaryMax: '',
    salaryRange: '',
    industry: '',
    companySize: [],
    datePosted: '',
    remoteOk: false,
    featuredOnly: false,
    verifiedOnly: false
  }
  onFilterChange()
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