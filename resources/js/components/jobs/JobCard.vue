<template>
  <div
    class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden group"
    :class="{ 'ring-2 ring-indigo-500': featured }"
  >
    <!-- Job Header -->
    <div class="p-6">
      <!-- Company Logo & Featured Badge -->
      <div class="flex items-start justify-between mb-4">
        <div class="flex items-center space-x-3">
          <!-- Company Logo -->
          <div v-if="showCompanyLogo" class="flex-shrink-0">
            <img
              v-if="job.company?.logo"
              :src="job.company.logo"
              :alt="job.company.name"
              class="w-12 h-12 rounded-lg object-cover border border-gray-200"
            />
            <div
              v-else
              class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center"
            >
              <span class="text-white font-semibold text-lg">
                {{ job.company?.name?.charAt(0) || 'C' }}
              </span>
            </div>
          </div>

          <!-- Job Title & Company -->
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition-colors duration-200">
              {{ job.title }}
            </h3>
            <p class="text-sm text-gray-600 truncate">
              {{ job.company?.name || 'Unknown Company' }}
            </p>
          </div>
        </div>

        <!-- Featured Badge -->
        <div v-if="featured" class="flex-shrink-0">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            <StarIcon class="w-3 h-3 mr-1" />
            Featured
          </span>
        </div>
      </div>

      <!-- Job Details -->
      <div class="space-y-3">
        <!-- Location & Employment Type -->
        <div class="flex items-center space-x-4 text-sm text-gray-600">
          <div class="flex items-center">
            <MapPinIcon class="w-4 h-4 mr-1" />
            <span>{{ job.location || 'Location not specified' }}</span>
          </div>
          <div class="flex items-center">
            <ClockIcon class="w-4 h-4 mr-1" />
            <span>{{ formatEmploymentType(job.employment_type) }}</span>
          </div>
        </div>

        <!-- Salary Range -->
        <div v-if="job.salary_min || job.salary_max" class="flex items-center text-sm text-gray-600">
          <CurrencyDollarIcon class="w-4 h-4 mr-1" />
          <span>{{ formatSalaryRange(job.salary_min, job.salary_max) }}</span>
        </div>

        <!-- Job Description Preview -->
        <p class="text-sm text-gray-700 line-clamp-3">
          {{ job.description || 'No description available.' }}
        </p>

        <!-- Skills/Tags -->
        <div v-if="job.skills && job.skills.length > 0" class="flex flex-wrap gap-2">
          <span
            v-for="skill in job.skills.slice(0, 4)"
            :key="skill"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200"
          >
            {{ skill }}
          </span>
          <span
            v-if="job.skills.length > 4"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"
          >
            +{{ job.skills.length - 4 }} more
          </span>
        </div>
      </div>
    </div>

    <!-- Job Footer -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <!-- Posted Date -->
        <div class="flex items-center text-xs text-gray-500">
          <CalendarIcon class="w-4 h-4 mr-1" />
          <span>{{ formatPostedDate(job.created_at) }}</span>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-2">
          <!-- Bookmark Button -->
          <button
            @click.stop="toggleBookmark"
            :disabled="bookmarkLoading"
            class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
            :class="{ 'text-indigo-600 bg-indigo-50': isBookmarked }"
          >
            <BookmarkIcon
              class="w-5 h-5"
              :class="{ 'fill-current': isBookmarked }"
            />
          </button>

          <!-- View Details Button -->
          <BaseButton
            variant="outline-primary"
            size="sm"
            :to="{ name: 'jobs.show', params: { id: job.id } }"
            tag="router-link"
            class="hover:scale-105 transition-transform duration-200"
          >
            View Details
          </BaseButton>

          <!-- Quick Apply Button -->
          <BaseButton
            v-if="showQuickApply"
            variant="primary"
            size="sm"
            @click.stop="quickApply"
            :loading="applyLoading"
            class="hover:scale-105 transition-transform duration-200"
          >
            Quick Apply
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Application Status Overlay -->
    <div
      v-if="applicationStatus"
      class="absolute top-4 right-4 z-10"
    >
      <span
        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
        :class="applicationStatusClasses"
      >
        <component :is="applicationStatusIcon" class="w-3 h-3 mr-1" />
        {{ applicationStatus }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import {
  MapPinIcon,
  ClockIcon,
  CurrencyDollarIcon,
  CalendarIcon,
  BookmarkIcon,
  StarIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ClockIcon as PendingIcon
} from '@heroicons/vue/24/outline'
import BaseButton from '../ui/BaseButton.vue'

interface Job {
  id: number
  title: string
  description?: string
  location?: string
  employment_type?: string
  salary_min?: number
  salary_max?: number
  skills?: string[]
  created_at: string
  company?: {
    id: number
    name: string
    logo?: string
  }
  application_status?: 'applied' | 'pending' | 'rejected' | 'interviewed'
  is_bookmarked?: boolean
}

interface Props {
  job: Job
  showCompanyLogo?: boolean
  showQuickApply?: boolean
  featured?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showCompanyLogo: true,
  showQuickApply: true,
  featured: false
})

const emit = defineEmits<{
  bookmark: [jobId: number, bookmarked: boolean]
  apply: [jobId: number]
}>()

// State
const bookmarkLoading = ref(false)
const applyLoading = ref(false)
const isBookmarked = ref(props.job.is_bookmarked || false)

// Computed
const applicationStatus = computed(() => props.job.application_status)

const applicationStatusClasses = computed(() => {
  const statusMap = {
    applied: 'bg-blue-100 text-blue-800',
    pending: 'bg-yellow-100 text-yellow-800',
    rejected: 'bg-red-100 text-red-800',
    interviewed: 'bg-green-100 text-green-800'
  }
  return applicationStatus.value ? statusMap[applicationStatus.value] : ''
})

const applicationStatusIcon = computed(() => {
  const iconMap = {
    applied: CheckCircleIcon,
    pending: PendingIcon,
    rejected: ExclamationCircleIcon,
    interviewed: CheckCircleIcon
  }
  return applicationStatus.value ? iconMap[applicationStatus.value] : null
})

// Methods
const formatEmploymentType = (type?: string) => {
  if (!type) return 'Not specified'
  
  const typeMap: Record<string, string> = {
    'full-time': 'Full-time',
    'part-time': 'Part-time',
    'contract': 'Contract',
    'freelance': 'Freelance',
    'internship': 'Internship',
    'temporary': 'Temporary'
  }
  
  return typeMap[type] || type
}

const formatSalaryRange = (min?: number, max?: number) => {
  if (!min && !max) return 'Salary not specified'
  
  const formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  })
  
  if (min && max) {
    return `${formatter.format(min)} - ${formatter.format(max)}`
  } else if (min) {
    return `From ${formatter.format(min)}`
  } else if (max) {
    return `Up to ${formatter.format(max)}`
  }
  
  return 'Salary not specified'
}

const formatPostedDate = (dateString: string) => {
  if (!dateString) return 'Recently posted'
  
  const date = new Date(dateString)
  const now = new Date()
  const diffInDays = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60 * 24))
  
  if (diffInDays === 0) return 'Today'
  if (diffInDays === 1) return 'Yesterday'
  if (diffInDays < 7) return `${diffInDays} days ago`
  if (diffInDays < 30) return `${Math.floor(diffInDays / 7)} weeks ago`
  if (diffInDays < 365) return `${Math.floor(diffInDays / 30)} months ago`
  
  return `${Math.floor(diffInDays / 365)} years ago`
}

const toggleBookmark = async () => {
  if (bookmarkLoading.value) return
  
  bookmarkLoading.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    isBookmarked.value = !isBookmarked.value
    emit('bookmark', props.job.id, isBookmarked.value)
  } catch (error) {
    console.error('Failed to toggle bookmark:', error)
  } finally {
    bookmarkLoading.value = false
  }
}

const quickApply = async () => {
  if (applyLoading.value) return
  
  applyLoading.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    emit('apply', props.job.id)
  } catch (error) {
    console.error('Failed to apply to job:', error)
  } finally {
    applyLoading.value = false
  }
}
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 