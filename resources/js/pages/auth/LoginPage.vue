<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
      <div>
        <img class="mx-auto h-16 w-auto" src="/images/logo.svg" alt="Job Portal Logo">
        <h2 class="mt-6 text-center text-4xl font-extrabold text-gray-900 dark:text-white">
          {{ $t('auth.sign_in_to_account') }}
        </h2>
        <p class="mt-2 text-center text-lg text-gray-600 dark:text-gray-400">
          {{ $t('auth.welcome_to_job_portal') }}
        </p>
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
              {{ $t('auth.authentication_error') }}
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

      <!-- Login Form -->
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.email_address') }}</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              autocomplete="email"
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
              autocomplete="current-password"
              required
              class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
              :class="{ 'border-red-500 dark:border-red-400': errors.password }"
              :placeholder="$t('auth.password_placeholder')"
            />
            <p v-if="errors.password" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.password[0] }}</p>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember"
              v-model="form.remember"
              type="checkbox"
              class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600 rounded-md transition duration-150 ease-in-out"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
              {{ $t('auth.remember_me') }}
            </label>
          </div>

          <div class="text-sm">
            <router-link to="/forgot-password" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
              {{ $t('auth.forgot_password') }}
            </router-link>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="authStore.isLoading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out shadow-md"
          >
            <span v-if="!authStore.isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="h-6 w-6 text-blue-500 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M18 8V6a4 4 0 00-8 0v2H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V10a2 2 0 00-2-2h-4zM10 7a2 2 0 114 0v2h-4V7zm6 3a1 1 0 011 1v5a1 1 0 01-1 1H6a1 1 0 01-1-1v-5a1 1 0 011-1h10z" clip-rule="evenodd"/>
              </svg>
            </span>
            <svg v-else class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ authStore.isLoading ? $t('auth.signing_in') : $t('auth.sign_in') }}
          </button>
        </div>

        <!-- Test Credentials -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ $t('auth.test_credentials') }}:</h3>
          <div class="grid grid-cols-1 gap-3 text-sm">
            <button
              type="button"
              @click="fillTestCredentials('admin')"
              class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out text-gray-800 dark:text-gray-200"
            >
              <strong>{{ $t('auth.admin') }}:</strong> admin@test.com / password
            </button>
            <button
              type="button"
              @click="fillTestCredentials('employer')"
              class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out text-gray-800 dark:text-gray-200"
            >
              <strong>{{ $t('auth.employer') }}:</strong> employer@test.com / password
            </button>
            <button
              type="button"
              @click="fillTestCredentials('candidate')"
              class="w-full text-left p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition duration-150 ease-in-out text-gray-800 dark:text-gray-200"
            >
              <strong>{{ $t('auth.candidate') }}:</strong> candidate@test.com / password
            </button>
          </div>
        </div>
      </form>

      <!-- Register Link -->
      <div class="text-center mt-6">
        <p class="text-base text-gray-600 dark:text-gray-400">
          {{ $t('auth.no_account') }}
          <router-link to="/register" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
            {{ $t('auth.sign_up_here') }}
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import type { LoginCredentials } from '../../types/auth'

// Stores and Router
const authStore = useAuthStore()
const router = useRouter()

// Form state
const form = reactive<LoginCredentials>({
  email: '',
  password: '',
  remember: false
})

const errors = ref<Record<string, string[]>>({})

// Test credentials
const testCredentials = {
  admin: { email: 'admin@test.com', password: 'password' },
  employer: { email: 'employer@test.com', password: 'password' },
  candidate: { email: 'candidate@test.com', password: 'password' }
}

// Methods
async function handleLogin() {
  errors.value = {}
  authStore.clearError()

  try {
    const success = await authStore.login(form)
    
    if (success) {
      console.log('Login successful, redirecting...')
      
      // Redirect based on user role
      if (authStore.isAdmin) {
        await router.push('/admin/dashboard')
      } else {
        await router.push('/dashboard')
      }
    }
  } catch (error: any) {
    console.error('Login failed:', error)
    
    // Handle validation errors
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
  }
}

function fillTestCredentials(role: keyof typeof testCredentials) {
  const credentials = testCredentials[role]
  form.email = credentials.email
  form.password = credentials.password
  authStore.clearError()
  errors.value = {}
}
</script> 