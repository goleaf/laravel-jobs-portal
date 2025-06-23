<template>
  <div class="relative inline-block">
    <button
      @click="toggleThemeMenu"
      class="inline-flex items-center p-2 rounded-md text-sm font-medium text-neutral-700 bg-white shadow-sm hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
      aria-expanded="false"
      aria-label="Toggle theme"
    >
      <svg v-if="currentTheme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.021 11.314l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      <svg v-else-if="currentTheme === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
      </svg>
      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
      </svg>
    </button>

    <div
      v-if="isThemeMenuOpen"
      class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="theme-button"
    >
      <button
        @click="setTheme('light')"
        class="w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700"
        :class="{ 'font-bold': currentTheme === 'light' }"
        role="menuitem"
      >
        <span class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.021 11.314l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          Light
        </span>
      </button>
      <button
        @click="setTheme('dark')"
        class="w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700"
        :class="{ 'font-bold': currentTheme === 'dark' }"
        role="menuitem"
      >
        <span class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
          Dark
        </span>
      </button>
      <button
        @click="setTheme('system')"
        class="w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700"
        :class="{ 'font-bold': currentTheme === 'system' }"
        role="menuitem"
      >
        <span class="flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
          </svg>
          System
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue'

const isThemeMenuOpen = ref(false)
const currentTheme = ref<'light' | 'dark' | 'system'>('system')

// Toggle dropdown menu visibility
const toggleThemeMenu = () => {
  isThemeMenuOpen.value = !isThemeMenuOpen.value
}

// Set theme and update localStorage
const setTheme = (theme: 'light' | 'dark' | 'system') => {
  currentTheme.value = theme
  if (theme === 'system') {
    localStorage.removeItem('theme')
  } else {
    localStorage.setItem('theme', theme)
  }
  applyTheme()
  isThemeMenuOpen.value = false
}

// Apply theme to document
const applyTheme = () => {
  let themeToApply = currentTheme.value
  if (themeToApply === 'system') {
    themeToApply = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
  }
  if (themeToApply === 'dark') {
    document.documentElement.classList.add('dark')
  } else {
    document.documentElement.classList.remove('dark')
  }
}

// Listen for system theme changes
const systemThemeListener = (e: MediaQueryListEvent) => {
  if (currentTheme.value === 'system') {
    applyTheme()
  }
}

onMounted(() => {
  // Load saved theme or system preference
  const savedTheme = localStorage.getItem('theme')
  if (savedTheme) {
    currentTheme.value = savedTheme as 'light' | 'dark'
  } else {
    currentTheme.value = 'system'
  }
  applyTheme()

  // Add listener for system theme changes
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', systemThemeListener)

  // Close dropdown when clicking outside
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    if (isThemeMenuOpen.value && !target.closest('.relative')) {
      isThemeMenuOpen.value = false
    }
  }
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  window.matchMedia('(prefers-color-scheme: dark)').removeEventListener('change', systemThemeListener)
})

watch(currentTheme, () => {
  applyTheme()
})
</script> 