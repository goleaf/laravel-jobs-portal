<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <HeroSection
      title="Privacy Policy"
      subtitle="We are committed to protecting your privacy and ensuring the security of your personal information. This policy explains how we collect, use, and safeguard your data."
      size="md"
      theme="primary"
      :show-actions="false"
    >
      <template #content>
        <div class="text-center">
          <Badge 
            :text="`Last Updated: ${lastUpdated}`" 
            variant="secondary" 
            size="lg" 
            class="bg-white/20 text-white border-white/30"
          />
        </div>
      </template>
    </HeroSection>

    <!-- Quick Navigation -->
    <section class="py-8 bg-white border-b border-gray-200">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Navigation</h2>
        <div class="flex flex-wrap gap-3">
          <BaseButton
            v-for="section in tableOfContents"
            :key="section.id"
            variant="outline"
            size="sm"
            @click="scrollToSection(section.id)"
          >
            {{ section.title }}
          </BaseButton>
        </div>
      </div>
    </section>

    <!-- Privacy Content -->
    <section class="py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Introduction -->
        <div id="introduction" class="mb-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Introduction</h2>
          <div class="prose max-w-none">
            <p class="text-gray-600 mb-4">
              This Privacy Policy describes how we collect, use, disclose, and safeguard your personal information when you use our job portal platform ("Service"). We respect your privacy and are committed to protecting your personal data.
            </p>
            <p class="text-gray-600 mb-4">
              This policy applies to all information collected through our Service and any related services, sales, marketing, or events.
            </p>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
              <div class="flex items-start">
                <div class="w-6 h-6 text-blue-600 mr-3 mt-0.5">
                  <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-blue-800 font-medium mb-1">Important Notice</h3>
                  <p class="text-blue-700 text-sm">
                    By using our Service, you agree to the collection and use of information in accordance with this Privacy Policy.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Information We Collect -->
        <div id="information-collected" class="mb-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">2. Information We Collect</h2>
          
          <div class="grid md:grid-cols-2 gap-8">
            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                  <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                </div>
                Personal Information
              </h3>
              <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Full name and contact information
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Professional information (resume, work experience)
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Account credentials and preferences
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Communication records
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-blue-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Payment information for premium services
                </li>
              </ul>
            </div>

            <div>
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                  <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                  </svg>
                </div>
                Usage Information
              </h3>
              <ul class="space-y-2 text-gray-600">
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  IP address and device information
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Browser type and operating system
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Pages viewed and time spent
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Search queries and job applications
                </li>
                <li class="flex items-start">
                  <span class="w-1.5 h-1.5 bg-green-600 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                  Referral sources and exit pages
                </li>
              </ul>
            </div>
          </div>

          <div class="mt-8 p-4 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-900 mb-2">Cookies and Tracking Technologies</h4>
            <p class="text-gray-600 text-sm">
              We use cookies, web beacons, and similar tracking technologies to collect information about your browsing behavior and improve our Service.
            </p>
          </div>
        </div>

        <!-- How We Use Information -->
        <div id="how-we-use" class="mb-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">3. How We Use Your Information</h2>
          
          <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
              <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Service Provision</h3>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>Account management</li>
                <li>Job matching</li>
                <li>Customer support</li>
                <li>Payment processing</li>
              </ul>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                  <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Communication</h3>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>Job alerts</li>
                <li>Service updates</li>
                <li>Support responses</li>
                <li>Promotional materials</li>
              </ul>
            </div>

            <div class="text-center">
              <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-3">Platform Improvement</h3>
              <ul class="text-sm text-gray-600 space-y-1">
                <li>Usage analytics</li>
                <li>Feature development</li>
                <li>Security enhancement</li>
                <li>Fraud prevention</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Continue with remaining sections -->
        <div class="space-y-12">
          <!-- Add remaining sections here with similar modern styling -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Need More Information?</h2>
            <p class="text-gray-600 mb-6">
              If you have questions about this Privacy Policy or our data practices, we're here to help.
            </p>
            <BaseButton variant="primary" :to="{ name: 'contact' }" tag="router-link">
              Contact Us
            </BaseButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Signup -->
    <NewsletterSignup />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useHead } from '@vueuse/head'
import HeroSection from '../components/ui/HeroSection.vue'
import BaseButton from '../components/ui/BaseButton.vue'
import Badge from '../components/ui/Badge.vue'
import NewsletterSignup from '../components/ui/NewsletterSignup.vue'

// Set page metadata
useHead({
  title: 'Privacy Policy - Job Portal',
  meta: [
    {
      name: 'description',
      content: 'Learn how we collect, use, and protect your personal information on our job portal platform. Your privacy is our priority.'
    },
    {
      property: 'og:title',
      content: 'Privacy Policy - Job Portal'
    },
    {
      property: 'og:description',
      content: 'Learn how we collect, use, and protect your personal information on our job portal platform.'
    }
  ]
})

// Data
const lastUpdated = ref('January 1, 2025')

const tableOfContents = ref([
  { id: 'introduction', title: 'Introduction' },
  { id: 'information-collected', title: 'Information We Collect' },
  { id: 'how-we-use', title: 'How We Use Information' },
  { id: 'information-sharing', title: 'Information Sharing' },
  { id: 'data-security', title: 'Data Security' },
  { id: 'your-rights', title: 'Your Rights' },
  { id: 'cookies', title: 'Cookies & Tracking' },
  { id: 'data-retention', title: 'Data Retention' },
  { id: 'third-party', title: 'Third-Party Links' },
  { id: 'policy-updates', title: 'Policy Updates' },
  { id: 'contact', title: 'Contact Us' }
])

// Methods
const scrollToSection = (sectionId: string) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<style scoped>
.prose {
  @apply text-gray-600 leading-relaxed;
}

.prose h3 {
  @apply text-lg font-semibold text-gray-800 mb-2;
}

.prose h4 {
  @apply font-medium text-gray-800;
}

.prose p {
  @apply mb-4;
}

.prose ul {
  @apply list-disc list-inside mb-4 space-y-2;
}
</style> 