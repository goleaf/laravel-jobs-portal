{{-- Universal Translation Demo Component --}}
@props([
    'showSwitcher' => true,
    'showDemo' => true
])

<div class="universal-i18n-demo bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            <span data-translate="demo.title">Multilingual System Demo</span>
        </h3>
        
        @if($showSwitcher)
            {{-- Language Switcher --}}
            <x-ui.language-switcher type="dropdown" size="sm" />
        @endif
    </div>

    @if($showDemo)
        {{-- Demo Content --}}
        <div class="space-y-4">
            {{-- Welcome Message --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">
                    <span data-translate="demo.welcome.title">Welcome to Job Portal</span>
                </h4>
                <p class="text-blue-800 dark:text-blue-200 text-sm">
                    <span data-translate="demo.welcome.message">Find your dream job or hire the best talent</span>
                </p>
            </div>

            {{-- Feature List --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">
                        <span data-translate="demo.features.jobs">Job Listings</span>
                    </h5>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        <span data-translate="demo.features.jobs_desc">Browse thousands of job opportunities</span>
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">
                        <span data-translate="demo.features.companies">Companies</span>
                    </h5>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        <span data-translate="demo.features.companies_desc">Connect with top employers</span>
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">
                        <span data-translate="demo.features.applications">Applications</span>
                    </h5>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        <span data-translate="demo.features.applications_desc">Track your job applications</span>
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-md p-4">
                    <h5 class="font-medium text-gray-900 dark:text-white mb-2">
                        <span data-translate="demo.features.profiles">Profiles</span>
                    </h5>
                    <p class="text-gray-600 dark:text-gray-300 text-sm">
                        <span data-translate="demo.features.profiles_desc">Build your professional profile</span>
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button class="rounded-md px-4 py-2 text-sm font-semibold focus:outline-none-primary">
                    <span data-translate="demo.actions.search_jobs">Search Jobs</span>
                </button>
                <button class="rounded-md px-4 py-2 text-sm font-semibold focus:outline-none-secondary">
                    <span data-translate="demo.actions.post_job">Post a Job</span>
                </button>
                <button class="rounded-md px-4 py-2 text-sm font-semibold focus:outline-none-outline">
                    <span data-translate="demo.actions.browse_companies">Browse Companies</span>
                </button>
            </div>

            {{-- Language Info --}}
            <div class="mt-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-md border border-indigo-200 dark:border-indigo-800">
                <div class="flex items-center space-x-3">
                    <div class="text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                            <span data-translate="demo.info.current_language">Current Language</span>: 
                            <span data-current-language-name>English</span>
                        </p>
                        <p class="text-xs text-indigo-700 dark:text-indigo-300">
                            <span data-translate="demo.info.change_language">Use the language switcher above to change languages</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Demo Translations Script --}}
<script>
// Demo translations for testing
window.demoTranslations = {
    en: {
        demo: {
            title: "Multilingual System Demo",
            welcome: {
                title: "Welcome to Job Portal",
                message: "Find your dream job or hire the best talent"
            },
            features: {
                jobs: "Job Listings",
                jobs_desc: "Browse thousands of job opportunities",
                companies: "Companies", 
                companies_desc: "Connect with top employers",
                applications: "Applications",
                applications_desc: "Track your job applications",
                profiles: "Profiles",
                profiles_desc: "Build your professional profile"
            },
            actions: {
                search_jobs: "Search Jobs",
                post_job: "Post a Job",
                browse_companies: "Browse Companies"
            },
            info: {
                current_language: "Current Language",
                change_language: "Use the language switcher above to change languages"
            }
        }
    },
    ar: {
        demo: {
            title: "عرض توضيحي للنظام متعدد اللغات",
            welcome: {
                title: "مرحباً بك في بوابة الوظائف",
                message: "ابحث عن وظيفة أحلامك أو وظف أفضل المواهب"
            },
            features: {
                jobs: "قوائم الوظائف",
                jobs_desc: "تصفح آلاف الفرص الوظيفية",
                companies: "الشركات",
                companies_desc: "تواصل مع أفضل أصحاب العمل",
                applications: "الطلبات",
                applications_desc: "تتبع طلبات العمل الخاصة بك",
                profiles: "الملفات الشخصية",
                profiles_desc: "أنشئ ملفك المهني"
            },
            actions: {
                search_jobs: "البحث عن وظائف",
                post_job: "انشر وظيفة",
                browse_companies: "تصفح الشركات"
            },
            info: {
                current_language: "اللغة الحالية",
                change_language: "استخدم مبدل اللغة أعلاه لتغيير اللغات"
            }
        }
    },
    es: {
        demo: {
            title: "Demostración del Sistema Multilingüe",
            welcome: {
                title: "Bienvenido al Portal de Empleos",
                message: "Encuentra el trabajo de tus sueños o contrata el mejor talento"
            },
            features: {
                jobs: "Listados de Empleos",
                jobs_desc: "Explora miles de oportunidades laborales",
                companies: "Empresas",
                companies_desc: "Conecta con los mejores empleadores",
                applications: "Aplicaciones",
                applications_desc: "Rastrea tus solicitudes de empleo",
                profiles: "Perfiles",
                profiles_desc: "Construye tu perfil profesional"
            },
            actions: {
                search_jobs: "Buscar Empleos",
                post_job: "Publicar Empleo",
                browse_companies: "Explorar Empresas"
            },
            info: {
                current_language: "Idioma Actual",
                change_language: "Usa el selector de idioma arriba para cambiar idiomas"
            }
        }
    },
    fr: {
        demo: {
            title: "Démonstration du Système Multilingue",
            welcome: {
                title: "Bienvenue sur le Portail d'Emploi",
                message: "Trouvez l'emploi de vos rêves ou embauchez les meilleurs talents"
            },
            features: {
                jobs: "Offres d'Emploi",
                jobs_desc: "Parcourez des milliers d'opportunités d'emploi",
                companies: "Entreprises",
                companies_desc: "Connectez-vous avec les meilleurs employeurs",
                applications: "Candidatures",
                applications_desc: "Suivez vos candidatures d'emploi",
                profiles: "Profils",
                profiles_desc: "Construisez votre profil professionnel"
            },
            actions: {
                search_jobs: "Rechercher des Emplois",
                post_job: "Publier un Emploi",
                browse_companies: "Parcourir les Entreprises"
            },
            info: {
                current_language: "Langue Actuelle",
                change_language: "Utilisez le sélecteur de langue ci-dessus pour changer de langue"
            }
        }
    },
    de: {
        demo: {
            title: "Mehrsprachiges System Demo",
            welcome: {
                title: "Willkommen im Job Portal",
                message: "Finden Sie Ihren Traumjob oder stellen Sie die besten Talente ein"
            },
            features: {
                jobs: "Stellenausschreibungen",
                jobs_desc: "Durchsuchen Sie Tausende von Jobmöglichkeiten",
                companies: "Unternehmen",
                companies_desc: "Verbinden Sie sich mit Top-Arbeitgebern",
                applications: "Bewerbungen",
                applications_desc: "Verfolgen Sie Ihre Jobbewerbungen",
                profiles: "Profile",
                profiles_desc: "Erstellen Sie Ihr professionelles Profil"
            },
            actions: {
                search_jobs: "Jobs Suchen",
                post_job: "Job Veröffentlichen",
                browse_companies: "Unternehmen Durchsuchen"
            },
            info: {
                current_language: "Aktuelle Sprache",
                change_language: "Verwenden Sie den Sprachumschalter oben, um Sprachen zu ändern"
            }
        }
    }
};

// Initialize demo when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Load demo translations into Universal I18n if available
    if (window.UniversalI18n) {
        // Add demo translations to the system
        for (const [locale, translations] of Object.entries(window.demoTranslations)) {
            if (window.UniversalI18n.translations) {
                window.UniversalI18n.translations.set(locale, {
                    ...window.UniversalI18n.translations.get(locale),
                    ...translations
                });
            }
        }
        
        // Update current language name display
        function updateCurrentLanguageName() {
            const currentLocale = window.UniversalI18n.getCurrentLocale();
            const languageNames = {
                'en': 'English',
                'ar': 'العربية', 
                'es': 'Español',
                'fr': 'Français',
                'de': 'Deutsch',
                'pt': 'Português',
                'ru': 'Русский',
                'tr': 'Türkçe',
                'zh': '中文'
            };
            
            const nameElement = document.querySelector('[data-current-language-name]');
            if (nameElement && languageNames[currentLocale]) {
                nameElement.textContent = languageNames[currentLocale];
            }
        }
        
        // Update on language change
        window.addEventListener('language-changed', updateCurrentLanguageName);
        
        // Initial update
        updateCurrentLanguageName();
    }
});
</script>

{{-- Demo Styles --}}
<style>
.universal-i18n-demo .btn-primary {
    @apply bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200;
}

.universal-i18n-demo .btn-secondary {
    @apply bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200;
}

.universal-i18n-demo .btn-outline {
    @apply border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200;
}

/* RTL adjustments */
.rtl .universal-i18n-demo {
    direction: rtl;
}

.rtl .universal-i18n-demo .flex {
    flex-direction: row-reverse;
}

.rtl .universal-i18n-demo .space-x-3 > * + * {
    margin-right: 0.75rem;
    margin-left: 0;
}

.rtl .universal-i18n-demo .grid {
    direction: rtl;
}
</style> 