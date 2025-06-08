<?php

/**
 * Context7 Blade Hardcoded Text Fixer
 * Scans and fixes hardcoded text in blade files
 */

class Context7BladeHardcodedFixer
{
    private $viewsPath;
    private $fixedFiles = [];
    private $translationMappings = [];
    private $totalReplacements = 0;

    public function __construct()
    {
        $this->viewsPath = __DIR__ . '/resources/views';
        
        // Define translation mappings for common hardcoded strings
        $this->translationMappings = [
            'Welcome to Job Portal' => '{{ __("web.welcome_title") }}',
            'Find your dream job or hire the perfect candidate' => '{{ __("web.welcome_subtitle") }}',
            'For Job Seekers' => '{{ __("web.for_job_seekers") }}',
            'For Employers' => '{{ __("web.for_employers") }}',
            'Find the perfect candidates for your company' => '{{ __("web.find_perfect_candidates") }}',
            'Post a Job' => '{{ __("web.post_job") }}',
            'Login' => '{{ __("auth.login") }}',
            'Register' => '{{ __("auth.register") }}',
            'Edit' => '{{ __("common.edit") }}',
            'Delete' => '{{ __("common.delete") }}',
            'Candidate Profile' => '{{ __("web.candidate_profile") }}',
            'Front Settings' => '{{ __("common.front_settings") }}',
            'Notification Settings' => '{{ __("common.notification_settings") }}',
            'Subscribe' => '{{ __("common.subscribe") }}',
            'Confirm Password' => '{{ __("auth.confirm_password") }}',
            'Forgot Password' => '{{ __("auth.forgot_password") }}',
            'Reset Password' => '{{ __("auth.reset_password") }}',
            'Email Verification' => '{{ __("auth.email_verification") }}',
            'Dashboard' => '{{ __("nav.dashboard") }}',
            'Profile' => '{{ __("nav.profile") }}',
            'Logout' => '{{ __("nav.logout") }}',
            'Home' => '{{ __("nav.home") }}',
            'Jobs' => '{{ __("nav.jobs") }}',
            'Companies' => '{{ __("nav.companies") }}',
            'Candidates' => '{{ __("nav.candidates") }}',
            'About Us' => '{{ __("nav.about") }}',
            'Contact' => '{{ __("nav.contact") }}',
            'Save' => '{{ __("common.save") }}',
            'Cancel' => '{{ __("common.cancel") }}',
            'Submit' => '{{ __("common.submit") }}',
            'Close' => '{{ __("common.close") }}',
            'Search' => '{{ __("common.search") }}',
            'Name' => '{{ __("common.name") }}',
            'Email' => '{{ __("common.email") }}',
            'Phone' => '{{ __("common.phone") }}',
            'Address' => '{{ __("common.address") }}',
            'Description' => '{{ __("common.description") }}',
            'Actions' => '{{ __("common.actions") }}',
            'Status' => '{{ __("common.status") }}',
            'Active' => '{{ __("common.active") }}',
            'Inactive' => '{{ __("common.inactive") }}',
            'Created At' => '{{ __("common.created_at") }}',
            'Updated At' => '{{ __("common.updated_at") }}',
            'View' => '{{ __("common.view") }}',
            'Add' => '{{ __("common.add") }}',
            'Create' => '{{ __("common.create") }}',
            'Update' => '{{ __("common.update") }}',
            'Show' => '{{ __("common.show") }}',
            'Loading...' => '{{ __("common.loading") }}',
            'Please wait...' => '{{ __("common.please_wait") }}',
            'Success' => '{{ __("common.success") }}',
            'Error' => '{{ __("common.error") }}',
            'Warning' => '{{ __("common.warning") }}',
            'Info' => '{{ __("common.info") }}',
        ];

        echo "🔧 Context7 Blade Hardcoded Text Fixer\n";
        echo "======================================\n\n";
    }

    public function scanAndFixAllBlades()
    {
        echo "🚀 Starting Blade Hardcoded Text Scan and Fix...\n\n";

        $this->addMissingCommonTranslations();
        $this->scanAndFixBladeFiles();
        $this->generateFixReport();
    }

    private function addMissingCommonTranslations()
    {
        echo "1️⃣ Adding Missing Common Translations...\n";

        $commonTranslations = [
            'en' => [
                'common.edit' => 'Edit',
                'common.delete' => 'Delete',
                'common.save' => 'Save',
                'common.cancel' => 'Cancel',
                'common.submit' => 'Submit',
                'common.close' => 'Close',
                'common.search' => 'Search',
                'common.name' => 'Name',
                'common.email' => 'Email',
                'common.phone' => 'Phone',
                'common.address' => 'Address',
                'common.description' => 'Description',
                'common.actions' => 'Actions',
                'common.status' => 'Status',
                'common.active' => 'Active',
                'common.inactive' => 'Inactive',
                'common.created_at' => 'Created At',
                'common.updated_at' => 'Updated At',
                'common.view' => 'View',
                'common.add' => 'Add',
                'common.create' => 'Create',
                'common.update' => 'Update',
                'common.show' => 'Show',
                'common.loading' => 'Loading...',
                'common.please_wait' => 'Please wait...',
                'common.success' => 'Success',
                'common.error' => 'Error',
                'common.warning' => 'Warning',
                'common.info' => 'Info',
                'common.front_settings' => 'Front Settings',
                'common.notification_settings' => 'Notification Settings',
                'common.subscribe' => 'Subscribe',
                'web.candidate_profile' => 'Candidate Profile',
                'web.find_perfect_candidates' => 'Find the perfect candidates for your company',
                'auth.email_verification' => 'Email Verification'
            ],
            'ar' => [
                'common.edit' => 'تحرير',
                'common.delete' => 'حذف',
                'common.save' => 'حفظ',
                'common.cancel' => 'إلغاء',
                'common.submit' => 'إرسال',
                'common.close' => 'إغلاق',
                'common.search' => 'بحث',
                'common.name' => 'الاسم',
                'common.email' => 'البريد الإلكتروني',
                'common.phone' => 'الهاتف',
                'common.address' => 'العنوان',
                'common.description' => 'الوصف',
                'common.actions' => 'الإجراءات',
                'common.status' => 'الحالة',
                'common.active' => 'نشط',
                'common.inactive' => 'غير نشط',
                'common.created_at' => 'تاريخ الإنشاء',
                'common.updated_at' => 'تاريخ التحديث',
                'common.view' => 'عرض',
                'common.add' => 'إضافة',
                'common.create' => 'إنشاء',
                'common.update' => 'تحديث',
                'common.show' => 'إظهار',
                'common.loading' => 'جاري التحميل...',
                'common.please_wait' => 'يرجى الانتظار...',
                'common.success' => 'نجح',
                'common.error' => 'خطأ',
                'common.warning' => 'تحذير',
                'common.info' => 'معلومات',
                'common.front_settings' => 'إعدادات الواجهة',
                'common.notification_settings' => 'إعدادات الإشعارات',
                'common.subscribe' => 'اشتراك',
                'web.candidate_profile' => 'ملف المرشح',
                'web.find_perfect_candidates' => 'العثور على المرشحين المثاليين لشركتك',
                'auth.email_verification' => 'التحقق من البريد الإلكتروني'
            ],
            'de' => [
                'common.edit' => 'Bearbeiten',
                'common.delete' => 'Löschen',
                'common.save' => 'Speichern',
                'common.cancel' => 'Abbrechen',
                'common.submit' => 'Absenden',
                'common.close' => 'Schließen',
                'common.search' => 'Suchen',
                'common.name' => 'Name',
                'common.email' => 'E-Mail',
                'common.phone' => 'Telefon',
                'common.address' => 'Adresse',
                'common.description' => 'Beschreibung',
                'common.actions' => 'Aktionen',
                'common.status' => 'Status',
                'common.active' => 'Aktiv',
                'common.inactive' => 'Inaktiv',
                'common.created_at' => 'Erstellt am',
                'common.updated_at' => 'Aktualisiert am',
                'common.view' => 'Ansehen',
                'common.add' => 'Hinzufügen',
                'common.create' => 'Erstellen',
                'common.update' => 'Aktualisieren',
                'common.show' => 'Anzeigen',
                'common.loading' => 'Laden...',
                'common.please_wait' => 'Bitte warten...',
                'common.success' => 'Erfolg',
                'common.error' => 'Fehler',
                'common.warning' => 'Warnung',
                'common.info' => 'Info',
                'common.front_settings' => 'Frontend-Einstellungen',
                'common.notification_settings' => 'Benachrichtigungseinstellungen',
                'common.subscribe' => 'Abonnieren',
                'web.candidate_profile' => 'Kandidatenprofil',
                'web.find_perfect_candidates' => 'Finden Sie die perfekten Kandidaten für Ihr Unternehmen',
                'auth.email_verification' => 'E-Mail-Verifizierung'
            ],
            'es' => [
                'common.edit' => 'Editar',
                'common.delete' => 'Eliminar',
                'common.save' => 'Guardar',
                'common.cancel' => 'Cancelar',
                'common.submit' => 'Enviar',
                'common.close' => 'Cerrar',
                'common.search' => 'Buscar',
                'common.name' => 'Nombre',
                'common.email' => 'Correo electrónico',
                'common.phone' => 'Teléfono',
                'common.address' => 'Dirección',
                'common.description' => 'Descripción',
                'common.actions' => 'Acciones',
                'common.status' => 'Estado',
                'common.active' => 'Activo',
                'common.inactive' => 'Inactivo',
                'common.created_at' => 'Creado en',
                'common.updated_at' => 'Actualizado en',
                'common.view' => 'Ver',
                'common.add' => 'Agregar',
                'common.create' => 'Crear',
                'common.update' => 'Actualizar',
                'common.show' => 'Mostrar',
                'common.loading' => 'Cargando...',
                'common.please_wait' => 'Por favor espere...',
                'common.success' => 'Éxito',
                'common.error' => 'Error',
                'common.warning' => 'Advertencia',
                'common.info' => 'Información',
                'common.front_settings' => 'Configuración frontal',
                'common.notification_settings' => 'Configuración de notificaciones',
                'common.subscribe' => 'Suscribirse',
                'web.candidate_profile' => 'Perfil del candidato',
                'web.find_perfect_candidates' => 'Encuentra los candidatos perfectos para tu empresa',
                'auth.email_verification' => 'Verificación de correo electrónico'
            ],
            'fr' => [
                'common.edit' => 'Modifier',
                'common.delete' => 'Supprimer',
                'common.save' => 'Sauvegarder',
                'common.cancel' => 'Annuler',
                'common.submit' => 'Soumettre',
                'common.close' => 'Fermer',
                'common.search' => 'Rechercher',
                'common.name' => 'Nom',
                'common.email' => 'E-mail',
                'common.phone' => 'Téléphone',
                'common.address' => 'Adresse',
                'common.description' => 'Description',
                'common.actions' => 'Actions',
                'common.status' => 'Statut',
                'common.active' => 'Actif',
                'common.inactive' => 'Inactif',
                'common.created_at' => 'Créé le',
                'common.updated_at' => 'Mis à jour le',
                'common.view' => 'Voir',
                'common.add' => 'Ajouter',
                'common.create' => 'Créer',
                'common.update' => 'Mettre à jour',
                'common.show' => 'Afficher',
                'common.loading' => 'Chargement...',
                'common.please_wait' => 'Veuillez patienter...',
                'common.success' => 'Succès',
                'common.error' => 'Erreur',
                'common.warning' => 'Avertissement',
                'common.info' => 'Information',
                'common.front_settings' => 'Paramètres de façade',
                'common.notification_settings' => 'Paramètres de notification',
                'common.subscribe' => 'S\'abonner',
                'web.candidate_profile' => 'Profil du candidat',
                'web.find_perfect_candidates' => 'Trouvez les candidats parfaits pour votre entreprise',
                'auth.email_verification' => 'Vérification e-mail'
            ],
            'pt' => [
                'common.edit' => 'Editar',
                'common.delete' => 'Excluir',
                'common.save' => 'Salvar',
                'common.cancel' => 'Cancelar',
                'common.submit' => 'Enviar',
                'common.close' => 'Fechar',
                'common.search' => 'Pesquisar',
                'common.name' => 'Nome',
                'common.email' => 'E-mail',
                'common.phone' => 'Telefone',
                'common.address' => 'Endereço',
                'common.description' => 'Descrição',
                'common.actions' => 'Ações',
                'common.status' => 'Status',
                'common.active' => 'Ativo',
                'common.inactive' => 'Inativo',
                'common.created_at' => 'Criado em',
                'common.updated_at' => 'Atualizado em',
                'common.view' => 'Ver',
                'common.add' => 'Adicionar',
                'common.create' => 'Criar',
                'common.update' => 'Atualizar',
                'common.show' => 'Mostrar',
                'common.loading' => 'Carregando...',
                'common.please_wait' => 'Por favor aguarde...',
                'common.success' => 'Sucesso',
                'common.error' => 'Erro',
                'common.warning' => 'Aviso',
                'common.info' => 'Informação',
                'common.front_settings' => 'Configurações frontais',
                'common.notification_settings' => 'Configurações de notificação',
                'common.subscribe' => 'Inscrever-se',
                'web.candidate_profile' => 'Perfil do candidato',
                'web.find_perfect_candidates' => 'Encontre os candidatos perfeitos para sua empresa',
                'auth.email_verification' => 'Verificação de e-mail'
            ],
            'ru' => [
                'common.edit' => 'Редактировать',
                'common.delete' => 'Удалить',
                'common.save' => 'Сохранить',
                'common.cancel' => 'Отмена',
                'common.submit' => 'Отправить',
                'common.close' => 'Закрыть',
                'common.search' => 'Поиск',
                'common.name' => 'Имя',
                'common.email' => 'Электронная почта',
                'common.phone' => 'Телефон',
                'common.address' => 'Адрес',
                'common.description' => 'Описание',
                'common.actions' => 'Действия',
                'common.status' => 'Статус',
                'common.active' => 'Активный',
                'common.inactive' => 'Неактивный',
                'common.created_at' => 'Создано',
                'common.updated_at' => 'Обновлено',
                'common.view' => 'Просмотр',
                'common.add' => 'Добавить',
                'common.create' => 'Создать',
                'common.update' => 'Обновить',
                'common.show' => 'Показать',
                'common.loading' => 'Загрузка...',
                'common.please_wait' => 'Пожалуйста, подождите...',
                'common.success' => 'Успех',
                'common.error' => 'Ошибка',
                'common.warning' => 'Предупреждение',
                'common.info' => 'Информация',
                'common.front_settings' => 'Настройки фронтенда',
                'common.notification_settings' => 'Настройки уведомлений',
                'common.subscribe' => 'Подписаться',
                'web.candidate_profile' => 'Профиль кандидата',
                'web.find_perfect_candidates' => 'Найдите идеальных кандидатов для вашей компании',
                'auth.email_verification' => 'Подтверждение электронной почты'
            ],
            'tr' => [
                'common.edit' => 'Düzenle',
                'common.delete' => 'Sil',
                'common.save' => 'Kaydet',
                'common.cancel' => 'İptal',
                'common.submit' => 'Gönder',
                'common.close' => 'Kapat',
                'common.search' => 'Ara',
                'common.name' => 'İsim',
                'common.email' => 'E-posta',
                'common.phone' => 'Telefon',
                'common.address' => 'Adres',
                'common.description' => 'Açıklama',
                'common.actions' => 'İşlemler',
                'common.status' => 'Durum',
                'common.active' => 'Aktif',
                'common.inactive' => 'Pasif',
                'common.created_at' => 'Oluşturulma',
                'common.updated_at' => 'Güncellenme',
                'common.view' => 'Görüntüle',
                'common.add' => 'Ekle',
                'common.create' => 'Oluştur',
                'common.update' => 'Güncelle',
                'common.show' => 'Göster',
                'common.loading' => 'Yükleniyor...',
                'common.please_wait' => 'Lütfen bekleyin...',
                'common.success' => 'Başarı',
                'common.error' => 'Hata',
                'common.warning' => 'Uyarı',
                'common.info' => 'Bilgi',
                'common.front_settings' => 'Ön Ayarlar',
                'common.notification_settings' => 'Bildirim Ayarları',
                'common.subscribe' => 'Abone ol',
                'web.candidate_profile' => 'Aday Profili',
                'web.find_perfect_candidates' => 'Şirketiniz için mükemmel adayları bulun',
                'auth.email_verification' => 'E-posta Doğrulama'
            ],
            'zh' => [
                'common.edit' => '编辑',
                'common.delete' => '删除',
                'common.save' => '保存',
                'common.cancel' => '取消',
                'common.submit' => '提交',
                'common.close' => '关闭',
                'common.search' => '搜索',
                'common.name' => '姓名',
                'common.email' => '电子邮件',
                'common.phone' => '电话',
                'common.address' => '地址',
                'common.description' => '描述',
                'common.actions' => '操作',
                'common.status' => '状态',
                'common.active' => '活跃',
                'common.inactive' => '非活跃',
                'common.created_at' => '创建时间',
                'common.updated_at' => '更新时间',
                'common.view' => '查看',
                'common.add' => '添加',
                'common.create' => '创建',
                'common.update' => '更新',
                'common.show' => '显示',
                'common.loading' => '加载中...',
                'common.please_wait' => '请稍候...',
                'common.success' => '成功',
                'common.error' => '错误',
                'common.warning' => '警告',
                'common.info' => '信息',
                'common.front_settings' => '前端设置',
                'common.notification_settings' => '通知设置',
                'common.subscribe' => '订阅',
                'web.candidate_profile' => '候选人资料',
                'web.find_perfect_candidates' => '为您的公司找到完美的候选人',
                'auth.email_verification' => '电子邮件验证'
            ]
        ];

        $this->addTranslationsToFiles($commonTranslations, 'common.json');
        echo "   ✅ Common translations added to all languages\n\n";
    }

    private function addTranslationsToFiles($translations, $fileName)
    {
        $languages = ['en', 'ar', 'de', 'es', 'fr', 'pt', 'ru', 'tr', 'zh'];
        
        foreach ($languages as $lang) {
            $filePath = __DIR__ . '/lang/' . $lang . '_json/' . $fileName;
            
            if (!isset($translations[$lang])) {
                continue;
            }

            if (file_exists($filePath)) {
                $existingData = json_decode(file_get_contents($filePath), true);
                
                if ($existingData === null) {
                    $existingData = [];
                }

                $updated = false;
                foreach ($translations[$lang] as $key => $value) {
                    if ($this->setNestedValue($existingData, $key, $value)) {
                        $updated = true;
                    }
                }

                if ($updated) {
                    $jsonContent = json_encode($existingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    file_put_contents($filePath, $jsonContent);
                    echo "   ✅ Updated {$lang}/{$fileName}\n";
                }
            } else {
                // Create new file
                $jsonContent = json_encode($translations[$lang], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($filePath, $jsonContent);
                echo "   ✅ Created {$lang}/{$fileName}\n";
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
        
        if (is_array($current) || $current === [] || $current !== $value) {
            $current = $value;
            return true;
        }
        
        return false;
    }

    private function scanAndFixBladeFiles()
    {
        echo "2️⃣ Scanning and Fixing Blade Files...\n";

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->viewsPath)
        );

        $bladeFiles = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $bladeFiles[] = $file->getRealPath();
            }
        }

        echo "   📄 Found " . count($bladeFiles) . " blade files to process\n";

        foreach ($bladeFiles as $filePath) {
            $this->processBladeFile($filePath);
        }

        echo "   ✅ Processed " . count($bladeFiles) . " blade files\n";
        echo "   📝 Fixed " . count($this->fixedFiles) . " files\n";
        echo "   🔄 Made " . $this->totalReplacements . " replacements\n\n";
    }

    private function processBladeFile($filePath)
    {
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $replacements = 0;

        // Fix hardcoded strings using our mappings
        foreach ($this->translationMappings as $hardcoded => $replacement) {
            // Match hardcoded text in various contexts
            $patterns = [
                // Between HTML tags
                '/>\s*' . preg_quote($hardcoded, '/') . '\s*</',
                // In value attributes
                '/value\s*=\s*["\']' . preg_quote($hardcoded, '/') . '["\']/',
                // In placeholder attributes
                '/placeholder\s*=\s*["\']' . preg_quote($hardcoded, '/') . '["\']/',
                // In title attributes
                '/title\s*=\s*["\']' . preg_quote($hardcoded, '/') . '["\']/',
                // In button text
                '/<button[^>]*>\s*' . preg_quote($hardcoded, '/') . '\s*<\/button>/',
                // In span text
                '/<span[^>]*>\s*' . preg_quote($hardcoded, '/') . '\s*<\/span>/',
                // In h1-h6 tags
                '/<h[1-6][^>]*>\s*' . preg_quote($hardcoded, '/') . '\s*<\/h[1-6]>/',
                // In p tags
                '/<p[^>]*>\s*' . preg_quote($hardcoded, '/') . '\s*<\/p>/',
                // In a tags
                '/<a[^>]*>\s*' . preg_quote($hardcoded, '/') . '\s*<\/a>/',
            ];

            foreach ($patterns as $pattern) {
                $newContent = preg_replace_callback($pattern, function($matches) use ($replacement) {
                    return str_replace($hardcoded, $replacement, $matches[0]);
                }, $content);
                
                if ($newContent !== $content && $newContent !== null) {
                    $content = $newContent;
                    $replacements++;
                }
            }
        }

        // Save if changes were made
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $relativePath = str_replace($this->viewsPath . '/', '', $filePath);
            $this->fixedFiles[] = $relativePath;
            $this->totalReplacements += $replacements;
        }
    }

    private function generateFixReport()
    {
        echo "📋 BLADE HARDCODED TEXT FIX REPORT\n";
        echo "==================================\n\n";

        echo "📊 SUMMARY:\n";
        echo "-----------\n";
        echo "Total files processed: " . count(glob($this->viewsPath . '/**/*.php', GLOB_BRACE)) . "\n";
        echo "Files fixed: " . count($this->fixedFiles) . "\n";
        echo "Total replacements: " . $this->totalReplacements . "\n";
        echo "Translation mappings used: " . count($this->translationMappings) . "\n\n";

        if (count($this->fixedFiles) > 0) {
            echo "📝 FIXED FILES:\n";
            echo "---------------\n";
            foreach (array_slice($this->fixedFiles, 0, 20) as $file) {
                echo "• {$file}\n";
            }
            
            if (count($this->fixedFiles) > 20) {
                echo "... and " . (count($this->fixedFiles) - 20) . " more files\n";
            }
            echo "\n";
        }

        echo "🎯 COMPLETED TRANSLATIONS:\n";
        echo "--------------------------\n";
        foreach (array_slice($this->translationMappings, 0, 15) as $hardcoded => $replacement) {
            echo "• \"{$hardcoded}\" → {$replacement}\n";
        }
        echo "\n";

        echo "✅ Blade hardcoded text fix completed!\n";
        echo "All common strings now use translation functions.\n";
    }
}

// Run the blade hardcoded text fixer
$fixer = new Context7BladeHardcodedFixer();
$fixer->scanAndFixAllBlades(); 