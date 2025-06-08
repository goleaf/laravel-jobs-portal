<template>
  <div class="relative inline-block text-left">
    <!-- Language Switcher Button -->
    <button
      @click="toggleDropdown"
      class="inline-flex items-center justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      :aria-expanded="isOpen"
      aria-haspopup="true"
    >
      <span class="mr-2 text-lg">{{ currentLanguage.flag }}</span>
      <span>{{ currentLanguage.name }}</span>
      <svg
        class="ml-2 -mr-1 h-5 w-5 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <!-- Language Dropdown Menu -->
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-show="isOpen"
        class="origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="language-menu"
      >
        <div class="py-1" role="none">
          <button
            v-for="language in languages"
            :key="language.code"
            @click="changeLanguage(language.code)"
            class="group flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150"
            :class="{
              'bg-gray-100 text-gray-900': currentLanguage.code === language.code,
              'text-gray-700': currentLanguage.code !== language.code,
            }"
            role="menuitem"
          >
            <span class="mr-3 text-lg">{{ language.flag }}</span>
            <div class="flex-1 text-left">
              <div class="font-medium">{{ language.name }}</div>
              <div class="text-xs text-gray-500">{{ language.nativeName }}</div>
            </div>
            <svg
              v-if="currentLanguage.code === language.code"
              class="ml-2 h-4 w-4 text-indigo-600"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M5 13l4 4L19 7"
              />
            </svg>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';

// Language configuration with Context7 patterns
const languages = [
  { code: 'en', name: 'English', nativeName: 'English', flag: '🇺🇸', dir: 'ltr' },
  { code: 'ar', name: 'Arabic', nativeName: 'العربية', flag: '🇸🇦', dir: 'rtl' },
  { code: 'de', name: 'German', nativeName: 'Deutsch', flag: '🇩🇪', dir: 'ltr' },
  { code: 'es', name: 'Spanish', nativeName: 'Español', flag: '🇪🇸', dir: 'ltr' },
  { code: 'fr', name: 'French', nativeName: 'Français', flag: '🇫🇷', dir: 'ltr' },
  { code: 'pt', name: 'Portuguese', nativeName: 'Português', flag: '🇵🇹', dir: 'ltr' },
  { code: 'ru', name: 'Russian', nativeName: 'Русский', flag: '🇷🇺', dir: 'ltr' },
  { code: 'tr', name: 'Turkish', nativeName: 'Türkçe', flag: '🇹🇷', dir: 'ltr' },
  { code: 'zh', name: 'Chinese', nativeName: '中文', flag: '🇨🇳', dir: 'ltr' },
];

// Reactive state
const isOpen = ref(false);
const currentLocale = ref(localStorage.getItem('locale') || 'en');

// Computed properties
const currentLanguage = computed(() => {
  return languages.find(lang => lang.code === currentLocale.value) || languages[0];
});

// Methods
const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const changeLanguage = async (languageCode: string) => {
  try {
    // Update local state
    currentLocale.value = languageCode;
    
    // Store in localStorage
    localStorage.setItem('locale', languageCode);
    
    // Close dropdown
    isOpen.value = false;
    
    // Find language config
    const language = languages.find(lang => lang.code === languageCode);
    
    if (language) {
      // Update document direction for RTL languages
      document.documentElement.dir = language.dir;
      document.documentElement.lang = languageCode;
      
      // Add/remove RTL class for Arabic
      if (language.dir === 'rtl') {
        document.documentElement.classList.add('rtl');
      } else {
        document.documentElement.classList.remove('rtl');
      }
    }
    
    // Send request to Laravel backend to update session locale
    await fetch('/api/locale', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({ locale: languageCode }),
    });
    
    // Emit custom event for other components
    window.dispatchEvent(new CustomEvent('locale-changed', {
      detail: { locale: languageCode, language }
    }));
    
    // Reload page to apply Laravel translations
    window.location.reload();
    
  } catch (error) {
    console.error('Error changing language:', error);
  }
};

const handleClickOutside = (event: Event) => {
  const target = event.target as Element;
  if (!target.closest('.relative')) {
    isOpen.value = false;
  }
};

// Lifecycle hooks
onMounted(() => {
  // Set initial language direction
  const language = currentLanguage.value;
  document.documentElement.dir = language.dir;
  document.documentElement.lang = language.code;
  
  if (language.dir === 'rtl') {
    document.documentElement.classList.add('rtl');
  }
  
  // Add click outside listener
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
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