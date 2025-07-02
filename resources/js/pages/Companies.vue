<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold text-gray-900 mb-4">Discover Great Companies</h1>
          <p class="text-lg text-gray-600 mb-8">
            Explore {{ stats.totalCompanies.toLocaleString() }}+ companies and find your perfect workplace
          </p>

          <!-- Search Section -->
          <div class="max-w-2xl mx-auto">
            <form @submit.prevent="performSearch" class="bg-white rounded-lg shadow-lg border border-gray-200 p-6">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Company Name/Keywords -->
                <div class="md:col-span-2">
                  <BaseInput
                    v-model="searchForm.keywords"
                    type="text"
                    placeholder="Company name or keywords"
                    :left-icon="MagnifyingGlassIcon"
                    size="lg"
                    class="w-full"
                  />
                </div>

                <!-- Industry -->
                <div>
                  <select
                    v-model="searchForm.industry"
                    class="block w-full px-4 py-3 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
                  >
                    <option value="">All Industries</option>
                    <option
                      v-for="industry in industries"
                      :key="industry.slug"
                      :value="industry.slug"
                    >
                      {{ industry.name }}
                    </option>
                  </select>
                </div>
              </div>

              <!-- Search Button -->
              <div class="mt-6">
                <BaseButton
                  type="submit"
                  variant="primary"
                  size="lg"
                  :loading="isSearching"
                  class="w-full md:w-auto px-12"
                >
                  <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                  Search Companies
                </BaseButton>
              </div>
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
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Companies</h3>
            
            <!-- Company Size Filter -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Company Size</h4>
              <div class="space-y-2">
                <label
                  v-for="size in companySizes"
                  :key="size.value"
                  class="flex items-center"
                >
                  <input
                    v-model="selectedSizes"
                    :value="size.value"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">{{ size.label }}</span>
                  <span class="ml-auto text-xs text-gray-500">({{ size.count }})</span>
                </label>
              </div>
            </div>

            <!-- Industry Filter -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Industries</h4>
              <div class="space-y-2 max-h-48 overflow-y-auto">
                <label
                  v-for="industry in industries"
                  :key="industry.slug"
                  class="flex items-center"
                >
                  <input
                    v-model="selectedIndustries"
                    :value="industry.slug"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">{{ industry.name }}</span>
                  <span class="ml-auto text-xs text-gray-500">({{ industry.count }})</span>
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

            <!-- Founded Filter -->
            <div class="mb-6">
              <h4 class="text-sm font-medium text-gray-700 mb-3">Founded</h4>
              <div class="space-y-2">
                <label
                  v-for="period in foundedPeriods"
                  :key="period.value"
                  class="flex items-center"
                >
                  <input
                    v-model="selectedFoundedPeriods"
                    :value="period.value"
                    type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-700">{{ period.label }}</span>
                  <span class="ml-auto text-xs text-gray-500">({{ period.count }})</span>
                </label>
              </div>
            </div>

            <!-- Clear Filters -->
            <div v-if="hasActiveFilters">
              <BaseButton
                variant="outline-gray"
                size="sm"
                @click="clearAllFilters"
                class="w-full"
              >
                Clear All Filters
              </BaseButton>
            </div>
          </div>
        </div>

        <!-- Companies List -->
        <div class="mt-8 lg:mt-0 lg:col-span-3">
          <!-- Results Header -->
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">
                {{ totalCompanies.toLocaleString() }} Compan{{ totalCompanies !== 1 ? 'ies' : 'y' }} Found
              </h2>
              <p class="text-sm text-gray-600 mt-1">
                Showing {{ ((currentPage - 1) * itemsPerPage) + 1 }} to {{ Math.min(currentPage * itemsPerPage, totalCompanies) }} of {{ totalCompanies }} results
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
                <option value="name">Company Name</option>
                <option value="size">Company Size</option>
                <option value="jobs_count">Open Positions</option>
                <option value="founded">Founded Date</option>
                <option value="popular">Most Popular</option>
              </select>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="i in 9" :key="i" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="animate-pulse">
                <div class="w-16 h-16 bg-gray-200 rounded-lg mb-4"></div>
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2 mb-4"></div>
                <div class="h-3 bg-gray-200 rounded w-full mb-2"></div>
                <div class="h-3 bg-gray-200 rounded w-2/3"></div>
              </div>
            </div>
          </div>

          <!-- Companies Grid -->
          <div v-else-if="companies.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <CompanyCard
              v-for="company in companies"
              :key="company.id"
              :company="company"
              class="hover:shadow-lg transition-shadow duration-200"
              @click="viewCompany(company)"
              @follow="toggleFollow(company)"
            />
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 text-gray-300">
              <BuildingOfficeIcon class="h-20 w-20" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No companies found</h3>
            <p class="text-gray-600 mb-4">
              We couldn't find any companies matching your criteria. Try adjusting your search filters.
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
import { useHead } from '@/composables/useHead';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';
import CompanyCard from '@/components/companies/CompanyCard.vue';

// Icons
import {
  MagnifyingGlassIcon,
  BuildingOfficeIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();
const route = useRoute();

// State
const isLoading = ref(false);
const isSearching = ref(false);
const currentPage = ref(1);
const itemsPerPage = ref(18);
const sortBy = ref('name');

// Search form
const searchForm = ref({
  keywords: '',
  industry: ''
});

// Filter selections
const selectedSizes = ref<string[]>([]);
const selectedIndustries = ref<string[]>([]);
const selectedLocations = ref<string[]>([]);
const selectedFoundedPeriods = ref<string[]>([]);

// Mock data - replace with API calls
const stats = ref({
  totalCompanies: 2847
});

const companies = ref([
  {
    id: 1,
    name: 'TechCorp Inc.',
    slug: 'techcorp-inc',
    logo: null,
    industry: 'Technology',
    size: '51-200 employees',
    location: 'New York, NY',
    founded: 2015,
    description: 'Leading technology company focused on innovative web applications and digital solutions.',
    website: 'https://techcorp.com',
    jobs_count: 45,
    followers_count: 892,
    is_following: false,
    is_featured: true,
    rating: 4.5,
    benefits: ['Health Insurance', 'Remote Work', 'Flexible Hours'],
    tech_stack: ['Vue.js', 'Node.js', 'AWS', 'PostgreSQL']
  },
  {
    id: 2,
    name: 'Innovation Labs',
    slug: 'innovation-labs',
    logo: null,
    industry: 'Technology',
    size: '11-50 employees',
    location: 'San Francisco, CA',
    founded: 2018,
    description: 'Startup focused on AI and machine learning solutions for businesses.',
    website: 'https://innovationlabs.com',
    jobs_count: 32,
    followers_count: 567,
    is_following: true,
    is_featured: false,
    rating: 4.3,
    benefits: ['Stock Options', 'Learning Budget', 'Catered Meals'],
    tech_stack: ['Python', 'TensorFlow', 'React', 'MongoDB']
  }
  // Add more mock companies
]);

const industries = ref([
  { slug: 'technology', name: 'Technology', count: 486 },
  { slug: 'healthcare', name: 'Healthcare', count: 325 },
  { slug: 'finance', name: 'Finance', count: 298 },
  { slug: 'education', name: 'Education', count: 187 },
  { slug: 'marketing', name: 'Marketing', count: 156 },
  { slug: 'retail', name: 'Retail', count: 134 }
]);

const companySizes = ref([
  { value: '1-10', label: '1-10 employees', count: 156 },
  { value: '11-50', label: '11-50 employees', count: 298 },
  { value: '51-200', label: '51-200 employees', count: 421 },
  { value: '201-500', label: '201-500 employees', count: 287 },
  { value: '501-1000', label: '501-1000 employees', count: 145 },
  { value: '1000+', label: '1000+ employees', count: 98 }
]);

const topLocations = ref([
  { name: 'New York, NY', count: 234 },
  { name: 'San Francisco, CA', count: 198 },
  { name: 'Los Angeles, CA', count: 156 },
  { name: 'Chicago, IL', count: 134 },
  { name: 'Austin, TX', count: 112 },
  { name: 'Remote', count: 289 }
]);

const foundedPeriods = ref([
  { value: '2020+', label: '2020 or later', count: 89 },
  { value: '2015-2019', label: '2015-2019', count: 287 },
  { value: '2010-2014', label: '2010-2014', count: 356 },
  { value: '2000-2009', label: '2000-2009', count: 234 },
  { value: '1990-1999', label: '1990-1999', count: 134 },
  { value: '1989-', label: 'Before 1990', count: 78 }
]);

// Computed properties
const totalCompanies = computed(() => companies.value.length);
const totalPages = computed(() => Math.ceil(totalCompanies.value / itemsPerPage.value));

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

const hasActiveFilters = computed(() => {
  return selectedSizes.value.length > 0 ||
         selectedIndustries.value.length > 0 ||
         selectedLocations.value.length > 0 ||
         selectedFoundedPeriods.value.length > 0 ||
         searchForm.value.keywords ||
         searchForm.value.industry;
});

// Methods
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
    await loadCompanies();
  } catch (error) {
    console.error('Search error:', error);
  } finally {
    isSearching.value = false;
  }
};

const loadCompanies = async () => {
  isLoading.value = true;
  
  try {
    // API call would go here
    // const response = await apiService.get('/api/companies', { params: searchParams });
    // companies.value = response.data.companies;
    
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 1000));
  } catch (error) {
    console.error('Failed to load companies:', error);
  } finally {
    isLoading.value = false;
  }
};

const handleSort = () => {
  loadCompanies();
};

const goToPage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
    loadCompanies();
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const viewCompany = (company: any) => {
  router.push({
    name: 'companies.show',
    params: { slug: company.slug || company.id }
  });
};

const toggleFollow = (company: any) => {
  // Handle follow/unfollow functionality
  console.log('Toggle follow for company:', company.id);
  company.is_following = !company.is_following;
  
  if (company.is_following) {
    company.followers_count++;
  } else {
    company.followers_count--;
  }
};

const clearAllFilters = () => {
  searchForm.value.keywords = '';
  searchForm.value.industry = '';
  selectedSizes.value = [];
  selectedIndustries.value = [];
  selectedLocations.value = [];
  selectedFoundedPeriods.value = [];
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
watch([selectedSizes, selectedIndustries, selectedLocations, selectedFoundedPeriods], () => {
  loadCompanies();
}, { deep: true });

// Lifecycle
onMounted(() => {
  initializeFromQuery();
  loadCompanies();
});

// SEO
useHead({
  title: 'Companies - Discover Great Workplaces',
  meta: [
    {
      name: 'description',
      content: 'Explore thousands of companies and find your perfect workplace. Browse company profiles, ratings, and open positions.'
    },
    {
      name: 'keywords',
      content: 'companies, employers, workplaces, company profiles, company culture, company reviews'
    }
  ]
});
</script>

<style scoped>
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