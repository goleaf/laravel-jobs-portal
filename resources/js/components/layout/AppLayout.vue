<template>
  <div class="min-h-screen bg-neutral-50">
    <!-- Header -->
    <AppHeader @toggle-mobile-menu="toggleMobileMenu" />
    
    <!-- Mobile Navigation Overlay -->
    <div 
      v-if="isMobileMenuOpen" 
      class="fixed inset-0 z-40 lg:hidden"
      @click="toggleMobileMenu"
    >
      <div class="fixed inset-0 bg-black opacity-50"></div>
    </div>

    <!-- Main Content -->
    <div class="flex">
      <!-- Sidebar Navigation -->
      <aside 
        v-if="showSidebar"
        :class="[
          'fixed inset-y-0 left-0 z-50 w-64 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0',
          isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
      >
        <AppSidebar @close="toggleMobileMenu" />
      </aside>

      <!-- Content Area -->
      <main 
        :class="[
          'flex-1 flex flex-col min-h-screen',
          showSidebar ? 'lg:ml-0' : 'w-full'
        ]"
      >
        <!-- Breadcrumbs -->
        <div v-if="showBreadcrumbs" class="bg-white border-b border-neutral-200 px-4 sm:px-6 lg:px-8 py-4">
          <AppBreadcrumbs />
        </div>

        <!-- Page Content -->
        <div class="flex-1 p-4 sm:p-6 lg:p-8">
          <div :class="[containerClass, 'animate-fade-in']">
            <slot />
          </div>
        </div>
      </main>
    </div>

    <!-- Footer -->
    <AppFooter v-if="showFooter" />

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Loading Overlay -->
    <div 
      v-if="isLoading" 
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    >
      <div class="bg-white rounded-lg p-6 shadow-xl">
        <div class="flex items-center space-x-3">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-primary-600"></div>
          <span class="text-neutral-700 font-medium">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from './AppHeader.vue'
import AppSidebar from './AppSidebar.vue'
import AppFooter from './AppFooter.vue'
import AppBreadcrumbs from './AppBreadcrumbs.vue'

interface Props {
  showSidebar?: boolean
  showBreadcrumbs?: boolean
  showFooter?: boolean
  containerClass?: string
  isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  showSidebar: true,
  showBreadcrumbs: true,
  showFooter: true,
  containerClass: 'max-w-7xl mx-auto',
  isLoading: false
})

const route = useRoute()
const isMobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value
}

// Close mobile menu when route changes
watch(() => route.path, () => {
  isMobileMenuOpen.value = false
})
</script>