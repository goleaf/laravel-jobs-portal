<template>
  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">
          Trusted by Top Companies
        </h2>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
          Join thousands of professionals who have found their dream jobs through our platform.
        </p>
      </div>

      <!-- Company Logos -->
      <div class="mb-16">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 items-center opacity-70">
          <div
            v-for="company in trustedCompanies"
            :key="company.name"
            class="flex items-center justify-center"
          >
            <img
              v-if="company.logo"
              :src="company.logo"
              :alt="company.name"
              class="h-12 w-auto grayscale hover:grayscale-0 transition-all duration-300"
            />
            <div
              v-else
              class="h-12 w-24 bg-gray-200 rounded flex items-center justify-center text-gray-500 text-sm font-medium"
            >
              {{ company.name }}
            </div>
          </div>
        </div>
      </div>

      <!-- Testimonials -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
        <div
          v-for="testimonial in testimonials"
          :key="testimonial.id"
          class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow duration-200"
        >
          <!-- Quote -->
          <div class="mb-4">
            <QuoteIcon class="w-8 h-8 text-indigo-600 mb-2" />
            <p class="text-gray-700 italic">
              "{{ testimonial.quote }}"
            </p>
          </div>

          <!-- Author -->
          <div class="flex items-center">
            <img
              v-if="testimonial.avatar"
              :src="testimonial.avatar"
              :alt="testimonial.name"
              class="w-12 h-12 rounded-full object-cover mr-4"
            />
            <div
              v-else
              class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mr-4"
            >
              <span class="text-indigo-600 font-bold text-lg">
                {{ testimonial.name.charAt(0) }}
              </span>
            </div>
            <div>
              <div class="font-semibold text-gray-900">{{ testimonial.name }}</div>
              <div class="text-sm text-gray-600">{{ testimonial.position }}</div>
              <div class="text-sm text-gray-500">{{ testimonial.company }}</div>
            </div>
          </div>

          <!-- Rating -->
          <div class="flex items-center mt-3">
            <StarIcon
              v-for="n in 5"
              :key="n"
              class="w-4 h-4"
              :class="n <= testimonial.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'"
            />
            <span class="ml-2 text-sm text-gray-600">{{ testimonial.rating }}/5</span>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-8 text-center">
        <div class="p-6">
          <div class="text-3xl font-bold text-indigo-600 mb-2">{{ stats.jobsPlaced.toLocaleString() }}+</div>
          <div class="text-gray-600">Jobs Placed</div>
        </div>
        <div class="p-6">
          <div class="text-3xl font-bold text-indigo-600 mb-2">{{ stats.companiesServed.toLocaleString() }}+</div>
          <div class="text-gray-600">Companies Served</div>
        </div>
        <div class="p-6">
          <div class="text-3xl font-bold text-indigo-600 mb-2">{{ stats.averageTime }}</div>
          <div class="text-gray-600">Avg. Time to Hire</div>
        </div>
        <div class="p-6">
          <div class="text-3xl font-bold text-indigo-600 mb-2">{{ stats.satisfactionRate }}</div>
          <div class="text-gray-600">Satisfaction Rate</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { QuoteIcon, StarIcon } from '@heroicons/vue/24/outline'

interface TrustedCompany {
  name: string
  logo?: string
}

interface Testimonial {
  id: number
  name: string
  position: string
  company: string
  quote: string
  rating: number
  avatar?: string
}

interface Stats {
  jobsPlaced: number
  companiesServed: number
  averageTime: string
  satisfactionRate: string
}

// Data
const trustedCompanies = ref<TrustedCompany[]>([
  { name: 'TechCorp', logo: null },
  { name: 'InnovateNow', logo: null },
  { name: 'DataSoft', logo: null },
  { name: 'CloudTech', logo: null },
  { name: 'StartupHub', logo: null },
  { name: 'Enterprise', logo: null }
])

const testimonials = ref<Testimonial[]>([
  {
    id: 1,
    name: 'Sarah Johnson',
    position: 'Software Engineer',
    company: 'TechCorp',
    quote: 'I found my dream job within 2 weeks of joining. The platform is incredibly user-friendly and the job matches were perfect for my skills.',
    rating: 5,
    avatar: null
  },
  {
    id: 2,
    name: 'Michael Chen',
    position: 'Product Manager',
    company: 'InnovateNow',
    quote: 'The quality of companies and positions on this platform is outstanding. I received multiple offers and could choose the best fit for my career.',
    rating: 5,
    avatar: null
  },
  {
    id: 3,
    name: 'Emily Rodriguez',
    position: 'UX Designer',
    company: 'DesignStudio',
    quote: 'What impressed me most was the personalized job recommendations. Every suggestion was relevant to my experience and career goals.',
    rating: 5,
    avatar: null
  }
])

const stats = ref<Stats>({
  jobsPlaced: 50000,
  companiesServed: 2500,
  averageTime: '14 days',
  satisfactionRate: '98%'
})
</script> 