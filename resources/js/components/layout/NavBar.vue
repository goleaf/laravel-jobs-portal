<template>
  <nav class="bg-white shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <router-link to="/" class="text-xl font-bold text-blue-600">
          JobPortal
        </router-link>
        <div class="flex space-x-4">
          <router-link to="/jobs" class="text-gray-700 hover:text-blue-600">
            Jobs
          </router-link>
          <router-link to="/companies" class="text-gray-700 hover:text-blue-600">
            Companies
          </router-link>
          <router-link to="/professions" class="text-gray-700 hover:text-blue-600">
            Professions
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>