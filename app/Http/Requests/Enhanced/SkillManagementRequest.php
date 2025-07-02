<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SkillManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->getSkillTaxonomyRules();
        $rules = array_merge($rules, $this->getCompetencyFrameworkRules());
        $rules = array_merge($rules, $this->getSkillAssessmentRules());
        $rules = array_merge($rules, $this->getMatchingAlgorithmRules());
        $rules = array_merge($rules, $this->getSkillDevelopmentRules());
        $rules = array_merge($rules, $this->getCertificationManagementRules());
        $rules = array_merge($rules, $this->getSkillAnalyticsRules());
        $rules = array_merge($rules, $this->getAdvancedFeaturesRules());
        
        return $rules;
    }

    private function getSkillTaxonomyRules(): array
    {
        return [
            // Skill Entity Management
            'skill_id' => ['nullable', 'integer', 'exists:skills,id'],
            'skill_name' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s\-_\.\/]+$/'],
            'skill_slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9\-_]+$/', 'unique:skills,slug'],
            'skill_description' => ['nullable', 'string', 'max:2000'],
            'skill_type' => ['nullable', 'string', Rule::in(['hard', 'soft', 'technical', 'behavioral', 'cognitive', 'interpersonal', 'leadership'])],
            'skill_complexity' => ['nullable', 'string', Rule::in(['basic', 'intermediate', 'advanced', 'expert', 'master'])],
            'skill_domain' => ['nullable', 'string', 'max:100'],
            'skill_category' => ['nullable', 'string', 'max:100'],
            'skill_subcategory' => ['nullable', 'string', 'max:100'],
            
            // Hierarchical Structure
            'parent_skill_id' => ['nullable', 'integer', 'exists:skills,id'],
            'child_skills' => ['nullable', 'array'],
            'child_skills.*' => ['integer', 'exists:skills,id'],
            'skill_level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'hierarchy_depth' => ['nullable', 'integer', 'min:0', 'max:5'],
            'skill_path' => ['nullable', 'string', 'max:500'],
            'related_skills' => ['nullable', 'array'],
            'related_skills.*' => ['integer', 'exists:skills,id'],
            'prerequisite_skills' => ['nullable', 'array'],
            'prerequisite_skills.*' => ['integer', 'exists:skills,id'],
            
            // Skill Metadata
            'aliases' => ['nullable', 'array', 'max:10'],
            'aliases.*' => ['string', 'max:100'],
            'keywords' => ['nullable', 'array', 'max:20'],
            'keywords.*' => ['string', 'max:50'],
            'skill_tags' => ['nullable', 'array', 'max:15'],
            'skill_tags.*' => ['string', 'max:30'],
            'industry_relevance' => ['nullable', 'array'],
            'industry_relevance.*' => ['string', 'max:100'],
            'job_roles_relevance' => ['nullable', 'array'],
            'job_roles_relevance.*' => ['string', 'max:100'],
            
            // Taxonomy Classification
            'taxonomies' => ['nullable', 'array'],
            'taxonomies.*.taxonomy_name' => ['string', 'max:100'],
            'taxonomies.*.taxonomy_version' => ['string', 'max:20'],
            'taxonomies.*.skill_code' => ['string', 'max:50'],
            'taxonomies.*.classification_level' => ['integer', 'min:1', 'max:10'],
            'standard_classifications' => ['nullable', 'array'],
            'standard_classifications.*' => ['string', Rule::in(['onet', 'esco', 'nice', 'sfia', 'custom'])],
            'external_mappings' => ['nullable', 'array'],
            
            // Skill Lifecycle
            'status' => ['nullable', 'string', Rule::in(['active', 'deprecated', 'emerging', 'obsolete', 'draft'])],
            'version' => ['nullable', 'string', 'max:20'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'approved_by' => ['nullable', 'integer', 'exists:users,id'],
            'approval_date' => ['nullable', 'date'],
            'review_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after:now'],
            'popularity_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'trending_score' => ['nullable', 'numeric', 'min:-100', 'max:100'],
            
            // Market Data
            'market_demand' => ['nullable', 'string', Rule::in(['very_low', 'low', 'medium', 'high', 'very_high'])],
            'salary_impact' => ['nullable', 'numeric', 'min:-50', 'max:200'], // percentage
            'job_postings_count' => ['nullable', 'integer', 'min:0'],
            'skill_shortage_indicator' => ['nullable', 'boolean'],
            'emerging_technology' => ['nullable', 'boolean'],
            'automation_risk' => ['nullable', 'numeric', 'min:0', 'max:100'], // percentage
            'future_relevance' => ['nullable', 'string', Rule::in(['declining', 'stable', 'growing', 'exploding'])],
        ];
    }

    private function getCompetencyFrameworkRules(): array
    {
        return [
            // Competency Definition
            'competency_id' => ['nullable', 'string', 'max:255'],
            'competency_name' => ['nullable', 'string', 'max:255'],
            'competency_description' => ['nullable', 'string', 'max:2000'],
            'competency_type' => ['nullable', 'string', Rule::in(['core', 'functional', 'leadership', 'technical', 'behavioral'])],
            'proficiency_levels' => ['nullable', 'array', 'max:10'],
            'proficiency_levels.*.level_name' => ['string', 'max:100'],
            'proficiency_levels.*.level_number' => ['integer', 'min:1', 'max:10'],
            'proficiency_levels.*.description' => ['string', 'max:1000'],
            'proficiency_levels.*.indicators' => ['array'],
            'proficiency_levels.*.assessment_criteria' => ['array'],
            
            // Framework Structure
            'framework_name' => ['nullable', 'string', 'max:255'],
            'framework_version' => ['nullable', 'string', 'max:20'],
            'framework_description' => ['nullable', 'string', 'max:2000'],
            'framework_type' => ['nullable', 'string', Rule::in(['organizational', 'industry', 'role_specific', 'functional', 'custom'])],
            'competency_domains' => ['nullable', 'array'],
            'competency_domains.*.domain_name' => ['string', 'max:100'],
            'competency_domains.*.competencies' => ['array'],
            'competency_domains.*.weight' => ['numeric', 'min:0', 'max:100'],
            
            // Behavioral Indicators
            'behavioral_indicators' => ['nullable', 'array'],
            'behavioral_indicators.*.indicator_text' => ['string', 'max:500'],
            'behavioral_indicators.*.proficiency_level' => ['integer', 'min:1', 'max:10'],
            'behavioral_indicators.*.observable_actions' => ['array'],
            'behavioral_indicators.*.measurement_criteria' => ['string', 'max:1000'],
            'performance_standards' => ['nullable', 'array'],
            'success_metrics' => ['nullable', 'array'],
            
            // Competency Mapping
            'skill_competency_mapping' => ['nullable', 'array'],
            'skill_competency_mapping.*.skill_id' => ['integer', 'exists:skills,id'],
            'skill_competency_mapping.*.competency_id' => ['string', 'max:255'],
            'skill_competency_mapping.*.relevance_weight' => ['numeric', 'min:0', 'max:1'],
            'skill_competency_mapping.*.required_proficiency' => ['integer', 'min:1', 'max:10'],
            'job_role_mapping' => ['nullable', 'array'],
            'career_level_mapping' => ['nullable', 'array'],
            
            // Assessment Framework
            'assessment_methods' => ['nullable', 'array'],
            'assessment_methods.*' => ['string', Rule::in(['observation', 'interview', 'test', 'portfolio', 'simulation', 'peer_review', 'self_assessment', '360_feedback'])],
            'assessment_frequency' => ['nullable', 'string', Rule::in(['continuous', 'quarterly', 'annually', 'on_demand', 'milestone_based'])],
            'assessment_criteria' => ['nullable', 'array'],
            'assessment_rubrics' => ['nullable', 'array'],
            'scoring_methodology' => ['nullable', 'string', Rule::in(['weighted_average', 'holistic', 'criterion_referenced', 'norm_referenced'])],
            
            // Development Pathways
            'development_activities' => ['nullable', 'array'],
            'development_activities.*.activity_type' => ['string', Rule::in(['training', 'mentoring', 'coaching', 'project', 'rotation', 'certification'])],
            'development_activities.*.activity_name' => ['string', 'max:255'],
            'development_activities.*.target_proficiency' => ['integer', 'min:1', 'max:10'],
            'development_activities.*.duration_hours' => ['integer', 'min:1', 'max:1000'],
            'development_activities.*.cost_estimate' => ['numeric', 'min:0'],
            'learning_objectives' => ['nullable', 'array'],
            'progression_milestones' => ['nullable', 'array'],
        ];
    }

    private function getSkillAssessmentRules(): array
    {
        return [
            // Assessment Configuration
            'assessment_id' => ['nullable', 'string', 'max:255'],
            'assessment_name' => ['nullable', 'string', 'max:255'],
            'assessment_type' => ['nullable', 'string', Rule::in(['self_assessment', 'manager_assessment', 'peer_assessment', 'expert_assessment', 'automated_assessment', '360_assessment'])],
            'assessment_method' => ['nullable', 'string', Rule::in(['questionnaire', 'practical_test', 'portfolio_review', 'interview', 'simulation', 'observation', 'project_based'])],
            'assessment_duration' => ['nullable', 'integer', 'min:5', 'max:480'], // minutes
            'assessment_difficulty' => ['nullable', 'string', Rule::in(['beginner', 'intermediate', 'advanced', 'expert', 'adaptive'])],
            'passing_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'retake_allowed' => ['nullable', 'boolean'],
            'max_retake_attempts' => ['nullable', 'integer', 'min:1', 'max:10'],
            
            // Question Management
            'question_bank' => ['nullable', 'array'],
            'question_bank.*.question_id' => ['string', 'max:255'],
            'question_bank.*.question_text' => ['string', 'max:2000'],
            'question_bank.*.question_type' => ['string', Rule::in(['multiple_choice', 'true_false', 'short_answer', 'essay', 'practical', 'simulation', 'coding'])],
            'question_bank.*.difficulty_level' => ['string', Rule::in(['easy', 'medium', 'hard', 'expert'])],
            'question_bank.*.skill_measured' => ['integer', 'exists:skills,id'],
            'question_bank.*.points' => ['integer', 'min:1', 'max:100'],
            'question_bank.*.time_limit' => ['integer', 'min:30', 'max:3600'], // seconds
            'adaptive_questioning' => ['nullable', 'boolean'],
            'question_randomization' => ['nullable', 'boolean'],
            
            // Scoring System
            'scoring_algorithm' => ['nullable', 'string', Rule::in(['simple_sum', 'weighted_average', 'irt_model', 'rasch_model', 'bayesian'])],
            'item_response_theory' => ['nullable', 'boolean'],
            'confidence_intervals' => ['nullable', 'boolean'],
            'skill_proficiency_mapping' => ['nullable', 'array'],
            'skill_proficiency_mapping.*.skill_id' => ['integer', 'exists:skills,id'],
            'skill_proficiency_mapping.*.proficiency_score' => ['numeric', 'min:0', 'max:100'],
            'skill_proficiency_mapping.*.confidence_level' => ['numeric', 'min:0', 'max:100'],
            'skill_proficiency_mapping.*.evidence_strength' => ['string', Rule::in(['weak', 'moderate', 'strong', 'very_strong'])],
            
            // Assessment Analytics
            'performance_analytics' => ['nullable', 'boolean'],
            'skill_gap_analysis' => ['nullable', 'boolean'],
            'benchmark_comparisons' => ['nullable', 'boolean'],
            'improvement_recommendations' => ['nullable', 'boolean'],
            'learning_path_suggestions' => ['nullable', 'boolean'],
            'competency_radar_charts' => ['nullable', 'boolean'],
            'progress_tracking' => ['nullable', 'boolean'],
            'trend_analysis' => ['nullable', 'boolean'],
            
            // Validation and Reliability
            'content_validity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'construct_validity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'reliability_coefficient' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'test_retest_reliability' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'inter_rater_reliability' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'standard_error_measurement' => ['nullable', 'numeric', 'min:0'],
            'assessment_bias_analysis' => ['nullable', 'boolean'],
            'fairness_indicators' => ['nullable', 'array'],
            
            // Certification Integration
            'certification_eligible' => ['nullable', 'boolean'],
            'certification_provider' => ['nullable', 'string', 'max:255'],
            'certification_level' => ['nullable', 'string', 'max:100'],
            'certification_validity_period' => ['nullable', 'integer', 'min:30', 'max:1825], // days
            'continuing_education_required' => ['nullable', 'boolean'],
            'renewal_requirements' => ['nullable', 'array'],
        ];
    }

    private function getMatchingAlgorithmRules(): array
    {
        return [
            // Algorithm Configuration
            'matching_algorithm' => ['nullable', 'string', Rule::in(['keyword_based', 'semantic_similarity', 'machine_learning', 'hybrid', 'ai_powered'])],
            'similarity_threshold' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'matching_precision' => ['nullable', 'string', Rule::in(['exact', 'high', 'medium', 'low', 'fuzzy'])],
            'weight_configuration' => ['nullable', 'array'],
            'weight_configuration.exact_match' => ['numeric', 'min:0', 'max:1'],
            'weight_configuration.partial_match' => ['numeric', 'min:0', 'max:1'],
            'weight_configuration.semantic_similarity' => ['numeric', 'min:0', 'max:1'],
            'weight_configuration.context_relevance' => ['numeric', 'min:0', 'max:1'],
            
            // Semantic Analysis
            'natural_language_processing' => ['nullable', 'boolean'],
            'word_embeddings_model' => ['nullable', 'string', Rule::in(['word2vec', 'glove', 'bert', 'gpt', 'custom'])],
            'semantic_similarity_model' => ['nullable', 'string', 'max:100'],
            'context_analysis' => ['nullable', 'boolean'],
            'synonym_recognition' => ['nullable', 'boolean'],
            'multilingual_support' => ['nullable', 'boolean'],
            'supported_languages' => ['nullable', 'array'],
            'supported_languages.*' => ['string', 'size:2'], // ISO language codes
            
            // Machine Learning Models
            'ml_model_type' => ['nullable', 'string', Rule::in(['collaborative_filtering', 'content_based', 'neural_network', 'ensemble', 'deep_learning'])],
            'training_data_size' => ['nullable', 'integer', 'min:1000'],
            'model_accuracy' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'model_precision' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'model_recall' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'f1_score' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'model_training_frequency' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly', 'quarterly', 'on_demand'])],
            'online_learning' => ['nullable', 'boolean'],
            
            // Skill Matching Features
            'skill_similarity_matrix' => ['nullable', 'array'],
            'skill_clustering' => ['nullable', 'boolean'],
            'skill_embeddings' => ['nullable', 'boolean'],
            'transferable_skills_detection' => ['nullable', 'boolean'],
            'skill_evolution_tracking' => ['nullable', 'boolean'],
            'industry_context_awareness' => ['nullable', 'boolean'],
            'role_specific_matching' => ['nullable', 'boolean'],
            'experience_level_consideration' => ['nullable', 'boolean'],
            
            // Advanced Matching
            'graph_based_matching' => ['nullable', 'boolean'],
            'knowledge_graph_integration' => ['nullable', 'boolean'],
            'ontology_based_reasoning' => ['nullable', 'boolean'],
            'fuzzy_logic_matching' => ['nullable', 'boolean'],
            'probabilistic_matching' => ['nullable', 'boolean'],
            'multi_criteria_matching' => ['nullable', 'boolean'],
            'temporal_skill_relevance' => ['nullable', 'boolean'],
            'market_demand_weighting' => ['nullable', 'boolean'],
            
            // Performance Optimization
            'caching_enabled' => ['nullable', 'boolean'],
            'cache_duration' => ['nullable', 'integer', 'min:300', 'max:86400'], // seconds
            'parallel_processing' => ['nullable', 'boolean'],
            'batch_processing' => ['nullable', 'boolean'],
            'real_time_matching' => ['nullable', 'boolean'],
            'index_optimization' => ['nullable', 'boolean'],
            'query_optimization' => ['nullable', 'boolean'],
            'result_ranking_algorithm' => ['nullable', 'string', Rule::in(['relevance_score', 'composite_score', 'machine_learned', 'custom'])],
            
            // Quality Metrics
            'matching_accuracy_target' => ['nullable', 'numeric', 'min:0.5', 'max:1.0'],
            'false_positive_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'false_negative_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'user_satisfaction_score' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'click_through_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'conversion_rate' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ];
    }

    private function getSkillDevelopmentRules(): array
    {
        return [
            // Learning Path Configuration
            'learning_path_id' => ['nullable', 'string', 'max:255'],
            'learning_path_name' => ['nullable', 'string', 'max:255'],
            'target_skills' => ['nullable', 'array'],
            'target_skills.*' => ['integer', 'exists:skills,id'],
            'current_skill_level' => ['nullable', 'array'],
            'target_skill_level' => ['nullable', 'array'],
            'estimated_duration' => ['nullable', 'integer', 'min:1', 'max:8760'], // hours (1 year max)
            'difficulty_progression' => ['nullable', 'string', Rule::in(['linear', 'exponential', 'adaptive', 'custom'])],
            'personalization_level' => ['nullable', 'string', Rule::in(['basic', 'moderate', 'high', 'ai_driven'])],
            
            // Learning Resources
            'learning_resources' => ['nullable', 'array'],
            'learning_resources.*.resource_type' => ['string', Rule::in(['course', 'book', 'video', 'article', 'tutorial', 'workshop', 'mentorship', 'project'])],
            'learning_resources.*.resource_name' => ['string', 'max:255'],
            'learning_resources.*.provider' => ['string', 'max:255'],
            'learning_resources.*.duration_hours' => ['integer', 'min:1', 'max:1000'],
            'learning_resources.*.cost' => ['numeric', 'min:0'],
            'learning_resources.*.skill_coverage' => ['array'],
            'learning_resources.*.quality_rating' => ['numeric', 'min:1', 'max:5'],
            'learning_resources.*.prerequisites' => ['array'],
            
            // Skill Gap Analysis
            'skill_gap_identification' => ['nullable', 'boolean'],
            'gap_analysis_method' => ['nullable', 'string', Rule::in(['competency_based', 'role_based', 'goal_based', 'ai_analyzed'])],
            'priority_skills' => ['nullable', 'array'],
            'priority_skills.*.skill_id' => ['integer', 'exists:skills,id'],
            'priority_skills.*.priority_level' => ['string', Rule::in(['critical', 'high', 'medium', 'low'])],
            'priority_skills.*.business_impact' => ['string', Rule::in(['high', 'medium', 'low'])],
            'priority_skills.*.urgency' => ['string', Rule::in(['immediate', 'short_term', 'medium_term', 'long_term'])],
            
            // Development Planning
            'development_goals' => ['nullable', 'array'],
            'development_goals.*.goal_name' => ['string', 'max:255'],
            'development_goals.*.target_date' => ['date', 'after:now'],
            'development_goals.*.success_criteria' => ['string', 'max:1000'],
            'development_goals.*.measurement_method' => ['string', 'max:255'],
            'milestone_tracking' => ['nullable', 'boolean'],
            'progress_monitoring' => ['nullable', 'boolean'],
            'adaptive_learning' => ['nullable', 'boolean'],
            'microlearning_enabled' => ['nullable', 'boolean'],
            
            // Mentorship and Coaching
            'mentorship_program' => ['nullable', 'boolean'],
            'mentor_matching_criteria' => ['nullable', 'array'],
            'coaching_integration' => ['nullable', 'boolean'],
            'peer_learning_groups' => ['nullable', 'boolean'],
            'expert_network_access' => ['nullable', 'boolean'],
            'community_learning' => ['nullable', 'boolean'],
            'social_learning_features' => ['nullable', 'boolean'],
            
            // Gamification
            'gamification_enabled' => ['nullable', 'boolean'],
            'achievement_badges' => ['nullable', 'boolean'],
            'skill_points_system' => ['nullable', 'boolean'],
            'leaderboards' => ['nullable', 'boolean'],
            'learning_streaks' => ['nullable', 'boolean'],
            'challenges_and_quests' => ['nullable', 'boolean'],
            'team_competitions' => ['nullable', 'boolean'],
            
            // ROI and Analytics
            'learning_roi_tracking' => ['nullable', 'boolean'],
            'skill_impact_measurement' => ['nullable', 'boolean'],
            'business_outcome_correlation' => ['nullable', 'boolean'],
            'learning_analytics' => ['nullable', 'boolean'],
            'predictive_success_modeling' => ['nullable', 'boolean'],
            'cost_benefit_analysis' => ['nullable', 'boolean'],
        ];
    }

    private function getCertificationManagementRules(): array
    {
        return [
            // Certification Configuration
            'certification_id' => ['nullable', 'string', 'max:255'],
            'certification_name' => ['nullable', 'string', 'max:255'],
            'certification_provider' => ['nullable', 'string', 'max:255'],
            'certification_type' => ['nullable', 'string', Rule::in(['professional', 'technical', 'industry', 'vendor', 'academic', 'government'])],
            'certification_level' => ['nullable', 'string', Rule::in(['foundation', 'associate', 'professional', 'expert', 'master'])],
            'certification_domain' => ['nullable', 'string', 'max:100'],
            'associated_skills' => ['nullable', 'array'],
            'associated_skills.*' => ['integer', 'exists:skills,id'],
            
            // Validity and Maintenance
            'validity_period' => ['nullable', 'integer', 'min:30', 'max:3650'], // days
            'renewal_required' => ['nullable', 'boolean'],
            'renewal_period' => ['nullable', 'integer', 'min:30', 'max:1825'], // days
            'continuing_education_units' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'maintenance_activities' => ['nullable', 'array'],
            'maintenance_activities.*' => ['string', Rule::in(['training', 'conference', 'workshop', 'project', 'publication', 'volunteering'])],
            'recertification_requirements' => ['nullable', 'array'],
            
            // Prerequisites and Requirements
            'prerequisites' => ['nullable', 'array'],
            'prerequisites.education_level' => ['string', 'max:100'],
            'prerequisites.work_experience_years' => ['integer', 'min:0', 'max:50'],
            'prerequisites.required_certifications' => ['array'],
            'prerequisites.required_skills' => ['array'],
            'eligibility_criteria' => ['nullable', 'array'],
            'exemption_conditions' => ['nullable', 'array'],
            
            // Examination Details
            'exam_format' => ['nullable', 'string', Rule::in(['online', 'in_person', 'hybrid', 'practical', 'portfolio'])],
            'exam_duration' => ['nullable', 'integer', 'min:30', 'max:480'], // minutes
            'number_of_questions' => ['nullable', 'integer', 'min:10', 'max:500'],
            'passing_score_percentage' => ['nullable', 'numeric', 'min:50', 'max:100'],
            'question_types' => ['nullable', 'array'],
            'question_types.*' => ['string', Rule::in(['multiple_choice', 'true_false', 'essay', 'practical', 'simulation'])],
            'exam_cost' => ['nullable', 'numeric', 'min:0'],
            'retake_policy' => ['nullable', 'array'],
            
            // Industry Recognition
            'industry_recognition' => ['nullable', 'string', Rule::in(['high', 'medium', 'low', 'emerging'])],
            'market_value' => ['nullable', 'numeric', 'min:0'],
            'employer_demand' => ['nullable', 'string', Rule::in(['very_high', 'high', 'medium', 'low'])],
            'salary_impact_percentage' => ['nullable', 'numeric', 'min:-10', 'max:100'],
            'career_advancement_potential' => ['nullable', 'string', Rule::in(['high', 'medium', 'low'])],
            'global_recognition' => ['nullable', 'boolean'],
            'accreditation_bodies' => ['nullable', 'array'],
            
            // Tracking and Management
            'certification_tracking' => ['nullable', 'boolean'],
            'expiry_notifications' => ['nullable', 'boolean'],
            'renewal_reminders' => ['nullable', 'boolean'],
            'compliance_monitoring' => ['nullable', 'boolean'],
            'audit_trail' => ['nullable', 'boolean'],
            'verification_system' => ['nullable', 'boolean'],
            'digital_badges' => ['nullable', 'boolean'],
            'blockchain_verification' => ['nullable', 'boolean'],
        ];
    }

    private function getSkillAnalyticsRules(): array
    {
        return [
            // Analytics Configuration
            'analytics_enabled' => ['nullable', 'boolean'],
            'real_time_analytics' => ['nullable', 'boolean'],
            'historical_analysis' => ['nullable', 'boolean'],
            'predictive_analytics' => ['nullable', 'boolean'],
            'comparative_analytics' => ['nullable', 'boolean'],
            'trend_analysis' => ['nullable', 'boolean'],
            'sentiment_analysis' => ['nullable', 'boolean'],
            
            // Skill Metrics
            'skill_popularity_tracking' => ['nullable', 'boolean'],
            'skill_demand_analysis' => ['nullable', 'boolean'],
            'skill_supply_analysis' => ['nullable', 'boolean'],
            'skill_gap_measurement' => ['nullable', 'boolean'],
            'skill_evolution_tracking' => ['nullable', 'boolean'],
            'skill_obsolescence_prediction' => ['nullable', 'boolean'],
            'emerging_skills_detection' => ['nullable', 'boolean'],
            'skill_clustering_analysis' => ['nullable', 'boolean'],
            
            // Market Intelligence
            'job_market_analysis' => ['nullable', 'boolean'],
            'salary_trend_analysis' => ['nullable', 'boolean'],
            'industry_skill_mapping' => ['nullable', 'boolean'],
            'geographic_skill_distribution' => ['nullable', 'boolean'],
            'competitor_skill_analysis' => ['nullable', 'boolean'],
            'talent_mobility_patterns' => ['nullable', 'boolean'],
            'skills_roi_analysis' => ['nullable', 'boolean'],
            
            // Learning Analytics
            'learning_effectiveness_measurement' => ['nullable', 'boolean'],
            'skill_acquisition_rate' => ['nullable', 'boolean'],
            'learning_path_optimization' => ['nullable', 'boolean'],
            'resource_utilization_analysis' => ['nullable', 'boolean'],
            'learner_engagement_metrics' => ['nullable', 'boolean'],
            'completion_rate_analysis' => ['nullable', 'boolean'],
            'knowledge_retention_analysis' => ['nullable', 'boolean'],
            
            // Reporting and Visualization
            'automated_reports' => ['nullable', 'boolean'],
            'custom_dashboards' => ['nullable', 'boolean'],
            'data_visualization' => ['nullable', 'boolean'],
            'interactive_charts' => ['nullable', 'boolean'],
            'export_capabilities' => ['nullable', 'boolean'],
            'api_access' => ['nullable', 'boolean'],
            'scheduled_reporting' => ['nullable', 'boolean'],
            
            // Data Sources
            'internal_data_sources' => ['nullable', 'array'],
            'external_data_sources' => ['nullable', 'array'],
            'social_media_integration' => ['nullable', 'boolean'],
            'job_board_integration' => ['nullable', 'boolean'],
            'professional_network_data' => ['nullable', 'boolean'],
            'academic_institution_data' => ['nullable', 'boolean'],
            'government_data_sources' => ['nullable', 'boolean'],
        ];
    }

    private function getAdvancedFeaturesRules(): array
    {
        return [
            // AI and Machine Learning
            'artificial_intelligence' => ['nullable', 'boolean'],
            'neural_networks' => ['nullable', 'boolean'],
            'deep_learning' => ['nullable', 'boolean'],
            'reinforcement_learning' => ['nullable', 'boolean'],
            'transfer_learning' => ['nullable', 'boolean'],
            'federated_learning' => ['nullable', 'boolean'],
            'explainable_ai' => ['nullable', 'boolean'],
            'ai_bias_mitigation' => ['nullable', 'boolean'],
            
            // Advanced Analytics
            'graph_analytics' => ['nullable', 'boolean'],
            'network_analysis' => ['nullable', 'boolean'],
            'social_network_analysis' => ['nullable', 'boolean'],
            'knowledge_graphs' => ['nullable', 'boolean'],
            'ontology_reasoning' => ['nullable', 'boolean'],
            'semantic_web_technologies' => ['nullable', 'boolean'],
            'linked_data_integration' => ['nullable', 'boolean'],
            
            // Future Technologies
            'blockchain_integration' => ['nullable', 'boolean'],
            'quantum_computing_ready' => ['nullable', 'boolean'],
            'augmented_reality_training' => ['nullable', 'boolean'],
            'virtual_reality_simulations' => ['nullable', 'boolean'],
            'mixed_reality_experiences' => ['nullable', 'boolean'],
            'iot_skill_tracking' => ['nullable', 'boolean'],
            'brain_computer_interfaces' => ['nullable', 'boolean'],
            
            // Integration Capabilities
            'api_ecosystem' => ['nullable', 'boolean'],
            'microservices_architecture' => ['nullable', 'boolean'],
            'cloud_native_deployment' => ['nullable', 'boolean'],
            'edge_computing' => ['nullable', 'boolean'],
            'serverless_functions' => ['nullable', 'boolean'],
            'containerization' => ['nullable', 'boolean'],
            'kubernetes_orchestration' => ['nullable', 'boolean'],
            
            // Security and Privacy
            'advanced_encryption' => ['nullable', 'boolean'],
            'privacy_preserving_analytics' => ['nullable', 'boolean'],
            'differential_privacy' => ['nullable', 'boolean'],
            'homomorphic_encryption' => ['nullable', 'boolean'],
            'secure_multiparty_computation' => ['nullable', 'boolean'],
            'zero_knowledge_proofs' => ['nullable', 'boolean'],
            'gdpr_compliance' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // Skill Taxonomy Messages
            'skill_name.regex' => __('validation.skill_management.invalid_skill_name'),
            'skill_slug.unique' => __('validation.skill_management.skill_slug_taken'),
            'hierarchy_depth.max' => __('validation.skill_management.hierarchy_too_deep'),
            'aliases.max' => __('validation.skill_management.too_many_aliases'),
            'keywords.max' => __('validation.skill_management.too_many_keywords'),
            
            // Assessment Messages
            'assessment_duration.max' => __('validation.skill_management.assessment_too_long'),
            'max_retake_attempts.max' => __('validation.skill_management.too_many_retakes'),
            'question_bank.*.time_limit.max' => __('validation.skill_management.question_time_too_long'),
            
            // Matching Algorithm Messages
            'similarity_threshold.max' => __('validation.skill_management.similarity_threshold_invalid'),
            'training_data_size.min' => __('validation.skill_management.insufficient_training_data'),
            'matching_accuracy_target.min' => __('validation.skill_management.accuracy_target_too_low'),
            
            // Development Messages
            'estimated_duration.max' => __('validation.skill_management.learning_duration_too_long'),
            'learning_resources.*.duration_hours.max' => __('validation.skill_management.resource_duration_too_long'),
            'continuing_education_units.max' => __('validation.skill_management.too_many_ceu'),
        ];
    }

    protected function passedValidation(): void
    {
        $this->validateSkillConfiguration();
        $this->optimizeSkillPerformance();
        $this->logSkillActivity();
    }

    private function validateSkillConfiguration(): void
    {
        // Validate skill hierarchy consistency
        if ($this->has(['skill_id', 'parent_skill_id'])) {
            if ($this->skill_id === $this->parent_skill_id) {
                throw new \InvalidArgumentException(__('validation.skill_management.circular_hierarchy'));
            }
        }

        // Validate competency framework weights
        if ($this->has('competency_domains')) {
            $totalWeight = 0;
            foreach ($this->competency_domains as $domain) {
                $totalWeight += $domain['weight'] ?? 0;
            }
            
            if ($totalWeight > 100) {
                throw new \InvalidArgumentException(__('validation.skill_management.domain_weights_exceed_100'));
            }
        }

        // Validate assessment scoring consistency
        if ($this->has(['passing_score', 'question_bank'])) {
            $totalPoints = 0;
            foreach ($this->question_bank as $question) {
                $totalPoints += $question['points'] ?? 0;
            }
            
            $passingPoints = ($this->passing_score / 100) * $totalPoints;
            if ($passingPoints <= 0) {
                throw new \InvalidArgumentException(__('validation.skill_management.invalid_passing_score'));
            }
        }

        // Validate matching algorithm weights
        if ($this->has('weight_configuration')) {
            $totalWeight = array_sum($this->weight_configuration);
            if (abs($totalWeight - 1.0) > 0.01) {
                throw new \InvalidArgumentException(__('validation.skill_management.matching_weights_must_sum_to_one'));
            }
        }
    }

    private function optimizeSkillPerformance(): void
    {
        // Optimize based on skill complexity
        if ($this->has('skill_complexity')) {
            $optimizations = $this->calculateSkillOptimizations($this->skill_complexity);
            
            $this->merge([
                'recommended_assessment_duration' => $optimizations['assessment_duration'],
                'suggested_learning_path_length' => $optimizations['learning_path_length'],
                'optimal_question_count' => $optimizations['question_count']
            ]);
        }

        // Cache skill configuration
        if ($this->has('skill_id')) {
            Cache::remember("skill_config_{$this->skill_id}", 3600, function() {
                return $this->validated();
            });
        }
    }

    private function calculateSkillOptimizations(string $complexity): array
    {
        $optimizations = [
            'basic' => ['assessment_duration' => 30, 'learning_path_length' => 20, 'question_count' => 15],
            'intermediate' => ['assessment_duration' => 60, 'learning_path_length' => 40, 'question_count' => 25],
            'advanced' => ['assessment_duration' => 90, 'learning_path_length' => 60, 'question_count' => 35],
            'expert' => ['assessment_duration' => 120, 'learning_path_length' => 80, 'question_count' => 45],
            'master' => ['assessment_duration' => 180, 'learning_path_length' => 100, 'question_count' => 60]
        ];
        
        return $optimizations[$complexity] ?? $optimizations['intermediate'];
    }

    private function logSkillActivity(): void
    {
        \Log::info('Skill Management Request', [
            'skill_id' => $this->skill_id ?? 'new',
            'operation_type' => $this->getOperationType(),
            'skill_complexity' => $this->skill_complexity ?? 'unknown',
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'timestamp' => now(),
            'optimizations_applied' => $this->has('recommended_assessment_duration')
        ]);
    }

    private function getOperationType(): string
    {
        if ($this->has('skill_name')) return 'skill_taxonomy_management';
        if ($this->has('competency_name')) return 'competency_framework';
        if ($this->has('assessment_name')) return 'skill_assessment';
        if ($this->has('matching_algorithm')) return 'matching_algorithm_config';
        if ($this->has('learning_path_name')) return 'skill_development';
        if ($this->has('certification_name')) return 'certification_management';
        if ($this->has('analytics_enabled')) return 'skill_analytics';
        
        return 'general_skill_operation';
    }
}
