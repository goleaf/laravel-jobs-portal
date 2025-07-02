<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold text-gray-900 mb-4">Find Your Perfect Job</h1>
          <p class="text-lg text-gray-600 mb-8">
            Discover {{ stats.totalJobs.toLocaleString() }}+ job opportunities from top companies
          </p>

          <!-- Search Section -->
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

              <!-- Advanced Search Toggle -->
              <div class="mb-4">
                <button
                  type="button"
                  @click="toggleAdvancedSearch"
                  class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center"
                >
                  <span>{{ showAdvancedSearch ? 'Hide' : 'Show' }} Advanced Search</span>
                  <ChevronDownIcon 
                    :class="['h-4 w-4 ml-1 transition-transform duration-200', showAdvancedSearch ? 'rotate-180' : '']" 
                  />
                </button>
              </div>

              <!-- Advanced Search Filters -->
              <Transition
                enter-active-class="transition-all duration-300"
                enter-from-class="opacity-0 max-h-0 overflow-hidden"
                enter-to-class="opacity-100 max-h-96"
                leave-active-class="transition-all duration-300"
                leave-from-class="opacity-100 max-h-96"
                leave-to-class="opacity-0 max-h-0 overflow-hidden"
              >
                <div v-if="showAdvancedSearch" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 pt-4 border-t border-gray-200">
                  <!-- Employment Type -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                    <select
                      v-model="searchForm.employmentType"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="">Any Type</option>
                      <option value="full-time">Full-time</option>
                      <option value="part-time">Part-time</option>
                      <option value="contract">Contract</option>
                      <option value="freelance">Freelance</option>
                      <option value="internship">Internship</option>
                    </select>
                  </div>

                  <!-- Experience Level -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                    <select
                      v-model="searchForm.experienceLevel"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="">Any Level</option>
                      <option value="entry">Entry Level</option>
                      <option value="mid">Mid Level</option>
                      <option value="senior">Senior Level</option>
                      <option value="executive">Executive</option>
                    </select>
                  </div>

                  <!-- Salary Range -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                    <select
                      v-model="searchForm.salaryRange"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="">Any Salary</option>
                      <option value="0-30000">$0 - $30,000</option>
                      <option value="30000-50000">$30,000 - $50,000</option>
                      <option value="50000-75000">$50,000 - $75,000</option>
                      <option value="75000-100000">$75,000 - $100,000</option>
                      <option value="100000+">$100,000+</option>
                    </select>
                  </div>

                  <!-- Remote Work -->
                  <div class="flex items-center">
                    <input
                      id="remote-work"
                      v-model="searchForm.remoteOk"
                      type="checkbox"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="remote-work" class="ml-2 block text-sm text-gray-900">
                      Remote work options
                    </label>
                  </div>

                  <!-- Featured Jobs Only -->
                  <div class="flex items-center">
                    <input
                      id="featured-only"
                      v-model="searchForm.featuredOnly"
                      type="checkbox"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="featured-only" class="ml-2 block text-sm text-gray-900">
                      Featured jobs only
                    </label>
                  </div>

                  <!-- Date Posted -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Posted</label>
                    <select
                      v-model="searchForm.datePosted"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="">Any Time</option>
                      <option value="1">Last 24 hours</option>
                      <option value="7">Last 7 days</option>
                      <option value="30">Last 30 days</option>
                    </select>
                  </div>
                </div>
              </Transition>

              <!-- Search Button -->
              <BaseButton
                type="submit"
                variant="primary"
                size="lg"
                :loading="isSearching"
                class="w-full md:w-auto px-12"
              >
                <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                Search Jobs
              </BaseButton>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="lg:grid lg:grid-cols-4 lg:gap-8">
        <!-- Sidebar Filters -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Jobs</h3>
            
            <!-- Active Filters -->
            <div v-if="activeFilters.length > 0" class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-2">Active Filters</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="filter in activeFilters"
                  :key="filter.key"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                >
                  {{ filter.label }}
                  <button
                    @click="removeFilter(filter.key)"
                    class="ml-1 h-3 w-3 rounded-full flex items-center justify-center hover:bg-indigo-200"
                  >
                    <XMarkIcon class="h-2 w-2" />
                  </button>
                </span>
              </div>
              <button
                @click="clearAllFilters"
                class="text-xs text-indigo-600 hover:text-indigo-800 mt-2"
              >
                Clear all filters
              </button>
            </div>

            <!-- Company Filter -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Companies</h4>
              <div class="space-y-2 max-h-48 overflow-y-auto">
                <label
                  v-for="company in topCompanies"
                  :key="company.id"
                  class="flex items-center"
                >
                  <input
                    v-model="selectedCompanies"
                    :value="company.id"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">{{ company.name }}</span>
                  <span class="ml-auto text-xs text-gray-500">({{ company.jobsCount }})</span>
                </label>
              </div>
            </div>

            <!-- Location Filter -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Locations</h4>
              <div class="space-y-2 max-h-48 overflow-y-auto">
                <label
                  v-for="location in topLocations"
                  :key="location.name"
                  class="flex items-center"
                >
                  <input
                    v-model="selectedLocations"
                    :value="location.name"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">{{ location.name }}</span>
                  <span class="ml-auto text-xs text-gray-500">({{ location.count }})</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Jobs List -->
        <div class="mt-8 lg:mt-0 lg:col-span-3">
          <!-- Results Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
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
                <option value="relevance">Relevance</option>
                <option value="date_desc">Newest First</option>
                <option value="date_asc">Oldest First</option>
                <option value="salary_desc">Highest Salary</option>
                <option value="salary_asc">Lowest Salary</option>
                <option value="company">Company Name</option>
              </select>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="space-y-4">
            <div v-for="i in 10" :key="i" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="animate-pulse">
                <div class="flex items-start space-x-4">
                  <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                  <div class="flex-1">
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Jobs Grid -->
          <div v-else-if="jobs.length > 0" class="space-y-4">
            <JobCard
              v-for="job in jobs"
              :key="job.id"
              :job="job"
              :show-company-logo="true"
              class="hover:shadow-md transition-shadow duration-200"
              @click="viewJob(job)"
              @bookmark="toggleBookmark(job)"
              @apply="applyToJob(job)"
            />
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 text-gray-300">
              <svg fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No jobs found</h3>
            <p class="text-gray-600 mb-4">
              We couldn't find any jobs matching your criteria. Try adjusting your search filters.
            </p>
            <BaseButton
              variant="primary"
              @click="clearAllFilters"
            >
              Clear Filters
            </BaseButton>
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8">
            <nav class="flex items-center justify-between border-t border-gray-200 pt-6">
              <div class="flex-1 flex justify-between">
                <BaseButton
                  variant="outline-primary"
                  :disabled="currentPage === 1"
                  @click="goToPage(currentPage - 1)"
                >
                  <ChevronLeftIcon class="h-4 w-4 mr-1" />
                  Previous
                </BaseButton>
                
                <div class="hidden md:flex space-x-2">
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="goToPage(page)"
                    :class="[
                      'px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200',
                      page === currentPage
                        ? 'bg-indigo-600 text-white'
                        : 'text-gray-700 hover:bg-gray-100'
                    ]"
                  >
                    {{ page }}
                  </button>
                </div>

                <BaseButton
                  variant="outline-primary"
                  :disabled="currentPage === totalPages"
                  @click="goToPage(currentPage + 1)"
                >
                  Next
                  <ChevronRightIcon class="h-4 w-4 ml-1" />
                </BaseButton>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>
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