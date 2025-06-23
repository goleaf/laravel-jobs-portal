<template>
  <div class="job-form-container max-w-4xl mx-auto p-6">
    <form @submit.prevent="handleSubmit" class="space-y-8">
      <!-- Job Basic Information -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Job Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
              Job Title <span class="text-red-500">*</span>
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :class="{ 'border-red-300': errors.title }"
            />
            <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title[0] }}</p>
          </div>

          <div>
            <label for="job_category_id" class="block text-sm font-medium text-gray-700 mb-1">
              Job Category <span class="text-red-500">*</span>
            </label>
            <select
              id="job_category_id"
              v-model="form.job_category_id"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :class="{ 'border-red-300': errors.job_category_id }"
            >
              <option value="">Select Category</option>
              <option 
                v-for="category in jobCategories" 
                :key="category.id" 
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <p v-if="errors.job_category_id" class="mt-1 text-sm text-red-600">{{ errors.job_category_id[0] }}</p>
          </div>

          <div>
            <label for="job_type_id" class="block text-sm font-medium text-gray-700 mb-1">
              Job Type <span class="text-red-500">*</span>
            </label>
            <select
              id="job_type_id"
              v-model="form.job_type_id"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :class="{ 'border-red-300': errors.job_type_id }"
            >
              <option value="">Select Type</option>
              <option 
                v-for="type in jobTypes" 
                :key="type.id" 
                :value="type.id"
              >
                {{ type.name }}
              </option>
            </select>
            <p v-if="errors.job_type_id" class="mt-1 text-sm text-red-600">{{ errors.job_type_id[0] }}</p>
          </div>

          <div>
            <label for="career_level_id" class="block text-sm font-medium text-gray-700 mb-1">
              Career Level
            </label>
            <select
              id="career_level_id"
              v-model="form.career_level_id"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
              <option value="">Select Level</option>
              <option 
                v-for="level in careerLevels" 
                :key="level.id" 
                :value="level.id"
              >
                {{ level.level_name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Job Description -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Job Description</h3>
        
        <div class="space-y-6">
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
              Job Description <span class="text-red-500">*</span>
            </label>
            <div ref="descriptionEditor" class="min-h-[200px]"></div>
            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
          </div>

          <div>
            <label for="key_responsibilities" class="block text-sm font-medium text-gray-700 mb-1">
              Key Responsibilities <span class="text-red-500">*</span>
            </label>
            <div ref="responsibilitiesEditor" class="min-h-[200px]"></div>
            <p v-if="errors.key_responsibilities" class="mt-1 text-sm text-red-600">{{ errors.key_responsibilities[0] }}</p>
          </div>
        </div>
      </div>

      <!-- Salary Information -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Salary Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label for="salary_from" class="block text-sm font-medium text-gray-700 mb-1">
              Salary From
            </label>
            <input
              id="salary_from"
              v-model="form.salary_from"
              type="number"
              step="0.1"
              min="0"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              @input="validateSalaryRange"
            />
          </div>

          <div>
            <label for="salary_to" class="block text-sm font-medium text-gray-700 mb-1">
              Salary To
            </label>
            <input
              id="salary_to"
              v-model="form.salary_to"
              type="number"
              step="0.1"
              min="0"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              :class="{ 'border-red-300': salaryRangeError }"
              @input="validateSalaryRange"
            />
            <p v-if="salaryRangeError" class="mt-1 text-sm text-red-600">{{ salaryRangeError }}</p>
          </div>

          <div>
            <label for="salary_currency_id" class="block text-sm font-medium text-gray-700 mb-1">
              Currency
            </label>
            <select
              id="salary_currency_id"
              v-model="form.salary_currency_id"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
              <option value="">Select Currency</option>
              <option 
                v-for="currency in salaryCurrencies" 
                :key="currency.id" 
                :value="currency.id"
              >
                {{ currency.currency_name }} ({{ currency.currency_code }})
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Location & Requirements -->
      <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Location & Requirements</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="country_id" class="block text-sm font-medium text-gray-700 mb-1">
              Country <span class="text-red-500">*</span>
            </label>
            <select
              id="country_id"
              v-model="form.country_id"
              required
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              @change="onCountryChange"
            >
              <option value="">Select Country</option>
              <option 
                v-for="country in countries" 
                :key="country.id" 
                :value="country.id"
              >
                {{ country.name }}
              </option>
            </select>
          </div>

          <div>
            <label for="state_id" class="block text-sm font-medium text-gray-700 mb-1">
              State/Province
            </label>
            <select
              id="state_id"
              v-model="form.state_id"
              :disabled="!form.country_id"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              @change="onStateChange"
            >
              <option value="">Select State</option>
              <option 
                v-for="state in states" 
                :key="state.id" 
                :value="state.id"
              >
                {{ state.name }}
              </option>
            </select>
          </div>

          <div>
            <label for="city_id" class="block text-sm font-medium text-gray-700 mb-1">
              City
            </label>
            <select
              id="city_id"
              v-model="form.city_id"
              :disabled="!form.state_id"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
              <option value="">Select City</option>
              <option 
                v-for="city in cities" 
                :key="city.id" 
                :value="city.id"
              >
                {{ city.name }}
              </option>
            </select>
          </div>

          <div>
            <label for="degree_level_id" class="block text-sm font-medium text-gray-700 mb-1">
              Required Education Level
            </label>
            <select
              id="degree_level_id"
              v-model="form.degree_level_id"
              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
              <option value="">Select Education Level</option>
              <option 
                v-for="level in degreeLevels" 
                :key="level.id" 
                :value="level.id"
              >
                {{ level.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="flex justify-end space-x-4 pt-6">
        <button
          type="button"
          @click="saveDraft"
          :disabled="processing"
          class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          Save as Draft
        </button>
        
        <button
          type="submit"
          :disabled="processing || !isFormValid"
          class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
        >
          <span v-if="processing" class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
          </span>
          <span v-else>{{ isEditing ? 'Update Job' : 'Create Job' }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue'
import Quill from 'quill'
import 'quill/dist/quill.snow.css'

// Props
interface Job {
  id?: number
  title: string
  job_category_id: number | null
  job_type_id: number | null
  career_level_id: number | null
  description: string
  key_responsibilities: string
  salary_from: number | null
  salary_to: number | null
  salary_currency_id: number | null
  country_id: number | null
  state_id: number | null
  city_id: number | null
  degree_level_id: number | null
  status: 'draft' | 'published'
}

const props = defineProps<{
  job?: Job
  jobCategories: Array<{ id: number; name: string }>
  jobTypes: Array<{ id: number; name: string }>
  careerLevels: Array<{ id: number; level_name: string }>
  countries: Array<{ id: number; name: string }>
  salaryCurrencies: Array<{ id: number; currency_name: string; currency_code: string }>
  degreeLevels: Array<{ id: number; name: string }>
}>()

// Emits
const emit = defineEmits<{
  submit: [job: Job, isDraft: boolean]
  cancel: []
}>()

// Reactive state
const form = reactive<Job>({
  title: '',
  job_category_id: null,
  job_type_id: null,
  career_level_id: null,
  description: '',
  key_responsibilities: '',
  salary_from: null,
  salary_to: null,
  salary_currency_id: null,
  country_id: null,
  state_id: null,
  city_id: null,
  degree_level_id: null,
  status: 'published'
})

const errors = ref<Record<string, string[]>>({})
const processing = ref(false)
const salaryRangeError = ref('')
const states = ref<Array<{ id: number; name: string }>>([])
const cities = ref<Array<{ id: number; name: string }>>([])

// Editor refs
const descriptionEditor = ref<HTMLElement>()
const responsibilitiesEditor = ref<HTMLElement>()
let quillDescription: Quill | null = null
let quillResponsibilities: Quill | null = null

// Computed
const isEditing = computed(() => !!props.job?.id)
const isFormValid = computed(() => {
  return form.title && 
         form.job_category_id && 
         form.job_type_id && 
         form.description && 
         form.key_responsibilities &&
         form.country_id &&
         !salaryRangeError.value
})

// Methods
const initializeQuillEditors = () => {
  if (descriptionEditor.value) {
    quillDescription = new Quill(descriptionEditor.value, {
      modules: {
        toolbar: [['bold', 'italic', 'underline', 'strike'], ['clean']]
      },
      placeholder: 'Enter job description...',
      theme: 'snow'
    })
    
    quillDescription.on('text-change', () => {
      form.description = quillDescription?.root.innerHTML || ''
    })
  }

  if (responsibilitiesEditor.value) {
    quillResponsibilities = new Quill(responsibilitiesEditor.value, {
      modules: {
        toolbar: [['bold', 'italic', 'underline', 'strike'], ['clean']]
      },
      placeholder: 'Enter key responsibilities...',
      theme: 'snow'
    })
    
    quillResponsibilities.on('text-change', () => {
      form.key_responsibilities = quillResponsibilities?.root.innerHTML || ''
    })
  }
}

const validateSalaryRange = () => {
  salaryRangeError.value = ''
  
  if (form.salary_from && form.salary_to && form.salary_to < form.salary_from) {
    salaryRangeError.value = 'Salary range "to" must be greater than "from"'
  }
}

const onCountryChange = async () => {
  form.state_id = null
  form.city_id = null
  states.value = []
  cities.value = []
  
  if (form.country_id) {
    // Load states for selected country
    // This would typically be an API call
    console.log('Load states for country:', form.country_id)
  }
}

const onStateChange = async () => {
  form.city_id = null
  cities.value = []
  
  if (form.state_id) {
    // Load cities for selected state
    // This would typically be an API call
    console.log('Load cities for state:', form.state_id)
  }
}

const handleSubmit = async () => {
  errors.value = {}
  processing.value = true

  try {
    // Validate required fields
    if (!form.description.trim()) {
      errors.value.description = ['Job description is required']
    }
    
    if (!form.key_responsibilities.trim()) {
      errors.value.key_responsibilities = ['Key responsibilities are required']
    }

    if (Object.keys(errors.value).length > 0) {
      processing.value = false
      return
    }

    form.status = 'published'
    emit('submit', { ...form }, false)
  } catch (error) {
    console.error('Error submitting job:', error)
  } finally {
    processing.value = false
  }
}

const saveDraft = async () => {
  processing.value = true
  
  try {
    form.status = 'draft'
    emit('submit', { ...form }, true)
  } catch (error) {
    console.error('Error saving draft:', error)
  } finally {
    processing.value = false
  }
}

// Lifecycle
onMounted(() => {
  // Initialize form with existing job data
  if (props.job) {
    Object.assign(form, props.job)
  }
  
  // Initialize Quill editors after DOM is ready
  setTimeout(initializeQuillEditors, 100)
})

onUnmounted(() => {
  quillDescription = null
  quillResponsibilities = null
})
</script>

<style scoped>
.job-form-container {
  max-width: 1024px;
}

/* Quill editor custom styles */
:deep(.ql-editor) {
  min-height: 150px;
}

:deep(.ql-toolbar) {
  border-top: 1px solid #e5e7eb;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
  border-radius: 0.375rem 0.375rem 0 0;
}

:deep(.ql-container) {
  border-bottom: 1px solid #e5e7eb;
  border-left: 1px solid #e5e7eb;
  border-right: 1px solid #e5e7eb;
  border-radius: 0 0 0.375rem 0.375rem;
}
</style> 