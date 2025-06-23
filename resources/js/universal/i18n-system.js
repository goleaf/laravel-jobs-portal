/**
 * Universal Internationalization System - Enhanced
 * Dynamic Language Switching and Translation Management with Laravel Integration
 */

class UniversalI18nSystem {
    constructor() {
        this.currentLocale = this.getStoredLocale() || this.getDefaultLocale();
        this.translations = new Map();
        this.loadedLocales = new Set();
        this.rtlLocales = ['ar']; // Arabic requires RTL support
        this.fallbackLocale = 'en';
        this.apiEndpoint = '/locale';
        this.init();
    }

    init() {
        this.setupLanguageDetection();
        this.setupLanguageSwitchers();
        this.loadCurrentLocale();
        this.applyRTLDirection();
        this.setupLaravelIntegration();
        console.log(`🌍 Universal I18n System initialized with locale: ${this.currentLocale}`);
    }

    // Laravel Integration
    setupLaravelIntegration() {
        // Get CSRF token for Laravel requests
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Listen for Laravel locale changes
        window.addEventListener('laravel-locale-changed', (event) => {
            this.setLocale(event.detail.locale, false); // Don't send to server again
        });
    }

    // Enhanced Locale Management
    getDefaultLocale() {
        // Check browser language preference
        const browserLang = navigator.language || navigator.userLanguage;
        const shortLang = browserLang.split('-')[0];
        
        // Available locales
        const availableLocales = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
        
        return availableLocales.includes(shortLang) ? shortLang : 'en';
    }

    getStoredLocale() {
        return localStorage.getItem('universal_locale');
    }

    async setLocale(locale, updateServer = true) {
        if (this.currentLocale === locale) return Promise.resolve();
        
        try {
            // Update server if requested
            if (updateServer) {
                await this.updateServerLocale(locale);
            }
            
            this.currentLocale = locale;
            localStorage.setItem('universal_locale', locale);
            
            await this.loadLocale(locale);
            this.applyRTLDirection();
            this.updateLanguageSwitchers();
            this.updatePageContent();
            this.dispatchLanguageChange();
            
            // Show success notification
            this.showNotification(
                await this.translate('locale.switched_successfully', { language: await this.getLocaleDisplayName(locale) }),
                'success'
            );
            
        } catch (error) {
            console.error('Failed to switch locale:', error);
            this.showNotification(
                await this.translate('locale.switch_failed'),
                'error'
            );
            throw error;
        }
    }

    async updateServerLocale(locale) {
        if (!this.csrfToken) {
            console.warn('CSRF token not found, skipping server update');
            return;
        }

        const response = await fetch(`${this.apiEndpoint}/switch`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ locale })
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Failed to update server locale');
        }

        return response.json();
    }

    // Enhanced Translation Loading
    async loadLocale(locale) {
        if (this.loadedLocales.has(locale)) {
            return Promise.resolve();
        }

        try {
            // Load multiple translation files for the locale
            const files = [
                'auth',
                'dashboard', 
                'forms',
                'navigation',
                'validation',
                'common',
                'locale',
                'messages',
                'jobs',
                'admin'
            ];

            const translations = {};
            const loadPromises = files.map(async (file) => {
                try {
                    const response = await fetch(`/lang/${locale}_json/${file}.json`);
                    if (response.ok) {
                        const data = await response.json();
                        translations[file] = data;
                    }
                } catch (error) {
                    console.warn(`Failed to load ${file}.json for locale ${locale}:`, error);
                }
            });

            await Promise.all(loadPromises);

            this.translations.set(locale, translations);
            this.loadedLocales.add(locale);
            
            console.log(`✅ Loaded translations for locale: ${locale}`);
        } catch (error) {
            console.error(`Failed to load locale ${locale}:`, error);
            throw error;
        }
    }

    async loadCurrentLocale() {
        return this.loadLocale(this.currentLocale);
    }

    // Enhanced Translation Functions
    async translate(key, params = {}, locale = null) {
        const targetLocale = locale || this.currentLocale;
        
        // Ensure locale is loaded
        if (!this.loadedLocales.has(targetLocale)) {
            await this.loadLocale(targetLocale);
        }
        
        const localeTranslations = this.translations.get(targetLocale);
        
        if (!localeTranslations) {
            console.warn(`Translations not loaded for locale: ${targetLocale}`);
            return this.getFallbackTranslation(key, params);
        }

        // Support nested keys like 'dashboard.welcome' or 'forms.validation.required'
        const keys = key.split('.');
        let value = localeTranslations;
        
        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                console.warn(`Translation key not found: ${key} in locale ${targetLocale}`);
                return this.getFallbackTranslation(key, params);
            }
        }

        // Handle parameterized translations
        if (typeof value === 'string' && Object.keys(params).length > 0) {
            return this.interpolate(value, params);
        }

        return value;
    }

    async getFallbackTranslation(key, params = {}) {
        if (this.currentLocale !== this.fallbackLocale) {
            try {
                return await this.translate(key, params, this.fallbackLocale);
            } catch (error) {
                console.warn(`Fallback translation failed for key: ${key}`);
            }
        }
        return key; // Return key as last resort
    }

    interpolate(template, params) {
        return template.replace(/\{(\w+)\}/g, (match, key) => {
            return params.hasOwnProperty(key) ? params[key] : match;
        });
    }

    // Enhanced Pluralization
    async plural(key, count, params = {}, locale = null) {
        const targetLocale = locale || this.currentLocale;
        const pluralRules = this.getPluralRules(targetLocale);
        const pluralForm = pluralRules.select(count);
        
        const pluralKey = `${key}.${pluralForm}`;
        const fallbackKey = `${key}.other`;
        
        let translation = await this.translate(pluralKey, { ...params, count }, locale);
        
        if (translation === pluralKey) {
            translation = await this.translate(fallbackKey, { ...params, count }, locale);
        }
        
        if (translation === fallbackKey) {
            translation = await this.translate(key, { ...params, count }, locale);
        }
        
        return translation;
    }

    getPluralRules(locale) {
        try {
            return new Intl.PluralRules(locale);
        } catch (error) {
            console.warn(`Intl.PluralRules not supported for locale ${locale}, falling back to English`);
            return new Intl.PluralRules('en');
        }
    }

    // Enhanced RTL Support
    applyRTLDirection() {
        const isRTL = this.rtlLocales.includes(this.currentLocale);
        
        document.documentElement.setAttribute('dir', isRTL ? 'rtl' : 'ltr');
        document.documentElement.setAttribute('lang', this.currentLocale);
        
        // Update CSS classes for RTL
        if (isRTL) {
            document.body.classList.add('rtl');
            document.body.classList.remove('ltr');
        } else {
            document.body.classList.add('ltr');
            document.body.classList.remove('rtl');
        }

        // Dispatch RTL change event
        window.dispatchEvent(new CustomEvent('rtl-changed', {
            detail: { isRTL, locale: this.currentLocale }
        }));
    }

    // Enhanced DOM Updates
    setupLanguageSwitchers() {
        // Handle dropdown language switchers
        document.querySelectorAll('[data-language-switcher]').forEach(switcher => {
            switcher.addEventListener('change', async (e) => {
                const locale = e.target.value;
                await this.setLocale(locale);
            });
        });

        // Handle button language switchers
        document.querySelectorAll('[data-language-option]').forEach(option => {
            option.addEventListener('click', async (e) => {
                e.preventDefault();
                const locale = option.getAttribute('data-language-option');
                await this.setLocale(locale);
            });
        });

        // Handle form-based language switchers
        document.querySelectorAll('[data-language-form]').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(form);
                const locale = formData.get('locale');
                if (locale) {
                    await this.setLocale(locale);
                }
            });
        });
    }

    updateLanguageSwitchers() {
        // Update all language switcher elements
        document.querySelectorAll('[data-language-switcher]').forEach(switcher => {
            if (switcher.tagName === 'SELECT') {
                switcher.value = this.currentLocale;
            }
        });

        // Update language option buttons
        document.querySelectorAll('[data-language-option]').forEach(option => {
            const locale = option.getAttribute('data-language-option');
            if (locale === this.currentLocale) {
                option.classList.add('active', 'selected');
                option.setAttribute('aria-current', 'true');
            } else {
                option.classList.remove('active', 'selected');
                option.removeAttribute('aria-current');
            }
        });
    }

    async updatePageContent() {
        // Update elements with data-translate attribute
        const elements = document.querySelectorAll('[data-translate]');
        
        for (const element of elements) {
            const key = element.getAttribute('data-translate');
            const params = this.parseTranslateParams(element);
            
            try {
                const translation = await this.translate(key, params);
                
                if (element.hasAttribute('data-translate-html')) {
                    element.innerHTML = translation;
                } else {
                    element.textContent = translation;
                }
            } catch (error) {
                console.warn(`Failed to translate element with key: ${key}`, error);
            }
        }

        // Update placeholder attributes
        const placeholderElements = document.querySelectorAll('[data-translate-placeholder]');
        for (const element of placeholderElements) {
            const key = element.getAttribute('data-translate-placeholder');
            try {
                const translation = await this.translate(key);
                element.setAttribute('placeholder', translation);
            } catch (error) {
                console.warn(`Failed to translate placeholder with key: ${key}`, error);
            }
        }

        // Update title attributes
        const titleElements = document.querySelectorAll('[data-translate-title]');
        for (const element of titleElements) {
            const key = element.getAttribute('data-translate-title');
            try {
                const translation = await this.translate(key);
                element.setAttribute('title', translation);
            } catch (error) {
                console.warn(`Failed to translate title with key: ${key}`, error);
            }
        }
    }

    parseTranslateParams(element) {
        const paramsAttr = element.getAttribute('data-translate-params');
        if (!paramsAttr) return {};
        
        try {
            return JSON.parse(paramsAttr);
        } catch (error) {
            console.warn('Invalid JSON in data-translate-params:', paramsAttr);
            return {};
        }
    }

    setupLanguageDetection() {
        // Auto-detect language changes in browser
        if ('language' in navigator) {
            // Some browsers support language change detection
            window.addEventListener('languagechange', () => {
                const newLocale = this.getDefaultLocale();
                if (newLocale !== this.currentLocale) {
                    this.setLocale(newLocale);
                }
            });
        }
    }

    dispatchLanguageChange() {
        // Dispatch custom event for other components
        window.dispatchEvent(new CustomEvent('language-changed', {
            detail: {
                locale: this.currentLocale,
                isRTL: this.isRTL(),
                displayName: this.getLocaleDisplayName(this.currentLocale)
            }
        }));
    }

    // Utility Methods
    getCurrentLocale() {
        return this.currentLocale;
    }

    isRTL() {
        return this.rtlLocales.includes(this.currentLocale);
    }

    getAvailableLocales() {
        return ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
    }

    async getLocaleDisplayName(locale, displayLocale = null) {
        const displayNames = {
            'en': { native: 'English', name: 'English' },
            'ar': { native: 'العربية', name: 'Arabic' },
            'de': { native: 'Deutsch', name: 'German' },
            'es': { native: 'Español', name: 'Spanish' },
            'fr': { native: 'Français', name: 'French' },
            'pt': { native: 'Português', name: 'Portuguese' },
            'ru': { native: 'Русский', name: 'Russian' },
            'tr': { native: 'Türkçe', name: 'Turkish' },
            'zh': { native: '中文', name: 'Chinese' }
        };

        const localeData = displayNames[locale];
        if (!localeData) return locale;

        return displayLocale ? localeData.name : localeData.native;
    }

    // Enhanced Formatting
    formatDate(date, options = {}) {
        try {
            return new Intl.DateTimeFormat(this.currentLocale, options).format(date);
        } catch (error) {
            return new Intl.DateTimeFormat('en', options).format(date);
        }
    }

    formatNumber(number, options = {}) {
        try {
            return new Intl.NumberFormat(this.currentLocale, options).format(number);
        } catch (error) {
            return new Intl.NumberFormat('en', options).format(number);
        }
    }

    formatCurrency(amount, currency = 'USD', options = {}) {
        try {
            return new Intl.NumberFormat(this.currentLocale, {
                style: 'currency',
                currency,
                ...options
            }).format(amount);
        } catch (error) {
            return new Intl.NumberFormat('en', {
                style: 'currency',
                currency,
                ...options
            }).format(amount);
        }
    }

    // Notification System
    showNotification(message, type = 'info') {
        // Try to use existing notification system
        if (window.showToast) {
            window.showToast(message, type);
            return;
        }

        if (window.Swal) {
            window.Swal.fire({
                text: message,
                icon: type,
                timer: 3000,
                showConfirmButton: false
            });
            return;
        }

        // Fallback to console
        console.log(`[${type.toUpperCase()}] ${message}`);
    }

    // API Methods for external use
    async getServerLocales() {
        try {
            const response = await fetch(`${this.apiEndpoint}/available`);
            if (response.ok) {
                return await response.json();
            }
        } catch (error) {
            console.warn('Failed to fetch server locales:', error);
        }
        return null;
    }

    async getCurrentServerLocale() {
        try {
            const response = await fetch(`${this.apiEndpoint}/current`);
            if (response.ok) {
                return await response.json();
            }
        } catch (error) {
            console.warn('Failed to fetch current server locale:', error);
        }
        return null;
    }
}

// Global instance
window.i18n = new UniversalI18nSystem();

// Global helper functions
window.__ = async function(key, params = {}) {
    return await window.i18n.translate(key, params);
};

window.__n = async function(key, count, params = {}) {
    return await window.i18n.plural(key, count, params);
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UniversalI18nSystem;
} 