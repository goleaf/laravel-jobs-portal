<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl w-full space-y-8 p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
      <div>
        <img class="mx-auto h-16 w-auto" src="/images/logo.svg" alt="Job Portal Logo">
        <h2 class="mt-6 text-center text-4xl font-extrabold text-gray-900 dark:text-white">
          {{ $t('auth.create_your_account') }}
        </h2>
        <p class="mt-2 text-center text-lg text-gray-600 dark:text-gray-400">
          {{ $t('auth.choose_account_type') }}
        </p>

        <div class="mt-8 flex justify-center space-x-4">
          <button 
            @click="selectType('candidate')" 
            :class="[registerType === 'candidate' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-600']"
            class="flex-1 py-3 px-6 rounded-lg font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
          >
            {{ $t('auth.i_am_candidate') }}
          </button>
          <button 
            @click="selectType('employer')" 
            :class="[registerType === 'employer' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-600']"
            class="flex-1 py-3 px-6 rounded-lg font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
          >
            {{ $t('auth.i_am_employer') }}
          </button>
        </div>
      </div>

      <!-- Error Alert -->
      <div v-if="authStore.error" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-700">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">
              {{ $t('auth.registration_error') }}
            </h3>
            <p class="mt-1 text-sm text-red-700 dark:text-red-200">
              {{ authStore.error }}
            </p>
          </div>
          <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
              <button @click="authStore.clearError" class="inline-flex bg-red-50 dark:bg-red-900/20 rounded-md p-1.5 text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Registration Form -->
      <form v-if="registerType" class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.first_name') }}</label>
            <input
              id="first_name"
              v-model="form.first_name"
              type="text"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
              :class="{ 'border-red-500 dark:border-red-400': errors.first_name }"
              :placeholder="$t('auth.first_name_placeholder')"
            />
            <p v-if="errors.first_name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.first_name[0] }}</p>
          </div>
          <div>
            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.last_name') }}</label>
            <input
              id="last_name"
              v-model="form.last_name"
              type="text"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
              :class="{ 'border-red-500 dark:border-red-400': errors.last_name }"
              :placeholder="$t('auth.last_name_placeholder')"
            />
            <p v-if="errors.last_name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.last_name[0] }}</p>
          </div>
        </div>

        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.email_address') }}</label>
          <input
            id="email"
            v-model="form.email"
            type="email"
            required
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
            :class="{ 'border-red-500 dark:border-red-400': errors.email }"
            :placeholder="$t('auth.email_placeholder')"
          />
          <p v-if="errors.email" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.email[0] }}</p>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.password') }}</label>
          <input
            id="password"
            v-model="form.password"
            type="password"
            required
            @input="checkPasswordStrength"
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
            :class="{ 'border-red-500 dark:border-red-400': errors.password }"
            :placeholder="$t('auth.password_placeholder')"
          />
          <p v-if="errors.password" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.password[0] }}</p>
          <div v-if="form.password.length > 0" class="w-full bg-gray-200 rounded-full h-2 mt-2">
            <div 
              :class="passwordStrengthClass"
              :style="{ width: passwordStrength + '%' }"
              class="h-2 rounded-full transition-all duration-300"
            ></div>
          </div>
          <p v-if="form.password.length > 0" class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $t(passwordStrengthText) }}
          </p>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.confirm_password') }}</label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            required
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
            :class="{ 'border-red-500 dark:border-red-400': errors.password_confirmation }"
            :placeholder="$t('auth.confirm_password_placeholder')"
          />
          <p v-if="errors.password_confirmation" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.password_confirmation[0] }}</p>
        </div>

        <!-- Candidate Specific Fields -->
        <div v-if="registerType === 'candidate'" class="space-y-6">
          <div>
            <label for="resume" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.resume') }}</label>
            <input
              id="resume"
              type="file"
              @change="handleResumeUpload"
              accept=".pdf,.doc,.docx"
              class="block w-full text-sm text-gray-900 dark:text-gray-300
                file:mr-4 file:py-2 file:px-4
                file:rounded-md file:border-0
                file:text-sm file:font-semibold
                file:bg-blue-50 file:text-blue-700
                hover:file:bg-blue-100
                dark:file:bg-blue-900 dark:file:text-blue-300
                dark:hover:file:bg-blue-800
                focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
            <p v-if="errors.resume" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.resume[0] }}</p>
          </div>
          <!-- Add more candidate specific fields here if needed, e.g., desired job title -->
        </div>

        <!-- Employer Specific Fields -->
        <div v-if="registerType === 'employer'" class="space-y-6">
          <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.company_name') }}</label>
            <input
              id="company_name"
              v-model="form.company_name"
              type="text"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
              :class="{ 'border-red-500 dark:border-red-400': errors.company_name }"
              :placeholder="$t('auth.company_name_placeholder')"
            />
            <p v-if="errors.company_name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.company_name[0] }}</p>
          </div>
          <!-- Add more employer specific fields here if needed, e.g., industry, company size -->
        </div>

        <div>
          <button
            type="submit"
            :disabled="authStore.isLoading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out shadow-md"
          >
            <span v-if="!authStore.isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="h-6 w-6 text-blue-500 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm3.707 8.293a1 1 0 00-1.414-1.414L11 10.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </span>
            <svg v-else class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ authStore.isLoading ? $t('auth.registering') : $t('auth.register') }}
          </button>
        </div>

        <!-- Login Link -->
        <div class="text-center mt-6">
          <p class="text-base text-gray-600 dark:text-gray-400">
            {{ $t('auth.already_have_account') }}
            <router-link to="/login" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
              {{ $t('auth.sign_in') }}
            </router-link>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import type { RegisterCredentials } from '../../types/auth'

// Stores and Router
const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

// Form state
const registerType = ref<'candidate' | 'employer' | null>(null)
const form = reactive<RegisterCredentials>({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  password_confirmation: '',
  resume: null, // For candidate
  company_name: '', // For employer
  type: '', // 'candidate' or 'employer'
})

const errors = ref<Record<string, string[]>>({})

// Password Strength Indicator
const passwordStrength = ref(0)
const passwordStrengthText = computed(() => {
  if (passwordStrength.value < 40) return 'auth.password_weak'
  if (passwordStrength.value < 70) return 'auth.password_moderate'
  return 'auth.password_strong'
})
const passwordStrengthClass = computed(() => {
  if (passwordStrength.value < 40) return 'bg-red-500'
  if (passwordStrength.value < 70) return 'bg-orange-500'
  return 'bg-green-500'
})

function checkPasswordStrength() {
  let score = 0
  const password = form.password

  if (password.length >= 8) score += 20
  if (/[A-Z]/.test(password)) score += 20
  if (/[a-z]/.test(password)) score += 20
  if (/[0-9]/.test(password)) score += 20
  if (/[^A-Za-z0-9]/.test(password)) score += 20

  passwordStrength.value = Math.min(score, 100)
}

// Methods
function selectType(type: 'candidate' | 'employer') {
  registerType.value = type
  form.type = type
  // Clear previous errors when switching type
  errors.value = {}
  authStore.clearError()
}

function handleResumeUpload(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files && target.files[0]) {
    form.resume = target.files[0]
  } else {
    form.resume = null
  }
}

async function handleRegister() {
  errors.value = {}
  authStore.clearError()

  // Ensure type is selected
  if (!form.type) {
    authStore.setError($t('auth.select_account_type_error'))
    return
  }

  try {
    const success = await authStore.register(form)
    
    if (success) {
      console.log('Registration successful, redirecting...')
      // Redirect to dashboard or a success page
      await router.push('/dashboard')
    }
  } catch (error: any) {
    console.error('Registration failed:', error)
    
    // Handle validation errors
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  }
}

onMounted(() => {
  // Check for 'type' query parameter from external links (e.g., home page buttons)
  const typeParam = route.query.type
  if (typeParam === 'candidate' || typeParam === 'employer') {
    selectType(typeParam)
  }
})
</script>
