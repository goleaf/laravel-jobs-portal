<template>
  <MainLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="bg-white px-6 py-8">
        <div class="max-w-3xl mx-auto">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                {{ isEditing ? 'Edit Job Posting' : 'Post a New Job' }}
              </h1>
              <p class="text-gray-600 text-lg">
                {{ isEditing ? 'Update your job posting details' : 'Create an attractive job posting to find the right candidates' }}
              </p>
            </div>
            
            <div class="flex gap-3">
              <BaseButton
                variant="outline"
                :to="{ name: 'employer.jobs' }"
                tag="router-link"
              >
                Cancel
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                @click="saveAsDraft"
                :disabled="isSubmitting"
              >
                <DocumentIcon class="h-4 w-4 mr-2" />
                Save Draft
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <form @submit.prevent="submitJob" class="space-y-8">
        <!-- Basic Job Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Job Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Job Title -->
            <div class="md:col-span-2">
              <BaseInput
                v-model="form.title"
                label="Job Title"
                placeholder="e.g. Senior Frontend Developer"
                required
                :error="errors.title"
              />
            </div>

            <!-- Department -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Department <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.department_id"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
              >
                <option value="">Select Department</option>
                <option
                  v-for="department in departments"
                  :key="department.id"
                  :value="department.id"
                >
                  {{ department.name }}
                </option>
              </select>
              <p v-if="errors.department_id" class="text-red-500 text-xs mt-1">{{ errors.department_id }}</p>
            </div>

            <!-- Job Category -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Category <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.category_id"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
              >
                <option value="">Select Category</option>
                <option
                  v-for="category in categories"
                  :key="category.id"
                  :value="category.id"
                >
                  {{ category.name }}
                </option>
              </select>
              <p v-if="errors.category_id" class="text-red-500 text-xs mt-1">{{ errors.category_id }}</p>
            </div>

            <!-- Employment Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Employment Type <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.employment_type"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
              >
                <option value="">Select Type</option>
                <option value="full-time">Full-time</option>
                <option value="part-time">Part-time</option>
                <option value="contract">Contract</option>
                <option value="freelance">Freelance</option>
                <option value="internship">Internship</option>
              </select>
              <p v-if="errors.employment_type" class="text-red-500 text-xs mt-1">{{ errors.employment_type }}</p>
            </div>

            <!-- Experience Level -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Experience Level <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.experience_level"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
              >
                <option value="">Select Level</option>
                <option value="entry">Entry Level (0-2 years)</option>
                <option value="mid">Mid Level (3-5 years)</option>
                <option value="senior">Senior Level (6-10 years)</option>
                <option value="lead">Lead/Principal (10+ years)</option>
                <option value="executive">Executive Level</option>
              </select>
              <p v-if="errors.experience_level" class="text-red-500 text-xs mt-1">{{ errors.experience_level }}</p>
            </div>
          </div>
        </div>

        <!-- Location & Remote Options -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Location & Work Arrangement</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Country -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Country <span class="text-red-500">*</span>
              </label>
              <select
                v-model="form.country_id"
                @change="loadStates"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                required
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
              <p v-if="errors.country_id" class="text-red-500 text-xs mt-1">{{ errors.country_id }}</p>
            </div>

            <!-- State -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                State/Province
              </label>
              <select
                v-model="form.state_id"
                @change="loadCities"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                :disabled="!states.length"
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

            <!-- City -->
            <div>
              <BaseInput
                v-model="form.city"
                label="City"
                placeholder="e.g. New York"
              />
            </div>

            <!-- Remote Work -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Remote Work Options
              </label>
              <select
                v-model="form.remote_option"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="on-site">On-site Only</option>
                <option value="hybrid">Hybrid (Office + Remote)</option>
                <option value="remote">Fully Remote</option>
                <option value="flexible">Flexible</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Salary & Benefits -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Compensation & Benefits</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Salary Type -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Salary Type
              </label>
              <select
                v-model="form.salary_type"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="fixed">Fixed Salary</option>
                <option value="range">Salary Range</option>
                <option value="negotiable">Negotiable</option>
                <option value="not-disclosed">Not Disclosed</option>
              </select>
            </div>

            <!-- Currency -->
            <div v-if="form.salary_type !== 'not-disclosed'">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Currency
              </label>
              <select
                v-model="form.currency"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="USD">USD ($)</option>
                <option value="EUR">EUR (€)</option>
                <option value="GBP">GBP (£)</option>
                <option value="CAD">CAD (C$)</option>
                <option value="AUD">AUD (A$)</option>
              </select>
            </div>

            <!-- Minimum Salary -->
            <div v-if="form.salary_type === 'range' || form.salary_type === 'fixed'">
              <BaseInput
                v-model="form.min_salary"
                label="Minimum Salary (Annual)"
                type="number"
                placeholder="50000"
                :error="errors.min_salary"
              />
            </div>

            <!-- Maximum Salary -->
            <div v-if="form.salary_type === 'range'">
              <BaseInput
                v-model="form.max_salary"
                label="Maximum Salary (Annual)"
                type="number"
                placeholder="80000"
                :error="errors.max_salary"
              />
            </div>
          </div>

          <!-- Benefits -->
          <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">
              Benefits & Perks
            </label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
              <label
                v-for="benefit in availableBenefits"
                :key="benefit.id"
                class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-indigo-300 cursor-pointer transition-colors duration-200"
                :class="{ 'border-indigo-500 bg-indigo-50': form.benefits.includes(benefit.id) }"
              >
                <input
                  type="checkbox"
                  :value="benefit.id"
                  v-model="form.benefits"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mr-3"
                />
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ benefit.name }}</div>
                  <div class="text-xs text-gray-500">{{ benefit.description }}</div>
                </div>
              </label>
            </div>
          </div>
        </div>

        <!-- Job Description -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Job Description</h2>
          
          <div class="space-y-6">
            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Job Description <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.description"
                rows="8"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="Describe the role, responsibilities, and what makes this opportunity exciting..."
                required
              ></textarea>
              <p v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ form.description?.length || 0 }} characters</p>
            </div>

            <!-- Requirements -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Requirements <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.requirements"
                rows="6"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="List the required qualifications, skills, and experience..."
                required
              ></textarea>
              <p v-if="errors.requirements" class="text-red-500 text-xs mt-1">{{ errors.requirements }}</p>
            </div>

            <!-- Preferred Qualifications -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Preferred Qualifications
              </label>
              <textarea
                v-model="form.preferred_qualifications"
                rows="4"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="List any nice-to-have qualifications or bonus skills..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Skills & Technologies -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Required Skills & Technologies</h2>
          
          <!-- Skills Input -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Search and select skills
            </label>
            <div class="relative">
              <input
                v-model="skillSearchQuery"
                @input="searchSkills"
                @focus="showSkillDropdown = true"
                type="text"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                placeholder="Type to search skills (e.g., JavaScript, Python, React...)"
              />
              
              <!-- Skills Dropdown -->
              <div
                v-if="showSkillDropdown && (filteredSkills.length > 0 || skillSearchQuery)"
                class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"
              >
                <button
                  v-for="skill in filteredSkills"
                  :key="skill.id"
                  type="button"
                  @click="addSkill(skill)"
                  class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50 focus:outline-none focus:bg-gray-50"
                  :class="{ 'text-gray-400': form.skills.some(s => s.id === skill.id) }"
                  :disabled="form.skills.some(s => s.id === skill.id)"
                >
                  {{ skill.name }}
                  <span v-if="form.skills.some(s => s.id === skill.id)" class="text-xs text-gray-400 ml-2">
                    Already selected
                  </span>
                </button>
                
                <div
                  v-if="skillSearchQuery && !filteredSkills.some(s => s.name.toLowerCase() === skillSearchQuery.toLowerCase())"
                  class="px-4 py-2 text-sm text-gray-500 border-t border-gray-100"
                >
                  <button
                    type="button"
                    @click="createNewSkill"
                    class="text-indigo-600 hover:text-indigo-800 font-medium"
                  >
                    + Create "{{ skillSearchQuery }}"
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Selected Skills -->
          <div v-if="form.skills.length > 0" class="space-y-3">
            <label class="block text-sm font-medium text-gray-700">
              Selected Skills ({{ form.skills.length }})
            </label>
            <div class="flex flex-wrap gap-2">
              <div
                v-for="skill in form.skills"
                :key="skill.id"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-800"
              >
                {{ skill.name }}
                <button
                  type="button"
                  @click="removeSkill(skill)"
                  class="ml-2 text-indigo-600 hover:text-indigo-800"
                >
                  <XMarkIcon class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Job Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-6">Job Settings</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Application Deadline -->
            <div>
              <BaseInput
                v-model="form.application_deadline"
                label="Application Deadline"
                type="date"
                :min="minDate"
              />
            </div>

            <!-- Number of Positions -->
            <div>
              <BaseInput
                v-model="form.positions_count"
                label="Number of Positions"
                type="number"
                min="1"
                placeholder="1"
              />
            </div>

            <!-- Job Urgency -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Priority Level
              </label>
              <select
                v-model="form.urgency"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="normal">Normal</option>
                <option value="urgent">Urgent</option>
                <option value="featured">Featured</option>
              </select>
            </div>

            <!-- Status -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Status
              </label>
              <select
                v-model="form.status"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
              >
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="paused">Paused</option>
                <option value="closed">Closed</option>
              </select>
            </div>
          </div>

          <!-- Additional Settings -->
          <div class="mt-6 space-y-4">
            <label class="flex items-center">
              <input
                v-model="form.is_remote"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-900">Allow remote applications</span>
            </label>

            <label class="flex items-center">
              <input
                v-model="form.receive_applications"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-900">Accept applications via platform</span>
            </label>

            <label class="flex items-center">
              <input
                v-model="form.show_salary"
                type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <span class="ml-2 text-sm text-gray-900">Display salary information publicly</span>
            </label>
          </div>
        </div>

        <!-- Submit Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-sm text-gray-600">
              <p class="mb-1">
                <strong>Preview:</strong> You can preview your job posting before publishing
              </p>
              <p>
                <strong>Note:</strong> Draft jobs are not visible to candidates
              </p>
            </div>
            
            <div class="flex gap-3">
              <BaseButton
                type="button"
                variant="outline"
                @click="previewJob"
                :disabled="isSubmitting"
              >
                <EyeIcon class="h-4 w-4 mr-2" />
                Preview
              </BaseButton>
              
              <BaseButton
                type="submit"
                variant="primary"
                :disabled="isSubmitting"
                :loading="isSubmitting"
              >
                <span v-if="form.status === 'draft'">
                  <DocumentIcon class="h-4 w-4 mr-2" />
                  Save Draft
                </span>
                <span v-else>
                  <CheckIcon class="h-4 w-4 mr-2" />
                  {{ isEditing ? 'Update Job' : 'Publish Job' }}
                </span>
              </BaseButton>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Success Modal -->
    <div
      v-if="showSuccessModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      @click="showSuccessModal = false"
    >
      <div class="bg-white rounded-lg p-6 max-w-md mx-4" @click.stop>
        <div class="text-center">
          <CheckCircleIcon class="h-12 w-12 text-green-500 mx-auto mb-4" />
          <h3 class="text-lg font-semibold text-gray-900 mb-2">
            {{ isEditing ? 'Job Updated!' : 'Job Posted Successfully!' }}
          </h3>
          <p class="text-gray-600 mb-6">
            {{ form.status === 'active' 
              ? 'Your job is now live and candidates can apply.' 
              : 'Your job has been saved as a draft.' 
            }}
          </p>
          <div class="flex gap-3 justify-center">
            <BaseButton
              variant="outline"
              @click="showSuccessModal = false"
            >
              Close
            </BaseButton>
            <BaseButton
              variant="primary"
              :to="{ name: 'employer.jobs.show', params: { id: jobId } }"
              tag="router-link"
            >
              View Job
            </BaseButton>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useApi } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';

// Icons
import {
  DocumentIcon,
  EyeIcon,
  CheckIcon,
  XMarkIcon,
  CheckCircleIcon
} from '@heroicons/vue/24/outline';

interface Skill {
  id: number;
  name: string;
}

interface Benefit {
  id: number;
  name: string;
  description: string;
}

const router = useRouter();
const route = useRoute();
const { apiPost, apiPut, apiGet } = useApi();

// State
const isEditing = computed(() => Boolean(route.params.id));
const isSubmitting = ref(false);
const showSuccessModal = ref(false);
const jobId = ref<number | null>(null);

// Form data
const form = reactive({
  title: '',
  department_id: '',
  category_id: '',
  employment_type: '',
  experience_level: '',
  country_id: '',
  state_id: '',
  city: '',
  remote_option: 'on-site',
  salary_type: 'negotiable',
  currency: 'USD',
  min_salary: '',
  max_salary: '',
  benefits: [] as number[],
  description: '',
  requirements: '',
  preferred_qualifications: '',
  skills: [] as Skill[],
  application_deadline: '',
  positions_count: 1,
  urgency: 'normal',
  status: 'draft',
  is_remote: false,
  receive_applications: true,
  show_salary: false
});

// Validation errors
const errors = ref<Record<string, string>>({});

// Dropdown data
const departments = ref([]);
const categories = ref([]);
const countries = ref([]);
const states = ref([]);
const cities = ref([]);
const availableBenefits = ref<Benefit[]>([
  { id: 1, name: 'Health Insurance', description: 'Medical, dental, vision' },
  { id: 2, name: 'Retirement Plan', description: '401k, pension' },
  { id: 3, name: 'Flexible Hours', description: 'Choose your schedule' },
  { id: 4, name: 'Remote Work', description: 'Work from anywhere' },
  { id: 5, name: 'Paid Time Off', description: 'Vacation, sick days' },
  { id: 6, name: 'Professional Development', description: 'Training, conferences' },
  { id: 7, name: 'Gym Membership', description: 'Fitness benefits' },
  { id: 8, name: 'Free Meals', description: 'Lunch, snacks' }
]);

// Skills search
const skillSearchQuery = ref('');
const showSkillDropdown = ref(false);
const allSkills = ref<Skill[]>([]);
const filteredSkills = ref<Skill[]>([]);

// Computed
const breadcrumbs = computed(() => [
  { label: 'Employer', to: '/employer' },
  { label: 'Jobs', to: '/employer/jobs' },
  { label: isEditing.value ? 'Edit Job' : 'Create Job' }
]);

const minDate = computed(() => {
  const today = new Date();
  return today.toISOString().split('T')[0];
});

// Methods
const searchSkills = async () => {
  if (skillSearchQuery.value.length < 2) {
    filteredSkills.value = [];
    return;
  }
  
  const filtered = allSkills.value.filter(skill =>
    skill.name.toLowerCase().includes(skillSearchQuery.value.toLowerCase())
  ).slice(0, 10);
  
  filteredSkills.value = filtered;
};

const addSkill = (skill: Skill) => {
  if (!form.skills.some(s => s.id === skill.id)) {
    form.skills.push(skill);
  }
  skillSearchQuery.value = '';
  showSkillDropdown.value = false;
  filteredSkills.value = [];
};

const removeSkill = (skill: Skill) => {
  const index = form.skills.findIndex(s => s.id === skill.id);
  if (index > -1) {
    form.skills.splice(index, 1);
  }
};

const createNewSkill = async () => {
  try {
    const response = await apiPost('/api/skills', {
      name: skillSearchQuery.value
    });
    
    const newSkill = response.data;
    allSkills.value.push(newSkill);
    addSkill(newSkill);
  } catch (error) {
    console.error('Failed to create skill:', error);
  }
};

const loadStates = async () => {
  if (!form.country_id) {
    states.value = [];
    return;
  }
  
  try {
    const response = await apiGet(`/api/countries/${form.country_id}/states`);
    states.value = response.data;
  } catch (error) {
    console.error('Failed to load states:', error);
  }
};

const loadCities = async () => {
  if (!form.state_id) {
    cities.value = [];
    return;
  }
  
  try {
    const response = await apiGet(`/api/states/${form.state_id}/cities`);
    cities.value = response.data;
  } catch (error) {
    console.error('Failed to load cities:', error);
  }
};

const validateForm = (): boolean => {
  errors.value = {};
  
  if (!form.title) errors.value.title = 'Job title is required';
  if (!form.department_id) errors.value.department_id = 'Department is required';
  if (!form.category_id) errors.value.category_id = 'Category is required';
  if (!form.employment_type) errors.value.employment_type = 'Employment type is required';
  if (!form.experience_level) errors.value.experience_level = 'Experience level is required';
  if (!form.country_id) errors.value.country_id = 'Country is required';
  if (!form.description) errors.value.description = 'Job description is required';
  if (!form.requirements) errors.value.requirements = 'Requirements are required';
  
  if (form.salary_type === 'range') {
    if (!form.min_salary) errors.value.min_salary = 'Minimum salary is required';
    if (!form.max_salary) errors.value.max_salary = 'Maximum salary is required';
    if (form.min_salary && form.max_salary && Number(form.min_salary) >= Number(form.max_salary)) {
      errors.value.max_salary = 'Maximum salary must be greater than minimum';
    }
  } else if (form.salary_type === 'fixed' && !form.min_salary) {
    errors.value.min_salary = 'Salary is required';
  }
  
  return Object.keys(errors.value).length === 0;
};

const submitJob = async () => {
  if (!validateForm()) {
    return;
  }
  
  isSubmitting.value = true;
  
  try {
    const jobData = {
      ...form,
      skill_ids: form.skills.map(s => s.id)
    };
    
    let response;
    if (isEditing.value) {
      response = await apiPut(`/api/employer/jobs/${route.params.id}`, jobData);
    } else {
      response = await apiPost('/api/employer/jobs', jobData);
    }
    
    jobId.value = response.data.id;
    showSuccessModal.value = true;
    
    // Redirect after 2 seconds
    setTimeout(() => {
      router.push({ name: 'employer.jobs' });
    }, 2000);
    
  } catch (error: any) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    } else {
      console.error('Failed to save job:', error);
    }
  } finally {
    isSubmitting.value = false;
  }
};

const saveAsDraft = async () => {
  form.status = 'draft';
  await submitJob();
};

const previewJob = () => {
  // Open preview in new tab
  const previewData = encodeURIComponent(JSON.stringify(form));
  window.open(`/employer/jobs/preview?data=${previewData}`, '_blank');
};

// Load initial data
onMounted(async () => {
  try {
    // Load dropdown data
    const [departmentsRes, categoriesRes, countriesRes, skillsRes] = await Promise.all([
      apiGet('/api/departments'),
      apiGet('/api/job-categories'),
      apiGet('/api/countries'),
      apiGet('/api/skills')
    ]);
    
    departments.value = departmentsRes.data;
    categories.value = categoriesRes.data;
    countries.value = countriesRes.data;
    allSkills.value = skillsRes.data;
    
    // Load job data if editing
    if (isEditing.value) {
      const jobRes = await apiGet(`/api/employer/jobs/${route.params.id}`);
      const job = jobRes.data;
      
      Object.assign(form, {
        ...job,
        benefits: job.benefits?.map((b: any) => b.id) || [],
        skills: job.skills || []
      });
      
      if (job.state_id) {
        await loadStates();
      }
    }
  } catch (error) {
    console.error('Failed to load initial data:', error);
  }
});

// Hide dropdown when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target?.closest('.skill-search-container')) {
    showSkillDropdown.value = false;
  }
});
</script>

<style scoped>
/* Hide number input arrows for cleaner look */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}

/* Smooth transitions for form elements */
.form-transition {
  transition: all 0.2s ease-in-out;
}

/* Enhanced checkbox styling */
input[type="checkbox"]:checked {
  background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='m13.854 3.646-7.5 7.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6 10.293l7.146-7.147a.5.5 0 0 1 .708.708z'/%3e%3c/svg%3e");
}

/* Skill tag animations */
.skill-tag {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

/* Dropdown z-index fix */
.skill-search-container {
  position: relative;
  z-index: 10;
}

/* Form section spacing */
.form-section {
  border-left: 4px solid #e5e7eb;
  transition: border-color 0.2s ease-in-out;
}

.form-section:hover {
  border-left-color: #6366f1;
}

/* Button loading state */
.btn-loading {
  position: relative;
}

.btn-loading::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 16px;
  height: 16px;
  margin: -8px 0 0 -8px;
  border: 2px solid transparent;
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Responsive form improvements */
@media (max-width: 640px) {
  .form-actions {
    flex-direction: column;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
  }
}

/* Enhanced focus states */
input:focus,
select:focus,
textarea:focus {
  outline: none;
  ring: 2px;
  ring-color: #6366f1;
  ring-opacity: 50%;
  border-color: #6366f1;
}

/* Success modal animation */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style> 