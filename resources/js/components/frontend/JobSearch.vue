<template>
  <div class="job-search bg-white shadow-lg rounded-lg p-6">
    <!-- Search Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $t('jobs.search.title') }}</h2>
      <p class="text-gray-600">{{ $t('jobs.search.subtitle') }}</p>
    </div>

    <!-- Search Form -->
    <form @submit.prevent="performSearch" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Keyword Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('jobs.search.keyword') }}
          </label>
          <input
            v-model="searchForm.keyword"
            type="text"
            :placeholder="$t('jobs.search.keyword_placeholder')"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>

        <!-- Location -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('jobs.search.location') }}
          </label>
          <select
            v-model="searchForm.location"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">{{ $t('jobs.search.all_locations') }}</option>
            <option v-for="location in locations" :key="location.id" :value="location.id">
              {{ location.name }}
            </option>
          </select>
        </div>

        <!-- Category -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('jobs.search.category') }}
          </label>
          <select
            v-model="searchForm.category"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          >
            <option value="">{{ $t('jobs.search.all_categories') }}</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Search Button -->
      <div class="flex justify-end">
        <button
          type="submit"
          :disabled="searching"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          <span v-if="searching" class="flex items-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
            {{ $t('jobs.search.searching') }}
          </span>
          <span v-else>{{ $t('jobs.search.search') }}</span>
        </button>
      </div>
    </form>

    <!-- Search Results -->
    <div v-if="searchPerformed" class="mt-8">
      <!-- Results Header -->
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">
          {{ $t('jobs.search.results_count', { count: totalJobs }) }}
        </h3>
      </div>

      <!-- Loading State -->
      <div v-if="searching" class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
      </div>

      <!-- Job Results -->
      <div v-else-if="jobs.length > 0" class="space-y-4">
        <div
          v-for="job in jobs"
          :key="job.id"
          class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h4 class="text-lg font-semibold text-gray-900 mb-2">
                <router-link :to="`/jobs/${job.id}`" class="hover:text-indigo-600">
                  {{ job.title }}
                </router-link>
              </h4>
              
              <div class="flex items-center text-sm text-gray-600 mb-2">
                <span class="font-medium">{{ job.company.name }}</span>
                <span class="mx-2">•</span>
                <span>{{ job.location }}</span>
              </div>
              
              <p class="text-gray-700 mb-3">{{ job.description }}</p>
            </div>
            
            <div class="ml-4">
              <router-link
                :to="`/jobs/${job.id}`"
                class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700"
              >
                {{ $t('jobs.search.view_details') }}
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-else class="text-center py-8">
        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $t('jobs.search.no_results') }}</h3>
        <p class="text-gray-600">{{ $t('jobs.search.no_results_description') }}</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'

// Types
interface Job {
  id: number
  title: string
  description: string
  company: {
    name: string
  }
  location: string
}

interface SearchForm {
  keyword: string
  location: string
  category: string
}

// Composables
const { t } = useI18n()

// State
const searching = ref(false)
const searchPerformed = ref(false)
const jobs = ref<Job[]>([])
const totalJobs = ref(0)

const searchForm = reactive<SearchForm>({
  keyword: '',
  location: '',
  category: ''
})

// Mock data
const locations = ref([
  { id: 1, name: 'New York' },
  { id: 2, name: 'San Francisco' },
  { id: 3, name: 'London' }
])

const categories = ref([
  { id: 1, name: 'Technology' },
  { id: 2, name: 'Marketing' },
  { id: 3, name: 'Finance' }
])

// Methods
const performSearch = async () => {
  searching.value = true
  searchPerformed.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Mock results
    jobs.value = [
      {
        id: 1,
        title: 'Senior Software Engineer',
        description: 'We are looking for a senior software engineer to join our team...',
        company: { name: 'Tech Corp' },
        location: 'San Francisco, CA'
      }
    ]
    
    totalJobs.value = 1
  } catch (error) {
    console.error('Search failed:', error)
  } finally {
    searching.value = false
  }
}

// Lifecycle
onMounted(() => {
  // Load initial data
})
</script>
