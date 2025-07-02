<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
          <h1 class="text-5xl font-bold mb-6">Privacy Policy</h1>
          <p class="text-xl text-blue-100 leading-relaxed">
            We are committed to protecting your privacy and ensuring the security of your personal information. 
            This policy explains how we collect, use, and safeguard your data.
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

    <!-- Privacy Content -->
    <section class="py-16">
      <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
          
          <!-- Introduction -->
          <div id="introduction" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">1. Introduction</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                This Privacy Policy describes how we collect, use, disclose, and safeguard your personal information when you use our job portal platform ("Service"). We respect your privacy and are committed to protecting your personal data.
              </p>
              <p class="text-gray-600 mb-4">
                This policy applies to all information collected through our Service and any related services, sales, marketing, or events.
              </p>
              <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                <p class="text-blue-800">
                  <strong>Important:</strong> By using our Service, you agree to the collection and use of information in accordance with this Privacy Policy.
                </p>
              </div>
            </div>
          </div>

          <!-- Information We Collect -->
          <div id="information-collected" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">2. Information We Collect</h2>
            <div class="prose max-w-none">
              
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Personal Information</h3>
              <p class="text-gray-600 mb-4">We may collect the following personal information:</p>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>Full name and contact information (email, phone, address)</li>
                <li>Professional information (resume, work experience, education)</li>
                <li>Account credentials (username, password)</li>
                <li>Profile preferences and settings</li>
                <li>Communication records with employers or our support team</li>
                <li>Payment information for premium services</li>
              </ul>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Usage Information</h3>
              <p class="text-gray-600 mb-4">We automatically collect certain information about your use of our Service:</p>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>IP address and device information</li>
                <li>Browser type and operating system</li>
                <li>Pages viewed and time spent on the platform</li>
                <li>Search queries and job application history</li>
                <li>Referral sources and exit pages</li>
              </ul>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Cookies and Tracking Technologies</h3>
              <p class="text-gray-600">
                We use cookies, web beacons, and similar tracking technologies to collect information about your browsing behavior and improve our Service.
              </p>
            </div>
          </div>

          <!-- How We Use Information -->
          <div id="how-we-use" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">3. How We Use Your Information</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">We use the collected information for the following purposes:</p>
              
              <h3 class="text-lg font-semibold text-gray-800 mb-2">Service Provision</h3>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>Creating and managing your account</li>
                <li>Facilitating job applications and candidate matching</li>
                <li>Providing customer support and technical assistance</li>
                <li>Processing payments for premium services</li>
              </ul>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Communication</h3>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>Sending job alerts and notifications</li>
                <li>Providing updates about our services</li>
                <li>Responding to your inquiries and requests</li>
                <li>Sending promotional materials (with your consent)</li>
              </ul>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Platform Improvement</h3>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>Analyzing usage patterns and user behavior</li>
                <li>Developing new features and improving existing ones</li>
                <li>Conducting research and analytics</li>
                <li>Preventing fraud and ensuring platform security</li>
              </ul>
            </div>
          </div>

          <!-- Information Sharing -->
          <div id="information-sharing" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">4. How We Share Your Information</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">We may share your information in the following circumstances:</p>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">With Employers</h3>
              <p class="text-gray-600 mb-4">
                When you apply for jobs, we share relevant information from your profile with the respective employers, including your resume, contact information, and application details.
              </p>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Service Providers</h3>
              <p class="text-gray-600 mb-4">
                We may share information with trusted third-party service providers who assist us in operating our platform, conducting business, or serving our users.
              </p>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Legal Requirements</h3>
              <p class="text-gray-600 mb-4">
                We may disclose information when required by law, court order, or government regulation, or when we believe disclosure is necessary to protect our rights or comply with legal obligations.
              </p>

              <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                <p class="text-yellow-800">
                  <strong>Note:</strong> We do not sell, trade, or rent your personal information to third parties for marketing purposes without your explicit consent.
                </p>
              </div>
            </div>
          </div>

          <!-- Data Security -->
          <div id="data-security" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">5. Data Security</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.
              </p>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Security Measures Include:</h3>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>SSL encryption for data transmission</li>
                <li>Secure servers and data centers</li>
                <li>Regular security audits and assessments</li>
                <li>Access controls and authentication protocols</li>
                <li>Employee training on data protection</li>
                <li>Incident response and breach notification procedures</li>
              </ul>

              <p class="text-gray-600">
                However, no method of transmission over the internet or electronic storage is 100% secure. While we strive to protect your information, we cannot guarantee absolute security.
              </p>
            </div>
          </div>

          <!-- Your Rights -->
          <div id="your-rights" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">6. Your Privacy Rights</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">You have the following rights regarding your personal information:</p>

              <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">Access & Portability</h3>
                  <p class="text-gray-600 text-sm">
                    Request access to your personal data and receive a copy in a structured, machine-readable format.
                  </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">Correction</h3>
                  <p class="text-gray-600 text-sm">
                    Request correction of inaccurate or incomplete personal information.
                  </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">Deletion</h3>
                  <p class="text-gray-600 text-sm">
                    Request deletion of your personal data, subject to certain legal limitations.
                  </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                  <h3 class="text-lg font-semibold text-gray-800 mb-2">Opt-out</h3>
                  <p class="text-gray-600 text-sm">
                    Withdraw consent for marketing communications and certain data processing activities.
                  </p>
                </div>
              </div>

              <p class="text-gray-600 mb-4">
                To exercise these rights, please contact us using the information provided in the "Contact Us" section below.
              </p>
            </div>
          </div>

          <!-- Cookies Policy -->
          <div id="cookies" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">7. Cookies and Tracking</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                We use cookies and similar tracking technologies to enhance your browsing experience and analyze platform usage.
              </p>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Types of Cookies We Use:</h3>
              <div class="space-y-4 mb-6">
                <div class="border-l-4 border-blue-400 pl-4">
                  <h4 class="font-medium text-gray-800">Essential Cookies</h4>
                  <p class="text-gray-600 text-sm">Required for the platform to function properly and cannot be disabled.</p>
                </div>
                
                <div class="border-l-4 border-green-400 pl-4">
                  <h4 class="font-medium text-gray-800">Functional Cookies</h4>
                  <p class="text-gray-600 text-sm">Remember your preferences and enhance your user experience.</p>
                </div>
                
                <div class="border-l-4 border-yellow-400 pl-4">
                  <h4 class="font-medium text-gray-800">Analytics Cookies</h4>
                  <p class="text-gray-600 text-sm">Help us understand how users interact with our platform to improve our services.</p>
                </div>
                
                <div class="border-l-4 border-purple-400 pl-4">
                  <h4 class="font-medium text-gray-800">Marketing Cookies</h4>
                  <p class="text-gray-600 text-sm">Used to deliver relevant advertisements and track campaign effectiveness.</p>
                </div>
              </div>

              <p class="text-gray-600">
                You can manage your cookie preferences through your browser settings or our cookie consent tool.
              </p>
            </div>
          </div>

          <!-- Data Retention -->
          <div id="data-retention" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">8. Data Retention</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                We retain your personal information for as long as necessary to provide our services and fulfill the purposes outlined in this Privacy Policy.
              </p>

              <h3 class="text-lg font-semibold text-gray-800 mb-2">Retention Periods:</h3>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li><strong>Active Accounts:</strong> Information retained while your account is active</li>
                <li><strong>Inactive Accounts:</strong> Data may be retained for up to 2 years after account inactivity</li>
                <li><strong>Legal Requirements:</strong> Some data may be retained longer due to legal obligations</li>
                <li><strong>Marketing Data:</strong> Retained until you opt-out or as required by law</li>
              </ul>

              <p class="text-gray-600">
                When we no longer need your information, we will securely delete or anonymize it in accordance with our data retention policies.
              </p>
            </div>
          </div>

          <!-- Third-Party Links -->
          <div id="third-party" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">9. Third-Party Links</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                Our platform may contain links to third-party websites, services, or applications. This Privacy Policy does not apply to these external sites.
              </p>
              <p class="text-gray-600 mb-4">
                We recommend reviewing the privacy policies of any third-party sites you visit through our platform, as their data practices may differ from ours.
              </p>
              <div class="bg-red-50 border-l-4 border-red-400 p-4">
                <p class="text-red-800">
                  <strong>Disclaimer:</strong> We are not responsible for the privacy practices or content of third-party websites.
                </p>
              </div>
            </div>
          </div>

          <!-- Updates to Policy -->
          <div id="policy-updates" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">10. Updates to This Policy</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                We may update this Privacy Policy from time to time to reflect changes in our practices, technology, legal requirements, or other factors.
              </p>
              <p class="text-gray-600 mb-4">
                When we make material changes, we will notify you by:
              </p>
              <ul class="list-disc list-inside text-gray-600 mb-4 space-y-2">
                <li>Sending an email notification to your registered email address</li>
                <li>Posting a prominent notice on our platform</li>
                <li>Updating the "Last Updated" date at the top of this policy</li>
              </ul>
              <p class="text-gray-600">
                Your continued use of our Service after such notification constitutes acceptance of the updated Privacy Policy.
              </p>
            </div>
          </div>

          <!-- Contact Information -->
          <div id="contact" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">11. Contact Us</h2>
            <div class="prose max-w-none">
              <p class="text-gray-600 mb-4">
                If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
              </p>
              
              <div class="bg-gray-100 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Data Protection Officer</h3>
                <div class="space-y-2">
                  <p class="text-gray-700"><strong>Email:</strong> privacy@jobportal.com</p>
                  <p class="text-gray-700"><strong>Phone:</strong> +1 (555) 123-4567</p>
                  <p class="text-gray-700">
                    <strong>Address:</strong><br>
                    Privacy Department<br>
                    123 Business Avenue<br>
                    Suite 500<br>
                    New York, NY 10001
                  </p>
                </div>
              </div>

              <div class="bg-blue-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Response Time</h3>
                <p class="text-blue-700">
                  We aim to respond to all privacy-related inquiries within 30 days. For urgent matters, please include "URGENT" in your subject line.
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