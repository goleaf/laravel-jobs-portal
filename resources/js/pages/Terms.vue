<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-5xl font-bold mb-6">Terms of Service</h1>
          <p class="text-xl text-blue-100 leading-relaxed">
            These terms govern your use of our job portal platform. Please read them carefully 
            to understand your rights and responsibilities as a user.
          </p>
          <div class="mt-8 text-sm text-blue-200">
            Last Updated: {{ lastUpdated }}
          </div>
        </div>
      </div>
    </section>

    <!-- Quick Navigation -->
    <section class="py-8 bg-white border-b">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Navigation</h2>
          <div class="flex flex-wrap gap-4 text-sm">
            <a
              v-for="section in tableOfContents"
              :key="section.id"
              :href="`#${section.id}`"
              class="text-blue-600 hover:text-blue-800 hover:underline"
              @click="scrollToSection(section.id)"
            >
              {{ section.title }}
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Terms Content -->
    <section class="py-16">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
          <!-- Acceptance of Terms -->
          <div id="acceptance" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Acceptance of Terms</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                By accessing and using this job portal platform ("Service"), you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.
              </p>
              <p class="text-gray-600 mb-4">
                These Terms of Service ("Terms") apply to all visitors, users, and others who access or use the Service, including but not limited to job seekers, employers, and companies.
              </p>
              <p class="text-gray-600">
                We reserve the right to modify these Terms at any time. We will notify users of any material changes via email or through the platform. Your continued use of the Service after such modifications constitutes acceptance of the updated Terms.
              </p>
            </div>
          </div>

          <!-- Service Description -->
          <div id="service-description" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">2. Service Description</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                Our platform provides an online job portal service that connects job seekers with potential employers. The Service includes:
              </p>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>Job posting and browsing capabilities</li>
                <li>Profile creation and management for job seekers and employers</li>
                <li>Application submission and management tools</li>
                <li>Communication features between job seekers and employers</li>
                <li>Search and filtering functionality</li>
                <li>Analytics and reporting tools</li>
              </ul>
              <p class="text-gray-600">
                We act as an intermediary platform and do not guarantee employment outcomes, job placements, or the quality of jobs or candidates listed on our platform.
              </p>
            </div>
          </div>

          <!-- User Accounts -->
          <div id="user-accounts" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">3. User Accounts</h2>
            <div class="prose max-w-none">
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Account Creation</h3>
              <p class="text-gray-600 mb-4">
                To access certain features of the Service, you must create an account. You agree to provide accurate, current, and complete information during the registration process and to update such information to keep it accurate, current, and complete.
              </p>
              
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Account Security</h3>
              <p class="text-gray-600 mb-4">
                You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You agree to immediately notify us of any unauthorized use of your account.
              </p>
              
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Account Termination</h3>
              <p class="text-gray-600">
                We reserve the right to suspend or terminate your account at any time for violations of these Terms or for any other reason we deem appropriate, with or without notice.
              </p>
            </div>
          </div>

          <!-- User Conduct -->
          <div id="user-conduct" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">4. User Conduct</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">You agree not to use the Service to:</p>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>Post false, misleading, or inaccurate information</li>
                <li>Violate any applicable laws or regulations</li>
                <li>Infringe upon the rights of others</li>
                <li>Post discriminatory content based on race, gender, religion, or other protected characteristics</li>
                <li>Engage in harassment, spam, or unsolicited communications</li>
                <li>Attempt to gain unauthorized access to our systems</li>
                <li>Use automated systems to scrape or collect data from the platform</li>
                <li>Post job listings that are not genuine employment opportunities</li>
              </ul>
              <p class="text-gray-600">
                We reserve the right to remove any content that violates these Terms and to take appropriate action against violating users.
              </p>
            </div>
          </div>

          <!-- Privacy and Data -->
          <div id="privacy-data" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Privacy and Data Protection</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                Your privacy is important to us. Our collection and use of personal information is governed by our Privacy Policy, which is incorporated into these Terms by reference.
              </p>
              <p class="text-gray-600 mb-4">
                By using the Service, you consent to the collection, use, and sharing of your information as described in our Privacy Policy.
              </p>
              <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                <p class="text-blue-800">
                  <strong>Note:</strong> Please review our 
                  <router-link to="/privacy" class="underline hover:text-blue-900">Privacy Policy</router-link> 
                  for detailed information about how we handle your personal data.
                </p>
              </div>
            </div>
          </div>

          <!-- Intellectual Property -->
          <div id="intellectual-property" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Intellectual Property</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                The Service and its original content, features, and functionality are owned by us and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.
              </p>
              <p class="text-gray-600 mb-4">
                You retain ownership of content you post on the platform, but you grant us a non-exclusive, worldwide, royalty-free license to use, reproduce, and distribute such content in connection with the Service.
              </p>
              <p class="text-gray-600">
                You may not reproduce, distribute, modify, create derivative works of, publicly display, publicly perform, republish, download, store, or transmit any of the material on our Service without our prior written consent.
              </p>
            </div>
          </div>

          <!-- Payment Terms -->
          <div id="payment-terms" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Payment Terms</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                Some features of the Service may require payment of fees. All fees are non-refundable unless otherwise stated.
              </p>
              <p class="text-gray-600 mb-4">
                You agree to pay all charges incurred by you or any users of your account. We reserve the right to change our pricing at any time with reasonable notice.
              </p>
              <p class="text-gray-600">
                Failure to pay fees may result in suspension or termination of your account and access to premium features.
              </p>
            </div>
          </div>

          <!-- Disclaimers -->
          <div id="disclaimers" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Disclaimers</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                The Service is provided on an "AS IS" and "AS AVAILABLE" basis. We make no representations or warranties of any kind, express or implied, as to the operation of the Service or the information, content, materials, or products included on the Service.
              </p>
              <p class="text-gray-600 mb-4">
                We do not warrant that the Service will be uninterrupted or error-free, and we will not be liable for any interruptions or errors.
              </p>
              <p class="text-gray-600">
                We are not responsible for the conduct of any users of the Service or for any content posted by users.
              </p>
            </div>
          </div>

          <!-- Limitation of Liability -->
          <div id="liability" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">9. Limitation of Liability</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                In no event shall we be liable for any indirect, incidental, special, consequential, or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your use of the Service.
              </p>
              <p class="text-gray-600">
                Our total liability to you for all claims arising out of or relating to the use of the Service shall not exceed the amount you paid us in the twelve (12) months preceding the claim.
              </p>
            </div>
          </div>

          <!-- Governing Law -->
          <div id="governing-law" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">10. Governing Law</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                These Terms shall be governed by and construed in accordance with the laws of [Jurisdiction], without regard to its conflict of law provisions.
              </p>
              <p class="text-gray-600">
                Any disputes arising under these Terms shall be resolved in the courts of [Jurisdiction].
              </p>
            </div>
          </div>

          <!-- Contact Information -->
          <div id="contact" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">11. Contact Information</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                If you have any questions about these Terms, please contact us:
              </p>
              <div class="bg-gray-100 rounded-lg p-6">
                <p class="text-gray-700 mb-2"><strong>Email:</strong> legal@jobportal.com</p>
                <p class="text-gray-700 mb-2"><strong>Phone:</strong> +1 (555) 123-4567</p>
                <p class="text-gray-700">
                  <strong>Address:</strong><br>
                  123 Business Avenue<br>
                  Suite 500<br>
                  New York, NY 10001
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Back to Top -->
    <div class="fixed bottom-8 right-8">
      <button
        @click="scrollToTop"
        class="bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-colors"
        aria-label="Back to top"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
        </svg>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useHead } from '@vueuse/head'

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