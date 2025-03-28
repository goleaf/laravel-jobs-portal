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
    public const COMMON_NO_RECORDS = 'common.no_records';
    public const COMMON_PER_PAGE = 'common.per_page';
    public const COMMON_FILTERS = 'common.filters';
    public const COMMON_SELECT = 'common.select';
    public const COMMON_LOADING = 'common.loading';
    public const COMMON_CONFIRMATION = 'common.confirmation';
    public const COMMON_CONFIRMATION_MESSAGE = 'common.confirmation_message';
    public const COMMON_STATUS = 'common.status';
    public const COMMON_SHOWING = 'common.showing';
    public const COMMON_TO = 'common.to';
    public const COMMON_OF = 'common.of';
    public const COMMON_RESULTS = 'common.results';
    public const COMMON_NO_RESULTS = 'common.no_results';
    public const COMMON_CREATED_ON = 'common.created_on';
    public const COMMON_UPDATED_ON = 'common.updated_on';
    public const COMMON_EXPIRE = 'common.expire';
    public const COMMON_FROM_DATE = 'common.from_date';
    public const COMMON_TO_DATE = 'common.to_date';
    public const COMMON_LAST_CHANGE_BY = 'common.last_change_by';
    public const COMMON_ACTION = 'common.action';
    public const COMMON_SHOW = 'common.show';
    public const COMMON_MANAGE = 'common.manage';
    public const COMMON_SELECT_ALL = 'common.select_all';
    public const COMMON_DESELECT_ALL = 'common.deselect_all';
    public const COMMON_CREATED_DATE = 'common.created_date';
    public const COMMON_UPDATED_DATE = 'common.updated_date';
    public const COMMON_FILTER = 'common.filter';
    public const COMMON_CLEAR_FILTERS = 'common.clear_filters';
    public const COMMON_EXPORT = 'common.export';
    public const COMMON_IMPORT = 'common.import';
    public const COMMON_PRINT = 'common.print';
    public const COMMON_REFRESH = 'common.refresh';
    public const COMMON_MORE = 'common.more';
    public const COMMON_LESS = 'common.less';
    public const COMMON_CLOSE = 'common.close';
    public const COMMON_CONFIRM = 'common.confirm';
    public const COMMON_APPROVE = 'common.approve';
    public const COMMON_REJECT = 'common.reject';
    public const COMMON_SUBMIT = 'common.submit';
    public const COMMON_DOWNLOAD = 'common.download';
    public const COMMON_UPLOAD = 'common.upload';
    public const COMMON_COPY = 'common.copy';
    public const COMMON_COPIED = 'common.copied';
    public const COMMON_SHARE = 'common.share';
    public const COMMON_DETAILS = 'common.details';
    public const COMMON_FIRST = 'common.first';
    public const COMMON_LAST = 'common.last';
    public const COMMON_NO_RECORDS_FOUND = 'common.no_records_found';
    public const COMMON_PROCESS = 'common.process';
    
    // Flash messages
    public const FLASH_NO_RECORD = 'flash.no_record';
    public const FLASH_CREATE_SUCCESS = 'flash.create_success';
    public const FLASH_UPDATE_SUCCESS = 'flash.update_success';
    public const FLASH_DELETE_SUCCESS = 'flash.delete_success';
    public const FLASH_DELETE_WARNING = 'flash.delete_warning';
    public const FLASH_DELETE_WARNING_MESSAGE = 'flash.delete_warning_message';
    public const FLASH_ERROR = 'flash.error';
    public const FLASH_ERROR_MESSAGE = 'flash.error_message';
    public const FLASH_SUCCESS = 'flash.success';
    public const FLASH_WARNING = 'flash.warning';
    public const FLASH_INFO = 'flash.info';
    
    // Job translations
    public const JOB_TITLE = 'job.job_title';
    public const JOB_IS_FEATURED = 'job.is_featured';
    public const JOB_IS_SUSPENDED = 'job.is_suspended';
    public const JOB_EXPIRY_DATE = 'job.job_expiry_date';
    public const JOB_TYPE = 'job.job_type';
    public const JOB_DESCRIPTION = 'job.job_description';
    public const JOB_REQUIREMENTS = 'job.job_requirements';
    public const JOB_LOCATION = 'job.job_location';
    public const JOB_SALARY = 'job.job_salary';
    public const JOB_POSITION = 'job.job_position';
    public const JOB_CATEGORY = 'job.job_category';
    public const JOB_STATUS = 'job.job_status';
    public const JOB_COMPANY = 'job.job_company';
    public const JOB_POSTED_DATE = 'job.job_posted_date';
    public const JOB_DEADLINE = 'job.job_deadline';
    public const JOB_SKILLS = 'job.job_skills';
    public const JOB_APPLICATIONS = 'job.job_applications';
    public const JOB_APPLICANTS = 'job.job_applicants';
    public const JOB_VACANCY = 'job.job_vacancy';
    public const JOB_PUBLISHED = 'job.job_published';
    public const JOB_DRAFT = 'job.job_draft';
    public const JOB_ARCHIVE = 'job.job_archive';
    public const JOB_APPLY = 'job.job_apply';
    public const JOB_SEARCH_JOBS = 'job.search_jobs';
    public const JOB_SIMILAR_JOBS = 'job.similar_jobs';
    public const JOB_DETAILS = 'job.job_details';
    public const JOB_NEW_JOB = 'job.new_job';
    public const JOB_EDIT_JOB = 'job.edit_job';
    public const JOB_KEY_RESPONSIBILITIES = 'job.key_responsibilities';
    public const JOB_SALARY_FROM = 'job.salary_from';
    public const JOB_SALARY_TO = 'job.salary_to';
    public const JOB_CURRENCY = 'job.currency';
    public const JOB_SALARY_PERIOD = 'job.salary_period';
    public const JOB_HIDE_SALARY = 'job.hide_salary';
    public const JOB_IS_FREELANCE = 'job.is_freelance';
    public const JOB_DEGREE_LEVEL = 'job.degree_level';
    public const JOB_FUNCTIONAL_AREA = 'job.functional_area';
    public const JOB_CAREER_LEVEL = 'job.career_level';
    public const JOB_SHIFT = 'job.job_shift';
    
    // Filter name translations
    public const FILTER_FEATURED_JOB = 'filter.featured_job';
    public const FILTER_SELECT_FEATURED_COMPANY = 'filter.select_featured_company';
    public const FILTER_SUSPENDED_JOB = 'filter.suspended_job';
    public const FILTER_SELECT_SUSPENDED_JOB = 'filter.select_suspended_job';
    public const FILTER_SELECT_INDEPENDENT_WORK = 'filter.select_independent_work';
    public const FILTER_JOB_STATUS = 'filter.job_status';
    public const FILTER_COMPANY_STATUS = 'filter.company_status';
    public const FILTER_DATE_POSTED = 'filter.date_posted';
    public const FILTER_EXPERIENCE = 'filter.experience';
    public const FILTER_JOB_TYPE = 'filter.job_type';
    public const FILTER_SALARY_RANGE = 'filter.salary_range';
    public const FILTER_CAREER_LEVEL = 'filter.career_level';
    public const FILTER_FUNCTIONAL_AREA = 'filter.functional_area';
    public const FILTER_GENDER = 'filter.gender';
    public const FILTER_DEGREE_LEVEL = 'filter.degree_level';
    public const FILTER_INDUSTRY = 'filter.industry';
    public const FILTER_JOB_SHIFT = 'filter.job_shift';
    public const FILTER_SKILLS = 'filter.skills';
    public const FILTER_LANGUAGE = 'filter.language';
    
    // Pagination translations
    public const PAGINATION_PREVIOUS = 'pagination.previous';
    public const PAGINATION_NEXT = 'pagination.next';
    public const PAGINATION_SHOWING = 'pagination.showing';
    public const PAGINATION_TO = 'pagination.to';
    public const PAGINATION_OF = 'pagination.of';
    public const PAGINATION_RESULTS = 'pagination.results';
    public const PAGINATION_GO_TO_PAGE = 'pagination.go_to_page';
    
    // Company translations
    public const COMPANY_NAME = 'company.company_name';
    public const COMPANY_SELECT_COMPANY = 'company.select_company';
    public const COMPANY_SELECT_JOB_TYPE = 'company.select_job_type';
    public const COMPANY_SELECT_JOB_CATEGORY = 'company.select_job_category';
    public const COMPANY_CURRENT_PASSWORD = 'company.current_password';
    public const COMPANY_NEW_PASSWORD = 'company.new_password';
    public const COMPANY_CONFIRM_PASSWORD = 'company.confirm_password';
    public const COMPANY_SELECT_GENDER = 'company.select_gender';
    public const COMPANY_SELECT_CURRENCY = 'company.select_currency';
    public const COMPANY_SELECT_SALARY_PERIOD = 'company.select_salary_period';
    public const COMPANY_COUNTRY = 'company.country';
    public const COMPANY_SELECT_COUNTRY = 'company.select_country';
    public const COMPANY_STATE = 'company.state';
    public const COMPANY_SELECT_STATE = 'company.select_state';
    public const COMPANY_CITY = 'company.city';
    public const COMPANY_SELECT_CITY = 'company.select_city';
    public const COMPANY_SELECT_CAREER_LEVEL = 'company.select_career_level';
    public const COMPANY_SELECT_JOB_SHIFT = 'company.select_job_shift';
    public const COMPANY_SELECT_POSITION = 'company.select_position';
    public const COMPANY_SELECT_FUNCTIONAL_AREA = 'company.select_functional_area';
    public const COMPANY_SELECT_DEGREE_LEVEL = 'company.select_degree_level';
    public const COMPANY_ENTER_EXPERIENCE_YEAR = 'company.enter_experience_year';
    
    // Other entity translations
    public const CANDIDATE_GENDER = 'candidate.gender';
    public const JOB_CATEGORY_JOB_CATEGORY = 'job_category.job_category';
    public const JOB_TAG_SHOW_JOB_TAG = 'job_tag.show_job_tag';
    public const JOB_EXPERIENCE_JOB_EXPERIENCE = 'job_experience.job_experience';
    public const MARITAL_STATUS_MARITAL_STATUS = 'marital_status.marital_status';
    public const USER_CHANGE_PASSWORD = 'user.change_password';

    // Common translation keys used throughout the application
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
     */
    public static function getAllTranslations(): array
    {
        $locale = App::getLocale();
        $cacheKey = "translations_{$locale}_all";
        
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($locale) {
            $translations = [];
            $langPath = resource_path('lang/' . $locale);
            
            if (File::exists($langPath)) {
                foreach (File::allFiles($langPath) as $file) {
                    if ($file->getExtension() === 'php') {
                        $fileName = $file->getBasename('.php');
                        $translations[$fileName] = require $file->getPathname();
                    }
                }
            }
            
            // Handle JSON translations
            $jsonPath = resource_path('lang/' . $locale . '.json');
            if (File::exists($jsonPath)) {
                $jsonTranslations = json_decode(File::get($jsonPath), true);
                if ($jsonTranslations) {
                    $translations['json'] = $jsonTranslations;
                }
            }
            
            return static::flattenTranslations($translations);
        });
    }
    
    /**
     * Get missing translations for a specific locale compared to the fallback locale
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
            if (!isset($flatLocale[$key]) || (strpos($flatLocale[$key], '[TRANSLATION_NEEDED]') === 0)) {
                $missing[$key] = $value;
            }
        }
        
        return $missing;
    }
    
    /**
     * Check if a translation key exists for the current locale
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
     * Get a translation for a key in the standardized format
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
     * Get a translation value following the standardized format
     */
    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();
        
        // Standardize the key format (e.g., convert legacy 'messages.common.search' to 'common.search')
        $standardizedKey = static::standardizeTranslationKey($key);
        
        // Try Laravel's built-in translation function with the standardized key
        $translation = trans($standardizedKey, $replace, $locale);
        
        // If the key wasn't found (Laravel returns the key itself), try our direct file access
        if ($translation === $standardizedKey) {
            // Memory efficient way to access translations - we only load what's needed
            $cacheKey = "translation:{$locale}:{$standardizedKey}";
            
            return Cache::remember($cacheKey, now()->addDay(), function () use ($standardizedKey, $locale, $replace) {
                // Parse the key (e.g., 'app.home' becomes ['app', 'home'])
                $parts = explode('.', $standardizedKey);
                
                if (count($parts) < 2) {
                    return $standardizedKey; // Not a valid key format
                }
                
                $file = resource_path("lang/{$locale}.php");
                
                if (File::exists($file)) {
                    $translations = require $file;
                    
                    // Navigate through the nested array
                    $section = $parts[0];
                    $key = $parts[1];
                    
                    if (isset($translations[$section][$key])) {
                        $value = $translations[$section][$key];
                        
                        // Apply replacements
                        foreach ($replace as $placeholder => $replacement) {
                            $value = str_replace(":{$placeholder}", $replacement, $value);
                        }
                        
                        return $value;
                    }
                }
                
                return $standardizedKey; // Fallback to key if not found
            });
        }
        
        return $translation;
    }
    
    /**
     * Standardize a translation key to the new format
     */
    protected static function standardizeTranslationKey(string $key): string
    {
        // Convert legacy 'messages.common.search' to 'common.search'
        if (strpos($key, 'messages.common.') === 0) {
            return substr_replace($key, 'common.', 0, strlen('messages.common.'));
        }
        
        // Convert other legacy formats as needed
        $legacyPrefixes = [
            'messages.job.' => 'job.',
            'messages.filter_name.' => 'filter.',
            'messages.company.' => 'company.',
            'messages.pagination.' => 'pagination.',
            'messages.candidate.' => 'candidate.',
            'messages.flash.' => 'flash.',
        ];
        
        foreach ($legacyPrefixes as $legacy => $new) {
            if (strpos($key, $legacy) === 0) {
                return substr_replace($key, $new, 0, strlen($legacy));
            }
        }
        
        return $key;
    }
} 