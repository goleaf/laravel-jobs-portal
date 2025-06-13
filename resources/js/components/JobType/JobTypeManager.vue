<template>
  <div class="job-type-manager">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $t('job_type.title') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $t('job_type.pages.index') }}</p>
          </div>
          <div class="flex items-center gap-3">
            <button
              @click="showStatistics = !showStatistics"
              class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <ChartBarIcon class="h-4 w-4 mr-2" />
              {{ $t('job_type.actions.statistics') }}
            </button>
            <button
              @click="openCreateModal"
              class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
              <PlusIcon class="h-4 w-4 mr-2" />
              {{ $t('job_type.actions.create') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Panel -->
    <div v-if="showStatistics" class="bg-gray-50 border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <JobTypeStatistics :statistics="statistics" @refresh="loadStatistics" />
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Search -->
          <div class="flex-1">
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
              <input
                v-model="filters.search"
                type="text"
                :placeholder="$t('job_type.placeholders.search')"
                class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                @input="debouncedSearch"
              />
            </div>
          </div>

          <!-- Status Filter -->
          <select
            v-model="filters.status"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="">{{ $t('job_type.filters.all') }}</option>
            <option value="active">{{ $t('job_type.filters.active') }}</option>
            <option value="inactive">{{ $t('job_type.filters.inactive') }}</option>
          </select>

          <!-- Type Filter -->
          <select
            v-model="filters.type"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="">{{ $t('job_type.filters.all') }}</option>
            <option value="default">{{ $t('job_type.filters.default') }}</option>
            <option value="custom">{{ $t('job_type.filters.custom') }}</option>
            <option value="featured">{{ $t('job_type.filters.featured') }}</option>
          </select>

          <!-- Sort -->
          <select
            v-model="filters.sort"
            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="name">{{ $t('job_type.sorting.name_asc') }}</option>
            <option value="popularity">{{ $t('job_type.sorting.most_popular') }}</option>
            <option value="recent">{{ $t('job_type.sorting.created_newest') }}</option>
            <option value="usage">{{ $t('job_type.sorting.usage_high') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Job Types Table -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <!-- Bulk Actions -->
        <div v-if="selectedJobTypes.length > 0" class="bg-gray-50 px-6 py-3 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-700">
              {{ selectedJobTypes.length }} {{ $t('job_type.actions.selected') }}
            </span>
            <div class="flex gap-2">
              <button
                @click="bulkAction('activate')"
                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-md text-xs font-medium hover:bg-green-200"
              >
                {{ $t('job_type.actions.activate') }}
              </button>
              <button
                @click="bulkAction('deactivate')"
                class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-medium hover:bg-yellow-200"
              >
                {{ $t('job_type.actions.deactivate') }}
              </button>
              <button
                @click="bulkAction('delete')"
                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-800 rounded-md text-xs font-medium hover:bg-red-200"
              >
                {{ $t('job_type.actions.delete') }}
              </button>
            </div>
          </div>
        </div>

        <!-- Table -->
        <JobTypeTable
          :job-types="jobTypes.data"
          :selected-items="selectedJobTypes"
          @select="handleSelection"
          @edit="openEditModal"
          @delete="deleteJobType"
          @toggle-status="toggleStatus"
          @toggle-featured="toggleFeatured"
        />

        <!-- Pagination -->
        <div v-if="jobTypes.meta" class="bg-white px-6 py-3 border-t border-gray-200">
          <Pagination
            :current-page="jobTypes.meta.current_page"
            :last-page="jobTypes.meta.last_page"
            :total="jobTypes.meta.total"
            :per-page="jobTypes.meta.per_page"
            @page-changed="loadJobTypes"
          />
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <JobTypeModal
      v-if="showModal"
      :job-type="selectedJobType"
      :is-editing="isEditing"
      @close="closeModal"
      @saved="handleJobTypeSaved"
    />

    <!-- Delete Confirmation Modal -->
    <ConfirmationModal
      v-if="showDeleteConfirm"
      :title="$t('job_type.confirmations.delete')"
      :message="deleteMessage"
      @confirm="confirmDelete"
      @cancel="showDeleteConfirm = false"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { debounce } from 'lodash'
import {
  PlusIcon,
  MagnifyingGlassIcon,
  ChartBarIcon,
} from '@heroicons/vue/24/outline'

import JobTypeTable from './JobTypeTable.vue'
import JobTypeModal from './JobTypeModal.vue'
import JobTypeStatistics from './JobTypeStatistics.vue'
import Pagination from '../UI/Pagination.vue'
import ConfirmationModal from '../UI/ConfirmationModal.vue'
import { useJobTypeApi } from '../../composables/useJobTypeApi'
import { useNotifications } from '../../composables/useNotifications'

const { t } = useI18n()
const { showNotification } = useNotifications()
const {
  getJobTypes,
  createJobType,
  updateJobType,
  deleteJobType: apiDeleteJobType,
  getStatistics,
  bulkUpdate
} = useJobTypeApi()

// Reactive state
const loading = ref(false)
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const showStatistics = ref(false)
const isEditing = ref(false)
const selectedJobType = ref(null)
const jobTypesToDelete = ref([])
const selectedJobTypes = ref([])

const jobTypes = ref({ data: [], meta: null })
const statistics = ref({})

const filters = reactive({
  search: '',
  status: '',
  type: '',
  sort: 'name',
  page: 1,
  per_page: 15
})

// Computed properties
const deleteMessage = computed(() => {
  if (jobTypesToDelete.value.length === 1) {
    const jobType = jobTypesToDelete.value[0]
    return jobType.jobs_count > 0
      ? t('job_type.confirmations.delete_with_jobs', { count: jobType.jobs_count })
      : t('job_type.confirmations.delete')
  }
  return t('job_type.confirmations.bulk_delete')
})

// Methods
const loadJobTypes = async (page = 1) => {
  loading.value = true
  try {
    filters.page = page
    const response = await getJobTypes({
      ...filters,
      include_counts: true
    })
    jobTypes.value = response
  } catch (error) {
    showNotification(t('job_type.errors.loading_failed'), 'error')
  } finally {
    loading.value = false
  }
}

const loadStatistics = async () => {
  try {
    statistics.value = await getStatistics()
  } catch (error) {
    showNotification(t('job_type.errors.statistics_failed'), 'error')
  }
}

const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  filters.page = 1
  loadJobTypes(1)
}

const openCreateModal = () => {
  selectedJobType.value = null
  isEditing.value = false
  showModal.value = true
}

const openEditModal = (jobType) => {
  selectedJobType.value = { ...jobType }
  isEditing.value = true
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedJobType.value = null
  isEditing.value = false
}

const handleJobTypeSaved = (jobType) => {
  if (isEditing.value) {
    showNotification(t('job_type.messages.updated_successfully'), 'success')
  } else {
    showNotification(t('job_type.messages.created_successfully'), 'success')
  }
  closeModal()
  loadJobTypes(filters.page)
  if (showStatistics.value) {
    loadStatistics()
  }
}

const deleteJobType = (jobType) => {
  jobTypesToDelete.value = [jobType]
  showDeleteConfirm.value = true
}

const confirmDelete = async () => {
  try {
    for (const jobType of jobTypesToDelete.value) {
      await apiDeleteJobType(jobType.id)
    }
    showNotification(
      jobTypesToDelete.value.length === 1
        ? t('job_type.messages.deleted_successfully')
        : t('job_type.messages.bulk_updated', { count: jobTypesToDelete.value.length }),
      'success'
    )
    loadJobTypes(filters.page)
    if (showStatistics.value) {
      loadStatistics()
    }
  } catch (error) {
    showNotification(t('job_type.errors.deletion_failed'), 'error')
  } finally {
    showDeleteConfirm.value = false
    jobTypesToDelete.value = []
  }
}

const toggleStatus = async (jobType) => {
  try {
    await updateJobType(jobType.id, {
      is_active: !jobType.is_active
    })
    jobType.is_active = !jobType.is_active
    showNotification(
      jobType.is_active
        ? t('job_type.messages.activated_successfully')
        : t('job_type.messages.deactivated_successfully'),
      'success'
    )
  } catch (error) {
    showNotification(t('job_type.errors.update_failed'), 'error')
  }
}

const toggleFeatured = async (jobType) => {
  try {
    await updateJobType(jobType.id, {
      is_featured: !jobType.is_featured
    })
    jobType.is_featured = !jobType.is_featured
    showNotification(
      jobType.is_featured
        ? t('job_type.messages.featured_successfully')
        : t('job_type.messages.unfeatured_successfully'),
      'success'
    )
  } catch (error) {
    showNotification(t('job_type.errors.update_failed'), 'error')
  }
}

const handleSelection = (selection) => {
  selectedJobTypes.value = selection
}

const bulkAction = async (action) => {
  if (selectedJobTypes.value.length === 0) return

  try {
    await bulkUpdate({
      job_type_ids: selectedJobTypes.value.map(jt => jt.id),
      action
    })
    
    showNotification(
      t('job_type.messages.bulk_updated', { count: selectedJobTypes.value.length }),
      'success'
    )
    
    selectedJobTypes.value = []
    loadJobTypes(filters.page)
    if (showStatistics.value) {
      loadStatistics()
    }
  } catch (error) {
    showNotification(t('job_type.errors.bulk_action_failed'), 'error')
  }
}

// Lifecycle
onMounted(() => {
  loadJobTypes()
  loadStatistics()
})
</script>

<style scoped>
.job-type-manager {
  @apply min-h-screen bg-gray-50;
}
</style> 