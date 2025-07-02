<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Mobile Header -->
    <header 
      v-if="isMobile" 
      class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-50 lg:hidden"
    >
      <div class="flex items-center justify-between px-4 py-3">
        <!-- Mobile Menu Button -->
        <button
          @click="toggleMobileSidebar"
          class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors duration-200"
          aria-label="Toggle navigation menu"
        >
          <Bars3Icon v-if="!isMobileSidebarOpen" class="h-6 w-6" />
          <XMarkIcon v-else class="h-6 w-6" />
        </button>

        <!-- Mobile Logo -->
        <RouterLink to="/" class="flex items-center">
          <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-2">
            <span class="text-white font-bold text-sm">JP</span>
          </div>
          <span class="text-lg font-bold text-gray-900">JobPortal</span>
        </RouterLink>

        <!-- Mobile User Menu -->
        <div class="flex items-center space-x-2">
          <!-- Notifications -->
          <button
            v-if="isAuthenticated"
            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors duration-200 relative"
            aria-label="Notifications"
          >
            <BellIcon class="h-5 w-5" />
            <span 
              v-if="unreadNotifications > 0" 
              class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center"
            >
              {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
            </span>
          </button>

          <!-- User Avatar -->
          <div v-if="isAuthenticated" class="relative">
            <button
              @click="toggleUserMenu"
              class="flex items-center p-1 rounded-full hover:bg-gray-100 transition-colors duration-200"
            >
              <img
                v-if="user?.avatar"
                :src="user.avatar"
                :alt="user.name"
                class="h-8 w-8 rounded-full object-cover"
              />
              <div
                v-else
                class="h-8 w-8 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center"
              >
                <span class="text-white text-sm font-medium">
                  {{ (user?.name || 'U').charAt(0).toUpperCase() }}
                </span>
              </div>
            </button>
          </div>

          <!-- Login Button for Non-authenticated Users -->
          <BaseButton
            v-else
            variant="primary"
            size="sm"
            :to="{ name: 'login' }"
            tag="router-link"
          >
            Sign In
          </BaseButton>
        </div>
      </div>
    </header>

    <!-- Desktop Sidebar + Main Content -->
    <div class="flex">
      <!-- Desktop Sidebar -->
      <div
        v-if="!isMobile"
        :class="[
          'fixed top-0 left-0 h-full bg-white border-r border-gray-200 transition-all duration-300 z-40',
          isDesktopSidebarCollapsed ? 'w-16' : 'w-64'
        ]"
      >
        <SidebarNavigation
          :is-collapsed="isDesktopSidebarCollapsed"
          @toggle-collapse="toggleDesktopSidebar"
        />
      </div>

      <!-- Mobile Sidebar Overlay -->
      <Transition
        enter-active-class="transition-opacity duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="isMobile && isMobileSidebarOpen"
          class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
          @click="closeMobileSidebar"
        />
      </Transition>

      <!-- Mobile Sidebar -->
      <Transition
        enter-active-class="transition-transform duration-300"
        enter-from-class="-translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition-transform duration-300"
        leave-from-class="translate-x-0"
        leave-to-class="-translate-x-full"
      >
        <div
          v-if="isMobile && isMobileSidebarOpen"
          class="fixed top-0 left-0 h-full w-64 bg-white border-r border-gray-200 z-50 lg:hidden overflow-y-auto"
        >
          <SidebarNavigation
            :is-mobile="true"
            @close-mobile="closeMobileSidebar"
          />
        </div>
      </Transition>

      <!-- Main Content Area -->
      <main
        :class="[
          'flex-1 transition-all duration-300',
          !isMobile ? (isDesktopSidebarCollapsed ? 'ml-16' : 'ml-64') : 'ml-0',
          isMobile ? 'pt-16' : 'pt-0'
        ]"
      >
        <!-- Page Content -->
        <div class="min-h-screen">
          <!-- Breadcrumb Navigation (Desktop Only) -->
          <nav
            v-if="!isMobile && showBreadcrumbs"
            class="bg-white border-b border-gray-200 px-6 py-3"
            aria-label="Breadcrumb"
          >
            <ol class="flex items-center space-x-2 text-sm">
              <li v-for="(crumb, index) in breadcrumbs" :key="index">
                <div class="flex items-center">
                  <ChevronRightIcon
                    v-if="index > 0"
                    class="h-4 w-4 text-gray-400 mr-2"
                  />
                  <RouterLink
                    v-if="crumb.to && index < breadcrumbs.length - 1"
                    :to="crumb.to"
                    class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                  >
                    {{ crumb.label }}
                  </RouterLink>
                  <span
                    v-else
                    :class="[
                      index === breadcrumbs.length - 1
                        ? 'text-gray-900 font-medium'
                        : 'text-gray-600'
                    ]"
                  >
                    {{ crumb.label }}
                  </span>
                </div>
              </li>
            </ol>
          </nav>

          <!-- Page Header (if provided) -->
          <header
            v-if="$slots.header"
            :class="[
              'bg-white border-b border-gray-200',
              !isMobile && showBreadcrumbs ? '' : 'border-t-0'
            ]"
          >
            <slot name="header" />
          </header>

          <!-- Main Page Content -->
          <div class="flex-1">
            <slot />
          </div>
        </div>
      </main>
    </div>

    <!-- User Menu Dropdown (Mobile) -->
    <Transition
      enter-active-class="transition-all duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition-all duration-200"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="isMobile && isUserMenuOpen && isAuthenticated"
        class="fixed top-16 right-4 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50 py-2"
      >
        <div class="px-4 py-3 border-b border-gray-100">
          <div class="flex items-center space-x-3">
            <img
              v-if="user?.avatar"
              :src="user.avatar"
              :alt="user.name"
              class="h-10 w-10 rounded-full object-cover"
            />
            <div
              v-else
              class="h-10 w-10 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center"
            >
              <span class="text-white font-medium">
                {{ (user?.name || 'U').charAt(0).toUpperCase() }}
              </span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">
                {{ user?.name || 'User' }}
              </p>
              <p class="text-xs text-gray-500 truncate">
                {{ user?.email }}
              </p>
            </div>
          </div>
        </div>

        <div class="py-1">
          <RouterLink
            :to="{ name: `${userRole}.profile` }"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200"
            @click="closeUserMenu"
          >
            <UserIcon class="h-4 w-4 mr-3" />
            Profile Settings
          </RouterLink>

          <RouterLink
            v-if="userRole === 'candidate'"
            :to="{ name: 'candidate.applications' }"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200"
            @click="closeUserMenu"
          >
            <BriefcaseIcon class="h-4 w-4 mr-3" />
            My Applications
          </RouterLink>

          <RouterLink
            v-if="userRole === 'employer'"
            :to="{ name: 'employer.jobs' }"
            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200"
            @click="closeUserMenu"
          >
            <BuildingOfficeIcon class="h-4 w-4 mr-3" />
            My Job Posts
          </RouterLink>

          <button
            @click="handleLogout"
            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200"
          >
            <ArrowRightOnRectangleIcon class="h-4 w-4 mr-3" />
            Sign Out
          </button>
        </div>
      </div>
    </Transition>

    <!-- Global Loading Indicator -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isGlobalLoading"
        class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50"
      >
        <div class="flex items-center space-x-3">
          <div class="animate-spin h-8 w-8 border-4 border-indigo-600 border-t-transparent rounded-full"></div>
          <span class="text-gray-700 font-medium">Loading...</span>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import SidebarNavigation from '@/components/navigation/SidebarNavigation.vue';
import BaseButton from '@/components/base/BaseButton.vue';

// Icons
import {
  Bars3Icon,
  XMarkIcon,
  BellIcon,
  ChevronRightIcon,
  UserIcon,
  BriefcaseIcon,
  BuildingOfficeIcon,
  ArrowRightOnRectangleIcon
} from '@heroicons/vue/24/outline';

export interface BreadcrumbItem {
  label: string;
  to?: string;
}

export interface Props {
  showBreadcrumbs?: boolean;
  breadcrumbs?: BreadcrumbItem[];
  isLoading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showBreadcrumbs: true,
  breadcrumbs: () => [],
  isLoading: false
});

const route = useRoute();
const router = useRouter();
const { user, logout, isAuthenticated, userRole } = useAuth();

// State
const isMobile = ref(false);
const isDesktopSidebarCollapsed = ref(false);
const isMobileSidebarOpen = ref(false);
const isUserMenuOpen = ref(false);
const unreadNotifications = ref(3); // This would come from a store/API
const isGlobalLoading = ref(false);

// Computed
const showSidebar = computed(() => {
  // Show sidebar for authenticated users or on certain public pages
  return isAuthenticated.value || ['jobs.index', 'companies.index'].includes(route.name as string);
});

// Methods
const checkMobile = () => {
  isMobile.value = window.innerWidth < 1024; // lg breakpoint
};

const toggleDesktopSidebar = () => {
  isDesktopSidebarCollapsed.value = !isDesktopSidebarCollapsed.value;
  localStorage.setItem('sidebarCollapsed', String(isDesktopSidebarCollapsed.value));
};

const toggleMobileSidebar = () => {
  isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
  if (isMobileSidebarOpen.value) {
    document.body.style.overflow = 'hidden';
  } else {
    document.body.style.overflow = '';
  }
};

const closeMobileSidebar = () => {
  isMobileSidebarOpen.value = false;
  document.body.style.overflow = '';
};

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
};

const closeUserMenu = () => {
  isUserMenuOpen.value = false;
};

const handleLogout = async () => {
  try {
    await logout();
    router.push({ name: 'home' });
    closeUserMenu();
  } catch (error) {
    console.error('Logout error:', error);
  }
};

// Handle clicks outside user menu
const handleDocumentClick = (event: Event) => {
  const target = event.target as Element;
  if (isUserMenuOpen.value && !target.closest('.user-menu')) {
    closeUserMenu();
  }
};

// Load sidebar state from localStorage
const loadSidebarState = () => {
  const saved = localStorage.getItem('sidebarCollapsed');
  if (saved !== null) {
    isDesktopSidebarCollapsed.value = saved === 'true';
  }
};

// Watch for route changes to close mobile sidebar
watch(() => route.path, () => {
  closeMobileSidebar();
  closeUserMenu();
});

// Watch for loading state changes
watch(() => props.isLoading, (loading) => {
  isGlobalLoading.value = loading;
});

// Lifecycle
onMounted(() => {
  checkMobile();
  loadSidebarState();
  window.addEventListener('resize', checkMobile);
  document.addEventListener('click', handleDocumentClick);
});

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile);
  document.removeEventListener('click', handleDocumentClick);
  document.body.style.overflow = '';
});
</script>

<style scoped>
/* Ensure smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Mobile sidebar overlay animation */
.mobile-sidebar-overlay {
  backdrop-filter: blur(8px);
}

/* Prevent scroll when mobile sidebar is open */
body.sidebar-open {
  overflow: hidden;
}

/* Custom scrollbar for sidebar on webkit browsers */
.sidebar-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.sidebar-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 2px;
}

.sidebar-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.7);
}

/* Loading spinner animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Focus management for accessibility */
.focus-visible {
  outline: 2px solid #4f46e5;
  outline-offset: 2px;
}

/* Mobile-first responsive design helpers */
@media (max-width: 1023px) {
  .desktop-only {
    display: none;
  }
}

@media (min-width: 1024px) {
  .mobile-only {
    display: none;
  }
}

/* Smooth height transitions for dynamic content */
.smooth-height {
  transition: height 0.3s ease-in-out;
}

/* Card hover effects */
.hover-lift {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style> 