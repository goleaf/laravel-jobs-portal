<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class CompanyManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->getCompanyProfileRules();
        $rules = array_merge($rules, $this->getRecruitmentManagementRules());
        $rules = array_merge($rules, $this->getBrandingMarketingRules());
        $rules = array_merge($rules, $this->getAnalyticsReportingRules());
        $rules = array_merge($rules, $this->getComplianceGovernanceRules());
        $rules = array_merge($rules, $this->getIntegrationApiRules());
        $rules = array_merge($rules, $this->getTeamCollaborationRules());
        $rules = array_merge($rules, $this->getPerformanceOptimizationRules());
        $rules = array_merge($rules, $this->getAdvancedFeaturesRules());

        return $rules;
    }

    private function getCompanyProfileRules(): array
    {
        return [
            // Basic Company Information
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'company_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-_&\.]+$/'],
            'company_slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9\-_]+$/', 'unique:companies,slug'],
            'company_type' => ['nullable', 'string', Rule::in(['startup', 'small_business', 'medium_enterprise', 'large_enterprise', 'corporation', 'non_profit', 'government'])],
            'industry_sector' => ['nullable', 'string', 'max:100'],
            'company_size' => ['nullable', 'string', Rule::in(['1-10', '11-50', '51-200', '201-500', '501-1000', '1001-5000', '5000+'])],
            'founding_year' => ['nullable', 'integer', 'min:1800', 'max:'.date('Y')],
            'company_stage' => ['nullable', 'string', Rule::in(['idea', 'startup', 'growth', 'mature', 'decline', 'turnaround'])],

            // Contact & Location Information
            'headquarters_address' => ['nullable', 'array'],
            'headquarters_address.street' => ['nullable', 'string', 'max:255'],
            'headquarters_address.city' => ['nullable', 'string', 'max:100'],
            'headquarters_address.state' => ['nullable', 'string', 'max:100'],
            'headquarters_address.country' => ['nullable', 'string', 'size:2'],
            'headquarters_address.postal_code' => ['nullable', 'string', 'max:20'],
            'office_locations' => ['nullable', 'array', 'max:50'],
            'office_locations.*.location_name' => ['string', 'max:100'],
            'office_locations.*.address' => ['array'],
            'office_locations.*.is_headquarters' => ['boolean'],

            // Business Details
            'business_description' => ['nullable', 'string', 'max:5000'],
            'mission_statement' => ['nullable', 'string', 'max:1000'],
            'vision_statement' => ['nullable', 'string', 'max:1000'],
            'core_values' => ['nullable', 'array', 'max:10'],
            'core_values.*' => ['string', 'max:200'],
            'company_culture' => ['nullable', 'string', 'max:2000'],
            'unique_selling_proposition' => ['nullable', 'string', 'max:500'],
            'target_market' => ['nullable', 'string', 'max:1000'],

            // Financial Information
            'annual_revenue' => ['nullable', 'string', Rule::in(['0-1M', '1M-10M', '10M-50M', '50M-100M', '100M-500M', '500M-1B', '1B+'])],
            'funding_stage' => ['nullable', 'string', Rule::in(['bootstrapped', 'pre_seed', 'seed', 'series_a', 'series_b', 'series_c', 'ipo', 'acquired'])],
            'total_funding' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'valuation' => ['nullable', 'numeric', 'min:0', 'max:999999999999.99'],
            'public_company' => ['nullable', 'boolean'],
            'stock_symbol' => ['nullable', 'string', 'max:10', 'regex:/^[A-Z]+$/'],

            // Contact Information
            'primary_contact_email' => ['nullable', 'email', 'max:255'],
            'hr_contact_email' => ['nullable', 'email', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'twitter_handle' => ['nullable', 'string', 'max:50', 'regex:/^@?[A-Za-z0-9_]+$/'],
        ];
    }

    private function getRecruitmentManagementRules(): array
    {
        return [
            // Recruitment Strategy
            'recruitment_strategy' => ['nullable', 'string', Rule::in(['aggressive', 'moderate', 'conservative', 'passive'])],
            'hiring_goals' => ['nullable', 'array'],
            'hiring_goals.quarterly_target' => ['integer', 'min:0', 'max:10000'],
            'hiring_goals.annual_target' => ['integer', 'min:0', 'max:50000'],
            'priority_departments' => ['nullable', 'array'],
            'priority_departments.*' => ['string', 'max:100'],
            'remote_work_policy' => ['nullable', 'string', Rule::in(['fully_remote', 'hybrid', 'on_site', 'flexible'])],
            'global_hiring' => ['nullable', 'boolean'],
            'visa_sponsorship' => ['nullable', 'boolean'],

            // Job Posting Management
            'auto_posting_enabled' => ['nullable', 'boolean'],
            'posting_templates' => ['nullable', 'array'],
            'default_application_deadline' => ['nullable', 'integer', 'min:1', 'max:365'], // days
            'application_auto_response' => ['nullable', 'boolean'],
            'application_tracking_enabled' => ['nullable', 'boolean'],
            'candidate_scoring_algorithm' => ['nullable', 'string', Rule::in(['basic', 'advanced', 'ai_powered', 'custom'])],

            // Recruitment Channels
            'preferred_job_boards' => ['nullable', 'array'],
            'preferred_job_boards.*' => ['string', 'max:100'],
            'social_media_recruitment' => ['nullable', 'boolean'],
            'employee_referral_program' => ['nullable', 'boolean'],
            'referral_bonus_amount' => ['nullable', 'numeric', 'min:0', 'max:50000'],
            'university_partnerships' => ['nullable', 'array'],
            'recruitment_agencies' => ['nullable', 'array'],
            'headhunter_partnerships' => ['nullable', 'boolean'],

            // Interview Process
            'interview_process_stages' => ['nullable', 'array', 'max:10'],
            'interview_process_stages.*.stage_name' => ['string', 'max:100'],
            'interview_process_stages.*.duration_minutes' => ['integer', 'min:15', 'max:480'],
            'interview_process_stages.*.interviewer_count' => ['integer', 'min:1', 'max:20'],
            'interview_process_stages.*.stage_type' => ['string', Rule::in(['phone', 'video', 'in_person', 'technical', 'cultural', 'panel'])],
            'automated_scheduling' => ['nullable', 'boolean'],
            'interview_feedback_system' => ['nullable', 'boolean'],
            'background_check_required' => ['nullable', 'boolean'],
            'reference_check_required' => ['nullable', 'boolean'],

            // Offer Management
            'offer_approval_workflow' => ['nullable', 'boolean'],
            'salary_negotiation_policy' => ['nullable', 'string', Rule::in(['fixed', 'limited_negotiation', 'full_negotiation'])],
            'offer_expiry_days' => ['nullable', 'integer', 'min:1', 'max:30'],
            'counter_offer_policy' => ['nullable', 'boolean'],
            'signing_bonus_enabled' => ['nullable', 'boolean'],
            'relocation_assistance' => ['nullable', 'boolean'],
            'benefits_package_tiers' => ['nullable', 'array'],
        ];
    }

    private function getBrandingMarketingRules(): array
    {
        return [
            // Employer Branding
            'employer_brand_strategy' => ['nullable', 'string', Rule::in(['premium', 'innovative', 'traditional', 'startup', 'corporate'])],
            'brand_messaging' => ['nullable', 'array'],
            'brand_messaging.tagline' => ['nullable', 'string', 'max:100'],
            'brand_messaging.key_messages' => ['nullable', 'array', 'max:5'],
            'brand_messaging.key_messages.*' => ['string', 'max:200'],
            'brand_colors' => ['nullable', 'array'],
            'brand_colors.primary' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'brand_colors.secondary' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'brand_assets' => ['nullable', 'array'],

            // Content Marketing
            'content_marketing_enabled' => ['nullable', 'boolean'],
            'blog_content_strategy' => ['nullable', 'string', Rule::in(['thought_leadership', 'company_culture', 'industry_insights', 'mixed'])],
            'social_media_presence' => ['nullable', 'array'],
            'social_media_presence.platforms' => ['array'],
            'social_media_presence.posting_frequency' => ['string', Rule::in(['daily', 'weekly', 'monthly'])],
            'employee_advocacy_program' => ['nullable', 'boolean'],
            'testimonials_enabled' => ['nullable', 'boolean'],
            'case_studies_published' => ['nullable', 'boolean'],

            // Marketing Campaigns
            'recruitment_campaigns' => ['nullable', 'array'],
            'recruitment_campaigns.*.campaign_name' => ['string', 'max:100'],
            'recruitment_campaigns.*.target_audience' => ['string', 'max:200'],
            'recruitment_campaigns.*.budget' => ['numeric', 'min:0'],
            'recruitment_campaigns.*.duration_days' => ['integer', 'min:1', 'max:365'],
            'job_fair_participation' => ['nullable', 'boolean'],
            'conference_sponsorships' => ['nullable', 'array'],
            'industry_event_presence' => ['nullable', 'boolean'],

            // Digital Marketing
            'seo_optimization' => ['nullable', 'boolean'],
            'google_ads_campaigns' => ['nullable', 'boolean'],
            'linkedin_advertising' => ['nullable', 'boolean'],
            'retargeting_campaigns' => ['nullable', 'boolean'],
            'email_marketing_enabled' => ['nullable', 'boolean'],
            'newsletter_subscription' => ['nullable', 'boolean'],
            'webinar_hosting' => ['nullable', 'boolean'],
        ];
    }

    private function getAnalyticsReportingRules(): array
    {
        return [
            // Analytics Configuration
            'analytics_enabled' => ['nullable', 'boolean'],
            'data_collection_consent' => ['nullable', 'boolean'],
            'tracking_methods' => ['nullable', 'array'],
            'tracking_methods.*' => ['string', Rule::in(['cookies', 'pixels', 'utm_parameters', 'session_tracking'])],
            'real_time_analytics' => ['nullable', 'boolean'],
            'historical_data_retention' => ['nullable', 'integer', 'min:30', 'max:2555'], // days

            // Recruitment Metrics
            'track_source_effectiveness' => ['nullable', 'boolean'],
            'measure_time_to_hire' => ['nullable', 'boolean'],
            'calculate_cost_per_hire' => ['nullable', 'boolean'],
            'monitor_application_rates' => ['nullable', 'boolean'],
            'track_conversion_funnel' => ['nullable', 'boolean'],
            'measure_candidate_satisfaction' => ['nullable', 'boolean'],
            'analyze_drop_off_points' => ['nullable', 'boolean'],

            // Performance Analytics
            'hiring_manager_performance' => ['nullable', 'boolean'],
            'recruiter_performance_metrics' => ['nullable', 'boolean'],
            'interview_success_rates' => ['nullable', 'boolean'],
            'offer_acceptance_rates' => ['nullable', 'boolean'],
            'employee_retention_analysis' => ['nullable', 'boolean'],
            'onboarding_effectiveness' => ['nullable', 'boolean'],

            // Business Intelligence
            'workforce_planning_analytics' => ['nullable', 'boolean'],
            'skills_gap_analysis' => ['nullable', 'boolean'],
            'market_salary_analysis' => ['nullable', 'boolean'],
            'competitor_benchmarking' => ['nullable', 'boolean'],
            'diversity_inclusion_metrics' => ['nullable', 'boolean'],
            'predictive_hiring_models' => ['nullable', 'boolean'],

            // Reporting Features
            'automated_reports' => ['nullable', 'boolean'],
            'report_frequency' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly', 'quarterly'])],
            'custom_dashboards' => ['nullable', 'boolean'],
            'executive_summaries' => ['nullable', 'boolean'],
            'data_export_capabilities' => ['nullable', 'boolean'],
            'api_data_access' => ['nullable', 'boolean'],
            'white_label_reports' => ['nullable', 'boolean'],
        ];
    }

    private function getComplianceGovernanceRules(): array
    {
        return [
            // Legal Compliance
            'compliance_framework' => ['nullable', 'string', Rule::in(['gdpr', 'ccpa', 'eeoc', 'ada', 'ofccp', 'custom'])],
            'data_protection_measures' => ['nullable', 'array'],
            'data_protection_measures.*' => ['string', Rule::in(['encryption', 'anonymization', 'pseudonymization', 'access_control'])],
            'right_to_be_forgotten' => ['nullable', 'boolean'],
            'data_portability' => ['nullable', 'boolean'],
            'consent_management' => ['nullable', 'boolean'],
            'audit_trail_enabled' => ['nullable', 'boolean'],

            // Employment Law Compliance
            'equal_opportunity_policies' => ['nullable', 'boolean'],
            'anti_discrimination_measures' => ['nullable', 'boolean'],
            'accessibility_compliance' => ['nullable', 'boolean'],
            'veteran_hiring_programs' => ['nullable', 'boolean'],
            'diversity_reporting' => ['nullable', 'boolean'],
            'pay_equity_analysis' => ['nullable', 'boolean'],
            'background_check_compliance' => ['nullable', 'boolean'],

            // Industry-Specific Compliance
            'industry_certifications' => ['nullable', 'array'],
            'industry_certifications.*' => ['string', 'max:100'],
            'regulatory_requirements' => ['nullable', 'array'],
            'security_clearance_jobs' => ['nullable', 'boolean'],
            'professional_licensing' => ['nullable', 'boolean'],
            'continuing_education_requirements' => ['nullable', 'boolean'],

            // Governance Structure
            'approval_workflows' => ['nullable', 'array'],
            'approval_workflows.job_posting' => ['boolean'],
            'approval_workflows.offer_letters' => ['boolean'],
            'approval_workflows.salary_changes' => ['boolean'],
            'role_based_permissions' => ['nullable', 'boolean'],
            'segregation_of_duties' => ['nullable', 'boolean'],
            'management_oversight' => ['nullable', 'boolean'],

            // Risk Management
            'risk_assessment_enabled' => ['nullable', 'boolean'],
            'fraud_detection' => ['nullable', 'boolean'],
            'insider_threat_monitoring' => ['nullable', 'boolean'],
            'vendor_risk_management' => ['nullable', 'boolean'],
            'business_continuity_planning' => ['nullable', 'boolean'],
            'incident_response_procedures' => ['nullable', 'boolean'],
        ];
    }

    private function getIntegrationApiRules(): array
    {
        return [
            // API Configuration
            'api_access_enabled' => ['nullable', 'boolean'],
            'api_rate_limiting' => ['nullable', 'integer', 'min:100', 'max:100000'],
            'webhook_support' => ['nullable', 'boolean'],
            'webhook_endpoints' => ['nullable', 'array', 'max:20'],
            'webhook_endpoints.*' => ['url', 'max:500'],
            'api_versioning' => ['nullable', 'string', Rule::in(['v1', 'v2', 'latest'])],
            'api_documentation_access' => ['nullable', 'boolean'],

            // Third-party Integrations
            'ats_integration' => ['nullable', 'boolean'],
            'crm_integration' => ['nullable', 'boolean'],
            'hris_integration' => ['nullable', 'boolean'],
            'payroll_system_integration' => ['nullable', 'boolean'],
            'background_check_providers' => ['nullable', 'array'],
            'assessment_tool_integrations' => ['nullable', 'array'],

            // Social Media Integrations
            'linkedin_integration' => ['nullable', 'boolean'],
            'indeed_integration' => ['nullable', 'boolean'],
            'glassdoor_integration' => ['nullable', 'boolean'],
            'facebook_jobs_integration' => ['nullable', 'boolean'],
            'twitter_job_posting' => ['nullable', 'boolean'],
            'social_login_providers' => ['nullable', 'array'],

            // Enterprise Integrations
            'sso_configuration' => ['nullable', 'array'],
            'sso_configuration.provider' => ['string', Rule::in(['okta', 'azure_ad', 'google', 'saml', 'ldap'])],
            'sso_configuration.domain_restriction' => ['boolean'],
            'directory_sync' => ['nullable', 'boolean'],
            'calendar_integration' => ['nullable', 'boolean'],
            'email_system_integration' => ['nullable', 'boolean'],
            'file_storage_integration' => ['nullable', 'boolean'],

            // Data Synchronization
            'real_time_sync' => ['nullable', 'boolean'],
            'batch_sync_frequency' => ['nullable', 'string', Rule::in(['hourly', 'daily', 'weekly'])],
            'conflict_resolution_strategy' => ['nullable', 'string', Rule::in(['last_updated_wins', 'manual_review', 'source_priority'])],
            'data_mapping_configuration' => ['nullable', 'array'],
            'field_mapping_customization' => ['nullable', 'boolean'],
            'transformation_rules' => ['nullable', 'array'],
        ];
    }

    private function getTeamCollaborationRules(): array
    {
        return [
            // Team Structure
            'organizational_hierarchy' => ['nullable', 'array'],
            'team_roles_permissions' => ['nullable', 'array'],
            'department_structure' => ['nullable', 'array'],
            'reporting_relationships' => ['nullable', 'array'],
            'cross_functional_teams' => ['nullable', 'boolean'],
            'matrix_organization' => ['nullable', 'boolean'],

            // Collaboration Tools
            'internal_communication_tools' => ['nullable', 'array'],
            'project_management_integration' => ['nullable', 'boolean'],
            'document_collaboration' => ['nullable', 'boolean'],
            'video_conferencing_setup' => ['nullable', 'boolean'],
            'screen_sharing_enabled' => ['nullable', 'boolean'],
            'real_time_editing' => ['nullable', 'boolean'],

            // Workflow Management
            'approval_workflows' => ['nullable', 'boolean'],
            'task_assignment_automation' => ['nullable', 'boolean'],
            'deadline_management' => ['nullable', 'boolean'],
            'progress_tracking' => ['nullable', 'boolean'],
            'milestone_notifications' => ['nullable', 'boolean'],
            'bottleneck_identification' => ['nullable', 'boolean'],

            // Knowledge Management
            'knowledge_base_enabled' => ['nullable', 'boolean'],
            'documentation_standards' => ['nullable', 'boolean'],
            'best_practices_sharing' => ['nullable', 'boolean'],
            'lessons_learned_capture' => ['nullable', 'boolean'],
            'expertise_location' => ['nullable', 'boolean'],
            'institutional_knowledge_preservation' => ['nullable', 'boolean'],

            // Performance Management
            'individual_goal_setting' => ['nullable', 'boolean'],
            'team_goal_alignment' => ['nullable', 'boolean'],
            'performance_reviews' => ['nullable', 'boolean'],
            'peer_feedback_systems' => ['nullable', 'boolean'],
            '360_degree_feedback' => ['nullable', 'boolean'],
            'recognition_programs' => ['nullable', 'boolean'],
        ];
    }

    private function getPerformanceOptimizationRules(): array
    {
        return [
            // System Performance
            'caching_strategy' => ['nullable', 'string', Rule::in(['redis', 'memcached', 'file_cache', 'database_cache'])],
            'cdn_integration' => ['nullable', 'boolean'],
            'image_optimization' => ['nullable', 'boolean'],
            'lazy_loading_enabled' => ['nullable', 'boolean'],
            'database_optimization' => ['nullable', 'boolean'],
            'query_optimization' => ['nullable', 'boolean'],

            // Scalability Configuration
            'auto_scaling_enabled' => ['nullable', 'boolean'],
            'load_balancing' => ['nullable', 'boolean'],
            'horizontal_scaling' => ['nullable', 'boolean'],
            'microservices_architecture' => ['nullable', 'boolean'],
            'containerization' => ['nullable', 'boolean'],
            'cloud_native_deployment' => ['nullable', 'boolean'],

            // Monitoring & Alerts
            'performance_monitoring' => ['nullable', 'boolean'],
            'uptime_monitoring' => ['nullable', 'boolean'],
            'error_tracking' => ['nullable', 'boolean'],
            'log_aggregation' => ['nullable', 'boolean'],
            'alert_thresholds' => ['nullable', 'array'],
            'automated_incident_response' => ['nullable', 'boolean'],

            // Optimization Metrics
            'page_load_time_target' => ['nullable', 'integer', 'min:500', 'max:10000'], // milliseconds
            'api_response_time_target' => ['nullable', 'integer', 'min:100', 'max:5000'], // milliseconds
            'database_query_time_limit' => ['nullable', 'integer', 'min:50', 'max:2000'], // milliseconds
            'concurrent_user_capacity' => ['nullable', 'integer', 'min:100', 'max:1000000'],
            'throughput_targets' => ['nullable', 'array'],
            'availability_sla' => ['nullable', 'numeric', 'min:95.0', 'max:99.99'],
        ];
    }

    private function getAdvancedFeaturesRules(): array
    {
        return [
            // AI/ML Features
            'ai_powered_matching' => ['nullable', 'boolean'],
            'predictive_analytics' => ['nullable', 'boolean'],
            'sentiment_analysis' => ['nullable', 'boolean'],
            'automated_screening' => ['nullable', 'boolean'],
            'chatbot_integration' => ['nullable', 'boolean'],
            'natural_language_processing' => ['nullable', 'boolean'],
            'machine_learning_insights' => ['nullable', 'boolean'],

            // Advanced Analytics
            'behavioral_analytics' => ['nullable', 'boolean'],
            'cohort_analysis' => ['nullable', 'boolean'],
            'funnel_analysis' => ['nullable', 'boolean'],
            'attribution_modeling' => ['nullable', 'boolean'],
            'predictive_modeling' => ['nullable', 'boolean'],
            'anomaly_detection' => ['nullable', 'boolean'],

            // Automation Features
            'workflow_automation' => ['nullable', 'boolean'],
            'email_automation' => ['nullable', 'boolean'],
            'report_automation' => ['nullable', 'boolean'],
            'candidate_nurturing_automation' => ['nullable', 'boolean'],
            'interview_scheduling_automation' => ['nullable', 'boolean'],
            'offer_generation_automation' => ['nullable', 'boolean'],

            // Future-ready Features
            'blockchain_verification' => ['nullable', 'boolean'],
            'virtual_reality_interviews' => ['nullable', 'boolean'],
            'augmented_reality_office_tours' => ['nullable', 'boolean'],
            'voice_interface_support' => ['nullable', 'boolean'],
            'mobile_first_design' => ['nullable', 'boolean'],
            'progressive_web_app' => ['nullable', 'boolean'],

            // Enterprise Features
            'multi_tenant_architecture' => ['nullable', 'boolean'],
            'white_label_customization' => ['nullable', 'boolean'],
            'custom_domain_support' => ['nullable', 'boolean'],
            'advanced_security_features' => ['nullable', 'boolean'],
            'enterprise_sla' => ['nullable', 'boolean'],
            'dedicated_support' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // Company Profile Messages
            'company_name.regex' => __('validation.company_management.invalid_company_name'),
            'company_slug.unique' => __('validation.company_management.company_slug_taken'),
            'founding_year.max' => __('validation.company_management.founding_year_future'),
            'stock_symbol.regex' => __('validation.company_management.invalid_stock_symbol'),

            // Recruitment Messages
            'hiring_goals.quarterly_target.max' => __('validation.company_management.quarterly_target_too_high'),
            'referral_bonus_amount.max' => __('validation.company_management.referral_bonus_too_high'),
            'offer_expiry_days.max' => __('validation.company_management.offer_expiry_too_long'),

            // Analytics Messages
            'historical_data_retention.max' => __('validation.company_management.data_retention_too_long'),
            'api_rate_limiting.min' => __('validation.company_management.api_rate_too_low'),

            // Performance Messages
            'page_load_time_target.min' => __('validation.company_management.page_load_too_fast'),
            'concurrent_user_capacity.max' => __('validation.company_management.user_capacity_exceeded'),
            'availability_sla.min' => __('validation.company_management.sla_too_low'),
        ];
    }

    protected function passedValidation(): void
    {
        $this->validateCompanyConfiguration();
        $this->optimizeCompanyPerformance();
        $this->logCompanyActivity();
    }

    private function validateCompanyConfiguration(): void
    {
        // Validate company size vs hiring goals
        if ($this->has(['company_size', 'hiring_goals'])) {
            $sizeMap = [
                '1-10' => 10, '11-50' => 50, '51-200' => 200,
                '201-500' => 500, '501-1000' => 1000, '1001-5000' => 5000, '5000+' => 10000,
            ];

            $maxSize = $sizeMap[$this->company_size] ?? 10;
            $quarterlyTarget = $this->hiring_goals['quarterly_target'] ?? 0;

            if ($quarterlyTarget > $maxSize * 0.5) {
                throw new \InvalidArgumentException(__('validation.company_management.hiring_goal_unrealistic'));
            }
        }

        // Validate financial consistency
        if ($this->has(['annual_revenue', 'total_funding'])) {
            $revenueMap = [
                '0-1M' => 1000000, '1M-10M' => 10000000, '10M-50M' => 50000000,
                '50M-100M' => 100000000, '100M-500M' => 500000000,
                '500M-1B' => 1000000000, '1B+' => 10000000000,
            ];

            $maxRevenue = $revenueMap[$this->annual_revenue] ?? 1000000;

            if ($this->total_funding > $maxRevenue * 10) {
                throw new \InvalidArgumentException(__('validation.company_management.funding_revenue_mismatch'));
            }
        }

        // Validate performance targets
        if ($this->has(['page_load_time_target', 'api_response_time_target'])) {
            if ($this->api_response_time_target > $this->page_load_time_target) {
                throw new \InvalidArgumentException(__('validation.company_management.api_slower_than_page'));
            }
        }
    }

    private function optimizeCompanyPerformance(): void
    {
        // Optimize based on company size
        if ($this->has('company_size')) {
            $optimizations = $this->calculateOptimizations($this->company_size);

            $this->merge([
                'recommended_cache_strategy' => $optimizations['cache_strategy'],
                'suggested_api_rate_limit' => $optimizations['api_rate_limit'],
                'optimal_team_structure' => $optimizations['team_structure'],
            ]);
        }

        // Cache company configuration
        if ($this->has('company_id')) {
            Cache::remember("company_config_{$this->company_id}", 7200, function () {
                return $this->validated();
            });
        }
    }

    private function calculateOptimizations(string $companySize): array
    {
        $optimizations = [
            '1-10' => ['cache_strategy' => 'file_cache', 'api_rate_limit' => 1000, 'team_structure' => 'flat'],
            '11-50' => ['cache_strategy' => 'redis', 'api_rate_limit' => 5000, 'team_structure' => 'departmental'],
            '51-200' => ['cache_strategy' => 'redis', 'api_rate_limit' => 10000, 'team_structure' => 'hierarchical'],
            '201-500' => ['cache_strategy' => 'redis', 'api_rate_limit' => 25000, 'team_structure' => 'matrix'],
            '501-1000' => ['cache_strategy' => 'redis', 'api_rate_limit' => 50000, 'team_structure' => 'divisional'],
            '1001-5000' => ['cache_strategy' => 'distributed', 'api_rate_limit' => 75000, 'team_structure' => 'enterprise'],
            '5000+' => ['cache_strategy' => 'distributed', 'api_rate_limit' => 100000, 'team_structure' => 'enterprise'],
        ];

        return $optimizations[$companySize] ?? $optimizations['1-10'];
    }

    private function logCompanyActivity(): void
    {
        \Log::info('Company Management Request', [
            'company_id' => $this->company_id ?? 'new',
            'operation_type' => $this->getOperationType(),
            'company_size' => $this->company_size ?? 'unknown',
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'timestamp' => now(),
            'optimizations_applied' => $this->has('recommended_cache_strategy'),
        ]);
    }

    private function getOperationType(): string
    {
        if ($this->has('company_name')) {
            return 'profile_management';
        }
        if ($this->has('recruitment_strategy')) {
            return 'recruitment_management';
        }
        if ($this->has('employer_brand_strategy')) {
            return 'branding_marketing';
        }
        if ($this->has('analytics_enabled')) {
            return 'analytics_configuration';
        }
        if ($this->has('compliance_framework')) {
            return 'compliance_management';
        }
        if ($this->has('api_access_enabled')) {
            return 'integration_management';
        }

        return 'general_company_operation';
    }
}
