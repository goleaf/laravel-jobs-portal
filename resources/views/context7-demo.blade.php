<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="{{ in_array(app()->getLocale(), ['ar']) ? 'rtl' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Universal Multilingual Demo - Job Portal</title>
    
    {{-- Universal Assets --}}
    @vite(['resources/css/universal/components.scss', 'resources/css/universal/rtl-support.scss', 'resources/js/universal/ui-system.js', 'resources/js/universal/i18n-system.js'])
    
    {{-- TailwindCSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    {{-- Navigation --}}
    <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo --}}
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        Job Portal
                    </h1>
                </div>

                {{-- Right Navigation --}}
                <div class="flex items-center space-x-4">
                    {{-- Theme Toggle --}}
                    <x-ui.theme-toggle />
                    
                    {{-- Language Switcher --}}
                    <x-ui.language-switcher type="dropdown" />
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- Hero Section --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                <span data-translate="hero.title">Universal Multilingual System</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                <span data-translate="hero.subtitle">Supporting 9 languages with real-time switching</span>
            </p>
            
            {{-- Language Stats --}}
            <div class="grid grid-cols-3 md:grid-cols-5 gap-4 max-w-2xl mx-auto">
                <div class="text-center">
                    <div class="text-2xl mb-2">🇺🇸</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">English</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-2">🇸🇦</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">العربية</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-2">🇪🇸</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Español</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-2">🇫🇷</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Français</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl mb-2">🇩🇪</div>
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Deutsch</div>
                </div>
            </div>
        </div>

        {{-- Demo Sections --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Features Demo --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    <span data-translate="features.title">Platform Features</span>
                </h2>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="text-blue-600 dark:text-blue-400 mt-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">
                                <span data-translate="features.jobs.title">Job Listings</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                <span data-translate="features.jobs.description">Browse thousands of job opportunities</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="text-green-600 dark:text-green-400 mt-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">
                                <span data-translate="features.companies.title">Company Profiles</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                <span data-translate="features.companies.description">Connect with top employers</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="text-purple-600 dark:text-purple-400 mt-1">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-white">
                                <span data-translate="features.applications.title">Application Tracking</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                <span data-translate="features.applications.description">Track your job applications</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Language Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                    <span data-translate="language_info.title">Language Information</span>
                </h2>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md p-4">
                        <h3 class="font-medium text-blue-900 dark:text-blue-100 mb-2">
                            <span data-translate="language_info.current.title">Current Language</span>
                        </h3>
                        <p class="text-blue-800 dark:text-blue-200 text-sm">
                            <strong data-current-language-display>English</strong> - 
                            <span data-translate="language_info.current.description">Switch languages using the dropdown above</span>
                        </p>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
                        <h3 class="font-medium text-green-900 dark:text-green-100 mb-2">
                            <span data-translate="language_info.rtl.title">RTL Support</span>
                        </h3>
                        <p class="text-green-800 dark:text-green-200 text-sm">
                            <span data-translate="language_info.rtl.description">Arabic language includes right-to-left text direction support</span>
                        </p>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-md p-4">
                        <h3 class="font-medium text-purple-900 dark:text-purple-100 mb-2">
                            <span data-translate="language_info.storage.title">Preference Storage</span>
                        </h3>
                        <p class="text-purple-800 dark:text-purple-200 text-sm">
                            <span data-translate="language_info.storage.description">Your language preference is saved in browser storage</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="text-center mt-12">
            <div class="space-x-4">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium transition-colors">
                    <span data-translate="actions.get_started">Get Started</span>
                </button>
                <button class="border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-md font-medium transition-colors">
                    <span data-translate="actions.learn_more">Learn More</span>
                </button>
            </div>
        </div>
    </main>

    {{-- Demo Translations --}}
    <script>
        // Demo translations for immediate testing
        window.demoTranslations = {
            en: {
                hero: {
                    title: "Universal Multilingual System",
                    subtitle: "Supporting 9 languages with real-time switching"
                },
                features: {
                    title: "Platform Features",
                    jobs: {
                        title: "Job Listings",
                        description: "Browse thousands of job opportunities"
                    },
                    companies: {
                        title: "Company Profiles", 
                        description: "Connect with top employers"
                    },
                    applications: {
                        title: "Application Tracking",
                        description: "Track your job applications"
                    }
                },
                language_info: {
                    title: "Language Information",
                    current: {
                        title: "Current Language",
                        description: "Switch languages using the dropdown above"
                    },
                    rtl: {
                        title: "RTL Support",
                        description: "Arabic language includes right-to-left text direction support"
                    },
                    storage: {
                        title: "Preference Storage",
                        description: "Your language preference is saved in browser storage"
                    }
                },
                actions: {
                    get_started: "Get Started",
                    learn_more: "Learn More"
                }
            },
            ar: {
                hero: {
                    title: "نظام Universal متعدد اللغات",
                    subtitle: "يدعم 9 لغات مع التبديل في الوقت الفعلي"
                },
                features: {
                    title: "ميزات المنصة",
                    jobs: {
                        title: "قوائم الوظائف",
                        description: "تصفح آلاف الفرص الوظيفية"
                    },
                    companies: {
                        title: "ملفات الشركات",
                        description: "تواصل مع أفضل أصحاب العمل"
                    },
                    applications: {
                        title: "تتبع الطلبات",
                        description: "تتبع طلبات العمل الخاصة بك"
                    }
                },
                language_info: {
                    title: "معلومات اللغة",
                    current: {
                        title: "اللغة الحالية",
                        description: "قم بتبديل اللغات باستخدام القائمة المنسدلة أعلاه"
                    },
                    rtl: {
                        title: "دعم RTL",
                        description: "تتضمن اللغة العربية دعم اتجاه النص من اليمين إلى اليسار"
                    },
                    storage: {
                        title: "تخزين التفضيلات",
                        description: "يتم حفظ تفضيل اللغة الخاص بك في تخزين المتصفح"
                    }
                },
                actions: {
                    get_started: "ابدأ الآن",
                    learn_more: "اعرف المزيد"
                }
            },
            es: {
                hero: {
                    title: "Sistema Multilingüe Universal",
                    subtitle: "Soporta 9 idiomas con cambio en tiempo real"
                },
                features: {
                    title: "Características de la Plataforma",
                    jobs: {
                        title: "Listados de Empleos",
                        description: "Explora miles de oportunidades laborales"
                    },
                    companies: {
                        title: "Perfiles de Empresas",
                        description: "Conecta con los mejores empleadores"
                    },
                    applications: {
                        title: "Seguimiento de Aplicaciones",
                        description: "Rastrea tus solicitudes de empleo"
                    }
                },
                language_info: {
                    title: "Información del Idioma",
                    current: {
                        title: "Idioma Actual",
                        description: "Cambia idiomas usando el menú desplegable arriba"
                    },
                    rtl: {
                        title: "Soporte RTL",
                        description: "El idioma árabe incluye soporte de dirección de texto de derecha a izquierda"
                    },
                    storage: {
                        title: "Almacenamiento de Preferencias",
                        description: "Tu preferencia de idioma se guarda en el almacenamiento del navegador"
                    }
                },
                actions: {
                    get_started: "Empezar",
                    learn_more: "Saber Más"
                }
            }
        };

        // Initialize demo when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Load demo translations into Universal I18n if available
            if (window.UniversalI18n) {
                // Add demo translations to the system
                for (const [locale, translations] of Object.entries(window.demoTranslations)) {
                    window.UniversalI18n.addTranslations(locale, translations);
                }
            }
            
            // Update current language display
            function updateCurrentLanguageDisplay() {
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
                
                const currentLocale = window.UniversalI18n ? window.UniversalI18n.getCurrentLocale() : 'en';
                const displayElement = document.querySelector('[data-current-language-display]');
                
                if (displayElement && languageNames[currentLocale]) {
                    displayElement.textContent = languageNames[currentLocale];
                }
            }
            
            // Update on language change
            window.addEventListener('language-changed', updateCurrentLanguageDisplay);
            
            // Initial update
            updateCurrentLanguageDisplay();
        });
    </script>
</body>
</html> 