<template>
  <div>
    <h1 class="text-3xl font-bold mb-8">Browse Jobs</h1>
    
    <!-- Search and Filters -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <Input 
          v-model="searchQuery" 
          placeholder="Search jobs..." 
        />
        <select v-model="locationFilter" 
                class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All Locations</option>
          <option value="remote">Remote</option>
          <option value="on-site">On-site</option>
        </select>
        <select v-model="categoryFilter" 
                class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All Categories</option>
          <option value="it">IT & Software</option>
          <option value="marketing">Marketing</option>
          <option value="sales">Sales</option>
        </select>
        <Button @click="searchJobs" :loading="jobsStore.loading">
          Search
        </Button>
      </div>
    </div>

    <!-- Jobs List -->
    <div class="space-y-6">
      <div v-for="job in jobsStore.filteredJobs" :key="job.id" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow">
        <h3 class="text-xl font-semibold mb-2">{{ job.title }}</h3>
        <p class="text-gray-600 mb-4">{{ job.company }}</p>
        <div class="flex flex-wrap gap-2 mb-4">
          <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
            {{ job.location }}
          </span>
          <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
            {{ job.type }}
          </span>
        </div>
        <p class="text-gray-700 mb-4">{{ job.description }}</p>
        <Button @click="viewJob(job.id)">
          View Details
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"
import { useJobsStore } from "../stores/jobs"
import Button from "../components/ui/Button.vue"
import Input from "../components/forms/Input.vue"

const router = useRouter()
const jobsStore = useJobsStore()

const searchQuery = ref("")
const locationFilter = ref("")
const categoryFilter = ref("")

const searchJobs = () => {
  jobsStore.searchJobs({
    search: searchQuery.value,
    location: locationFilter.value,
    category: categoryFilter.value
  })
}

const viewJob = (id: number) => {
  router.push(`/jobs/${id}`)
}

onMounted(() => {
  jobsStore.fetchJobs()
})
</script> 