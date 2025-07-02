<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class SkillSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Based on user requirements: no auth system
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string|ValidationRule>
     */
    public function rules(): array
    {
        return [
            // Core search parameters
            'query' => [
                'sometimes',
                'string',
                'max:500',
                'min:2',
                function ($attribute, $value, $fail) {
                    if ($this->containsInappropriateContent($value)) {
                        $fail(__('validation.inappropriate_search_content'));
                    }
                },
            ],

            'keywords' => [
                'sometimes',
                'array',
                'max:50',
            ],

            'keywords.*' => [
                'string',
                'max:100',
                'min:2',
                'regex:/^[a-zA-Z0-9\s\-\+\#\.\/]+$/',
            ],

            'search_type' => [
                'sometimes',
                'string',
                Rule::in(['exact', 'fuzzy', 'semantic', 'phonetic', 'advanced', 'ai_powered']),
            ],

            'search_mode' => [
                'sometimes',
                'string',
                Rule::in(['skills_only', 'candidates', 'jobs', 'combined', 'market_analysis']),
            ],

            // Skill categories and taxonomy
            'skill_categories' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'skill_categories.*' => [
                'integer',
                'exists:skill_categories,id',
            ],

            'skill_types' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'skill_types.*' => [
                'string',
                Rule::in([
                    'technical',
                    'soft',
                    'leadership',
                    'analytical',
                    'creative',
                    'communication',
                    'problem_solving',
                    'project_management',
                    'industry_specific',
                    'language',
                    'certification',
                    'tool_proficiency',
                    'framework',
                    'methodology'
                ]),
            ],

            'skill_domains' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'skill_domains.*' => [
                'string',
                Rule::in([
                    'programming',
                    'design',
                    'marketing',
                    'sales',
                    'finance',
                    'operations',
                    'hr',
                    'engineering',
                    'data_science',
                    'cybersecurity',
                    'cloud_computing',
                    'ai_ml',
                    'blockchain',
                    'mobile_development',
                    'web_development',
                    'devops',
                    'quality_assurance',
                    'product_management',
                    'business_analysis',
                    'digital_transformation'
                ]),
            ],

            // Proficiency and experience levels
            'proficiency_levels' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'proficiency_levels.*' => [
                'string',
                Rule::in(['beginner', 'novice', 'intermediate', 'advanced', 'expert', 'master']),
            ],

            'min_proficiency' => [
                'sometimes',
                'string',
                Rule::in(['beginner', 'novice', 'intermediate', 'advanced', 'expert', 'master']),
            ],

            'max_proficiency' => [
                'sometimes',
                'string',
                Rule::in(['beginner', 'novice', 'intermediate', 'advanced', 'expert', 'master']),
            ],

            'experience_years' => [
                'sometimes',
                'array',
                'size:2',
            ],

            'experience_years.min' => [
                'integer',
                'min:0',
                'max:50',
            ],

            'experience_years.max' => [
                'integer',
                'min:0',
                'max:50',
                'gte:experience_years.min',
            ],

            'certification_required' => [
                'sometimes',
                'boolean',
            ],

            'certifications' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'certifications.*' => [
                'string',
                'max:200',
            ],

            // Geographic and location filtering
            'locations' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'locations.*.country' => [
                'string',
                'size:2',
                'regex:/^[A-Z]{2}$/',
            ],

            'locations.*.state' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'locations.*.city' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s\-\'\.]+$/',
            ],

            'locations.*.radius' => [
                'sometimes',
                'integer',
                'min:1',
                'max:500', // 500 km maximum
            ],

            'remote_work' => [
                'sometimes',
                'string',
                Rule::in(['required', 'preferred', 'available', 'not_available']),
            ],

            'timezone_preference' => [
                'sometimes',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, timezone_identifiers_list())) {
                        $fail(__('validation.invalid_timezone'));
                    }
                },
            ],

            // Industry and company filtering
            'industries' => [
                'sometimes',
                'array',
                'max:25',
            ],

            'industries.*' => [
                'integer',
                'exists:categories,id',
            ],

            'company_sizes' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'company_sizes.*' => [
                'string',
                Rule::in(['startup', 'small', 'medium', 'large', 'enterprise', 'corporation']),
            ],

            'company_types' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'company_types.*' => [
                'string',
                Rule::in([
                    'public',
                    'private',
                    'nonprofit',
                    'government',
                    'startup',
                    'scale_up',
                    'consulting',
                    'agency',
                    'product',
                    'service',
                    'b2b',
                    'b2c',
                    'saas',
                    'fintech',
                    'healthtech'
                ]),
            ],

            // Salary and compensation
            'salary_range' => [
                'sometimes',
                'array',
                'size:2',
            ],

            'salary_range.min' => [
                'numeric',
                'min:0',
                'max:10000000',
            ],

            'salary_range.max' => [
                'numeric',
                'min:0',
                'max:10000000',
                'gte:salary_range.min',
            ],

            'currency' => [
                'sometimes',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/',
            ],

            'salary_type' => [
                'sometimes',
                'string',
                Rule::in(['hourly', 'daily', 'weekly', 'monthly', 'annually']),
            ],

            'equity_offered' => [
                'sometimes',
                'boolean',
            ],

            'benefits_required' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'benefits_required.*' => [
                'string',
                'max:100',
            ],

            // Advanced matching algorithms
            'matching_algorithm' => [
                'sometimes',
                'string',
                Rule::in(['cosine_similarity', 'jaccard_index', 'neural_network', 'hybrid', 'machine_learning']),
            ],

            'similarity_threshold' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],

            'skill_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],

            'experience_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],

            'location_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],

            'education_weight' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],

            // Machine learning features
            'enable_ml_matching' => [
                'sometimes',
                'boolean',
            ],

            'ml_model_version' => [
                'sometimes',
                'string',
                'max:50',
                'regex:/^v\d+\.\d+\.\d+$/',
            ],

            'training_data_cutoff' => [
                'sometimes',
                'date',
                'before_or_equal:today',
            ],

            'prediction_confidence' => [
                'sometimes',
                'numeric',
                'min:0',
                'max:1',
            ],

            'use_collaborative_filtering' => [
                'sometimes',
                'boolean',
            ],

            'content_based_filtering' => [
                'sometimes',
                'boolean',
            ],

            // Skill gap analysis
            'analyze_skill_gaps' => [
                'sometimes',
                'boolean',
            ],

            'target_role' => [
                'sometimes',
                'string',
                'max:200',
            ],

            'target_level' => [
                'sometimes',
                'string',
                Rule::in(['junior', 'mid', 'senior', 'lead', 'principal', 'director', 'vp', 'c_level']),
            ],

            'career_path' => [
                'sometimes',
                'string',
                'max:200',
            ],

            'skill_development_priority' => [
                'sometimes',
                'string',
                Rule::in(['high_demand', 'emerging_tech', 'career_advancement', 'salary_impact', 'custom']),
            ],

            'learning_time_available' => [
                'sometimes',
                'integer',
                'min:1',
                'max:2000', // hours
            ],

            // Market analysis and trends
            'include_market_trends' => [
                'sometimes',
                'boolean',
            ],

            'trend_analysis_period' => [
                'sometimes',
                'string',
                Rule::in(['3_months', '6_months', '1_year', '2_years', '5_years']),
            ],

            'demand_forecast' => [
                'sometimes',
                'boolean',
            ],

            'supply_analysis' => [
                'sometimes',
                'boolean',
            ],

            'salary_trends' => [
                'sometimes',
                'boolean',
            ],

            'emerging_skills' => [
                'sometimes',
                'boolean',
            ],

            'skill_obsolescence' => [
                'sometimes',
                'boolean',
            ],

            // Competency mapping
            'competency_framework' => [
                'sometimes',
                'string',
                Rule::in(['custom', 'o_net', 'sfia', 'itil', 'agile', 'safe', 'pmbok']),
            ],

            'competency_levels' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'competency_levels.*' => [
                'string',
                'max:100',
            ],

            'behavioral_competencies' => [
                'sometimes',
                'array',
                'max:15',
            ],

            'behavioral_competencies.*' => [
                'string',
                'max:150',
            ],

            'technical_competencies' => [
                'sometimes',
                'array',
                'max:30',
            ],

            'technical_competencies.*' => [
                'string',
                'max:150',
            ],

            // Filtering and sorting options
            'filters' => [
                'sometimes',
                'array',
                'max:20',
            ],

            'filters.availability' => [
                'sometimes',
                'string',
                Rule::in(['immediate', 'within_2_weeks', 'within_month', 'within_3_months', 'not_looking']),
            ],

            'filters.employment_type' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'filters.employment_type.*' => [
                'string',
                Rule::in(['full_time', 'part_time', 'contract', 'freelance', 'internship', 'temporary']),
            ],

            'filters.education_level' => [
                'sometimes',
                'array',
                'max':8,
            ],

            'filters.education_level.*' => [
                'string',
                Rule::in(['high_school', 'associate', 'bachelor', 'master', 'phd', 'certification', 'bootcamp', 'self_taught']),
            ],

            'filters.languages' => [
                'sometimes',
                'array',
                'max:10,
            ],

            'filters.languages.*' => [
                'string',
                'size:2',
                'regex:/^[a-z]{2}$/',
            ],

            'filters.last_active' => [
                'sometimes',
                'string',
                Rule::in(['24_hours', '7_days', '30_days', '90_days', '6_months', '1_year']),
            ],

            // Pagination and results
            'page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:1000',
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:5',
                'max':500,
            ],

            'sort_by' => [
                'sometimes',
                'string',
                Rule::in([
                    'relevance',
                    'match_score',
                    'experience',
                    'rating',
                    'last_active',
                    'salary',
                    'location',
                    'skills_count',
                    'endorsements',
                    'popularity',
                    'alphabetical'
                ]),
            ],

            'sort_order' => [
                'sometimes',
                'string',
                Rule::in(['asc', 'desc']),
            ],

            'group_by' => [
                'sometimes',
                'string',
                Rule::in(['skill_category', 'proficiency', 'location', 'industry', 'experience_level', 'none']),
            ],

            // Export and reporting
            'export_format' => [
                'sometimes',
                'string',
                Rule::in(['json', 'csv', 'excel', 'pdf', 'xml']),
            ],

            'include_analytics' => [
                'sometimes',
                'boolean',
            ],

            'include_recommendations' => [
                'sometimes',
                'boolean',
            ],

            'include_skill_matrix' => [
                'sometimes',
                'boolean',
            ],

            'include_gaps' => [
                'sometimes',
                'boolean',
            ],

            'report_type' => [
                'sometimes',
                'string',
                Rule::in(['summary', 'detailed', 'executive', 'technical', 'comparative']),
            ],

            // Performance and caching
            'use_cache' => [
                'sometimes',
                'boolean',
            ],

            'cache_ttl' => [
                'sometimes',
                'integer',
                'min:60',
                'max:86400', // 24 hours
            ],

            'optimize_query' => [
                'sometimes',
                'boolean',
            ],

            'parallel_processing' => [
                'sometimes',
                'boolean',
            ],

            'index_hints' => [
                'sometimes',
                'array',
                'max:5',
            ],

            'index_hints.*' => [
                'string',
                'max:100',
            ],

            // Real-time features
            'real_time_updates' => [
                'sometimes',
                'boolean',
            ],

            'websocket_channel' => [
                'sometimes',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\-_\.]+$/',
            ],

            'live_search' => [
                'sometimes',
                'boolean',
            ],

            'debounce_ms' => [
                'sometimes',
                'integer',
                'min:100',
                'max:5000',
            ],

            // Custom scoring and ranking
            'custom_scoring' => [
                'sometimes',
                'array',
                'max:10',
            ],

            'custom_scoring.*.metric' => [
                'string',
                'max:100',
            ],

            'custom_scoring.*.weight' => [
                'numeric',
                'min:0',
                'max:1',
            ],

            'custom_scoring.*.boost' => [
                'numeric',
                'min':0.1,
                'max':10,
            ],

            'boost_factors' => [
                'sometimes',
                'array',
                'max:15,
            ],

            'boost_factors.premium_candidates' => [
                'sometimes',
                'numeric',
                'min':1,
                'max':5,
            ],

            'boost_factors.verified_skills' => [
                'sometimes',
                'numeric',
                'min':1,
                'max':3,
            ],

            'boost_factors.recent_projects' => [
                'sometimes',
                'numeric',
                'min':1,
                'max':2,
            ],

            'boost_factors.high_ratings' => [
                'sometimes',
                'numeric',
                'min':1,
                'max':2,
            ],

            // Privacy and security
            'anonymize_results' => [
                'sometimes',
                'boolean',
            ],

            'gdpr_compliant' => [
                'sometimes',
                'boolean',
            ],

            'data_minimization' => [
                'sometimes',
                'boolean',
            ],

            'consent_levels' => [
                'sometimes',
                'array',
                'max':5,
            ],

            'consent_levels.*' => [
                'string',
                Rule::in(['basic', 'profile', 'contact', 'portfolio', 'recommendations']),
            ],

            // API and integration
            'api_version' => [
                'sometimes',
                'string',
                Rule::in(['v1', 'v2', 'v3', 'latest']),
            ],

            'response_format' => [
                'sometimes',
                'string',
                Rule::in(['minimal', 'standard', 'detailed', 'comprehensive']),
            ],

            'include_metadata' => [
                'sometimes',
                'boolean',
            ],

            'webhook_url' => [
                'sometimes',
                'url',
                'max':500,
            ],

            'callback_params' => [
                'sometimes',
                'array',
                'max':10,
            ],

            'callback_params.*' => [
                'string',
                'max':255,
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'query.min' => __('validation.search_query_too_short'),
            'query.max' => __('validation.search_query_too_long'),
            'keywords.max' => __('validation.too_many_keywords'),
            'keywords.*.min' => __('validation.keyword_too_short'),
            'keywords.*.max' => __('validation.keyword_too_long'),
            'keywords.*.regex' => __('validation.invalid_keyword_format'),
            'skill_categories.max' => __('validation.too_many_skill_categories'),
            'skill_types.max' => __('validation.too_many_skill_types'),
            'proficiency_levels.max' => __('validation.too_many_proficiency_levels'),
            'experience_years.min.min' => __('validation.experience_years_negative'),
            'experience_years.max.max' => __('validation.experience_years_too_high'),
            'experience_years.max.gte' => __('validation.experience_range_invalid'),
            'locations.max' => __('validation.too_many_locations'),
            'locations.*.country.regex' => __('validation.invalid_country_code'),
            'locations.*.city.regex' => __('validation.invalid_city_name'),
            'locations.*.radius.max' => __('validation.search_radius_too_large'),
            'industries.max' => __('validation.too_many_industries'),
            'salary_range.max.gte' => __('validation.salary_range_invalid'),
            'currency.regex' => __('validation.invalid_currency_code'),
            'similarity_threshold.min' => __('validation.similarity_threshold_too_low'),
            'similarity_threshold.max' => __('validation.similarity_threshold_too_high'),
            'ml_model_version.regex' => __('validation.invalid_model_version_format'),
            'training_data_cutoff.before_or_equal' => __('validation.training_data_future_not_allowed'),
            'prediction_confidence.min' => __('validation.confidence_too_low'),
            'prediction_confidence.max' => __('validation.confidence_too_high'),
            'learning_time_available.max' => __('validation.learning_time_too_high'),
            'filters.languages.*.regex' => __('validation.invalid_language_code'),
            'per_page.min' => __('validation.per_page_too_small'),
            'per_page.max' => __('validation.per_page_too_large'),
            'cache_ttl.min' => __('validation.cache_ttl_too_short'),
            'cache_ttl.max' => __('validation.cache_ttl_too_long'),
            'websocket_channel.regex' => __('validation.invalid_websocket_channel'),
            'debounce_ms.min' => __('validation.debounce_too_short'),
            'debounce_ms.max' => __('validation.debounce_too_long'),
            'custom_scoring.max' => __('validation.too_many_scoring_metrics'),
            'boost_factors.premium_candidates.max' => __('validation.boost_factor_too_high'),
            'consent_levels.max' => __('validation.too_many_consent_levels'),
            'webhook_url.url' => __('validation.invalid_webhook_url'),
            'callback_params.max' => __('validation.too_many_callback_params'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'query' => __('validation.attributes.search_query'),
            'keywords' => __('validation.attributes.keywords'),
            'search_type' => __('validation.attributes.search_type'),
            'search_mode' => __('validation.attributes.search_mode'),
            'skill_categories' => __('validation.attributes.skill_categories'),
            'skill_types' => __('validation.attributes.skill_types'),
            'proficiency_levels' => __('validation.attributes.proficiency_levels'),
            'experience_years' => __('validation.attributes.experience_years'),
            'certifications' => __('validation.attributes.certifications'),
            'locations' => __('validation.attributes.locations'),
            'remote_work' => __('validation.attributes.remote_work'),
            'industries' => __('validation.attributes.industries'),
            'salary_range' => __('validation.attributes.salary_range'),
            'currency' => __('validation.attributes.currency'),
            'matching_algorithm' => __('validation.attributes.matching_algorithm'),
            'similarity_threshold' => __('validation.attributes.similarity_threshold'),
            'ml_model_version' => __('validation.attributes.ml_model_version'),
            'target_role' => __('validation.attributes.target_role'),
            'competency_framework' => __('validation.attributes.competency_framework'),
            'sort_by' => __('validation.attributes.sort_by'),
            'sort_order' => __('validation.attributes.sort_order'),
            'export_format' => __('validation.attributes.export_format'),
            'cache_ttl' => __('validation.attributes.cache_ttl'),
            'websocket_channel' => __('validation.attributes.websocket_channel'),
            'api_version' => __('validation.attributes.api_version'),
            'response_format' => __('validation.attributes.response_format'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set intelligent defaults
        if (!$this->has('search_type')) {
            $this->merge(['search_type' => 'advanced']);
        }

        if (!$this->has('search_mode')) {
            $this->merge(['search_mode' => 'combined']);
        }

        if (!$this->has('matching_algorithm')) {
            $this->merge(['matching_algorithm' => 'hybrid']);
        }

        if (!$this->has('similarity_threshold')) {
            $this->merge(['similarity_threshold' => 0.7]);
        }

        if (!$this->has('currency')) {
            $this->merge(['currency' => config('app.currency', 'USD')]);
        }

        if (!$this->has('salary_type')) {
            $this->merge(['salary_type' => 'annually']);
        }

        if (!$this->has('sort_by')) {
            $this->merge(['sort_by' => 'relevance']);
        }

        if (!$this->has('sort_order')) {
            $this->merge(['sort_order' => 'desc']);
        }

        if (!$this->has('per_page')) {
            $this->merge(['per_page' => 25]);
        }

        if (!$this->has('page')) {
            $this->merge(['page' => 1]);
        }

        if (!$this->has('api_version')) {
            $this->merge(['api_version' => 'latest']);
        }

        if (!$this->has('response_format')) {
            $this->merge(['response_format' => 'standard']);
        }

        if (!$this->has('cache_ttl')) {
            $this->merge(['cache_ttl' => 1800]); // 30 minutes
        }

        if (!$this->has('debounce_ms')) {
            $this->merge(['debounce_ms' => 300]);
        }

        if (!$this->has('competency_framework')) {
            $this->merge(['competency_framework' => 'custom']);
        }

        if (!$this->has('trend_analysis_period')) {
            $this->merge(['trend_analysis_period' => '1_year']);
        }

        if (!$this->has('skill_development_priority')) {
            $this->merge(['skill_development_priority' => 'high_demand']);
        }

        if (!$this->has('report_type')) {
            $this->merge(['report_type' => 'summary']);
        }

        // Enable intelligent features by default for advanced searches
        if ($this->input('search_type') === 'ai_powered' || $this->input('search_type') === 'advanced') {
            $defaultFeatures = [
                'enable_ml_matching' => true,
                'use_collaborative_filtering' => true,
                'content_based_filtering' => true,
                'include_analytics' => true,
                'include_recommendations' => true,
                'optimize_query' => true,
            ];

            foreach ($defaultFeatures as $key => $defaultValue) {
                if (!$this->has($key)) {
                    $this->merge([$key => $defaultValue]);
                }
            }
        }

        // Set performance optimizations for large searches
        if ($this->input('per_page') > 100 || $this->input('search_mode') === 'market_analysis') {
            $performanceSettings = [
                'use_cache' => true,
                'parallel_processing' => true,
                'optimize_query' => true,
            ];

            foreach ($performanceSettings as $key => $defaultValue) {
                if (!$this->has($key)) {
                    $this->merge([$key => $defaultValue]);
                }
            }
        }

        // Convert string booleans to actual booleans
        $booleanFields = [
            'certification_required', 'equity_offered', 'enable_ml_matching',
            'use_collaborative_filtering', 'content_based_filtering', 'analyze_skill_gaps',
            'include_market_trends', 'demand_forecast', 'supply_analysis', 'salary_trends',
            'emerging_skills', 'skill_obsolescence', 'include_analytics', 'include_recommendations',
            'include_skill_matrix', 'include_gaps', 'use_cache', 'optimize_query',
            'parallel_processing', 'real_time_updates', 'live_search', 'anonymize_results',
            'gdpr_compliant', 'data_minimization', 'include_metadata'
        ];

        foreach ($booleanFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => filter_var($this->input($field), FILTER_VALIDATE_BOOLEAN)
                ]);
            }
        }

        // Ensure arrays are properly formatted
        $arrayFields = [
            'keywords', 'skill_categories', 'skill_types', 'skill_domains', 'proficiency_levels',
            'certifications', 'industries', 'company_sizes', 'company_types', 'benefits_required',
            'competency_levels', 'behavioral_competencies', 'technical_competencies',
            'index_hints', 'consent_levels', 'callback_params'
        ];

        foreach ($arrayFields as $field) {
            if ($this->has($field) && !is_array($this->input($field))) {
                $this->merge([
                    $field => array_filter(explode(',', $this->input($field)))
                ]);
            }
        }

        // Set default skill weights for balanced matching
        $weightFields = ['skill_weight', 'experience_weight', 'location_weight', 'education_weight'];
        $defaultWeights = [0.4, 0.3, 0.2, 0.1]; // Total = 1.0

        foreach ($weightFields as $index => $field) {
            if (!$this->has($field)) {
                $this->merge([$field => $defaultWeights[$index]]);
            }
        }

        // Set default boost factors
        if (!$this->has('boost_factors')) {
            $this->merge(['boost_factors' => [
                'premium_candidates' => 1.2,
                'verified_skills' => 1.3,
                'recent_projects' => 1.1,
                'high_ratings' => 1.15,
            ]]);
        }

        // Set default ML model version
        if ($this->input('enable_ml_matching') && !$this->has('ml_model_version')) {
            $this->merge(['ml_model_version' => 'v2.1.0']);
        }

        // Auto-enable GDPR compliance for EU searches
        if ($this->hasEuropeanLocations() && !$this->has('gdpr_compliant')) {
            $this->merge(['gdpr_compliant' => true]);
        }

        // Log skill search request for analytics
        Log::info('Skill search request prepared', [
            'search_type' => $this->input('search_type'),
            'search_mode' => $this->input('search_mode'),
            'query_provided' => !empty($this->input('query')),
            'keywords_count' => count($this->input('keywords', [])),
            'skill_categories_count' => count($this->input('skill_categories', [])),
            'ml_enabled' => $this->input('enable_ml_matching', false),
            'market_analysis' => $this->input('include_market_trends', false),
            'skill_gap_analysis' => $this->input('analyze_skill_gaps', false),
            'real_time_enabled' => $this->input('real_time_updates', false),
            'gdpr_compliant' => $this->input('gdpr_compliant', false),
            'ip_address' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        // Log successful validation with comprehensive metrics
        Log::info('Skill search request validated successfully', [
            'search_parameters' => [
                'type' => $this->input('search_type'),
                'mode' => $this->input('search_mode'),
                'algorithm' => $this->input('matching_algorithm'),
                'similarity_threshold' => $this->input('similarity_threshold'),
                'sort_criteria' => $this->input('sort_by') . '_' . $this->input('sort_order'),
            ],
            'filtering_applied' => [
                'skill_categories' => count($this->input('skill_categories', [])),
                'skill_types' => count($this->input('skill_types', [])),
                'locations' => count($this->input('locations', [])),
                'industries' => count($this->input('industries', [])),
                'proficiency_filters' => !empty($this->input('proficiency_levels')),
                'salary_range' => !empty($this->input('salary_range')),
            ],
            'advanced_features' => [
                'ml_matching' => $this->input('enable_ml_matching', false),
                'collaborative_filtering' => $this->input('use_collaborative_filtering', false),
                'skill_gap_analysis' => $this->input('analyze_skill_gaps', false),
                'market_trends' => $this->input('include_market_trends', false),
                'competency_mapping' => !empty($this->input('competency_framework')),
            ],
            'performance_settings' => [
                'caching_enabled' => $this->input('use_cache', false),
                'parallel_processing' => $this->input('parallel_processing', false),
                'query_optimization' => $this->input('optimize_query', false),
                'real_time_updates' => $this->input('real_time_updates', false),
            ],
            'privacy_compliance' => [
                'gdpr_compliant' => $this->input('gdpr_compliant', false),
                'data_minimization' => $this->input('data_minimization', false),
                'anonymize_results' => $this->input('anonymize_results', false),
                'consent_levels' => $this->input('consent_levels', []),
            ],
            'output_preferences' => [
                'format' => $this->input('response_format'),
                'export_format' => $this->input('export_format'),
                'include_analytics' => $this->input('include_analytics', false),
                'include_recommendations' => $this->input('include_recommendations', false),
                'include_skill_matrix' => $this->input('include_skill_matrix', false),
            ],
            'ip_address' => $this->ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Check if content contains inappropriate material.
     */
    private function containsInappropriateContent(string $content): bool
    {
        $inappropriateWords = [
            'spam', 'scam', 'fraud', 'fake', 'illegal', 'hack', 'virus',
            'malware', 'phishing', 'adult', 'xxx', 'porn', 'sex', 'drug',
            'weapon', 'violence', 'hate', 'racist', 'terrorist', 'discrimin'
        ];

        $lowercaseContent = strtolower($content);
        
        foreach ($inappropriateWords as $word) {
            if (strpos($lowercaseContent, $word) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if locations include European countries.
     */
    private function hasEuropeanLocations(): bool
    {
        $euCountries = [
            'AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR',
            'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL',
            'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'
        ];

        $locations = $this->input('locations', []);
        
        foreach ($locations as $location) {
            if (isset($location['country']) && in_array($location['country'], $euCountries)) {
                return true;
            }
        }

        return false;
    }
}
