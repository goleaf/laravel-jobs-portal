<template>
  <MainLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="bg-white px-6 py-8">
        <div class="max-w-7xl mx-auto">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                User Management 👥
              </h1>
              <p class="text-gray-600 text-lg">
                Manage {{ filteredUsers.length }} of {{ totalUsers }} platform users across all roles
              </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
              <BaseButton
                variant="outline-primary"
                @click="exportUsers"
                :disabled="isExporting"
              >
                <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
                {{ isExporting ? 'Exporting...' : 'Export Users' }}
              </BaseButton>
              
              <BaseButton
                variant="primary"
                @click="showCreateUserModal = true"
              >
                <UserPlusIcon class="h-4 w-4 mr-2" />
                Add New User
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- User Statistics Overview -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <UsersIcon class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-2xl font-bold text-gray-900">{{ userStats.total.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">All registered users</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <UserGroupIcon class="h-6 w-6 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Candidates</p>
              <p class="text-2xl font-bold text-gray-900">{{ userStats.candidates.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">{{ Math.round((userStats.candidates / userStats.total) * 100) }}% of total</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <BuildingOfficeIcon class="h-6 w-6 text-purple-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Employers</p>
              <p class="text-2xl font-bold text-gray-900">{{ userStats.employers.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">{{ Math.round((userStats.employers / userStats.total) * 100) }}% of total</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-orange-100 rounded-lg">
              <ShieldCheckIcon class="h-6 w-6 text-orange-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Administrators</p>
              <p class="text-2xl font-bold text-gray-900">{{ userStats.admins.toLocaleString() }}</p>
              <p class="text-xs text-gray-500">{{ Math.round((userStats.admins / userStats.total) * 100) }}% of total</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters and Search -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
          <!-- Search -->
          <div class="lg:col-span-2">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
              Search Users
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
              </div>
              <input
                id="search"
                v-model="filters.search"
                type="text"
                placeholder="Search by name, email, or company..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
            </div>
          </div>

          <!-- Role Filter -->
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
              Role
            </label>
            <select
              id="role"
              v-model="filters.role"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">All Roles</option>
              <option value="candidate">Candidates</option>
              <option value="employer">Employers</option>
              <option value="admin">Administrators</option>
            </select>
          </div>

          <!-- Status Filter -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
              Status
            </label>
            <select
              id="status"
              v-model="filters.status"
              class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
              <option value="suspended">Suspended</option>
              <option value="pending">Pending Verification</option>
            </select>
          </div>
        </div>

        <!-- Active Filters Display -->
        <div v-if="hasActiveFilters" class="flex flex-wrap gap-2 mb-4">
          <span class="text-sm text-gray-600 mr-2">Active filters:</span>
          
          <span v-if="filters.search" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
            Search: "{{ filters.search }}"
            <button @click="filters.search = ''" class="ml-1 text-indigo-600 hover:text-indigo-800">
              <XMarkIcon class="h-3 w-3" />
            </button>
          </span>
          
          <span v-if="filters.role" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            Role: {{ filters.role }}
            <button @click="filters.role = ''" class="ml-1 text-green-600 hover:text-green-800">
              <XMarkIcon class="h-3 w-3" />
            </button>
          </span>
          
          <span v-if="filters.status" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
            Status: {{ filters.status }}
            <button @click="filters.status = ''" class="ml-1 text-yellow-600 hover:text-yellow-800">
              <XMarkIcon class="h-3 w-3" />
            </button>
          </span>
          
          <button
            @click="clearFilters"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200"
          >
            Clear all
            <XMarkIcon class="h-3 w-3 ml-1" />
          </button>
        </div>

        <!-- Bulk Actions -->
        <div v-if="selectedUsers.length > 0" class="flex items-center justify-between p-4 bg-indigo-50 rounded-lg">
          <div class="flex items-center">
            <span class="text-sm font-medium text-indigo-800">
              {{ selectedUsers.length }} user{{ selectedUsers.length === 1 ? '' : 's' }} selected
            </span>
          </div>
          
          <div class="flex space-x-2">
            <BaseButton
              variant="outline-primary"
              size="sm"
              @click="bulkExport"
              :disabled="isBulkActionInProgress"
            >
              <DocumentArrowDownIcon class="h-4 w-4 mr-1" />
              Export Selected
            </BaseButton>
            
            <BaseButton
              variant="outline-danger"
              size="sm"
              @click="showBulkSuspendModal = true"
              :disabled="isBulkActionInProgress"
            >
              <NoSymbolIcon class="h-4 w-4 mr-1" />
              Suspend Selected
            </BaseButton>
            
            <BaseButton
              variant="outline-success"
              size="sm"
              @click="bulkActivate"
              :disabled="isBulkActionInProgress"
            >
              <CheckCircleIcon class="h-4 w-4 mr-1" />
              Activate Selected
            </BaseButton>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">
              Users List ({{ filteredUsers.length }})
            </h2>
            
            <div class="flex items-center space-x-2">
              <!-- View Toggle -->
              <div class="flex bg-gray-100 rounded-lg p-1">
                <button
                  @click="viewMode = 'table'"
                  :class="[
                    'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                    viewMode === 'table'
                      ? 'bg-white text-gray-900 shadow-sm'
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                >
                  <TableCellsIcon class="h-4 w-4" />
                </button>
                <button
                  @click="viewMode = 'grid'"
                  :class="[
                    'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                    viewMode === 'grid'
                      ? 'bg-white text-gray-900 shadow-sm'
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                >
                  <Squares2X2Icon class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="relative px-6 py-3">
                  <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @change="toggleSelectAll"
                    class="absolute left-4 top-1/2 -mt-2 h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  User
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Role
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Joined
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Last Active
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="user in paginatedUsers"
                :key="user.id"
                :class="[
                  'hover:bg-gray-50 transition-colors duration-200',
                  selectedUsers.includes(user.id) ? 'bg-indigo-50' : ''
                ]"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <input
                    type="checkbox"
                    :checked="selectedUsers.includes(user.id)"
                    @change="toggleUserSelection(user.id)"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                  />
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <img
                      v-if="user.avatar"
                      :src="user.avatar"
                      :alt="user.name"
                      class="h-10 w-10 rounded-full object-cover"
                    />
                    <div
                      v-else
                      class="h-10 w-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center"
                    >
                      <span class="text-white font-medium">
                        {{ user.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-sm text-gray-500">{{ user.email }}</div>
                      <div v-if="user.company" class="text-xs text-gray-400">{{ user.company }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      getRoleColor(user.role)
                    ]"
                  >
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      getStatusColor(user.status)
                    ]"
                  >
                    {{ user.status }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatTime(user.last_login_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <BaseButton
                      variant="ghost"
                      size="sm"
                      @click="viewUser(user)"
                    >
                      <EyeIcon class="h-4 w-4" />
                    </BaseButton>
                    
                    <BaseButton
                      variant="ghost"
                      size="sm"
                      @click="editUser(user)"
                    >
                      <PencilIcon class="h-4 w-4" />
                    </BaseButton>
                    
                    <BaseButton
                      variant="ghost"
                      size="sm"
                      @click="showUserActionsMenu(user, $event)"
                      class="text-gray-400 hover:text-gray-600"
                    >
                      <EllipsisVerticalIcon class="h-4 w-4" />
                    </BaseButton>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Grid View -->
        <div v-else class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="user in paginatedUsers"
              :key="user.id"
              :class="[
                'bg-white border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200',
                selectedUsers.includes(user.id) ? 'border-indigo-300 shadow-md' : ''
              ]"
            >
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                  <input
                    type="checkbox"
                    :checked="selectedUsers.includes(user.id)"
                    @change="toggleUserSelection(user.id)"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3"
                  />
                  
                  <img
                    v-if="user.avatar"
                    :src="user.avatar"
                    :alt="user.name"
                    class="h-12 w-12 rounded-full object-cover"
                  />
                  <div
                    v-else
                    class="h-12 w-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center"
                  >
                    <span class="text-white font-medium text-lg">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </div>
                
                <BaseButton
                  variant="ghost"
                  size="sm"
                  @click="showUserActionsMenu(user, $event)"
                  class="text-gray-400 hover:text-gray-600"
                >
                  <EllipsisVerticalIcon class="h-4 w-4" />
                </BaseButton>
              </div>
              
              <div class="mb-4">
                <h3 class="text-lg font-medium text-gray-900 mb-1">{{ user.name }}</h3>
                <p class="text-sm text-gray-500 mb-2">{{ user.email }}</p>
                <p v-if="user.company" class="text-sm text-gray-400">{{ user.company }}</p>
              </div>
              
              <div class="flex items-center justify-between mb-4">
                <span 
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    getRoleColor(user.role)
                  ]"
                >
                  {{ user.role }}
                </span>
                
                <span 
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    getStatusColor(user.status)
                  ]"
                >
                  {{ user.status }}
                </span>
              </div>
              
              <div class="text-xs text-gray-500 mb-4">
                <p>Joined: {{ formatDate(user.created_at) }}</p>
                <p>Last active: {{ formatTime(user.last_login_at) }}</p>
              </div>
              
              <div class="flex space-x-2">
                <BaseButton
                  variant="outline-primary"
                  size="sm"
                  @click="viewUser(user)"
                  class="flex-1"
                >
                  <EyeIcon class="h-4 w-4 mr-1" />
                  View
                </BaseButton>
                
                <BaseButton
                  variant="outline-primary"
                  size="sm"
                  @click="editUser(user)"
                  class="flex-1"
                >
                  <PencilIcon class="h-4 w-4 mr-1" />
                  Edit
                </BaseButton>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to 
              {{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }} of 
              {{ filteredUsers.length }} results
            </div>
            
            <div class="flex items-center space-x-2">
              <BaseButton
                variant="outline-primary"
                size="sm"
                @click="currentPage--"
                :disabled="currentPage === 1"
              >
                <ChevronLeftIcon class="h-4 w-4" />
                Previous
              </BaseButton>
              
              <span class="text-sm text-gray-700">
                Page {{ currentPage }} of {{ totalPages }}
              </span>
              
              <BaseButton
                variant="outline-primary"
                size="sm"
                @click="currentPage++"
                :disabled="currentPage === totalPages"
              >
                Next
                <ChevronRightIcon class="h-4 w-4" />
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <div v-if="isLoading" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="flex items-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          <div class="ml-4">
            <div class="text-lg font-medium text-gray-900">Loading...</div>
            <div class="text-sm text-gray-500">{{ loadingMessage }}</div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useApiGet } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';

// Icons
import {
  UsersIcon,
  UserGroupIcon,
  BuildingOfficeIcon,
  ShieldCheckIcon,
  UserPlusIcon,
  DocumentArrowDownIcon,
  MagnifyingGlassIcon,
  XMarkIcon,
  NoSymbolIcon,
  CheckCircleIcon,
  TableCellsIcon,
  Squares2X2Icon,
  EyeIcon,
  PencilIcon,
  EllipsisVerticalIcon,
  ChevronLeftIcon,
  ChevronRightIcon
} from '@heroicons/vue/24/outline';

// Types
interface User {
  id: number;
  name: string;
  email: string;
  role: 'candidate' | 'employer' | 'admin';
  status: 'active' | 'inactive' | 'suspended' | 'pending';
  avatar?: string;
  company?: string;
  created_at: string;
  last_login_at?: string;
  email_verified_at?: string;
}

interface Filters {
  search: string;
  role: string;
  status: string;
}

// State
const isLoading = ref(false);
const loadingMessage = ref('');
const viewMode = ref<'table' | 'grid'>('table');
const currentPage = ref(1);
const itemsPerPage = ref(20);
const selectedUsers = ref<number[]>([]);
const showCreateUserModal = ref(false);
const showBulkSuspendModal = ref(false);
const isBulkActionInProgress = ref(false);
const isExporting = ref(false);

const filters = ref<Filters>({
  search: '',
  role: '',
  status: ''
});

// Computed
const breadcrumbs = computed(() => [
  { label: 'Administration', to: '/admin' },
  { label: 'User Management' }
]);

// Mock data - in real app, this would come from API
const users = ref<User[]>([
  {
    id: 1,
    name: 'Sarah Johnson',
    email: 'sarah.johnson@example.com',
    role: 'candidate',
    status: 'active',
    avatar: null,
    created_at: '2024-01-15T08:30:00Z',
    last_login_at: '2024-02-10T14:22:00Z',
    email_verified_at: '2024-01-15T09:15:00Z'
  },
  {
    id: 2,
    name: 'Michael Chen',
    email: 'm.chen@techcorp.com',
    role: 'employer',
    status: 'active',
    avatar: null,
    company: 'TechCorp Solutions',
    created_at: '2024-01-20T10:15:00Z',
    last_login_at: '2024-02-09T16:45:00Z',
    email_verified_at: '2024-01-20T11:00:00Z'
  },
  {
    id: 3,
    name: 'Emily Rodriguez',
    email: 'emily.r@startup.io',
    role: 'candidate',
    status: 'pending',
    avatar: null,
    created_at: '2024-02-05T12:20:00Z',
    last_login_at: '2024-02-08T09:30:00Z'
  },
  {
    id: 4,
    name: 'David Thompson',
    email: 'david.thompson@admin.com',
    role: 'admin',
    status: 'active',
    avatar: null,
    created_at: '2023-12-01T08:00:00Z',
    last_login_at: '2024-02-10T18:00:00Z',
    email_verified_at: '2023-12-01T08:30:00Z'
  },
  {
    id: 5,
    name: 'Lisa Wang',
    email: 'lisa.wang@innovate.com',
    role: 'employer',
    status: 'suspended',
    avatar: null,
    company: 'Innovate Labs',
    created_at: '2024-01-10T14:45:00Z',
    last_login_at: '2024-01-25T11:15:00Z',
    email_verified_at: '2024-01-10T15:30:00Z'
  }
]);

const userStats = computed(() => {
  const total = users.value.length;
  const candidates = users.value.filter(u => u.role === 'candidate').length;
  const employers = users.value.filter(u => u.role === 'employer').length;
  const admins = users.value.filter(u => u.role === 'admin').length;
  
  return {
    total,
    candidates,
    employers,
    admins
  };
});

const filteredUsers = computed(() => {
  let filtered = users.value;
  
  // Search filter
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    filtered = filtered.filter(user => 
      user.name.toLowerCase().includes(search) ||
      user.email.toLowerCase().includes(search) ||
      (user.company && user.company.toLowerCase().includes(search))
    );
  }
  
  // Role filter
  if (filters.value.role) {
    filtered = filtered.filter(user => user.role === filters.value.role);
  }
  
  // Status filter
  if (filters.value.status) {
    filtered = filtered.filter(user => user.status === filters.value.status);
  }
  
  return filtered;
});

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredUsers.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / itemsPerPage.value);
});

const totalUsers = computed(() => users.value.length);

const hasActiveFilters = computed(() => {
  return !!(filters.value.search || filters.value.role || filters.value.status);
});

const isAllSelected = computed(() => {
  return paginatedUsers.value.length > 0 && 
         paginatedUsers.value.every(user => selectedUsers.value.includes(user.id));
});

// Methods
const getRoleColor = (role: string): string => {
  const colors = {
    candidate: 'bg-blue-100 text-blue-800',
    employer: 'bg-green-100 text-green-800',
    admin: 'bg-purple-100 text-purple-800'
  };
  return colors[role] || 'bg-gray-100 text-gray-800';
};

const getStatusColor = (status: string): string => {
  const colors = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-gray-100 text-gray-800',
    suspended: 'bg-red-100 text-red-800',
    pending: 'bg-yellow-100 text-yellow-800'
  };
  return colors[status] || 'bg-gray-100 text-gray-800';
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const formatTime = (dateString?: string): string => {
  if (!dateString) return 'Never';
  
  const date = new Date(dateString);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMins / 60);
  const diffDays = Math.floor(diffHours / 24);
  
  if (diffMins < 1) return 'Just now';
  if (diffMins < 60) return `${diffMins}m ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  if (diffDays < 7) return `${diffDays}d ago`;
  return date.toLocaleDateString();
};

const clearFilters = (): void => {
  filters.value = {
    search: '',
    role: '',
    status: ''
  };
  currentPage.value = 1;
};

const toggleUserSelection = (userId: number): void => {
  const index = selectedUsers.value.indexOf(userId);
  if (index === -1) {
    selectedUsers.value.push(userId);
  } else {
    selectedUsers.value.splice(index, 1);
  }
};

const toggleSelectAll = (): void => {
  if (isAllSelected.value) {
    // Deselect all current page users
    paginatedUsers.value.forEach(user => {
      const index = selectedUsers.value.indexOf(user.id);
      if (index !== -1) {
        selectedUsers.value.splice(index, 1);
      }
    });
  } else {
    // Select all current page users
    paginatedUsers.value.forEach(user => {
      if (!selectedUsers.value.includes(user.id)) {
        selectedUsers.value.push(user.id);
      }
    });
  }
};

const viewUser = (user: User): void => {
  console.log('View user:', user);
};

const editUser = (user: User): void => {
  console.log('Edit user:', user);
};

const showUserActionsMenu = (user: User, event: Event): void => {
  console.log('Show actions for user:', user);
};

const exportUsers = async (): Promise<void> => {
  isExporting.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 2000));
    console.log('Exporting all users...');
  } catch (error) {
    console.error('Export failed:', error);
  } finally {
    isExporting.value = false;
  }
};

const bulkExport = async (): Promise<void> => {
  isBulkActionInProgress.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 1500));
    console.log('Exporting selected users:', selectedUsers.value);
  } catch (error) {
    console.error('Bulk export failed:', error);
  } finally {
    isBulkActionInProgress.value = false;
  }
};

const bulkActivate = async (): Promise<void> => {
  isBulkActionInProgress.value = true;
  try {
    await new Promise(resolve => setTimeout(resolve, 1000));
    console.log('Activating users:', selectedUsers.value);
    selectedUsers.value = [];
  } catch (error) {
    console.error('Bulk activation failed:', error);
  } finally {
    isBulkActionInProgress.value = false;
  }
};

// Watch for filter changes to reset pagination
watch(filters, () => {
  currentPage.value = 1;
}, { deep: true });

// Load users data
onMounted(async () => {
  try {
    isLoading.value = true;
    loadingMessage.value = 'Loading users...';
    
    await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate loading
  } catch (error) {
    console.error('Failed to load users:', error);
  } finally {
    isLoading.value = false;
  }
});
</script>

<style scoped>
/* User card hover effects */
.user-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Selection animations */
.user-selected {
  animation: pulseSelection 0.3s ease-in-out;
}

@keyframes pulseSelection {
  0% { background-color: rgb(255, 255, 255); }
  50% { background-color: rgb(238, 242, 255); }
  100% { background-color: rgb(238, 242, 255); }
}

/* Filter animations */
.filter-badge {
  animation: slideInFromTop 0.2s ease-out;
}

@keyframes slideInFromTop {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Table row hover */
.table-row:hover {
  background-color: rgb(249, 250, 251);
}

/* Loading animation */
.loading-overlay {
  backdrop-filter: blur(4px);
}

/* Responsive improvements */
@media (max-width: 640px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .filter-grid {
    grid-template-columns: 1fr;
  }
  
  .user-grid {
    grid-template-columns: 1fr;
  }
}

/* Smooth transitions */
.transition-all {
  transition: all 0.2s ease-in-out;
}

/* Status indicator pulse */
.status-active {
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}
</style> 