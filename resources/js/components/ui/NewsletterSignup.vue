<template>
  <section class="bg-gradient-to-r from-indigo-600 to-purple-600 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h2 class="text-3xl font-bold text-white mb-4">
          Stay Updated with Latest Jobs
        </h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
          Get personalized job recommendations, industry insights, and career tips delivered straight to your inbox.
        </p>

        <!-- Newsletter Form -->
        <form @submit.prevent="handleSubmit" class="max-w-md mx-auto">
          <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
              <BaseInput
                v-model="email"
                type="email"
                placeholder="Enter your email address"
                :left-icon="EnvelopeIcon"
                size="lg"
                :error="emailError"
                class="w-full"
                required
              />
            </div>
            <BaseButton
              type="submit"
              variant="secondary"
              size="lg"
              :loading="isSubmitting"
              class="px-8 bg-white text-indigo-600 hover:bg-gray-50"
            >
              Subscribe
            </BaseButton>
          </div>
          
          <!-- Error Message -->
          <div v-if="error" class="mt-2 text-red-200 text-sm">
            {{ error }}
          </div>
          
          <!-- Success Message -->
          <div v-if="success" class="mt-2 text-green-200 text-sm">
            {{ success }}
          </div>
        </form>

        <!-- Privacy Notice -->
        <p class="text-indigo-200 text-sm mt-4">
          By subscribing, you agree to our 
          <router-link to="/privacy" class="underline hover:text-white">Privacy Policy</router-link>
          and 
          <router-link to="/terms" class="underline hover:text-white">Terms of Service</router-link>.
          You can unsubscribe at any time.
        </p>

        <!-- Newsletter Benefits -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12 max-w-4xl mx-auto">
          <div class="text-center">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-3">
              <BellIcon class="w-6 h-6 text-white" />
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Job Alerts</h3>
            <p class="text-indigo-200 text-sm">
              Get notified when new jobs matching your preferences are posted.
            </p>
          </div>
          
          <div class="text-center">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-3">
              <ChartBarIcon class="w-6 h-6 text-white" />
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Market Insights</h3>
            <p class="text-indigo-200 text-sm">
              Stay informed about salary trends, hiring patterns, and industry news.
            </p>
          </div>
          
          <div class="text-center">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mx-auto mb-3">
              <LightBulbIcon class="w-6 h-6 text-white" />
            </div>
            <h3 class="text-lg font-semibold text-white mb-2">Career Tips</h3>
            <p class="text-indigo-200 text-sm">
              Receive expert advice on resume writing, interviews, and career growth.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import {
  EnvelopeIcon,
  BellIcon,
  ChartBarIcon,
  LightBulbIcon
} from '@heroicons/vue/24/outline'
import BaseInput from './BaseInput.vue'
import BaseButton from './BaseButton.vue'

// State
const email = ref('')
const isSubmitting = ref(false)
const emailError = ref('')
const error = ref('')
const success = ref('')

// Email validation
const validateEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

// Form submission
const handleSubmit = async () => {
  // Reset messages
  emailError.value = ''
  error.value = ''
  success.value = ''

  // Validate email
  if (!email.value) {
    emailError.value = 'Email address is required'
    return
  }

  if (!validateEmail(email.value)) {
    emailError.value = 'Please enter a valid email address'
    return
  }

  isSubmitting.value = true

  try {
    // Simulate API call to subscribe to newsletter
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Simulate success/error response
    const isSuccess = Math.random() > 0.1 // 90% success rate for demo
    
    if (isSuccess) {
      success.value = 'Thank you for subscribing! Check your email for confirmation.'
      email.value = ''
    } else {
      error.value = 'Something went wrong. Please try again later.'
    }
  } catch (err) {
    error.value = 'Network error. Please check your connection and try again.'
  } finally {
    isSubmitting.value = false
  }
}
</script> 