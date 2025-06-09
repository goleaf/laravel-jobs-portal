<template>
  <header class="bg-white shadow-sm border-b border-neutral-200 sticky top-0 z-30 dark:bg-neutral-800 dark:border-neutral-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Left Section: Logo and Mobile Menu -->
        <div class="flex items-center space-x-4">
          <!-- Mobile Menu Button -->
          <button
            @click="$emit('toggle-mobile-menu')"
            class="lg:hidden p-2 rounded-md text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 transition-colors dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-700"
          >
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>

          <!-- Logo -->
          <router-link to="/" class="flex items-center space-x-2 group">
            <div class="h-8 w-8 bg-gradient-to-br from-primary-600 to-purple-600 rounded-lg flex items-center justify-center">
              <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <span class="text-xl font-bold text-neutral-900 group-hover:text-primary-600 transition-colors dark:text-white dark:group-hover:text-primary-400">
              JobPortal
            </span>
          </router-link>
        </div>

        <!-- Center Section: Navigation (Desktop) -->
        <nav class="hidden lg:flex items-center space-x-8">
          <router-link 
            v-for="item in navigation" 
            :key="item.name"
            :to="item.href"
            class="text-neutral-600 hover:text-primary-600 font-medium transition-colors relative group dark:text-neutral-300 dark:hover:text-primary-400"
            :class="{ 'text-primary-600 dark:text-primary-400': isActiveRoute(item.href) }"
          >
            {{ item.name }}
            <span 
              class="absolute -bottom-6 left-0 w-full h-0.5 bg-primary-600 transform scale-x-0 transition-transform group-hover:scale-x-100"
              :class="{ 'scale-x-100': isActiveRoute(item.href) }"
            ></span>
          </router-link>
        </nav>

        <!-- Right Section: Search, Notifications, User Menu, Theme Toggle, Language Switcher -->
        <div class="flex items-center space-x-4">
          <!-- Search -->
          <div class="hidden md:block relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-neutral-400 dark:text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search jobs, companies..."
              class="pl-10 pr-4 py-2 w-64 text-sm border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all dark:bg-neutral-700 dark:border-neutral-600 dark:text-white dark:placeholder-neutral-400"
              @keyup.enter="handleSearch"
            />
          </div>

          <!-- Search Button (Mobile) -->
          <button class="md:hidden p-2 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-md transition-colors dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-700">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>

          <!-- Theme Toggle -->
          <ThemeToggle />

          <!-- Language Switcher -->
          <LanguageSwitcher />

          <!-- Notifications -->
          <div class="relative">
            <button 
              @click="toggleNotifications"
              class="p-2 text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 rounded-md transition-colors relative dark:text-neutral-300 dark:hover:text-white dark:hover:bg-neutral-700"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <!-- Notification Badge -->
              <span v-if="unreadNotifications > 0" class="absolute -top-1 -right-1 h-5 w-5 bg-error-500 text-white text-xs rounded-full flex items-center justify-center">
                {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
              </span>
            </button>

            <!-- Notifications Dropdown -->
            <div 
              v-if="showNotifications"
              class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-neutral-200 py-2 z-50 dark:bg-neutral-800 dark:border-neutral-700"
            >
              <div class="px-4 py-2 border-b border-neutral-200 dark:border-neutral-700">
                <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Notifications</h3>
              </div>
              <div class="max-h-64 overflow-y-auto">
                <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-neutral-500 dark:text-neutral-400">
                  No notifications
                </div>
                <div v-else>
                  <div 
                    v-for="notification in notifications.slice(0, 5)" 
                    :key="notification.id"
                    class="px-4 py-3 hover:bg-neutral-50 cursor-pointer border-b border-neutral-100 dark:hover:bg-neutral-700 dark:border-neutral-700"
                    @click="handleNotificationClick(notification)"
                  >
                    <p class="text-sm text-neutral-900 dark:text-white">{{ notification.title }}</p>
                    <p class="text-xs text-neutral-500 mt-1 dark:text-neutral-400">{{ notification.time }}</p>
                  </div>
                </div>
              </div>
              <div class="px-4 py-2 border-t border-neutral-200 dark:border-neutral-700">
                <router-link to="/notifications" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                  View all notifications
                </router-link>
              </div>
            </div>
          </div>

          <!-- User Menu -->
          <div class="relative">
            <button 
              @click="toggleUserMenu"
              class="flex items-center space-x-2 p-1 rounded-lg hover:bg-neutral-100 transition-colors dark:hover:bg-neutral-700"
            >
              <div class="h-8 w-8 bg-primary-600 rounded-full flex items-center justify-center">
                <span class="text-sm font-medium text-white">
                  {{ user?.name?.charAt(0) || 'U' }}
                </span>
              </div>
              <svg class="h-4 w-4 text-neutral-600 dark:text-neutral-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- User Dropdown -->
            <div 
              v-if="showUserMenu"
              class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-neutral-200 py-2 z-50 dark:bg-neutral-800 dark:border-neutral-700"
            >
              <div class="px-4 py-2 border-b border-neutral-200 dark:border-neutral-700">
                <p class="text-sm font-medium text-neutral-900 dark:text-white">{{ user?.name }}</p>
                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ user?.email }}</p>
              </div>
              <div class="py-1">
                <router-link 
                  v-for="item in userMenuItems" 
                  :key="item.name"
                  :to="item.href"
                  class="block px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 transition-colors dark:text-neutral-300 dark:hover:bg-neutral-700"
                >
                  {{ item.name }}
                </router-link>
              </div>
              <div class="border-t border-neutral-200 py-1 dark:border-neutral-700">
                <button 
                  @click="handleLogout"
                  class="block w-full text-left px-4 py-2 text-sm text-error-600 hover:bg-neutral-100 transition-colors dark:text-error-400 dark:hover:bg-neutral-700"
                >
                  Sign out
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import ThemeToggle from '../ui/ThemeToggle.vue'
import LanguageSwitcher from '../ui/LanguageSwitcher.vue'

const emit = defineEmits<{
  'toggle-mobile-menu': []
}>()

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const searchQuery = ref('')
const showNotifications = ref(false)
const showUserMenu = ref(false)

const navigation = [
  { name: 'Home', href: '/' },
  { name: 'Jobs', href: '/jobs' },
  { name: 'Companies', href: '/companies' },
  { name: 'Candidates', href: '/candidates' },
]

const userMenuItems = [
  { name: 'Profile', href: '/profile' },
  { name: 'Dashboard', href: '/dashboard' },
  { name: 'Settings', href: '/settings' },
]

const user = computed(() => authStore.user)
const notifications = computed(() => authStore.notifications || [])
const unreadNotifications = computed(() => notifications.value.filter(n => !n.read).length)

const isActiveRoute = (href: string) => {
  return route.path === href || (href !== '/' && route.path.startsWith(href))
}

const toggleNotifications = () => {
  showNotifications.value = !showNotifications.value
  showUserMenu.value = false
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
  showNotifications.value = false
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ path: '/search', query: { q: searchQuery.value } })
  }
}

const handleNotificationClick = (notification: any) => {
  // Mark as read and navigate
  authStore.markNotificationAsRead(notification.id)
  if (notification.link) {
    router.push(notification.link)
  }
  showNotifications.value = false
}

const handleLogout = async () => {
  await authStore.logout()
  router.push('/auth/login')
  showUserMenu.value = false
}

// Close dropdowns when clicking outside
const closeDropdowns = (event: Event) => {
  const target = event.target as HTMLElement
  if (!target.closest('.relative')) {
    showNotifications.value = false
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns)
})
</script>