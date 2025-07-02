<template>
  <div>
    <!-- Mobile menu button -->
    <div v-if="isMobile" class="lg:hidden">
      <button
        type="button"
        @click="toggleMobileSidebar"
        class="bg-gray-800 p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
        aria-controls="mobile-menu"
        :aria-expanded="isMobileSidebarOpen"
      >
        <span class="sr-only">Open sidebar</span>
        <svg v-if="!isMobileSidebarOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg v-else class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Mobile sidebar overlay -->
    <div 
      v-show="isMobileSidebarOpen && isMobile" 
      class="fixed inset-0 flex z-40 lg:hidden"
      role="dialog"
      aria-modal="true"
    >
      <div 
        class="fixed inset-0 bg-gray-600 bg-opacity-75" 
        aria-hidden="true"
        @click="closeMobileSidebar"
      ></div>
      
      <div class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800 pt-5 pb-4">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
          <button
            type="button"
            class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
            @click="closeMobileSidebar"
          >
            <span class="sr-only">Close sidebar</span>
            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <SidebarContent :user-role="userRole" :is-collapsed="false" />
      </div>
      
      <div class="flex-shrink-0 w-14" aria-hidden="true">
        <!-- Dummy element to force sidebar to shrink to fit close icon -->
      </div>
    </div>

    <!-- Desktop sidebar -->
    <div 
      v-if="!isMobile"
      :class="desktopSidebarClasses"
      class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 transition-all duration-300"
    >
      <SidebarContent :user-role="userRole" :is-collapsed="isCollapsed" />
    </div>

    <!-- Collapse/Expand button for desktop -->
    <button
      v-if="!isMobile && showCollapseButton"
      @click="toggleDesktopSidebar"
      :class="collapseButtonClasses"
      class="fixed top-4 z-30 bg-white shadow-md rounded-full p-2 border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all duration-300"
      :aria-label="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
    >
      <svg 
        :class="{ 'rotate-180': isCollapsed }" 
        class="h-5 w-5 text-gray-600 transition-transform duration-300" 
        xmlns="http://www.w3.org/2000/svg" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import SidebarContent from './SidebarContent.vue';

export interface Props {
  showCollapseButton?: boolean;
  defaultCollapsed?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showCollapseButton: true,
  defaultCollapsed: false
});

const { userRole } = useAuth();

// State
const isMobile = ref(false);
const isCollapsed = ref(props.defaultCollapsed);
const isMobileSidebarOpen = ref(false);

// Responsive breakpoint detection
const checkScreenSize = () => {
  isMobile.value = window.innerWidth < 1024; // lg breakpoint
  if (!isMobile.value) {
    isMobileSidebarOpen.value = false;
  }
};

// Computed classes
const desktopSidebarClasses = computed(() => ({
  'w-64': !isCollapsed.value,
  'w-16': isCollapsed.value,
  'bg-gray-800': true,
  'shadow-lg': true
}));

const collapseButtonClasses = computed(() => ({
  'left-60': !isCollapsed.value, // 240px (w-64) - 16px padding
  'left-12': isCollapsed.value,   // 48px (w-16) - 16px padding
}));

// Methods
const toggleDesktopSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
};

const toggleMobileSidebar = () => {
  isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

const closeMobileSidebar = () => {
  isMobileSidebarOpen.value = false;
};

// Lifecycle
onMounted(() => {
  checkScreenSize();
  window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
  window.removeEventListener('resize', checkScreenSize);
});

// Expose state for parent components
defineExpose({
  isCollapsed,
  isMobileSidebarOpen,
  toggleDesktopSidebar,
  toggleMobileSidebar,
  closeMobileSidebar
});
</script>

<style scoped>
/* Smooth transitions for all sidebar changes */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

/* Custom scrollbar for sidebar content */
:deep(.sidebar-scroll) {
  scrollbar-width: thin;
  scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
}

:deep(.sidebar-scroll::-webkit-scrollbar) {
  width: 6px;
}

:deep(.sidebar-scroll::-webkit-scrollbar-track) {
  background: transparent;
}

:deep(.sidebar-scroll::-webkit-scrollbar-thumb) {
  background-color: rgba(156, 163, 175, 0.5);
  border-radius: 3px;
}

:deep(.sidebar-scroll::-webkit-scrollbar-thumb:hover) {
  background-color: rgba(156, 163, 175, 0.7);
}
</style> 