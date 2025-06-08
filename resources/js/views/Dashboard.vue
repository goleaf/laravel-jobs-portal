<template>
  <div class="min-h-screen bg-gray-50">
    <nav class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-xl font-semibold">Job Portal Dashboard</h1>
          </div>
          <div class="flex items-center space-x-4">
            <router-link to="/" class="text-gray-600 hover:text-gray-900">
              Home
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Dashboard</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">Jobs</h3>
              <p class="text-3xl font-bold text-blue-600">{{ stats.jobs }}</p>
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">Companies</h3>
              <p class="text-3xl font-bold text-green-600">{{ stats.companies }}</p>
            </div>
          </div>
          
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900">Candidates</h3>
              <p class="text-3xl font-bold text-purple-600">{{ stats.candidates }}</p>
            </div>
          </div>
        </div>

        <div class="mt-8">
          <div class="bg-white shadow rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">
                Recent Activity
              </h3>
              <div class="space-y-4">
                <div v-for="activity in activities" :key="activity.id" 
                     class="border-l-4 border-blue-400 pl-4">
                  <p class="text-sm text-gray-900">{{ activity.message }}</p>
                  <p class="text-xs text-gray-500">{{ activity.time }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const stats = ref({
  jobs: 0,
  companies: 0,
  candidates: 0
})

const activities = ref([
  { id: 1, message: 'New job posted: Senior Developer', time: '2 hours ago' },
  { id: 2, message: 'Company registered: Tech Corp', time: '4 hours ago' },
  { id: 3, message: 'Candidate applied for Frontend Developer', time: '6 hours ago' }
])

onMounted(() => {
  // Simulate loading stats
  setTimeout(() => {
    stats.value = {
      jobs: 150,
      companies: 45,
      candidates: 320
    }
  }, 500)
})
</script> 