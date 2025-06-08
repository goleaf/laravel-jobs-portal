<template>
  <div id="app" class="min-h-screen bg-neutral-50">
    <!-- Skip to content link for accessibility -->
    <a 
      href="#main-content" 
      class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-md z-50"
    >
      Skip to main content
    </a>

    <!-- Main App Layout -->
    <AppLayout>
      <router-view v-slot="{ Component, route }">
        <transition
          :name="getTransitionName(route)"
          mode="out-in"
          @enter="onEnter"
          @leave="onLeave"
        >
          <component 
            :is="Component" 
            :key="route.path"
            class="min-h-screen"
            id="main-content"
            tabindex="-1"
          />
        </transition>
      </router-view>
    </AppLayout>

    <!-- Global Loading Overlay -->
    <Teleport to="body">
      <div 
        v-if="isGlobalLoading" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-white/80 backdrop-blur-sm"
      >
        <div class="text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 mb-4 bg-primary-600 rounded-full">
            <svg class="w-8 h-8 text-white animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
          <p class="text-lg font-medium text-neutral-900">Loading...</p>
        </div>
      </div>
    </Teleport>

    <!-- Global Toast Container -->
    <Teleport to="body">
      <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 pointer-events-none">
        <!-- Toasts will be rendered here -->
      </div>
    </Teleport>

    <!-- Global Modal Container -->
    <Teleport to="body">
      <div id="modal-container">
        <!-- Modals will be rendered here -->
      </div>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'
import AppLayout from './components/layout/AppLayout.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const isGlobalLoading = ref(false)

// Computed properties
const isAuthenticated = computed(() => authStore.isAuthenticated)

// Page transition logic
const getTransitionName = (route: any) => {
  // Different transitions for different route types
  if (route.meta?.transition) {
    return route.meta.transition
  }
  
  // Default transitions based on route hierarchy
  if (route.path === '/') return 'fade'
  if (route.path.startsWith('/auth')) return 'slide-up'
  if (route.path.startsWith('/dashboard')) return 'slide-left'
  
  return 'fade'
}

// Transition event handlers
const onEnter = (el: Element) => {
  // Focus management for accessibility
  const focusTarget = el.querySelector('[autofocus]') || el.querySelector('h1') || el
  if (focusTarget && typeof (focusTarget as HTMLElement).focus === 'function') {
    (focusTarget as HTMLElement).focus()
  }
}

const onLeave = () => {
  // Cleanup animations or side effects
}

// Global loading state management
const startGlobalLoading = () => {
  isGlobalLoading.value = true
}

const stopGlobalLoading = () => {
  isGlobalLoading.value = false
}

// Watch for route changes to manage loading states
watch(
  () => route.path,
  async (newPath, oldPath) => {
    if (newPath !== oldPath) {
      startGlobalLoading()
      
      // Simulate async operations or wait for components to load
      await new Promise(resolve => setTimeout(resolve, 300))
      
      stopGlobalLoading()
    }
  }
)

// Initialize app
onMounted(async () => {
  // Initialize authentication state
  if (localStorage.getItem('auth_token')) {
    await authStore.checkAuth()
  }
  
  // Initialize other global services
  await initializeApp()
})

const initializeApp = async () => {
  // Set up global error handling
  window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled promise rejection:', event.reason)
    // Could show toast notification here
  })
  
  // Set up global keyboard shortcuts
  document.addEventListener('keydown', (event) => {
    // Ctrl/Cmd + K for search
    if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
      event.preventDefault()
      // Open search modal or focus search input
    }
    
    // Escape to close modals
    if (event.key === 'Escape') {
      // Close any open modals or dropdowns
    }
  })
  
  // Set up theme detection
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  mediaQuery.addEventListener('change', (e) => {
    // Handle theme changes if dark mode is implemented
  })
}

// Expose methods for global use
defineExpose({
  startGlobalLoading,
  stopGlobalLoading
})
</script>

<style>
/* Global styles */
*,
*::before,
*::after {
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

body {
  margin: 0;
  font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
  line-height: 1.6;
  color: #334155;
  background-color: #fafafa;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Focus styles for accessibility */
:focus {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

:focus:not(:focus-visible) {
  outline: none;
}

/* Screen reader only class */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}

.sr-only:focus {
  position: static;
  width: auto;
  height: auto;
  padding: 0.5rem 1rem;
  margin: 0;
  overflow: visible;
  clip: auto;
  white-space: normal;
}

/* Page transition styles */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease-in-out;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.slide-up-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}

.slide-left-enter-active,
.slide-left-leave-active {
  transition: all 0.3s ease-in-out;
}

.slide-left-enter-from {
  opacity: 0;
  transform: translateX(20px);
}

.slide-left-leave-to {
  opacity: 0;
  transform: translateX(-20px);
}

/* Custom scrollbar styles */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Selection styles */
::selection {
  background-color: #3b82f6;
  color: white;
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
  
  body {
    background: white !important;
    color: black !important;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  :root {
    --tw-border-opacity: 1;
  }
}
</style>