<template>
  <div class="min-h-screen bg-gradient-to-br from-neutral-50 to-white">
    <!-- Hero Section -->
    <section class="relative py-20 lg:py-32 overflow-hidden">
      <!-- Background Elements -->
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/10 to-purple-600/10"></div>
        <div class="absolute top-0 left-0 w-72 h-72 bg-primary-500/20 rounded-full filter blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500/20 rounded-full filter blur-3xl transform translate-x-1/2 translate-y-1/2"></div>
      </div>

      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <!-- Main Heading -->
          <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold text-neutral-900 mb-6 text-balance">
            Find Your 
            <span class="bg-gradient-to-r from-primary-600 to-purple-600 bg-clip-text text-transparent">
              Dream Job
            </span>
          </h1>
          
          <!-- Subheading -->
          <p class="text-xl md:text-2xl lg:text-3xl text-neutral-600 mb-12 max-w-4xl mx-auto text-balance">
            Connect with top employers and discover opportunities that match your skills and ambitions in our global job marketplace.
          </p>

          <!-- Search Section -->
          <div class="max-w-5xl mx-auto">
            <div class="glass rounded-2xl p-6 lg:p-8 shadow-strong">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Job Title Search -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-neutral-700">Job Title</label>
                  <div class="relative">
                    <input
                      v-model="searchQuery"
                      type="text"
                      placeholder="Software Engineer, Designer..."
                      class="w-full pl-4 pr-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                    />
                  </div>
                </div>

                <!-- Location Search -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-neutral-700">Location</label>
                  <div class="relative">
                    <input
                      v-model="locationQuery"
                      type="text"
                      placeholder="New York, Remote..."
                      class="w-full pl-4 pr-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                    />
                  </div>
                </div>

                <!-- Category Select -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-neutral-700">Category</label>
                  <select 
                    v-model="categoryQuery" 
                    class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                  >
                    <option value="">All Categories</option>
                    <option value="technology">Technology</option>
                    <option value="design">Design & Creative</option>
                    <option value="marketing">Marketing & Sales</option>
                    <option value="finance">Finance & Accounting</option>
                    <option value="healthcare">Healthcare</option>
                    <option value="education">Education</option>
                    <option value="engineering">Engineering</option>
                  </select>
                </div>

                <!-- Search Button -->
                <div class="space-y-2">
                  <label class="block text-sm font-medium text-transparent">Search</label>
                  <button
                    @click="searchJobs"
                    :disabled="jobsStore.loading"
                    class="w-full btn bg-gradient-to-r from-primary-600 to-purple-600 text-white hover:from-primary-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 py-3 px-6 rounded-lg font-semibold"
                  >
                    <svg v-if="jobsStore.loading" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Jobs
                  </button>
                </div>
              </div>

              <!-- Quick Filters -->
              <div class="mt-6 pt-6 border-t border-neutral-200">
                <div class="flex flex-wrap gap-2">
                  <span class="text-sm text-neutral-600 mr-3">Popular searches:</span>
                  <button 
                    v-for="tag in popularTags" 
                    :key="tag"
                    @click="quickSearch(tag)"
                    class="px-3 py-1 text-sm bg-white border border-neutral-300 rounded-full hover:bg-primary-50 hover:border-primary-300 hover:text-primary-700 transition-colors"
                  >
                    {{ tag }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
          <div 
            v-for="(stat, index) in stats" 
            :key="stat.label"
            class="text-center group cursor-pointer"
            :style="{ animationDelay: `${index * 100}ms` }"
          >
            <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-2xl group-hover:scale-110 transition-transform duration-200"
                 :class="stat.bgColor">
              <div :class="['text-2xl font-bold', stat.textColor]">
                {{ stat.icon }}
              </div>
            </div>
            <div :class="['text-3xl lg:text-4xl font-bold mb-2 group-hover:scale-105 transition-transform', stat.textColor]">
              {{ stat.value }}
            </div>
            <div class="text-neutral-600 font-medium">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Jobs Section -->
    <section class="py-20 bg-gradient-to-b from-white to-neutral-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-neutral-900 mb-4">
            Featured Opportunities
          </h2>
          <p class="text-xl text-neutral-600 max-w-3xl mx-auto">
            Discover amazing opportunities from top companies and innovative startups
          </p>
        </div>

        <!-- Jobs Grid -->
        <div v-if="jobsStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <!-- Loading Skeletons -->
          <div v-for="i in 6" :key="i" class="card animate-pulse">
            <div class="h-4 bg-neutral-200 rounded mb-4"></div>
            <div class="h-3 bg-neutral-200 rounded mb-2"></div>
            <div class="h-3 bg-neutral-200 rounded w-2/3 mb-4"></div>
            <div class="flex gap-2 mb-4">
              <div class="h-6 bg-neutral-200 rounded-full w-16"></div>
              <div class="h-6 bg-neutral-200 rounded-full w-20"></div>
            </div>
            <div class="h-3 bg-neutral-200 rounded"></div>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div 
            v-for="job in jobsStore.featuredJobs" 
            :key="job.id"
            class="card hoverable clickable group animate-slide-up"
            @click="viewJob(job.id)"
          >
            <!-- Job Header -->
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <h3 class="text-xl font-semibold text-neutral-900 mb-2 group-hover:text-primary-600 transition-colors">
                  {{ job.title }}
                </h3>
                <p class="text-primary-600 font-medium mb-1">{{ job.company }}</p>
                <p class="text-neutral-500 text-sm">{{ job.location }}</p>
              </div>
              <div class="w-12 h-12 bg-gradient-to-br from-primary-100 to-purple-100 rounded-lg flex items-center justify-center">
                <span class="text-lg font-bold text-primary-600">
                  {{ job.company.charAt(0) }}
                </span>
              </div>
            </div>

            <!-- Job Tags -->
            <div class="flex flex-wrap gap-2 mb-4">
              <span class="px-3 py-1 bg-primary-100 text-primary-800 text-sm rounded-full">
                {{ job.type }}
              </span>
              <span class="px-3 py-1 bg-success-100 text-success-800 text-sm rounded-full">
                {{ job.salary_range || 'Competitive' }}
              </span>
              <span v-if="job.is_featured" class="px-3 py-1 bg-warning-100 text-warning-800 text-sm rounded-full">
                Featured
              </span>
            </div>

            <!-- Job Description -->
            <p class="text-neutral-700 text-sm line-clamp-3 mb-4">
              {{ job.description }}
            </p>

            <!-- Job Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-neutral-200">
              <span class="text-xs text-neutral-500">
                Posted {{ job.posted_time || '2 days ago' }}
              </span>
              <div class="flex items-center text-primary-600 text-sm font-medium group-hover:text-primary-700">
                Apply Now
                <svg class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- View All Jobs Button -->
        <div class="text-center mt-12">
          <button
            @click="viewAllJobs"
            class="btn bg-white border-2 border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 px-8 py-3 rounded-lg font-semibold"
          >
            View All Jobs
            <svg class="h-5 w-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>
      </div>
    </section>

    <!-- Top Companies Section -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
          <h2 class="text-3xl lg:text-4xl font-bold text-neutral-900 mb-4">
            Top Companies
          </h2>
          <p class="text-xl text-neutral-600 max-w-3xl mx-auto">
            Join industry leaders and innovative startups shaping the future
          </p>
        </div>

        <!-- Companies Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
          <div 
            v-for="company in companiesStore.featuredCompanies.slice(0, 8)" 
            :key="company.id"
            class="card hoverable clickable text-center group animate-scale-in"
            @click="viewCompany(company.id)"
          >
            <!-- Company Logo -->
            <div class="w-16 h-16 bg-gradient-to-br from-neutral-100 to-neutral-200 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:from-primary-100 group-hover:to-purple-100 transition-all duration-200">
              <span class="text-xl font-bold text-neutral-600 group-hover:text-primary-600">
                {{ company.name.charAt(0) }}
              </span>
            </div>
            
            <!-- Company Info -->
            <h4 class="font-semibold text-neutral-900 text-sm mb-1 group-hover:text-primary-600 transition-colors">
              {{ company.name }}
            </h4>
            <p class="text-xs text-neutral-500">
              {{ company.job_count || Math.floor(Math.random() * 50) + 1 }} open positions
            </p>
          </div>
        </div>

        <!-- View All Companies Button -->
        <div class="text-center mt-12">
          <button
            @click="viewAllCompanies"
            class="btn bg-gradient-to-r from-secondary-600 to-neutral-600 text-white hover:from-secondary-700 hover:to-neutral-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 px-8 py-3 rounded-lg font-semibold"
          >
            Explore All Companies
          </button>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-purple-600 relative overflow-hidden">
      <!-- Background Elements -->
      <div class="absolute inset-0">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/10 rounded-full filter blur-3xl transform -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-white/10 rounded-full filter blur-3xl transform translate-x-1/2 translate-y-1/2"></div>
      </div>

      <div class="relative max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl lg:text-4xl font-bold text-white mb-6">
          Ready to Take the Next Step?
        </h2>
        <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">
          Join thousands of professionals who have found their dream jobs through our platform.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button class="btn bg-white text-primary-600 hover:bg-neutral-50 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 px-8 py-3 rounded-lg font-semibold">
            Create Your Profile
          </button>
          <button class="btn border-2 border-white text-white hover:bg-white hover:text-primary-600 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 px-8 py-3 rounded-lg font-semibold">
            Post a Job
          </button>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"
import { useJobsStore } from "../stores/jobs"
import { useCompaniesStore } from "../stores/companies"

const router = useRouter()
const jobsStore = useJobsStore()
const companiesStore = useCompaniesStore()

const searchQuery = ref("")
const locationQuery = ref("")
const categoryQuery = ref("")

const popularTags = ["Remote Work", "Full-time", "Senior Level", "JavaScript", "Design", "Marketing"]

const stats = [
  { 
    label: "Active Jobs", 
    value: "10,000+", 
    icon: "💼",
    bgColor: "bg-primary-100",
    textColor: "text-primary-600"
  },
  { 
    label: "Companies", 
    value: "5,000+", 
    icon: "🏢",
    bgColor: "bg-success-100",
    textColor: "text-success-600"
  },
  { 
    label: "Job Seekers", 
    value: "50,000+", 
    icon: "👥",
    bgColor: "bg-purple-100",
    textColor: "text-purple-600"
  },
  { 
    label: "Success Stories", 
    value: "2,500+", 
    icon: "⭐",
    bgColor: "bg-warning-100",
    textColor: "text-warning-600"
  }
]

const searchJobs = () => {
  jobsStore.searchJobs({
    search: searchQuery.value,
    location: locationQuery.value,
    category: categoryQuery.value
  })
  router.push("/jobs")
}

const quickSearch = (tag: string) => {
  searchQuery.value = tag
  searchJobs()
}

const viewJob = (id: number) => {
  router.push(`/jobs/${id}`)
}

const viewCompany = (id: number) => {
  router.push(`/companies/${id}`)
}

const viewAllJobs = () => {
  router.push("/jobs")
}

const viewAllCompanies = () => {
  router.push("/companies")
}

onMounted(() => {
  jobsStore.fetchJobs()
  companiesStore.fetchCompanies()
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-slide-up {
  animation: slideUp 0.6s ease-out;
}

.animate-scale-in {
  animation: scaleIn 0.4s ease-out;
}
</style>