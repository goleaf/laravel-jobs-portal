<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <HeroSection
      title="Contact Us"
      subtitle="Have questions or need support? We're here to help! Reach out to us through any of the channels below and we'll get back to you as soon as possible."
      size="md"
      theme="primary"
      :show-actions="false"
    />

    <!-- Contact Options Section -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8 mb-16">
          <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Email Support</h3>
            <p class="text-gray-600 mb-6">
              Send us an email and we'll respond within 24 hours.
            </p>
            <BaseButton
              tag="a"
              href="mailto:support@jobportal.com"
              variant="primary"
              class="w-full"
            >
              support@jobportal.com
            </BaseButton>
          </div>
          
          <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Phone Support</h3>
            <p class="text-gray-600 mb-6">
              Call us during business hours for immediate assistance.
            </p>
            <BaseButton
              tag="a"
              href="tel:+1-555-123-4567"
              variant="secondary"
              class="w-full mb-2"
            >
              +1 (555) 123-4567
            </BaseButton>
            <Badge text="Mon-Fri: 9AM-6PM EST" variant="success" size="sm" />
          </div>
          
          <div class="bg-white rounded-lg shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Visit Our Office</h3>
            <p class="text-gray-600 mb-6">
              Come visit us at our headquarters for in-person support.
            </p>
            <div class="text-gray-700">
              <p class="font-medium">123 Business Avenue</p>
              <p>Suite 500</p>
              <p>New York, NY 10001</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-16 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Send Us a Message</h2>
          <p class="text-lg text-gray-600">
            Fill out the form below and we'll get back to you as soon as possible.
          </p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-8">
          <form @submit.prevent="submitForm" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                  First Name *
                </label>
                <BaseInput
                  id="firstName"
                  v-model="form.firstName"
                  type="text"
                  placeholder="Enter your first name"
                  required
                />
              </div>
              
              <div>
                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                  Last Name *
                </label>
                <BaseInput
                  id="lastName"
                  v-model="form.lastName"
                  type="text"
                  placeholder="Enter your last name"
                  required
                />
              </div>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                  Email Address *
                </label>
                <BaseInput
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="Enter your email address"
                  required
                />
              </div>
              
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                  Phone Number
                </label>
                <BaseInput
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  placeholder="Enter your phone number"
                />
              </div>
            </div>
            
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                Subject *
              </label>
              <select
                id="subject"
                v-model="form.subject"
                required
                class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white"
              >
                <option value="">Select a subject</option>
                <option value="general">General Inquiry</option>
                <option value="support">Technical Support</option>
                <option value="business">Business Partnership</option>
                <option value="feedback">Feedback</option>
                <option value="other">Other</option>
              </select>
            </div>
            
            <div>
              <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                Message *
              </label>
              <textarea
                id="message"
                v-model="form.message"
                required
                rows="6"
                class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-vertical"
                placeholder="Enter your message here..."
              ></textarea>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
              <div class="flex items-start">
                <input
                  type="checkbox"
                  id="newsletter"
                  v-model="form.newsletter"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-0.5"
                >
                <label for="newsletter" class="ml-2 text-sm text-gray-600">
                  Subscribe to our newsletter for job market updates
                </label>
              </div>
              
              <BaseButton
                type="submit"
                variant="primary"
                size="lg"
                :loading="isSubmitting"
                class="sm:min-w-[200px]"
              >
                Send Message
              </BaseButton>
            </div>
          </form>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
          <p class="text-lg text-gray-600">
            Find quick answers to common questions about our platform.
          </p>
        </div>

        <div class="space-y-6">
          <div v-for="faq in faqs" :key="faq.id" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ faq.question }}</h3>
            <p class="text-gray-600">{{ faq.answer }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Signup Section -->
    <NewsletterSignup />
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { useHead } from '@/composables/useHead'
import HeroSection from '../components/ui/HeroSection.vue'
import BaseInput from '../components/ui/BaseInput.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import Badge from '../components/ui/Badge.vue'
import NewsletterSignup from '../components/ui/NewsletterSignup.vue'

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
    question: "How do I create an account?",
    answer: "You can create an account by clicking the 'Sign Up' button in the top navigation. Fill out your information and verify your email address to get started."
  },
  {
    id: 2,
    question: "Is your service free for job seekers?",
    answer: "Yes! Our platform is completely free for job seekers. You can search jobs, apply to positions, and manage your profile at no cost."
  },
  {
    id: 3,
    question: "How do I upload my resume?",
    answer: "After creating your account, go to your profile settings and click 'Upload Resume'. You can upload PDF, DOC, or DOCX files up to 5MB in size."
  },
  {
    id: 4,
    question: "Can I apply to multiple jobs at once?",
    answer: "Yes, you can apply to as many jobs as you'd like. Each application is tracked separately in your dashboard."
  },
  {
    id: 5,
    question: "How do I contact employers directly?",
    answer: "You can contact employers through our messaging system once you've applied to a position or if they've reached out to you first."
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
</script>

<style scoped>
/* Additional styles if needed */
</style> 