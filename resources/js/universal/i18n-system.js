/**
 * Universal Internationalization System
 * Dynamic Language Switching and Translation Management
 */

class UniversalI18nSystem {
    constructor() {
        this.currentLocale = this.getStoredLocale() || this.getDefaultLocale();
        this.translations = new Map();
        this.loadedLocales = new Set();
        this.rtlLocales = ['ar']; // Arabic requires RTL support
        this.init();
    }

    init() {
        this.setupLanguageDetection();
        this.setupLanguageSwitchers();
        this.loadCurrentLocale();
        this.applyRTLDirection();
        console.log(`🌍 Universal I18n System initialized with locale: ${this.currentLocale}`);
    }

    // Locale Management
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

    setLocale(locale) {
        if (this.currentLocale === locale) return Promise.resolve();
        
        this.currentLocale = locale;
        localStorage.setItem('universal_locale', locale);
        
        return this.loadLocale(locale).then(() => {
            this.applyRTLDirection();
            this.updateLanguageSwitchers();
            this.updatePageContent();
            this.dispatchLanguageChange();
        });
    }

    // Translation Loading
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
                'common'
            ];

            const translations = {};
            
            for (const file of files) {
                try {
                    const response = await fetch(`/lang/${locale}_json/${file}.json`);
                    if (response.ok) {
                        const data = await response.json();
                        translations[file] = data;
                    }
                } catch (error) {
                    console.warn(`Failed to load ${file}.json for locale ${locale}:`, error);
                }
            }

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

    // Translation Functions
    translate(key, params = {}, locale = null) {
        const targetLocale = locale || this.currentLocale;
        const localeTranslations = this.translations.get(targetLocale);
        
        if (!localeTranslations) {
            console.warn(`Translations not loaded for locale: ${targetLocale}`);
            return key;
        }

        // Support nested keys like 'dashboard.welcome' or 'forms.validation.required'
        const keys = key.split('.');
        let value = localeTranslations;
        
        for (const k of keys) {
            if (value && typeof value === 'object' && k in value) {
                value = value[k];
            } else {
                console.warn(`Translation key not found: ${key} in locale ${targetLocale}`);
                return key;
            }
        }

        // Handle parameterized translations
        if (typeof value === 'string' && Object.keys(params).length > 0) {
            return this.interpolate(value, params);
        }

        return value;
    }

    interpolate(template, params) {
        return template.replace(/\{(\w+)\}/g, (match, key) => {
            return params.hasOwnProperty(key) ? params[key] : match;
        });
    }

    // Pluralization
    plural(key, count, params = {}, locale = null) {
        const targetLocale = locale || this.currentLocale;
        const pluralRules = this.getPluralRules(targetLocale);
        const pluralForm = pluralRules.select(count);
        
        const pluralKey = `${key}.${pluralForm}`;
        const fallbackKey = `${key}.other`;
        
        let translation = this.translate(pluralKey, { ...params, count }, locale);
        
        if (translation === pluralKey) {
            translation = this.translate(fallbackKey, { ...params, count }, locale);
        }
        
        if (translation === fallbackKey) {
            translation = this.translate(key, { ...params, count }, locale);
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

    // RTL Support
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
    }

    // DOM Updates
    setupLanguageSwitchers() {
        document.querySelectorAll('[data-language-switcher]').forEach(switcher => {
            switcher.addEventListener('change', (e) => {
                const locale = e.target.value;
                this.setLocale(locale);
            });
        });

        document.querySelectorAll('[data-language-option]').forEach(option => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                const locale = option.getAttribute('data-language-option');
                this.setLocale(locale);
            });
        });
    }

    updateLanguageSwitchers() {
        // Update select dropdowns
        document.querySelectorAll('[data-language-switcher]').forEach(switcher => {
            switcher.value = this.currentLocale;
        });

        // Update language option buttons
        document.querySelectorAll('[data-language-option]').forEach(option => {
            const locale = option.getAttribute('data-language-option');
            if (locale === this.currentLocale) {
                option.classList.add('active');
            } else {
                option.classList.remove('active');
            }
        });
    }

    updatePageContent() {
        // Update elements with data-translate attributes
        document.querySelectorAll('[data-translate]').forEach(element => {
            const key = element.getAttribute('data-translate');
            const params = this.parseTranslateParams(element);
            const translation = this.translate(key, params);
            
            if (element.hasAttribute('data-translate-html')) {
                element.innerHTML = translation;
            } else {
                element.textContent = translation;
            }
        });

        // Update placeholders
        document.querySelectorAll('[data-translate-placeholder]').forEach(element => {
            const key = element.getAttribute('data-translate-placeholder');
            const translation = this.translate(key);
            element.setAttribute('placeholder', translation);
        });

        // Update titles
        document.querySelectorAll('[data-translate-title]').forEach(element => {
            const key = element.getAttribute('data-translate-title');
            const translation = this.translate(key);
            element.setAttribute('title', translation);
        });

        // Update aria-labels
        document.querySelectorAll('[data-translate-aria]').forEach(element => {
            const key = element.getAttribute('data-translate-aria');
            const translation = this.translate(key);
            element.setAttribute('aria-label', translation);
        });
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

    // Language Detection
    setupLanguageDetection() {
        // Listen for browser language changes
        window.addEventListener('languagechange', () => {
            const newLocale = this.getDefaultLocale();
            if (newLocale !== this.currentLocale && !this.getStoredLocale()) {
                this.setLocale(newLocale);
            }
        });
    }

    // Events
    dispatchLanguageChange() {
        window.dispatchEvent(new CustomEvent('language-changed', {
            detail: { 
                locale: this.currentLocale,
                isRTL: this.rtlLocales.includes(this.currentLocale)
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

    getLocaleDisplayName(locale, displayLocale = null) {
        const displayLang = displayLocale || this.currentLocale;
        
        const names = {
            'en': { en: 'English', ar: 'الإنجليزية', de: 'Englisch', es: 'Inglés', fr: 'Anglais', pt: 'Inglês', ru: 'Английский', tr: 'İngilizce', zh: '英语' },
            'ar': { en: 'Arabic', ar: 'العربية', de: 'Arabisch', es: 'Árabe', fr: 'Arabe', pt: 'Árabe', ru: 'Арабский', tr: 'Arapça', zh: '阿拉伯语' },
            'de': { en: 'German', ar: 'الألمانية', de: 'Deutsch', es: 'Alemán', fr: 'Allemand', pt: 'Alemão', ru: 'Немецкий', tr: 'Almanca', zh: '德语' },
            'es': { en: 'Spanish', ar: 'الإسبانية', de: 'Spanisch', es: 'Español', fr: 'Espagnol', pt: 'Espanhol', ru: 'Испанский', tr: 'İspanyolca', zh: '西班牙语' },
            'fr': { en: 'French', ar: 'الفرنسية', de: 'Französisch', es: 'Francés', fr: 'Français', pt: 'Francês', ru: 'Французский', tr: 'Fransızca', zh: '法语' },
            'pt': { en: 'Portuguese', ar: 'البرتغالية', de: 'Portugiesisch', es: 'Portugués', fr: 'Portugais', pt: 'Português', ru: 'Португальский', tr: 'Portekizce', zh: '葡萄牙语' },
            'ru': { en: 'Russian', ar: 'الروسية', de: 'Russisch', es: 'Ruso', fr: 'Russe', pt: 'Russo', ru: 'Русский', tr: 'Rusça', zh: '俄语' },
            'tr': { en: 'Turkish', ar: 'التركية', de: 'Türkisch', es: 'Turco', fr: 'Turc', pt: 'Turco', ru: 'Турецкий', tr: 'Türkçe', zh: '土耳其语' },
            'zh': { en: 'Chinese', ar: 'الصينية', de: 'Chinesisch', es: 'Chino', fr: 'Chinois', pt: 'Chinês', ru: 'Китайский', tr: 'Çince', zh: '中文' }
        };
        
        return names[locale] && names[locale][displayLang] ? names[locale][displayLang] : locale.toUpperCase();
    }

    // Date and Number Formatting
    formatDate(date, options = {}) {
        return new Intl.DateTimeFormat(this.currentLocale, options).format(date);
    }

    formatNumber(number, options = {}) {
        return new Intl.NumberFormat(this.currentLocale, options).format(number);
    }

    formatCurrency(amount, currency = 'USD', options = {}) {
        return new Intl.NumberFormat(this.currentLocale, {
            style: 'currency',
            currency: currency,
            ...options
        }).format(amount);
    }
}

// Global translation helper functions
window.__ = function(key, params = {}) {
    if (window.UniversalI18n) {
        return window.UniversalI18n.translate(key, params);
    }
    return key;
};

window.__n = function(key, count, params = {}) {
    if (window.UniversalI18n) {
        return window.UniversalI18n.plural(key, count, params);
    }
    return key;
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.UniversalI18n = new UniversalI18nSystem();
});

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UniversalI18nSystem;
} 