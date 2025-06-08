<template>
  <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 mb-6">
    <!-- Header with Database Status -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-gray-900 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Demo Login Information
      </h3>
      <div class="flex items-center space-x-2">
        <div class="flex items-center" :class="statusClasses">
          <div class="w-2 h-2 rounded-full mr-2" :class="statusDotClasses"></div>
          <span class="text-xs font-medium">{{ statusText }}</span>
        </div>
        <button @click="toggleExpanded" class="text-gray-400 hover:text-gray-600">
          <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
      </div>
    </div>

    <!-- Quick Login Buttons -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
      <button 
        v-for="credential in credentials"
        :key="credential.email"
        @click="fillCredentials(credential)"
        class="group relative bg-gradient-to-r p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md"
        :class="getCredentialClasses(credential.role)"
      >
        <div class="flex items-center">
          <div class="flex-shrink-0 mr-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="getIconClasses(credential.role)">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path v-if="credential.role === 'admin'" fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"/>
                <path v-else-if="credential.role === 'employer'" fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-5L9 2H4z"/>
                <path v-else fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
              </svg>
            </div>
          </div>
          <div class="flex-grow text-left">
            <div class="font-semibold text-gray-900 capitalize">{{ credential.role }}</div>
            <div class="text-sm text-gray-600">{{ credential.email }}</div>
            <div class="text-xs text-gray-500 mt-1">
              <span class="inline-flex items-center">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"/>
                </svg>
                password
              </span>
            </div>
          </div>
          <div class="opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </div>
      </button>
    </div>

    <!-- Expanded Information -->
    <transition name="fade">
      <div v-if="expanded" class="space-y-4 border-t border-gray-200 pt-4">
        <!-- Database Users -->
        <div v-if="databaseUsers.length > 0">
          <h4 class="text-sm font-medium text-gray-800 mb-3 flex items-center">
            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Database Users ({{ databaseUsers.length }} found)
          </h4>
          <div class="bg-gray-50 rounded-lg p-3">
            <div class="grid grid-cols-1 gap-2">
              <div v-for="user in databaseUsers" :key="user.id" 
                   class="flex items-center justify-between bg-white rounded p-2 text-sm">
                <div class="flex-grow">
                  <span class="font-medium">{{ user.name }}</span>
                  <span class="text-gray-500 ml-2">{{ user.email }}</span>
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ml-2"
                        :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ user.is_active ? 'Active' : 'Inactive' }}
                  </span>
                </div>
                <button @click="fillDatabaseUser(user)" 
                        class="text-blue-600 hover:text-blue-800 text-xs font-medium px-2 py-1 rounded hover:bg-blue-50">
                  Use
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- System Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="text-sm font-medium text-gray-800 mb-2 flex items-center">
              <svg class="w-4 h-4 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
              </svg>
              System Status
            </h5>
            <div class="space-y-1 text-xs text-gray-600">
              <div class="flex justify-between">
                <span>Database:</span>
                <span class="font-medium" :class="databaseConnected ? 'text-green-600' : 'text-red-600'">
                  {{ databaseConnected ? 'Connected' : 'Error' }}
                </span>
              </div>
              <div class="flex justify-between">
                <span>Users Found:</span>
                <span class="font-medium">{{ databaseUsers.length }}</span>
              </div>
              <div class="flex justify-between">
                <span>Last Check:</span>
                <span class="font-medium">{{ lastCheck }}</span>
              </div>
            </div>
          </div>
          
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h5 class="text-sm font-medium text-yellow-800 mb-2 flex items-center">
              <svg class="w-4 h-4 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
              </svg>
              Security Notice
            </h5>
            <div class="text-xs text-yellow-700 space-y-1">
              <p>• Default password: <code class="bg-white px-1 rounded font-mono">password</code></p>
              <p>• Demo environment only</p>
              <p>• Change credentials in production</p>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
const emit = defineEmits<{
  fillCredentials: [email: string]
}>()

const fillCredentials = (email: string) => {
  emit('fillCredentials', email)
}
</script> 