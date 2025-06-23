<template>
  <div class="job-management min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">{{ $t('jobs.management.title') }}</h1>
          <p class="text-sm text-gray-600 mt-1">{{ $t('jobs.management.subtitle') }}</p>
        </div>
        <div class="flex items-center space-x-3">
          <button
            @click="refreshJobs"
            :disabled="loading"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <svg class="h-4 w-4 mr-2" :class="{ 'animate-spin': loading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ $t('common.refresh') }}
          </button>
          <router-link
            to="/admin/jobs/create"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ $t('jobs.actions.create') }}
          </router-link>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('jobs.filters.status') }}
          </label>
          <select
            v-model="filters.status"
            @change="applyFilters"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.all') }}</option>
            <option value="open">{{ $t('jobs.status.open') }}</option>
            <option value="closed">{{ $t('jobs.status.closed') }}</option>
            <option value="drafted">{{ $t('jobs.status.drafted') }}</option>
            <option value="paused">{{ $t('jobs.status.paused') }}</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('jobs.filters.company') }}
          </label>
          <select
            v-model="filters.company_id"
            @change="applyFilters"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.all') }}</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.name }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('jobs.filters.search') }}
          </label>
          <input
            v-model="filters.search"
            @input="debounceSearch"
            type="text"
            :placeholder="$t('jobs.filters.search_placeholder')"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('jobs.filters.date_range') }}
          </label>
          <select
            v-model="filters.date_range"
            @change="applyFilters"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
          >
            <option value="">{{ $t('common.all_time') }}</option>
            <option value="today">{{ $t('common.today') }}</option>
            <option value="week">{{ $t('common.this_week') }}</option>
            <option value="month">{{ $t('common.this_month') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-6 py-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <BriefcaseIcon class="h-6 w-6 text-gray-400" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ $t('jobs.stats.total') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ statistics.total || 0 }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <CheckCircleIcon class="h-6 w-6 text-green-400" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ $t('jobs.stats.active') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ statistics.active || 0 }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <UserGroupIcon class="h-6 w-6 text-blue-400" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ $t('jobs.stats.applications') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ statistics.applications || 0 }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
        
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <StarIcon class="h-6 w-6 text-yellow-400" />
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ $t('jobs.stats.featured') }}
                  </dt>
                  <dd class="text-lg font-medium text-gray-900">
                    {{ statistics.featured || 0 }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Jobs Table -->
    <div class="px-6 pb-6">
      <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:p-6">
          <!-- Loading State -->
          <div v-if="loading" class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <span class="ml-3 text-gray-600">{{ $t('common.loading') }}</span>
          </div>

          <!-- Jobs List -->
          <div v-else-if="jobs.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('jobs.table.title') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('jobs.table.company') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('jobs.table.status') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('jobs.table.applications') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('jobs.table.created') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ $t('jobs.table.expires') }}
                  </th>
                  <th class="relative px-6 py-3">
                    <span class="sr-only">{{ $t('common.actions') }}</span>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="job in jobs" :key="job.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                          <BriefcaseIcon class="h-5 w-5 text-indigo-600" />
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ job.title }}
                        </div>
                        <div class="text-sm text-gray-500">
                          {{ job.job_type }} • {{ job.category }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ job.company?.name }}</div>
                    <div class="text-sm text-gray-500">{{ job.location?.city }}, {{ job.location?.country }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="getStatusBadgeClass(job.status)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                      {{ $t(`jobs.status.${job.status}`) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ job.statistics?.applications_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ formatDate(job.dates?.created) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span v-if="job.dates?.expires" :class="{ 'text-red-600': job.dates?.is_expired }">
                      {{ formatDate(job.dates?.expires) }}
                    </span>
                    <span v-else class="text-gray-400">{{ $t('common.never') }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center space-x-2">
                      <router-link
                        :to="`/admin/jobs/${job.id}`"
                        class="text-indigo-600 hover:text-indigo-900"
                        :title="$t('common.view')"
                      >
                        <EyeIcon class="h-4 w-4" />
                      </router-link>
                      <router-link
                        v-if="job.permissions?.can_edit"
                        :to="`/admin/jobs/${job.id}/edit`"
                        class="text-gray-600 hover:text-gray-900"
                        :title="$t('common.edit')"
                      >
                        <PencilIcon class="h-4 w-4" />
                      </router-link>
                      <button
                        v-if="job.permissions?.can_feature"
                        @click="toggleFeature(job)"
                        :class="job.flags?.is_featured ? 'text-yellow-600 hover:text-yellow-900' : 'text-gray-400 hover:text-gray-600'"
                        :title="job.flags?.is_featured ? $t('jobs.actions.unfeature') : $t('jobs.actions.feature')"
                      >
                        <StarIcon class="h-4 w-4" />
                      </button>
                      <button
                        v-if="job.permissions?.can_delete"
                        @click="confirmDelete(job)"
                        class="text-red-600 hover:text-red-900"
                        :title="$t('common.delete')"
                      >
                        <TrashIcon class="h-4 w-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8zM8 14v.01M12 14v.01M16 14v.01" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('jobs.empty.title') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ $t('jobs.empty.description') }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <TransitionRoot as="template" :show="showDeleteModal">
      <Dialog as="div" class="relative z-10" @close="showDeleteModal = false">
        <TransitionChild
          as="template"
          enter="ease-out duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="ease-in duration-200"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
        </TransitionChild>

        <div class="fixed inset-0 z-10 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <TransitionChild
              as="template"
              enter="ease-out duration-300"
              enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              enter-to="opacity-100 translate-y-0 sm:scale-100"
              leave="ease-in duration-200"
              leave-from="opacity-100 translate-y-0 sm:scale-100"
              leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
              <DialogPanel class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                  <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                      <ExclamationTriangleIcon class="h-6 w-6 text-red-600" />
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                      <DialogTitle as="h3" class="text-lg font-medium leading-6 text-gray-900">
                        {{ $t('jobs.delete.title') }}
                      </DialogTitle>
                      <div class="mt-2">
                        <p class="text-sm text-gray-500">
                          {{ $t('jobs.delete.message', { title: jobToDelete?.title }) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                  <button
                    @click="deleteJob"
                    :disabled="deleting"
                    class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                  >
                    <span v-if="deleting" class="flex items-center">
                      <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                      {{ $t('common.deleting') }}
                    </span>
                    <span v-else>{{ $t('common.delete') }}</span>
                  </button>
                  <button
                    @click="showDeleteModal = false"
                    :disabled="deleting"
                    class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                  >
                    {{ $t('common.cancel') }}
                  </button>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { 
  Dialog, 
  DialogPanel, 
  DialogTitle, 
  TransitionChild, 
  TransitionRoot 
} from '@headlessui/vue'
import {
  BriefcaseIcon,
  CheckCircleIcon,
  UserGroupIcon,
  StarIcon,
  PlusIcon,
  RefreshIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  ExclamationTriangleIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline'
import { debounce } from 'lodash-es'
import { jobService } from '@/services/jobService'
import { companyService } from '@/services/companyService'
import type { Job, Company, JobFilters, Pagination, Statistics } from '@/types'

// Composables
const { t } = useI18n()
const router = useRouter()

// Reactive state
const loading = ref(false)
const deleting = ref(false)
const showDeleteModal = ref(false)
const jobToDelete = ref<Job | null>(null)

const jobs = ref<Job[]>([])
const companies = ref<Company[]>([])
const statistics = ref<Statistics>({
  total: 0,
  active: 0,
  applications: 0,
  featured: 0
})

const pagination = ref<Pagination>({
  current_page: 1,
  per_page: 15,
  total: 0,
  from: 0,
  to: 0,
  prev_page_url: null,
  next_page_url: null,
  last_page: 1
})

const filters = reactive<JobFilters>({
  status: '',
  company_id: '',
  search: '',
  date_range: '',
  sort_by: 'created_at',
  sort_order: 'desc'
})

// Computed properties
const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  const pages: number[] = []
  
  // Always show first page
  if (current > 3) pages.push(1)
  
  // Show pages around current
  for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
    pages.push(i)
  }
  
  // Always show last page
  if (current < last - 2) pages.push(last)
  
  return [...new Set(pages)].sort((a, b) => a - b)
})

// Methods
const loadJobs = async (page = 1) => {
  try {
    loading.value = true
    const response = await jobService.getJobs({
      ...filters,
      page,
      per_page: pagination.value.per_page
    })
    
    jobs.value = response.data
    pagination.value = response.meta.pagination
    statistics.value = response.meta.statistics
  } catch (error) {
    console.error('Failed to load jobs:', error)
    // Handle error with toast notification
  } finally {
    loading.value = false
  }
}

const loadCompanies = async () => {
  try {
    const response = await companyService.getCompanies({ per_page: 1000 })
    companies.value = response.data
  } catch (error) {
    console.error('Failed to load companies:', error)
  }
}

const refreshJobs = () => {
  loadJobs(pagination.value.current_page)
}

const applyFilters = () => {
  loadJobs(1)
}

const debounceSearch = debounce(() => {
  applyFilters()
}, 500)

const goToPage = (page: number) => {
  loadJobs(page)
}

const previousPage = () => {
  if (pagination.value.current_page > 1) {
    loadJobs(pagination.value.current_page - 1)
  }
}

const nextPage = () => {
  if (pagination.value.current_page < pagination.value.last_page) {
    loadJobs(pagination.value.current_page + 1)
  }
}

const getStatusBadgeClass = (status: string) => {
  const classes = {
    open: 'bg-green-100 text-green-800',
    closed: 'bg-red-100 text-red-800',
    drafted: 'bg-gray-100 text-gray-800',
    paused: 'bg-yellow-100 text-yellow-800'
  }
  return classes[status as keyof typeof classes] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

const toggleFeature = async (job: Job) => {
  try {
    if (job.flags?.is_featured) {
      await jobService.unfeatureJob(job.id)
    } else {
      await jobService.featureJob(job.id)
    }
    refreshJobs()
  } catch (error) {
    console.error('Failed to toggle feature:', error)
  }
}

const confirmDelete = (job: Job) => {
  jobToDelete.value = job
  showDeleteModal.value = true
}

const deleteJob = async () => {
  if (!jobToDelete.value) return
  
  try {
    deleting.value = true
    await jobService.deleteJob(jobToDelete.value.id)
    showDeleteModal.value = false
    jobToDelete.value = null
    refreshJobs()
  } catch (error) {
    console.error('Failed to delete job:', error)
  } finally {
    deleting.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadJobs()
  loadCompanies()
})

// Watchers
watch(() => filters.search, debounceSearch)
</script>

<style scoped>
.job-management {
  @apply min-h-screen bg-gray-50;
}
</style> 