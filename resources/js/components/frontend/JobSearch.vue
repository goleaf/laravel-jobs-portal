<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Search Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center">
          <h1 class="text-4xl font-bold text-white mb-4">
            {{ $t('jobs.search.hero_title') }}
          </h1>
          <p class="text-xl text-blue-100 mb-8">
            {{ $t('jobs.search.hero_subtitle') }}
          </p>
          
          <!-- Main Search Form -->
          <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
              <form @submit.prevent="searchJobs" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <!-- Keywords -->
                  <div class="relative">
                    <input
                      v-model="searchForm.keywords"
                      type="text"
                      :placeholder="$t('jobs.search.keywords_placeholder')"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                    <MagnifyingGlassIcon class="absolute right-3 top-3 h-6 w-6 text-gray-400" />
                  </div>
                  
                  <!-- Location -->
                  <div class="relative">
                    <select
                      v-model="searchForm.location"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                    >
                      <option value="">{{ $t('jobs.search.all_locations') }}</option>
                      <option v-for="city in cities" :key="city.id" :value="city.id">
                        {{ city.name }}, {{ city.state.name }}
                      </option>
                    </select>
                    <MapPinIcon class="absolute right-3 top-3 h-6 w-6 text-gray-400 pointer-events-none" />
                  </div>
                  
                  <!-- Category -->
                  <div class="relative">
                    <select
                      v-model="searchForm.category"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none"
                    >
                      <option value="">{{ $t('jobs.search.all_categories') }}</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                      </option>
                    </select>
                    <FolderOpenIcon class="absolute right-3 top-3 h-6 w-6 text-gray-400 pointer-events-none" />
                  </div>
                </div>
                
                <!-- Search Button -->
                <div class="flex justify-center">
                  <button
                    type="submit"
                    :disabled="isSearching"
                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <MagnifyingGlassIcon v-if="!isSearching" class="h-5 w-5 mr-2" />
                    <svg v-else class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ isSearching ? $t('jobs.search.searching') : $t('jobs.search.search_jobs') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Results Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:w-1/4">
          <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">{{ $t('jobs.search.filters') }}</h3>
              <button
                @click="clearFilters"
                class="text-sm text-blue-600 hover:text-blue-800"
              >
                {{ $t('jobs.search.clear_all') }}
              </button>
            </div>

            <!-- Job Type Filter -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $t('jobs.search.job_type') }}</h4>
              <div class="space-y-2">
                <label v-for="type in jobTypes" :key="type.id" class="flex items-center">
                  <input
                    type="checkbox"
                    :value="type.id"
                    v-model="filters.jobTypes"
                    @change="applyFilters"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ type.name }}</span>
                  <span class="ml-auto text-xs text-gray-500">({{ type.jobs_count || 0 }})</span>
                </label>
              </div>
            </div>

            <!-- Experience Level -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $t('jobs.search.experience_level') }}</h4>
              <div class="space-y-2">
                <label v-for="level in experienceLevels" :key="level.id" class="flex items-center">
                  <input
                    type="checkbox"
                    :value="level.id"
                    v-model="filters.experienceLevels"
                    @change="applyFilters"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ level.name }}</span>
                  <span class="ml-auto text-xs text-gray-500">({{ level.jobs_count || 0 }})</span>
                </label>
              </div>
            </div>

            <!-- Salary Range -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $t('jobs.search.salary_range') }}</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-xs text-gray-600 mb-1">{{ $t('jobs.search.min_salary') }}</label>
                  <input
                    v-model.number="filters.minSalary"
                    type="number"
                    :placeholder="$t('jobs.search.min_salary_placeholder')"
                    @input="debouncedApplyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">{{ $t('jobs.search.max_salary') }}</label>
                  <input
                    v-model.number="filters.maxSalary"
                    type="number"
                    :placeholder="$t('jobs.search.max_salary_placeholder')"
                    @input="debouncedApplyFilters"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
              </div>
            </div>

            <!-- Company Size -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $t('jobs.search.company_size') }}</h4>
              <div class="space-y-2">
                <label v-for="size in companySizes" :key="size.id" class="flex items-center">
                  <input
                    type="checkbox"
                    :value="size.id"
                    v-model="filters.companySizes"
                    @change="applyFilters"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ size.size }}</span>
                </label>
              </div>
            </div>

            <!-- Remote Work -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $t('jobs.search.work_arrangement') }}</h4>
              <div class="space-y-2">
                <label class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="filters.remoteWork"
                    @change="applyFilters"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ $t('jobs.search.remote_work') }}</span>
                </label>
                <label class="flex items-center">
                  <input
                    type="checkbox"
                    v-model="filters.urgentHiring"
                    @change="applyFilters"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ $t('jobs.search.urgent_hiring') }}</span>
                </label>
              </div>
            </div>

            <!-- Date Posted -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-900 mb-3">{{ $t('jobs.search.date_posted') }}</h4>
              <select
                v-model="filters.datePosted"
                @change="applyFilters"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">{{ $t('jobs.search.any_time') }}</option>
                <option value="today">{{ $t('jobs.search.today') }}</option>
                <option value="week">{{ $t('jobs.search.this_week') }}</option>
                <option value="month">{{ $t('jobs.search.this_month') }}</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Results Section -->
        <div class="lg:w-3/4">
          <!-- Results Header -->
          <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h2 class="text-xl font-semibold text-gray-900">
                  {{ $t('jobs.search.results_title') }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                  {{ $t('jobs.search.results_count', { count: totalJobs }) }}
                </p>
              </div>
              
              <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                <!-- View Toggle -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                  <button
                    @click="viewMode = 'list'"
                    :class="[
                      'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                      viewMode === 'list' 
                        ? 'bg-white text-gray-900 shadow-sm' 
                        : 'text-gray-600 hover:text-gray-900'
                    ]"
                  >
                    <Bars3Icon class="h-4 w-4" />
                  </button>
                  <button
                    @click="viewMode = 'grid'"
                    :class="[
                      'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                      viewMode === 'grid' 
                        ? 'bg-white text-gray-900 shadow-sm' 
                        : 'text-gray-600 hover:text-gray-900'
                    ]"
                  >
                    <Squares2X2Icon class="h-4 w-4" />
                  </button>
                </div>

                <!-- Sort Options -->
                <select
                  v-model="sortBy"
                  @change="applyFilters"
                  class="px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="relevance">{{ $t('jobs.search.sort_relevance') }}</option>
                  <option value="date">{{ $t('jobs.search.sort_date') }}</option>
                  <option value="salary">{{ $t('jobs.search.sort_salary') }}</option>
                  <option value="company">{{ $t('jobs.search.sort_company') }}</option>
                </select>
              </div>
            </div>
          </div>

          <!-- Job Listings -->
          <div v-if="isLoading" class="space-y-4">
            <div v-for="n in 5" :key="`skeleton-${n}`" class="bg-white rounded-lg shadow-sm p-6 animate-pulse">
              <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                <div class="flex-1 space-y-2">
                  <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                  <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                  <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="jobs.length === 0" class="bg-white rounded-lg shadow-sm p-12 text-center">
            <BriefcaseIcon class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-4 text-lg font-medium text-gray-900">{{ $t('jobs.search.no_jobs_found') }}</h3>
            <p class="mt-2 text-sm text-gray-600">{{ $t('jobs.search.no_jobs_message') }}</p>
            <button
              @click="clearFilters"
              class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200"
            >
              {{ $t('jobs.search.clear_filters_try_again') }}
            </button>
          </div>

          <!-- Job Cards -->
          <div v-else :class="[
            'grid gap-6',
            viewMode === 'grid' ? 'grid-cols-1 lg:grid-cols-2' : 'grid-cols-1'
          ]">
            <JobCard
              v-for="job in jobs"
              :key="job.id"
              :job="job"
              :view-mode="viewMode"
              @save="toggleSaveJob"
              @apply="openApplyModal"
              @view="viewJobDetails"
            />
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8">
            <Pagination
              :current-page="currentPage"
              :total-pages="totalPages"
              :per-page="perPage"
              :total="totalJobs"
              @page-changed="changePage"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Apply Modal -->
    <JobApplicationModal
      v-if="showApplyModal"
      :job="selectedJob"
      @close="showApplyModal = false"
      @applied="handleJobApplied"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { OptimizedLodash } from '@/utils/dynamicImports'
import {
  MagnifyingGlassIcon,
  MapPinIcon,
  FolderOpenIcon,
  BriefcaseIcon,
  Bars3Icon,
  Squares2X2Icon
} from '@heroicons/vue/24/outline'

import JobCard from '@/components/jobs/JobCard.vue'
import JobApplicationModal from '@/components/jobs/JobApplicationModal.vue'
import Pagination from '@/components/ui/Pagination.vue'
import { jobsApi } from '@/services/api'
import { useToast } from '@/composables/useToast'

const { t } = useI18n()
const router = useRouter()
const route = useRoute()
const { showToast } = useToast()

// Reactive state
const isLoading = ref(false)
const isSearching = ref(false)
const jobs = ref([])
const totalJobs = ref(0)
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = ref(12)
const viewMode = ref('list')
const sortBy = ref('relevance')

// Filter data
const cities = ref([])
const categories = ref([])
const jobTypes = ref([])
const experienceLevels = ref([])
const companySizes = ref([])

// Search form
const searchForm = reactive({
  keywords: '',
  location: '',
  category: ''
})

// Filters
const filters = reactive({
  jobTypes: [],
  experienceLevels: [],
  companySizes: [],
  minSalary: null,
  maxSalary: null,
  remoteWork: false,
  urgentHiring: false,
  datePosted: ''
})

// Modal state
const showApplyModal = ref(false)
const selectedJob = ref(null)

// Computed
const allCompaniesSelected = computed(() => {
  return companies.value.length > 0 && selectedCompanies.value.length === companies.value.length
})

// Methods
const searchJobs = async () => {
  isSearching.value = true
  currentPage.value = 1
  await loadJobs()
  isSearching.value = false
}

const loadJobs = async () => {
  try {
    isLoading.value = true
    
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      sort_by: sortBy.value,
      ...searchForm,
      ...filters
    }

    const response = await jobsApi.getJobs(params)
    
    jobs.value = response.data.data
    totalJobs.value = response.data.total
    currentPage.value = response.data.current_page
    totalPages.value = response.data.last_page

    // Update URL with search params
    updateURLParams()
    
  } catch (error) {
    showToast(error.response?.data?.message || t('jobs.search.error_loading'), 'error')
  } finally {
    isLoading.value = false
  }
}

const applyFilters = () => {
  currentPage.value = 1
  loadJobs()
}

const debouncedApplyFilters = OptimizedLodash.debounce(applyFilters, 500)

const clearFilters = () => {
  Object.assign(searchForm, {
    keywords: '',
    location: '',
    category: ''
  })
  
  Object.assign(filters, {
    jobTypes: [],
    experienceLevels: [],
    companySizes: [],
    minSalary: null,
    maxSalary: null,
    remoteWork: false,
    urgentHiring: false,
    datePosted: ''
  })
  
  sortBy.value = 'relevance'
  applyFilters()
}

const changePage = (page) => {
  currentPage.value = page
  loadJobs()
}

const toggleSaveJob = async (job) => {
  try {
    if (job.is_saved) {
      await jobsApi.unsaveJob(job.id)
      job.is_saved = false
      showToast(t('jobs.search.job_unsaved'), 'success')
    } else {
      await jobsApi.saveJob(job.id)
      job.is_saved = true
      showToast(t('jobs.search.job_saved'), 'success')
    }
  } catch (error) {
    showToast(error.response?.data?.message || t('jobs.search.error_saving'), 'error')
  }
}

const openApplyModal = (job) => {
  selectedJob.value = job
  showApplyModal.value = true
}

const viewJobDetails = (job) => {
  router.push({ name: 'job-details', params: { id: job.id } })
}

const handleJobApplied = (application) => {
  showApplyModal.value = false
  showToast(t('jobs.search.application_submitted'), 'success')
  
  // Update job application status
  const job = jobs.value.find(j => j.id === application.job_id)
  if (job) {
    job.has_applied = true
    job.applications_count++
  }
}

const loadFilterData = async () => {
  try {
    const [citiesResponse, categoriesResponse, typesResponse, levelsResponse, sizesResponse] = await Promise.all([
      jobsApi.getCities(),
      jobsApi.getCategories(),
      jobsApi.getJobTypes(),
      jobsApi.getExperienceLevels(),
      jobsApi.getCompanySizes()
    ])

    cities.value = citiesResponse.data
    categories.value = categoriesResponse.data
    jobTypes.value = typesResponse.data
    experienceLevels.value = levelsResponse.data
    companySizes.value = sizesResponse.data
  } catch (error) {
    console.error('Error loading filter data:', error)
  }
}

const updateURLParams = () => {
  const params = new URLSearchParams()
  
  if (searchForm.keywords) params.set('q', searchForm.keywords)
  if (searchForm.location) params.set('location', searchForm.location)
  if (searchForm.category) params.set('category', searchForm.category)
  if (currentPage.value > 1) params.set('page', currentPage.value)
  
  const newURL = `${window.location.pathname}?${params.toString()}`
  window.history.replaceState(null, '', newURL)
}

const loadFromURLParams = () => {
  const params = new URLSearchParams(window.location.search)
  
  searchForm.keywords = params.get('q') || ''
  searchForm.location = params.get('location') || ''
  searchForm.category = params.get('category') || ''
  currentPage.value = parseInt(params.get('page')) || 1
}

// Lifecycle
onMounted(async () => {
  await loadFilterData()
  loadFromURLParams()
  await loadJobs()
})

// Watchers
watch(sortBy, () => {
  applyFilters()
})
</script>
