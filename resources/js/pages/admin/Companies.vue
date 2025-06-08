<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="px-4 sm:px-6 lg:px-8">
        <div class="py-6 flex justify-between items-center">
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Manage Companies</h1>
            <p class="mt-1 text-sm text-gray-500">{{ pagination.total }} companies total</p>
          </div>
          <Button @click="showCreateModal = true" variant="primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Company
          </Button>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="px-4 sm:px-6 lg:px-8 py-6">
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <Input
            v-model="searchQuery"
            placeholder="Search companies..."
            @input="debouncedSearch"
          />
          <select
            v-model="industryFilter"
            @change="handleFilterChange"
            class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Industries</option>
            <option value="technology">Technology</option>
            <option value="healthcare">Healthcare</option>
            <option value="finance">Finance</option>
            <option value="education">Education</option>
            <option value="retail">Retail</option>
          </select>
          <select
            v-model="locationFilter"
            @change="handleFilterChange"
            class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Locations</option>
            <option value="remote">Remote</option>
            <option value="new-york">New York</option>
            <option value="san-francisco">San Francisco</option>
            <option value="london">London</option>
            <option value="berlin">Berlin</option>
          </select>
          <Button @click="clearFilters" variant="outline">
            Clear Filters
          </Button>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="companiesStore.error" class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
        <div class="flex">
          <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <div>
            <h3 class="text-sm font-medium text-red-800">Error</h3>
            <p class="text-sm text-red-700">{{ companiesStore.error }}</p>
          </div>
          <button @click="companiesStore.clearError" class="ml-auto text-red-400 hover:text-red-600">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="companiesStore.loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>

      <!-- Companies Table -->
      <div v-else class="bg-white shadow rounded-lg overflow-hidden">
        <div class="min-w-full divide-y divide-gray-200">
          <div class="bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider grid grid-cols-7 gap-4">
            <div>Company</div>
            <div>Industry</div>
            <div>Location</div>
            <div>Jobs</div>
            <div>Status</div>
            <div>Created</div>
            <div class="text-right">Actions</div>
          </div>
          <div class="bg-white divide-y divide-gray-200">
            <div
              v-for="company in companiesStore.companies"
              :key="company.id"
              class="px-6 py-4 grid grid-cols-7 gap-4 items-center hover:bg-gray-50"
            >
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <div class="h-10 w-10 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold">
                    {{ company.name.charAt(0) }}
                  </div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ company.name }}</div>
                  <div class="text-sm text-gray-500">{{ company.email }}</div>
                </div>
              </div>
              <div class="text-sm text-gray-900">{{ company.industry || 'N/A' }}</div>
              <div class="text-sm text-gray-900">{{ company.location || 'N/A' }}</div>
              <div class="text-sm text-gray-900">{{ company.job_count || 0 }}</div>
              <div>
                <span
                  :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    company.is_active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ company.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
              <div class="text-sm text-gray-500">
                {{ formatDate(company.created_at) }}
              </div>
              <div class="text-right space-x-2">
                <Button @click="editCompany(company)" variant="outline" size="sm">
                  Edit
                </Button>
                <Button
                  @click="confirmDelete(company)"
                  variant="danger"
                  size="sm"
                  :loading="deletingId === company.id"
                >
                  Delete
                </Button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
          <div class="flex-1 flex justify-between sm:hidden">
            <Button
              @click="companiesStore.prevPage"
              :disabled="!companiesStore.pagination.hasPrevPage"
              variant="outline"
            >
              Previous
            </Button>
            <Button
              @click="companiesStore.nextPage"
              :disabled="!companiesStore.pagination.hasNextPage"
              variant="outline"
            >
              Next
            </Button>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing {{ ((currentPage - 1) * perPage) + 1 }} to
                {{ Math.min(currentPage * perPage, total) }} of {{ total }} results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <Button
                  @click="companiesStore.prevPage"
                  :disabled="!companiesStore.pagination.hasPrevPage"
                  variant="outline"
                  class="rounded-l-md"
                >
                  Previous
                </Button>
                <Button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="companiesStore.goToPage(page)"
                  :variant="page === currentPage ? 'primary' : 'outline'"
                  class="rounded-none"
                >
                  {{ page }}
                </Button>
                <Button
                  @click="companiesStore.nextPage"
                  :disabled="!companiesStore.pagination.hasNextPage"
                  variant="outline"
                  class="rounded-r-md"
                >
                  Next
                </Button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Company Modal -->
    <Modal
      :show="showCreateModal || showEditModal"
      :title="editingCompany ? 'Edit Company' : 'Create Company'"
      @close="closeModal"
    >
      <form @submit.prevent="submitForm" class="space-y-4">
        <Input
          v-model="companyForm.name"
          label="Company Name"
          required
          :error="formErrors.name"
        />
        <Input
          v-model="companyForm.email"
          type="email"
          label="Email"
          required
          :error="formErrors.email"
        />
        <Input
          v-model="companyForm.website"
          label="Website"
          :error="formErrors.website"
        />
        <div class="grid grid-cols-2 gap-4">
          <Input
            v-model="companyForm.industry"
            label="Industry"
            :error="formErrors.industry"
          />
          <Input
            v-model="companyForm.location"
            label="Location"
            :error="formErrors.location"
          />
        </div>
        <div class="space-y-1">
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="companyForm.description"
            rows="3"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
            :class="formErrors.description ? 'border-red-300' : 'border-gray-300'"
            placeholder="Company description..."
          ></textarea>
          <p v-if="formErrors.description" class="text-sm text-red-600">{{ formErrors.description }}</p>
        </div>
        <div class="flex items-center">
          <input
            id="is-active"
            v-model="companyForm.is_active"
            type="checkbox"
            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <label for="is-active" class="ml-2 text-sm text-gray-700">Active</label>
        </div>
      </form>
      
      <template #footer>
        <div class="flex justify-end space-x-3">
          <Button @click="closeModal" variant="outline">Cancel</Button>
          <Button
            @click="submitForm"
            :loading="submitting"
            variant="primary"
          >
            {{ editingCompany ? 'Update' : 'Create' }}
          </Button>
        </div>
      </template>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal
      :show="showDeleteModal"
      title="Delete Company"
      @close="showDeleteModal = false"
    >
      <p class="text-sm text-gray-500">
        Are you sure you want to delete <strong>{{ companyToDelete?.name }}</strong>?
        This action cannot be undone.
      </p>
      
      <template #footer>
        <div class="flex justify-end space-x-3">
          <Button @click="showDeleteModal = false" variant="outline">Cancel</Button>
          <Button
            @click="deleteCompany"
            :loading="deletingId === companyToDelete?.id"
            variant="danger"
          >
            Delete
          </Button>
        </div>
      </template>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, reactive } from "vue"
import { useCompaniesStore } from "../../stores/companies"
import Button from "../../components/ui/Button.vue"
import Input from "../../components/forms/Input.vue"
import Modal from "../../components/ui/Modal.vue"

const companiesStore = useCompaniesStore()

// Reactive state
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const editingCompany = ref<any>(null)
const companyToDelete = ref<any>(null)
const submitting = ref(false)
const deletingId = ref<number | null>(null)

// Search and filters
const searchQuery = ref("")
const industryFilter = ref("")
const locationFilter = ref("")

// Form state
const companyForm = reactive({
  name: "",
  email: "",
  website: "",
  industry: "",
  location: "",
  description: "",
  is_active: true
})

const formErrors = reactive<Record<string, string>>({})

// Computed properties
const pagination = computed(() => companiesStore.pagination)

const visiblePages = computed(() => {
  const pages = []
  const paginationData = companiesStore.pagination
  const start = Math.max(1, paginationData.currentPage - 2)
  const end = Math.min(paginationData.lastPage, paginationData.currentPage + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Utility functions
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

let searchTimeout: number

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    handleFilterChange()
  }, 300)
}

const handleFilterChange = () => {
  companiesStore.searchCompanies({
    search: searchQuery.value,
    industry: industryFilter.value,
    location: locationFilter.value
  })
}

const clearFilters = () => {
  searchQuery.value = ""
  industryFilter.value = ""
  locationFilter.value = ""
  companiesStore.clearFilters()
  companiesStore.fetchCompanies()
}

// Form handling
const resetForm = () => {
  Object.assign(companyForm, {
    name: "",
    email: "",
    website: "",
    industry: "",
    location: "",
    description: "",
    is_active: true
  })
  Object.keys(formErrors).forEach(key => delete formErrors[key])
}

const closeModal = () => {
  showCreateModal.value = false
  showEditModal.value = false
  editingCompany.value = null
  resetForm()
}

const editCompany = (company: any) => {
  editingCompany.value = company
  Object.assign(companyForm, {
    name: company.name,
    email: company.email,
    website: company.website || "",
    industry: company.industry || "",
    location: company.location || "",
    description: company.description || "",
    is_active: company.is_active
  })
  showEditModal.value = true
}

const validateForm = () => {
  Object.keys(formErrors).forEach(key => delete formErrors[key])
  
  if (!companyForm.name.trim()) {
    formErrors.name = "Company name is required"
  }
  
  if (!companyForm.email.trim()) {
    formErrors.email = "Email is required"
  } else if (!/\S+@\S+\.\S+/.test(companyForm.email)) {
    formErrors.email = "Email format is invalid"
  }
  
  return Object.keys(formErrors).length === 0
}

const submitForm = async () => {
  if (!validateForm()) return
  
  submitting.value = true
  
  try {
    let success = false
    
    if (editingCompany.value) {
      success = await companiesStore.updateCompany(editingCompany.value.id, companyForm)
    } else {
      success = await companiesStore.createCompany(companyForm)
    }
    
    if (success) {
      closeModal()
      // Refresh the list to get updated data
      await companiesStore.fetchCompanies()
    }
  } catch (error) {
    console.error('Form submission error:', error)
  } finally {
    submitting.value = false
  }
}

const confirmDelete = (company: any) => {
  companyToDelete.value = company
  showDeleteModal.value = true
}

const deleteCompany = async () => {
  if (!companyToDelete.value) return
  
  deletingId.value = companyToDelete.value.id
  
  try {
    const success = await companiesStore.deleteCompany(companyToDelete.value.id)
    
    if (success) {
      showDeleteModal.value = false
      companyToDelete.value = null
    }
  } catch (error) {
    console.error('Delete error:', error)
  } finally {
    deletingId.value = null
  }
}

// Initialize
onMounted(() => {
  companiesStore.fetchCompanies()
})
</script>

<style scoped>
/* Component-specific styles */
</style> 