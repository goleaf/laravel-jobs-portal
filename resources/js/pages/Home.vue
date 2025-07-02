<template>
  <div class="min-h-screen bg-white">
    <!-- Hero Section -->
    <HeroSection
      title="Find Your <span class='text-yellow-400'>Dream Job</span> Today"
      subtitle="Discover amazing opportunities, connect with top employers, and take the next step in your career journey."
      :primary-action="{
        text: 'Search Jobs',
        icon: MagnifyingGlassIcon,
        onClick: () => $router.push('/jobs')
      }"
      :secondary-action="{
        text: 'Browse Companies',
        variant: 'outline',
        onClick: () => $router.push('/companies')
      }"
      :stats="[
        { value: stats.totalJobs, label: 'Active Jobs', suffix: '+' },
        { value: stats.totalCompanies, label: 'Companies', suffix: '+' },
        { value: stats.totalCandidates, label: 'Job Seekers', suffix: '+' }
      ]"
    >
      <!-- Job Search Form -->
      <template #content>
        <div class="max-w-4xl mx-auto mb-12">
          <form @submit.prevent="performSearch" class="bg-white rounded-2xl shadow-2xl p-6 sm:p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
              <!-- Job Title/Keywords -->
              <div>
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
                  <option value="technology">Technology</option>
                  <option value="healthcare">Healthcare</option>
                  <option value="finance">Finance</option>
                  <option value="education">Education</option>
                  <option value="marketing">Marketing</option>
                  <option value="sales">Sales</option>
                  <option value="design">Design</option>
                  <option value="engineering">Engineering</option>
                </select>
              </div>
            </div>

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
      </template>
    </HeroSection>

    <!-- Featured Jobs Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
            Featured Jobs
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Discover hand-picked opportunities from top companies looking for talented professionals.
          </p>
        </div>

        <!-- Jobs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <JobCard
            v-for="job in featuredJobs"
            :key="job.id"
            :job="job"
            :show-company-logo="true"
            :featured="true"
            class="hover:transform hover:scale-105 transition-transform duration-200"
            @bookmark="handleJobBookmark"
            @apply="handleJobApply"
          />
        </div>

        <!-- View All Jobs Button -->
        <div class="text-center">
          <BaseButton
            variant="outline-primary"
            size="lg"
            :to="{ name: 'jobs.index' }"
            tag="router-link"
          >
            View All Jobs
            <ArrowRightIcon class="h-5 w-5 ml-2" />
          </BaseButton>
        </div>
      </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
            How It Works
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Get started with your job search in just three simple steps.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- Step 1 -->
          <div class="text-center group">
            <div class="relative">
              <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-indigo-200 transition-colors duration-200">
                <UserPlusIcon class="h-10 w-10 text-indigo-600" />
              </div>
              <div class="absolute -top-2 -right-2 w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                1
              </div>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Create Your Profile</h3>
            <p class="text-gray-600">
              Build a compelling profile that showcases your skills, experience, and career aspirations to potential employers.
            </p>
          </div>

          <!-- Step 2 -->
          <div class="text-center group">
            <div class="relative">
              <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-green-200 transition-colors duration-200">
                <MagnifyingGlassIcon class="h-10 w-10 text-green-600" />
              </div>
              <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                2
              </div>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Search & Apply</h3>
            <p class="text-gray-600">
              Browse thousands of job opportunities, filter by your preferences, and apply directly through our platform.
            </p>
          </div>

          <!-- Step 3 -->
          <div class="text-center group">
            <div class="relative">
              <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-yellow-200 transition-colors duration-200">
                <BriefcaseIcon class="h-10 w-10 text-yellow-600" />
              </div>
              <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                3
              </div>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-4">Get Hired</h3>
            <p class="text-gray-600">
              Connect with hiring managers, ace your interviews, and land your dream job with confidence.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Companies Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
            Trusted by Leading Companies
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Join thousands of professionals who found their perfect role through our platform.
          </p>
        </div>

        <!-- Companies Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center">
          <div
            v-for="company in featuredCompanies"
            :key="company.id"
            class="group cursor-pointer"
            @click="viewCompany(company)"
          >
            <div class="bg-white rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-200 text-center">
              <img
                :src="company.logo || '/images/company-placeholder.png'"
                :alt="company.name"
                class="h-12 w-auto mx-auto object-contain filter grayscale group-hover:grayscale-0 transition-all duration-200"
              />
              <p class="text-sm text-gray-600 mt-2 group-hover:text-gray-900 transition-colors duration-200">
                {{ company.name }}
              </p>
            </div>
          </div>
        </div>

        <!-- View All Companies Button -->
        <div class="text-center mt-8">
          <BaseButton
            variant="ghost"
            size="lg"
            :to="{ name: 'companies.index' }"
            tag="router-link"
          >
            View All Companies
            <ArrowRightIcon class="h-5 w-5 ml-2" />
          </BaseButton>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-indigo-600">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
          Ready to Find Your Next Opportunity?
        </h2>
        <p class="text-xl text-indigo-100 mb-8">
          Join thousands of professionals who trust us with their career journey.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <BaseButton
            variant="light"
            size="lg"
            :to="{ name: 'register' }"
            tag="router-link"
            class="px-8"
          >
            Get Started - It's Free
          </BaseButton>
          
          <BaseButton
            variant="outline-primary"
            size="lg"
            :to="{ name: 'jobs.index' }"
            tag="router-link"
            class="px-8 border-white text-white hover:bg-white hover:text-indigo-600"
          >
            Browse Jobs
          </BaseButton>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useApiGet } from '@/composables/useApi';
import { useHead } from '@/composables/useHead';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';
import JobCard from '@/components/jobs/JobCard.vue';
import HeroSection from '../components/ui/HeroSection.vue';

// Icons
import {
  MagnifyingGlassIcon,
  MapPinIcon,
  ArrowRightIcon,
  UserPlusIcon,
  BriefcaseIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();

// Search form state
const searchForm = ref({
  keywords: '',
  location: '',
  category: ''
});

const isSearching = ref(false);

// Stats data
const stats = ref({
  totalJobs: 15742,
  totalCompanies: 2847,
  totalCandidates: 128563
});

// Featured jobs API
const { data: featuredJobs, loading: jobsLoading } = useApiGet('/api/jobs/featured', {}, {
  immediate: true,
  transform: (data) => data.data || []
});

// Featured companies API
const { data: featuredCompanies, loading: companiesLoading } = useApiGet('/api/companies/featured', {}, {
  immediate: true,
  transform: (data) => data.data || []
});

// Methods
const performSearch = async () => {
  isSearching.value = true;
  
  try {
    // Build search query
    const searchParams = new URLSearchParams();
    
    if (searchForm.value.keywords) {
      searchParams.set('q', searchForm.value.keywords);
    }
    
    if (searchForm.value.location) {
      searchParams.set('location', searchForm.value.location);
    }
    
    if (searchForm.value.category) {
      searchParams.set('category', searchForm.value.category);
    }

    // Navigate to jobs page with search parameters
    await router.push({
      name: 'jobs.index',
      query: Object.fromEntries(searchParams)
    });
  } catch (error) {
    console.error('Search error:', error);
  } finally {
    isSearching.value = false;
  }
};

const viewCompany = (company: any) => {
  router.push({
    name: 'companies.show',
    params: { slug: company.slug }
  });
};

// Load real-time stats
onMounted(async () => {
  try {
    const response = await fetch('/api/stats/homepage');
    if (response.ok) {
      const data = await response.json();
      stats.value = data;
    }
  } catch (error) {
    console.log('Could not load real-time stats, using defaults');
  }
});

// Meta tags for SEO
useHead({
  title: 'Find Your Dream Job - JobPortal',
  meta: [
    {
      name: 'description',
      content: 'Discover amazing job opportunities and connect with top employers. Search thousands of jobs across various industries and find your perfect career match.'
    },
    {
      name: 'keywords',
      content: 'jobs, careers, employment, job search, hiring, recruitment, companies, job opportunities'
    },
    {
      property: 'og:title',
      content: 'Find Your Dream Job - JobPortal'
    },
    {
      property: 'og:description',
      content: 'Discover amazing job opportunities and connect with top employers. Search thousands of jobs across various industries.'
    },
    {
      property: 'og:type',
      content: 'website'
    }
  ]
});
</script>

<style scoped>
/* Custom animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

/* Gradient text */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Custom hover effects */
.hover-lift:hover {
  transform: translateY(-4px);
  transition: transform 0.2s ease-in-out;
}

/* Search form focus styles */
.search-form input:focus {
  transform: scale(1.02);
  transition: transform 0.2s ease-in-out;
}

/* Stats counter animation */
@keyframes countUp {
  from {
    opacity: 0;
    transform: scale(0.8);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.stat-number {
  animation: countUp 0.8s ease-out;
}
</style>