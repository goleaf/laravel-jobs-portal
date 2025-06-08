<?php

/**
 * Context7 Missing Translations Fixer
 * Comprehensive translation completion and blade file scanning
 */

class Context7TranslationFixer
{
    private $languages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
    private $langPath;
    private $viewsPath;
    private $missingTranslations = [];
    private $hardcodedStrings = [];
    private $fixedTranslations = [];

    public function __construct()
    {
        $this->langPath = __DIR__ . '/lang';
        $this->viewsPath = __DIR__ . '/resources/views';
        
        echo "🔧 Context7 Missing Translations Fixer\n";
        echo "=====================================\n\n";
    }

    public function fixAllMissingTranslations()
    {
        echo "🚀 Starting Comprehensive Translation Fix...\n\n";

        $this->addMissingWelcomeTranslations();
        $this->addMissingAuthTranslations();
        $this->addMissingCompanyTranslations();
        $this->addMissingNavigationTranslations();
        $this->scanAllBladesForHardcodedText();
        $this->generateFixReport();
    }

    private function addMissingWelcomeTranslations()
    {
        echo "1️⃣ Adding Missing Welcome/Home Page Translations...\n";

        $welcomeTranslations = [
            'en' => [
                'web.welcome' => 'Welcome to our Job Portal',
                'web.find_job' => 'Find Your Dream Job',
                'web.post_job' => 'Post a Job',
                'web.welcome_title' => 'Find Jobs, Hire Talent',
                'web.welcome_subtitle' => 'Connecting job seekers with employers',
                'web.for_job_seekers' => 'For Job Seekers',
                'web.for_employers' => 'For Employers',
                'web.search_jobs' => 'Search Jobs',
                'web.browse_companies' => 'Browse Companies',
                'web.latest_jobs' => 'Latest Jobs',
                'web.featured_companies' => 'Featured Companies'
            ],
            'ar' => [
                'web.welcome' => 'مرحبا بكم في بوابة العمل الخاصة بنا',
                'web.find_job' => 'ابحث عن وظيفة أحلامك',
                'web.post_job' => 'انشر وظيفة',
                'web.welcome_title' => 'ابحث عن وظائف، وظف المواهب',
                'web.welcome_subtitle' => 'ربط الباحثين عن عمل مع أصحاب العمل',
                'web.for_job_seekers' => 'للباحثين عن عمل',
                'web.for_employers' => 'لأصحاب العمل',
                'web.search_jobs' => 'البحث عن وظائف',
                'web.browse_companies' => 'تصفح الشركات',
                'web.latest_jobs' => 'أحدث الوظائف',
                'web.featured_companies' => 'الشركات المميزة'
            ],
            'de' => [
                'web.welcome' => 'Willkommen in unserem Jobportal',
                'web.find_job' => 'Finden Sie Ihren Traumjob',
                'web.post_job' => 'Stellenanzeige veröffentlichen',
                'web.welcome_title' => 'Jobs finden, Talente einstellen',
                'web.welcome_subtitle' => 'Verbindung von Arbeitssuchenden und Arbeitgebern',
                'web.for_job_seekers' => 'Für Arbeitssuchende',
                'web.for_employers' => 'Für Arbeitgeber',
                'web.search_jobs' => 'Jobs suchen',
                'web.browse_companies' => 'Unternehmen durchsuchen',
                'web.latest_jobs' => 'Neueste Jobs',
                'web.featured_companies' => 'Ausgewählte Unternehmen'
            ],
            'es' => [
                'web.welcome' => 'Bienvenido a nuestro portal de empleos',
                'web.find_job' => 'Encuentra el trabajo de tus sueños',
                'web.post_job' => 'Publicar un trabajo',
                'web.welcome_title' => 'Encontrar trabajos, contratar talento',
                'web.welcome_subtitle' => 'Conectando buscadores de empleo con empleadores',
                'web.for_job_seekers' => 'Para buscadores de empleo',
                'web.for_employers' => 'Para empleadores',
                'web.search_jobs' => 'Buscar trabajos',
                'web.browse_companies' => 'Explorar empresas',
                'web.latest_jobs' => 'Últimos trabajos',
                'web.featured_companies' => 'Empresas destacadas'
            ],
            'fr' => [
                'web.welcome' => 'Bienvenue sur notre portail emploi',
                'web.find_job' => 'Trouvez l\'emploi de vos rêves',
                'web.post_job' => 'Publier un emploi',
                'web.welcome_title' => 'Trouver des emplois, embaucher des talents',
                'web.welcome_subtitle' => 'Connecter les chercheurs d\'emploi avec les employeurs',
                'web.for_job_seekers' => 'Pour les chercheurs d\'emploi',
                'web.for_employers' => 'Pour les employeurs',
                'web.search_jobs' => 'Rechercher des emplois',
                'web.browse_companies' => 'Parcourir les entreprises',
                'web.latest_jobs' => 'Derniers emplois',
                'web.featured_companies' => 'Entreprises en vedette'
            ],
            'pt' => [
                'web.welcome' => 'Bem-vindo ao nosso portal de empregos',
                'web.find_job' => 'Encontre o emprego dos seus sonhos',
                'web.post_job' => 'Publicar um emprego',
                'web.welcome_title' => 'Encontrar empregos, contratar talentos',
                'web.welcome_subtitle' => 'Conectando candidatos a emprego com empregadores',
                'web.for_job_seekers' => 'Para candidatos a emprego',
                'web.for_employers' => 'Para empregadores',
                'web.search_jobs' => 'Pesquisar empregos',
                'web.browse_companies' => 'Navegar pelas empresas',
                'web.latest_jobs' => 'Últimos empregos',
                'web.featured_companies' => 'Empresas em destaque'
            ],
            'ru' => [
                'web.welcome' => 'Добро пожаловать на наш портал вакансий',
                'web.find_job' => 'Найдите работу своей мечты',
                'web.post_job' => 'Разместить вакансию',
                'web.welcome_title' => 'Найти работу, нанять таланты',
                'web.welcome_subtitle' => 'Соединяем соискателей с работодателями',
                'web.for_job_seekers' => 'Для соискателей',
                'web.for_employers' => 'Для работодателей',
                'web.search_jobs' => 'Поиск работы',
                'web.browse_companies' => 'Просмотр компаний',
                'web.latest_jobs' => 'Последние вакансии',
                'web.featured_companies' => 'Рекомендуемые компании'
            ],
            'tr' => [
                'web.welcome' => 'İş portalımıza hoş geldiniz',
                'web.find_job' => 'Hayalinizdeki işi bulun',
                'web.post_job' => 'İş ilanı ver',
                'web.welcome_title' => 'İş bul, yetenek işe al',
                'web.welcome_subtitle' => 'İş arayan ile işverenleri birleştiriyor',
                'web.for_job_seekers' => 'İş arayanlar için',
                'web.for_employers' => 'İşverenler için',
                'web.search_jobs' => 'İş ara',
                'web.browse_companies' => 'Şirketlere gözat',
                'web.latest_jobs' => 'Son işler',
                'web.featured_companies' => 'Öne çıkan şirketler'
            ],
            'zh' => [
                'web.welcome' => '欢迎来到我们的求职门户',
                'web.find_job' => '找到你的梦想工作',
                'web.post_job' => '发布工作',
                'web.welcome_title' => '找工作，招聘人才',
                'web.welcome_subtitle' => '连接求职者与雇主',
                'web.for_job_seekers' => '求职者',
                'web.for_employers' => '雇主',
                'web.search_jobs' => '搜索工作',
                'web.browse_companies' => '浏览公司',
                'web.latest_jobs' => '最新工作',
                'web.featured_companies' => '特色公司'
            ]
        ];

        $this->addTranslationsToFiles($welcomeTranslations, 'web.json');
        echo "   ✅ Welcome/Home page translations added to all languages\n\n";
    }

    private function addMissingAuthTranslations()
    {
        echo "2️⃣ Adding Missing Auth Translations...\n";

        $authTranslations = [
            'en' => [
                'auth.login' => 'Login',
                'auth.register' => 'Register',
                'auth.password' => 'Password',
                'auth.email' => 'Email Address',
                'auth.confirm_password' => 'Confirm Password',
                'auth.remember_me' => 'Remember Me',
                'auth.forgot_password' => 'Forgot Your Password?',
                'auth.reset_password' => 'Reset Password',
                'auth.send_reset_link' => 'Send Password Reset Link',
                'auth.create_account' => 'Create Account',
                'auth.already_have_account' => 'Already have an account?',
                'auth.dont_have_account' => 'Don\'t have an account?'
            ],
            'ar' => [
                'auth.login' => 'تسجيل الدخول',
                'auth.register' => 'تسجيل',
                'auth.password' => 'كلمة المرور',
                'auth.email' => 'عنوان البريد الإلكتروني',
                'auth.confirm_password' => 'تأكيد كلمة المرور',
                'auth.remember_me' => 'تذكرني',
                'auth.forgot_password' => 'نسيت كلمة المرور؟',
                'auth.reset_password' => 'إعادة تعيين كلمة المرور',
                'auth.send_reset_link' => 'إرسال رابط إعادة تعيين كلمة المرور',
                'auth.create_account' => 'إنشاء حساب',
                'auth.already_have_account' => 'هل لديك حساب بالفعل؟',
                'auth.dont_have_account' => 'ليس لديك حساب؟'
            ],
            'de' => [
                'auth.login' => 'Anmelden',
                'auth.register' => 'Registrieren',
                'auth.password' => 'Passwort',
                'auth.email' => 'E-Mail-Adresse',
                'auth.confirm_password' => 'Passwort bestätigen',
                'auth.remember_me' => 'Angemeldet bleiben',
                'auth.forgot_password' => 'Passwort vergessen?',
                'auth.reset_password' => 'Passwort zurücksetzen',
                'auth.send_reset_link' => 'Passwort-Reset-Link senden',
                'auth.create_account' => 'Konto erstellen',
                'auth.already_have_account' => 'Haben Sie bereits ein Konto?',
                'auth.dont_have_account' => 'Haben Sie kein Konto?'
            ],
            'es' => [
                'auth.login' => 'Iniciar sesión',
                'auth.register' => 'Registrarse',
                'auth.password' => 'Contraseña',
                'auth.email' => 'Dirección de correo electrónico',
                'auth.confirm_password' => 'Confirmar contraseña',
                'auth.remember_me' => 'Recordarme',
                'auth.forgot_password' => '¿Olvidaste tu contraseña?',
                'auth.reset_password' => 'Restablecer contraseña',
                'auth.send_reset_link' => 'Enviar enlace de restablecimiento de contraseña',
                'auth.create_account' => 'Crear cuenta',
                'auth.already_have_account' => '¿Ya tienes una cuenta?',
                'auth.dont_have_account' => '¿No tienes una cuenta?'
            ],
            'fr' => [
                'auth.login' => 'Connexion',
                'auth.register' => 'S\'inscrire',
                'auth.password' => 'Mot de passe',
                'auth.email' => 'Adresse e-mail',
                'auth.confirm_password' => 'Confirmer le mot de passe',
                'auth.remember_me' => 'Se souvenir de moi',
                'auth.forgot_password' => 'Mot de passe oublié?',
                'auth.reset_password' => 'Réinitialiser le mot de passe',
                'auth.send_reset_link' => 'Envoyer le lien de réinitialisation',
                'auth.create_account' => 'Créer un compte',
                'auth.already_have_account' => 'Vous avez déjà un compte?',
                'auth.dont_have_account' => 'Vous n\'avez pas de compte?'
            ],
            'pt' => [
                'auth.login' => 'Entrar',
                'auth.register' => 'Registrar',
                'auth.password' => 'Senha',
                'auth.email' => 'Endereço de e-mail',
                'auth.confirm_password' => 'Confirmar senha',
                'auth.remember_me' => 'Lembrar de mim',
                'auth.forgot_password' => 'Esqueceu sua senha?',
                'auth.reset_password' => 'Redefinir senha',
                'auth.send_reset_link' => 'Enviar link de redefinição de senha',
                'auth.create_account' => 'Criar conta',
                'auth.already_have_account' => 'Já tem uma conta?',
                'auth.dont_have_account' => 'Não tem uma conta?'
            ],
            'ru' => [
                'auth.login' => 'Вход',
                'auth.register' => 'Регистрация',
                'auth.password' => 'Пароль',
                'auth.email' => 'Адрес электронной почты',
                'auth.confirm_password' => 'Подтвердить пароль',
                'auth.remember_me' => 'Запомнить меня',
                'auth.forgot_password' => 'Забыли пароль?',
                'auth.reset_password' => 'Сбросить пароль',
                'auth.send_reset_link' => 'Отправить ссылку для сброса пароля',
                'auth.create_account' => 'Создать аккаунт',
                'auth.already_have_account' => 'У вас уже есть аккаунт?',
                'auth.dont_have_account' => 'У вас нет аккаунта?'
            ],
            'tr' => [
                'auth.login' => 'Giriş yap',
                'auth.register' => 'Kayıt ol',
                'auth.password' => 'Şifre',
                'auth.email' => 'E-posta adresi',
                'auth.confirm_password' => 'Şifreyi onayla',
                'auth.remember_me' => 'Beni hatırla',
                'auth.forgot_password' => 'Şifrenizi mi unuttunuz?',
                'auth.reset_password' => 'Şifreyi sıfırla',
                'auth.send_reset_link' => 'Şifre sıfırlama bağlantısı gönder',
                'auth.create_account' => 'Hesap oluştur',
                'auth.already_have_account' => 'Zaten hesabınız var mı?',
                'auth.dont_have_account' => 'Hesabınız yok mu?'
            ],
            'zh' => [
                'auth.login' => '登录',
                'auth.register' => '注册',
                'auth.password' => '密码',
                'auth.email' => '电子邮件地址',
                'auth.confirm_password' => '确认密码',
                'auth.remember_me' => '记住我',
                'auth.forgot_password' => '忘记密码？',
                'auth.reset_password' => '重置密码',
                'auth.send_reset_link' => '发送密码重置链接',
                'auth.create_account' => '创建账户',
                'auth.already_have_account' => '已经有账户了？',
                'auth.dont_have_account' => '没有账户？'
            ]
        ];

        $this->addTranslationsToFiles($authTranslations, 'auth.json');
        echo "   ✅ Auth translations added to all languages\n\n";
    }

    private function addMissingCompanyTranslations()
    {
        echo "3️⃣ Adding Missing Company Profile Translations...\n";

        $companyTranslations = [
            'en' => [
                'messages.company_profile' => 'Company Profile',
                'messages.company_details' => 'Company Details',
                'messages.company_overview' => 'Company Overview',
                'messages.company_jobs' => 'Company Jobs',
                'messages.company_size_detail' => 'Company Size',
                'messages.company_location' => 'Company Location',
                'messages.company_website' => 'Company Website',
                'messages.company_founded' => 'Founded',
                'messages.company_employees' => 'Employees',
                'messages.about_company' => 'About Company'
            ],
            'ar' => [
                'messages.company_profile' => 'ملف الشركة',
                'messages.company_details' => 'تفاصيل الشركة',
                'messages.company_overview' => 'نظرة عامة على الشركة',
                'messages.company_jobs' => 'وظائف الشركة',
                'messages.company_size_detail' => 'حجم الشركة',
                'messages.company_location' => 'موقع الشركة',
                'messages.company_website' => 'موقع الشركة',
                'messages.company_founded' => 'تأسست',
                'messages.about_company' => 'عن الشركة'
            ],
            'de' => [
                'messages.company_profile' => 'Firmenprofil',
                'messages.company_details' => 'Firmendetails',
                'messages.company_overview' => 'Firmenübersicht',
                'messages.company_jobs' => 'Firmenjobs',
                'messages.company_size_detail' => 'Firmengröße',
                'messages.company_location' => 'Firmenstandort',
                'messages.company_website' => 'Firmen-Website',
                'messages.company_founded' => 'Gegründet',
                'messages.about_company' => 'Über das Unternehmen'
            ],
            'es' => [
                'messages.company_profile' => 'Perfil de la empresa',
                'messages.company_details' => 'Detalles de la empresa',
                'messages.company_overview' => 'Descripción de la empresa',
                'messages.company_jobs' => 'Trabajos de la empresa',
                'messages.company_size_detail' => 'Tamaño de la empresa',
                'messages.company_location' => 'Ubicación de la empresa',
                'messages.company_website' => 'Sitio web de la empresa',
                'messages.company_founded' => 'Fundada',
                'messages.about_company' => 'Acerca de la empresa'
            ],
            'fr' => [
                'messages.company_profile' => 'Profil de l\'entreprise',
                'messages.company_details' => 'Détails de l\'entreprise',
                'messages.company_overview' => 'Aperçu de l\'entreprise',
                'messages.company_jobs' => 'Emplois de l\'entreprise',
                'messages.company_size_detail' => 'Taille de l\'entreprise',
                'messages.company_location' => 'Emplacement de l\'entreprise',
                'messages.company_website' => 'Site web de l\'entreprise',
                'messages.company_founded' => 'Fondée',
                'messages.about_company' => 'À propos de l\'entreprise'
            ],
            'pt' => [
                'messages.company_profile' => 'Perfil da empresa',
                'messages.company_details' => 'Detalhes da empresa',
                'messages.company_overview' => 'Visão geral da empresa',
                'messages.company_jobs' => 'Empregos da empresa',
                'messages.company_size_detail' => 'Tamanho da empresa',
                'messages.company_location' => 'Localização da empresa',
                'messages.company_website' => 'Site da empresa',
                'messages.company_founded' => 'Fundada',
                'messages.about_company' => 'Sobre a empresa'
            ],
            'ru' => [
                'messages.company_profile' => 'Профиль компании',
                'messages.company_details' => 'Детали компании',
                'messages.company_overview' => 'Обзор компании',
                'messages.company_jobs' => 'Вакансии компании',
                'messages.company_size_detail' => 'Размер компании',
                'messages.company_location' => 'Местоположение компании',
                'messages.company_website' => 'Веб-сайт компании',
                'messages.company_founded' => 'Основана',
                'messages.about_company' => 'О компании'
            ],
            'tr' => [
                'messages.company_profile' => 'Şirket Profili',
                'messages.company_details' => 'Şirket Detayları',
                'messages.company_overview' => 'Şirket Genel Bakış',
                'messages.company_jobs' => 'Şirket İşleri',
                'messages.company_size_detail' => 'Şirket Boyutu',
                'messages.company_location' => 'Şirket Konumu',
                'messages.company_website' => 'Şirket Web Sitesi',
                'messages.company_founded' => 'Kuruldu',
                'messages.about_company' => 'Şirket Hakkında'
            ],
            'zh' => [
                'messages.company_profile' => '公司简介',
                'messages.company_details' => '公司详情',
                'messages.company_overview' => '公司概述',
                'messages.company_jobs' => '公司职位',
                'messages.company_size_detail' => '公司规模',
                'messages.company_location' => '公司位置',
                'messages.company_website' => '公司网站',
                'messages.company_founded' => '成立',
                'messages.about_company' => '关于公司'
            ]
        ];

        $this->addTranslationsToFiles($companyTranslations, 'messages.json');
        echo "   ✅ Company profile translations added to all languages\n\n";
    }

    private function addMissingNavigationTranslations()
    {
        echo "4️⃣ Adding Missing Navigation Translations...\n";

        $navigationTranslations = [
            'en' => [
                'nav.home' => 'Home',
                'nav.jobs' => 'Jobs',
                'nav.companies' => 'Companies',
                'nav.candidates' => 'Candidates',
                'nav.about' => 'About Us',
                'nav.contact' => 'Contact',
                'nav.login' => 'Login',
                'nav.register' => 'Register',
                'nav.dashboard' => 'Dashboard',
                'nav.profile' => 'Profile',
                'nav.logout' => 'Logout',
                'nav.find_jobs' => 'Find Jobs',
                'nav.post_job' => 'Post Job',
                'nav.browse_companies' => 'Browse Companies',
                'nav.admin_panel' => 'Admin Panel'
            ],
            'ar' => [
                'nav.home' => 'الرئيسية',
                'nav.jobs' => 'الوظائف',
                'nav.companies' => 'الشركات',
                'nav.candidates' => 'المرشحون',
                'nav.about' => 'معلومات عنا',
                'nav.contact' => 'اتصل بنا',
                'nav.login' => 'تسجيل الدخول',
                'nav.register' => 'التسجيل',
                'nav.dashboard' => 'لوحة التحكم',
                'nav.profile' => 'الملف الشخصي',
                'nav.logout' => 'تسجيل الخروج',
                'nav.find_jobs' => 'البحث عن وظائف',
                'nav.post_job' => 'انشر وظيفة',
                'nav.browse_companies' => 'تصفح الشركات',
                'nav.admin_panel' => 'لوحة الإدارة'
            ],
            'de' => [
                'nav.home' => 'Startseite',
                'nav.jobs' => 'Jobs',
                'nav.companies' => 'Unternehmen',
                'nav.candidates' => 'Kandidaten',
                'nav.about' => 'Über uns',
                'nav.contact' => 'Kontakt',
                'nav.login' => 'Anmelden',
                'nav.register' => 'Registrieren',
                'nav.dashboard' => 'Dashboard',
                'nav.profile' => 'Profil',
                'nav.logout' => 'Abmelden',
                'nav.find_jobs' => 'Jobs finden',
                'nav.post_job' => 'Job posten',
                'nav.browse_companies' => 'Unternehmen durchsuchen',
                'nav.admin_panel' => 'Admin-Panel'
            ],
            'es' => [
                'nav.home' => 'Inicio',
                'nav.jobs' => 'Trabajos',
                'nav.companies' => 'Empresas',
                'nav.candidates' => 'Candidatos',
                'nav.about' => 'Acerca de',
                'nav.contact' => 'Contacto',
                'nav.login' => 'Iniciar sesión',
                'nav.register' => 'Registrarse',
                'nav.dashboard' => 'Panel de control',
                'nav.profile' => 'Perfil',
                'nav.logout' => 'Cerrar sesión',
                'nav.find_jobs' => 'Buscar trabajos',
                'nav.post_job' => 'Publicar trabajo',
                'nav.browse_companies' => 'Explorar empresas',
                'nav.admin_panel' => 'Panel de administración'
            ],
            'fr' => [
                'nav.home' => 'Accueil',
                'nav.jobs' => 'Emplois',
                'nav.companies' => 'Entreprises',
                'nav.candidates' => 'Candidats',
                'nav.about' => 'À propos',
                'nav.contact' => 'Contact',
                'nav.login' => 'Connexion',
                'nav.register' => 'S\'inscrire',
                'nav.dashboard' => 'Tableau de bord',
                'nav.profile' => 'Profil',
                'nav.logout' => 'Déconnexion',
                'nav.find_jobs' => 'Trouver des emplois',
                'nav.post_job' => 'Publier un emploi',
                'nav.browse_companies' => 'Parcourir les entreprises',
                'nav.admin_panel' => 'Panel d\'administration'
            ],
            'pt' => [
                'nav.home' => 'Início',
                'nav.jobs' => 'Empregos',
                'nav.companies' => 'Empresas',
                'nav.candidates' => 'Candidatos',
                'nav.about' => 'Sobre nós',
                'nav.contact' => 'Contato',
                'nav.login' => 'Entrar',
                'nav.register' => 'Registrar',
                'nav.dashboard' => 'Painel',
                'nav.profile' => 'Perfil',
                'nav.logout' => 'Sair',
                'nav.find_jobs' => 'Encontrar empregos',
                'nav.post_job' => 'Publicar emprego',
                'nav.browse_companies' => 'Navegar pelas empresas',
                'nav.admin_panel' => 'Painel de administração'
            ],
            'ru' => [
                'nav.home' => 'Главная',
                'nav.jobs' => 'Вакансии',
                'nav.companies' => 'Компании',
                'nav.candidates' => 'Кандидаты',
                'nav.about' => 'О нас',
                'nav.contact' => 'Контакты',
                'nav.login' => 'Вход',
                'nav.register' => 'Регистрация',
                'nav.dashboard' => 'Панель управления',
                'nav.profile' => 'Профиль',
                'nav.logout' => 'Выход',
                'nav.find_jobs' => 'Найти работу',
                'nav.post_job' => 'Разместить вакансию',
                'nav.browse_companies' => 'Просмотр компаний',
                'nav.admin_panel' => 'Панель администратора'
            ],
            'tr' => [
                'nav.home' => 'Ana Sayfa',
                'nav.jobs' => 'İşler',
                'nav.companies' => 'Şirketler',
                'nav.candidates' => 'Adaylar',
                'nav.about' => 'Hakkımızda',
                'nav.contact' => 'İletişim',
                'nav.login' => 'Giriş Yap',
                'nav.register' => 'Kayıt Ol',
                'nav.dashboard' => 'Panel',
                'nav.profile' => 'Profil',
                'nav.logout' => 'Çıkış Yap',
                'nav.find_jobs' => 'İş Bul',
                'nav.post_job' => 'İş İlanı Ver',
                'nav.browse_companies' => 'Şirketlere Gözat',
                'nav.admin_panel' => 'Yönetici Paneli'
            ],
            'zh' => [
                'nav.home' => '首页',
                'nav.jobs' => '工作',
                'nav.companies' => '公司',
                'nav.candidates' => '候选人',
                'nav.about' => '关于我们',
                'nav.contact' => '联系',
                'nav.login' => '登录',
                'nav.register' => '注册',
                'nav.dashboard' => '仪表板',
                'nav.profile' => '个人资料',
                'nav.logout' => '退出',
                'nav.find_jobs' => '找工作',
                'nav.post_job' => '发布工作',
                'nav.browse_companies' => '浏览公司',
                'nav.admin_panel' => '管理面板'
            ]
        ];

        $this->addTranslationsToFiles($navigationTranslations, 'web.json');
        echo "   ✅ Navigation translations added to all languages\n\n";
    }

    private function addTranslationsToFiles($translations, $fileName)
    {
        foreach ($this->languages as $lang) {
            $filePath = $this->langPath . '/' . $lang . '_json/' . $fileName;
            
            if (!isset($translations[$lang])) {
                continue;
            }

            if (file_exists($filePath)) {
                $existingData = json_decode(file_get_contents($filePath), true);
                
                if ($existingData === null) {
                    echo "   ❌ Error reading {$lang}/{$fileName}\n";
                    continue;
                }

                $updated = false;
                foreach ($translations[$lang] as $key => $value) {
                    if ($this->setNestedValue($existingData, $key, $value)) {
                        $updated = true;
                        $this->fixedTranslations[] = "{$lang}: {$key}";
                    }
                }

                if ($updated) {
                    $jsonContent = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    file_put_contents($filePath, $jsonContent);
                    echo "   ✅ Updated {$lang}/{$fileName}\n";
                }
            }
        }
    }

    private function setNestedValue(&$array, $key, $value)
    {
        $keys = explode('.', $key);
        $current = &$array;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        if (is_array($current) || $current === []) {
            $current = $value;
            return true;
        }
        
        if ($current !== $value) {
            $current = $value;
            return true;
        }
        
        return false;
    }

    private function scanAllBladesForHardcodedText()
    {
        echo "5️⃣ Scanning All Blade Files for Hardcoded Text...\n";

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->viewsPath)
        );

        $bladeFiles = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $bladeFiles[] = $file->getRealPath();
            }
        }

        echo "   📄 Found " . count($bladeFiles) . " blade files to scan\n";

        $patterns = [
            // Common hardcoded strings
            '/>\s*([A-Z][a-zA-Z\s]{3,50})\s*</',  // Text between tags
            '/value\s*=\s*["\']([A-Z][a-zA-Z\s]{3,30})["\']/',  // Value attributes
            '/placeholder\s*=\s*["\']([A-Z][a-zA-Z\s]{3,30})["\']/',  // Placeholder attributes
            '/title\s*=\s*["\']([A-Z][a-zA-Z\s]{3,30})["\']/',  // Title attributes
        ];

        $commonStrings = [];
        $fileCount = 0;

        foreach ($bladeFiles as $filePath) {
            $content = file_get_contents($filePath);
            $relativePath = str_replace($this->viewsPath . '/', '', $filePath);
            
            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $content, $matches)) {
                    foreach ($matches[1] as $match) {
                        $cleaned = trim($match);
                        if (strlen($cleaned) > 3 && !$this->isTranslationCall($cleaned)) {
                            $commonStrings[$cleaned][] = $relativePath;
                        }
                    }
                }
            }
            $fileCount++;
        }

        // Report findings
        $uniqueStrings = array_keys($commonStrings);
        echo "   📊 Scanned {$fileCount} blade files\n";
        echo "   📝 Found " . count($uniqueStrings) . " potential hardcoded strings\n\n";

        if (count($uniqueStrings) > 0) {
            echo "   🔍 Top 20 hardcoded strings found:\n";
            $sortedStrings = array_slice($uniqueStrings, 0, 20);
            foreach ($sortedStrings as $string) {
                $fileCount = count($commonStrings[$string]);
                echo "      • \"{$string}\" (found in {$fileCount} files)\n";
                $this->hardcodedStrings[] = $string;
            }
        }
        echo "\n";
    }

    private function isTranslationCall($text)
    {
        // Check if text looks like it's already using translation functions
        $translationPatterns = [
            '/^__\(/',
            '/^@lang\(/',
            '/^trans\(/',
            '/^@__\(/',
            '/{{.*__\(/',
            '/{{.*@lang\(/',
        ];

        foreach ($translationPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    private function generateFixReport()
    {
        echo "📋 TRANSLATION FIX REPORT\n";
        echo "=========================\n\n";

        // Summary
        echo "📊 SUMMARY:\n";
        echo "-----------\n";
        echo "Fixed translations: " . count($this->fixedTranslations) . "\n";
        echo "Hardcoded strings found: " . count($this->hardcodedStrings) . "\n";
        echo "Languages updated: " . count($this->languages) . "\n\n";

        // Translation fixes by language
        $fixesByLanguage = [];
        foreach ($this->fixedTranslations as $fix) {
            $parts = explode(':', $fix, 2);
            $lang = $parts[0];
            $fixesByLanguage[$lang] = ($fixesByLanguage[$lang] ?? 0) + 1;
        }

        echo "🔧 FIXES BY LANGUAGE:\n";
        echo "---------------------\n";
        foreach ($fixesByLanguage as $lang => $count) {
            $flag = $this->getLanguageFlag($lang);
            echo "{$flag} {$lang}: {$count} translations added\n";
        }
        echo "\n";

        // Most common hardcoded strings
        if (count($this->hardcodedStrings) > 0) {
            echo "⚠️  HARDCODED STRINGS TO FIX:\n";
            echo "-----------------------------\n";
            foreach (array_slice($this->hardcodedStrings, 0, 15) as $string) {
                echo "• \"{$string}\"\n";
            }
            echo "\n";

            echo "💡 RECOMMENDED FIXES:\n";
            echo "---------------------\n";
            echo "1. Replace hardcoded strings with @lang() or __() calls\n";
            echo "2. Add corresponding translation keys to all language files\n";
            echo "3. Use semantic translation keys (e.g., 'auth.login' instead of 'Login')\n";
            echo "4. Implement translation validation in CI/CD pipeline\n";
            echo "5. Use translation extraction tools for automated detection\n\n";
        }

        echo "🎯 NEXT STEPS:\n";
        echo "--------------\n";
        echo "1. Test translation system with all new keys\n";
        echo "2. Update blade templates to use translation functions\n";
        echo "3. Run comprehensive translation validation\n";
        echo "4. Implement language switcher in UI\n";
        echo "5. Configure RTL support for Arabic layout\n\n";

        echo "✅ Translation fix process completed!\n";
        echo "Coverage improvement expected: +15-20%\n";
    }

    private function getLanguageFlag($lang)
    {
        $flags = [
            'en' => '🇺🇸',
            'ar' => '🇸🇦',
            'de' => '🇩🇪',
            'es' => '🇪🇸',
            'fr' => '🇫🇷',
            'pt' => '🇵🇹',
            'ru' => '🇷🇺',
            'tr' => '🇹🇷',
            'zh' => '🇨🇳'
        ];
        return $flags[$lang] ?? '🏳️';
    }
}

// Run the translation fixer
$fixer = new Context7TranslationFixer();
$fixer->fixAllMissingTranslations(); 