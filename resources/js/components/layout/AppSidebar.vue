<template>
  <div class="h-full bg-white shadow-lg border-r border-neutral-200">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between p-4 border-b border-neutral-200">
      <div class="flex items-center space-x-2">
        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-sm">JP</span>
        </div>
        <span class="font-bold text-neutral-900">Job Portal</span>
      </div>
      
      <!-- Close button for mobile -->
      <button
        @click="$emit('close')"
        class="lg:hidden p-2 rounded-md text-neutral-400 hover:text-neutral-600 hover:bg-neutral-100"
      >
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="mt-6 px-3">
      <div class="space-y-1">
        <!-- Dashboard -->
        <router-link
          to="/"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-neutral-700 hover:text-primary-700 hover:bg-primary-50 transition-colors duration-150"
          active-class="bg-primary-100 text-primary-700"
        >
          <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
          </svg>
          Dashboard
        </router-link>

        <!-- Jobs -->
        <router-link
          to="/jobs"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-neutral-700 hover:text-primary-700 hover:bg-primary-50 transition-colors duration-150"
          active-class="bg-primary-100 text-primary-700"
        >
          <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M8 6a2 2 0 00-2 2v6l10-6V8a2 2 0 00-2-2H8z" />
          </svg>
          Jobs
        </router-link>

        <!-- Companies -->
        <router-link
          to="/companies"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-neutral-700 hover:text-primary-700 hover:bg-primary-50 transition-colors duration-150"
          active-class="bg-primary-100 text-primary-700"
        >
          <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h1a1 1 0 011 1v5m-4 0h4" />
          </svg>
          Companies
        </router-link>

        <!-- Candidates -->
        <router-link
          to="/candidates"
          class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-neutral-700 hover:text-primary-700 hover:bg-primary-50 transition-colors duration-150"
          active-class="bg-primary-100 text-primary-700"
        >
          <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
          </svg>
          Candidates
        </router-link>
      </div>
    </nav>
  </div>
</template>

<script setup lang="ts">
// Define emits for component communication
defineEmits<{
  close: []
}>();
</script> 