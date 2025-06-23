<template>
  <div class="required-degree-level-management">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">
            {{ $t('required_degree_levels.management.title') }}
          </h1>
          <p class="mt-1 text-sm text-gray-600">
            {{ $t('required_degree_levels.management.description') }}
          </p>
        </div>
        <div class="flex items-center space-x-3">
          <Button
            @click="refreshData"
            variant="secondary"
            size="sm"
            :loading="loading"
            :disabled="loading"
          >
            <RefreshIcon class="w-4 h-4 mr-2" />
            {{ $t('common.refresh') }}
          </Button>
          <Button
            @click="openCreateModal"
            variant="primary"
            size="sm"
            v-if="can('create', 'RequiredDegreeLevel')"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            {{ $t('required_degree_levels.actions.create') }}
          </Button>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gray-50 border-b border-gray-200 px-6 py-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.search') }}
          </label>
          <div class="relative">
            <input
              v-model="filters.search"
              type="text"
              :placeholder="$t('required_degree_levels.search_placeholder')"
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              @input="debouncedSearch"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <SearchIcon class="h-5 w-5 text-gray-400" />
            </div>
          </div>
        </div>

        <!-- Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.status') }}
          </label>
          <select
            v-model="filters.active"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="">{{ $t('common.all_statuses') }}</option>
            <option value="true">{{ $t('common.active') }}</option>
            <option value="false">{{ $t('common.inactive') }}</option>
          </select>
        </div>

        <!-- Type Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.type') }}
          </label>
          <select
            v-model="filters.default"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="">{{ $t('common.all_types') }}</option>
            <option value="true">{{ $t('common.default') }}</option>
            <option value="false">{{ $t('common.custom') }}</option>
          </select>
        </div>

        <!-- Sort By -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ $t('common.sort_by') }}
          </label>
          <select
            v-model="filters.sort_by"
            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            @change="applyFilters"
          >
            <option value="name">{{ $t('required_degree_levels.attributes.name') }}</option>
            <option value="alphabetical">{{ $t('common.alphabetical') }}</option>
            <option value="created_at">{{ $t('common.created_at') }}</option>
            <option value="popular">{{ $t('common.popular') }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <AcademicCapIcon class="h-8 w-8 text-indigo-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $t('required_degree_levels.stats.total') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ statistics.total || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <CheckCircleIcon class="h-8 w-8 text-green-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $t('required_degree_levels.stats.active') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ statistics.active || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <StarIcon class="h-8 w-8 text-yellow-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $t('required_degree_levels.stats.default') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ statistics.default || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <BriefcaseIcon class="h-8 w-8 text-blue-600" />
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">
                  {{ $t('required_degree_levels.stats.with_jobs') }}
                </dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ statistics.with_jobs || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md mx-6 mb-6">
      <div class="px-4 py-5 sm:p-6">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
          <span class="ml-3 text-sm text-gray-600">{{ $t('common.loading') }}</span>
        </div>

        <!-- Empty State -->
        <div v-else-if="!requiredDegreeLevels.length" class="text-center py-12">
          <AcademicCapIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            {{ $t('required_degree_levels.empty.title') }}
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            {{ $t('required_degree_levels.empty.description') }}
          </p>
          <div class="mt-6">
            <Button
              @click="openCreateModal"
              variant="primary"
              v-if="can('create', 'RequiredDegreeLevel')"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              {{ $t('required_degree_levels.actions.create') }}
            </Button>
          </div>
        </div>

        <!-- Data Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ $t('required_degree_levels.attributes.name') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ $t('required_degree_levels.attributes.description') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ $t('common.status') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ $t('common.type') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ $t('required_degree_levels.attributes.jobs_count') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  {{ $t('common.created_at') }}
                </th>
                <th scope="col" class="relative px-6 py-3">
                  <span class="sr-only">{{ $t('common.actions') }}</span>
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="level in requiredDegreeLevels" :key="level.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div>
                      <div class="text-sm font-medium text-gray-900">
                        {{ level.name }}
                      </div>
                      <div v-if="level.display_name" class="text-sm text-gray-500">
                        {{ level.display_name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900 max-w-xs truncate">
                    {{ level.description || $t('common.no_description') }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    level.is_active 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]">
                    {{ level.status_label }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    level.is_default 
                      ? 'bg-blue-100 text-blue-800' 
                      : 'bg-gray-100 text-gray-800'
                  ]">
                    {{ level.type_label }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ level.jobs_count || 0 }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(level.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <Button
                      @click="viewLevel(level)"
                      variant="secondary"
                      size="xs"
                      v-if="can('view', level)"
                    >
                      <EyeIcon class="w-4 h-4" />
                    </Button>
                    <Button
                      @click="editLevel(level)"
                      variant="secondary"
                      size="xs"
                      v-if="can('update', level)"
                    >
                      <PencilIcon class="w-4 h-4" />
                    </Button>
                    <Button
                      @click="deleteLevel(level)"
                      variant="danger"
                      size="xs"
                      v-if="can('delete', level)"
                      :disabled="level.jobs_count > 0"
                    >
                      <TrashIcon class="w-4 h-4" />
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.total > pagination.per_page" class="mt-6">
          <Pagination
            :current-page="pagination.current_page"
            :last-page="pagination.last_page"
            :per-page="pagination.per_page"
            :total="pagination.total"
            @page-changed="changePage"
          />
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <Modal
      :show="showModal"
      :title="modalTitle"
      @close="closeModal"
      size="md"
    >
      <RequiredDegreeLevelForm
        :level="selectedLevel"
        :loading="formLoading"
        @submit="handleFormSubmit"
        @cancel="closeModal"
      />
    </Modal>

    <!-- View Modal -->
    <Modal
      :show="showViewModal"
      :title="$t('required_degree_levels.actions.view')"
      @close="closeViewModal"
      size="lg"
    >
      <RequiredDegreeLevelDetails
        v-if="selectedLevel"
        :level="selectedLevel"
        @edit="editLevel"
        @close="closeViewModal"
      />
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { debounce } from 'lodash-es'
import { 
  PlusIcon, 
  RefreshIcon, 
  SearchIcon, 
  EyeIcon, 
  PencilIcon, 
  TrashIcon,
  AcademicCapIcon,
  CheckCircleIcon,
  StarIcon,
  BriefcaseIcon
} from '@heroicons/vue/24/outline'

// Components
import Button from '@/components/ui/Button.vue'
import Modal from '@/components/ui/Modal.vue'
import Pagination from '@/components/ui/Pagination.vue'
import RequiredDegreeLevelForm from './RequiredDegreeLevelForm.vue'
import RequiredDegreeLevelDetails from './RequiredDegreeLevelDetails.vue'

// Composables
import { usePermissions } from '@/composables/usePermissions'
import { useNotifications } from '@/composables/useNotifications'
import { useRequiredDegreeLevels } from '@/composables/useRequiredDegreeLevels'

const { t } = useI18n()
const { can } = usePermissions()
const { showSuccess, showError, showConfirm } = useNotifications()
const { 
  requiredDegreeLevels, 
  loading, 
  pagination, 
  statistics,
  fetchRequiredDegreeLevels,
  createRequiredDegreeLevel,
  updateRequiredDegreeLevel,
  deleteRequiredDegreeLevel
} = useRequiredDegreeLevels()

// State
const showModal = ref(false)
const showViewModal = ref(false)
const selectedLevel = ref(null)
const formLoading = ref(false)

const filters = reactive({
  search: '',
  active: '',
  default: '',
  sort_by: 'name',
  sort_direction: 'asc'
})

// Computed
const modalTitle = computed(() => {
  return selectedLevel.value?.id 
    ? t('required_degree_levels.actions.edit')
    : t('required_degree_levels.actions.create')
})

// Methods
const refreshData = () => {
  fetchRequiredDegreeLevels(filters)
}

const applyFilters = () => {
  fetchRequiredDegreeLevels(filters)
}

const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

const changePage = (page: number) => {
  fetchRequiredDegreeLevels({ ...filters, page })
}

const openCreateModal = () => {
  selectedLevel.value = null
  showModal.value = true
}

const editLevel = (level: any) => {
  selectedLevel.value = level
  showModal.value = true
}

const viewLevel = (level: any) => {
  selectedLevel.value = level
  showViewModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedLevel.value = null
  formLoading.value = false
}

const closeViewModal = () => {
  showViewModal.value = false
  selectedLevel.value = null
}

const handleFormSubmit = async (data: any) => {
  formLoading.value = true
  
  try {
    if (selectedLevel.value?.id) {
      await updateRequiredDegreeLevel(selectedLevel.value.id, data)
      showSuccess(t('required_degree_levels.messages.updated'))
    } else {
      await createRequiredDegreeLevel(data)
      showSuccess(t('required_degree_levels.messages.created'))
    }
    
    closeModal()
    refreshData()
  } catch (error) {
    showError(error.message || t('common.something_went_wrong'))
  } finally {
    formLoading.value = false
  }
}

const deleteLevel = async (level: any) => {
  const confirmed = await showConfirm(
    t('required_degree_levels.confirm.delete.title'),
    t('required_degree_levels.confirm.delete.message', { name: level.name })
  )
  
  if (confirmed) {
    try {
      await deleteRequiredDegreeLevel(level.id)
      showSuccess(t('required_degree_levels.messages.deleted'))
      refreshData()
    } catch (error) {
      showError(error.message || t('common.something_went_wrong'))
    }
  }
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString()
}

// Lifecycle
onMounted(() => {
  refreshData()
})
</script>

<style scoped>
.required-degree-level-management {
  @apply min-h-screen bg-gray-100;
}
</style> 