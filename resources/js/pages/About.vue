<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <HeroSection
      title="About Our Job Portal"
      subtitle="Connecting talented professionals with exceptional opportunities since our founding. We're dedicated to transforming the way people find meaningful careers and helping companies discover the perfect talent."
      size="lg"
      theme="primary"
      :show-actions="true"
      :actions="[
        { text: 'Browse Jobs', variant: 'primary', to: '/jobs' },
        { text: 'Find Companies', variant: 'outline', to: '/companies' }
      ]"
    />

    <!-- Mission & Vision Section -->
    <section class="py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-12">
          <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
              <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h2>
            <p class="text-gray-600 leading-relaxed">
              To empower professionals and organizations by creating seamless connections between 
              talent and opportunity. We believe everyone deserves to find work that inspires them 
              and enables companies to build exceptional teams.
            </p>
          </div>
          
          <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
              <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h2>
            <p class="text-gray-600 leading-relaxed">
              To become the world's most trusted career platform, where innovation meets opportunity. 
              We envision a future where every professional finds their ideal career path and every 
              company builds their dream team.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Core Values Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Core Values</h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            These principles guide everything we do and shape the experience we create for our community.
          </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Excellence</h3>
            <p class="text-gray-600">
              We strive for excellence in everything we do, from our platform's user experience 
              to the quality of connections we facilitate.
            </p>
          </div>
          
          <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Integrity</h3>
            <p class="text-gray-600">
              Trust is the foundation of our community. We operate with transparency, 
              honesty, and ethical practices in all our interactions.
            </p>
          </div>
          
          <div class="text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">Community</h3>
            <p class="text-gray-600">
              We foster a supportive community where professionals and employers can 
              grow together and achieve their goals.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Impact</h2>
          <p class="text-lg text-gray-600">Making a difference in careers and businesses worldwide</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="text-4xl font-bold text-blue-600 mb-2">{{ stats.totalJobs.toLocaleString() }}+</div>
            <div class="text-gray-600 font-medium">Active Jobs</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-green-600 mb-2">{{ stats.totalCompanies.toLocaleString() }}+</div>
            <div class="text-gray-600 font-medium">Partner Companies</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-purple-600 mb-2">{{ stats.totalCandidates.toLocaleString() }}+</div>
            <div class="text-gray-600 font-medium">Professionals</div>
          </div>
          <div class="text-center">
            <div class="text-4xl font-bold text-orange-600 mb-2">{{ stats.successfulMatches.toLocaleString() }}+</div>
            <div class="text-gray-600 font-medium">Successful Matches</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Leadership Team</h2>
          <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Meet the passionate individuals driving our mission to transform the job market.
          </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div class="text-center" v-for="member in teamMembers" :key="member.id">
            <div class="w-32 h-32 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-2xl font-bold">
              {{ member.initials }}
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ member.name }}</h3>
            <Badge :text="member.role" variant="primary" size="md" class="mb-3" />
            <p class="text-gray-600 text-sm">{{ member.bio }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Social Proof Section -->
    <SocialProof />

    <!-- Newsletter Signup Section -->
    <NewsletterSignup />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useHead } from '@vueuse/head'
import HeroSection from '../components/ui/HeroSection.vue'
import Badge from '../components/ui/Badge.vue'
import SocialProof from '../components/ui/SocialProof.vue'
import NewsletterSignup from '../components/ui/NewsletterSignup.vue'
import HeroSection from '../components/ui/HeroSection.vue'
import Badge from '../components/ui/Badge.vue'
import SocialProof from '../components/ui/SocialProof.vue'
import NewsletterSignup from '../components/ui/NewsletterSignup.vue'

// Set page metadata
useHead({
  title: 'About Us - Job Portal',
  meta: [
    {
      name: 'description',
      content: 'Learn about our mission to connect talented professionals with exceptional opportunities. Discover our values, team, and impact on the job market.'
    },
    {
      property: 'og:title',
      content: 'About Us - Job Portal'
    },
    {
      property: 'og:description',
      content: 'Learn about our mission to connect talented professionals with exceptional opportunities.'
    }
  ]
})

// Statistics data
const stats = ref({
  totalJobs: 15000,
  totalCompanies: 2500,
  totalCandidates: 45000,
  successfulMatches: 8500
})

// Team members data
const teamMembers = ref([
  {
    id: 1,
    name: 'Sarah Johnson',
    role: 'Chief Executive Officer',
    initials: 'SJ',
    bio: 'Visionary leader with 15+ years in talent acquisition and business strategy.'
  },
  {
    id: 2,
    name: 'Michael Chen',
    role: 'Chief Technology Officer',
    initials: 'MC',
    bio: 'Technology innovator passionate about creating seamless user experiences.'
  },
  {
    id: 3,
    name: 'Emily Rodriguez',
    role: 'Head of Operations',
    initials: 'ER',
    bio: 'Operations expert ensuring quality connections between talent and opportunity.'
  }
])

onMounted(() => {
  // Could load real stats from API here
  // await loadStatistics()
})
</script>

<style scoped>
/* Additional styles if needed */
</style> 