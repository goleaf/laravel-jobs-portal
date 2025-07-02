<template>
  <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo and Brand -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow duration-200">
              <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6" />
              </svg>
            </div>
            <span class="text-xl font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-200">
              JobPortal
            </span>
          </router-link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <router-link
            to="/"
            class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200"
            active-class="text-indigo-600 font-semibold"
          >
            Home
          </router-link>
          <router-link
            to="/jobs"
            class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200"
            active-class="text-indigo-600 font-semibold"
          >
            Jobs
          </router-link>
          <router-link
            to="/companies"
            class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200"
            active-class="text-indigo-600 font-semibold"
          >
            Companies
          </router-link>
          <router-link
            to="/about"
            class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200"
            active-class="text-indigo-600 font-semibold"
          >
            About
          </router-link>
          <router-link
            to="/contact"
            class="text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200"
            active-class="text-indigo-600 font-semibold"
          >
            Contact
          </router-link>
        </nav>

        <!-- Right Side Actions -->
        <div class="flex items-center space-x-4">
          <!-- Quick Search -->
          <div class="hidden lg:flex items-center">
            <div class="relative">
              <input
                v-model="quickSearch"
                type="text"
                placeholder="Quick job search..."
                @keyup.enter="performQuickSearch"
                @focus="showQuickSearchResults = true"
                @blur="hideQuickSearchResults"
                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
              />
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
              
              <!-- Quick Search Results -->
              <div
                v-if="showQuickSearchResults && quickSearchResults.length > 0"
                class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
              >
                <div class="py-2">
                  <div
                    v-for="result in quickSearchResults"
                    :key="result.id"
                    @click="navigateToJob(result)"
                    class="px-4 py-2 hover:bg-gray-50 cursor-pointer"
                  >
                    <div class="text-sm font-medium text-gray-900">{{ result.title }}</div>
                    <div class="text-xs text-gray-500">{{ result.company }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Language Toggle -->
          <div class="relative">
            <button
              @click="toggleLanguageDropdown"
              class="flex items-center space-x-1 text-gray-700 hover:text-indigo-600 transition-colors duration-200"
            >
              <GlobeAltIcon class="h-5 w-5" />
              <span class="text-sm font-medium">{{ currentLanguage.toUpperCase() }}</span>
              <ChevronDownIcon class="h-4 w-4" />
            </button>
            
            <!-- Language Dropdown -->
            <div
              v-if="showLanguageDropdown"
              v-click-outside="() => showLanguageDropdown = false"
              class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
            >
              <div class="py-2">
                <button
                  v-for="lang in availableLanguages"
                  :key="lang.code"
                  @click="changeLanguage(lang.code)"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                  :class="{ 'bg-indigo-50 text-indigo-600': currentLanguage === lang.code }"
                >
                  {{ lang.name }}
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="$emit('toggle-mobile-menu')"
            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-100 transition-colors duration-200"
          >
            <Bars3Icon v-if="!isMobileMenuOpen" class="h-6 w-6" />
            <XMarkIcon v-else class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Mobile Navigation -->
      <div v-if="isMobileMenuOpen" class="md:hidden border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <router-link
            to="/"
            @click="$emit('toggle-mobile-menu')"
            class="block px-3 py-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-50 font-medium transition-colors duration-200"
            active-class="bg-indigo-50 text-indigo-600"
          >
            Home
          </router-link>
          <router-link
            to="/jobs"
            @click="$emit('toggle-mobile-menu')"
            class="block px-3 py-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-50 font-medium transition-colors duration-200"
            active-class="bg-indigo-50 text-indigo-600"
          >
            Jobs
          </router-link>
          <router-link
            to="/companies"
            @click="$emit('toggle-mobile-menu')"
            class="block px-3 py-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-50 font-medium transition-colors duration-200"
            active-class="bg-indigo-50 text-indigo-600"
          >
            Companies
          </router-link>
          <router-link
            to="/about"
            @click="$emit('toggle-mobile-menu')"
            class="block px-3 py-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-50 font-medium transition-colors duration-200"
            active-class="bg-indigo-50 text-indigo-600"
          >
            About
          </router-link>
          <router-link
            to="/contact"
            @click="$emit('toggle-mobile-menu')"
            class="block px-3 py-2 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-50 font-medium transition-colors duration-200"
            active-class="bg-indigo-50 text-indigo-600"
          >
            Contact
          </router-link>
        </div>
        
        <!-- Mobile Search -->
        <div class="px-4 py-3 border-t border-gray-200">
          <div class="relative">
            <input
              v-model="quickSearch"
              type="text"
              placeholder="Search jobs..."
              @keyup.enter="performQuickSearch"
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
            />
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import {
  MagnifyingGlassIcon,
  GlobeAltIcon,
  ChevronDownIcon,
  Bars3Icon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

interface Props {
  isMobileMenuOpen?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  isMobileMenuOpen: false
})

const emit = defineEmits<{
  'toggle-mobile-menu': []
}>()

const router = useRouter()
const { locale } = useI18n()

// State
const quickSearch = ref('')
const showQuickSearchResults = ref(false)
const showLanguageDropdown = ref(false)
const quickSearchResults = ref([])

// Language management
const currentLanguage = computed(() => locale.value)
const availableLanguages = ref([
  { code: 'en', name: 'English' },
  { code: 'lt', name: 'Lietuvių' }
])

// Methods
const performQuickSearch = () => {
  if (quickSearch.value.trim()) {
    router.push({
      name: 'jobs.index',
      query: { search: quickSearch.value.trim() }
    })
    quickSearch.value = ''
    showQuickSearchResults.value = false
  }
}

const navigateToJob = (job: any) => {
  router.push({ name: 'jobs.show', params: { id: job.id } })
  showQuickSearchResults.value = false
  quickSearch.value = ''
}

const hideQuickSearchResults = () => {
  setTimeout(() => {
    showQuickSearchResults.value = false
  }, 200)
}

const toggleLanguageDropdown = () => {
  showLanguageDropdown.value = !showLanguageDropdown.value
}

const changeLanguage = (langCode: string) => {
  locale.value = langCode
  showLanguageDropdown.value = false
  localStorage.setItem('preferred-language', langCode)
}

// Watch for quick search changes
watch(quickSearch, async (newValue) => {
  if (newValue.trim().length > 2) {
    // Simulate API call for quick search results
    // In real app, this would be an actual API call
    quickSearchResults.value = [
      { id: 1, title: 'Frontend Developer', company: 'TechCorp' },
      { id: 2, title: 'Backend Engineer', company: 'DataSoft' },
      { id: 3, title: 'Full Stack Developer', company: 'WebFlow' }
    ].filter(job => 
      job.title.toLowerCase().includes(newValue.toLowerCase()) ||
      job.company.toLowerCase().includes(newValue.toLowerCase())
    )
  } else {
    quickSearchResults.value = []
  }
})

// Custom directive for click outside
const vClickOutside = {
  beforeMount(el: any, binding: any) {
    el.clickOutsideEvent = (event: Event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value()
      }
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el: any) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}
</script>