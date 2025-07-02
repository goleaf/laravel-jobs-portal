<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <HeroSection
      title="Discover Great Companies"
      :subtitle="`Explore ${stats.totalCompanies.toLocaleString()}+ companies and find your perfect workplace`"
      size="md"
      theme="primary"
      :show-actions="false"
    >
      <template #content>
        <div class="max-w-2xl mx-auto">
          <form @submit.prevent="performSearch" class="bg-white rounded-lg shadow-lg border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
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
            <div class="text-center">
              <BaseButton
                type="submit"
                variant="primary"
                size="lg"
                :loading="isSearching"
                class="px-12"
              >
                <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                Search Companies
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
          <SearchFilter
            v-model="filters"
            :show-industry-filter="true"
            :show-company-size-filter="true"
            :show-company-filters="true"
            @apply="handleFilterApply"
          />
        </div>

        <!-- Companies List -->
        <div class="lg:col-span-3">
          <!-- Results Header -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
              <div class="mb-4 sm:mb-0">
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
                  <option value="rating">Highest Rated</option>
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
          <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div v-for="n in 6" :key="n" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="animate-pulse">
                <div class="flex items-start space-x-4">
                  <div class="w-16 h-16 bg-gray-200 rounded-lg"></div>
                  <div class="flex-1">
                    <div class="h-6 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-4"></div>
                    <div class="flex space-x-4">
                      <div class="h-4 bg-gray-200 rounded w-20"></div>
                      <div class="h-4 bg-gray-200 rounded w-16"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- No Results -->
          <div v-else-if="companies.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-20 h-20 mx-auto mb-4 text-gray-300">
              <BuildingOfficeIcon class="h-20 w-20" />
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Companies Found</h3>
            <p class="text-gray-600 mb-6">
              Try adjusting your search criteria or filters to find more companies.
            </p>
            <BaseButton
              variant="outline-primary"
              @click="clearAllFilters"
            >
              Clear All Filters
            </BaseButton>
          </div>

          <!-- Companies Grid -->
          <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <CompanyCard
              v-for="company in companies"
              :key="company.id"
              :company="company"
              class="hover:transform hover:scale-[1.02] transition-transform duration-200"
              @follow="handleCompanyFollow"
              @view-jobs="handleViewJobs"
            />
          </div>

          <!-- Pagination -->
          <div v-if="totalPages > 1" class="mt-8">
            <Pagination
              :current-page="currentPage"
              :total-pages="totalPages"
              :total="totalCompanies"
              :per-page="itemsPerPage"
              :show-page-size-selector="true"
              item-name="company"
              @page-change="handlePageChange"
              @page-size-change="handlePageSizeChange"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Social Proof Section -->
    <SocialProof />

    <!-- Newsletter Signup Section -->
    <NewsletterSignup />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useHead } from '@/composables/useHead';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';
import CompanyCard from '@/components/companies/CompanyCard.vue';
import HeroSection from '../components/ui/HeroSection.vue'
import SearchFilter from '../components/ui/SearchFilter.vue'
import Badge from '../components/ui/Badge.vue'
import Pagination from '../components/ui/Pagination.vue'
import SocialProof from '../components/ui/SocialProof.vue'
import NewsletterSignup from '../components/ui/NewsletterSignup.vue'
import { BuildingOfficeIcon } from '@heroicons/vue/24/outline'

// Icons
import {
  MagnifyingGlassIcon,
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