<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="text-center">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">
          Job Portal - Vue3 SPA
        </h1>
        <p class="text-xl text-gray-600 mb-8">
          Welcome to the modern Vue3 Single Page Application
        </p>
        <div class="space-x-4">
          <router-link to="/login" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
            Login
          </router-link>
          <router-link to="/dashboard" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700">
            Dashboard
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Home page component
</script> 