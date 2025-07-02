<template>
  <MainLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="bg-white px-6 py-8">
        <div class="max-w-7xl mx-auto">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                System Administration 🛠️
              </h1>
              <p class="text-gray-600 text-lg">
                Monitor platform health, manage users, and oversee {{ totalUsers }} registered users across {{ totalCompanies }} companies
              </p>
            </div>
            
            <div class="flex gap-3">
              <BaseButton
                variant="outline-primary"
                :to="{ name: 'admin.users' }"
                tag="router-link"
              >
                <UsersIcon class="h-4 w-4 mr-2" />
                Manage Users
              </BaseButton>
              
              <BaseButton
                variant="primary"
                :to="{ name: 'admin.settings' }"
                tag="router-link"
              >
                <CogIcon class="h-4 w-4 mr-2" />
                System Settings
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- System Health Alert -->
      <div v-if="systemHealth.status !== 'healthy'" class="mb-8">
        <div 
          :class="[
            'rounded-lg border-l-4 p-4',
            systemHealth.status === 'warning' ? 'bg-yellow-50 border-yellow-400' : 'bg-red-50 border-red-400'
          ]"
        >
          <div class="flex">
            <div class="flex-shrink-0">
              <ExclamationTriangleIcon 
                :class="[
                  'h-5 w-5',
                  systemHealth.status === 'warning' ? 'text-yellow-400' : 'text-red-400'
                ]" 
              />
            </div>
            <div class="ml-3">
              <h3 :class="[
                'text-sm font-medium',
                systemHealth.status === 'warning' ? 'text-yellow-800' : 'text-red-800'
              ]">
                {{ systemHealth.message }}
              </h3>
              <div :class="[
                'mt-2 text-sm',
                systemHealth.status === 'warning' ? 'text-yellow-700' : 'text-red-700'
              ]">
                <p>{{ systemHealth.details }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- System Overview Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <UsersIcon class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.totalUsers.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">
                <span :class="stats.userGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ stats.userGrowth >= 0 ? '+' : '' }}{{ stats.userGrowth }}%
                </span>
                this month
              </p>
            </div>
          </div>
        </div>

        <!-- Active Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <BriefcaseIcon class="h-6 w-6 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Active Jobs</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.activeJobs.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">
                <span :class="stats.jobGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ stats.jobGrowth >= 0 ? '+' : '' }}{{ stats.jobGrowth }}%
                </span>
                this month
              </p>
            </div>
          </div>
        </div>

        <!-- Applications Today -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <DocumentTextIcon class="h-6 w-6 text-purple-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Applications Today</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.applicationsToday.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">
                <span :class="stats.applicationGrowth >= 0 ? 'text-green-600' : 'text-red-600'">
                  {{ stats.applicationGrowth >= 0 ? '+' : '' }}{{ stats.applicationGrowth }}%
                </span>
                vs yesterday
              </p>
            </div>
          </div>
        </div>

        <!-- System Load -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <ServerIcon class="h-6 w-6 text-yellow-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">System Load</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.systemLoad }}%</p>
              <p class="text-xs text-gray-500">
                <span :class="getLoadColor(stats.systemLoad)">
                  {{ getLoadStatus(stats.systemLoad) }}
                </span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (Left Column - 2/3) -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Platform Analytics Chart -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-lg font-semibold text-gray-900">Platform Analytics</h2>
              <div class="flex items-center space-x-2">
                <select
                  v-model="selectedTimeframe"
                  class="text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"
                >
                  <option value="7d">Last 7 days</option>
                  <option value="30d">Last 30 days</option>
                  <option value="90d">Last 90 days</option>
                </select>
              </div>
            </div>
            
            <!-- Chart placeholder with mock data visualization -->
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
              <div class="text-center">
                <ChartBarIcon class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                <p class="text-gray-500">Interactive Analytics Chart</p>
                <p class="text-sm text-gray-400 mt-1">Users, Jobs, Applications trends over time</p>
              </div>
            </div>
            
            <!-- Chart Legend -->
            <div class="flex justify-center space-x-6 mt-4">
              <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">New Users</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Job Posts</span>
              </div>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                <span class="text-sm text-gray-600">Applications</span>
              </div>
            </div>
          </div>

          <!-- Recent Activities -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Recent System Activities</h2>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'admin.activities' }"
                tag="router-link"
              >
                View All
                <ArrowRightIcon class="h-4 w-4 ml-1" />
              </BaseButton>
            </div>
            
            <div class="divide-y divide-gray-200">
              <div 
                v-for="activity in recentActivities" 
                :key="activity.id"
                class="p-6 hover:bg-gray-50 transition-colors duration-200"
              >
                <div class="flex items-start space-x-4">
                  <div 
                    :class="[
                      'p-2 rounded-lg',
                      getActivityIconBg(activity.type)
                    ]"
                  >
                    <component 
                      :is="getActivityIcon(activity.type)" 
                      class="h-5 w-5"
                      :class="getActivityIconColor(activity.type)"
                    />
                  </div>
                  
                  <div class="flex-1">
                    <div class="flex items-center justify-between">
                      <h3 class="text-sm font-medium text-gray-900">{{ activity.title }}</h3>
                      <span class="text-xs text-gray-500">{{ formatTime(activity.created_at) }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ activity.description }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                      <span class="text-xs text-gray-500">{{ activity.user }}</span>
                      <span 
                        :class="[
                          'text-xs px-2 py-1 rounded-full',
                          getActivityStatusColor(activity.severity)
                        ]"
                      >
                        {{ activity.severity }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-if="recentActivities.length === 0" class="p-6 text-center">
                <ClockIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
                <p class="text-gray-500">No recent activities</p>
              </div>
            </div>
          </div>

          <!-- User Management Overview -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div>
                <h2 class="text-lg font-semibold text-gray-900">User Management Overview</h2>
                <p class="text-sm text-gray-600 mt-1">Quick overview of user registrations and activities</p>
              </div>
              <BaseButton
                variant="primary"
                :to="{ name: 'admin.users' }"
                tag="router-link"
              >
                Manage Users
                <ArrowRightIcon class="h-4 w-4 ml-1" />
              </BaseButton>
            </div>
            
            <div class="p-6">
              <!-- User Type Distribution -->
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                  <div class="text-2xl font-bold text-blue-600">{{ userDistribution.candidates }}</div>
                  <div class="text-sm text-blue-700">Candidates</div>
                  <div class="text-xs text-blue-600 mt-1">
                    {{ Math.round((userDistribution.candidates / stats.totalUsers) * 100) }}% of total
                  </div>
                </div>
                
                <div class="text-center p-4 bg-green-50 rounded-lg">
                  <div class="text-2xl font-bold text-green-600">{{ userDistribution.employers }}</div>
                  <div class="text-sm text-green-700">Employers</div>
                  <div class="text-xs text-green-600 mt-1">
                    {{ Math.round((userDistribution.employers / stats.totalUsers) * 100) }}% of total
                  </div>
                </div>
                
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                  <div class="text-2xl font-bold text-purple-600">{{ userDistribution.admins }}</div>
                  <div class="text-sm text-purple-700">Administrators</div>
                  <div class="text-xs text-purple-600 mt-1">
                    {{ Math.round((userDistribution.admins / stats.totalUsers) * 100) }}% of total
                  </div>
                </div>
              </div>

              <!-- Recent User Registrations -->
              <div>
                <h3 class="text-sm font-medium text-gray-900 mb-3">Recent Registrations</h3>
                <div class="space-y-3">
                  <div 
                    v-for="user in recentUsers" 
                    :key="user.id"
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                  >
                    <div class="flex items-center space-x-3">
                      <img
                        v-if="user.avatar"
                        :src="user.avatar"
                        :alt="user.name"
                        class="w-8 h-8 rounded-full object-cover"
                      />
                      <div
                        v-else
                        class="w-8 h-8 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center"
                      >
                        <span class="text-white font-medium text-sm">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                      
                      <div>
                        <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                        <p class="text-xs text-gray-500">{{ user.email }}</p>
                      </div>
                    </div>
                    
                    <div class="text-right">
                      <span 
                        :class="[
                          'px-2 py-1 text-xs font-medium rounded-full',
                          getUserRoleColor(user.role)
                        ]"
                      >
                        {{ user.role }}
                      </span>
                      <div class="text-xs text-gray-500 mt-1">{{ formatDate(user.created_at) }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar (Right Column - 1/3) -->
        <div class="space-y-6">
          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'admin.users.create' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <UserPlusIcon class="h-5 w-5 mr-2" />
                Add New User
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'admin.companies' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <BuildingOfficeIcon class="h-5 w-5 mr-2" />
                Manage Companies
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'admin.jobs' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <BriefcaseIcon class="h-5 w-5 mr-2" />
                Review Jobs
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'admin.reports' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <ChartBarIcon class="h-5 w-5 mr-2" />
                Generate Reports
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'admin.settings' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <CogIcon class="h-5 w-5 mr-2" />
                System Settings
              </BaseButton>
            </div>
          </div>

          <!-- System Status -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
              <div 
                :class="[
                  'w-3 h-3 rounded-full',
                  systemHealth.status === 'healthy' ? 'bg-green-500' : 
                  systemHealth.status === 'warning' ? 'bg-yellow-500' : 'bg-red-500'
                ]"
              ></div>
            </div>
            
            <div class="space-y-4">
              <!-- Database -->
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <CircleStackIcon class="h-5 w-5 text-gray-400 mr-2" />
                  <span class="text-sm text-gray-700">Database</span>
                </div>
                <span 
                  :class="[
                    'text-xs px-2 py-1 rounded-full',
                    systemStatus.database === 'operational' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ systemStatus.database }}
                </span>
              </div>

              <!-- Cache -->
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <RocketLaunchIcon class="h-5 w-5 text-gray-400 mr-2" />
                  <span class="text-sm text-gray-700">Cache</span>
                </div>
                <span 
                  :class="[
                    'text-xs px-2 py-1 rounded-full',
                    systemStatus.cache === 'operational' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ systemStatus.cache }}
                </span>
              </div>

              <!-- Storage -->
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <ServerIcon class="h-5 w-5 text-gray-400 mr-2" />
                  <span class="text-sm text-gray-700">Storage</span>
                </div>
                <span 
                  :class="[
                    'text-xs px-2 py-1 rounded-full',
                    systemStatus.storage === 'operational' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ systemStatus.storage }}
                </span>
              </div>

              <!-- Email Service -->
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <EnvelopeIcon class="h-5 w-5 text-gray-400 mr-2" />
                  <span class="text-sm text-gray-700">Email Service</span>
                </div>
                <span 
                  :class="[
                    'text-xs px-2 py-1 rounded-full',
                    systemStatus.email === 'operational' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ systemStatus.email }}
                </span>
              </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-600">Last updated</span>
                <span class="text-gray-900">{{ formatTime(systemStatus.lastUpdated) }}</span>
              </div>
            </div>
          </div>

          <!-- Security Alerts -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Security Alerts</h3>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'admin.security' }"
                tag="router-link"
              >
                View All
              </BaseButton>
            </div>
            
            <div v-if="securityAlerts.length > 0" class="space-y-3">
              <div 
                v-for="alert in securityAlerts" 
                :key="alert.id"
                :class="[
                  'p-3 rounded-lg border-l-4',
                  alert.severity === 'high' ? 'bg-red-50 border-red-400' :
                  alert.severity === 'medium' ? 'bg-yellow-50 border-yellow-400' :
                  'bg-blue-50 border-blue-400'
                ]"
              >
                <div class="flex items-start">
                  <ExclamationTriangleIcon 
                    :class="[
                      'h-4 w-4 mt-0.5 mr-2',
                      alert.severity === 'high' ? 'text-red-400' :
                      alert.severity === 'medium' ? 'text-yellow-400' :
                      'text-blue-400'
                    ]" 
                  />
                  <div class="flex-1">
                    <p 
                      :class="[
                        'text-sm font-medium',
                        alert.severity === 'high' ? 'text-red-800' :
                        alert.severity === 'medium' ? 'text-yellow-800' :
                        'text-blue-800'
                      ]"
                    >
                      {{ alert.title }}
                    </p>
                    <p 
                      :class="[
                        'text-xs mt-1',
                        alert.severity === 'high' ? 'text-red-700' :
                        alert.severity === 'medium' ? 'text-yellow-700' :
                        'text-blue-700'
                      ]"
                    >
                      {{ alert.description }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ formatTime(alert.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-4">
              <ShieldCheckIcon class="h-8 w-8 text-green-500 mx-auto mb-2" />
              <p class="text-sm text-gray-600">No security alerts</p>
              <p class="text-xs text-gray-500">System is secure</p>
            </div>
          </div>

          <!-- Performance Metrics -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Metrics</h3>
            
            <div class="space-y-4">
              <!-- Response Time -->
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-600">Avg Response Time</span>
                  <span class="font-medium">{{ performanceMetrics.responseTime }}ms</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    class="bg-green-500 h-2 rounded-full transition-all duration-300"
                    :style="{ width: Math.min((1000 - performanceMetrics.responseTime) / 10, 100) + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Memory Usage -->
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-600">Memory Usage</span>
                  <span class="font-medium">{{ performanceMetrics.memoryUsage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    :class="[
                      'h-2 rounded-full transition-all duration-300',
                      performanceMetrics.memoryUsage < 70 ? 'bg-green-500' :
                      performanceMetrics.memoryUsage < 85 ? 'bg-yellow-500' : 'bg-red-500'
                    ]"
                    :style="{ width: performanceMetrics.memoryUsage + '%' }"
                  ></div>
                </div>
              </div>

              <!-- CPU Usage -->
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-600">CPU Usage</span>
                  <span class="font-medium">{{ performanceMetrics.cpuUsage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    :class="[
                      'h-2 rounded-full transition-all duration-300',
                      performanceMetrics.cpuUsage < 70 ? 'bg-green-500' :
                      performanceMetrics.cpuUsage < 85 ? 'bg-yellow-500' : 'bg-red-500'
                    ]"
                    :style="{ width: performanceMetrics.cpuUsage + '%' }"
                  ></div>
                </div>
              </div>

              <!-- Disk Usage -->
              <div>
                <div class="flex justify-between text-sm mb-1">
                  <span class="text-gray-600">Disk Usage</span>
                  <span class="font-medium">{{ performanceMetrics.diskUsage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div 
                    :class="[
                      'h-2 rounded-full transition-all duration-300',
                      performanceMetrics.diskUsage < 80 ? 'bg-green-500' :
                      performanceMetrics.diskUsage < 90 ? 'bg-yellow-500' : 'bg-red-500'
                    ]"
                    :style="{ width: performanceMetrics.diskUsage + '%' }"
                  ></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useApiGet } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';

// Icons
import {
  UsersIcon,
  CogIcon,
  BriefcaseIcon,
  DocumentTextIcon,
  ServerIcon,
  ExclamationTriangleIcon,
  ArrowRightIcon,
  ChartBarIcon,
  ClockIcon,
  UserPlusIcon,
  BuildingOfficeIcon,
  CircleStackIcon,
  RocketLaunchIcon,
  EnvelopeIcon,
  ShieldCheckIcon
} from '@heroicons/vue/24/outline';

// State
const selectedTimeframe = ref('30d');

// Computed
const breadcrumbs = computed(() => [
  { label: 'Administration', to: '/admin' },
  { label: 'Dashboard' }
]);

// Mock data - in real app, these would come from API
const stats = ref({
  totalUsers: 15847,
  userGrowth: 12.5,
  activeJobs: 3421,
  jobGrowth: 8.3,
  applicationsToday: 287,
  applicationGrowth: -2.1,
  systemLoad: 68
});

const totalUsers = computed(() => stats.value.totalUsers);
const totalCompanies = computed(() => Math.round(stats.value.totalUsers * 0.15)); // Rough estimate

const systemHealth = ref({
  status: 'healthy', // healthy, warning, critical
  message: 'All systems operational',
  details: 'System is running smoothly with no issues detected.'
});

const systemStatus = ref({
  database: 'operational',
  cache: 'operational', 
  storage: 'operational',
  email: 'operational',
  lastUpdated: new Date().toISOString()
});

const userDistribution = ref({
  candidates: 12678,
  employers: 2384,
  admins: 785
});

const recentActivities = ref([
  {
    id: 1,
    type: 'user_registration',
    title: 'New user registration',
    description: 'Sarah Johnson registered as a candidate',
    user: 'System',
    severity: 'info',
    created_at: new Date(Date.now() - 5 * 60000).toISOString()
  },
  {
    id: 2,
    type: 'job_post',
    title: 'New job posting',
    description: 'TechCorp posted Senior Developer position',
    user: 'TechCorp HR',
    severity: 'info',
    created_at: new Date(Date.now() - 15 * 60000).toISOString()
  },
  {
    id: 3,
    type: 'security',
    title: 'Failed login attempts',
    description: 'Multiple failed login attempts detected',
    user: 'Security System',
    severity: 'warning',
    created_at: new Date(Date.now() - 30 * 60000).toISOString()
  },
  {
    id: 4,
    type: 'system',
    title: 'Database backup completed',
    description: 'Automated backup successfully completed',
    user: 'System',
    severity: 'info',
    created_at: new Date(Date.now() - 60 * 60000).toISOString()
  }
]);

const recentUsers = ref([
  {
    id: 1,
    name: 'Sarah Johnson',
    email: 'sarah.j@example.com',
    role: 'candidate',
    avatar: null,
    created_at: new Date(Date.now() - 2 * 60 * 60000).toISOString()
  },
  {
    id: 2,
    name: 'Michael Chen',
    email: 'm.chen@techcorp.com',
    role: 'employer',
    avatar: null,
    created_at: new Date(Date.now() - 4 * 60 * 60000).toISOString()
  },
  {
    id: 3,
    name: 'Emily Rodriguez',
    email: 'emily.r@startup.io',
    role: 'candidate',
    avatar: null,
    created_at: new Date(Date.now() - 6 * 60 * 60000).toISOString()
  }
]);

const securityAlerts = ref([
  {
    id: 1,
    title: 'Unusual login activity',
    description: 'Login from new location detected',
    severity: 'medium',
    created_at: new Date(Date.now() - 2 * 60 * 60000).toISOString()
  }
]);

const performanceMetrics = ref({
  responseTime: 245,
  memoryUsage: 67,
  cpuUsage: 42,
  diskUsage: 78
});

// Methods
const getLoadColor = (load: number): string => {
  if (load < 70) return 'text-green-600';
  if (load < 85) return 'text-yellow-600';
  return 'text-red-600';
};

const getLoadStatus = (load: number): string => {
  if (load < 70) return 'Normal';
  if (load < 85) return 'High';
  return 'Critical';
};

const getActivityIcon = (type: string) => {
  const icons = {
    user_registration: UsersIcon,
    job_post: BriefcaseIcon,
    security: ExclamationTriangleIcon,
    system: ServerIcon
  };
  return icons[type] || ClockIcon;
};

const getActivityIconBg = (type: string): string => {
  const colors = {
    user_registration: 'bg-blue-100',
    job_post: 'bg-green-100',
    security: 'bg-red-100',
    system: 'bg-gray-100'
  };
  return colors[type] || 'bg-gray-100';
};

const getActivityIconColor = (type: string): string => {
  const colors = {
    user_registration: 'text-blue-600',
    job_post: 'text-green-600',
    security: 'text-red-600',
    system: 'text-gray-600'
  };
  return colors[type] || 'text-gray-600';
};

const getActivityStatusColor = (severity: string): string => {
  const colors = {
    info: 'bg-blue-100 text-blue-800',
    warning: 'bg-yellow-100 text-yellow-800',
    error: 'bg-red-100 text-red-800'
  };
  return colors[severity] || 'bg-gray-100 text-gray-800';
};

const getUserRoleColor = (role: string): string => {
  const colors = {
    candidate: 'bg-blue-100 text-blue-800',
    employer: 'bg-green-100 text-green-800',
    admin: 'bg-purple-100 text-purple-800'
  };
  return colors[role] || 'bg-gray-100 text-gray-800';
};

const formatTime = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMins / 60);
  
  if (diffMins < 1) return 'Just now';
  if (diffMins < 60) return `${diffMins}m ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  return date.toLocaleDateString();
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  });
};

// Load dashboard data
onMounted(async () => {
  try {
    // In real app, load from API
    // const response = await useApiGet('/api/admin/dashboard/stats');
    // stats.value = response.data;
  } catch (error) {
    console.error('Failed to load admin dashboard data:', error);
  }
});
</script>

<style scoped>
/* Progress bar animations */
.progress-bar {
  transition: width 0.8s ease-in-out;
}

/* Activity hover effects */
.activity-item:hover {
  transform: translateX(4px);
}

/* System status indicators */
.status-indicator {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.status-indicator.healthy {
  animation: none;
}

/* Performance metric animations */
.metric-bar {
  transition: all 0.5s ease-in-out;
}

/* Card hover effects */
.dashboard-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Responsive improvements */
@media (max-width: 640px) {
  .dashboard-title {
    font-size: 1.5rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Alert animations */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.alert-enter-active {
  animation: slideIn 0.3s ease-out;
}

/* Chart placeholder styling */
.chart-placeholder {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border: 2px dashed #cbd5e1;
}

/* Security alert styling */
.security-alert {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Performance status colors */
.performance-excellent {
  background: linear-gradient(90deg, #10b981 0%, #059669 100%);
}

.performance-good {
  background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
}

.performance-poor {
  background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
}
</style> 