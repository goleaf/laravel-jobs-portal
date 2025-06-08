<template>
  <div class="context7-candidate-profile">
    <!-- Experience Section -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">{{ $t('candidate.experience') }}</h3>
        <button
          @click="showExperienceModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
        >
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ $t('candidate.add_experience') }}
        </button>
      </div>

      <!-- Experience Cards -->
      <div class="space-y-4">
        <div
          v-for="(experience, index) in experiences"
          :key="experience.id || index"
          class="bg-white shadow rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h4 class="text-lg font-medium text-indigo-600">{{ experience.title }}</h4>
              <h5 class="text-md text-gray-700 mt-1">{{ experience.company }}</h5>
              <p class="text-sm text-gray-500 mt-2">
                {{ formatDateRange(experience.start_date, experience.end_date) }} | {{ experience.country }}
              </p>
              <p class="text-gray-700 mt-3" v-if="experience.description">{{ experience.description }}</p>
            </div>
            <div class="flex items-center space-x-2 ml-4">
              <button
                @click="editExperience(experience)"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                :title="$t('common.edit')"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                @click="deleteExperience(experience)"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                :title="$t('common.delete')"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="experiences.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8zM8 14v.01M12 14v.01M16 14v.01" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('candidate.no_experience') }}</h3>
          <p class="mt-1 text-sm text-gray-500">{{ $t('candidate.add_first_experience') }}</p>
        </div>
      </div>
    </div>

    <!-- Education Section -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-medium text-gray-900">{{ $t('candidate.education') }}</h3>
        <button
          @click="showEducationModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
        >
          <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          {{ $t('candidate.add_education') }}
        </button>
      </div>

      <!-- Education Cards -->
      <div class="space-y-4">
        <div
          v-for="(education, index) in educations"
          :key="education.id || index"
          class="bg-white shadow rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <h4 class="text-lg font-medium text-indigo-600">{{ education.degree_level }}</h4>
              <h5 class="text-md text-gray-700 mt-1">{{ education.degree_title }}</h5>
              <p class="text-sm text-gray-500 mt-2">
                {{ education.year }} | {{ education.country }}
              </p>
              <p class="text-gray-700 mt-3 font-medium" v-if="education.institute">{{ education.institute }}</p>
            </div>
            <div class="flex items-center space-x-2 ml-4">
              <button
                @click="editEducation(education)"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                :title="$t('common.edit')"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                @click="deleteEducation(education)"
                class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200"
                :title="$t('common.delete')"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="educations.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('candidate.no_education') }}</h3>
          <p class="mt-1 text-sm text-gray-500">{{ $t('candidate.add_first_education') }}</p>
        </div>
      </div>
    </div>

    <!-- Experience Modal -->
    <Context7Modal
      v-model="showExperienceModal"
      :title="editingExperience ? $t('candidate.edit_experience') : $t('candidate.add_experience')"
      size="lg"
    >
      <form @submit.prevent="saveExperience" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.job_title') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="experienceForm.title"
              type="text"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :placeholder="$t('candidate.job_title_placeholder')"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.company') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="experienceForm.company"
              type="text"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :placeholder="$t('candidate.company_placeholder')"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.start_date') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="experienceForm.start_date"
              type="date"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.end_date') }}
            </label>
            <input
              v-model="experienceForm.end_date"
              type="date"
              :disabled="experienceForm.currently_working"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm disabled:bg-gray-100"
            />
            <div class="mt-2">
              <label class="flex items-center">
                <input
                  v-model="experienceForm.currently_working"
                  type="checkbox"
                  class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                />
                <span class="ml-2 text-sm text-gray-600">{{ $t('candidate.currently_working') }}</span>
              </label>
            </div>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.country') }} <span class="text-red-500">*</span>
            </label>
            <select
              v-model="experienceForm.country"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">{{ $t('candidate.select_country') }}</option>
              <option v-for="country in countries" :key="country.id" :value="country.name">
                {{ country.name }}
              </option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.description') }}
            </label>
            <textarea
              v-model="experienceForm.description"
              rows="4"
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :placeholder="$t('candidate.description_placeholder')"
            ></textarea>
          </div>
        </div>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="cancelExperienceEdit"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('common.cancel') }}
          </button>
          <button
            type="submit"
            :disabled="savingExperience"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <span v-if="savingExperience" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ $t('common.saving') }}
            </span>
            <span v-else>{{ editingExperience ? $t('common.update') : $t('common.save') }}</span>
          </button>
        </div>
      </form>
    </Context7Modal>

    <!-- Education Modal -->
    <Context7Modal
      v-model="showEducationModal"
      :title="editingEducation ? $t('candidate.edit_education') : $t('candidate.add_education')"
      size="lg"
    >
      <form @submit.prevent="saveEducation" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.degree_level') }} <span class="text-red-500">*</span>
            </label>
            <select
              v-model="educationForm.degree_level"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">{{ $t('candidate.select_degree_level') }}</option>
              <option v-for="level in degreeLevels" :key="level.id" :value="level.name">
                {{ level.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.degree_title') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="educationForm.degree_title"
              type="text"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :placeholder="$t('candidate.degree_title_placeholder')"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.year') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="educationForm.year"
              type="number"
              :min="1950"
              :max="new Date().getFullYear() + 10"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.country') }} <span class="text-red-500">*</span>
            </label>
            <select
              v-model="educationForm.country"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
              <option value="">{{ $t('candidate.select_country') }}</option>
              <option v-for="country in countries" :key="country.id" :value="country.name">
                {{ country.name }}
              </option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              {{ $t('candidate.institute') }} <span class="text-red-500">*</span>
            </label>
            <input
              v-model="educationForm.institute"
              type="text"
              required
              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              :placeholder="$t('candidate.institute_placeholder')"
            />
          </div>
        </div>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="cancelEducationEdit"
            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ $t('common.cancel') }}
          </button>
          <button
            type="submit"
            :disabled="savingEducation"
            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
          >
            <span v-if="savingEducation" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ $t('common.saving') }}
            </span>
            <span v-else>{{ editingEducation ? $t('common.update') : $t('common.save') }}</span>
          </button>
        </div>
      </form>
    </Context7Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useContext7Notifications } from '@/composables/useContext7Notifications'
import Context7Modal from '@/components/ui/Context7Modal.vue'

// Interfaces
interface Experience {
  id?: number
  title: string
  company: string
  start_date: string
  end_date?: string
  currently_working?: boolean
  country: string
  description?: string
}

interface Education {
  id?: number
  degree_level: string
  degree_title: string
  year: number
  country: string
  institute: string
}

interface Country {
  id: number
  name: string
}

interface DegreeLevel {
  id: number
  name: string
}

// Props
const props = defineProps<{
  candidateId?: number
  experiences?: Experience[]
  educations?: Education[]
  countries?: Country[]
  degreeLevels?: DegreeLevel[]
}>()

// Emits
const emit = defineEmits<{
  'experience-updated': []
  'education-updated': []
}>()

// Composables
const { showSuccess, showError, showConfirmation } = useContext7Notifications()

// Reactive Data
const experiences = ref<Experience[]>(props.experiences || [])
const educations = ref<Education[]>(props.educations || [])
const countries = ref<Country[]>(props.countries || [])
const degreeLevels = ref<DegreeLevel[]>(props.degreeLevels || [])

// Modal States
const showExperienceModal = ref(false)
const showEducationModal = ref(false)
const editingExperience = ref<Experience | null>(null)
const editingEducation = ref<Education | null>(null)
const savingExperience = ref(false)
const savingEducation = ref(false)

// Form Data
const experienceForm = reactive<Experience>({
  title: '',
  company: '',
  start_date: '',
  end_date: '',
  currently_working: false,
  country: '',
  description: ''
})

const educationForm = reactive<Education>({
  degree_level: '',
  degree_title: '',
  year: new Date().getFullYear(),
  country: '',
  institute: ''
})

// Methods
const formatDateRange = (startDate: string, endDate?: string) => {
  const start = new Date(startDate).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short' 
  })
  
  if (!endDate) {
    return `${start} - Present`
  }
  
  const end = new Date(endDate).toLocaleDateString('en-US', { 
    year: 'numeric', 
    month: 'short' 
  })
  
  return `${start} - ${end}`
}

const editExperience = (experience: Experience) => {
  editingExperience.value = experience
  Object.assign(experienceForm, { ...experience })
  showExperienceModal.value = true
}

const editEducation = (education: Education) => {
  editingEducation.value = education
  Object.assign(educationForm, { ...education })
  showEducationModal.value = true
}

const deleteExperience = async (experience: Experience) => {
  const confirmed = await showConfirmation({
    title: 'Delete Experience',
    text: 'Are you sure you want to delete this experience?',
    confirmText: 'Yes, delete it!',
    cancelText: 'Cancel'
  })

  if (confirmed) {
    try {
      if (experience.id) {
        // Make API call to delete
        const response = await fetch(`/api/candidate/experience/${experience.id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
          }
        })

        if (!response.ok) {
          throw new Error('Delete failed')
        }
      }

      // Remove from local array
      const index = experiences.value.findIndex(exp => exp.id === experience.id)
      if (index > -1) {
        experiences.value.splice(index, 1)
      }

      showSuccess('Experience deleted successfully')
      emit('experience-updated')
    } catch (error) {
      showError('Failed to delete experience. Please try again.')
    }
  }
}

const deleteEducation = async (education: Education) => {
  const confirmed = await showConfirmation({
    title: 'Delete Education',
    text: 'Are you sure you want to delete this education?',
    confirmText: 'Yes, delete it!',
    cancelText: 'Cancel'
  })

  if (confirmed) {
    try {
      if (education.id) {
        // Make API call to delete
        const response = await fetch(`/api/candidate/education/${education.id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
          }
        })

        if (!response.ok) {
          throw new Error('Delete failed')
        }
      }

      // Remove from local array
      const index = educations.value.findIndex(edu => edu.id === education.id)
      if (index > -1) {
        educations.value.splice(index, 1)
      }

      showSuccess('Education deleted successfully')
      emit('education-updated')
    } catch (error) {
      showError('Failed to delete education. Please try again.')
    }
  }
}

const saveExperience = async () => {
  savingExperience.value = true

  try {
    const url = editingExperience.value 
      ? `/api/candidate/experience/${editingExperience.value.id}`
      : '/api/candidate/experience'
    
    const method = editingExperience.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        ...experienceForm,
        candidate_id: props.candidateId
      })
    })

    if (!response.ok) {
      throw new Error('Save failed')
    }

    const savedExperience = await response.json()

    if (editingExperience.value) {
      // Update existing
      const index = experiences.value.findIndex(exp => exp.id === editingExperience.value?.id)
      if (index > -1) {
        experiences.value[index] = savedExperience.data
      }
    } else {
      // Add new
      experiences.value.push(savedExperience.data)
    }

    showSuccess(editingExperience.value ? 'Experience updated successfully' : 'Experience added successfully')
    cancelExperienceEdit()
    emit('experience-updated')
  } catch (error) {
    showError('Failed to save experience. Please try again.')
  } finally {
    savingExperience.value = false
  }
}

const saveEducation = async () => {
  savingEducation.value = true

  try {
    const url = editingEducation.value 
      ? `/api/candidate/education/${editingEducation.value.id}`
      : '/api/candidate/education'
    
    const method = editingEducation.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        ...educationForm,
        candidate_id: props.candidateId
      })
    })

    if (!response.ok) {
      throw new Error('Save failed')
    }

    const savedEducation = await response.json()

    if (editingEducation.value) {
      // Update existing
      const index = educations.value.findIndex(edu => edu.id === editingEducation.value?.id)
      if (index > -1) {
        educations.value[index] = savedEducation.data
      }
    } else {
      // Add new
      educations.value.push(savedEducation.data)
    }

    showSuccess(editingEducation.value ? 'Education updated successfully' : 'Education added successfully')
    cancelEducationEdit()
    emit('education-updated')
  } catch (error) {
    showError('Failed to save education. Please try again.')
  } finally {
    savingEducation.value = false
  }
}

const cancelExperienceEdit = () => {
  showExperienceModal.value = false
  editingExperience.value = null
  Object.assign(experienceForm, {
    title: '',
    company: '',
    start_date: '',
    end_date: '',
    currently_working: false,
    country: '',
    description: ''
  })
}

const cancelEducationEdit = () => {
  showEducationModal.value = false
  editingEducation.value = null
  Object.assign(educationForm, {
    degree_level: '',
    degree_title: '',
    year: new Date().getFullYear(),
    country: '',
    institute: ''
  })
}

// Load initial data if needed
onMounted(async () => {
  if (!countries.value.length) {
    // Load countries from API
    try {
      const response = await fetch('/api/countries')
      if (response.ok) {
        const data = await response.json()
        countries.value = data.data || []
      }
    } catch (error) {
      console.error('Failed to load countries:', error)
    }
  }

  if (!degreeLevels.value.length) {
    // Load degree levels from API
    try {
      const response = await fetch('/api/degree-levels')
      if (response.ok) {
        const data = await response.json()
        degreeLevels.value = data.data || []
      }
    } catch (error) {
      console.error('Failed to load degree levels:', error)
    }
  }
})
</script> 