<template>
  <div
    class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden group cursor-pointer"
    @click="viewCompany"
  >
    <!-- Company Header -->
    <div class="p-6">
      <!-- Company Logo & Info -->
      <div class="flex items-start space-x-4 mb-4">
        <!-- Company Logo -->
        <div class="flex-shrink-0">
          <img
            v-if="company.logo"
            :src="company.logo"
            :alt="company.name"
            class="w-16 h-16 rounded-lg object-cover border border-gray-200"
          />
          <div
            v-else
            class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center"
          >
            <span class="text-white font-bold text-xl">
              {{ company.name?.charAt(0) || 'C' }}
            </span>
          </div>
        </div>

        <!-- Company Info -->
        <div class="flex-1 min-w-0">
          <h3 class="text-xl font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition-colors duration-200">
            {{ company.name }}
          </h3>
          <p class="text-sm text-gray-600 truncate">
            {{ company.industry || 'Technology' }}
          </p>
          <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
            <div class="flex items-center">
              <MapPinIcon class="w-3 h-3 mr-1" />
              <span>{{ company.location || 'Multiple Locations' }}</span>
            </div>
            <div class="flex items-center">
              <UsersIcon class="w-3 h-3 mr-1" />
              <span>{{ formatCompanySize(company.size) }}</span>
            </div>
          </div>
        </div>

        <!-- Follow Button -->
        <button
          @click.stop="toggleFollow"
          :disabled="followLoading"
          class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors duration-200"
          :class="{ 'text-indigo-600 bg-indigo-50': isFollowing }"
        >
          <HeartIcon
            class="w-5 h-5"
            :class="{ 'fill-current': isFollowing }"
          />
        </button>
      </div>

      <!-- Company Description -->
      <p class="text-sm text-gray-700 line-clamp-3 mb-4">
        {{ company.description || 'No description available.' }}
      </p>

      <!-- Company Stats -->
      <div class="grid grid-cols-3 gap-4 mb-4">
        <div class="text-center">
          <div class="text-lg font-semibold text-gray-900">{{ company.open_jobs || 0 }}</div>
          <div class="text-xs text-gray-500">Open Jobs</div>
        </div>
        <div class="text-center">
          <div class="text-lg font-semibold text-gray-900">{{ company.employees || 'N/A' }}</div>
          <div class="text-xs text-gray-500">Employees</div>
        </div>
        <div class="text-center">
          <div class="text-lg font-semibold text-gray-900">{{ formatFoundedYear(company.founded) }}</div>
          <div class="text-xs text-gray-500">Founded</div>
        </div>
      </div>

      <!-- Company Benefits/Features -->
      <div v-if="company.benefits && company.benefits.length > 0" class="flex flex-wrap gap-2 mb-4">
        <span
          v-for="benefit in company.benefits.slice(0, 3)"
          :key="benefit"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200"
        >
          {{ benefit }}
        </span>
        <span
          v-if="company.benefits.length > 3"
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"
        >
          +{{ company.benefits.length - 3 }} more
        </span>
      </div>

      <!-- Company Rating -->
      <div v-if="company.rating" class="flex items-center mb-4">
        <div class="flex items-center">
          <StarIcon
            v-for="n in 5"
            :key="n"
            class="w-4 h-4"
            :class="n <= Math.floor(company.rating) ? 'text-yellow-400 fill-current' : 'text-gray-300'"
          />
        </div>
        <span class="ml-2 text-sm text-gray-600">
          {{ company.rating.toFixed(1) }} ({{ company.reviews_count || 0 }} reviews)
        </span>
      </div>
    </div>

    <!-- Company Footer -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <!-- Company Type & Verification -->
        <div class="flex items-center space-x-3">
          <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
            :class="getCompanyTypeClasses(company.type)"
          >
            {{ formatCompanyType(company.type) }}
          </span>
          <span
            v-if="company.verified"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700"
          >
            <CheckBadgeIcon class="w-3 h-3 mr-1" />
            Verified
          </span>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-2">
          <BaseButton
            variant="outline"
            size="sm"
            @click.stop="viewJobs"
          >
            View Jobs ({{ company.open_jobs || 0 }})
          </BaseButton>
          <BaseButton
            variant="primary"
            size="sm"
            @click.stop="viewCompany"
          >
            View Company
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Hiring Badge -->
    <div
      v-if="company.is_hiring"
      class="absolute top-4 right-4 z-10"
    >
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
        <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
        Actively Hiring
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import {
  MapPinIcon,
  UsersIcon,
  HeartIcon,
  StarIcon,
  CheckBadgeIcon
} from '@heroicons/vue/24/outline'
import BaseButton from '../ui/BaseButton.vue'

interface Company {
  id: number
  name: string
  description?: string
  logo?: string
  industry?: string
  location?: string
  size?: string
  founded?: string | number
  open_jobs?: number
  employees?: string | number
  benefits?: string[]
  rating?: number
  reviews_count?: number
  type?: 'startup' | 'corporate' | 'nonprofit' | 'government'
  verified?: boolean
  is_hiring?: boolean
  is_following?: boolean
}

interface Props {
  company: Company
}

const props = defineProps<Props>()

const emit = defineEmits<{
  follow: [companyId: number, following: boolean]
  viewJobs: [companyId: number]
}>()

const router = useRouter()

// State
const followLoading = ref(false)
const isFollowing = ref(props.company.is_following || false)

// Methods
const formatCompanySize = (size?: string | number) => {
  if (!size) return 'Unknown'
  
  if (typeof size === 'string') return size
  
  const sizeNum = Number(size)
  if (sizeNum < 50) return '1-50 employees'
  if (sizeNum < 200) return '51-200 employees'
  if (sizeNum < 1000) return '201-1000 employees'
  if (sizeNum < 5000) return '1001-5000 employees'
  return '5000+ employees'
}

const formatFoundedYear = (founded?: string | number) => {
  if (!founded) return 'N/A'
  if (typeof founded === 'string') return founded
  return founded.toString()
}

const formatCompanyType = (type?: string) => {
  const typeMap: Record<string, string> = {
    startup: 'Startup',
    corporate: 'Corporate',
    nonprofit: 'Non-Profit',
    government: 'Government'
  }
  return typeMap[type || 'corporate'] || 'Company'
}

const getCompanyTypeClasses = (type?: string) => {
  const classMap: Record<string, string> = {
    startup: 'bg-purple-50 text-purple-700',
    corporate: 'bg-blue-50 text-blue-700',
    nonprofit: 'bg-green-50 text-green-700',
    government: 'bg-gray-50 text-gray-700'
  }
  return classMap[type || 'corporate'] || 'bg-blue-50 text-blue-700'
}

const toggleFollow = async () => {
  if (followLoading.value) return
  
  followLoading.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 500))
    
    isFollowing.value = !isFollowing.value
    emit('follow', props.company.id, isFollowing.value)
  } catch (error) {
    console.error('Failed to toggle follow:', error)
  } finally {
    followLoading.value = false
  }
}

const viewCompany = () => {
  router.push({ name: 'companies.show', params: { id: props.company.id } })
}

const viewJobs = () => {
  emit('viewJobs', props.company.id)
  router.push({ 
    name: 'jobs.index', 
    query: { company: props.company.id } 
  })
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