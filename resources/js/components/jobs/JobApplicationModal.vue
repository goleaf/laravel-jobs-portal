<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 z-50 overflow-y-auto"
      @keydown.escape="$emit('close')"
    >
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
      
      <!-- Modal -->
      <div class="flex min-h-full items-center justify-center p-4">
        <div
          class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div>
              <h2 class="text-xl font-semibold text-gray-900">Apply for Position</h2>
              <p class="text-sm text-gray-600 mt-1">
                {{ job.title }} at {{ job.company.name }}
              </p>
            </div>
            <button
              @click="$emit('close')"
              class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
            >
              <XMarkIcon class="h-6 w-6" />
            </button>
          </div>

          <!-- Form -->
          <form @submit.prevent="submitApplication" class="p-6 space-y-6">
            <!-- Personal Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <BaseInput
                  v-model="form.firstName"
                  label="First Name"
                  placeholder="Enter your first name"
                  required
                  :error="errors.firstName"
                />
              </div>
              <div>
                <BaseInput
                  v-model="form.lastName"
                  label="Last Name"
                  placeholder="Enter your last name"
                  required
                  :error="errors.lastName"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <BaseInput
                  v-model="form.email"
                  type="email"
                  label="Email Address"
                  placeholder="your.email@example.com"
                  required
                  :error="errors.email"
                />
              </div>
              <div>
                <BaseInput
                  v-model="form.phone"
                  type="tel"
                  label="Phone Number"
                  placeholder="+1 (555) 123-4567"
                  required
                  :error="errors.phone"
                />
              </div>
            </div>

            <!-- Location -->
            <div>
              <BaseInput
                v-model="form.location"
                label="Current Location"
                placeholder="City, State/Country"
                required
                :error="errors.location"
              />
            </div>

            <!-- Professional Information -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Professional Information</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Years of Experience
                  </label>
                  <select
                    v-model="form.experience"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  >
                    <option value="">Select experience level</option>
                    <option value="0-1">0-1 years</option>
                    <option value="2-3">2-3 years</option>
                    <option value="4-5">4-5 years</option>
                    <option value="6-8">6-8 years</option>
                    <option value="9-12">9-12 years</option>
                    <option value="13+">13+ years</option>
                  </select>
                  <p v-if="errors.experience" class="mt-1 text-sm text-red-600">{{ errors.experience }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Current/Expected Salary
                  </label>
                  <select
                    v-model="form.salaryExpectation"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="">Prefer not to say</option>
                    <option value="40000-60000">$40,000 - $60,000</option>
                    <option value="60000-80000">$60,000 - $80,000</option>
                    <option value="80000-100000">$80,000 - $100,000</option>
                    <option value="100000-120000">$100,000 - $120,000</option>
                    <option value="120000-150000">$120,000 - $150,000</option>
                    <option value="150000+">$150,000+</option>
                  </select>
                </div>
              </div>

              <div class="mt-6">
                <BaseInput
                  v-model="form.currentCompany"
                  label="Current Company"
                  placeholder="Your current employer"
                  :error="errors.currentCompany"
                />
              </div>

              <div class="mt-6">
                <BaseInput
                  v-model="form.currentTitle"
                  label="Current Job Title"
                  placeholder="Your current position"
                  :error="errors.currentTitle"
                />
              </div>
            </div>

            <!-- Resume Upload -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Resume</h3>
              
              <div class="space-y-4">
                <!-- File Upload -->
                <div
                  @dragover.prevent
                  @drop.prevent="handleFileDrop"
                  class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors duration-200"
                  :class="{ 'border-indigo-400 bg-indigo-50': isDragging }"
                >
                  <input
                    ref="fileInput"
                    type="file"
                    accept=".pdf,.doc,.docx"
                    @change="handleFileSelect"
                    class="hidden"
                  />
                  
                  <div v-if="!form.resume">
                    <DocumentArrowUpIcon class="h-12 w-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-600 mb-2">
                      Drag and drop your resume here, or
                      <button
                        type="button"
                        @click="$refs.fileInput.click()"
                        class="text-indigo-600 hover:text-indigo-700 font-medium"
                      >
                        browse files
                      </button>
                    </p>
                    <p class="text-sm text-gray-500">Supports PDF, DOC, DOCX (max 5MB)</p>
                  </div>

                  <div v-else class="flex items-center justify-center space-x-3">
                    <DocumentIcon class="h-8 w-8 text-green-500" />
                    <div class="text-left">
                      <p class="text-sm font-medium text-gray-900">{{ form.resume.name }}</p>
                      <p class="text-xs text-gray-500">{{ formatFileSize(form.resume.size) }}</p>
                    </div>
                    <button
                      type="button"
                      @click="removeFile"
                      class="text-red-500 hover:text-red-700"
                    >
                      <XMarkIcon class="h-5 w-5" />
                    </button>
                  </div>
                </div>
                
                <p v-if="errors.resume" class="text-sm text-red-600">{{ errors.resume }}</p>
              </div>
            </div>

            <!-- Cover Letter -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Cover Letter</h3>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Tell us why you're interested in this position
                </label>
                <textarea
                  v-model="form.coverLetter"
                  rows="6"
                  class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-500': errors.coverLetter }"
                  placeholder="Share your motivation, relevant experience, and what makes you a great fit for this role..."
                  required
                ></textarea>
                <div class="flex justify-between items-center mt-2">
                  <p v-if="errors.coverLetter" class="text-sm text-red-600">{{ errors.coverLetter }}</p>
                  <p class="text-sm text-gray-500">{{ form.coverLetter.length }}/2000 characters</p>
                </div>
              </div>
            </div>

            <!-- Additional Questions -->
            <div v-if="job.application_questions && job.application_questions.length > 0" class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Questions</h3>
              
              <div class="space-y-4">
                <div
                  v-for="(question, index) in job.application_questions"
                  :key="index"
                  class="space-y-2"
                >
                  <label class="block text-sm font-medium text-gray-700">
                    {{ question.question }}
                    <span v-if="question.required" class="text-red-500">*</span>
                  </label>
                  
                  <div v-if="question.type === 'text'">
                    <BaseInput
                      v-model="form.additionalAnswers[index]"
                      :placeholder="question.placeholder"
                      :required="question.required"
                    />
                  </div>
                  
                  <div v-else-if="question.type === 'textarea'">
                    <textarea
                      v-model="form.additionalAnswers[index]"
                      :placeholder="question.placeholder"
                      :required="question.required"
                      rows="3"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    ></textarea>
                  </div>
                  
                  <div v-else-if="question.type === 'select'">
                    <select
                      v-model="form.additionalAnswers[index]"
                      :required="question.required"
                      class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option value="">Select an option</option>
                      <option
                        v-for="option in question.options"
                        :key="option"
                        :value="option"
                      >
                        {{ option }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Availability -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Availability</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    When can you start?
                  </label>
                  <select
                    v-model="form.availability"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    required
                  >
                    <option value="">Select availability</option>
                    <option value="immediately">Immediately</option>
                    <option value="1-week">1 week notice</option>
                    <option value="2-weeks">2 weeks notice</option>
                    <option value="1-month">1 month notice</option>
                    <option value="2-months">2 months notice</option>
                    <option value="3-months">3+ months notice</option>
                  </select>
                  <p v-if="errors.availability" class="mt-1 text-sm text-red-600">{{ errors.availability }}</p>
                </div>

                <div v-if="job.remote_ok">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Work Preference
                  </label>
                  <select
                    v-model="form.workPreference"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                  >
                    <option value="onsite">On-site only</option>
                    <option value="remote">Remote only</option>
                    <option value="hybrid">Hybrid (flexible)</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Consent & Legal -->
            <div class="border-t border-gray-200 pt-6 space-y-4">
              <div class="flex items-start">
                <input
                  id="consent-processing"
                  v-model="form.consentProcessing"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1"
                  required
                />
                <label for="consent-processing" class="ml-3 text-sm text-gray-700">
                  I consent to the processing of my personal data for recruitment purposes.
                  <span class="text-red-500">*</span>
                </label>
              </div>

              <div class="flex items-start">
                <input
                  id="consent-contact"
                  v-model="form.consentContact"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1"
                />
                <label for="consent-contact" class="ml-3 text-sm text-gray-700">
                  I agree to receive updates about my application and other relevant opportunities.
                </label>
              </div>

              <div class="flex items-start">
                <input
                  id="authorize-check"
                  v-model="form.authorizeBackgroundCheck"
                  type="checkbox"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1"
                />
                <label for="authorize-check" class="ml-3 text-sm text-gray-700">
                  I authorize background verification if required for this position.
                </label>
              </div>
            </div>

            <!-- Error Summary -->
            <div v-if="hasErrors" class="border border-red-200 bg-red-50 rounded-md p-4">
              <div class="flex">
                <ExclamationCircleIcon class="h-5 w-5 text-red-400 mr-2 mt-0.5 flex-shrink-0" />
                <div>
                  <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                  <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    <li v-for="error in errorMessages" :key="error">{{ error }}</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="border-t border-gray-200 pt-6 flex justify-end space-x-4">
              <BaseButton
                variant="outline-gray"
                @click="$emit('close')"
                :disabled="isSubmitting"
              >
                Cancel
              </BaseButton>
              <BaseButton
                type="submit"
                variant="primary"
                :loading="isSubmitting"
                class="min-w-[120px]"
              >
                <PaperAirplaneIcon class="h-4 w-4 mr-2" />
                Submit Application
              </BaseButton>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';

// Icons
import {
  XMarkIcon,
  DocumentArrowUpIcon,
  DocumentIcon,
  ExclamationCircleIcon,
  PaperAirplaneIcon
} from '@heroicons/vue/24/outline';

interface Job {
  id: number;
  title: string;
  company: {
    name: string;
  };
  remote_ok?: boolean;
  application_questions?: Array<{
    question: string;
    type: 'text' | 'textarea' | 'select';
    placeholder?: string;
    required?: boolean;
    options?: string[];
  }>;
}

interface Props {
  show: boolean;
  job: Job;
}

interface Emits {
  (e: 'close'): void;
  (e: 'submitted', data: any): void;
}

defineProps<Props>();
defineEmits<Emits>();

// State
const isSubmitting = ref(false);
const isDragging = ref(false);
const fileInput = ref<HTMLInputElement>();

// Form data
const form = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  location: '',
  experience: '',
  salaryExpectation: '',
  currentCompany: '',
  currentTitle: '',
  resume: null as File | null,
  coverLetter: '',
  additionalAnswers: [] as string[],
  availability: '',
  workPreference: 'onsite',
  consentProcessing: false,
  consentContact: false,
  authorizeBackgroundCheck: false
});

// Validation
const errors = ref<Record<string, string>>({});

const validateForm = () => {
  errors.value = {};

  // Required fields validation
  if (!form.value.firstName.trim()) {
    errors.value.firstName = 'First name is required';
  }

  if (!form.value.lastName.trim()) {
    errors.value.lastName = 'Last name is required';
  }

  if (!form.value.email.trim()) {
    errors.value.email = 'Email is required';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'Please enter a valid email address';
  }

  if (!form.value.phone.trim()) {
    errors.value.phone = 'Phone number is required';
  }

  if (!form.value.location.trim()) {
    errors.value.location = 'Location is required';
  }

  if (!form.value.experience) {
    errors.value.experience = 'Experience level is required';
  }

  if (!form.value.resume) {
    errors.value.resume = 'Resume is required';
  }

  if (!form.value.coverLetter.trim()) {
    errors.value.coverLetter = 'Cover letter is required';
  } else if (form.value.coverLetter.length > 2000) {
    errors.value.coverLetter = 'Cover letter must be less than 2000 characters';
  }

  if (!form.value.availability) {
    errors.value.availability = 'Availability is required';
  }

  if (!form.value.consentProcessing) {
    errors.value.consent = 'You must consent to data processing to apply';
  }

  return Object.keys(errors.value).length === 0;
};

const hasErrors = computed(() => Object.keys(errors.value).length > 0);

const errorMessages = computed(() => {
  return Object.values(errors.value);
});

// File handling
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  if (file) {
    validateAndSetFile(file);
  }
};

const handleFileDrop = (event: DragEvent) => {
  isDragging.value = false;
  const file = event.dataTransfer?.files[0];
  if (file) {
    validateAndSetFile(file);
  }
};

const validateAndSetFile = (file: File) => {
  // Validate file type
  const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
  if (!allowedTypes.includes(file.type)) {
    errors.value.resume = 'Please upload a PDF, DOC, or DOCX file';
    return;
  }

  // Validate file size (5MB limit)
  if (file.size > 5 * 1024 * 1024) {
    errors.value.resume = 'File size must be less than 5MB';
    return;
  }

  form.value.resume = file;
  delete errors.value.resume;
};

const removeFile = () => {
  form.value.resume = null;
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Form submission
const submitApplication = async () => {
  if (!validateForm()) {
    return;
  }

  isSubmitting.value = true;

  try {
    // Create FormData for file upload
    const formData = new FormData();
    
    // Append all form fields
    Object.entries(form.value).forEach(([key, value]) => {
      if (key === 'resume' && value) {
        formData.append(key, value as File);
      } else if (key === 'additionalAnswers') {
        formData.append(key, JSON.stringify(value));
      } else if (typeof value === 'boolean') {
        formData.append(key, value.toString());
      } else if (value) {
        formData.append(key, value.toString());
      }
    });

    // API call to submit application
    // const response = await apiService.post(`/api/jobs/${job.id}/apply`, formData, {
    //   headers: {
    //     'Content-Type': 'multipart/form-data'
    //   }
    // });

    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 2000));

    // Emit success event
    emit('submitted', {
      message: 'Application submitted successfully!',
      application: form.value
    });

  } catch (error) {
    console.error('Failed to submit application:', error);
    errors.value.general = 'Failed to submit application. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};

// Reset form when modal closes
watch(() => props.show, (newValue) => {
  if (!newValue) {
    // Reset form
    form.value = {
      firstName: '',
      lastName: '',
      email: '',
      phone: '',
      location: '',
      experience: '',
      salaryExpectation: '',
      currentCompany: '',
      currentTitle: '',
      resume: null,
      coverLetter: '',
      additionalAnswers: [],
      availability: '',
      workPreference: 'onsite',
      consentProcessing: false,
      consentContact: false,
      authorizeBackgroundCheck: false
    };
    errors.value = {};
  }
});
</script>

<style scoped>
/* Custom scrollbar for modal content */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style> 