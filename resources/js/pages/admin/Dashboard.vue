<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <h1 class="text-xl font-semibold text-gray-900">Admin Dashboard</h1>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <button @click="logout" class="text-gray-500 hover:text-gray-700">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <div class="flex">
      <!-- Sidebar -->
      <div class="w-64 bg-white shadow-sm">
        <div class="p-4">
          <nav class="space-y-2">
            <router-link
              v-for="item in navigation"
              :key="item.name"
              :to="item.href"
              class="group flex items-center px-2 py-2 text-sm font-medium rounded-md"
              :class="[
                $route.path === item.href
                  ? 'bg-indigo-100 text-indigo-700'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
              ]"
            >
              <component
                :is="item.icon"
                class="mr-3 h-5 w-5 flex-shrink-0"
                :class="[
                  $route.path === item.href
                    ? 'text-indigo-500'
                    : 'text-gray-400 group-hover:text-gray-500'
                ]"
              />
              {{ item.name }}
            </router-link>
          </nav>
        </div>
      </div>

      <!-- Main content -->
      <div class="flex-1 p-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
          <div
            v-for="stat in stats"
            :key="stat.name"
            class="bg-white rounded-lg shadow p-6"
          >
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <component
                  :is="stat.icon"
                  class="h-8 w-8"
                  :class="stat.iconColor"
                />
              </div>
              <div class="ml-4 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">
                    {{ stat.name }}
                  </dt>
                  <dd class="text-2xl font-semibold text-gray-900">
                    {{ stat.value }}
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Recent Jobs -->
          <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Recent Jobs</h3>
            </div>
            <div class="p-6">
              <div v-if="loading" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
              </div>
              <div v-else-if="recentJobs.length === 0" class="text-center py-4 text-gray-500">
                No recent jobs found
              </div>
              <div v-else class="space-y-4">
                <div
                  v-for="job in recentJobs"
                  :key="job.id"
                  class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
                >
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">{{ job.title }}</h4>
                    <p class="text-sm text-gray-500">{{ job.company }}</p>
                  </div>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Active
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Recent Applications -->
          <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Recent Applications</h3>
            </div>
            <div class="p-6">
              <div v-if="loading" class="text-center py-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
              </div>
              <div v-else-if="recentApplications.length === 0" class="text-center py-4 text-gray-500">
                No recent applications found
              </div>
              <div v-else class="space-y-4">
                <div
                  v-for="application in recentApplications"
                  :key="application.id"
                  class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
                >
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">{{ application.candidate_name }}</h4>
                    <p class="text-sm text-gray-500">{{ application.job_title }}</p>
                  </div>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Pending
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { dashboardApi } from '../../services/api'

// Icons (you can replace with your preferred icon library)
const HomeIcon = 'svg'
const UsersIcon = 'svg'
const BriefcaseIcon = 'svg'
const OfficeBuildingIcon = 'svg'
const CogIcon = 'svg'
const ChartBarIcon = 'svg'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(true)
const stats = ref([
  {
    name: 'Total Jobs',
    value: '0',
    icon: BriefcaseIcon,
    iconColor: 'text-blue-600'
  },
  {
    name: 'Total Companies',
    value: '0',
    icon: OfficeBuildingIcon,
    iconColor: 'text-green-600'
  },
  {
    name: 'Total Candidates',
    value: '0',
    icon: UsersIcon,
    iconColor: 'text-purple-600'
  },
  {
    name: 'Applications',
    value: '0',
    icon: ChartBarIcon,
    iconColor: 'text-orange-600'
  }
])

const recentJobs = ref([])
const recentApplications = ref([])

const navigation = [
  { name: 'Dashboard', href: '/admin', icon: HomeIcon },
  { name: 'Jobs', href: '/admin/jobs', icon: BriefcaseIcon },
  { name: 'Companies', href: '/admin/companies', icon: OfficeBuildingIcon },
  { name: 'Candidates', href: '/admin/candidates', icon: UsersIcon },
  { name: 'Settings', href: '/admin/settings', icon: CogIcon },
]

const fetchDashboardData = async () => {
  try {
    loading.value = true
    const [statsData, jobsData, applicationsData] = await Promise.all([
      dashboardApi.getStats(),
      dashboardApi.getRecentJobs(),
      dashboardApi.getRecentApplications()
    ])
    
    stats.value = [
      { ...stats.value[0], value: statsData.total_jobs || '0' },
      { ...stats.value[1], value: statsData.total_companies || '0' },
      { ...stats.value[2], value: statsData.total_candidates || '0' },
      { ...stats.value[3], value: statsData.total_applications || '0' }
    ]
    
    recentJobs.value = jobsData.data || []
    recentApplications.value = applicationsData.data || []
  } catch (error) {
    console.error('Failed to fetch dashboard data:', error)
  } finally {
    loading.value = false
  }
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
/* Component-specific styles */
.router-link-exact-active {
  @apply bg-indigo-100 text-indigo-700;
}
</style> 