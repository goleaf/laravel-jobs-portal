<template>
  <div class="relative inline-block">
    <button
      @click="toggleLanguageMenu"
      class="inline-flex items-center p-2 rounded-md text-sm font-medium text-neutral-700 bg-white shadow-sm hover:bg-neutral-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700"
      aria-expanded="false"
      aria-label="Switch language"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8a2.5 2.5 0 012.5 2.5c0 1.186-.474 2.252-1.232 3.03a17.319 17.319 0 014.232-.495l.468-.935a2 2 0 011.289-2.573 16.947 16.947 0 001.024-2.204 18.108 18.108 0 001.44-5.243A18.09 18.09 0 0017.5 3.675" />
      </svg>
      <span class="ml-2 hidden md:inline">{{ currentLanguage ? currentLanguage.name : 'Language' }}</span>
      <svg class="ml-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div
      v-if="isLanguageMenuOpen"
      class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="language-button"
    >
      <button
        v-for="lang in languages"
        :key="lang.code"
        @click="setLanguage(lang.code)"
        class="w-full text-left px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-700"
        :class="{ 'font-bold': currentLanguage && currentLanguage.code === lang.code }"
        role="menuitem"
      >
        <span class="flex items-center">
          <span class="mr-2">{{ lang.flag }}</span>
          {{ lang.name }}
        </span>
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue'

interface Language {
  code: string
  name: string
  flag: string
}

const isLanguageMenuOpen = ref(false)
const languages: Language[] = [
  { code: 'en', name: 'English', flag: '🇺🇸' },
  { code: 'ar', name: 'Arabic', flag: '🇸🇦' },
  { code: 'de', name: 'German', flag: '🇩🇪' },
  { code: 'es', name: 'Spanish', flag: '🇪🇸' },
  { code: 'fr', name: 'French', flag: '🇫🇷' },
  { code: 'pt', name: 'Portuguese', flag: '🇵🇹' },
  { code: 'ru', name: 'Russian', flag: '🇷🇺' },
  { code: 'tr', name: 'Turkish', flag: '🇹🇷' },
  { code: 'zh', name: 'Chinese', flag: '🇨🇳' }
]
const currentLanguageCode = ref<string>('en')

const currentLanguage = computed(() => {
  return languages.find(lang => lang.code === currentLanguageCode.value) || null
})

// Toggle dropdown menu visibility
const toggleLanguageMenu = () => {
  isLanguageMenuOpen.value = !isLanguageMenuOpen.value
}

// Set language and update localStorage
const setLanguage = (code: string) => {
  currentLanguageCode.value = code
  localStorage.setItem('language', code)
  // Emit event to parent or use a store to notify about language change
  applyLanguageDirection(code)
  isLanguageMenuOpen.value = false
}

// Apply language direction (RTL for Arabic)
const applyLanguageDirection = (code: string) => {
  if (code === 'ar') {
    document.documentElement.setAttribute('dir', 'rtl')
    document.documentElement.setAttribute('lang', 'ar')
  } else {
    document.documentElement.setAttribute('dir', 'ltr')
    document.documentElement.setAttribute('lang', code)
  }
}

onMounted(() => {
  // Load saved language
  const savedLanguage = localStorage.getItem('language')
  if (savedLanguage) {
    currentLanguageCode.value = savedLanguage
  } else {
    // Detect browser language
    const browserLang = navigator.language.split('-')[0]
    if (languages.some(lang => lang.code === browserLang)) {
      currentLanguageCode.value = browserLang
    }
  }
  applyLanguageDirection(currentLanguageCode.value)

  // Close dropdown when clicking outside
  const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement
    if (isLanguageMenuOpen.value && !target.closest('.relative')) {
      isLanguageMenuOpen.value = false
    }
  }
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', () => {}) // Clean up event listener
})
</script>

<style scoped>
/* RTL Support */
:global(.rtl) .origin-top-right {
  @apply origin-top-left;
}

:global(.rtl) .right-0 {
  @apply left-0 right-auto;
}

/* Smooth transitions */
.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Loading state */
.language-switcher-loading {
  opacity: 0.6;
  pointer-events: none;
}
</style> 