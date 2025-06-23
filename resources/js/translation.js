/**
 * Frontend Translation Helper
 * Comprehensive internationalization support for JavaScript/Vue.js
 * 
 * Features:
 * - Translation loading from API
 * - Caching and performance optimization
 * - Parameter replacement
 * - Pluralization support
 * - RTL language detection
 * - Date/number formatting
 */

class TranslationManager {
    constructor() {
        this.translations = {};
        this.currentLocale = 'en';
        this.fallbackLocale = 'en';
        this.isRTL = false;
        this.availableLocales = [];
        this.loadPromises = {};
        this.cache = new Map();
        
        this.init();
    }

    /**
     * Initialize the translation manager
     */
    async init() {
        try {
            // Get current locale info from backend
            const response = await fetch('/locale/current');
            const data = await response.json();
            
            this.currentLocale = data.current;
            this.isRTL = data.is_rtl;
            this.availableLocales = Object.keys(data.available_locales);
            
            // Load initial translations
            await this.loadTranslations(this.currentLocale);
            
            // Set up HTML attributes
            this.updateHTMLAttributes();
            
            console.log('🌍 Translation Manager initialized:', {
                locale: this.currentLocale,
                isRTL: this.isRTL,
                available: this.availableLocales
            });
            
        } catch (error) {
            console.error('❌ Translation Manager initialization failed:', error);
        }
    }

    /**
     * Load translations for a specific locale
     */
    async loadTranslations(locale, namespace = null) {
        const cacheKey = `${locale}-${namespace || 'all'}`;
        
        // Return cached promise if already loading
        if (this.loadPromises[cacheKey]) {
            return this.loadPromises[cacheKey];
        }

        // Check cache first
        if (this.cache.has(cacheKey)) {
            this.translations[locale] = this.cache.get(cacheKey);
            return this.translations[locale];
        }

        // Load from API
        this.loadPromises[cacheKey] = this.fetchTranslations(locale, namespace);
        
        try {
            const translations = await this.loadPromises[cacheKey];
            this.translations[locale] = translations;
            this.cache.set(cacheKey, translations);
            return translations;
        } finally {
            delete this.loadPromises[cacheKey];
        }
    }

    /**
     * Fetch translations from API
     */
    async fetchTranslations(locale, namespace = null) {
        try {
            const url = `/locale/translations/${locale}${namespace ? `?namespace=${namespace}` : ''}`;
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            return data.translations || {};
        } catch (error) {
            console.warn(`Failed to load translations for ${locale}:`, error);
            return {};
        }
    }

    /**
     * Get translation with parameter replacement
     */
    trans(key, params = {}, locale = null) {
        locale = locale || this.currentLocale;
        
        // Get translation
        let translation = this.getTranslation(key, locale);
        
        // Fallback to fallback locale if not found
        if (translation === key && locale !== this.fallbackLocale) {
            translation = this.getTranslation(key, this.fallbackLocale);
        }
        
        // Replace parameters
        if (params && typeof params === 'object') {
            Object.keys(params).forEach(param => {
                const placeholder = `:${param}`;
                translation = translation.replace(new RegExp(placeholder, 'g'), params[param]);
            });
        }
        
        return translation;
    }

    /**
     * Get raw translation without fallback
     */
    getTranslation(key, locale) {
        const translations = this.translations[locale] || {};
        
        // Support dot notation
        const keys = key.split('.');
        let value = translations;
        
        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                return key; // Return key if not found
            }
        }
        
        return typeof value === 'string' ? value : key;
    }

    /**
     * Pluralization support
     */
    transChoice(key, count, params = {}, locale = null) {
        locale = locale || this.currentLocale;
        
        // Get the pluralization rule
        const translation = this.trans(key, params, locale);
        
        // Simple pluralization logic
        // In a full implementation, you'd use proper ICU pluralization rules
        if (translation.includes('|')) {
            const parts = translation.split('|');
            
            // Simple rule: 0-1 uses first part, 2+ uses second part
            if (count <= 1 && parts[0]) {
                return parts[0].replace(':count', count);
            } else if (parts[1]) {
                return parts[1].replace(':count', count);
            }
        }
        
        return translation.replace(':count', count);
    }

    /**
     * Check if translation key exists
     */
    has(key, locale = null) {
        locale = locale || this.currentLocale;
        const translation = this.getTranslation(key, locale);
        return translation !== key;
    }

    /**
     * Switch to a different locale
     */
    async switchLocale(locale) {
        if (!this.availableLocales.includes(locale)) {
            console.warn(`Locale ${locale} is not available`);
            return false;
        }

        try {
            // Switch on backend
            const response = await fetch('/locale/switch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ locale })
            });

            const data = await response.json();
            
            if (data.success) {
                this.currentLocale = locale;
                this.isRTL = data.is_rtl;
                
                // Load translations for new locale
                await this.loadTranslations(locale);
                
                // Update HTML attributes
                this.updateHTMLAttributes();
                
                // Trigger event for other components
                this.triggerLocaleChangeEvent(locale);
                
                return true;
            } else {
                console.error('Locale switch failed:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Error switching locale:', error);
            return false;
        }
    }

    /**
     * Get available locales
     */
    getAvailableLocales() {
        return this.availableLocales;
    }

    /**
     * Get current locale
     */
    getCurrentLocale() {
        return this.currentLocale;
    }

    /**
     * Check if current language is RTL
     */
    isRTLLanguage(locale = null) {
        if (locale) {
            // You could make an API call or maintain a local list
            const rtlLanguages = ['ar', 'he', 'fa', 'ur'];
            return rtlLanguages.includes(locale);
        }
        return this.isRTL;
    }

    /**
     * Format number according to locale
     */
    formatNumber(number, options = {}) {
        try {
            const locale = this.getFormattingLocale();
            return new Intl.NumberFormat(locale, options).format(number);
        } catch (error) {
            return number.toString();
        }
    }

    /**
     * Format date according to locale
     */
    formatDate(date, options = {}) {
        try {
            const locale = this.getFormattingLocale();
            return new Intl.DateTimeFormat(locale, options).format(new Date(date));
        } catch (error) {
            return date.toString();
        }
    }

    /**
     * Format currency according to locale
     */
    formatCurrency(amount, currency = 'USD', options = {}) {
        try {
            const locale = this.getFormattingLocale();
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency,
                ...options
            }).format(amount);
        } catch (error) {
            return `${currency} ${amount}`;
        }
    }

    /**
     * Get formatting locale (maps 2-letter codes to full locales)
     */
    getFormattingLocale() {
        const localeMap = {
            'en': 'en-US',
            'ar': 'ar-SA',
            'de': 'de-DE',
            'es': 'es-ES',
            'fr': 'fr-FR',
            'pt': 'pt-PT',
            'ru': 'ru-RU',
            'tr': 'tr-TR',
            'zh': 'zh-CN'
        };
        
        return localeMap[this.currentLocale] || 'en-US';
    }

    /**
     * Update HTML attributes for RTL/LTR
     */
    updateHTMLAttributes() {
        const html = document.documentElement;
        const direction = this.isRTL ? 'rtl' : 'ltr';
        
        html.setAttribute('dir', direction);
        html.setAttribute('lang', this.currentLocale);
        
        // Add/remove RTL class
        html.classList.toggle('rtl', this.isRTL);
        html.classList.toggle('ltr', !this.isRTL);
    }

    /**
     * Trigger locale change event
     */
    triggerLocaleChangeEvent(locale) {
        const event = new CustomEvent('localeChanged', {
            detail: {
                locale,
                isRTL: this.isRTL,
                translations: this.translations[locale]
            }
        });
        
        window.dispatchEvent(event);
    }

    /**
     * Get CSRF token from meta tag
     */
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Preload translations for multiple locales
     */
    async preloadTranslations(locales) {
        const promises = locales.map(locale => this.loadTranslations(locale));
        await Promise.allSettled(promises);
    }

    /**
     * Clear translation cache
     */
    clearCache() {
        this.cache.clear();
        this.translations = {};
    }
}

// Create global instance
window.TranslationManager = new TranslationManager();

// Global helper functions for backward compatibility
window.trans = (key, params = {}, locale = null) => {
    return window.TranslationManager.trans(key, params, locale);
};

window.transChoice = (key, count, params = {}, locale = null) => {
    return window.TranslationManager.transChoice(key, count, params, locale);
};

window.switchLocale = (locale) => {
    return window.TranslationManager.switchLocale(locale);
};

// Vue.js integration (if Vue is available)
if (typeof Vue !== 'undefined') {
    Vue.prototype.$trans = window.trans;
    Vue.prototype.$transChoice = window.transChoice;
    Vue.prototype.$switchLocale = window.switchLocale;
    Vue.prototype.$isRTL = () => window.TranslationManager.isRTLLanguage();
    Vue.prototype.$formatNumber = (number, options) => window.TranslationManager.formatNumber(number, options);
    Vue.prototype.$formatDate = (date, options) => window.TranslationManager.formatDate(date, options);
    Vue.prototype.$formatCurrency = (amount, currency, options) => window.TranslationManager.formatCurrency(amount, currency, options);
}

// Export for ES6 modules
export default TranslationManager;