<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 p-8 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
      <div>
        <img class="mx-auto h-16 w-auto" src="/images/logo.svg" alt="Job Portal Logo">
        <h2 class="mt-6 text-center text-4xl font-extrabold text-gray-900 dark:text-white">
          {{ $t('auth.forgot_password_title') }}
        </h2>
        <p class="mt-2 text-center text-lg text-gray-600 dark:text-gray-400">
          {{ $t('auth.forgot_password_subtitle') }}
        </p>
      </div>
      
      <!-- Success Message -->
      <div v-if="successMessage" class="rounded-md bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-700">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-green-800 dark:text-green-300">
              {{ $t('auth.success') }}
            </h3>
            <p class="mt-1 text-sm text-green-700 dark:text-green-200">
              {{ successMessage }}
            </p>
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-700">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-red-400 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">
              {{ $t('auth.error') }}
            </h3>
            <p class="mt-1 text-sm text-red-700 dark:text-red-200">
              {{ errorMessage }}
            </p>
          </div>
        </div>
      </div>

      <form class="mt-8 space-y-6" @submit.prevent="onSubmit">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $t('auth.email_address') }}</label>
          <input
            id="email"
            v-model="email"
            type="email"
            required
            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
            :class="{ 'border-red-500 dark:border-red-400': emailError }"
            :placeholder="$t('auth.email_placeholder')"
          />
          <p v-if="emailError" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ emailError }}</p>
        </div>

        <div>
          <button
            type="submit"
            :disabled="isLoading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-lg font-semibold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out shadow-md"
          >
            <span v-if="!isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="h-6 w-6 text-blue-500 group-hover:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M3 8V6a4 4 0 018 0v2h4a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V10a2 2 0 012-2h4zm8-2v2H4V6a2 2 0 012-2h2a2 2 0 012 2zm-4 4a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5a1 1 0 011-1h4z" clip-rule="evenodd"/>
              </svg>
            </span>
            <svg v-else class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isLoading ? $t('auth.sending_link') : $t('auth.send_reset_link') }}
          </button>
        </div>
      </form>

      <div class="text-center mt-6">
        <p class="text-base text-gray-600 dark:text-gray-400">
          {{ $t('auth.remember_password') }}
          <router-link to="/login" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
            {{ $t('auth.sign_in') }}
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
// import BaseInput from '@/components/BaseInput.vue' // No longer needed, using native input with TailwindCSS
// import BaseButton from '@/components/BaseButton.vue' // No longer needed, using native button with TailwindCSS

const email = ref('')
const emailError = ref('')
const successMessage = ref('')
const errorMessage = ref('')
const isLoading = ref(false)

const onSubmit = async () => {
  emailError.value = ''
  successMessage.value = ''
  errorMessage.value = ''
  isLoading.value = true

  if (!email.value) {
    emailError.value = $t('auth.email_required')
    isLoading.value = false
    return
  }
  
  try {
    // Simulate API call for sending password reset link
    // In a real application, you would make an actual API call here
    await new Promise(resolve => setTimeout(resolve, 1500)); 

    successMessage.value = $t('auth.reset_link_sent')
    email.value = '' // Clear email field on success
  } catch (error: any) {
    console.error('Forgot password failed:', error)
    errorMessage.value = error.response?.data?.message || $t('auth.reset_link_error')
  } finally {
    isLoading.value = false
  }
}
</script> 