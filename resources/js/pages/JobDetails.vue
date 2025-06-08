<template>
  <div v-if="loading" class="space-y-6">
    <div class="bg-white p-8 rounded-lg shadow-md animate-pulse">
      <div class="h-8 bg-gray-200 rounded mb-4"></div>
      <div class="h-4 bg-gray-200 rounded mb-2"></div>
      <div class="h-4 bg-gray-200 rounded w-2/3 mb-4"></div>
      <div class="h-32 bg-gray-200 rounded"></div>
    </div>
  </div>

  <div v-else-if="job" class="space-y-8">
    <!-- Job Header -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ job.title }}</h1>
          <p class="text-xl text-blue-600 font-medium">{{ job.company }}</p>
        </div>
        <div class="mt-4 md:mt-0">
          <Button variant="primary" size="lg" @click="applyForJob">
            Apply Now
          </Button>
        </div>
      </div>
      
      <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex items-center text-gray-600">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
          {{ job.location }}
        </div>
        <div class="flex items-center text-gray-600">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          {{ job.type }}
        </div>
        <div v-if="job.salary" class="flex items-center text-gray-600">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
          </svg>
          {{ job.salary }}
        </div>
      </div>
      
      <div class="flex flex-wrap gap-2">
        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
          {{ job.type }}
        </span>
        <span v-if="job.is_featured" class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">
          Featured
        </span>
      </div>
    </div>

    <!-- Job Description -->
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-2xl font-bold text-gray-900 mb-4">Job Description</h2>
      <div class="prose max-w-none">
        <p class="text-gray-700 whitespace-pre-line">{{ job.description }}</p>
      </div>
    </div>

    <!-- Application Modal -->
    <Modal :show="showApplicationModal" title="Apply for Job" @close="showApplicationModal = false">
      <div class="space-y-4">
        <Input
          v-model="applicationData.full_name"
          label="Full Name"
          required
        />
        <Input
          v-model="applicationData.email"
          type="email"
          label="Email Address"
          required
        />
        <Input
          v-model="applicationData.phone"
          label="Phone Number"
          required
        />
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Cover Letter
          </label>
          <textarea
            v-model="applicationData.cover_letter"
            rows="4"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Tell us why you are interested in this position..."
          ></textarea>
        </div>
      </div>
      
      <template #footer>
        <div class="flex justify-end space-x-4">
          <Button variant="outline" @click="showApplicationModal = false">
            Cancel
          </Button>
          <Button variant="primary" @click="submitApplication" :loading="submitting">
            Submit Application
          </Button>
        </div>
      </template>
    </Modal>
  </div>

  <div v-else class="text-center py-16">
    <h1 class="text-2xl font-bold text-gray-900 mb-4">Job Not Found</h1>
    <p class="text-gray-600 mb-8">The job you are looking for does not exist.</p>
    <Button variant="primary" @click="$router.push('/jobs')">
      Browse All Jobs
    </Button>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from "vue"
import { useRoute, useRouter } from "vue-router"
import { useJobsStore } from "../stores/jobs"
import Button from "../components/ui/Button.vue"
import Input from "../components/forms/Input.vue"
import Modal from "../components/ui/Modal.vue"

const route = useRoute()
const router = useRouter()
const jobsStore = useJobsStore()

const loading = ref(true)
const showApplicationModal = ref(false)
const submitting = ref(false)

const applicationData = ref({
  full_name: "",
  email: "",
  phone: "",
  cover_letter: ""
})

const job = computed(() => {
  const jobId = parseInt(route.params.id as string)
  return jobsStore.getJobById(jobId)
})

const applyForJob = () => {
  showApplicationModal.value = true
}

const submitApplication = async () => {
  submitting.value = true
  
  try {
    // Submit application logic here
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulate API call
    
    showApplicationModal.value = false
    // Show success message
    alert("Application submitted successfully!")
  } catch (error) {
    alert("Failed to submit application. Please try again.")
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  if (jobsStore.jobs.length === 0) {
    await jobsStore.fetchJobs()
  }
  loading.value = false
})
</script>