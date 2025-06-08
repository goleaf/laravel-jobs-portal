<template>
  <div class="job-management-container">
    <!-- Header Section -->
    <div class="header-section">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $t('admin.jobs.title') }}
          </h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            {{ $t('admin.jobs.subtitle') }}
          </p>
        </div>
        <div class="flex gap-3">
          <button
            @click="exportJobs"
            :disabled="exporting"
            class="btn-secondary"
          >
            <Icon name="download" class="w-4 h-4 mr-2" />
            {{ exporting ? $t('common.exporting') : $t('common.export') }}
          </button>
          <button
            @click="showCreateModal = true"
            class="btn-primary"
          >
            <Icon name="plus" class="w-4 h-4 mr-2" />
            {{ $t('admin.jobs.create') }}
          </button>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <StatCard
          v-for="stat in statistics"
          :key="stat.key"
          :title="stat.title"
          :value="stat.value"
          :change="stat.change"
          :icon="stat.icon"
          :color="stat.color"
        />
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
      <div class="grid grid-cols-1 lg:grid-cols-6 gap-4 mb-4">
        <!-- Search Input -->
        <div class="lg:col-span-2">
          <SearchInput
            v-model="filters.search"
            :placeholder="$t('admin.jobs.search_placeholder')"
            @input="debouncedSearch"
          />
        </div>

        <!-- Status Filter -->
        <div>
          <SelectField
            v-model="filters.status"
            :options="statusOptions"
            :placeholder="$t('admin.jobs.filter_status')"
            @update:modelValue="applyFilters"
          />
        </div>

        <!-- Category Filter -->
        <div>
          <SelectField
            v-model="filters.category"
            :options="categoryOptions"
            :placeholder="$t('admin.jobs.filter_category')"
            @update:modelValue="applyFilters"
          />
        </div>

        <!-- Company Filter -->
        <div>
          <SelectField
            v-model="filters.company"
            :options="companyOptions"
            :placeholder="$t('admin.jobs.filter_company')"
            @update:modelValue="applyFilters"
          />
        </div>

        <!-- Date Range -->
        <div>
          <DateRangePicker
            v-model="filters.dateRange"
            :placeholder="$t('admin.jobs.filter_date')"
            @update:modelValue="applyFilters"
          />
        </div>
      </div>

      <!-- Quick Filters -->
      <div class="flex flex-wrap gap-2">
        <button
          v-for="quickFilter in quickFilters"
          :key="quickFilter.key"
          @click="applyQuickFilter(quickFilter)"
          :class="[
            'px-3 py-1 text-sm rounded-full border transition-colors',
            activeQuickFilter === quickFilter.key
              ? 'bg-indigo-100 border-indigo-300 text-indigo-700 dark:bg-indigo-900 dark:border-indigo-700 dark:text-indigo-300'
              : 'bg-gray-100 border-gray-300 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600'
          ]"
        >
          {{ quickFilter.label }}
          <span v-if="quickFilter.count" class="ml-1 font-medium">
            ({{ quickFilter.count }})
          </span>
        </button>
      </div>
    </div>

    <!-- Jobs Table -->
    <div class="jobs-table-section bg-white dark:bg-gray-800 rounded-lg shadow">
      <!-- Table Header -->
      <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ $t('admin.jobs.list_title') }}
            </h3>
            <span class="text-sm text-gray-500 dark:text-gray-400">
              {{ $t('admin.jobs.showing_count', { 
                from: pagination.from || 0, 
                to: pagination.to || 0, 
                total: pagination.total || 0 
              }) }}
            </span>
          </div>

          <div class="flex items-center gap-2">
            <!-- Bulk Actions -->
            <div v-if="selectedJobs.length > 0" class="flex items-center gap-2">
              <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ $t('admin.jobs.selected_count', { count: selectedJobs.length }) }}
              </span>
              <BulkActions
                :selected-count="selectedJobs.length"
                @bulk-action="handleBulkAction"
              />
            </div>

            <!-- View Toggle -->
            <div class="flex border border-gray-300 dark:border-gray-600 rounded-lg">
              <button
                @click="viewMode = 'table'"
                :class="[
                  'px-3 py-1 text-sm',
                  viewMode === 'table'
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'
                ]"
              >
                <Icon name="table" class="w-4 h-4" />
              </button>
              <button
                @click="viewMode = 'card'"
                :class="[
                  'px-3 py-1 text-sm border-l border-gray-300 dark:border-gray-600',
                  viewMode === 'card'
                    ? 'bg-indigo-600 text-white'
                    : 'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'
                ]"
              >
                <Icon name="grid" class="w-4 h-4" />
              </button>
            </div>

            <!-- Per Page Selector -->
            <SelectField
              v-model="pagination.perPage"
              :options="perPageOptions"
              class="w-20"
              @update:modelValue="changePerPage"
            />
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <LoadingSpinner class="mx-auto" />
        <p class="text-gray-600 dark:text-gray-400 mt-2">
          {{ $t('common.loading') }}
        </p>
      </div>

      <!-- Empty State -->
      <div v-else-if="jobs.length === 0" class="p-8 text-center">
        <Icon name="briefcase" class="w-16 h-16 mx-auto text-gray-400 mb-4" />
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
          {{ $t('admin.jobs.empty_title') }}
        </h3>
        <p class="text-gray-600 dark:text-gray-400 mb-4">
          {{ $t('admin.jobs.empty_message') }}
        </p>
        <button @click="showCreateModal = true" class="btn-primary">
          {{ $t('admin.jobs.create_first') }}
        </button>
      </div>

      <!-- Table View -->
      <div v-else-if="viewMode === 'table'" class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-900">
            <tr>
              <th class="px-6 py-3 text-left">
                <Checkbox
                  :checked="allJobsSelected"
                  @update:checked="toggleAllJobs"
                />
              </th>
              <th
                v-for="column in tableColumns"
                :key="column.key"
                @click="sort(column.key)"
                :class="[
                  'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider',
                  column.sortable ? 'cursor-pointer hover:text-gray-700 dark:hover:text-gray-200' : ''
                ]"
              >
                <div class="flex items-center gap-1">
                  {{ column.label }}
                  <Icon
                    v-if="column.sortable && sortField === column.key"
                    :name="sortDirection === 'asc' ? 'chevron-up' : 'chevron-down'"
                    class="w-3 h-3"
                  />
                </div>
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $t('common.actions') }}
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <JobTableRow
              v-for="job in jobs"
              :key="job.id"
              :job="job"
              :selected="selectedJobs.includes(job.id)"
              @toggle-selection="toggleJobSelection"
              @edit="editJob"
              @delete="deleteJob"
              @feature="toggleFeature"
              @view-applications="viewApplications"
            />
          </tbody>
        </table>
      </div>

      <!-- Card View -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        <JobCard
          v-for="job in jobs"
          :key="job.id"
          :job="job"
          :selected="selectedJobs.includes(job.id)"
          @toggle-selection="toggleJobSelection"
          @edit="editJob"
          @delete="deleteJob"
          @feature="toggleFeature"
          @view-applications="viewApplications"
        />
      </div>

      <!-- Pagination -->
      <div v-if="pagination.lastPage > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <Pagination
          :current-page="pagination.currentPage"
          :last-page="pagination.lastPage"
          :total="pagination.total"
          :per-page="pagination.perPage"
          @page-changed="changePage"
        />
      </div>
    </div>

    <!-- Modals -->
    <CreateJobModal
      v-model:show="showCreateModal"
      @job-created="onJobCreated"
    />

    <EditJobModal
      v-model:show="showEditModal"
      :job="selectedJob"
      @job-updated="onJobUpdated"
    />

    <ViewApplicationsModal
      v-model:show="showApplicationsModal"
      :job="selectedJob"
    />

    <DeleteConfirmModal
      v-model:show="showDeleteModal"
      :title="$t('admin.jobs.delete_title')"
      :message="$t('admin.jobs.delete_message')"
      @confirmed="confirmDelete"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from '@/composables/useToast'
import { useDebounce } from '@/composables/useDebounce'
import { useJobsApi } from '@/composables/api/useJobsApi'
import { usePagination } from '@/composables/usePagination'
import { useSelection } from '@/composables/useSelection'

// Components
import StatCard from '@/components/ui/StatCard.vue'
import SearchInput from '@/components/ui/SearchInput.vue'
import SelectField from '@/components/ui/SelectField.vue'
import DateRangePicker from '@/components/ui/DateRangePicker.vue'
import LoadingSpinner from '@/components/ui/LoadingSpinner.vue'
import Checkbox from '@/components/ui/Checkbox.vue'
import Pagination from '@/components/ui/Pagination.vue'
import JobTableRow from './components/JobTableRow.vue'
import JobCard from './components/JobCard.vue'
import BulkActions from './components/BulkActions.vue'
import CreateJobModal from './modals/CreateJobModal.vue'
import EditJobModal from './modals/EditJobModal.vue'
import ViewApplicationsModal from './modals/ViewApplicationsModal.vue'
import DeleteConfirmModal from '@/components/ui/DeleteConfirmModal.vue'
import Icon from '@/components/ui/Icon.vue'

// Composables
const { t } = useI18n()
const { showToast } = useToast()
const { debouncedValue: debouncedSearch } = useDebounce()

// API
const {
  jobs,
  loading,
  pagination,
  statistics,
  fetchJobs,
  createJob,
  updateJob,
  deleteJob: apiDeleteJob,
  toggleFeatureJob,
  exportJobs: apiExportJobs
} = useJobsApi()

// Selection
const {
  selectedItems: selectedJobs,
  selectAll: allJobsSelected,
  toggleSelection: toggleJobSelection,
  toggleAll: toggleAllJobs,
  clearSelection
} = useSelection()

// Reactive Data
const viewMode = ref('table')
const exporting = ref(false)
const sortField = ref('created_at')
const sortDirection = ref('desc')
const activeQuickFilter = ref('all')

// Modal states
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showApplicationsModal = ref(false)
const showDeleteModal = ref(false)
const selectedJob = ref(null)

// Filters
const filters = reactive({
  search: '',
  status: '',
  category: '',
  company: '',
  dateRange: null
})

// Computed Properties
const statistics = computed(() => [
  {
    key: 'total',
    title: t('admin.jobs.stats.total'),
    value: statistics.value?.total || 0,
    change: statistics.value?.totalChange || 0,
    icon: 'briefcase',
    color: 'blue'
  },
  {
    key: 'active',
    title: t('admin.jobs.stats.active'),
    value: statistics.value?.active || 0,
    change: statistics.value?.activeChange || 0,
    icon: 'check-circle',
    color: 'green'
  },
  {
    key: 'applications',
    title: t('admin.jobs.stats.applications'),
    value: statistics.value?.applications || 0,
    change: statistics.value?.applicationsChange || 0,
    icon: 'users',
    color: 'purple'
  },
  {
    key: 'featured',
    title: t('admin.jobs.stats.featured'),
    value: statistics.value?.featured || 0,
    change: statistics.value?.featuredChange || 0,
    icon: 'star',
    color: 'yellow'
  }
])

const statusOptions = computed(() => [
  { value: '', label: t('admin.jobs.all_statuses') },
  { value: 'active', label: t('admin.jobs.status.active') },
  { value: 'inactive', label: t('admin.jobs.status.inactive') },
  { value: 'pending', label: t('admin.jobs.status.pending') },
  { value: 'expired', label: t('admin.jobs.status.expired') }
])

const categoryOptions = computed(() => [
  { value: '', label: t('admin.jobs.all_categories') },
  // This would be populated from API
])

const companyOptions = computed(() => [
  { value: '', label: t('admin.jobs.all_companies') },
  // This would be populated from API
])

const quickFilters = computed(() => [
  {
    key: 'all',
    label: t('admin.jobs.filters.all'),
    count: statistics.value?.total || 0
  },
  {
    key: 'active',
    label: t('admin.jobs.filters.active'),
    count: statistics.value?.active || 0
  },
  {
    key: 'featured',
    label: t('admin.jobs.filters.featured'),
    count: statistics.value?.featured || 0
  },
  {
    key: 'expiring',
    label: t('admin.jobs.filters.expiring'),
    count: statistics.value?.expiring || 0
  },
  {
    key: 'no_applications',
    label: t('admin.jobs.filters.no_applications'),
    count: statistics.value?.noApplications || 0
  }
])

const tableColumns = computed(() => [
  { key: 'title', label: t('admin.jobs.columns.title'), sortable: true },
  { key: 'company', label: t('admin.jobs.columns.company'), sortable: true },
  { key: 'category', label: t('admin.jobs.columns.category'), sortable: false },
  { key: 'applications', label: t('admin.jobs.columns.applications'), sortable: true },
  { key: 'status', label: t('admin.jobs.columns.status'), sortable: true },
  { key: 'created_at', label: t('admin.jobs.columns.created'), sortable: true }
])

const perPageOptions = computed(() => [
  { value: 10, label: '10' },
  { value: 25, label: '25' },
  { value: 50, label: '50' },
  { value: 100, label: '100' }
])

// Methods
const loadJobs = async () => {
  try {
    await fetchJobs({
      ...filters,
      sort: sortField.value,
      direction: sortDirection.value,
      page: pagination.currentPage,
      perPage: pagination.perPage
    })
  } catch (error) {
    showToast(t('admin.jobs.errors.load_failed'), 'error')
  }
}

const applyFilters = () => {
  pagination.currentPage = 1
  loadJobs()
}

const applyQuickFilter = (filter) => {
  activeQuickFilter.value = filter.key
  
  // Reset other filters
  Object.keys(filters).forEach(key => {
    filters[key] = key === 'search' ? filters[key] : ''
  })

  // Apply specific filter logic
  switch (filter.key) {
    case 'active':
      filters.status = 'active'
      break
    case 'featured':
      filters.status = 'active'
      // Add featured filter logic
      break
    case 'expiring':
      filters.status = 'active'
      // Add expiring filter logic
      break
    case 'no_applications':
      filters.status = 'active'
      // Add no applications filter logic
      break
  }

  applyFilters()
}

const sort = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
  applyFilters()
}

const changePage = (page) => {
  pagination.currentPage = page
  loadJobs()
}

const changePerPage = (perPage) => {
  pagination.perPage = perPage
  pagination.currentPage = 1
  loadJobs()
}

const editJob = (job) => {
  selectedJob.value = job
  showEditModal.value = true
}

const deleteJob = (job) => {
  selectedJob.value = job
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  try {
    await apiDeleteJob(selectedJob.value.id)
    showToast(t('admin.jobs.delete_success'), 'success')
    loadJobs()
  } catch (error) {
    showToast(t('admin.jobs.errors.delete_failed'), 'error')
  } finally {
    showDeleteModal.value = false
    selectedJob.value = null
  }
}

const toggleFeature = async (job) => {
  try {
    await toggleFeatureJob(job.id)
    showToast(
      job.is_featured 
        ? t('admin.jobs.unfeatured_success') 
        : t('admin.jobs.featured_success'),
      'success'
    )
    loadJobs()
  } catch (error) {
    showToast(t('admin.jobs.errors.feature_failed'), 'error')
  }
}

const viewApplications = (job) => {
  selectedJob.value = job
  showApplicationsModal.value = true
}

const exportJobs = async () => {
  try {
    exporting.value = true
    await apiExportJobs(filters)
    showToast(t('admin.jobs.export_success'), 'success')
  } catch (error) {
    showToast(t('admin.jobs.errors.export_failed'), 'error')
  } finally {
    exporting.value = false
  }
}

const handleBulkAction = async (action) => {
  try {
    switch (action) {
      case 'delete':
        // Handle bulk delete
        break
      case 'feature':
        // Handle bulk feature
        break
      case 'activate':
        // Handle bulk activate
        break
      case 'deactivate':
        // Handle bulk deactivate
        break
    }
    clearSelection()
    loadJobs()
  } catch (error) {
    showToast(t('admin.jobs.errors.bulk_action_failed'), 'error')
  }
}

const onJobCreated = (job) => {
  showToast(t('admin.jobs.create_success'), 'success')
  loadJobs()
}

const onJobUpdated = (job) => {
  showToast(t('admin.jobs.update_success'), 'success')
  loadJobs()
}

// Watchers
watch(() => filters.search, debouncedSearch)
watch(debouncedSearch, applyFilters)

// Lifecycle
onMounted(() => {
  loadJobs()
})
</script>

<style scoped>
.job-management-container {
  @apply p-6 max-w-7xl mx-auto;
}

.btn-primary {
  @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors;
}

.btn-secondary {
  @apply inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700;
}

.btn-secondary:disabled {
  @apply opacity-50 cursor-not-allowed;
}
</style> 