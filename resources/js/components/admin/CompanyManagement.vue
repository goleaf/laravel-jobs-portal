<template>
  <div class="company-management">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ $t('admin.company_management.title') }}
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            {{ $t('admin.company_management.subtitle') }}
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <button
            @click="exportCompanies"
            :disabled="loading"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            {{ $t('common.export') }}
          </button>
          <button
            @click="showCreateModal = true"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            {{ $t('admin.company_management.add_company') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.search') }}
          </label>
          <div class="relative">
            <input
              v-model="filters.search"
              @input="debouncedSearch"
              type="text"
              :placeholder="$t('admin.company_management.search_placeholder')"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.status') }}
          </label>
          <select
            v-model="filters.status"
            @change="applyFilters"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.all_statuses') }}</option>
            <option value="active">{{ $t('common.active') }}</option>
            <option value="inactive">{{ $t('common.inactive') }}</option>
            <option value="featured">{{ $t('common.featured') }}</option>
            <option value="verified">{{ $t('common.verified') }}</option>
          </select>
        </div>

        <!-- Industry Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.industry') }}
          </label>
          <select
            v-model="filters.industry_id"
            @change="applyFilters"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.all_industries') }}</option>
            <option
              v-for="industry in industries"
              :key="industry.id"
              :value="industry.id"
            >
              {{ industry.name }}
            </option>
          </select>
        </div>

        <!-- Location Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.location') }}
          </label>
          <select
            v-model="filters.country_id"
            @change="applyFilters"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.all_countries') }}</option>
            <option
              v-for="country in countries"
              :key="country.id"
              :value="country.id"
            >
              {{ country.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Advanced Filters Toggle -->
      <div class="mt-4">
        <button
          @click="showAdvancedFilters = !showAdvancedFilters"
          class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
        >
          {{ showAdvancedFilters ? $t('common.hide_advanced_filters') : $t('common.show_advanced_filters') }}
          <svg
            :class="{ 'rotate-180': showAdvancedFilters }"
            class="inline-block w-4 h-4 ml-1 transition-transform"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
      </div>

      <!-- Advanced Filters -->
      <div v-if="showAdvancedFilters" class="mt-4 grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.company_size') }}
          </label>
          <select
            v-model="filters.company_size_id"
            @change="applyFilters"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.all_sizes') }}</option>
            <option
              v-for="size in companySizes"
              :key="size.id"
              :value="size.id"
            >
              {{ size.size }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.established_year') }}
          </label>
          <input
            v-model="filters.established_from"
            @change="applyFilters"
            type="number"
            :placeholder="$t('common.from_year')"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.jobs_count') }}
          </label>
          <select
            v-model="filters.jobs_count_range"
            @change="applyFilters"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.any_count') }}</option>
            <option value="0">{{ $t('common.no_jobs') }}</option>
            <option value="1-5">1-5 {{ $t('common.jobs') }}</option>
            <option value="6-20">6-20 {{ $t('common.jobs') }}</option>
            <option value="21+">21+ {{ $t('common.jobs') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.created_date') }}
          </label>
          <input
            v-model="filters.created_from"
            @change="applyFilters"
            type="date"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          />
        </div>

        <div class="flex items-end">
          <button
            @click="clearFilters"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('common.clear_filters') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedCompanies.length > 0" class="bg-indigo-50 px-6 py-3 border-b border-indigo-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <span class="text-sm font-medium text-indigo-700">
            {{ $t('common.selected_count', { count: selectedCompanies.length }) }}
          </span>
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="bulkAction('activate')"
            :disabled="bulkActionLoading"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
          >
            {{ $t('common.activate') }}
          </button>
          <button
            @click="bulkAction('deactivate')"
            :disabled="bulkActionLoading"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
          >
            {{ $t('common.deactivate') }}
          </button>
          <button
            @click="bulkAction('feature')"
            :disabled="bulkActionLoading"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 disabled:opacity-50"
          >
            {{ $t('common.feature') }}
          </button>
          <button
            @click="bulkAction('verify')"
            :disabled="bulkActionLoading"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
          >
            {{ $t('common.verify') }}
          </button>
          <button
            @click="clearSelection"
            class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('common.clear_selection') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Companies Table -->
    <div class="bg-white shadow overflow-hidden">
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        <span class="ml-2 text-gray-600">{{ $t('common.loading') }}</span>
      </div>

      <div v-else-if="companies.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('admin.company_management.no_companies') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ $t('admin.company_management.no_companies_description') }}</p>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left">
                <input
                  type="checkbox"
                  :checked="allSelected"
                  @change="toggleSelectAll"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
              </th>
              <th
                v-for="column in tableColumns"
                :key="column.key"
                @click="sortBy(column.key)"
                :class="[
                  'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider',
                  column.sortable ? 'cursor-pointer hover:bg-gray-100' : ''
                ]"
              >
                <div class="flex items-center">
                  {{ $t(column.label) }}
                  <svg
                    v-if="column.sortable && sortField === column.key"
                    :class="[
                      'ml-1 w-4 h-4',
                      sortDirection === 'asc' ? 'transform rotate-180' : ''
                    ]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ $t('common.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="company in companies"
              :key="company.id"
              :class="[
                'hover:bg-gray-50',
                selectedCompanies.includes(company.id) ? 'bg-indigo-50' : ''
              ]"
            >
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
                      :src="company.logo || '/images/default-company-logo.png'"
                      :alt="company.name"
                      class="h-10 w-10 rounded-full object-cover"
                    />
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
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ company.location?.city?.name || '-' }}
                </div>
                <div class="text-sm text-gray-500">
                  {{ company.location?.country?.name || '-' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ company.industry?.name || '-' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ company.company_size?.size || '-' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ company.statistics?.jobs_count || 0 }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center space-x-2">
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      company.is_active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ company.is_active ? $t('common.active') : $t('common.inactive') }}
                  </span>
                  <span
                    v-if="company.is_featured"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"
                  >
                    {{ $t('common.featured') }}
                  </span>
                  <span
                    v-if="company.is_profile_verified"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800"
                  >
                    {{ $t('common.verified') }}
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                  {{ formatDate(company.created_at) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="viewCompany(company)"
                    class="text-indigo-600 hover:text-indigo-900"
                    :title="$t('common.view')"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                  <button
                    @click="editCompany(company)"
                    class="text-yellow-600 hover:text-yellow-900"
                    :title="$t('common.edit')"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    @click="toggleCompanyStatus(company)"
                    :class="[
                      company.is_active
                        ? 'text-red-600 hover:text-red-900'
                        : 'text-green-600 hover:text-green-900'
                    ]"
                    :title="company.is_active ? $t('common.deactivate') : $t('common.activate')"
                  >
                    <svg v-if="company.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </button>
                  <button
                    @click="deleteCompany(company)"
                    class="text-red-600 hover:text-red-900"
                    :title="$t('common.delete')"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="pagination.total > 0" class="bg-white px-6 py-3 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
          {{ $t('common.showing_results', {
            from: pagination.from,
            to: pagination.to,
            total: pagination.total
          }) }}
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="changePage(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed rounded-l-md"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          
          <template v-for="page in visiblePages" :key="page">
            <button
              v-if="page !== '...'"
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
            <span v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
              ...
            </span>
          </template>
          
          <button
            @click="changePage(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="relative inline-flex items-center px-2 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed rounded-r-md"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { OptimizedLodash } from '@/utils/dynamicImports'

// Composables
const { t } = useI18n()

// Reactive state
const loading = ref(false)
const bulkActionLoading = ref(false)
const showAdvancedFilters = ref(false)
const showCreateModal = ref(false)
const companies = ref([])
const selectedCompanies = ref([])
const industries = ref([])
const countries = ref([])
const companySizes = ref([])

// Filters
const filters = reactive({
  search: '',
  status: '',
  industry_id: '',
  country_id: '',
  company_size_id: '',
  established_from: '',
  jobs_count_range: '',
  created_from: '',
  page: 1,
  per_page: 15
})

// Sorting
const sortField = ref('created_at')
const sortDirection = ref('desc')

// Pagination
const pagination = reactive({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0
})

// Table columns configuration
const tableColumns = [
  { key: 'name', label: 'common.company', sortable: true },
  { key: 'location', label: 'common.location', sortable: false },
  { key: 'industry', label: 'common.industry', sortable: true },
  { key: 'company_size', label: 'common.size', sortable: true },
  { key: 'jobs_count', label: 'common.jobs', sortable: true },
  { key: 'status', label: 'common.status', sortable: false },
  { key: 'created_at', label: 'common.created_at', sortable: true }
]

// Computed properties
const allSelected = computed(() => {
  return companies.value.length > 0 && selectedCompanies.value.length === companies.value.length
})

const visiblePages = computed(() => {
  const pages = []
  const current = pagination.current_page
  const last = pagination.last_page
  
  if (last <= 7) {
    for (let i = 1; i <= last; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 4) {
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    } else if (current >= last - 3) {
      pages.push(1)
      pages.push('...')
      for (let i = last - 4; i <= last; i++) {
        pages.push(i)
      }
    } else {
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(last)
    }
  }
  
  return pages
})

// Methods
const fetchCompanies = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    
    // Add filters
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== '' && value !== null && value !== undefined) {
        params.append(key, value.toString())
      }
    })
    
    // Add sorting
    params.append('sort_by', sortField.value)
    params.append('sort_direction', sortDirection.value)
    
    const response = await fetch(`/api/admin/companies?${params}`)
    const data = await response.json()
    
    if (response.ok) {
      companies.value = data.data
      Object.assign(pagination, data.meta.pagination)
    } else {
      console.error('Failed to fetch companies:', data.message)
    }
  } catch (error) {
    console.error('Error fetching companies:', error)
  } finally {
    loading.value = false
  }
}

const fetchFilterOptions = async () => {
  try {
    const [industriesRes, countriesRes, sizesRes] = await Promise.all([
      fetch('/api/industries'),
      fetch('/api/countries'),
      fetch('/api/company-sizes')
    ])
    
    const [industriesData, countriesData, sizesData] = await Promise.all([
      industriesRes.json(),
      countriesRes.json(),
      sizesRes.json()
    ])
    
    industries.value = industriesData.data || []
    countries.value = countriesData.data || []
    companySizes.value = sizesData.data || []
  } catch (error) {
    console.error('Error fetching filter options:', error)
  }
}

const debouncedSearch = OptimizedLodash.debounce(() => {
  filters.page = 1
  fetchCompanies()
}, 500)

const applyFilters = () => {
  filters.page = 1
  fetchCompanies()
}

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    if (key !== 'page' && key !== 'per_page') {
      filters[key] = ''
    }
  })
  filters.page = 1
  fetchCompanies()
}

const sortBy = (field: string) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
  fetchCompanies()
}

const changePage = (page: number) => {
  if (page >= 1 && page <= pagination.last_page) {
    filters.page = page
    fetchCompanies()
  }
}

const toggleSelectAll = () => {
  if (allSelected.value) {
    selectedCompanies.value = []
  } else {
    selectedCompanies.value = companies.value.map(company => company.id)
  }
}

const clearSelection = () => {
  selectedCompanies.value = []
}

const bulkAction = async (action: string) => {
  if (selectedCompanies.value.length === 0) return
  
  bulkActionLoading.value = true
  try {
    const response = await fetch('/api/admin/companies/bulk-action', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        action,
        company_ids: selectedCompanies.value
      })
    })
    
    const data = await response.json()
    
    if (response.ok) {
      // Show success message
      console.log(`Bulk ${action} completed successfully`)
      selectedCompanies.value = []
      fetchCompanies()
    } else {
      console.error(`Bulk ${action} failed:`, data.message)
    }
  } catch (error) {
    console.error(`Error performing bulk ${action}:`, error)
  } finally {
    bulkActionLoading.value = false
  }
}

const viewCompany = (company: any) => {
  // Navigate to company details
  window.open(`/admin/companies/${company.id}`, '_blank')
}

const editCompany = (company: any) => {
  // Navigate to company edit form
  window.location.href = `/admin/companies/${company.id}/edit`
}

const toggleCompanyStatus = async (company: any) => {
  try {
    const response = await fetch(`/api/admin/companies/${company.id}/toggle-status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })
    
    const data = await response.json()
    
    if (response.ok) {
      company.is_active = !company.is_active
      console.log('Company status updated successfully')
    } else {
      console.error('Failed to update company status:', data.message)
    }
  } catch (error) {
    console.error('Error updating company status:', error)
  }
}

const deleteCompany = async (company: any) => {
  if (!confirm(t('admin.company_management.confirm_delete', { name: company.name }))) {
    return
  }
  
  try {
    const response = await fetch(`/api/admin/companies/${company.id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })
    
    const data = await response.json()
    
    if (response.ok) {
      console.log('Company deleted successfully')
      fetchCompanies()
    } else {
      console.error('Failed to delete company:', data.message)
    }
  } catch (error) {
    console.error('Error deleting company:', error)
  }
}

const exportCompanies = async () => {
  try {
    const params = new URLSearchParams()
    
    // Add current filters to export
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== '' && value !== null && value !== undefined && key !== 'page' && key !== 'per_page') {
        params.append(key, value.toString())
      }
    })
    
    params.append('export_format', 'excel')
    
    const response = await fetch(`/api/admin/companies/export?${params}`)
    
    if (response.ok) {
      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `companies-${new Date().toISOString().split('T')[0]}.xlsx`
      document.body.appendChild(a)
      a.click()
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    } else {
      console.error('Failed to export companies')
    }
  } catch (error) {
    console.error('Error exporting companies:', error)
  }
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

// Lifecycle hooks
onMounted(() => {
  fetchCompanies()
  fetchFilterOptions()
})

// Watchers
watch(() => filters.page, () => {
  fetchCompanies()
})
</script>

<style scoped>
.company-management {
  @apply min-h-screen bg-gray-50;
}

.rotate-180 {
  transform: rotate(180deg);
}
</style> 