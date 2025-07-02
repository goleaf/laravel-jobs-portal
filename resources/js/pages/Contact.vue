<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-5xl font-bold mb-6">Contact Us</h1>
          <p class="text-xl text-blue-100 leading-relaxed">
            Have questions or need support? We're here to help! Reach out to us through any of the 
            channels below and we'll get back to you as soon as possible.
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Options Section -->
    <section class="py-16">
      <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto mb-16">
          <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-blue-600 mb-4">
              <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Email Support</h3>
            <p class="text-gray-600 mb-4">
              Send us an email and we'll respond within 24 hours.
            </p>
            <a 
              href="mailto:support@jobportal.com" 
              class="text-blue-600 hover:text-blue-800 font-medium"
            >
              support@jobportal.com
            </a>
          </div>
          
          <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-green-600 mb-4">
              <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Phone Support</h3>
            <p class="text-gray-600 mb-4">
              Call us during business hours for immediate assistance.
            </p>
            <a 
              href="tel:+1-555-123-4567" 
              class="text-green-600 hover:text-green-800 font-medium"
            >
              +1 (555) 123-4567
            </a>
            <p class="text-sm text-gray-500 mt-2">Mon-Fri: 9AM-6PM EST</p>
          </div>
          
          <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="text-purple-600 mb-4">
              <svg class="w-12 h-12 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Visit Our Office</h3>
            <p class="text-gray-600 mb-4">
              Come visit us at our headquarters for in-person support.
            </p>
            <address class="text-purple-600 not-italic">
              123 Business Avenue<br>
              Suite 500<br>
              New York, NY 10001
            </address>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16 bg-white">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Send Us a Message</h2>
            <p class="text-gray-600">
              Fill out the form below and we'll get back to you as soon as possible.
            </p>
          </div>
          
          <form @submit.prevent="submitForm" class="bg-gray-50 rounded-lg p-8">
            <div class="grid md:grid-cols-2 gap-6 mb-6">
              <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                  First Name *
                </label>
                <input
                  type="text"
                  id="firstName"
                  v-model="form.firstName"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your first name"
                >
              </div>
              
              <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                  Last Name *
                </label>
                <input
                  type="text"
                  id="lastName"
                  v-model="form.lastName"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your last name"
                >
              </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 mb-6">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Email Address *
                </label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  required
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your email address"
                >
              </div>
              
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                  Phone Number
                </label>
                <input
                  type="tel"
                  id="phone"
                  v-model="form.phone"
                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  placeholder="Enter your phone number"
                >
              </div>
            </div>
            
            <div class="mb-6">
              <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                Subject *
              </label>
              <select
                id="subject"
                v-model="form.subject"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Select a subject</option>
                <option value="general">General Inquiry</option>
                <option value="support">Technical Support</option>
                <option value="business">Business Partnership</option>
                <option value="feedback">Feedback</option>
                <option value="other">Other</option>
              </select>
            </div>
            
            <div class="mb-6">
              <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                Message *
              </label>
              <textarea
                id="message"
                v-model="form.message"
                required
                rows="6"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical"
                placeholder="Enter your message here..."
              ></textarea>
            </div>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input
                  type="checkbox"
                  id="newsletter"
                  v-model="form.newsletter"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                >
                <label for="newsletter" class="ml-2 text-sm text-gray-600">
                  Subscribe to our newsletter for job market updates
                </label>
              </div>
              
              <button
                type="submit"
                :disabled="isSubmitting"
                class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <span v-if="!isSubmitting">Send Message</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Sending...
                </span>
              </button>
            </div>
          </form>
          
          <!-- Success Message -->
          <div v-if="showSuccessMessage" class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
              </svg>
              Thank you for your message! We'll get back to you within 24 hours.
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
          <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Frequently Asked Questions</h2>
            <p class="text-gray-600">
              Find quick answers to common questions about our platform.
            </p>
          </div>
          
          <div class="space-y-6">
            <div v-for="faq in faqs" :key="faq.id" class="bg-white rounded-lg shadow-sm">
              <button
                @click="toggleFaq(faq.id)"
                class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-inset"
              >
                <span class="font-medium text-gray-800">{{ faq.question }}</span>
                <svg
                  :class="{ 'rotate-180': faq.isOpen }"
                  class="w-5 h-5 text-gray-500 transform transition-transform duration-200"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div v-if="faq.isOpen" class="px-6 pb-4">
                <p class="text-gray-600">{{ faq.answer }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useHead } from '@vueuse/head'

// Set page metadata
useHead({
  title: 'Contact Us - Job Portal',
  meta: [
    {
      name: 'description',
      content: 'Get in touch with our team for support, partnerships, or general inquiries. We\'re here to help you succeed in your career journey.'
    },
    {
      property: 'og:title',
      content: 'Contact Us - Job Portal'
    },
    {
      property: 'og:description',
      content: 'Get in touch with our team for support, partnerships, or general inquiries.'
    }
  ]
})

// Form state
const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
  newsletter: false
})

const isSubmitting = ref(false)
const showSuccessMessage = ref(false)

// FAQ data
const faqs = ref([
  {
    id: 1,
    question: 'How do I create an account on the platform?',
    answer: 'You can create an account by clicking the "Sign Up" button on our homepage and following the registration process. It only takes a few minutes to get started.',
    isOpen: false
  },
  {
    id: 2,
    question: 'Is the platform free to use?',
    answer: 'Yes, job seekers can browse and apply for jobs completely free. Employers have access to both free and premium features depending on their needs.',
    isOpen: false
  },
  {
    id: 3,
    question: 'How do I post a job as an employer?',
    answer: 'After creating an employer account, you can post jobs through your dashboard. Simply click "Post a Job" and fill out the job details form.',
    isOpen: false
  },
  {
    id: 4,
    question: 'How can I edit my profile?',
    answer: 'You can edit your profile by logging into your account and clicking on "Profile" in your dashboard. Make sure to save your changes.',
    isOpen: false
  },
  {
    id: 5,
    question: 'What should I do if I forgot my password?',
    answer: 'Click on "Forgot Password" on the login page and enter your email address. We\'ll send you instructions to reset your password.',
    isOpen: false
  }
])

// Methods
const submitForm = async () => {
  isSubmitting.value = true
  
  try {
    // Simulate form submission
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Reset form
    Object.keys(form).forEach(key => {
      if (key === 'newsletter') {
        form[key] = false
      } else {
        form[key] = ''
      }
    })
    
    // Show success message
    showSuccessMessage.value = true
    setTimeout(() => {
      showSuccessMessage.value = false
    }, 5000)
    
  } catch (error) {
    console.error('Error submitting form:', error)
  } finally {
    isSubmitting.value = false
  }
}

const toggleFaq = (id: number) => {
  const faq = faqs.value.find(f => f.id === id)
  if (faq) {
    faq.isOpen = !faq.isOpen
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style> 