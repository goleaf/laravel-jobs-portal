<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Company Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-8">
          <!-- Company Logo -->
          <div class="w-24 h-24 bg-white rounded-lg flex items-center justify-center flex-shrink-0">
            <div class="text-2xl font-bold text-blue-600">
              {{ company.logo || company.name.charAt(0) }}
            </div>
          </div>
          
          <!-- Company Info -->
          <div class="flex-1">
            <h1 class="text-4xl font-bold mb-2">{{ company.name }}</h1>
            <p class="text-xl text-blue-100 mb-4">{{ company.tagline }}</p>
            <div class="flex flex-wrap gap-4 text-sm">
              <Badge :text="company.industry" variant="secondary" class="bg-white/20 text-white border-white/30" />
              <Badge :text="`${company.size} employees`" variant="secondary" class="bg-white/20 text-white border-white/30" />
              <Badge :text="company.location" variant="secondary" class="bg-white/20 text-white border-white/30" />
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <BaseButton variant="secondary" size="lg">
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
              </svg>
              Follow Company
            </BaseButton>
            <BaseButton variant="outline" size="lg" class="border-white text-white hover:bg-white hover:text-blue-600">
              View Jobs ({{ company.activeJobs }})
            </BaseButton>
          </div>
        </div>
      </div>
    </section>

    <!-- Company Stats -->
    <section class="py-8 bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          <div>
            <div class="text-3xl font-bold text-blue-600">{{ company.activeJobs }}</div>
            <div class="text-gray-600 font-medium">Open Positions</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-green-600">{{ company.employees }}</div>
            <div class="text-gray-600 font-medium">Team Members</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-purple-600">{{ company.founded }}</div>
            <div class="text-gray-600 font-medium">Founded</div>
          </div>
          <div>
            <div class="text-3xl font-bold text-orange-600">4.8</div>
            <div class="text-gray-600 font-medium">Rating</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Company Content -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-12">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-8">
            <!-- About Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-4">About {{ company.name }}</h2>
              <div class="prose max-w-none text-gray-600">
                <p>{{ company.description }}</p>
              </div>
            </div>

            <!-- Culture & Values -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
              <h2 class="text-2xl font-bold text-gray-900 mb-6">Culture & Values</h2>
              <div class="grid md:grid-cols-2 gap-6">
                <div v-for="value in company.values" :key="value.title" class="flex items-start">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-900 mb-1">{{ value.title }}</h3>
                    <p class="text-gray-600 text-sm">{{ value.description }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Open Positions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
              <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Open Positions</h2>
                <BaseButton variant="outline" size="sm">
                  View All Jobs
                </BaseButton>
              </div>
              <div class="space-y-4">
                <JobCard
                  v-for="job in company.recentJobs"
                  :key="job.id"
                  :job="job"
                  :show-company-logo="false"
                  class="border border-gray-200"
                  @bookmark="handleJobBookmark"
                  @apply="handleJobApply"
                />
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Company Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>
              <div class="space-y-4">
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                  </svg>
                  <div>
                    <div class="font-medium text-gray-900">Industry</div>
                    <div class="text-gray-600">{{ company.industry }}</div>
                  </div>
                </div>

                <div class="flex items-start">
                  <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                  </svg>
                  <div>
                    <div class="font-medium text-gray-900">Location</div>
                    <div class="text-gray-600">{{ company.location }}</div>
                  </div>
                </div>

                <div class="flex items-start">
                  <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                  </svg>
                  <div>
                    <div class="font-medium text-gray-900">Company Size</div>
                    <div class="text-gray-600">{{ company.size }} employees</div>
                  </div>
                </div>

                <div class="flex items-start">
                  <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                  </svg>
                  <div>
                    <div class="font-medium text-gray-900">Founded</div>
                    <div class="text-gray-600">{{ company.founded }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contact -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Get in Touch</h3>
              <div class="space-y-3">
                <BaseButton variant="primary" size="md" class="w-full">
                  Contact Company
                </BaseButton>
                <BaseButton variant="outline" size="md" class="w-full">
                  Visit Website
                </BaseButton>
              </div>
            </div>

            <!-- Social Links -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Follow Us</h3>
              <div class="flex space-x-3">
                <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M20 10C20 4.477 15.523 0 10 0S0 4.477 0 10c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V10h2.54V7.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V10h2.773l-.443 2.89h-2.33v6.988C16.343 19.128 20 14.991 20 10z"/>
                  </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500 transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0020 3.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.073 4.073 0 01.8 7.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 010 16.407a11.616 11.616 0 006.29 1.84"/>
                  </svg>
                </a>
                <a href="#" class="w-10 h-10 bg-blue-700 text-white rounded-full flex items-center justify-center hover:bg-blue-800 transition-colors">
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z"/>
                  </svg>
                </a>
              </div>
            </div>
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
import { useRoute } from 'vue-router'
import BaseButton from '../components/ui/BaseButton.vue'
import Badge from '../components/ui/Badge.vue'
import JobCard from '@/components/jobs/JobCard.vue'
import NewsletterSignup from '../components/ui/NewsletterSignup.vue'

// Get route params
const route = useRoute()
const companySlug = route.params.slug

// Company data (in real app, this would come from API)
const company = ref({
  id: 1,
  name: 'TechCorp Innovation',
  slug: 'techcorp-innovation',
  tagline: 'Building the future with innovative technology solutions',
  description: 'TechCorp Innovation is a leading technology company specializing in cutting-edge software solutions, AI development, and digital transformation services. We help businesses worldwide leverage technology to achieve their goals and drive growth.',
  industry: 'Technology',
  location: 'San Francisco, CA',
  size: '500-1000',
  employees: 750,
  founded: 2015,
  activeJobs: 24,
  logo: null,
  values: [
    {
      title: 'Innovation',
      description: 'We push boundaries and embrace new technologies to create groundbreaking solutions.'
    },
    {
      title: 'Collaboration',
      description: 'We believe in the power of teamwork and diverse perspectives to achieve excellence.'
    },
    {
      title: 'Growth',
      description: 'We invest in our people and provide opportunities for continuous learning and development.'
    },
    {
      title: 'Impact',
      description: 'We strive to make a positive difference in the world through our work and values.'
    }
  ],
  recentJobs: [
    {
      id: 1,
      title: 'Senior Frontend Developer',
      company: 'TechCorp Innovation',
      location: 'San Francisco, CA',
      type: 'Full-time',
      salary: '$120,000 - $160,000',
      tags: ['React', 'TypeScript', 'Vue.js'],
      posted: '2 days ago',
      applications: 45
    },
    {
      id: 2,
      title: 'Product Manager',
      company: 'TechCorp Innovation',
      location: 'Remote',
      type: 'Full-time',
      salary: '$130,000 - $170,000',
      tags: ['Product Strategy', 'Agile', 'Analytics'],
      posted: '1 week ago',
      applications: 32
    },
    {
      id: 3,
      title: 'DevOps Engineer',
      company: 'TechCorp Innovation',
      location: 'San Francisco, CA',
      type: 'Full-time',
      salary: '$110,000 - $150,000',
      tags: ['AWS', 'Kubernetes', 'Docker'],
      posted: '1 week ago',
      applications: 28
    }
  ]
})

// Handlers
const handleJobBookmark = (jobId: number) => {
  console.log('Bookmark job:', jobId)
}

const handleJobApply = (jobId: number) => {
  console.log('Apply to job:', jobId)
}

onMounted(() => {
  // In real app, load company data based on slug
  // await loadCompanyData(companySlug)
  
  // Set page title
  document.title = `${company.value.name} - Company Profile`
})
</script> 