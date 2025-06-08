<template>
  <div class="space-y-16">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20 rounded-2xl overflow-hidden">
      <div class="absolute inset-0 bg-black opacity-10"></div>
      <div class="relative z-10 text-center">
        <h1 class="text-5xl md:text-6xl font-bold mb-6">
          Find Your <span class="text-yellow-300">Dream Job</span>
        </h1>
        <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
          Connect with top employers and discover opportunities that match your skills and ambitions in our global job marketplace.
        </p>
        
        <!-- Search Bar -->
        <div class="max-w-4xl mx-auto bg-white rounded-lg p-4 shadow-lg">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <Input
              v-model="searchQuery"
              placeholder="Job title or keyword"
              class="text-gray-900"
            />
            <Input
              v-model="locationQuery"
              placeholder="Location"
              class="text-gray-900"
            />
            <select v-model="categoryQuery" class="rounded-md border-gray-300 text-gray-900">
              <option value="">All Categories</option>
              <option value="it">IT & Software</option>
              <option value="marketing">Marketing</option>
              <option value="sales">Sales</option>
              <option value="finance">Finance</option>
            </select>
            <Button variant="primary" size="lg" @click="searchJobs" :loading="jobsStore.loading">
              Search Jobs
            </Button>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics -->
    <section class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <div v-for="stat in stats" :key="stat.label" class="text-center bg-white p-8 rounded-xl shadow-md hover:shadow-lg transition-shadow">
        <div :class="['text-4xl font-bold mb-2', stat.color]">{{ stat.value }}</div>
        <div class="text-gray-600 font-medium">{{ stat.label }}</div>
      </div>
    </section>

    <!-- Featured Jobs -->
    <section class="space-y-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Jobs</h2>
        <p class="text-lg text-gray-600">Discover amazing opportunities from top companies</p>
      </div>
      
      <div v-if="jobsStore.loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-white p-6 rounded-lg shadow-md animate-pulse">
          <div class="h-4 bg-gray-200 rounded mb-4"></div>
          <div class="h-3 bg-gray-200 rounded mb-2"></div>
          <div class="h-3 bg-gray-200 rounded w-2/3"></div>
        </div>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="job in jobsStore.featuredJobs" :key="job.id" 
             class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer"
             @click="viewJob(job.id)">
          <h3 class="text-xl font-semibold mb-2 text-gray-900">{{ job.title }}</h3>
          <p class="text-blue-600 font-medium mb-2">{{ job.company }}</p>
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
              {{ job.location }}
            </span>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
              {{ job.type }}
            </span>
            <span v-if="job.is_featured" class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
              Featured
            </span>
          </div>
          <p class="text-gray-700 text-sm line-clamp-3">{{ job.description }}</p>
        </div>
      </div>
      
      <div class="text-center">
        <Button variant="outline" size="lg" @click="viewAllJobs">
          View All Jobs
        </Button>
      </div>
    </section>

    <!-- Featured Companies -->
    <section class="space-y-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Top Companies</h2>
        <p class="text-lg text-gray-600">Join industry leaders and innovative startups</p>
      </div>
      
      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
        <div v-for="company in companiesStore.featuredCompanies.slice(0, 8)" :key="company.id"
             class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-shadow text-center cursor-pointer"
             @click="viewCompany(company.id)">
          <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold mx-auto mb-2">
            {{ company.name.charAt(0) }}
          </div>
          <h4 class="font-medium text-sm text-gray-900 truncate">{{ company.name }}</h4>
          <p class="text-xs text-gray-500">{{ company.job_count }} jobs</p>
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
import Button from "../components/ui/Button.vue"
import Input from "../components/forms/Input.vue"

const router = useRouter()
const jobsStore = useJobsStore()
const companiesStore = useCompaniesStore()

const searchQuery = ref("")
const locationQuery = ref("")
const categoryQuery = ref("")

const stats = [
  { label: "Active Jobs", value: "10,000+", color: "text-blue-600" },
  { label: "Companies", value: "5,000+", color: "text-green-600" },
  { label: "Job Seekers", value: "50,000+", color: "text-purple-600" },
  { label: "Success Stories", value: "2,500+", color: "text-orange-600" }
]

const searchJobs = () => {
  jobsStore.searchJobs({
    search: searchQuery.value,
    location: locationQuery.value,
    category: categoryQuery.value
  })
  router.push("/jobs")
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

onMounted(() => {
  jobsStore.fetchJobs()
  companiesStore.fetchCompanies()
})
</script>