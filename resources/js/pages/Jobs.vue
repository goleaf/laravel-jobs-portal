<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Search Section -->
    <HeroSection
      title="Find Your Perfect Job"
      :subtitle="`Discover ${stats.totalJobs.toLocaleString()}+ job opportunities from top companies`"
      size="md"
      theme="primary"
      :show-actions="false"
    >
      <template #content>
        <div class="max-w-4xl mx-auto">
          <form @submit.prevent="performSearch" class="bg-white rounded-lg shadow-lg border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
              <!-- Keywords -->
              <div class="md:col-span-2">
                <BaseInput
                  v-model="searchForm.keywords"
                  type="text"
                  placeholder="Job title, keywords, or company"
                  :left-icon="MagnifyingGlassIcon"
                  size="lg"
                  class="w-full"
                />
              </div>

              <!-- Location -->
              <div>
                <BaseInput
                  v-model="searchForm.location"
                  type="text"
                  placeholder="City, state, or remote"
                  :left-icon="MapPinIcon"
                  size="lg"
                  class="w-full"
                />
              </div>

              <!-- Category -->
              <div>
                <select
                  v-model="searchForm.category"
                  class="block w-full px-4 py-3 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                >
                  <option value="">All Categories</option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.slug"
                  >
                    {{ category.name }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Search Button -->
            <div class="text-center">
              <BaseButton
                type="submit"
                variant="primary"
                size="lg"
                :loading="isSearching"
                class="px-12"
              >
                <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                Search Jobs
              </BaseButton>
            </div>
          </form>
        </div>
      </template>
    </HeroSection>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- Enhanced Filter Sidebar -->
        <div class="lg:col-span-1 mb-8 lg:mb-0">
          <JobFilterSidebar
            v-model="filters"
            @apply="handleFilterApply"
          />
        </div>

        <!-- Jobs List -->
        <div class="lg:col-span-3">
          <!-- Results Header -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div class="mb-4 sm:mb-0">
                <h2 class="text-xl font-semibold text-gray-900">
                  {{ totalJobs.toLocaleString() }} Job{{ totalJobs !== 1 ? 's' : '' }} Found
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                  Showing {{ ((currentPage - 1) * itemsPerPage) + 1 }} to {{ Math.min(currentPage * itemsPerPage, totalJobs) }} of {{ totalJobs }} results
                </p>
              </div>

              <!-- Sort Options -->
              <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <select
                  v-model="sortBy"
                  @change="handleSort"
                  class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="created_at">Most Recent</option>
                  <option value="salary_max">Highest Salary</option>
                  <option value="title">Job Title</option>
                  <option value="company_name">Company Name</option>
                  <option value="location">Location</option>
                  <option value="relevance">Most Relevant</option>
                </select>
              </div>
            </div>
            
            <!-- Active Filters Display -->
            <div v-if="activeFilters.length > 0" class="mt-4 pt-4 border-t border-gray-200">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Active Filters:</h4>
              <div class="flex flex-wrap gap-2">
                <Badge
                  v-for="filter in activeFilters"
                  :key="filter.key"
                  :text="filter.label"
                  variant="primary"
                  size="sm"
                  removable
                  @remove="removeFilter(filter.key)"
                />
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="space-y-6">
            <div v-for="n in 6" :key="n" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="animate-pulse">
                <div class="flex items-start space-x-4">
                  <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                  <div class="flex-1">
                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="flex space-x-4">
                      <div class="h-4 bg-gray-200 rounded w-20"></div>
                      <div class="h-4 bg-gray-200 rounded w-16"></div>
                      <div class="h-4 bg-gray-200 rounded w-24"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Results -->
          <div v-else-if="jobs.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-4 text-gray-300">
              <MagnifyingGlassIcon class="h-20 w-20" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Jobs Found</h3>
            <p class="text-gray-600 mb-6">
              Try adjusting your search criteria or filters to find more opportunities.
            </p>
            <BaseButton
              variant="outline-primary"
              @click="clearAllFilters"
            >
              Clear All Filters
            </BaseButton>
          </div>

          <!-- Jobs Grid -->
          <div v-else class="space-y-6">
            <JobCard
              v-for="job in jobs"
              :key="job.id"
              :job="job"
              :show-company-logo="true"
              class="hover:transform hover:scale-[1.02] transition-transform duration-200"
              @bookmark="handleJobBookmark"
              @apply="handleJobApply"
              @view="viewJobDetails"
            />
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8">
            <Pagination
              :current-page="currentPage"
              :total-pages="totalPages"
              :total="totalJobs"
              :per-page="itemsPerPage"
              :show-page-size-selector="true"
              item-name="job"
              @page-change="handlePageChange"
              @page-size-change="handlePageSizeChange"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Newsletter Signup Section -->
    <NewsletterSignup />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useApiGet } from '@/composables/useApi';
import { useHead } from '@/composables/useHead';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';
import JobCard from '@/components/jobs/JobCard.vue';
import HeroSection from '../components/ui/HeroSection.vue'
import JobFilterSidebar from '../components/jobs/JobFilterSidebar.vue'
import Badge from '../components/ui/Badge.vue'
import Pagination from '../components/ui/Pagination.vue'
import NewsletterSignup from '../components/ui/NewsletterSignup.vue'

// Icons
import {
  MagnifyingGlassIcon,
  MapPinIcon,
  ChevronDownIcon,
  XMarkIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();
const route = useRoute();

// State
const isLoading = ref(false);
const isSearching = ref(false);
const showAdvancedSearch = ref(false);
const currentPage = ref(1);
const itemsPerPage = ref(20);
const sortBy = ref('relevance');

// Search form
const searchForm = ref({
  keywords: '',
  location: '',
  category: '',
  employmentType: '',
  experienceLevel: '',
  salaryRange: '',
  remoteOk: false,
  featuredOnly: false,
  datePosted: ''
});

// Filter selections
const selectedCompanies = ref<number[]>([]);
const selectedLocations = ref<string[]>([]);

// Mock data - replace with API calls
const stats = ref({
  totalJobs: 15742
});

const jobs = ref([
  {
    id: 1,
    title: 'Senior Frontend Developer',
    company: { id: 1, name: 'TechCorp Inc.', logo: null },
    location: 'New York, NY',
    department: 'Engineering',
    employment_type: 'Full-time',
    experience_level: 'Senior',
    salary_min: 90000,
    salary_max: 130000,
    remote_ok: true,
    is_urgent: false,
    is_featured: true,
    description: 'We are looking for a Senior Frontend Developer to join our team...',
    skills: ['Vue.js', 'TypeScript', 'TailwindCSS', 'Node.js'],
    created_at: '2024-01-15T10:00:00Z'
  }
  // Add more mock jobs here
]);

const categories = ref([
  { id: 1, name: 'Technology', slug: 'technology' },
  { id: 2, name: 'Healthcare', slug: 'healthcare' },
  { id: 3, name: 'Finance', slug: 'finance' },
  { id: 4, name: 'Education', slug: 'education' },
  { id: 5, name: 'Marketing', slug: 'marketing' }
]);

const topCompanies = ref([
  { id: 1, name: 'TechCorp Inc.', jobsCount: 45 },
  { id: 2, name: 'Innovation Labs', jobsCount: 32 },
  { id: 3, name: 'StartupXYZ', jobsCount: 28 }
]);

const topLocations = ref([
  { name: 'New York, NY', count: 156 },
  { name: 'San Francisco, CA', count: 134 },
  { name: 'Remote', count: 89 },
  { name: 'Austin, TX', count: 67 }
]);

// Computed properties
const totalJobs = computed(() => jobs.value.length);
const totalPages = computed(() => Math.ceil(totalJobs.value / itemsPerPage.value));

const visiblePages = computed(() => {
  const delta = 2;
  const range = [];
  const rangeWithDots = [];

  for (let i = Math.max(2, currentPage.value - delta); 
       i <= Math.min(totalPages.value - 1, currentPage.value + delta); 
       i++) {
    range.push(i);
  }

  if (currentPage.value - delta > 2) {
    rangeWithDots.push(1, '...');
  } else {
    rangeWithDots.push(1);
  }

  rangeWithDots.push(...range);

  if (currentPage.value + delta < totalPages.value - 1) {
    rangeWithDots.push('...', totalPages.value);
  } else {
    rangeWithDots.push(totalPages.value);
  }

  return rangeWithDots;
});

const activeFilters = computed(() => {
  const filters = [];
  
  if (searchForm.value.keywords) {
    filters.push({ key: 'keywords', label: `Keywords: ${searchForm.value.keywords}` });
  }
  
  if (searchForm.value.location) {
    filters.push({ key: 'location', label: `Location: ${searchForm.value.location}` });
  }
  
  if (searchForm.value.category) {
    const category = categories.value.find(c => c.slug === searchForm.value.category);
    filters.push({ key: 'category', label: `Category: ${category?.name}` });
  }
  
  if (searchForm.value.employmentType) {
    filters.push({ key: 'employmentType', label: `Type: ${searchForm.value.employmentType}` });
  }
  
  if (searchForm.value.remoteOk) {
    filters.push({ key: 'remoteOk', label: 'Remote work' });
  }
  
  if (searchForm.value.featuredOnly) {
    filters.push({ key: 'featuredOnly', label: 'Featured only' });
  }
  
  return filters;
});

// Methods
const toggleAdvancedSearch = () => {
  showAdvancedSearch.value = !showAdvancedSearch.value;
};

const performSearch = async () => {
  isSearching.value = true;
  currentPage.value = 1;
  
  try {
    // Update URL with search parameters
    const query = { ...route.query };
    
    Object.keys(searchForm.value).forEach(key => {
      const value = searchForm.value[key as keyof typeof searchForm.value];
      if (value) {
        query[key] = value.toString();
      } else {
        delete query[key];
      }
    });
    
    await router.replace({ query });
    await loadJobs();
  } catch (error) {
    console.error('Search error:', error);
  } finally {
    isSearching.value = false;
  }
};

const loadJobs = async () => {
  isLoading.value = true;
  
  try {
    // API call would go here
    // const response = await apiService.get('/api/jobs', { params: searchParams });
    // jobs.value = response.data.jobs;
    
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 1000));
  } catch (error) {
    console.error('Failed to load jobs:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleSort = () => {
  loadJobs();
};

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    loadJobs();
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const viewJob = (job: any) => {
  router.push({
    name: 'jobs.show',
    params: { slug: job.slug || job.id }
  });
};

const toggleBookmark = (job: any) => {
  // Handle bookmark functionality
  console.log('Toggle bookmark for job:', job.id);
};

const applyToJob = (job: any) => {
  // Handle job application
  console.log('Apply to job:', job.id);
};

const removeFilter = (filterKey: string) => {
  if (filterKey in searchForm.value) {
    (searchForm.value as any)[filterKey] = '';
  }
  performSearch();
};

const clearAllFilters = () => {
  Object.keys(searchForm.value).forEach(key => {
    (searchForm.value as any)[key] = '';
  });
  selectedCompanies.value = [];
  selectedLocations.value = [];
  performSearch();
};

// Initialize from URL parameters
const initializeFromQuery = () => {
  Object.keys(route.query).forEach(key => {
    if (key in searchForm.value) {
      (searchForm.value as any)[key] = route.query[key];
    }
  });
};

// Watchers
watch([selectedCompanies, selectedLocations], () => {
  loadJobs();
}, { deep: true });

// Lifecycle
onMounted(() => {
  initializeFromQuery();
  loadJobs();
});

// SEO
useHead({
  title: 'Jobs - Find Your Perfect Career Opportunity',
  meta: [
    {
      name: 'description',
      content: 'Browse thousands of job opportunities from top companies. Find your perfect career match with our comprehensive job search platform.'
    },
    {
      name: 'keywords',
      content: 'jobs, careers, employment, job search, hiring, recruitment, job opportunities, work'
    }
  ]
});
</script>

<style scoped>
/* Smooth transitions for advanced search */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Custom scrollbar for filter lists */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style> 