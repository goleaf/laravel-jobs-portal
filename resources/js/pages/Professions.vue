<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
              <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Professional Categories & Careers
              </h1>
              <p class="mt-1 text-sm text-gray-500 max-w-2xl">
                Explore comprehensive career information organized by professional categories. 
                Discover professions, required skills, education requirements, and job market insights.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <ProfessionsDashboard />
    </div>

    <!-- Footer Info -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Info Card 1 -->
          <div class="text-center">
            <div class="bg-blue-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Organized Categories</h3>
            <p class="text-gray-600 text-sm">
              Browse professions organized by international standards (ISCO) with clear hierarchical structure.
            </p>
          </div>

          <!-- Info Card 2 -->
          <div class="text-center">
            <div class="bg-green-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Detailed Information</h3>
            <p class="text-gray-600 text-sm">
              Get comprehensive details including required skills, education requirements, and job market data.
            </p>
          </div>

          <!-- Info Card 3 -->
          <div class="text-center">
            <div class="bg-purple-100 rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4">
              <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9a9 9 0 01-9-9m9 9c0-5 0-9-9-9s-9 4-9 9" />
              </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Multilingual Support</h3>
            <p class="text-gray-600 text-sm">
              Access profession information in multiple languages with professional translations and fallback system.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import ProfessionsDashboard from '@/components/professions/ProfessionsDashboard.vue';

// Meta information for SEO
const meta = {
  title: 'Professional Categories & Careers | Job Portal',
  description: 'Explore comprehensive career information organized by professional categories. Discover professions, required skills, education requirements, and job market insights.',
  keywords: 'careers, professions, job categories, skills, education requirements, ISCO, professional development',
};

// Set page title
onMounted(() => {
  document.title = meta.title;
  
  // Update meta description
  const metaDescription = document.querySelector('meta[name="description"]');
  if (metaDescription) {
    metaDescription.setAttribute('content', meta.description);
  }
  
  // Update meta keywords
  const metaKeywords = document.querySelector('meta[name="keywords"]');
  if (metaKeywords) {
    metaKeywords.setAttribute('content', meta.keywords);
  }
});
</script>
