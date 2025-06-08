<template>
  <div class="bg-white shadow-sm rounded-lg">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ $t('admin.companies.management_title') }}</h2>
          <p class="mt-1 text-sm text-gray-600">{{ $t('admin.companies.management_description') }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
          <button
            @click="exportCompanies"
            :disabled="isLoading"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
            {{ $t('admin.companies.export') }}
          </button>
          <button
            @click="openCreateModal"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            {{ $t('admin.companies.create_company') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <BuildingOfficeIcon class="h-8 w-8 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.companies.total_companies') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.total || 0 }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <CheckCircleIcon class="h-8 w-8 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.companies.active_companies') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.active || 0 }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <StarIcon class="h-8 w-8 text-yellow-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.companies.featured_companies') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.featured || 0 }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <BriefcaseIcon class="h-8 w-8 text-purple-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.companies.with_active_jobs') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.withActiveJobs || 0 }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          </div>
          <input
            v-model="filters.search"
            @input="debounceSearch"
            type="text"
            :placeholder="$t('admin.companies.search_placeholder')"
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>

        <!-- Industry Filter -->
        <select
          v-model="filters.industry_id"
          @change="applyFilters"
          class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">{{ $t('admin.companies.all_industries') }}</option>
          <option 
            v-for="industry in industries" 
            :key="industry.id" 
            :value="industry.id"
          >
            {{ industry.name }}
          </option>
        </select>

        <!-- Company Size Filter -->
        <select
          v-model="filters.company_size_id"
          @change="applyFilters"
          class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">{{ $t('admin.companies.all_sizes') }}</option>
          <option 
            v-for="size in companySizes" 
            :key="size.id" 
            :value="size.id"
          >
            {{ size.size }}
          </option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="filters.status"
          @change="applyFilters"
          class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">{{ $t('admin.companies.all_statuses') }}</option>
          <option value="active">{{ $t('admin.companies.active') }}</option>
          <option value="inactive">{{ $t('admin.companies.inactive') }}</option>
          <option value="featured">{{ $t('admin.companies.featured') }}</option>
          <option value="verified">{{ $t('admin.companies.verified') }}</option>
        </select>
      </div>

      <!-- Advanced Filters Toggle -->
      <div class="mt-4">
        <button
          @click="showAdvancedFilters = !showAdvancedFilters"
          class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900"
        >
          <AdjustmentsHorizontalIcon class="h-4 w-4 mr-1" />
          {{ $t('admin.companies.advanced_filters') }}
          <ChevronDownIcon :class="['h-4 w-4 ml-1 transition-transform', { 'rotate-180': showAdvancedFilters }]" />
        </button>
      </div>

      <!-- Advanced Filters Panel -->
      <div v-if="showAdvancedFilters" class="mt-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Date Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('admin.companies.created_date_range') }}
            </label>
            <div class="grid grid-cols-2 gap-2">
              <input
                v-model="filters.created_after"
                type="date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
              <input
                v-model="filters.created_before"
                type="date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>

          <!-- Location Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('admin.companies.location') }}
            </label>
            <select
              v-model="filters.country_id"
              @change="applyFilters"
              class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">{{ $t('admin.companies.all_countries') }}</option>
              <option 
                v-for="country in countries" 
                :key="country.id" 
                :value="country.id"
              >
                {{ country.name }}
              </option>
            </select>
          </div>

          <!-- Employee Count Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('admin.companies.employee_count_range') }}
            </label>
            <div class="grid grid-cols-2 gap-2">
              <input
                v-model.number="filters.min_employees"
                type="number"
                min="0"
                :placeholder="$t('admin.companies.min_employees')"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
              <input
                v-model.number="filters.max_employees"
                type="number"
                min="0"
                :placeholder="$t('admin.companies.max_employees')"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>
        </div>

        <!-- Filter Actions -->
        <div class="mt-4 flex justify-end space-x-3">
          <button
            @click="clearFilters"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('admin.companies.clear_filters') }}
          </button>
          <button
            @click="applyFilters"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('admin.companies.apply_filters') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedCompanies.length > 0" class="px-6 py-3 bg-indigo-50 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <span class="text-sm text-indigo-700">
            {{ $t('admin.companies.selected_count', { count: selectedCompanies.length }) }}
          </span>
        </div>
        <div class="flex space-x-2">
          <button
            @click="bulkAction('activate')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200"
          >
            {{ $t('admin.companies.bulk_activate') }}
          </button>
          <button
            @click="bulkAction('deactivate')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200"
          >
            {{ $t('admin.companies.bulk_deactivate') }}
          </button>
          <button
            @click="bulkAction('feature')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200"
          >
            {{ $t('admin.companies.bulk_feature') }}
          </button>
          <button
            @click="bulkAction('delete')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200"
          >
            {{ $t('admin.companies.bulk_delete') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Companies Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <input
                type="checkbox"
                @change="toggleAllCompanies"
                :checked="allCompaniesSelected"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button @click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700">
                <span>{{ $t('admin.companies.company_name') }}</span>
                <ChevronUpDownIcon class="h-4 w-4" />
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.companies.industry') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.companies.location') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button @click="sortBy('jobs_count')" class="flex items-center space-x-1 hover:text-gray-700">
                <span>{{ $t('admin.companies.jobs_count') }}</span>
                <ChevronUpDownIcon class="h-4 w-4" />
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.companies.status') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button @click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700">
                <span>{{ $t('admin.companies.created_at') }}</span>
                <ChevronUpDownIcon class="h-4 w-4" />
              </button>
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.companies.actions') }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-if="isLoading" v-for="n in 5" :key="`loading-${n}`" class="animate-pulse">
            <td class="px-6 py-4">
              <div class="h-4 w-4 bg-gray-200 rounded"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-2/3"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-6 bg-gray-200 rounded w-20"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-1/3"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-8 bg-gray-200 rounded w-24"></div>
            </td>
          </tr>

          <tr v-else-if="companies.length === 0">
            <td colspan="8" class="px-6 py-12 text-center">
              <BuildingOfficeIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('admin.companies.no_companies') }}</h3>
              <p class="mt-1 text-sm text-gray-500">{{ $t('admin.companies.no_companies_message') }}</p>
              <div class="mt-6">
                <button
                  @click="openCreateModal"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  {{ $t('admin.companies.create_first_company') }}
                </button>
              </div>
            </td>
          </tr>

          <tr v-else v-for="company in companies" :key="company.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <input
                type="checkbox"
                :value="company.id"
                v-model="selectedCompanies"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img 
                    v-if="company.logo_url" 
                    :src="company.logo_url" 
                    :alt="company.name"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                  <div v-else class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                    <BuildingOfficeIcon class="h-6 w-6 text-gray-600" />
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ company.name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ company.email }}
                  </div>
                </div>
              </div>
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ company.industry?.name || '-' }}
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <div>{{ company.city?.name || company.location }}</div>
              <div class="text-xs text-gray-500">{{ company.country?.name }}</div>
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              <div class="flex items-center">
                <span class="font-medium">{{ company.jobs_count || 0 }}</span>
                <span v-if="company.active_jobs_count" class="ml-2 text-xs text-green-600">
                  ({{ company.active_jobs_count }} {{ $t('admin.companies.active') }})
                </span>
              </div>
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex flex-col space-y-1">
                <span :class="[
                  'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                  company.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ company.is_active ? $t('admin.companies.active') : $t('admin.companies.inactive') }}
                </span>
                <span v-if="company.is_featured" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                  {{ $t('admin.companies.featured') }}
                </span>
              </div>
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ formatDate(company.created_at) }}
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end space-x-2">
                <button
                  @click="viewCompany(company)"
                  class="text-indigo-600 hover:text-indigo-900"
                  :title="$t('admin.companies.view_company')"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editCompany(company)"
                  class="text-blue-600 hover:text-blue-900"
                  :title="$t('admin.companies.edit_company')"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="toggleCompanyStatus(company)"
                  :class="[
                    company.is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900'
                  ]"
                  :title="company.is_active ? $t('admin.companies.deactivate') : $t('admin.companies.activate')"
                >
                  <component :is="company.is_active ? XMarkIcon : CheckIcon" class="h-4 w-4" />
                </button>
                <button
                  @click="deleteCompany(company)"
                  class="text-red-600 hover:text-red-900"
                  :title="$t('admin.companies.delete_company')"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > 0" class="px-6 py-4 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          {{ $t('admin.companies.showing_results', {
            from: pagination.from,
            to: pagination.to,
            total: pagination.total
          }) }}
        </div>
        
        <div class="flex items-center space-x-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 rounded-l-md"
          >
            <ChevronLeftIcon class="h-5 w-5" />
          </button>
          
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page === '...'"
              disabled
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
            >
              ...
            </button>
            <button
              v-else
              @click="changePage(page)"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                page === pagination.current_page
                  ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                  : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
          </template>
          
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 rounded-r-md"
          >
            <ChevronRightIcon class="h-5 w-5" />
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modals would be included here -->
  <!-- Create/Edit Company Modal -->
  <!-- View Company Modal -->
  <!-- Delete Confirmation Modal -->
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { 
  BuildingOfficeIcon, CheckCircleIcon, StarIcon, BriefcaseIcon,
  PlusIcon, MagnifyingGlassIcon, AdjustmentsHorizontalIcon,
  ChevronDownIcon, ChevronUpDownIcon, ChevronLeftIcon, ChevronRightIcon,
  EyeIcon, PencilIcon, TrashIcon, CheckIcon, XMarkIcon,
  DocumentArrowDownIcon
} from '@heroicons/vue/24/outline'

// Composables
const { t } = useI18n()

// Reactive data
const isLoading = ref(false)
const companies = ref([])
const selectedCompanies = ref([])
const showAdvancedFilters = ref(false)
const searchTimeout = ref(null)

// Statistics
const statistics = reactive({
  total: 0,
  active: 0,
  featured: 0,
  withActiveJobs: 0
})

// Filters
const filters = reactive({
  search: '',
  industry_id: '',
  company_size_id: '',
  status: '',
  country_id: '',
  created_after: '',
  created_before: '',
  min_employees: '',
  max_employees: ''
})

// Pagination
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 20,
  total: 0,
  from: 0,
  to: 0
})

// Sorting
const sorting = reactive({
  field: 'created_at',
  direction: 'desc'
})

// Reference data
const industries = ref([])
const companySizes = ref([])
const countries = ref([])

// Computed properties
const allCompaniesSelected = computed(() => {
  return companies.value.length > 0 && selectedCompanies.value.length === companies.value.length
})

const visiblePages = computed(() => {
  const current = pagination.current_page
  const last = pagination.last_page
  const delta = 2
  const range = []
  const rangeWithDots = []

  for (let i = Math.max(2, current - delta); i <= Math.min(last - 1, current + delta); i++) {
    range.push(i)
  }

  if (current - delta > 2) {
    rangeWithDots.push(1, '...')
  } else {
    rangeWithDots.push(1)
  }

  rangeWithDots.push(...range)

  if (current + delta < last - 1) {
    rangeWithDots.push('...', last)
  } else {
    rangeWithDots.push(last)
  }

  return rangeWithDots.filter((page, index, array) => array.indexOf(page) === index)
})

// Methods
const loadCompanies = async () => {
  isLoading.value = true
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      sort: sorting.field,
      direction: sorting.direction,
      ...Object.fromEntries(Object.entries(filters).filter(([_, value]) => value !== ''))
    }

    const response = await window.axios.get('/api/admin/companies', { params })
    
    companies.value = response.data.data
    Object.assign(pagination, response.data.meta)
    
    // Update statistics
    if (response.data.statistics) {
      Object.assign(statistics, response.data.statistics)
    }
  } catch (error) {
    console.error('Error loading companies:', error)
    // Handle error - show notification
  } finally {
    isLoading.value = false
  }
}

const loadReferenceData = async () => {
  try {
    const [industriesRes, sizesRes, countriesRes] = await Promise.all([
      window.axios.get('/api/industries'),
      window.axios.get('/api/company-sizes'),
      window.axios.get('/api/countries')
    ])
    
    industries.value = industriesRes.data.data
    companySizes.value = sizesRes.data.data
    countries.value = countriesRes.data.data
  } catch (error) {
    console.error('Error loading reference data:', error)
  }
}

const debounceSearch = () => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }
  
  searchTimeout.value = setTimeout(() => {
    applyFilters()
  }, 500)
}

const applyFilters = () => {
  pagination.current_page = 1
  loadCompanies()
}

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  applyFilters()
}

const sortBy = (field) => {
  if (sorting.field === field) {
    sorting.direction = sorting.direction === 'asc' ? 'desc' : 'asc'
  } else {
    sorting.field = field
    sorting.direction = 'desc'
  }
  loadCompanies()
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.last_page) {
    pagination.current_page = page
    loadCompanies()
  }
}

const toggleAllCompanies = () => {
  if (allCompaniesSelected.value) {
    selectedCompanies.value = []
  } else {
    selectedCompanies.value = companies.value.map(company => company.id)
  }
}

const toggleCompanyStatus = async (company) => {
  try {
    await window.axios.patch(`/api/admin/companies/${company.id}/toggle-status`)
    company.is_active = !company.is_active
    // Show success notification
  } catch (error) {
    console.error('Error toggling company status:', error)
    // Show error notification
  }
}

const bulkAction = async (action) => {
  if (selectedCompanies.value.length === 0) return
  
  try {
    await window.axios.post('/api/admin/companies/bulk-action', {
      action,
      company_ids: selectedCompanies.value
    })
    
    selectedCompanies.value = []
    loadCompanies()
    // Show success notification
  } catch (error) {
    console.error('Error performing bulk action:', error)
    // Show error notification
  }
}

const viewCompany = (company) => {
  // Open view modal or navigate to company detail page
  window.open(`/admin/companies/${company.id}`, '_blank')
}

const editCompany = (company) => {
  // Open edit modal
  console.log('Edit company:', company)
}

const deleteCompany = async (company) => {
  if (confirm(t('admin.companies.confirm_delete', { name: company.name }))) {
    try {
      await window.axios.delete(`/api/admin/companies/${company.id}`)
      loadCompanies()
      // Show success notification
    } catch (error) {
      console.error('Error deleting company:', error)
      // Show error notification
    }
  }
}

const openCreateModal = () => {
  // Open create company modal
  console.log('Open create modal')
}

const exportCompanies = async () => {
  try {
    const response = await window.axios.get('/api/admin/companies/export', {
      params: filters,
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `companies-${new Date().toISOString().split('T')[0]}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Error exporting companies:', error)
    // Show error notification
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

// Lifecycle
onMounted(() => {
  loadReferenceData()
  loadCompanies()
})

// Watchers
watch(() => pagination.per_page, () => {
  pagination.current_page = 1
  loadCompanies()
})
</script> 