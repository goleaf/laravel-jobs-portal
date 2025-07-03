<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class DeepRelationshipAnalysisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->getBaseAnalysisRules();
        $rules = array_merge($rules, $this->getJobCandidateMatchingRules());
        $rules = array_merge($rules, $this->getSkillMappingRules());
        $rules = array_merge($rules, $this->getCompanyAnalysisRules());
        $rules = array_merge($rules, $this->getNetworkAnalysisRules());
        $rules = array_merge($rules, $this->getMLAlgorithmRules());
        $rules = array_merge($rules, $this->getPredictiveAnalyticsRules());
        $rules = array_merge($rules, $this->getGraphDatabaseRules());
        $rules = array_merge($rules, $this->getPerformanceOptimizationRules());
        $rules = array_merge($rules, $this->getAdvancedFeaturesRules());

        return $rules;
    }

    private function getBaseAnalysisRules(): array
    {
        return [
            // Core Analysis Parameters
            'analysis_type' => ['required', 'string', Rule::in(['job_candidate_matching', 'skill_mapping', 'company_analysis', 'network_analysis', 'predictive_modeling', 'relationship_discovery'])],
            'analysis_depth' => ['nullable', 'string', Rule::in(['shallow', 'medium', 'deep', 'comprehensive', 'exhaustive'])],
            'analysis_scope' => ['nullable', 'string', Rule::in(['local', 'regional', 'national', 'international', 'global'])],
            'analysis_timeframe' => ['nullable', 'string', Rule::in(['real_time', 'last_24h', 'last_week', 'last_month', 'last_quarter', 'last_year', 'historical'])],
            'confidence_threshold' => ['nullable', 'numeric', 'min:0.1', 'max:1.0'],
            'similarity_threshold' => ['nullable', 'numeric', 'min:0.0', 'max:1.0'],
            'max_results' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'output_format' => ['nullable', 'string', Rule::in(['json', 'graph', 'matrix', 'hierarchical', 'network'])],

            // Data Source Configuration
            'data_sources' => ['nullable', 'array'],
            'data_sources.*' => ['string', Rule::in(['internal_db', 'external_apis', 'social_networks', 'public_datasets', 'third_party_integrations'])],
            'include_external_data' => ['nullable', 'boolean'],
            'real_time_updates' => ['nullable', 'boolean'],
            'historical_data_included' => ['nullable', 'boolean'],
            'cross_platform_analysis' => ['nullable', 'boolean'],

            // Quality Control
            'data_quality_threshold' => ['nullable', 'numeric', 'min:0.1', 'max:1.0'],
            'outlier_detection' => ['nullable', 'boolean'],
            'noise_reduction' => ['nullable', 'boolean'],
            'bias_mitigation' => ['nullable', 'boolean'],
            'fairness_constraints' => ['nullable', 'array'],
            'privacy_compliance' => ['nullable', 'boolean'],
            'gdpr_compliance' => ['nullable', 'boolean'],
        ];
    }

    private function getJobCandidateMatchingRules(): array
    {
        return [
            // Job Candidate Matching Configuration
            'job_ids' => ['nullable', 'array', 'max:1000'],
            'job_ids.*' => ['integer', 'exists:jobs,id'],
            'candidate_ids' => ['nullable', 'array', 'max:5000'],
            'candidate_ids.*' => ['integer', 'exists:candidates,id'],
            'company_ids' => ['nullable', 'array', 'max:500'],
            'company_ids.*' => ['integer', 'exists:companies,id'],

            // Matching Algorithms
            'matching_algorithm' => ['nullable', 'string', Rule::in(['cosine_similarity', 'euclidean_distance', 'jaccard_index', 'neural_network', 'random_forest', 'gradient_boosting', 'hybrid'])],
            'skill_matching_weight' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'experience_matching_weight' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'location_matching_weight' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'salary_matching_weight' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'education_matching_weight' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'culture_fit_weight' => ['nullable', 'numeric', 'min:0', 'max:1'],

            // Advanced Matching Features
            'semantic_matching' => ['nullable', 'boolean'],
            'contextual_understanding' => ['nullable', 'boolean'],
            'behavioral_analysis' => ['nullable', 'boolean'],
            'personality_matching' => ['nullable', 'boolean'],
            'team_compatibility' => ['nullable', 'boolean'],
            'growth_potential_analysis' => ['nullable', 'boolean'],
            'career_trajectory_matching' => ['nullable', 'boolean'],
            'adaptive_learning' => ['nullable', 'boolean'],

            // Matching Constraints
            'minimum_match_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'maximum_distance_km' => ['nullable', 'integer', 'min:0', 'max:50000'],
            'salary_tolerance_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'experience_level_flexibility' => ['nullable', 'integer', 'min:0', 'max:10'],
            'skill_gap_tolerance' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'cultural_fit_importance' => ['nullable', 'string', Rule::in(['low', 'medium', 'high', 'critical'])],

            // Time-based Matching
            'availability_matching' => ['nullable', 'boolean'],
            'start_date_preference' => ['nullable', 'date'],
            'urgency_level' => ['nullable', 'string', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'time_zone_compatibility' => ['nullable', 'boolean'],
            'work_schedule_matching' => ['nullable', 'boolean'],
        ];
    }

    private function getSkillMappingRules(): array
    {
        return [
            // Skill Analysis Configuration
            'skill_ids' => ['nullable', 'array', 'max:2000'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'skill_categories' => ['nullable', 'array'],
            'skill_categories.*' => ['string', 'max:100'],
            'skill_levels' => ['nullable', 'array'],
            'skill_levels.*' => ['string', Rule::in(['beginner', 'intermediate', 'advanced', 'expert', 'master'])],

            // Skill Mapping Algorithms
            'skill_clustering_algorithm' => ['nullable', 'string', Rule::in(['k_means', 'hierarchical', 'dbscan', 'gaussian_mixture', 'spectral'])],
            'skill_similarity_method' => ['nullable', 'string', Rule::in(['word2vec', 'glove', 'bert', 'tf_idf', 'doc2vec', 'sentence_transformers'])],
            'skill_taxonomy_source' => ['nullable', 'string', Rule::in(['internal', 'onet', 'linkedin', 'indeed', 'custom'])],
            'skill_evolution_tracking' => ['nullable', 'boolean'],
            'emerging_skills_detection' => ['nullable', 'boolean'],
            'obsolete_skills_identification' => ['nullable', 'boolean'],

            // Skill Relationships
            'prerequisite_analysis' => ['nullable', 'boolean'],
            'complementary_skills_discovery' => ['nullable', 'boolean'],
            'skill_progression_paths' => ['nullable', 'boolean'],
            'transferable_skills_analysis' => ['nullable', 'boolean'],
            'skill_demand_forecasting' => ['nullable', 'boolean'],
            'skill_gap_analysis' => ['nullable', 'boolean'],
            'skill_market_trends' => ['nullable', 'boolean'],

            // Industry-Specific Mapping
            'industry_skill_mapping' => ['nullable', 'boolean'],
            'role_specific_skills' => ['nullable', 'boolean'],
            'seniority_skill_correlation' => ['nullable', 'boolean'],
            'geographic_skill_variations' => ['nullable', 'boolean'],
            'remote_work_skill_relevance' => ['nullable', 'boolean'],

            // Skill Validation
            'skill_verification_sources' => ['nullable', 'array'],
            'skill_verification_sources.*' => ['string', Rule::in(['certifications', 'education', 'experience', 'assessments', 'peer_review'])],
            'confidence_scoring' => ['nullable', 'boolean'],
            'skill_recency_factor' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'skill_usage_frequency' => ['nullable', 'boolean'],
            'skill_proficiency_assessment' => ['nullable', 'boolean'],
        ];
    }

    private function getCompanyAnalysisRules(): array
    {
        return [
            // Company Analysis Parameters
            'company_analysis_depth' => ['nullable', 'string', Rule::in(['basic', 'comprehensive', 'competitive', 'market_position'])],
            'competitor_analysis' => ['nullable', 'boolean'],
            'market_positioning' => ['nullable', 'boolean'],
            'growth_trajectory' => ['nullable', 'boolean'],
            'talent_acquisition_patterns' => ['nullable', 'boolean'],
            'company_culture_analysis' => ['nullable', 'boolean'],
            'employee_satisfaction_metrics' => ['nullable', 'boolean'],
            'retention_rate_analysis' => ['nullable', 'boolean'],

            // Company Clustering
            'company_clustering_method' => ['nullable', 'string', Rule::in(['size_based', 'industry_based', 'location_based', 'culture_based', 'performance_based'])],
            'similar_companies_analysis' => ['nullable', 'boolean'],
            'company_network_analysis' => ['nullable', 'boolean'],
            'partnership_discovery' => ['nullable', 'boolean'],
            'acquisition_target_identification' => ['nullable', 'boolean'],

            // Performance Metrics
            'hiring_success_rate' => ['nullable', 'boolean'],
            'time_to_hire_analysis' => ['nullable', 'boolean'],
            'cost_per_hire_optimization' => ['nullable', 'boolean'],
            'candidate_experience_scoring' => ['nullable', 'boolean'],
            'employer_brand_strength' => ['nullable', 'boolean'],
            'recruitment_channel_effectiveness' => ['nullable', 'boolean'],

            // Market Intelligence
            'salary_benchmarking' => ['nullable', 'boolean'],
            'benefits_comparison' => ['nullable', 'boolean'],
            'work_life_balance_metrics' => ['nullable', 'boolean'],
            'diversity_inclusion_metrics' => ['nullable', 'boolean'],
            'innovation_index' => ['nullable', 'boolean'],
            'market_share_analysis' => ['nullable', 'boolean'],
            'financial_health_indicators' => ['nullable', 'boolean'],

            // Risk Assessment
            'company_stability_score' => ['nullable', 'boolean'],
            'layoff_risk_prediction' => ['nullable', 'boolean'],
            'expansion_opportunity_analysis' => ['nullable', 'boolean'],
            'regulatory_compliance_status' => ['nullable', 'boolean'],
            'reputation_risk_assessment' => ['nullable', 'boolean'],
        ];
    }

    private function getNetworkAnalysisRules(): array
    {
        return [
            // Network Analysis Configuration
            'network_type' => ['nullable', 'string', Rule::in(['professional', 'skill_based', 'company_based', 'geographic', 'industry', 'hybrid'])],
            'graph_algorithms' => ['nullable', 'array'],
            'graph_algorithms.*' => ['string', Rule::in(['pagerank', 'betweenness_centrality', 'closeness_centrality', 'eigenvector_centrality', 'community_detection', 'shortest_path'])],
            'community_detection_algorithm' => ['nullable', 'string', Rule::in(['louvain', 'leiden', 'girvan_newman', 'label_propagation', 'spectral'])],

            // Network Metrics
            'centrality_measures' => ['nullable', 'boolean'],
            'influence_scoring' => ['nullable', 'boolean'],
            'network_density_analysis' => ['nullable', 'boolean'],
            'clustering_coefficient' => ['nullable', 'boolean'],
            'small_world_properties' => ['nullable', 'boolean'],
            'scale_free_analysis' => ['nullable', 'boolean'],

            // Relationship Discovery
            'hidden_connections' => ['nullable', 'boolean'],
            'weak_ties_analysis' => ['nullable', 'boolean'],
            'bridge_identification' => ['nullable', 'boolean'],
            'gatekeeper_detection' => ['nullable', 'boolean'],
            'influence_propagation' => ['nullable', 'boolean'],
            'network_evolution_tracking' => ['nullable', 'boolean'],

            // Dynamic Network Analysis
            'temporal_network_analysis' => ['nullable', 'boolean'],
            'network_growth_patterns' => ['nullable', 'boolean'],
            'relationship_lifecycle' => ['nullable', 'boolean'],
            'network_resilience' => ['nullable', 'boolean'],
            'cascading_effects_modeling' => ['nullable', 'boolean'],

            // Professional Networks
            'referral_network_analysis' => ['nullable', 'boolean'],
            'mentorship_relationships' => ['nullable', 'boolean'],
            'collaboration_patterns' => ['nullable', 'boolean'],
            'knowledge_transfer_paths' => ['nullable', 'boolean'],
            'career_progression_networks' => ['nullable', 'boolean'],
            'industry_mobility_analysis' => ['nullable', 'boolean'],
        ];
    }

    private function getMLAlgorithmRules(): array
    {
        return [
            // Machine Learning Configuration
            'ml_model_type' => ['nullable', 'string', Rule::in(['supervised', 'unsupervised', 'reinforcement', 'deep_learning', 'ensemble', 'hybrid'])],
            'algorithm_selection' => ['nullable', 'array'],
            'algorithm_selection.*' => ['string', Rule::in(['random_forest', 'gradient_boosting', 'neural_network', 'svm', 'logistic_regression', 'naive_bayes', 'xgboost', 'lightgbm'])],
            'feature_engineering' => ['nullable', 'boolean'],
            'automated_feature_selection' => ['nullable', 'boolean'],
            'hyperparameter_optimization' => ['nullable', 'boolean'],

            // Model Training
            'training_data_size' => ['nullable', 'integer', 'min:100', 'max:1000000'],
            'validation_split' => ['nullable', 'numeric', 'min:0.1', 'max:0.5'],
            'cross_validation_folds' => ['nullable', 'integer', 'min:3', 'max:10'],
            'early_stopping' => ['nullable', 'boolean'],
            'learning_rate' => ['nullable', 'numeric', 'min:0.0001', 'max:1.0'],
            'batch_size' => ['nullable', 'integer', 'min:16', 'max:1024'],
            'max_epochs' => ['nullable', 'integer', 'min:10', 'max:1000'],

            // Model Evaluation
            'evaluation_metrics' => ['nullable', 'array'],
            'evaluation_metrics.*' => ['string', Rule::in(['accuracy', 'precision', 'recall', 'f1_score', 'auc_roc', 'mse', 'mae', 'r2_score'])],
            'performance_threshold' => ['nullable', 'numeric', 'min:0.1', 'max:1.0'],
            'model_interpretability' => ['nullable', 'boolean'],
            'explainable_ai' => ['nullable', 'boolean'],
            'bias_detection' => ['nullable', 'boolean'],
            'fairness_metrics' => ['nullable', 'array'],

            // Deep Learning Specific
            'neural_network_architecture' => ['nullable', 'string', Rule::in(['feedforward', 'cnn', 'rnn', 'lstm', 'gru', 'transformer', 'autoencoder', 'gan'])],
            'hidden_layers' => ['nullable', 'integer', 'min:1', 'max:50'],
            'neurons_per_layer' => ['nullable', 'array'],
            'neurons_per_layer.*' => ['integer', 'min:8', 'max:2048'],
            'activation_functions' => ['nullable', 'array'],
            'activation_functions.*' => ['string', Rule::in(['relu', 'sigmoid', 'tanh', 'leaky_relu', 'swish', 'gelu'])],
            'dropout_rate' => ['nullable', 'numeric', 'min:0', 'max:0.8'],
            'regularization_strength' => ['nullable', 'numeric', 'min:0', 'max:1'],

            // Transfer Learning
            'transfer_learning' => ['nullable', 'boolean'],
            'pretrained_model' => ['nullable', 'string', 'max:255'],
            'fine_tuning_layers' => ['nullable', 'integer', 'min:1', 'max:20'],
            'domain_adaptation' => ['nullable', 'boolean'],
        ];
    }

    private function getPredictiveAnalyticsRules(): array
    {
        return [
            // Prediction Configuration
            'prediction_horizon' => ['nullable', 'string', Rule::in(['1_week', '1_month', '3_months', '6_months', '1_year', '2_years'])],
            'prediction_confidence' => ['nullable', 'numeric', 'min:0.5', 'max:0.99'],
            'prediction_interval' => ['nullable', 'boolean'],
            'scenario_analysis' => ['nullable', 'boolean'],
            'monte_carlo_simulations' => ['nullable', 'integer', 'min:100', 'max:10000'],

            // Career Trajectory Prediction
            'career_path_prediction' => ['nullable', 'boolean'],
            'salary_progression_forecast' => ['nullable', 'boolean'],
            'skill_demand_prediction' => ['nullable', 'boolean'],
            'job_market_trends' => ['nullable', 'boolean'],
            'industry_growth_prediction' => ['nullable', 'boolean'],
            'role_evolution_forecast' => ['nullable', 'boolean'],

            // Company Predictions
            'hiring_demand_forecast' => ['nullable', 'boolean'],
            'turnover_prediction' => ['nullable', 'boolean'],
            'company_growth_forecast' => ['nullable', 'boolean'],
            'market_expansion_prediction' => ['nullable', 'boolean'],
            'competitive_threat_analysis' => ['nullable', 'boolean'],

            // Economic Indicators
            'economic_impact_modeling' => ['nullable', 'boolean'],
            'recession_impact_prediction' => ['nullable', 'boolean'],
            'automation_impact_forecast' => ['nullable', 'boolean'],
            'remote_work_trend_analysis' => ['nullable', 'boolean'],
            'demographic_shift_impact' => ['nullable', 'boolean'],

            // Risk Modeling
            'risk_assessment_models' => ['nullable', 'array'],
            'risk_assessment_models.*' => ['string', Rule::in(['market_risk', 'operational_risk', 'talent_risk', 'technology_risk', 'regulatory_risk'])],
            'stress_testing' => ['nullable', 'boolean'],
            'sensitivity_analysis' => ['nullable', 'boolean'],
            'early_warning_systems' => ['nullable', 'boolean'],
        ];
    }

    private function getGraphDatabaseRules(): array
    {
        return [
            // Graph Database Configuration
            'graph_database_type' => ['nullable', 'string', Rule::in(['neo4j', 'arangodb', 'orientdb', 'tigergraph', 'amazon_neptune'])],
            'graph_traversal_depth' => ['nullable', 'integer', 'min:1', 'max:10'],
            'max_graph_nodes' => ['nullable', 'integer', 'min:100', 'max:1000000'],
            'graph_caching' => ['nullable', 'boolean'],
            'distributed_processing' => ['nullable', 'boolean'],

            // Graph Queries
            'cypher_queries' => ['nullable', 'array'],
            'cypher_queries.*' => ['string', 'max:2000'],
            'graph_patterns' => ['nullable', 'array'],
            'path_finding_algorithms' => ['nullable', 'array'],
            'path_finding_algorithms.*' => ['string', Rule::in(['shortest_path', 'all_paths', 'dijkstra', 'a_star', 'breadth_first', 'depth_first'])],

            // Relationship Types
            'relationship_types' => ['nullable', 'array'],
            'relationship_types.*' => ['string', Rule::in(['WORKS_FOR', 'HAS_SKILL', 'APPLIED_TO', 'REQUIRES_SKILL', 'SIMILAR_TO', 'COLLABORATED_WITH', 'MENTORED_BY', 'REFERRED_BY'])],
            'relationship_weights' => ['nullable', 'boolean'],
            'temporal_relationships' => ['nullable', 'boolean'],
            'multi_dimensional_relationships' => ['nullable', 'boolean'],

            // Graph Analytics
            'graph_statistics' => ['nullable', 'boolean'],
            'subgraph_extraction' => ['nullable', 'boolean'],
            'graph_matching' => ['nullable', 'boolean'],
            'pattern_mining' => ['nullable', 'boolean'],
            'anomaly_detection_in_graphs' => ['nullable', 'boolean'],
            'graph_neural_networks' => ['nullable', 'boolean'],
        ];
    }

    private function getPerformanceOptimizationRules(): array
    {
        return [
            // Performance Configuration
            'parallel_processing' => ['nullable', 'boolean'],
            'max_concurrent_threads' => ['nullable', 'integer', 'min:1', 'max:100'],
            'memory_optimization' => ['nullable', 'boolean'],
            'cpu_optimization' => ['nullable', 'boolean'],
            'gpu_acceleration' => ['nullable', 'boolean'],
            'distributed_computing' => ['nullable', 'boolean'],

            // Caching Strategy
            'result_caching' => ['nullable', 'boolean'],
            'cache_duration_minutes' => ['nullable', 'integer', 'min:5', 'max:1440'],
            'incremental_updates' => ['nullable', 'boolean'],
            'lazy_loading' => ['nullable', 'boolean'],
            'data_pagination' => ['nullable', 'boolean'],
            'streaming_results' => ['nullable', 'boolean'],

            // Resource Management
            'memory_limit_gb' => ['nullable', 'integer', 'min:1', 'max:64'],
            'processing_timeout_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'max_api_calls_per_minute' => ['nullable', 'integer', 'min:10', 'max:1000'],
            'rate_limiting' => ['nullable', 'boolean'],
            'queue_processing' => ['nullable', 'boolean'],
            'background_jobs' => ['nullable', 'boolean'],

            // Monitoring and Logging
            'performance_monitoring' => ['nullable', 'boolean'],
            'detailed_logging' => ['nullable', 'boolean'],
            'execution_profiling' => ['nullable', 'boolean'],
            'resource_usage_tracking' => ['nullable', 'boolean'],
            'error_tracking' => ['nullable', 'boolean'],
            'metrics_collection' => ['nullable', 'boolean'],
        ];
    }

    private function getAdvancedFeaturesRules(): array
    {
        return [
            // AI-Powered Features
            'natural_language_processing' => ['nullable', 'boolean'],
            'sentiment_analysis' => ['nullable', 'boolean'],
            'entity_recognition' => ['nullable', 'boolean'],
            'text_summarization' => ['nullable', 'boolean'],
            'automated_insights' => ['nullable', 'boolean'],
            'recommendation_engine' => ['nullable', 'boolean'],
            'personalization' => ['nullable', 'boolean'],

            // Real-time Features
            'real_time_analysis' => ['nullable', 'boolean'],
            'streaming_analytics' => ['nullable', 'boolean'],
            'live_dashboard_updates' => ['nullable', 'boolean'],
            'webhook_notifications' => ['nullable', 'boolean'],
            'push_notifications' => ['nullable', 'boolean'],
            'alert_system' => ['nullable', 'boolean'],

            // Visualization Features
            'interactive_visualizations' => ['nullable', 'boolean'],
            'network_diagrams' => ['nullable', 'boolean'],
            'heat_maps' => ['nullable', 'boolean'],
            'trend_charts' => ['nullable', 'boolean'],
            'comparison_matrices' => ['nullable', 'boolean'],
            'geographic_visualizations' => ['nullable', 'boolean'],

            // Export and Integration
            'export_formats' => ['nullable', 'array'],
            'export_formats.*' => ['string', Rule::in(['json', 'csv', 'xlsx', 'pdf', 'png', 'svg', 'html'])],
            'api_integrations' => ['nullable', 'array'],
            'webhook_urls' => ['nullable', 'array'],
            'webhook_urls.*' => ['url', 'max:255'],
            'third_party_sync' => ['nullable', 'boolean'],
            'automated_reporting' => ['nullable', 'boolean'],

            // Collaborative Features
            'shared_analysis' => ['nullable', 'boolean'],
            'team_collaboration' => ['nullable', 'boolean'],
            'annotation_system' => ['nullable', 'boolean'],
            'version_control' => ['nullable', 'boolean'],
            'access_control' => ['nullable', 'boolean'],
            'audit_trail' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            // Base Analysis Messages
            'analysis_type.required' => __('validation.deep_relationship.analysis_type_required'),
            'analysis_type.in' => __('validation.deep_relationship.analysis_type_invalid'),
            'confidence_threshold.numeric' => __('validation.deep_relationship.confidence_threshold_numeric'),
            'max_results.max' => __('validation.deep_relationship.max_results_exceeded'),

            // Job Candidate Matching Messages
            'job_ids.max' => __('validation.deep_relationship.job_ids_limit_exceeded'),
            'candidate_ids.max' => __('validation.deep_relationship.candidate_ids_limit_exceeded'),
            'matching_algorithm.in' => __('validation.deep_relationship.matching_algorithm_invalid'),
            'minimum_match_score.max' => __('validation.deep_relationship.match_score_invalid'),

            // Skill Mapping Messages
            'skill_ids.max' => __('validation.deep_relationship.skill_ids_limit_exceeded'),
            'skill_clustering_algorithm.in' => __('validation.deep_relationship.clustering_algorithm_invalid'),
            'skill_similarity_method.in' => __('validation.deep_relationship.similarity_method_invalid'),

            // ML Algorithm Messages
            'training_data_size.min' => __('validation.deep_relationship.training_data_insufficient'),
            'learning_rate.min' => __('validation.deep_relationship.learning_rate_too_low'),
            'hidden_layers.max' => __('validation.deep_relationship.hidden_layers_exceeded'),

            // Performance Messages
            'memory_limit_gb.max' => __('validation.deep_relationship.memory_limit_exceeded'),
            'processing_timeout_minutes.max' => __('validation.deep_relationship.timeout_exceeded'),
            'max_concurrent_threads.max' => __('validation.deep_relationship.thread_limit_exceeded'),
        ];
    }

    protected function passedValidation(): void
    {
        // Enhanced security and performance optimizations
        $this->validateAnalysisConfiguration();
        $this->optimizePerformance();
        $this->logAnalysisActivity();
    }

    private function validateAnalysisConfiguration(): void
    {
        // Validate weight consistency for matching algorithms
        $weights = [
            'skill_matching_weight',
            'experience_matching_weight',
            'location_matching_weight',
            'salary_matching_weight',
            'education_matching_weight',
            'culture_fit_weight',
        ];

        $totalWeight = 0;
        foreach ($weights as $weight) {
            if ($this->has($weight)) {
                $totalWeight += $this->input($weight);
            }
        }

        if ($totalWeight > 0 && abs($totalWeight - 1.0) > 0.01) {
            throw new \InvalidArgumentException(__('validation.deep_relationship.weights_sum_invalid'));
        }

        // Validate ML configuration consistency
        if ($this->has(['ml_model_type', 'algorithm_selection'])) {
            $modelType = $this->ml_model_type;
            $algorithms = $this->algorithm_selection;

            $compatibleAlgorithms = $this->getCompatibleAlgorithms($modelType);
            $invalidAlgorithms = array_diff($algorithms, $compatibleAlgorithms);

            if (! empty($invalidAlgorithms)) {
                throw new \InvalidArgumentException(__('validation.deep_relationship.incompatible_algorithms'));
            }
        }
    }

    private function getCompatibleAlgorithms(string $modelType): array
    {
        $compatibility = [
            'supervised' => ['random_forest', 'gradient_boosting', 'svm', 'logistic_regression', 'neural_network'],
            'unsupervised' => ['k_means', 'hierarchical', 'dbscan', 'gaussian_mixture'],
            'deep_learning' => ['neural_network', 'cnn', 'rnn', 'lstm', 'transformer'],
            'ensemble' => ['random_forest', 'gradient_boosting', 'xgboost', 'lightgbm'],
        ];

        return $compatibility[$modelType] ?? [];
    }

    private function optimizePerformance(): void
    {
        // Cache frequently accessed analysis configurations
        if ($this->has('analysis_type')) {
            Cache::remember("analysis_config_{$this->analysis_type}", 1800, function () {
                return $this->validated();
            });
        }

        // Optimize resource allocation based on analysis depth
        if ($this->has('analysis_depth')) {
            $resourceMultiplier = $this->getResourceMultiplier($this->analysis_depth);

            $this->merge([
                'optimized_memory_limit' => ($this->memory_limit_gb ?? 4) * $resourceMultiplier,
                'optimized_thread_count' => min(($this->max_concurrent_threads ?? 4) * $resourceMultiplier, 50),
                'estimated_processing_time' => $this->estimateProcessingTime(),
            ]);
        }
    }

    private function getResourceMultiplier(string $depth): float
    {
        $multipliers = [
            'shallow' => 0.5,
            'medium' => 1.0,
            'deep' => 2.0,
            'comprehensive' => 3.0,
            'exhaustive' => 5.0,
        ];

        return $multipliers[$depth] ?? 1.0;
    }

    private function estimateProcessingTime(): int
    {
        $baseTime = 60; // seconds
        $depthMultiplier = $this->getResourceMultiplier($this->analysis_depth ?? 'medium');
        $dataMultiplier = $this->getDataSizeMultiplier();

        return (int) ($baseTime * $depthMultiplier * $dataMultiplier);
    }

    private function getDataSizeMultiplier(): float
    {
        $totalRecords = 0;
        $totalRecords += count($this->job_ids ?? []);
        $totalRecords += count($this->candidate_ids ?? []);
        $totalRecords += count($this->skill_ids ?? []);

        if ($totalRecords < 100) {
            return 0.5;
        }
        if ($totalRecords < 1000) {
            return 1.0;
        }
        if ($totalRecords < 5000) {
            return 2.0;
        }

        return 3.0;
    }

    private function logAnalysisActivity(): void
    {
        // Comprehensive audit logging for relationship analysis
        \Log::info('Deep Relationship Analysis Request', [
            'analysis_type' => $this->analysis_type,
            'analysis_depth' => $this->analysis_depth ?? 'medium',
            'data_scope' => [
                'jobs' => count($this->job_ids ?? []),
                'candidates' => count($this->candidate_ids ?? []),
                'skills' => count($this->skill_ids ?? []),
            ],
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'timestamp' => now(),
            'estimated_processing_time' => $this->estimated_processing_time ?? 60,
        ]);
    }
}
