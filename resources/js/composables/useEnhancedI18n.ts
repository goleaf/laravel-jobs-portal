import { ref, computed, reactive, watch } from 'vue'
import type { Ref } from 'vue'

/**
 * Enhanced Internationalization System
 * 
 * Comprehensive multilingual support with RTL, caching, and performance optimization
 * Supports 9 languages: EN, AR, DE, ES, FR, PT, RU, TR, ZH
 */

// Types
interface Translation {
  [key: string]: string | Translation
}

interface Language {
  code: string
  name: string
  nativeName: string
  direction: 'ltr' | 'rtl'
  flag: string
  region: string
}

interface TranslationCache {
  [locale: string]: {
    [namespace: string]: Translation
  }
}

// Available languages
export const SUPPORTED_LANGUAGES: Record<string, Language> = {
  en: {
    code: 'en',
    name: 'English',
    nativeName: 'English',
    direction: 'ltr',
    flag: '🇺🇸',
    region: 'US'
  },
  ar: {
    code: 'ar',
    name: 'Arabic',
    nativeName: 'العربية',
    direction: 'rtl',
    flag: '🇸🇦',
    region: 'SA'
  },
  de: {
    code: 'de',
    name: 'German',
    nativeName: 'Deutsch',
    direction: 'ltr',
    flag: '🇩🇪',
    region: 'DE'
  },
  es: {
    code: 'es',
    name: 'Spanish',
    nativeName: 'Español',
    direction: 'ltr',
    flag: '🇪🇸',
    region: 'ES'
  },
  fr: {
    code: 'fr',
    name: 'French',
    nativeName: 'Français',
    direction: 'ltr',
    flag: '🇫🇷',
    region: 'FR'
  },
  pt: {
    code: 'pt',
    name: 'Portuguese',
    nativeName: 'Português',
    direction: 'ltr',
    flag: '🇵🇹',
    region: 'PT'
  },
  ru: {
    code: 'ru',
    name: 'Russian',
    nativeName: 'Русский',
    direction: 'ltr',
    flag: '🇷🇺',
    region: 'RU'
  },
  tr: {
    code: 'tr',
    name: 'Turkish',
    nativeName: 'Türkçe',
    direction: 'ltr',
    flag: '🇹🇷',
    region: 'TR'
  },
  zh: {
    code: 'zh',
    name: 'Chinese',
    nativeName: '中文',
    direction: 'ltr',
    flag: '🇨🇳',
    region: 'CN'
  }
}

// Global state
const currentLocale: Ref<string> = ref('en')
const fallbackLocale = 'en'
const translations: TranslationCache = reactive({})
const loadingStates: Record<string, boolean> = reactive({})

// Storage keys
const LOCALE_STORAGE_KEY = 'enhanced_locale'
const TRANSLATIONS_CACHE_KEY = 'enhanced_translations'

/**
 * Enhanced I18n Composable
 */
export function useEnhancedI18n() {
  // Computed properties
  const locale = computed(() => currentLocale.value)
  
  const currentLanguage = computed(() => SUPPORTED_LANGUAGES[currentLocale.value])
  
  const isRTL = computed(() => currentLanguage.value?.direction === 'rtl')
  
  const availableLanguages = computed(() => Object.values(SUPPORTED_LANGUAGES))

  // Initialize from localStorage
  const initializeLocale = () => {
    const savedLocale = localStorage.getItem(LOCALE_STORAGE_KEY)
    const browserLocale = navigator.language.slice(0, 2)
    
    const detectedLocale = savedLocale || 
      (SUPPORTED_LANGUAGES[browserLocale] ? browserLocale : fallbackLocale)
    
    setLocale(detectedLocale)
  }

  // Set locale
  const setLocale = async (newLocale: string) => {
    if (!SUPPORTED_LANGUAGES[newLocale]) {
      console.warn(`EnhancedI18n: Unsupported locale "${newLocale}", falling back to "${fallbackLocale}"`)
      newLocale = fallbackLocale
    }

    currentLocale.value = newLocale
    
    // Save to localStorage
    localStorage.setItem(LOCALE_STORAGE_KEY, newLocale)
    
    // Update document attributes
    updateDocumentAttributes()
    
    // Load translations if not cached
    await loadTranslations(newLocale)
    
    // Emit event for other components
    window.dispatchEvent(new CustomEvent('enhanced-locale-changed', {
      detail: { locale: newLocale, language: SUPPORTED_LANGUAGES[newLocale] }
    }))
  }

  // Update document attributes for RTL/LTR support
  const updateDocumentAttributes = () => {
    const html = document.documentElement
    const lang = currentLanguage.value
    
    if (lang) {
      html.setAttribute('lang', lang.code)
      html.setAttribute('dir', lang.direction)
      html.classList.toggle('rtl', lang.direction === 'rtl')
    }
  }

  // Load translations for a locale
  const loadTranslations = async (locale: string, namespace = 'common') => {
    const cacheKey = `${locale}_${namespace}`
    
    if (loadingStates[cacheKey]) {
      return // Already loading
    }
    
    if (translations[locale]?.[namespace]) {
      return // Already loaded
    }

    loadingStates[cacheKey] = true

    try {
      // Try to load from cache first
      const cachedTranslations = loadFromCache(locale, namespace)
      if (cachedTranslations) {
        if (!translations[locale]) {
          translations[locale] = {}
        }
        translations[locale][namespace] = cachedTranslations
        return
      }

      // Load from server
      const response = await fetch(`/api/translations/${locale}/${namespace}`)
      
      if (!response.ok) {
        throw new Error(`Failed to load translations: ${response.statusText}`)
      }

      const data = await response.json()

      // Store in reactive state
      if (!translations[locale]) {
        translations[locale] = {}
      }
      translations[locale][namespace] = data

      // Cache in localStorage
      saveToCache(locale, namespace, data)

    } catch (error) {
      console.error(`EnhancedI18n: Failed to load translations for ${locale}/${namespace}:`, error)
      
      // Fallback to default locale if not already trying fallback
      if (locale !== fallbackLocale) {
        await loadTranslations(fallbackLocale, namespace)
      }
    } finally {
      loadingStates[cacheKey] = false
    }
  }

  // Translation function
  const t = (key: string, params: Record<string, any> = {}, namespace = 'common') => {
    const locale = currentLocale.value
    
    // Get translation value
    let translation = getNestedTranslation(translations[locale]?.[namespace], key) ||
                     getNestedTranslation(translations[fallbackLocale]?.[namespace], key) ||
                     key

    // Handle parameterized translations
    if (typeof translation === 'string' && Object.keys(params).length > 0) {
      translation = interpolateString(translation, params)
    }

    return translation
  }

  // Pluralization function
  const tn = (key: string, count: number, params: Record<string, any> = {}, namespace = 'common') => {
    const pluralKey = getPluralKey(key, count, currentLocale.value)
    return t(pluralKey, { ...params, count }, namespace)
  }

  // Date formatting
  const formatDate = (date: Date | string, options: Intl.DateTimeFormatOptions = {}) => {
    const dateObj = typeof date === 'string' ? new Date(date) : date
    const lang = currentLanguage.value
    
    return new Intl.DateTimeFormat(`${lang.code}-${lang.region}`, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      ...options
    }).format(dateObj)
  }

  // Number formatting
  const formatNumber = (number: number, options: Intl.NumberFormatOptions = {}) => {
    const lang = currentLanguage.value
    
    return new Intl.NumberFormat(`${lang.code}-${lang.region}`, options).format(number)
  }

  // Currency formatting
  const formatCurrency = (amount: number, currency = 'USD', options: Intl.NumberFormatOptions = {}) => {
    const lang = currentLanguage.value
    
    return new Intl.NumberFormat(`${lang.code}-${lang.region}`, {
      style: 'currency',
      currency,
      ...options
    }).format(amount)
  }

  // Helper functions
  const getNestedTranslation = (obj: Translation | undefined, key: string): string | undefined => {
    if (!obj) return undefined
    
    const keys = key.split('.')
    let current: any = obj
    
    for (const k of keys) {
      if (current && typeof current === 'object' && k in current) {
        current = current[k]
      } else {
        return undefined
      }
    }
    
    return typeof current === 'string' ? current : undefined
  }

  const interpolateString = (str: string, params: Record<string, any>): string => {
    return str.replace(/\{\{(\w+)\}\}/g, (match, key) => {
      return params[key] !== undefined ? String(params[key]) : match
    })
  }

  const getPluralKey = (key: string, count: number, locale: string): string => {
    const rules = new Intl.PluralRules(locale)
    const rule = rules.select(count)
    
    // Try different plural forms
    const pluralKeys = [
      `${key}.${rule}`,           // key.zero, key.one, key.two, key.few, key.many, key.other
      `${key}_${rule}`,           // key_zero, key_one, etc.
      `${key}.${count}`,          // key.0, key.1, key.2, etc.
      key                         // fallback to base key
    ]
    
    for (const pluralKey of pluralKeys) {
      if (getNestedTranslation(translations[locale]?.common, pluralKey)) {
        return pluralKey
      }
    }
    
    return key
  }

  // Cache management
  const saveToCache = (locale: string, namespace: string, data: Translation) => {
    try {
      const cacheKey = `${TRANSLATIONS_CACHE_KEY}_${locale}_${namespace}`
      const cacheData = {
        data,
        timestamp: Date.now(),
        version: '1.0'
      }
      localStorage.setItem(cacheKey, JSON.stringify(cacheData))
    } catch (error) {
      console.warn('EnhancedI18n: Failed to save translations to cache:', error)
    }
  }

  const loadFromCache = (locale: string, namespace: string): Translation | null => {
    try {
      const cacheKey = `${TRANSLATIONS_CACHE_KEY}_${locale}_${namespace}`
      const cached = localStorage.getItem(cacheKey)
      
      if (!cached) return null
      
      const cacheData = JSON.parse(cached)
      
      // Check if cache is still valid (24 hours)
      const maxAge = 24 * 60 * 60 * 1000
      if (Date.now() - cacheData.timestamp > maxAge) {
        localStorage.removeItem(cacheKey)
        return null
      }
      
      return cacheData.data
    } catch (error) {
      console.warn('EnhancedI18n: Failed to load translations from cache:', error)
      return null
    }
  }

  const clearCache = () => {
    try {
      const keys = Object.keys(localStorage)
      keys.forEach(key => {
        if (key.startsWith(TRANSLATIONS_CACHE_KEY)) {
          localStorage.removeItem(key)
        }
      })
    } catch (error) {
      console.warn('EnhancedI18n: Failed to clear translations cache:', error)
    }
  }

  // Preload common translations
  const preloadTranslations = async (locales: string[] = [], namespaces: string[] = ['common']) => {
    const promises: Promise<void>[] = []
    
    locales.forEach(locale => {
      namespaces.forEach(namespace => {
        promises.push(loadTranslations(locale, namespace))
      })
    })
    
    await Promise.all(promises)
  }

  // Watch for locale changes to update document
  watch(currentLocale, () => {
    updateDocumentAttributes()
  })

  // Initialize on first use
  if (typeof window !== 'undefined' && !currentLocale.value) {
    initializeLocale()
  }

  return {
    // State
    locale,
    currentLanguage,
    isRTL,
    availableLanguages,
    translations,
    
    // Actions
    setLocale,
    loadTranslations,
    preloadTranslations,
    clearCache,
    
    // Translation functions
    t,
    tn,
    
    // Formatting functions
    formatDate,
    formatNumber,
    formatCurrency,
    
    // Utilities
    updateDocumentAttributes,
    
    // Constants
    SUPPORTED_LANGUAGES
  }
}

// Global instance for use outside of Vue components
export const enhancedI18n = useEnhancedI18n()

// Auto-initialize
if (typeof window !== 'undefined') {
  // Load common translations for current locale
  enhancedI18n.loadTranslations(enhancedI18n.locale.value, 'common')
  
  // Preload critical namespaces
  enhancedI18n.preloadTranslations([enhancedI18n.locale.value], ['common', 'navigation', 'forms', 'validation'])
} 