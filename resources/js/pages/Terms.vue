<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <HeroSection
      title="Terms of Service"
      subtitle="These terms govern your use of our job portal platform. Please read them carefully to understand your rights and responsibilities as a user."
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

    <!-- Terms Content -->
    <section class="py-16">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Acceptance of Terms -->
        <div id="acceptance" class="mb-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">1. Acceptance of Terms</h2>
          <div class="prose max-w-none">
            <p class="text-gray-600 mb-4">
              By accessing and using this job portal platform ("Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
            </p>
            <p class="text-gray-600 mb-4">
              These Terms of Service ("Terms") apply to all visitors, users, and others who access or use the Service, including but not limited to job seekers, employers, and companies.
            </p>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
              <div class="flex items-start">
                <div class="w-6 h-6 text-amber-600 mr-3 mt-0.5">
                  <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="text-amber-800 font-medium mb-1">Terms Updates</h3>
                  <p class="text-amber-700 text-sm">
                    We reserve the right to modify these Terms at any time. Continued use after modifications constitutes acceptance of updated Terms.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Service Description -->
        <div id="service-description" class="mb-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">2. Service Description</h2>
          <p class="text-gray-600 mb-6">
            Our platform provides an online job portal service that connects job seekers with potential employers. The Service includes:
          </p>
          
          <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                  <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900">Job Management</h3>
                  <p class="text-sm text-gray-600">Job posting, browsing, and application features</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                  <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900">Profile Management</h3>
                  <p class="text-sm text-gray-600">Creation and management for job seekers and employers</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                  <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900">Communication Tools</h3>
                  <p class="text-sm text-gray-600">Messaging between job seekers and employers</p>
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <div class="flex items-start">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                  <svg class="w-4 h-4 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900">Search & Filter</h3>
                  <p class="text-sm text-gray-600">Advanced search and filtering functionality</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                  <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900">Analytics & Reports</h3>
                  <p class="text-sm text-gray-600">Performance tracking and reporting tools</p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                  <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div>
                  <h3 class="font-medium text-gray-900">Application Management</h3>
                  <p class="text-sm text-gray-600">Submit and track job applications</p>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-gray-600 text-sm">
              <strong>Disclaimer:</strong> We act as an intermediary platform and do not guarantee employment outcomes, job placements, or the quality of jobs or candidates listed on our platform.
            </p>
          </div>
        </div>

        <!-- User Conduct -->
        <div id="user-conduct" class="mb-12 bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6">4. User Conduct</h2>
          <p class="text-gray-600 mb-6">You agree not to use the Service to:</p>
          
          <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-3">
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Post false, misleading, or inaccurate information</span>
              </div>
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Violate any applicable laws or regulations</span>
              </div>
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Infringe upon the rights of others</span>
              </div>
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Post discriminatory content</span>
              </div>
            </div>
            <div class="space-y-3">
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Engage in harassment or spam</span>
              </div>
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Attempt unauthorized system access</span>
              </div>
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Use automated scraping systems</span>
              </div>
              <div class="flex items-start">
                <span class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3 flex-shrink-0"></span>
                <span class="text-gray-600 text-sm">Post non-genuine job listings</span>
              </div>
            </div>
          </div>

          <div class="mt-6 p-4 bg-red-50 rounded-lg border border-red-200">
            <p class="text-red-800 text-sm">
              <strong>Enforcement:</strong> We reserve the right to remove any content that violates these Terms and to take appropriate action against violating users.
            </p>
          </div>
        </div>

        <!-- Contact for Questions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Questions About Our Terms?</h2>
          <p class="text-gray-600 mb-6">
            If you have questions about these Terms of Service or need clarification on any provisions, please don't hesitate to contact us.
          </p>
          <div class="flex flex-col sm:flex-row gap-4">
            <BaseButton variant="primary" :to="{ name: 'contact' }" tag="router-link">
              Contact Support
            </BaseButton>
            <BaseButton variant="outline" :to="{ name: 'privacy' }" tag="router-link">
              View Privacy Policy
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
  title: 'Terms of Service - Job Portal',
  meta: [
    {
      name: 'description',
      content: 'Read our terms of service to understand your rights and responsibilities when using our job portal platform.'
    },
    {
      property: 'og:title',
      content: 'Terms of Service - Job Portal'
    },
    {
      property: 'og:description',
      content: 'Read our terms of service to understand your rights and responsibilities when using our job portal platform.'
    }
  ]
})

// Data
const lastUpdated = ref('January 1, 2025')

const tableOfContents = ref([
  { id: 'acceptance', title: 'Acceptance of Terms' },
  { id: 'service-description', title: 'Service Description' },
  { id: 'user-accounts', title: 'User Accounts' },
  { id: 'user-conduct', title: 'User Conduct' },
  { id: 'privacy-data', title: 'Privacy and Data' },
  { id: 'intellectual-property', title: 'Intellectual Property' },
  { id: 'payment-terms', title: 'Payment Terms' },
  { id: 'disclaimers', title: 'Disclaimers' },
  { id: 'liability', title: 'Limitation of Liability' },
  { id: 'governing-law', title: 'Governing Law' },
  { id: 'contact', title: 'Contact Information' }
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

.prose p {
  @apply mb-4;
}

.prose ul {
  @apply list-disc list-inside mb-4 space-y-2;
}
</style> 