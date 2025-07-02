<template>
  <footer class="bg-gray-900 text-white">
    <!-- Main Footer Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Company Info -->
        <div class="col-span-1 lg:col-span-2">
          <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6" />
              </svg>
            </div>
            <span class="text-2xl font-bold">JobPortal</span>
          </div>
          <p class="text-gray-300 mb-6 max-w-md">
            Your trusted partner in finding the perfect job opportunity. Connect with top employers and discover your dream career path.
          </p>
          
          <!-- Newsletter Signup -->
          <div class="mb-6">
            <h4 class="text-lg font-semibold mb-3">Stay Updated</h4>
            <form @submit.prevent="subscribeNewsletter" class="flex flex-col sm:flex-row gap-3">
              <input
                v-model="emailSubscription"
                type="email"
                placeholder="Enter your email"
                required
                class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-white placeholder-gray-400"
              />
              <button
                type="submit"
                :disabled="isSubscribing"
                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 rounded-lg font-medium transition-colors duration-200 whitespace-nowrap"
              >
                <span v-if="!isSubscribing">Subscribe</span>
                <span v-else class="flex items-center">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Subscribing...
                </span>
              </button>
            </form>
          </div>

          <!-- Social Media Links -->
          <div class="flex space-x-4">
            <a
              v-for="social in socialLinks"
              :key="social.name"
              :href="social.url"
              :aria-label="social.name"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2 bg-gray-800 hover:bg-gray-700 rounded-lg transition-colors duration-200"
            >
              <component :is="social.icon" class="h-5 w-5" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
          <ul class="space-y-3">
            <li v-for="link in quickLinks" :key="link.name">
              <router-link
                :to="link.href"
                class="text-gray-300 hover:text-white transition-colors duration-200"
              >
                {{ link.name }}
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Job Categories -->
        <div>
          <h4 class="text-lg font-semibold mb-4">Popular Categories</h4>
          <ul class="space-y-3">
            <li v-for="category in popularCategories" :key="category.name">
              <router-link
                :to="{ name: 'jobs.index', query: { category: category.slug } }"
                class="text-gray-300 hover:text-white transition-colors duration-200 flex items-center justify-between"
              >
                <span>{{ category.name }}</span>
                <span class="text-xs bg-gray-800 text-gray-300 px-2 py-1 rounded">
                  {{ category.count }}
                </span>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Bottom Footer -->
    <div class="border-t border-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
          <!-- Copyright -->
          <div class="text-gray-400 text-sm">
            © {{ currentYear }} JobPortal. All rights reserved.
          </div>

          <!-- Legal Links -->
          <div class="flex space-x-6 text-sm">
            <router-link
              to="/privacy"
              class="text-gray-400 hover:text-white transition-colors duration-200"
            >
              Privacy Policy
            </router-link>
            <router-link
              to="/terms"
              class="text-gray-400 hover:text-white transition-colors duration-200"
            >
              Terms of Service
            </router-link>
            <router-link
              to="/contact"
              class="text-gray-400 hover:text-white transition-colors duration-200"
            >
              Contact Us
            </router-link>
          </div>

          <!-- Language & Region -->
          <div class="flex items-center space-x-2 text-sm text-gray-400">
            <GlobeAltIcon class="h-4 w-4" />
            <span>{{ currentLanguage.toUpperCase() }} - {{ currentRegion }}</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { GlobeAltIcon } from '@heroicons/vue/24/outline'

// Social media icons (you would import these from your icon library)
const FacebookIcon = { template: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>' }
const TwitterIcon = { template: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>' }
const LinkedInIcon = { template: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>' }
const InstagramIcon = { template: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.618 5.367 11.986 11.988 11.986 6.618 0 11.986-5.368 11.986-11.986C24.003 5.367 18.635.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.326-1.297L3.891 16.923c.878.807 2.029 1.297 3.326 1.297.65 0 1.235-.129 1.756-.35L7.741 16.638c-.521.221-1.106.35-1.756.35zm7.119 0c-.65 0-1.235-.129-1.756-.35l1.232 1.232c.521.221 1.106.35 1.756.35 1.297 0 2.448-.49 3.326-1.297l-1.232-1.232c-.878.807-2.029 1.297-3.326 1.297z"/></svg>' }

const { locale } = useI18n()

// State
const emailSubscription = ref('')
const isSubscribing = ref(false)

// Computed
const currentYear = computed(() => new Date().getFullYear())
const currentLanguage = computed(() => locale.value)
const currentRegion = computed(() => {
  // You could make this dynamic based on user location
  return locale.value === 'lt' ? 'Lithuania' : 'Global'
})

// Data
const socialLinks = ref([
  {
    name: 'Facebook',
    url: 'https://facebook.com/jobportal',
    icon: FacebookIcon
  },
  {
    name: 'Twitter',
    url: 'https://twitter.com/jobportal',
    icon: TwitterIcon
  },
  {
    name: 'LinkedIn',
    url: 'https://linkedin.com/company/jobportal',
    icon: LinkedInIcon
  },
  {
    name: 'Instagram',
    url: 'https://instagram.com/jobportal',
    icon: InstagramIcon
  }
])

const quickLinks = ref([
  { name: 'About Us', href: '/about' },
  { name: 'Contact', href: '/contact' },
  { name: 'How It Works', href: '/how-it-works' },
  { name: 'Success Stories', href: '/success-stories' },
  { name: 'Career Advice', href: '/career-advice' },
  { name: 'Help Center', href: '/help' }
])

const popularCategories = ref([
  { name: 'Technology', slug: 'technology', count: '1,234' },
  { name: 'Healthcare', slug: 'healthcare', count: '856' },
  { name: 'Finance', slug: 'finance', count: '642' },
  { name: 'Education', slug: 'education', count: '398' },
  { name: 'Marketing', slug: 'marketing', count: '567' },
  { name: 'Sales', slug: 'sales', count: '445' }
])

// Methods
const subscribeNewsletter = async () => {
  if (!emailSubscription.value.trim()) return

  isSubscribing.value = true
  
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Show success message (you would integrate with your toast system)
    console.log('Newsletter subscription successful:', emailSubscription.value)
    
    // Reset form
    emailSubscription.value = ''
  } catch (error) {
    console.error('Newsletter subscription failed:', error)
  } finally {
    isSubscribing.value = false
  }
}
</script> 