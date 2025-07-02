<template>
  <MainLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="bg-white px-6 py-6">
        <div class="max-w-7xl mx-auto">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                My Applications
              </h1>
              <p class="text-gray-600">
                Track and manage your job applications
              </p>
            </div>
            
            <!-- Header Actions -->
            <div class="flex flex-col sm:flex-row gap-3 mt-4 sm:mt-0">
              <BaseButton
                variant="outline-primary"
                size="md"
                @click="exportApplications"
                :loading="isExporting"
              >
                <ArrowDownTrayIcon class="h-5 w-5 mr-2" />
                Export
              </BaseButton>
              
              <BaseButton
                variant="primary"
                size="md"
                :to="{ name: 'jobs.index' }"
                tag="router-link"
              >
                <PlusIcon class="h-5 w-5 mr-2" />
                Apply to More Jobs
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Applications Summary -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
          <p class="text-2xl font-bold text-blue-600">{{ applicationStats.total }}</p>
          <p class="text-sm text-gray-600">Total Applications</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
          <p class="text-2xl font-bold text-yellow-600">{{ applicationStats.pending }}</p>
          <p class="text-sm text-gray-600">Pending Review</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
          <p class="text-2xl font-bold text-purple-600">{{ applicationStats.interview }}</p>
          <p class="text-sm text-gray-600">Interview Stage</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
          <p class="text-2xl font-bold text-green-600">{{ applicationStats.accepted }}</p>
          <p class="text-sm text-gray-600">Accepted</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center">
          <p class="text-2xl font-bold text-red-600">{{ applicationStats.rejected }}</p>
          <p class="text-sm text-gray-600">Rejected</p>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="md:col-span-2">
            <BaseInput
              v-model="filters.search"
              type="text"
              placeholder="Search by job title or company..."
              :left-icon="MagnifyingGlassIcon"
              size="md"
              @input="debouncedSearch"
            />
          </div>
          
          <!-- Status Filter -->
          <div>
            <select
              v-model="filters.status"
              @change="applyFilters"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
            >
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="reviewing">Under Review</option>
              <option value="interview">Interview</option>
              <option value="accepted">Accepted</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          
          <!-- Date Filter -->
          <div>
            <select
              v-model="filters.dateRange"
              @change="applyFilters"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
            >
              <option value="">All Time</option>
              <option value="7">Last 7 days</option>
              <option value="30">Last 30 days</option>
              <option value="90">Last 3 months</option>
              <option value="365">Last year</option>
            </select>
          </div>
        </div>
        
        <!-- Active Filters -->
        <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-200">
          <span class="text-sm text-gray-600 mr-2">Active filters:</span>
          
          <span
            v-if="filters.status"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
          >
            Status: {{ formatStatus(filters.status) }}
            <button @click="clearFilter('status')" class="ml-1 text-blue-600 hover:text-blue-800">
              <XMarkIcon class="h-3 w-3" />
            </button>
          </span>
          
          <span
            v-if="filters.dateRange"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
          >
            Date: Last {{ filters.dateRange }} days
            <button @click="clearFilter('dateRange')" class="ml-1 text-green-600 hover:text-green-800">
              <XMarkIcon class="h-3 w-3" />
            </button>
          </span>
          
          <button
            @click="clearAllFilters"
            class="text-sm text-gray-500 hover:text-gray-700 underline"
          >
            Clear all
          </button>
        </div>
      </div>

      <!-- Bulk Actions -->
      <div v-if="selectedApplications.length > 0" class="mb-6">
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <CheckCircleIcon class="h-5 w-5 text-indigo-600 mr-2" />
              <span class="text-sm font-medium text-indigo-800">
                {{ selectedApplications.length }} application(s) selected
              </span>
            </div>
            
            <div class="flex gap-2">
              <BaseButton
                variant="ghost"
                size="sm"
                @click="markAsRead"
                :disabled="isBulkActionLoading"
              >
                Mark as Read
              </BaseButton>
              
              <BaseButton
                variant="ghost"
                size="sm"
                @click="downloadApplications"
                :disabled="isBulkActionLoading"
              >
                Download
              </BaseButton>
              
              <BaseButton
                variant="ghost"
                size="sm"
                @click="clearSelection"
              >
                Clear Selection
              </BaseButton>
            </div>
          </div>
        </div>
      </div>

      <!-- Applications List -->
      <div class="space-y-4">
        <!-- Loading State -->
        <div v-if="isLoading" class="space-y-4">
          <div v-for="i in 5" :key="i" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 animate-pulse">
            <div class="flex space-x-4">
              <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
              <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                <div class="h-3 bg-gray-200 rounded w-1/4"></div>
              </div>
              <div class="w-20 h-6 bg-gray-200 rounded"></div>
            </div>
          </div>
        </div>

        <!-- Applications -->
        <div v-else-if="paginatedApplications.length > 0" class="space-y-4">
          <div
            v-for="application in paginatedApplications"
            :key="application.id"
            class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200"
          >
            <!-- Application Header -->
            <div class="p-6">
              <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                  <!-- Selection Checkbox -->
                  <div class="flex items-center h-5 mt-1">
                    <input
                      :id="`application-${application.id}`"
                      v-model="selectedApplications"
                      :value="application.id"
                      type="checkbox"
                      class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    />
                  </div>
                  
                  <!-- Company Logo -->
                  <img
                    v-if="application.company?.logo"
                    :src="application.company.logo"
                    :alt="application.company.name"
                    class="w-12 h-12 rounded-lg object-cover"
                  />
                  <div
                    v-else
                    class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center"
                  >
                    <span class="text-lg font-semibold text-indigo-600">
                      {{ (application.company?.name || 'C').charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  
                  <!-- Application Info -->
                  <div class="flex-1">
                    <div class="flex items-start justify-between">
                      <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                          {{ application.job_title }}
                        </h3>
                        <p class="text-indigo-600 font-medium mb-2">{{ application.company?.name }}</p>
                        <div class="flex items-center text-sm text-gray-500 space-x-4 mb-3">
                          <div class="flex items-center">
                            <MapPinIcon class="h-4 w-4 mr-1" />
                            {{ application.location }}
                          </div>
                          <div class="flex items-center">
                            <CalendarIcon class="h-4 w-4 mr-1" />
                            Applied {{ formatDate(application.applied_at) }}
                          </div>
                          <div v-if="application.salary_range" class="flex items-center">
                            <CurrencyDollarIcon class="h-4 w-4 mr-1" />
                            {{ application.salary_range }}
                          </div>
                        </div>
                      </div>
                      
                      <!-- Status and Actions -->
                      <div class="flex flex-col items-end space-y-2">
                        <span 
                          :class="[
                            'px-3 py-1 text-sm font-medium rounded-full',
                            getStatusBadgeClasses(application.status)
                          ]"
                        >
                          {{ formatStatus(application.status) }}
                        </span>
                        
                        <!-- Action Menu -->
                        <div class="relative">
                          <button
                            @click="toggleApplicationMenu(application.id)"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
                          >
                            <EllipsisVerticalIcon class="h-5 w-5" />
                          </button>
                          
                          <!-- Dropdown Menu -->
                          <div
                            v-if="activeApplicationMenu === application.id"
                            class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10"
                          >
                            <div class="py-1">
                              <button
                                @click="viewApplication(application)"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                              >
                                <EyeIcon class="h-4 w-4 mr-2" />
                                View Details
                              </button>
                              
                              <button
                                @click="viewJob(application.job_id)"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                              >
                                <LinkIcon class="h-4 w-4 mr-2" />
                                View Job
                              </button>
                              
                              <button
                                v-if="application.status === 'interview'"
                                @click="scheduleInterview(application)"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                              >
                                <CalendarIcon class="h-4 w-4 mr-2" />
                                Schedule Interview
                              </button>
                              
                              <button
                                @click="downloadApplication(application)"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                              >
                                <ArrowDownTrayIcon class="h-4 w-4 mr-2" />
                                Download
                              </button>
                              
                              <div class="border-t border-gray-100 my-1"></div>
                              
                              <button
                                @click="withdrawApplication(application)"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                              >
                                <XMarkIcon class="h-4 w-4 mr-2" />
                                Withdraw Application
                              </button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Application Timeline (Progressive Disclosure) -->
              <div
                v-if="expandedApplications.includes(application.id)"
                class="mt-6 pt-6 border-t border-gray-200"
              >
                <h4 class="text-sm font-medium text-gray-900 mb-4">Application Timeline</h4>
                <div class="flow-root">
                  <ul class="-mb-8">
                    <li
                      v-for="(event, eventIdx) in application.timeline"
                      :key="event.id"
                      class="relative pb-8"
                    >
                      <div v-if="eventIdx !== application.timeline.length - 1" class="absolute top-4 left-4 w-px h-full bg-gray-200"></div>
                      <div class="relative flex space-x-3">
                        <div>
                          <span 
                            :class="[
                              'h-8 w-8 rounded-full flex items-center justify-center',
                              getTimelineIconClasses(event.type)
                            ]"
                          >
                            <component :is="getTimelineIcon(event.type)" class="h-4 w-4" />
                          </span>
                        </div>
                        <div class="flex-1 min-w-0">
                          <div>
                            <p class="text-sm font-medium text-gray-900">{{ event.title }}</p>
                            <p class="text-sm text-gray-500">{{ event.description }}</p>
                          </div>
                          <div class="mt-1 text-xs text-gray-400">
                            {{ formatTimelineDate(event.created_at) }}
                          </div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              
              <!-- Expand/Collapse Button -->
              <button
                @click="toggleApplicationExpansion(application.id)"
                class="mt-4 flex items-center text-sm text-indigo-600 hover:text-indigo-700 font-medium"
              >
                <span v-if="expandedApplications.includes(application.id)">
                  Hide Timeline
                  <ChevronUpIcon class="h-4 w-4 ml-1" />
                </span>
                <span v-else>
                  View Timeline
                  <ChevronDownIcon class="h-4 w-4 ml-1" />
                </span>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-16">
          <BriefcaseIcon class="h-16 w-16 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">No applications found</h3>
          <p class="text-gray-500 mb-6">
            <span v-if="hasActiveFilters">Try adjusting your filters or</span>
            <span v-else>Start applying to jobs to see them here</span>
          </p>
          
          <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <BaseButton
              v-if="hasActiveFilters"
              variant="outline-primary"
              @click="clearAllFilters"
            >
              Clear Filters
            </BaseButton>
            
            <BaseButton
              variant="primary"
              :to="{ name: 'jobs.index' }"
              tag="router-link"
            >
              Browse Jobs
            </BaseButton>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="mt-8 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing {{ startItem }} to {{ endItem }} of {{ totalApplications }} applications
        </div>
        
        <nav class="flex items-center space-x-2">
          <BaseButton
            variant="ghost"
            size="sm"
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage <= 1"
          >
            <ChevronLeftIcon class="h-4 w-4" />
            Previous
          </BaseButton>
          
          <span
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-1 text-sm rounded cursor-pointer',
              page === currentPage
                ? 'bg-indigo-600 text-white'
                : 'text-gray-700 hover:bg-gray-100'
            ]"
          >
            {{ page }}
          </span>
          
          <BaseButton
            variant="ghost"
            size="sm"
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage >= totalPages"
          >
            Next
            <ChevronRightIcon class="h-4 w-4" />
          </BaseButton>
        </nav>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useApiGet, useApiPost } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';

// Icons
import {
  MagnifyingGlassIcon,
  PlusIcon,
  ArrowDownTrayIcon,
  XMarkIcon,
  CheckCircleIcon,
  MapPinIcon,
  CalendarIcon,
  CurrencyDollarIcon,
  EllipsisVerticalIcon,
  EyeIcon,
  LinkIcon,
  BriefcaseIcon,
  ChevronUpIcon,
  ChevronDownIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  DocumentCheckIcon,
  ClockIcon,
  UserGroupIcon,
  HandThumbUpIcon,
  XCircleIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();

// Breadcrumbs
const breadcrumbs = [
  { label: 'Candidate', to: '/candidate' },
  { label: 'Applications' }
];

// State
const isLoading = ref(false);
const isExporting = ref(false);
const isBulkActionLoading = ref(false);
const selectedApplications = ref<number[]>([]);
const expandedApplications = ref<number[]>([]);
const activeApplicationMenu = ref<number | null>(null);
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Filters
const filters = ref({
  search: '',
  status: '',
  dateRange: ''
});

// Mock data - would come from API
const applicationStats = ref({
  total: 24,
  pending: 8,
  interview: 3,
  accepted: 2,
  rejected: 11
});

// API calls
const { data: applications, loading, execute: loadApplications } = useApiGet('/api/candidate/applications', {}, {
  immediate: true,
  defaultValue: []
});

// Computed properties
const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.status || filters.value.dateRange;
});

const filteredApplications = computed(() => {
  let result = applications.value || [];
  
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    result = result.filter((app: any) => 
      app.job_title.toLowerCase().includes(search) ||
      app.company?.name.toLowerCase().includes(search)
    );
  }
  
  if (filters.value.status) {
    result = result.filter((app: any) => app.status === filters.value.status);
  }
  
  if (filters.value.dateRange) {
    const days = parseInt(filters.value.dateRange);
    const cutoff = new Date();
    cutoff.setDate(cutoff.getDate() - days);
    result = result.filter((app: any) => new Date(app.applied_at) >= cutoff);
  }
  
  return result;
});

const paginatedApplications = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredApplications.value.slice(start, end);
});

const totalApplications = computed(() => filteredApplications.value.length);
const totalPages = computed(() => Math.ceil(totalApplications.value / itemsPerPage.value));
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage.value + 1);
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage.value, totalApplications.value));

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);
  
  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

// Methods
const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now.getTime() - date.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 1) return '1 day ago';
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
  return `${Math.floor(diffDays / 30)} months ago`;
};

const formatTimelineDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatStatus = (status: string): string => {
  const statusMap: Record<string, string> = {
    'pending': 'Pending',
    'reviewing': 'Under Review',
    'interview': 'Interview',
    'accepted': 'Accepted',
    'rejected': 'Rejected'
  };
  return statusMap[status] || status;
};

const getStatusBadgeClasses = (status: string): string => {
  const classMap: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'reviewing': 'bg-blue-100 text-blue-800',
    'interview': 'bg-purple-100 text-purple-800',
    'accepted': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800'
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
};

const getTimelineIcon = (type: string) => {
  const iconMap: Record<string, any> = {
    'applied': DocumentCheckIcon,
    'reviewing': ClockIcon,
    'interview': UserGroupIcon,
    'accepted': HandThumbUpIcon,
    'rejected': XCircleIcon
  };
  return iconMap[type] || DocumentCheckIcon;
};

const getTimelineIconClasses = (type: string): string => {
  const classMap: Record<string, string> = {
    'applied': 'bg-blue-100 text-blue-600',
    'reviewing': 'bg-yellow-100 text-yellow-600',
    'interview': 'bg-purple-100 text-purple-600',
    'accepted': 'bg-green-100 text-green-600',
    'rejected': 'bg-red-100 text-red-600'
  };
  return classMap[type] || 'bg-gray-100 text-gray-600';
};

// Filter methods
const debouncedSearch = (() => {
  let timeout: NodeJS.Timeout;
  return () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      applyFilters();
    }, 300);
  };
})();

const applyFilters = () => {
  currentPage.value = 1;
  // In real app, this would trigger API call with filters
};

const clearFilter = (filterKey: string) => {
  (filters.value as any)[filterKey] = '';
  applyFilters();
};

const clearAllFilters = () => {
  filters.value = {
    search: '',
    status: '',
    dateRange: ''
  };
  applyFilters();
};

// Application actions
const toggleApplicationExpansion = (applicationId: number) => {
  const index = expandedApplications.value.indexOf(applicationId);
  if (index > -1) {
    expandedApplications.value.splice(index, 1);
  } else {
    expandedApplications.value.push(applicationId);
  }
};

const toggleApplicationMenu = (applicationId: number) => {
  activeApplicationMenu.value = activeApplicationMenu.value === applicationId ? null : applicationId;
};

const viewApplication = (application: any) => {
  router.push(`/candidate/applications/${application.id}`);
  activeApplicationMenu.value = null;
};

const viewJob = (jobId: number) => {
  router.push(`/jobs/${jobId}`);
  activeApplicationMenu.value = null;
};

const scheduleInterview = (application: any) => {
  // Handle interview scheduling
  console.log('Schedule interview for:', application);
  activeApplicationMenu.value = null;
};

const downloadApplication = (application: any) => {
  // Handle application download
  console.log('Download application:', application);
  activeApplicationMenu.value = null;
};

const withdrawApplication = async (application: any) => {
  if (confirm('Are you sure you want to withdraw this application?')) {
    try {
      // API call to withdraw application
      console.log('Withdraw application:', application);
      activeApplicationMenu.value = null;
    } catch (error) {
      console.error('Failed to withdraw application:', error);
    }
  }
};

// Bulk actions
const clearSelection = () => {
  selectedApplications.value = [];
};

const markAsRead = async () => {
  isBulkActionLoading.value = true;
  try {
    // API call to mark as read
    console.log('Mark as read:', selectedApplications.value);
    clearSelection();
  } catch (error) {
    console.error('Failed to mark as read:', error);
  } finally {
    isBulkActionLoading.value = false;
  }
};

const downloadApplications = async () => {
  isBulkActionLoading.value = true;
  try {
    // API call to download applications
    console.log('Download applications:', selectedApplications.value);
  } catch (error) {
    console.error('Failed to download applications:', error);
  } finally {
    isBulkActionLoading.value = false;
  }
};

const exportApplications = async () => {
  isExporting.value = true;
  try {
    // API call to export all applications
    console.log('Export all applications');
  } catch (error) {
    console.error('Failed to export applications:', error);
  } finally {
    isExporting.value = false;
  }
};

// Pagination
const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

// Watch for filters to reset pagination
watch(filters, () => {
  currentPage.value = 1;
}, { deep: true });

// Close menu when clicking outside
onMounted(() => {
  document.addEventListener('click', (event) => {
    const target = event.target as Element;
    if (!target.closest('.relative')) {
      activeApplicationMenu.value = null;
    }
  });
});
</script>

<style scoped>
/* Custom animations */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-down {
  animation: slideDown 0.2s ease-out;
}

/* Timeline styles */
.timeline-connector {
  background: linear-gradient(to bottom, #e5e7eb 0%, #e5e7eb 100%);
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Custom checkbox styles */
input[type="checkbox"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7-7a.5.5 0 0 0-.708 0l-3.5 3.5a.5.5 0 1 0 .708.708L6.5 3.707l6.646 6.647a.5.5 0 0 0 .708-.708z'/%3e%3c/svg%3e");
}

/* Hover effects */
.hover-shadow:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Loading skeleton */
.skeleton-loader {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style> 