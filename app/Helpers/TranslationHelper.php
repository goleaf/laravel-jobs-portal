<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;

/**
 * Class TranslationHelper
 * 
 * This class provides constants for all translation keys to ensure standardization
 * and to avoid typos when using translation strings.
 */
class TranslationHelper
{
    /**
     * Common translation keys that are consistently used across the application
     */
    // Common action translations
    public const COMMON_ADD = 'common.add';
    public const COMMON_EDIT = 'common.edit';
    public const COMMON_DELETE = 'common.delete';
    public const COMMON_SAVE = 'common.save';
    public const COMMON_CANCEL = 'common.cancel';
    public const COMMON_SEARCH = 'common.search';
    public const COMMON_RESET = 'common.reset';
    public const COMMON_APPLY = 'common.apply';
    public const COMMON_VIEW = 'common.view';
    public const COMMON_BACK = 'common.back';
    public const COMMON_NEXT = 'common.next';
    public const COMMON_PREVIOUS = 'common.previous';
    
    // Common status translations
    public const COMMON_YES = 'common.yes';
    public const COMMON_NO = 'common.no';
    public const COMMON_ACTIVE = 'common.active';
    public const COMMON_INACTIVE = 'common.inactive';
    public const COMMON_ENABLED = 'common.enabled';
    public const COMMON_DISABLED = 'common.disabled';
    
    // Common form/table translations
    public const COMMON_ACTIONS = 'common.actions';
    public const COMMON_NAME = 'common.name';
    public const COMMON_TITLE = 'common.title';
    public const COMMON_DESCRIPTION = 'common.description';
    public const COMMON_DATE = 'common.date';
    public const COMMON_NO_RECORDS = 'common.no_records_found';
    public const COMMON_PER_PAGE = 'common.per_page';
    public const COMMON_FILTERS = 'common.filters';
    public const COMMON_SELECT = 'common.select';
    
    // Common translations
    const COMMON_SEARCH = 'messages.common.search';
    const COMMON_RESET = 'messages.common.reset';
    const COMMON_ACTIONS = 'messages.common.actions';
    const COMMON_SAVE = 'messages.common.save';
    const COMMON_CANCEL = 'messages.common.cancel';
    const COMMON_EDIT = 'messages.common.edit';
    const COMMON_DELETE = 'messages.common.delete';
    const COMMON_VIEW = 'messages.common.view';
    const COMMON_BACK = 'messages.common.back';
    const COMMON_LOADING = 'messages.common.loading';
    const COMMON_CONFIRMATION = 'messages.common.confirmation';
    const COMMON_CONFIRMATION_MESSAGE = 'messages.common.confirmation_message';
    const COMMON_YES = 'messages.common.yes';
    const COMMON_NO = 'messages.common.no';
    const COMMON_ACTIVE = 'messages.common.active';
    const COMMON_INACTIVE = 'messages.common.inactive';
    const COMMON_STATUS = 'messages.common.status';
    const COMMON_DATE = 'messages.common.date';
    const COMMON_SHOWING = 'messages.common.showing';
    const COMMON_TO = 'messages.common.to';
    const COMMON_OF = 'messages.common.of';
    const COMMON_RESULTS = 'messages.common.results';
    const COMMON_NO_RESULTS = 'messages.common.no_results';
    const COMMON_CREATED_ON = 'messages.common.created_on';
    const COMMON_UPDATED_ON = 'messages.common.updated_on';
    const COMMON_EXPIRE = 'messages.common.expire';
    const COMMON_FROM_DATE = 'messages.common.from_date';
    const COMMON_TO_DATE = 'messages.common.to_date';
    const COMMON_LAST_CHANGE_BY = 'messages.common.last_change_by';
    const COMMON_ACTION = 'messages.common.action';
    const COMMON_SHOW = 'messages.common.show';
    const COMMON_ADD = 'messages.common.add';
    const COMMON_MANAGE = 'messages.common.manage';
    const COMMON_SELECT_ALL = 'messages.common.select_all';
    const COMMON_DESELECT_ALL = 'messages.common.deselect_all';
    const COMMON_CREATED_DATE = 'messages.common.created_date';
    const COMMON_UPDATED_DATE = 'messages.common.updated_date';
    const COMMON_FILTER = 'messages.common.filter';
    const COMMON_FILTERS = 'messages.common.filters';
    const COMMON_CLEAR_FILTERS = 'messages.common.clear_filters';
    const COMMON_EXPORT = 'messages.common.export';
    const COMMON_IMPORT = 'messages.common.import';
    const COMMON_PRINT = 'messages.common.print';
    const COMMON_REFRESH = 'messages.common.refresh';
    const COMMON_MORE = 'messages.common.more';
    const COMMON_LESS = 'messages.common.less';
    const COMMON_CLOSE = 'messages.common.close';
    const COMMON_CONFIRM = 'messages.common.confirm';
    const COMMON_APPROVE = 'messages.common.approve';
    const COMMON_REJECT = 'messages.common.reject';
    const COMMON_SUBMIT = 'messages.common.submit';
    const COMMON_DOWNLOAD = 'messages.common.download';
    const COMMON_UPLOAD = 'messages.common.upload';
    const COMMON_COPY = 'messages.common.copy';
    const COMMON_COPIED = 'messages.common.copied';
    const COMMON_SHARE = 'messages.common.share';
    const COMMON_DETAILS = 'messages.common.details';
    const COMMON_FIRST = 'messages.common.first';
    const COMMON_LAST = 'messages.common.last';
    const COMMON_NO_RECORDS_FOUND = 'messages.common.no_records_found';
    const COMMON_PROCESS = 'messages.common.process';
    
    // Flash messages
    const FLASH_NO_RECORD = 'messages.flash.no_record';
    const FLASH_CREATE_SUCCESS = 'messages.flash.create_success';
    const FLASH_UPDATE_SUCCESS = 'messages.flash.update_success';
    const FLASH_DELETE_SUCCESS = 'messages.flash.delete_success';
    const FLASH_DELETE_WARNING = 'messages.flash.delete_warning';
    const FLASH_DELETE_WARNING_MESSAGE = 'messages.flash.delete_warning_message';
    const FLASH_ERROR = 'messages.flash.error';
    const FLASH_ERROR_MESSAGE = 'messages.flash.error_message';
    const FLASH_SUCCESS = 'messages.flash.success';
    const FLASH_WARNING = 'messages.flash.warning';
    const FLASH_INFO = 'messages.flash.info';
    
    // Job translations
    const JOB_TITLE = 'messages.job.job_title';
    const JOB_IS_FEATURED = 'messages.job.is_featured';
    const JOB_IS_SUSPENDED = 'messages.job.is_suspended';
    const JOB_EXPIRY_DATE = 'messages.job.job_expiry_date';
    const JOB_TYPE = 'messages.job.job_type';
    const JOB_DESCRIPTION = 'messages.job.job_description';
    const JOB_REQUIREMENTS = 'messages.job.job_requirements';
    const JOB_LOCATION = 'messages.job.job_location';
    const JOB_SALARY = 'messages.job.job_salary';
    const JOB_POSITION = 'messages.job.job_position';
    const JOB_CATEGORY = 'messages.job.job_category';
    const JOB_STATUS = 'messages.job.job_status';
    const JOB_COMPANY = 'messages.job.job_company';
    const JOB_POSTED_DATE = 'messages.job.job_posted_date';
    const JOB_DEADLINE = 'messages.job.job_deadline';
    const JOB_SKILLS = 'messages.job.job_skills';
    const JOB_APPLICATIONS = 'messages.job.job_applications';
    const JOB_APPLICANTS = 'messages.job.job_applicants';
    const JOB_VACANCY = 'messages.job.job_vacancy';
    const JOB_PUBLISHED = 'messages.job.job_published';
    const JOB_DRAFT = 'messages.job.job_draft';
    const JOB_ARCHIVE = 'messages.job.job_archive';
    const JOB_APPLY = 'messages.job.job_apply';
    const JOB_SEARCH_JOBS = 'messages.job.search_jobs';
    const JOB_SIMILAR_JOBS = 'messages.job.similar_jobs';
    const JOB_DETAILS = 'messages.job.job_details';
    const JOB_NEW_JOB = 'messages.job.new_job';
    const JOB_EDIT_JOB = 'messages.job.edit_job';
    const JOB_KEY_RESPONSIBILITIES = 'messages.job.key_responsibilities';
    const JOB_SALARY_FROM = 'messages.job.salary_from';
    const JOB_SALARY_TO = 'messages.job.salary_to';
    const JOB_CURRENCY = 'messages.job.currency';
    const JOB_SALARY_PERIOD = 'messages.job.salary_period';
    const JOB_POSITION = 'messages.job.position';
    const JOB_HIDE_SALARY = 'messages.job.hide_salary';
    const JOB_IS_FREELANCE = 'messages.job.is_freelance';
    const JOB_DEGREE_LEVEL = 'messages.job.degree_level';
    const JOB_FUNCTIONAL_AREA = 'messages.job.functional_area';
    const JOB_CAREER_LEVEL = 'messages.job.career_level';
    const JOB_SHIFT = 'messages.job.job_shift';
    
    // Filter name translations
    const FILTER_FEATURED_JOB = 'messages.filter_name.featured_job';
    const FILTER_SELECT_FEATURED_COMPANY = 'messages.filter_name.select_featured_company';
    const FILTER_SUSPENDED_JOB = 'messages.filter_name.suspended_job';
    const FILTER_SELECT_SUSPENDED_JOB = 'messages.filter_name.select_suspended_job';
    const FILTER_SELECT_INDEPENDENT_WORK = 'messages.filter_name.select_independent_work';
    const FILTER_JOB_STATUS = 'messages.filter_name.job_status';
    const FILTER_COMPANY_STATUS = 'messages.filter_name.company_status';
    const FILTER_DATE_POSTED = 'messages.filter_name.date_posted';
    const FILTER_EXPERIENCE = 'messages.filter_name.experience';
    const FILTER_JOB_TYPE = 'messages.filter_name.job_type';
    const FILTER_SALARY_RANGE = 'messages.filter_name.salary_range';
    const FILTER_CAREER_LEVEL = 'messages.filter_name.career_level';
    const FILTER_FUNCTIONAL_AREA = 'messages.filter_name.functional_area';
    const FILTER_GENDER = 'messages.filter_name.gender';
    const FILTER_DEGREE_LEVEL = 'messages.filter_name.degree_level';
    const FILTER_INDUSTRY = 'messages.filter_name.industry';
    const FILTER_JOB_SHIFT = 'messages.filter_name.job_shift';
    const FILTER_SKILLS = 'messages.filter_name.skills';
    const FILTER_LANGUAGE = 'messages.filter_name.language';
    
    // Pagination translations
    const PAGINATION_PREVIOUS = 'messages.pagination.previous';
    const PAGINATION_NEXT = 'messages.pagination.next';
    const PAGINATION_SHOWING = 'messages.pagination.showing';
    const PAGINATION_TO = 'messages.pagination.to';
    const PAGINATION_OF = 'messages.pagination.of';
    const PAGINATION_RESULTS = 'messages.pagination.results';
    const PAGINATION_GO_TO_PAGE = 'messages.pagination.go_to_page';
    
    // Company translations
    const COMPANY_NAME = 'messages.company.company_name';
    const COMPANY_SELECT_COMPANY = 'messages.company.select_company';
    const COMPANY_SELECT_JOB_TYPE = 'messages.company.select_job_type';
    const COMPANY_SELECT_JOB_CATEGORY = 'messages.company.select_job_category';
    const COMPANY_CURRENT_PASSWORD = 'messages.company.current_password';
    const COMPANY_NEW_PASSWORD = 'messages.company.new_password';
    const COMPANY_CONFIRM_PASSWORD = 'messages.company.confirm_password';
    const COMPANY_SELECT_GENDER = 'messages.company.select_gender';
    const COMPANY_SELECT_CURRENCY = 'messages.company.select_currency';
    const COMPANY_SELECT_SALARY_PERIOD = 'messages.company.select_salary_period';
    const COMPANY_COUNTRY = 'messages.company.country';
    const COMPANY_SELECT_COUNTRY = 'messages.company.select_country';
    const COMPANY_STATE = 'messages.company.state';
    const COMPANY_SELECT_STATE = 'messages.company.select_state';
    const COMPANY_CITY = 'messages.company.city';
    const COMPANY_SELECT_CITY = 'messages.company.select_city';
    const COMPANY_SELECT_CAREER_LEVEL = 'messages.company.select_career_level';
    const COMPANY_SELECT_JOB_SHIFT = 'messages.company.select_job_shift';
    const COMPANY_SELECT_POSITION = 'messages.company.select_position';
    const COMPANY_SELECT_FUNCTIONAL_AREA = 'messages.company.select_functional_area';
    const COMPANY_SELECT_DEGREE_LEVEL = 'messages.company.select_degree_level';
    const COMPANY_ENTER_EXPERIENCE_YEAR = 'messages.company.enter_experience_year';
    
    // Other entity translations
    const CANDIDATE_GENDER = 'messages.candidate.gender';
    const JOB_CATEGORY_JOB_CATEGORY = 'messages.job_category.job_category';
    const JOB_TAG_SHOW_JOB_TAG = 'messages.job_tag.show_job_tag';
    const JOB_EXPERIENCE_JOB_EXPERIENCE = 'messages.job_experience.job_experience';
    const MARITAL_STATUS_MARITAL_STATUS = 'messages.marital_status.marital_status';
    const USER_CHANGE_PASSWORD = 'messages.user.change_password';

    // Common translation keys used throughout the application
    // Add more constants as needed for better IDE support and consistency
    public const HOME = 'app.home';
    public const DASHBOARD = 'app.dashboard';
    public const JOBS = 'app.jobs';
    public const COMPANIES = 'app.companies';
    public const CANDIDATES = 'app.candidates';
    public const PROFILE = 'app.profile';
    public const SETTINGS = 'app.settings';
    public const LOGIN = 'app.login';
    public const REGISTER = 'app.register';
    public const LOGOUT = 'app.logout';
    public const SEARCH = 'app.search';
    public const SAVE = 'app.save';
    public const EDIT = 'app.edit';
    public const DELETE = 'app.delete';
    public const CREATE = 'app.create';
    public const UPDATE = 'app.update';

    /**
     * Get all translations for the current locale
     *
     * @return array
     */
    public static function getAllTranslations(): array
    {
        $locale = App::getLocale();
        $filePath = resource_path("lang/{$locale}.php");
        
        if (File::exists($filePath)) {
            return require $filePath;
        }
        
        return [];
    }
    
    /**
     * Get missing translations for a specific locale compared to the fallback locale
     *
     * @param string|null $locale
     * @return array
     */
    public static function getMissingTranslations(?string $locale = null): array
    {
        $locale = $locale ?? App::getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        
        if ($locale === $fallbackLocale) {
            return [];
        }
        
        $localeFilePath = resource_path("lang/{$locale}.php");
        $fallbackFilePath = resource_path("lang/{$fallbackLocale}.php");
        
        if (!File::exists($localeFilePath) || !File::exists($fallbackFilePath)) {
            return [];
        }
        
        $localeTranslations = require $localeFilePath;
        $fallbackTranslations = require $fallbackFilePath;
        
        $flatLocale = static::flattenTranslations($localeTranslations);
        $flatFallback = static::flattenTranslations($fallbackTranslations);
        
        $missing = [];
        
        foreach ($flatFallback as $key => $value) {
            if (!isset($flatLocale[$key]) || (strpos($flatLocale[$key], '[REIKIA_IŠVERSTI]') === 0)) {
                $missing[$key] = $value;
            }
        }
        
        return $missing;
    }
    
    /**
     * Check if a translation key exists for the current locale
     *
     * @param string $key
     * @return bool
     */
    public static function hasTranslation(string $key): bool
    {
        $locale = App::getLocale();
        $filePath = resource_path("lang/{$locale}.php");
        
        if (!File::exists($filePath)) {
            return false;
        }
        
        $translations = require $filePath;
        $parts = explode('.', $key);
        
        $section = array_shift($parts);
        
        if (!isset($translations[$section])) {
            return false;
        }
        
        $sectionTranslations = $translations[$section];
        $remainingKey = implode('.', $parts);
        
        return Arr::has($sectionTranslations, $remainingKey);
    }
    
    /**
     * Get a translation for a key in the new format
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    public static function getTranslation(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? App::getLocale();
        $filePath = resource_path("lang/{$locale}.php");
        
        if (!File::exists($filePath)) {
            return $key;
        }
        
        $translations = require $filePath;
        $parts = explode('.', $key);
        
        if (count($parts) < 2) {
            return $key;
        }
        
        $section = array_shift($parts);
        
        if (!isset($translations[$section])) {
            return $key;
        }
        
        $sectionTranslations = $translations[$section];
        $remainingKey = implode('.', $parts);
        
        $value = Arr::get($sectionTranslations, $remainingKey, $key);
        
        // Replace placeholders
        foreach ($replace as $placeholder => $replacement) {
            $value = str_replace(":{$placeholder}", $replacement, $value);
        }
        
        return $value;
    }
    
    /**
     * Flatten translations array into dot notation
     *
     * @param array $translations
     * @param string $prefix
     * @return array
     */
    protected static function flattenTranslations(array $translations, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($translations as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, static::flattenTranslations($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Get a translation value.
     * First tries Laravel's standard trans() function, then falls back to direct file access if needed
     *
     * @param string $key The translation key
     * @param array $replace Replace placeholders in the translation string
     * @param string|null $locale Locale to use (defaults to current locale)
     * @return string The translated string
     */
    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();
        
        // Try Laravel's built-in translation function first
        $translation = trans($key, $replace, $locale);
        
        // If the key wasn't found (Laravel returns the key itself), try our direct file access
        if ($translation === $key) {
            // Memory efficient way to access translations - we only load what's needed
            $cacheKey = "translation:{$locale}:{$key}";
            
            return Cache::remember($cacheKey, now()->addDay(), function () use ($key, $locale, $replace) {
                // Parse the key (e.g., 'app.home' becomes ['app', 'home'])
                $parts = explode('.', $key);
                
                if (count($parts) < 2) {
                    return $key; // Not a valid key format
                }
                
                $file = resource_path("lang/{$locale}.php");
                
                if (File::exists($file)) {
                    $translations = require $file;
                    
                    // Navigate through the nested array
                    $segment = $translations;
                    
                    foreach ($parts as $part) {
                        if (isset($segment[$part])) {
                            $segment = $segment[$part];
                        } else {
                            return $key; // Key not found
                        }
                    }
                    
                    // If we reached a string value, return it with replacements
                    if (is_string($segment)) {
                        return static::applyReplacements($segment, $replace);
                    }
                }
                
                return $key; // Fallback to key if not found
            });
        }
        
        return $translation;
    }
    
    /**
     * Check if a translation exists.
     *
     * @param string $key The translation key
     * @param string|null $locale Locale to check
     * @return bool Whether the translation exists
     */
    public static function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?: App::getLocale();
        
        // Try Laravel's built-in translation function first
        if (trans()->has($key, $locale)) {
            return true;
        }
        
        // Memory efficient way to check translations
        $cacheKey = "translation_exists:{$locale}:{$key}";
        
        return Cache::remember($cacheKey, now()->addDay(), function () use ($key, $locale) {
            // Parse the key (e.g., 'app.home' becomes ['app', 'home'])
            $parts = explode('.', $key);
            
            if (count($parts) < 2) {
                return false; // Not a valid key format
            }
            
            $file = resource_path("lang/{$locale}.php");
            
            if (File::exists($file)) {
                $translations = require $file;
                
                // Navigate through the nested array
                $segment = $translations;
                
                foreach ($parts as $part) {
                    if (isset($segment[$part])) {
                        $segment = $segment[$part];
                    } else {
                        return false; // Key not found
                    }
                }
                
                return is_string($segment); // Must be a string to be a valid translation
            }
            
            return false;
        });
    }
    
    /**
     * Get all available locales from the lang directory.
     *
     * @return array Array of available locale codes
     */
    public static function getAvailableLocales(): array
    {
        return Cache::remember('available_locales', now()->addDay(), function () {
            $locales = [];
            $langPath = resource_path('lang');
            
            // Get PHP files (consolidated structure)
            if (File::exists($langPath)) {
                foreach (File::files($langPath) as $file) {
                    if ($file->getExtension() === 'php') {
                        $locales[] = $file->getFilenameWithoutExtension();
                    }
                }
            }
            
            return $locales;
        });
    }
    
    /**
     * Get locale display names for the available locales.
     *
     * @param string|null $locale The locale to get names in
     * @return array Associative array of locale codes => display names
     */
    public static function getLocaleDisplayNames(?string $locale = null): array
    {
        $locale = $locale ?: App::getLocale();
        
        $names = [
            'en' => 'English',
            'lt' => 'Lietuvių',
            // Add more as needed
        ];
        
        // Translate the names to the requested locale if possible
        if ($locale !== 'en') {
            $translatedNames = [];
            
            foreach ($names as $code => $name) {
                $translationKey = "languages.{$code}";
                $translated = static::get($translationKey, [], $locale);
                
                // If translation doesn't exist, use the original name
                $translatedNames[$code] = ($translated !== $translationKey) 
                    ? $translated 
                    : $name;
            }
            
            return $translatedNames;
        }
        
        return $names;
    }
    
    /**
     * Apply replacements to a translation string.
     *
     * @param string $line The translation string
     * @param array $replace Replacements
     * @return string The string with replacements applied
     */
    protected static function applyReplacements(string $line, array $replace): string
    {
        if (empty($replace)) {
            return $line;
        }
        
        foreach ($replace as $key => $value) {
            $line = str_replace(":{$key}", $value, $line);
        }
        
        return $line;
    }
} 