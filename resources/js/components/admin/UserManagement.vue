<template>
  <div class="bg-white shadow-sm rounded-lg">
    <!-- Header Section -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h2 class="text-xl font-semibold text-gray-900">{{ $t('admin.users.management_title') }}</h2>
          <p class="mt-1 text-sm text-gray-600">{{ $t('admin.users.management_description') }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
          <button
            @click="exportUsers"
            :disabled="isLoading"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <DocumentArrowDownIcon class="h-4 w-4 mr-2" />
            {{ $t('admin.users.export') }}
          </button>
          <button
            @click="openCreateModal"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <PlusIcon class="h-4 w-4 mr-2" />
            {{ $t('admin.users.create_user') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <UsersIcon class="h-8 w-8 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.users.total_users') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.total || 0 }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <UserIcon class="h-8 w-8 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.users.candidates') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.candidates || 0 }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <BuildingOfficeIcon class="h-8 w-8 text-purple-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.users.employers') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.employers || 0 }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <CheckCircleIcon class="h-8 w-8 text-emerald-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.users.verified_users') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.verified || 0 }}</p>
            </div>
          </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-sm">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <ClockIcon class="h-8 w-8 text-orange-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-500">{{ $t('admin.users.online_now') }}</p>
              <p class="text-2xl font-semibold text-gray-900">{{ statistics.online || 0 }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search -->
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
          </div>
          <input
            v-model="filters.search"
            @input="debounceSearch"
            type="text"
            :placeholder="$t('admin.users.search_placeholder')"
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
          />
        </div>

        <!-- Role Filter -->
        <select
          v-model="filters.role"
          @change="applyFilters"
          class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">{{ $t('admin.users.all_roles') }}</option>
          <option value="admin">{{ $t('admin.users.admin') }}</option>
          <option value="candidate">{{ $t('admin.users.candidate') }}</option>
          <option value="employer">{{ $t('admin.users.employer') }}</option>
        </select>

        <!-- Status Filter -->
        <select
          v-model="filters.status"
          @change="applyFilters"
          class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">{{ $t('admin.users.all_statuses') }}</option>
          <option value="active">{{ $t('admin.users.active') }}</option>
          <option value="inactive">{{ $t('admin.users.inactive') }}</option>
          <option value="verified">{{ $t('admin.users.verified') }}</option>
          <option value="unverified">{{ $t('admin.users.unverified') }}</option>
          <option value="online">{{ $t('admin.users.online') }}</option>
        </select>

        <!-- Country Filter -->
        <select
          v-model="filters.country_id"
          @change="applyFilters"
          class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">{{ $t('admin.users.all_countries') }}</option>
          <option 
            v-for="country in countries" 
            :key="country.id" 
            :value="country.id"
          >
            {{ country.name }}
          </option>
        </select>

        <!-- Date Range -->
        <select
          v-model="filters.date_range"
          @change="applyFilters"
          class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
        >
          <option value="">{{ $t('admin.users.all_time') }}</option>
          <option value="today">{{ $t('admin.users.today') }}</option>
          <option value="week">{{ $t('admin.users.this_week') }}</option>
          <option value="month">{{ $t('admin.users.this_month') }}</option>
          <option value="year">{{ $t('admin.users.this_year') }}</option>
        </select>
      </div>

      <!-- Advanced Filters Toggle -->
      <div class="mt-4">
        <button
          @click="showAdvancedFilters = !showAdvancedFilters"
          class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900"
        >
          <AdjustmentsHorizontalIcon class="h-4 w-4 mr-1" />
          {{ $t('admin.users.advanced_filters') }}
          <ChevronDownIcon :class="['h-4 w-4 ml-1 transition-transform', { 'rotate-180': showAdvancedFilters }]" />
        </button>
      </div>

      <!-- Advanced Filters Panel -->
      <div v-if="showAdvancedFilters" class="mt-4 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Age Range -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('admin.users.age_range') }}
            </label>
            <div class="grid grid-cols-2 gap-2">
              <input
                v-model.number="filters.min_age"
                type="number"
                min="18"
                max="100"
                :placeholder="$t('admin.users.min_age')"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
              <input
                v-model.number="filters.max_age"
                type="number"
                min="18"
                max="100"
                :placeholder="$t('admin.users.max_age')"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
              />
            </div>
          </div>

          <!-- Gender Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('admin.users.gender') }}
            </label>
            <select
              v-model="filters.gender"
              class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">{{ $t('admin.users.all_genders') }}</option>
              <option value="male">{{ $t('admin.users.male') }}</option>
              <option value="female">{{ $t('admin.users.female') }}</option>
              <option value="other">{{ $t('admin.users.other') }}</option>
            </select>
          </div>

          <!-- Language Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('admin.users.language') }}
            </label>
            <select
              v-model="filters.language"
              class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">{{ $t('admin.users.all_languages') }}</option>
              <option value="en">{{ $t('admin.users.english') }}</option>
              <option value="es">{{ $t('admin.users.spanish') }}</option>
              <option value="fr">{{ $t('admin.users.french') }}</option>
              <option value="de">{{ $t('admin.users.german') }}</option>
              <option value="ar">{{ $t('admin.users.arabic') }}</option>
            </select>
          </div>

          <!-- Subscription Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('admin.users.subscription_status') }}
            </label>
            <select
              v-model="filters.subscription_status"
              class="block w-full px-3 py-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            >
              <option value="">{{ $t('admin.users.all_subscriptions') }}</option>
              <option value="premium">{{ $t('admin.users.premium') }}</option>
              <option value="free">{{ $t('admin.users.free') }}</option>
              <option value="expired">{{ $t('admin.users.expired') }}</option>
            </select>
          </div>
        </div>

        <!-- Filter Actions -->
        <div class="mt-4 flex justify-end space-x-3">
          <button
            @click="clearFilters"
            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('admin.users.clear_filters') }}
          </button>
          <button
            @click="applyFilters"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('admin.users.apply_filters') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Bulk Actions -->
    <div v-if="selectedUsers.length > 0" class="px-6 py-3 bg-indigo-50 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <span class="text-sm text-indigo-700">
            {{ $t('admin.users.selected_count', { count: selectedUsers.length }) }}
          </span>
        </div>
        <div class="flex space-x-2">
          <button
            @click="bulkAction('activate')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200"
          >
            {{ $t('admin.users.bulk_activate') }}
          </button>
          <button
            @click="bulkAction('deactivate')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200"
          >
            {{ $t('admin.users.bulk_deactivate') }}
          </button>
          <button
            @click="bulkAction('verify')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200"
          >
            {{ $t('admin.users.bulk_verify') }}
          </button>
          <button
            @click="bulkAction('send_notification')"
            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-purple-700 bg-purple-100 hover:bg-purple-200"
          >
            {{ $t('admin.users.bulk_notify') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <input
                type="checkbox"
                @change="toggleAllUsers"
                :checked="allUsersSelected"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button @click="sortBy('name')" class="flex items-center space-x-1 hover:text-gray-700">
                <span>{{ $t('admin.users.user') }}</span>
                <ChevronUpDownIcon class="h-4 w-4" />
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.users.role') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.users.location') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.users.status') }}
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button @click="sortBy('last_login_at')" class="flex items-center space-x-1 hover:text-gray-700">
                <span>{{ $t('admin.users.last_login') }}</span>
                <ChevronUpDownIcon class="h-4 w-4" />
              </button>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button @click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-gray-700">
                <span>{{ $t('admin.users.joined') }}</span>
                <ChevronUpDownIcon class="h-4 w-4" />
              </button>
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              {{ $t('admin.users.actions') }}
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <!-- Loading State -->
          <tr v-if="isLoading" v-for="n in 10" :key="`loading-${n}`" class="animate-pulse">
            <td class="px-6 py-4">
              <div class="h-4 w-4 bg-gray-200 rounded"></div>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center space-x-3">
                <div class="h-10 w-10 bg-gray-200 rounded-full"></div>
                <div class="space-y-1">
                  <div class="h-4 bg-gray-200 rounded w-24"></div>
                  <div class="h-3 bg-gray-200 rounded w-32"></div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="h-6 bg-gray-200 rounded w-16"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-20"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-6 bg-gray-200 rounded w-16"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-16"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-4 bg-gray-200 rounded w-16"></div>
            </td>
            <td class="px-6 py-4">
              <div class="h-8 bg-gray-200 rounded w-24"></div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-else-if="users.length === 0">
            <td colspan="8" class="px-6 py-12 text-center">
              <UsersIcon class="mx-auto h-12 w-12 text-gray-400" />
              <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('admin.users.no_users') }}</h3>
              <p class="mt-1 text-sm text-gray-500">{{ $t('admin.users.no_users_message') }}</p>
              <div class="mt-6">
                <button
                  @click="openCreateModal"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  <PlusIcon class="h-4 w-4 mr-2" />
                  {{ $t('admin.users.create_first_user') }}
                </button>
              </div>
            </td>
          </tr>

          <!-- User Rows -->
          <tr v-else v-for="user in users" :key="user.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <input
                type="checkbox"
                :value="user.id"
                v-model="selectedUsers"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img 
                    v-if="user.avatar" 
                    :src="user.avatar" 
                    :alt="user.full_name"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                  <div v-else class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                    <UserIcon class="h-6 w-6 text-gray-600" />
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">
                    {{ user.full_name }}
                    <span v-if="user.is_online" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      {{ $t('admin.users.online') }}
                    </span>
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ user.email }}
                  </div>
                </div>
              </div>
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="[
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                getRoleColorClass(user.role)
              ]">
                {{ $t(`admin.users.${user.role}`) }}
              </span>
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ user.location || '-' }}
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center space-x-2">
                <span :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                ]">
                  {{ user.is_active ? $t('admin.users.active') : $t('admin.users.inactive') }}
                </span>
                <span v-if="user.is_verified" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ $t('admin.users.verified') }}
                </span>
              </div>
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ user.last_login_at ? formatDate(user.last_login_at) : $t('admin.users.never') }}
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(user.created_at) }}
            </td>
            
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex items-center justify-end space-x-2">
                <button
                  @click="viewUser(user)"
                  class="text-indigo-600 hover:text-indigo-900"
                  :title="$t('admin.users.view_user')"
                >
                  <EyeIcon class="h-4 w-4" />
                </button>
                <button
                  @click="editUser(user)"
                  class="text-gray-600 hover:text-gray-900"
                  :title="$t('admin.users.edit_user')"
                >
                  <PencilIcon class="h-4 w-4" />
                </button>
                <button
                  @click="toggleUserStatus(user)"
                  :class="[
                    'hover:text-gray-900',
                    user.is_active ? 'text-red-600' : 'text-green-600'
                  ]"
                  :title="user.is_active ? $t('admin.users.deactivate') : $t('admin.users.activate')"
                >
                  <component :is="user.is_active ? XMarkIcon : CheckIcon" class="h-4 w-4" />
                </button>
                <button
                  @click="deleteUser(user)"
                  class="text-red-600 hover:text-red-900"
                  :title="$t('admin.users.delete_user')"
                >
                  <TrashIcon class="h-4 w-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="px-6 py-4 border-t border-gray-200">
      <Pagination
        :current-page="currentPage"
        :total-pages="totalPages"
        :per-page="perPage"
        :total="totalUsers"
        @page-changed="changePage"
      />
    </div>

    <!-- Modals -->
    <UserCreateModal
      v-if="showCreateModal"
      @close="showCreateModal = false"
      @created="handleUserCreated"
    />

    <UserEditModal
      v-if="showEditModal"
      :user="selectedUser"
      @close="showEditModal = false"
      @updated="handleUserUpdated"
    />

    <UserViewModal
      v-if="showViewModal"
      :user="selectedUser"
      @close="showViewModal = false"
      @edit="editUser"
    />

    <ConfirmationModal
      v-if="showDeleteModal"
      :title="$t('admin.users.delete_confirmation_title')"
      :message="$t('admin.users.delete_confirmation_message', { name: selectedUser?.full_name })"
      @confirm="confirmDelete"
      @cancel="showDeleteModal = false"
    />

    <BulkNotificationModal
      v-if="showBulkNotificationModal"
      :users="selectedUsers"
      @close="showBulkNotificationModal = false"
      @sent="handleBulkNotificationSent"
    />
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { OptimizedLodash } from '@/utils/dynamicImports'
import {
  UsersIcon,
  UserIcon,
  BuildingOfficeIcon,
  CheckCircleIcon,
  ClockIcon,
  MagnifyingGlassIcon,
  AdjustmentsHorizontalIcon,
  ChevronDownIcon,
  ChevronUpDownIcon,
  PlusIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  CheckIcon,
  XMarkIcon,
  DocumentArrowDownIcon
} from '@heroicons/vue/24/outline'

import Pagination from '@/components/ui/Pagination.vue'
import UserCreateModal from '@/components/admin/modals/UserCreateModal.vue'
import UserEditModal from '@/components/admin/modals/UserEditModal.vue'
import UserViewModal from '@/components/admin/modals/UserViewModal.vue'
import ConfirmationModal from '@/components/ui/ConfirmationModal.vue'
import BulkNotificationModal from '@/components/admin/modals/BulkNotificationModal.vue'

import { usersApi } from '@/services/api'
import { useToast } from '@/composables/useToast'
import { formatDate } from '@/utils/date'

const { t } = useI18n()
const { showToast } = useToast()

// Reactive state
const isLoading = ref(false)
const users = ref([])
const totalUsers = ref(0)
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = ref(15)
const selectedUsers = ref([])
const showAdvancedFilters = ref(false)

// Statistics
const statistics = ref({
  total: 0,
  candidates: 0,
  employers: 0,
  verified: 0,
  online: 0
})

// Filter data
const countries = ref([])

// Filters
const filters = reactive({
  search: '',
  role: '',
  status: '',
  country_id: '',
  date_range: '',
  min_age: null,
  max_age: null,
  gender: '',
  language: '',
  subscription_status: ''
})

// Sorting
const sortField = ref('created_at')
const sortDirection = ref('desc')

// Modal state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showViewModal = ref(false)
const showDeleteModal = ref(false)
const showBulkNotificationModal = ref(false)
const selectedUser = ref(null)

// Computed
const allUsersSelected = computed(() => {
  return users.value.length > 0 && selectedUsers.value.length === users.value.length
})

// Methods
const loadUsers = async () => {
  try {
    isLoading.value = true
    
    const params = {
      page: currentPage.value,
      per_page: perPage.value,
      sort_by: sortField.value,
      sort_direction: sortDirection.value,
      ...filters
    }

    const response = await usersApi.getUsers(params)
    
    users.value = response.data.data
    totalUsers.value = response.data.total
    currentPage.value = response.data.current_page
    totalPages.value = response.data.last_page
    
  } catch (error) {
    showToast(error.response?.data?.message || t('admin.users.error_loading'), 'error')
  } finally {
    isLoading.value = false
  }
}

const loadStatistics = async () => {
  try {
    const response = await usersApi.getStatistics()
    statistics.value = response.data
  } catch (error) {
    console.error('Error loading statistics:', error)
  }
}

const loadCountries = async () => {
  try {
    const response = await usersApi.getCountries()
    countries.value = response.data
  } catch (error) {
    console.error('Error loading countries:', error)
  }
}

const applyFilters = () => {
  currentPage.value = 1
  loadUsers()
}

const debounceSearch = OptimizedLodash.debounce(() => {
  applyFilters()
}, 500)

const clearFilters = () => {
  Object.assign(filters, {
    search: '',
    role: '',
    status: '',
    country_id: '',
    date_range: '',
    min_age: null,
    max_age: null,
    gender: '',
    language: '',
    subscription_status: ''
  })
  applyFilters()
}

const sortBy = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortField.value = field
    sortDirection.value = 'asc'
  }
  loadUsers()
}

const changePage = (page) => {
  currentPage.value = page
  loadUsers()
}

const toggleAllUsers = () => {
  if (allUsersSelected.value) {
    selectedUsers.value = []
  } else {
    selectedUsers.value = users.value.map(user => user.id)
  }
}

const openCreateModal = () => {
  showCreateModal.value = true
}

const viewUser = (user) => {
  selectedUser.value = user
  showViewModal.value = true
}

const editUser = (user) => {
  selectedUser.value = user
  showEditModal.value = true
}

const deleteUser = (user) => {
  selectedUser.value = user
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  try {
    await usersApi.deleteUser(selectedUser.value.id)
    showToast(t('admin.users.user_deleted'), 'success')
    showDeleteModal.value = false
    selectedUser.value = null
    await loadUsers()
    await loadStatistics()
  } catch (error) {
    showToast(error.response?.data?.message || t('admin.users.error_deleting'), 'error')
  }
}

const toggleUserStatus = async (user) => {
  try {
    const action = user.is_active ? 'deactivate' : 'activate'
    await usersApi.updateUserStatus(user.id, { is_active: !user.is_active })
    
    user.is_active = !user.is_active
    showToast(t(`admin.users.user_${action}d`), 'success')
    await loadStatistics()
  } catch (error) {
    showToast(error.response?.data?.message || t('admin.users.error_updating_status'), 'error')
  }
}

const bulkAction = async (action) => {
  if (selectedUsers.value.length === 0) return

  try {
    switch (action) {
      case 'activate':
      case 'deactivate':
        await usersApi.bulkUpdateStatus(selectedUsers.value, { is_active: action === 'activate' })
        showToast(t(`admin.users.bulk_${action}d`), 'success')
        break
      case 'verify':
        await usersApi.bulkVerify(selectedUsers.value)
        showToast(t('admin.users.bulk_verified'), 'success')
        break
      case 'send_notification':
        showBulkNotificationModal.value = true
        return
    }
    
    selectedUsers.value = []
    await loadUsers()
    await loadStatistics()
  } catch (error) {
    showToast(error.response?.data?.message || t('admin.users.error_bulk_action'), 'error')
  }
}

const exportUsers = async () => {
  try {
    const response = await usersApi.exportUsers(filters)
    
    // Create download link
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `users-${new Date().toISOString().split('T')[0]}.xlsx`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    
    showToast(t('admin.users.export_success'), 'success')
  } catch (error) {
    showToast(error.response?.data?.message || t('admin.users.error_exporting'), 'error')
  }
}

const handleUserCreated = (user) => {
  showCreateModal.value = false
  showToast(t('admin.users.user_created'), 'success')
  loadUsers()
  loadStatistics()
}

const handleUserUpdated = (user) => {
  showEditModal.value = false
  selectedUser.value = null
  showToast(t('admin.users.user_updated'), 'success')
  loadUsers()
  loadStatistics()
}

const handleBulkNotificationSent = () => {
  showBulkNotificationModal.value = false
  selectedUsers.value = []
  showToast(t('admin.users.bulk_notification_sent'), 'success')
}

const getRoleColorClass = (role) => {
  const colors = {
    admin: 'bg-red-100 text-red-800',
    candidate: 'bg-blue-100 text-blue-800',
    employer: 'bg-purple-100 text-purple-800'
  }
  return colors[role] || 'bg-gray-100 text-gray-800'
}

// Lifecycle
onMounted(async () => {
  await Promise.all([
    loadUsers(),
    loadStatistics(),
    loadCountries()
  ])
})
</script> 