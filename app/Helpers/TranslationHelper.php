<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
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
    const COMMON_PER_PAGE = 'messages.common.per_page';
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
    const COMMON_SELECT = 'messages.common.select';
    const COMMON_SELECT_ALL = 'messages.common.select_all';
    const COMMON_DESELECT_ALL = 'messages.common.deselect_all';
    const COMMON_CREATED_DATE = 'messages.common.created_date';
    const COMMON_UPDATED_DATE = 'messages.common.updated_date';
    const COMMON_APPLY = 'messages.common.apply';
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
    const COMMON_NEXT = 'messages.common.next';
    const COMMON_PREVIOUS = 'messages.common.previous';
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
    const JOB_DESCRIPTION = 'messages.job.description';
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
} 