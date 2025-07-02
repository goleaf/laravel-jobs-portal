<template>
  <MainLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="bg-white px-6 py-8">
        <div class="max-w-7xl mx-auto">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                Application Management
              </h1>
              <p class="text-gray-600 text-lg">
                Review and manage {{ totalApplications }} applications across {{ totalJobs }} job postings
              </p>
            </div>
            
            <div class="flex gap-3">
              <BaseButton
                variant="outline-primary"
                @click="exportApplications"
                :disabled="applications.length === 0"
              >
                <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
                Export
              </BaseButton>
              
              <BaseButton
                variant="primary"
                :to="{ name: 'employer.jobs.create' }"
                tag="router-link"
              >
                <PlusIcon class="h-4 w-4 mr-2" />
                Post New Job
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Statistics Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <DocumentTextIcon class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Applications</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <ClockIcon class="h-6 w-6 text-yellow-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Pending Review</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <CalendarIcon class="h-6 w-6 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Interviews</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.interviews }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <CheckCircleIcon class="h-6 w-6 text-purple-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Hired</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.hired }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <div class="relative">
              <input
                v-model="filters.search"
                type="text"
                placeholder="Search candidates..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
              />
              <MagnifyingGlassIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
            </div>
          </div>

          <!-- Job Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Job Position</label>
            <select
              v-model="filters.job_id"
              class="block w-full border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Jobs</option>
              <option v-for="job in jobs" :key="job.id" :value="job.id">
                {{ job.title }}
              </option>
            </select>
          </div>

          <!-- Status Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select
              v-model="filters.status"
              class="block w-full border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="reviewing">Under Review</option>
              <option value="shortlisted">Shortlisted</option>
              <option value="interview">Interview</option>
              <option value="rejected">Rejected</option>
              <option value="hired">Hired</option>
            </select>
          </div>

          <!-- Date Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Applied Date</label>
            <input
              v-model="filters.date_from"
              type="date"
              class="block w-full border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
            />
          </div>
        </div>
        
        <!-- Active Filters -->
        <div v-if="activeFiltersCount > 0" class="mt-4 flex items-center gap-2">
          <span class="text-sm text-gray-600">Active filters:</span>
          <div class="flex gap-2">
            <span
              v-for="filter in activeFilters"
              :key="filter.key"
              class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800"
            >
              {{ filter.label }}
              <button
                @click="clearFilter(filter.key)"
                class="ml-1 text-indigo-600 hover:text-indigo-800"
              >
                <XMarkIcon class="h-3 w-3" />
              </button>
            </span>
          </div>
          <button
            @click="clearAllFilters"
            class="text-sm text-indigo-600 hover:text-indigo-800"
          >
            Clear all
          </button>
        </div>
      </div>

      <!-- Applications List -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <!-- List Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
              Applications ({{ filteredApplications.length }})
            </h2>
            
            <div class="flex items-center gap-3">
              <!-- Bulk Actions -->
              <div v-if="selectedApplications.length > 0" class="flex items-center gap-2">
                <span class="text-sm text-gray-600">
                  {{ selectedApplications.length }} selected
                </span>
                <select
                  v-model="bulkAction"
                  @change="performBulkAction"
                  class="text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="">Bulk Actions</option>
                  <option value="shortlist">Shortlist</option>
                  <option value="reject">Reject</option>
                  <option value="schedule-interview">Schedule Interview</option>
                  <option value="mark-reviewed">Mark as Reviewed</option>
                </select>
              </div>
              
              <!-- View Toggle -->
              <div class="flex rounded-lg border border-gray-300">
                <button
                  @click="viewMode = 'list'"
                  :class="[
                    'px-3 py-1 text-sm',
                    viewMode === 'list' 
                      ? 'bg-indigo-600 text-white' 
                      : 'bg-white text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  List
                </button>
                <button
                  @click="viewMode = 'cards'"
                  :class="[
                    'px-3 py-1 text-sm',
                    viewMode === 'cards' 
                      ? 'bg-indigo-600 text-white' 
                      : 'bg-white text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  Cards
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoading" class="p-6">
          <div class="space-y-4">
            <div v-for="i in 5" :key="i" class="animate-pulse flex space-x-4">
              <div class="rounded-full bg-gray-200 h-12 w-12"></div>
              <div class="flex-1 space-y-2 py-1">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Applications -->
        <div v-else-if="filteredApplications.length > 0">
          <!-- List View -->
          <div v-if="viewMode === 'list'" class="divide-y divide-gray-200">
            <div
              v-for="application in paginatedApplications"
              :key="application.id"
              class="p-6 hover:bg-gray-50 transition-colors duration-200"
            >
              <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                  <!-- Selection Checkbox -->
                  <input
                    type="checkbox"
                    :value="application.id"
                    v-model="selectedApplications"
                    class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  />
                  
                  <!-- Candidate Info -->
                  <div class="flex-1">
                    <div class="flex items-center space-x-4">
                      <!-- Avatar -->
                      <img
                        v-if="application.candidate?.avatar"
                        :src="application.candidate.avatar"
                        :alt="application.candidate.name"
                        class="w-12 h-12 rounded-full object-cover"
                      />
                      <div
                        v-else
                        class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center"
                      >
                        <span class="text-white font-medium text-lg">
                          {{ (application.candidate?.name || 'C').charAt(0).toUpperCase() }}
                        </span>
                      </div>
                      
                      <!-- Details -->
                      <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-1">
                          <h3 class="text-lg font-medium text-gray-900">
                            {{ application.candidate?.name }}
                          </h3>
                          <span
                            :class="[
                              'px-2 py-1 text-xs font-medium rounded-full',
                              getStatusColor(application.status)
                            ]"
                          >
                            {{ getStatusLabel(application.status) }}
                          </span>
                          <span
                            v-if="application.match_score"
                            class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800"
                          >
                            {{ application.match_score }}% match
                          </span>
                        </div>
                        
                        <p class="text-gray-600 mb-2">Applied for {{ application.job?.title }}</p>
                        
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                          <span>{{ formatDate(application.applied_at) }}</span>
                          <span>•</span>
                          <span>{{ application.candidate?.experience_years || 0 }}+ years experience</span>
                          <span v-if="application.candidate?.location">•</span>
                          <span v-if="application.candidate?.location">{{ application.candidate.location }}</span>
                        </div>
                        
                        <!-- Skills -->
                        <div v-if="application.candidate?.skills?.length" class="mt-2">
                          <div class="flex flex-wrap gap-1">
                            <span
                              v-for="skill in application.candidate.skills.slice(0, 5)"
                              :key="skill.id"
                              class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded-md"
                            >
                              {{ skill.name }}
                            </span>
                            <span
                              v-if="application.candidate.skills.length > 5"
                              class="px-2 py-1 text-xs bg-gray-100 text-gray-500 rounded-md"
                            >
                              +{{ application.candidate.skills.length - 5 }} more
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center space-x-2 ml-4">
                  <BaseButton
                    variant="ghost"
                    size="sm"
                    @click="viewApplication(application)"
                  >
                    <EyeIcon class="h-4 w-4 mr-1" />
                    View
                  </BaseButton>
                  
                  <BaseButton
                    v-if="application.status === 'pending'"
                    variant="outline-primary"
                    size="sm"
                    @click="shortlistCandidate(application)"
                  >
                    <CheckIcon class="h-4 w-4 mr-1" />
                    Shortlist
                  </BaseButton>
                  
                  <BaseButton
                    v-if="['pending', 'reviewing', 'shortlisted'].includes(application.status)"
                    variant="primary"
                    size="sm"
                    @click="scheduleInterview(application)"
                  >
                    <CalendarIcon class="h-4 w-4 mr-1" />
                    Interview
                  </BaseButton>
                  
                  <!-- Dropdown Menu -->
                  <div class="relative">
                    <button
                      @click="toggleDropdown(application.id)"
                      class="p-1 text-gray-400 hover:text-gray-600"
                    >
                      <EllipsisVerticalIcon class="h-5 w-5" />
                    </button>
                    
                    <div
                      v-if="openDropdown === application.id"
                      class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200"
                    >
                      <div class="py-1">
                        <button
                          @click="downloadResume(application)"
                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                          Download Resume
                        </button>
                        <button
                          @click="sendMessage(application)"
                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                          Send Message
                        </button>
                        <button
                          @click="addNotes(application)"
                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                        >
                          Add Notes
                        </button>
                        <hr class="my-1">
                        <button
                          @click="rejectApplication(application)"
                          class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                        >
                          Reject Application
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card View -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
            <div
              v-for="application in paginatedApplications"
              :key="application.id"
              class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
            >
              <!-- Card content would go here -->
              <div class="text-center">
                <img
                  v-if="application.candidate?.avatar"
                  :src="application.candidate.avatar"
                  :alt="application.candidate.name"
                  class="w-16 h-16 rounded-full mx-auto mb-4 object-cover"
                />
                <div
                  v-else
                  class="w-16 h-16 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4"
                >
                  <span class="text-white font-medium text-xl">
                    {{ (application.candidate?.name || 'C').charAt(0).toUpperCase() }}
                  </span>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900 mb-2">
                  {{ application.candidate?.name }}
                </h3>
                <p class="text-gray-600 mb-4">{{ application.job?.title }}</p>
                
                <div class="flex justify-center space-x-2">
                  <BaseButton
                    variant="outline-primary"
                    size="sm"
                    @click="viewApplication(application)"
                  >
                    View
                  </BaseButton>
                  <BaseButton
                    variant="primary"
                    size="sm"
                    @click="scheduleInterview(application)"
                  >
                    Interview
                  </BaseButton>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="p-12 text-center">
          <DocumentTextIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">No applications found</h3>
          <p class="text-gray-600 mb-6">
            {{ activeFiltersCount > 0 
              ? 'Try adjusting your filters to see more results.' 
              : 'Start by posting your first job to receive applications.' 
            }}
          </p>
          <BaseButton
            v-if="activeFiltersCount > 0"
            variant="outline-primary"
            @click="clearAllFilters"
          >
            Clear Filters
          </BaseButton>
          <BaseButton
            v-else
            variant="primary"
            :to="{ name: 'employer.jobs.create' }"
            tag="router-link"
          >
            Post Your First Job
          </BaseButton>
        </div>

        <!-- Pagination -->
        <div
          v-if="filteredApplications.length > pageSize"
          class="px-6 py-4 border-t border-gray-200 flex items-center justify-between"
        >
          <div class="text-sm text-gray-700">
            Showing {{ ((currentPage - 1) * pageSize) + 1 }} to 
            {{ Math.min(currentPage * pageSize, filteredApplications.length) }} of 
            {{ filteredApplications.length }} results
          </div>
          
          <div class="flex space-x-1">
            <button
              @click="currentPage = Math.max(1, currentPage - 1)"
              :disabled="currentPage === 1"
              class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="currentPage = page"
              :class="[
                'px-3 py-2 text-sm border rounded-md',
                page === currentPage
                  ? 'border-indigo-600 bg-indigo-600 text-white'
                  : 'border-gray-300 hover:bg-gray-50'
              ]"
            >
              {{ page }}
            </button>
            
            <button
              @click="currentPage = Math.min(totalPages, currentPage + 1)"
              :disabled="currentPage === totalPages"
              class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useApiGet } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';

// Icons
import {
  DocumentTextIcon,
  ClockIcon,
  CalendarIcon,
  CheckCircleIcon,
  PlusIcon,
  DocumentArrowDownIcon,
  MagnifyingGlassIcon,
  XMarkIcon,
  EyeIcon,
  CheckIcon,
  EllipsisVerticalIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();

// State
const isLoading = ref(true);
const viewMode = ref('list');
const openDropdown = ref<number | null>(null);
const selectedApplications = ref<number[]>([]);
const bulkAction = ref('');

// Pagination
const currentPage = ref(1);
const pageSize = ref(20);

// Filters
const filters = reactive({
  search: '',
  job_id: '',
  status: '',
  date_from: ''
});

// Data
const applications = ref([]);
const jobs = ref([]);
const stats = ref({
  total: 0,
  pending: 0,
  interviews: 0,
  hired: 0
});

// Computed
const breadcrumbs = computed(() => [
  { label: 'Employer', to: '/employer' },
  { label: 'Applications' }
]);

const totalApplications = computed(() => stats.value.total);
const totalJobs = computed(() => jobs.value.length);

const filteredApplications = computed(() => {
  let filtered = applications.value;

  if (filters.search) {
    const search = filters.search.toLowerCase();
    filtered = filtered.filter(app => 
      app.candidate?.name?.toLowerCase().includes(search) ||
      app.job?.title?.toLowerCase().includes(search)
    );
  }

  if (filters.job_id) {
    filtered = filtered.filter(app => app.job_id === Number(filters.job_id));
  }

  if (filters.status) {
    filtered = filtered.filter(app => app.status === filters.status);
  }

  if (filters.date_from) {
    filtered = filtered.filter(app => app.applied_at >= filters.date_from);
  }

  return filtered;
});

const paginatedApplications = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  const end = start + pageSize.value;
  return filteredApplications.value.slice(start, end);
});

const totalPages = computed(() => 
  Math.ceil(filteredApplications.value.length / pageSize.value)
);

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

const activeFilters = computed(() => {
  const active = [];
  if (filters.search) active.push({ key: 'search', label: `Search: ${filters.search}` });
  if (filters.job_id) {
    const job = jobs.value.find(j => j.id === Number(filters.job_id));
    active.push({ key: 'job_id', label: `Job: ${job?.title}` });
  }
  if (filters.status) active.push({ key: 'status', label: `Status: ${filters.status}` });
  if (filters.date_from) active.push({ key: 'date_from', label: `From: ${filters.date_from}` });
  return active;
});

const activeFiltersCount = computed(() => activeFilters.value.length);

// Methods
const getStatusColor = (status: string): string => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    reviewing: 'bg-blue-100 text-blue-800',
    shortlisted: 'bg-green-100 text-green-800',
    interview: 'bg-purple-100 text-purple-800',
    rejected: 'bg-red-100 text-red-800',
    hired: 'bg-green-100 text-green-800'
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const getStatusLabel = (status: string): string => {
  const labels = {
    pending: 'Pending',
    reviewing: 'Under Review',
    shortlisted: 'Shortlisted',
    interview: 'Interview',
    rejected: 'Rejected',
    hired: 'Hired'
  };
  return labels[status] || status;
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

const clearFilter = (key: string) => {
  filters[key] = '';
};

const clearAllFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = '';
  });
};

const toggleDropdown = (id: number) => {
  openDropdown.value = openDropdown.value === id ? null : id;
};

const viewApplication = (application: any) => {
  router.push(`/employer/applications/${application.id}`);
};

const scheduleInterview = (application: any) => {
  router.push(`/employer/interviews/schedule?application=${application.id}`);
};

const shortlistCandidate = async (application: any) => {
  // Implementation for shortlisting
  console.log('Shortlist candidate:', application.id);
};

const rejectApplication = async (application: any) => {
  // Implementation for rejection
  console.log('Reject application:', application.id);
};

const downloadResume = (application: any) => {
  // Implementation for resume download
  console.log('Download resume:', application.id);
};

const sendMessage = (application: any) => {
  // Implementation for messaging
  console.log('Send message:', application.id);
};

const addNotes = (application: any) => {
  // Implementation for notes
  console.log('Add notes:', application.id);
};

const exportApplications = () => {
  // Implementation for export
  console.log('Export applications');
};

const performBulkAction = () => {
  if (!bulkAction.value) return;
  
  console.log('Bulk action:', bulkAction.value, selectedApplications.value);
  bulkAction.value = '';
  selectedApplications.value = [];
};

// Load data
onMounted(async () => {
  try {
    const [applicationsRes, jobsRes, statsRes] = await Promise.all([
      useApiGet('/api/employer/applications'),
      useApiGet('/api/employer/jobs'),
      useApiGet('/api/employer/applications/stats')
    ]);
    
    applications.value = applicationsRes.data || [];
    jobs.value = jobsRes.data || [];
    stats.value = statsRes.data || stats.value;
  } catch (error) {
    console.error('Failed to load applications:', error);
  } finally {
    isLoading.value = false;
  }
});

// Reset pagination when filters change
watch(filters, () => {
  currentPage.value = 1;
}, { deep: true });

// Close dropdown when clicking outside
document.addEventListener('click', () => {
  openDropdown.value = null;
});
</script> 