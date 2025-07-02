<?php

namespace App\Http\Requests\Enhanced;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Cache;

class SecurityManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->getAuthenticationRules();
        $rules = array_merge($rules, $this->getAuthorizationRules());
        $rules = array_merge($rules, $this->getEncryptionRules());
        $rules = array_merge($rules, $this->getThreatDetectionRules());
        $rules = array_merge($rules, $this->getComplianceRules());
        $rules = array_merge($rules, $this->getSecurityAuditingRules());
        $rules = array_merge($rules, $this->getAdvancedSecurityRules());
        
        return $rules;
    }

    private function getAuthenticationRules(): array
    {
        return [
            // Multi-Factor Authentication
            'mfa_enabled' => ['nullable', 'boolean'],
            'mfa_methods' => ['nullable', 'array'],
            'mfa_methods.*' => ['string', Rule::in(['sms', 'email', 'totp', 'backup_codes', 'push', 'biometric', 'hardware_token'])],
            'mfa_required_roles' => ['nullable', 'array'],
            'mfa_bypass_allowed' => ['nullable', 'boolean'],
            'mfa_backup_codes_count' => ['nullable', 'integer', 'min:1', 'max:50'],
            'mfa_recovery_enabled' => ['nullable', 'boolean'],
            'mfa_totp_issuer' => ['nullable', 'string', 'max:255'],
            'mfa_totp_algorithm' => ['nullable', 'string', Rule::in(['SHA1', 'SHA256', 'SHA512'])],
            'mfa_session_timeout' => ['nullable', 'integer', 'min:60', 'max:86400'], // seconds
            
            // Single Sign-On
            'sso_enabled' => ['nullable', 'boolean'],
            'sso_providers' => ['nullable', 'array'],
            'sso_providers.*.provider_name' => ['string', 'max:100'],
            'sso_providers.*.provider_type' => ['string', Rule::in(['saml2', 'oauth2', 'openid_connect', 'ldap', 'active_directory'])],
            'sso_providers.*.identity_provider_url' => ['url', 'max:2000'],
            'sso_providers.*.certificate' => ['string'],
            'sso_providers.*.auto_provision' => ['boolean'],
            'sso_just_in_time_provisioning' => ['nullable', 'boolean'],
            'sso_attribute_mapping' => ['nullable', 'array'],
            
            // Password Policies
            'password_policy_enabled' => ['nullable', 'boolean'],
            'password_min_length' => ['nullable', 'integer', 'min:8', 'max:128'],
            'password_require_uppercase' => ['nullable', 'boolean'],
            'password_require_lowercase' => ['nullable', 'boolean'],
            'password_require_numbers' => ['nullable', 'boolean'],
            'password_require_symbols' => ['nullable', 'boolean'],
            'password_max_age_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'password_history_count' => ['nullable', 'integer', 'min:1', 'max:24'],
            'password_lockout_attempts' => ['nullable', 'integer', 'min:3', 'max:20'],
            'password_lockout_duration' => ['nullable', 'integer', 'min:60', 'max:86400'], // seconds
            
            // Session Management
            'session_security_enabled' => ['nullable', 'boolean'],
            'session_timeout' => ['nullable', 'integer', 'min:300', 'max:86400'], // seconds
            'session_concurrent_limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'session_device_tracking' => ['nullable', 'boolean'],
            'session_ip_validation' => ['nullable', 'boolean'],
            'session_encryption_enabled' => ['nullable', 'boolean'],
            'session_regeneration_interval' => ['nullable', 'integer', 'min:300', 'max:3600'], // seconds
            
            // Biometric Authentication
            'biometric_enabled' => ['nullable', 'boolean'],
            'biometric_types' => ['nullable', 'array'],
            'biometric_types.*' => ['string', Rule::in(['fingerprint', 'face_recognition', 'voice_recognition', 'iris_scan', 'palm_print'])],
            'biometric_fallback_enabled' => ['nullable', 'boolean'],
            'biometric_liveness_detection' => ['nullable', 'boolean'],
            'biometric_template_protection' => ['nullable', 'boolean'],
            
            // Advanced Authentication
            'risk_based_authentication' => ['nullable', 'boolean'],
            'device_fingerprinting' => ['nullable', 'boolean'],
            'behavioral_biometrics' => ['nullable', 'boolean'],
            'continuous_authentication' => ['nullable', 'boolean'],
            'adaptive_authentication' => ['nullable', 'boolean'],
            'zero_trust_authentication' => ['nullable', 'boolean'],
        ];
    }

    private function getAuthorizationRules(): array
    {
        return [
            // Role-Based Access Control
            'rbac_enabled' => ['nullable', 'boolean'],
            'rbac_inheritance_enabled' => ['nullable', 'boolean'],
            'rbac_dynamic_roles' => ['nullable', 'boolean'],
            'rbac_role_separation' => ['nullable', 'boolean'],
            'rbac_privilege_escalation_protection' => ['nullable', 'boolean'],
            
            // Attribute-Based Access Control
            'abac_enabled' => ['nullable', 'boolean'],
            'abac_policies' => ['nullable', 'array'],
            'abac_policy_evaluation_mode' => ['nullable', 'string', Rule::in(['permit_overrides', 'deny_overrides', 'first_applicable'])],
            'abac_attribute_sources' => ['nullable', 'array'],
            'abac_real_time_evaluation' => ['nullable', 'boolean'],
            
            // Permission Management
            'permission_inheritance' => ['nullable', 'boolean'],
            'permission_delegation' => ['nullable', 'boolean'],
            'temporary_permissions' => ['nullable', 'boolean'],
            'permission_approval_workflow' => ['nullable', 'boolean'],
            'least_privilege_enforcement' => ['nullable', 'boolean'],
            
            // Access Reviews
            'access_review_enabled' => ['nullable', 'boolean'],
            'access_review_frequency' => ['nullable', 'string', Rule::in(['weekly', 'monthly', 'quarterly', 'annually'])],
            'access_review_auto_revoke' => ['nullable', 'boolean'],
            'access_certification_required' => ['nullable', 'boolean'],
            
            // Just-In-Time Access
            'jit_access_enabled' => ['nullable', 'boolean'],
            'jit_access_approval_required' => ['nullable', 'boolean'],
            'jit_access_max_duration' => ['nullable', 'integer', 'min:300', 'max:86400'], // seconds
            'jit_access_auto_revoke' => ['nullable', 'boolean'],
            
            // Privileged Access Management
            'pam_enabled' => ['nullable', 'boolean'],
            'pam_password_vaulting' => ['nullable', 'boolean'],
            'pam_session_recording' => ['nullable', 'boolean'],
            'pam_dual_control' => ['nullable', 'boolean'],
            'pam_privileged_session_monitoring' => ['nullable', 'boolean'],
        ];
    }

    private function getEncryptionRules(): array
    {
        return [
            // Data Encryption
            'encryption_at_rest_enabled' => ['nullable', 'boolean'],
            'encryption_in_transit_enabled' => ['nullable', 'boolean'],
            'encryption_algorithm' => ['nullable', 'string', Rule::in(['AES-256', 'ChaCha20-Poly1305', 'AES-256-GCM'])],
            'encryption_key_rotation_enabled' => ['nullable', 'boolean'],
            'encryption_key_rotation_interval' => ['nullable', 'integer', 'min:1', 'max:365], // days
            'encryption_hsm_enabled' => ['nullable', 'boolean'],
            
            // Key Management
            'key_management_system' => ['nullable', 'string', Rule::in(['aws_kms', 'azure_key_vault', 'hashicorp_vault', 'custom_hsm'])],
            'key_escrow_enabled' => ['nullable', 'boolean'],
            'key_derivation_function' => ['nullable', 'string', Rule::in(['PBKDF2', 'scrypt', 'Argon2id'])],
            'key_splitting_enabled' => ['nullable', 'boolean'],
            'cryptographic_key_lifecycle' => ['nullable', 'boolean'],
            
            // Advanced Encryption
            'homomorphic_encryption' => ['nullable', 'boolean'],
            'format_preserving_encryption' => ['nullable', 'boolean'],
            'searchable_encryption' => ['nullable', 'boolean'],
            'quantum_resistant_encryption' => ['nullable', 'boolean'],
            'zero_knowledge_proofs' => ['nullable', 'boolean'],
            'secure_multiparty_computation' => ['nullable', 'boolean'],
            
            // Certificate Management
            'pki_enabled' => ['nullable', 'boolean'],
            'certificate_authority' => ['nullable', 'string', 'max:255'],
            'certificate_auto_renewal' => ['nullable', 'boolean'],
            'certificate_transparency_enabled' => ['nullable', 'boolean'],
            'certificate_pinning_enabled' => ['nullable', 'boolean'],
            'ocsp_stapling_enabled' => ['nullable', 'boolean'],
        ];
    }

    private function getThreatDetectionRules(): array
    {
        return [
            // Intrusion Detection
            'ids_enabled' => ['nullable', 'boolean'],
            'ids_signature_based' => ['nullable', 'boolean'],
            'ids_anomaly_based' => ['nullable', 'boolean'],
            'ids_real_time_monitoring' => ['nullable', 'boolean'],
            'ids_auto_response' => ['nullable', 'boolean'],
            'ids_threat_intelligence_integration' => ['nullable', 'boolean'],
            
            // Behavioral Analysis
            'user_behavior_analytics' => ['nullable', 'boolean'],
            'entity_behavior_analytics' => ['nullable', 'boolean'],
            'ml_anomaly_detection' => ['nullable', 'boolean'],
            'behavioral_baseline_learning' => ['nullable', 'boolean'],
            'risk_scoring_enabled' => ['nullable', 'boolean'],
            
            // Security Information and Event Management
            'siem_integration' => ['nullable', 'boolean'],
            'log_correlation_enabled' => ['nullable', 'boolean'],
            'security_orchestration' => ['nullable', 'boolean'],
            'automated_incident_response' => ['nullable', 'boolean'],
            'threat_hunting_enabled' => ['nullable', 'boolean'],
            
            // Vulnerability Management
            'vulnerability_scanning_enabled' => ['nullable', 'boolean'],
            'vulnerability_scan_frequency' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
            'vulnerability_auto_remediation' => ['nullable', 'boolean'],
            'penetration_testing_enabled' => ['nullable', 'boolean'],
            'security_assessment_frequency' => ['nullable', 'string', Rule::in(['quarterly', 'biannually', 'annually'])],
            
            // Fraud Detection
            'fraud_detection_enabled' => ['nullable', 'boolean'],
            'fraud_detection_ml_models' => ['nullable', 'array'],
            'fraud_real_time_scoring' => ['nullable', 'boolean'],
            'fraud_transaction_monitoring' => ['nullable', 'boolean'],
            'fraud_device_profiling' => ['nullable', 'boolean'],
            
            // Threat Intelligence
            'threat_intelligence_feeds' => ['nullable', 'array'],
            'threat_indicator_sharing' => ['nullable', 'boolean'],
            'cyber_threat_hunting' => ['nullable', 'boolean'],
            'dark_web_monitoring' => ['nullable', 'boolean'],
            'brand_protection_monitoring' => ['nullable', 'boolean'],
        ];
    }

    private function getComplianceRules(): array
    {
        return [
            // Regulatory Compliance
            'gdpr_compliance_enabled' => ['nullable', 'boolean'],
            'ccpa_compliance_enabled' => ['nullable', 'boolean'],
            'hipaa_compliance_enabled' => ['nullable', 'boolean'],
            'sox_compliance_enabled' => ['nullable', 'boolean'],
            'iso27001_compliance_enabled' => ['nullable', 'boolean'],
            'pci_dss_compliance_enabled' => ['nullable', 'boolean'],
            'nist_framework_enabled' => ['nullable', 'boolean'],
            
            // Data Protection
            'data_classification_enabled' => ['nullable', 'boolean'],
            'data_loss_prevention' => ['nullable', 'boolean'],
            'data_retention_policies' => ['nullable', 'array'],
            'data_anonymization_enabled' => ['nullable', 'boolean'],
            'right_to_be_forgotten' => ['nullable', 'boolean'],
            'data_portability_enabled' => ['nullable', 'boolean'],
            'consent_management_enabled' => ['nullable', 'boolean'],
            
            // Governance
            'security_governance_framework' => ['nullable', 'string', Rule::in(['cobit', 'itil', 'nist', 'iso27001', 'custom'])],
            'risk_management_framework' => ['nullable', 'boolean'],
            'security_policy_management' => ['nullable', 'boolean'],
            'compliance_reporting_automation' => ['nullable', 'boolean'],
            'regulatory_change_management' => ['nullable', 'boolean'],
            
            // Privacy Engineering
            'privacy_by_design' => ['nullable', 'boolean'],
            'privacy_impact_assessment' => ['nullable', 'boolean'],
            'data_minimization_enforcement' => ['nullable', 'boolean'],
            'purpose_limitation_enforcement' => ['nullable', 'boolean'],
            'privacy_preserving_technologies' => ['nullable', 'boolean'],
        ];
    }

    private function getSecurityAuditingRules(): array
    {
        return [
            // Audit Logging
            'comprehensive_audit_logging' => ['nullable', 'boolean'],
            'audit_log_integrity_protection' => ['nullable', 'boolean'],
            'audit_log_encryption' => ['nullable', 'boolean'],
            'audit_log_retention_period' => ['nullable', 'integer', 'min:30', 'max:2555], // days
            'audit_log_immutability' => ['nullable', 'boolean'],
            'audit_trail_correlation' => ['nullable', 'boolean'],
            
            // Security Monitoring
            'real_time_security_monitoring' => ['nullable', 'boolean'],
            'security_dashboard_enabled' => ['nullable', 'boolean'],
            'security_metrics_collection' => ['nullable', 'boolean'],
            'security_kpi_tracking' => ['nullable', 'boolean'],
            'security_alerting_enabled' => ['nullable', 'boolean'],
            
            // Incident Management
            'incident_response_automation' => ['nullable', 'boolean'],
            'incident_classification_system' => ['nullable', 'boolean'],
            'incident_escalation_procedures' => ['nullable', 'boolean'],
            'forensic_evidence_collection' => ['nullable', 'boolean'],
            'chain_of_custody_management' => ['nullable', 'boolean'],
            
            // Compliance Auditing
            'automated_compliance_checking' => ['nullable', 'boolean'],
            'compliance_evidence_collection' => ['nullable', 'boolean'],
            'regulatory_reporting_automation' => ['nullable', 'boolean'],
            'audit_trail_generation' => ['nullable', 'boolean'],
            'control_effectiveness_testing' => ['nullable', 'boolean'],
        ];
    }

    private function getAdvancedSecurityRules(): array
    {
        return [
            // AI-Powered Security
            'ai_threat_detection' => ['nullable', 'boolean'],
            'machine_learning_security' => ['nullable', 'boolean'],
            'predictive_security_analytics' => ['nullable', 'boolean'],
            'autonomous_security_response' => ['nullable', 'boolean'],
            'neural_network_anomaly_detection' => ['nullable', 'boolean'],
            
            // Zero Trust Architecture
            'zero_trust_network_access' => ['nullable', 'boolean'],
            'micro_segmentation' => ['nullable', 'boolean'],
            'software_defined_perimeter' => ['nullable', 'boolean'],
            'identity_centric_security' => ['nullable', 'boolean'],
            'continuous_verification' => ['nullable', 'boolean'],
            
            // Cloud Security
            'cloud_security_posture_management' => ['nullable', 'boolean'],
            'cloud_workload_protection' => ['nullable', 'boolean'],
            'container_security_enabled' => ['nullable', 'boolean'],
            'serverless_security_enabled' => ['nullable', 'boolean'],
            'multi_cloud_security_management' => ['nullable', 'boolean'],
            
            // DevSecOps Integration
            'security_as_code' => ['nullable', 'boolean'],
            'automated_security_testing' => ['nullable', 'boolean'],
            'continuous_security_monitoring' => ['nullable', 'boolean'],
            'shift_left_security' => ['nullable', 'boolean'],
            'security_pipeline_integration' => ['nullable', 'boolean'],
            
            // Emerging Technologies
            'blockchain_security_integration' => ['nullable', 'boolean'],
            'quantum_cryptography' => ['nullable', 'boolean'],
            'hardware_security_modules' => ['nullable', 'boolean'],
            'secure_enclaves' => ['nullable', 'boolean'],
            'confidential_computing' => ['nullable', 'boolean'],
            'homomorphic_encryption_processing' => ['nullable', 'boolean'],
            
            // Business Continuity
            'disaster_recovery_security' => ['nullable', 'boolean'],
            'security_backup_validation' => ['nullable', 'boolean'],
            'crisis_management_security' => ['nullable', 'boolean'],
            'supply_chain_security' => ['nullable', 'boolean'],
            'third_party_risk_management' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'password_min_length.min' => __('validation.security_management.password_too_short'),
            'session_timeout.min' => __('validation.security_management.session_timeout_too_short'),
            'mfa_backup_codes_count.max' => __('validation.security_management.too_many_backup_codes'),
            'encryption_key_rotation_interval.max' => __('validation.security_management.rotation_interval_too_long'),
            'audit_log_retention_period.min' => __('validation.security_management.retention_too_short'),
        ];
    }

    protected function passedValidation(): void
    {
        $this->validateSecurityConfiguration();
        $this->optimizeSecuritySettings();
        $this->logSecurityActivity();
    }

    private function validateSecurityConfiguration(): void
    {
        // Validate MFA configuration
        if ($this->mfa_enabled && empty($this->mfa_methods)) {
            throw new \InvalidArgumentException(__('validation.security_management.mfa_methods_required'));
        }

        // Validate encryption consistency
        if ($this->encryption_key_rotation_enabled && !$this->encryption_at_rest_enabled) {
            throw new \InvalidArgumentException(__('validation.security_management.key_rotation_requires_encryption'));
        }

        // Validate SSO configuration
        if ($this->sso_enabled && empty($this->sso_providers)) {
            throw new \InvalidArgumentException(__('validation.security_management.sso_providers_required'));
        }
    }

    private function optimizeSecuritySettings(): void
    {
        if ($this->has('risk_based_authentication') && $this->risk_based_authentication) {
            $this->merge(['recommended_mfa_methods' => ['totp', 'push', 'biometric']]);
        }

        Cache::remember("security_config_" . request()->ip(), 1800, function() {
            return $this->validated();
        });
    }

    private function logSecurityActivity(): void
    {
        \Log::info('Security Management Request', [
            'operation_type' => $this->getSecurityOperationType(),
            'security_level' => $this->calculateSecurityLevel(),
            'compliance_frameworks' => $this->getEnabledComplianceFrameworks(),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);
    }

    private function getSecurityOperationType(): string
    {
        if ($this->has('mfa_enabled')) return 'authentication_management';
        if ($this->has('rbac_enabled')) return 'authorization_management';
        if ($this->has('encryption_at_rest_enabled')) return 'encryption_management';
        if ($this->has('ids_enabled')) return 'threat_detection';
        if ($this->has('gdpr_compliance_enabled')) return 'compliance_management';
        if ($this->has('comprehensive_audit_logging')) return 'audit_management';
        if ($this->has('ai_threat_detection')) return 'advanced_security';
        
        return 'general_security_operation';
    }

    private function calculateSecurityLevel(): string
    {
        $score = 0;
        
        if ($this->mfa_enabled) $score += 20;
        if ($this->encryption_at_rest_enabled) $score += 15;
        if ($this->zero_trust_network_access) $score += 25;
        if ($this->ai_threat_detection) $score += 20;
        if ($this->comprehensive_audit_logging) $score += 10;
        if ($this->gdpr_compliance_enabled) $score += 10;
        
        return match(true) {
            $score >= 80 => 'enterprise_grade',
            $score >= 60 => 'high_security',
            $score >= 40 => 'standard_security',
            default => 'basic_security'
        };
    }

    private function getEnabledComplianceFrameworks(): array
    {
        $frameworks = [];
        
        if ($this->gdpr_compliance_enabled) $frameworks[] = 'GDPR';
        if ($this->hipaa_compliance_enabled) $frameworks[] = 'HIPAA';
        if ($this->sox_compliance_enabled) $frameworks[] = 'SOX';
        if ($this->iso27001_compliance_enabled) $frameworks[] = 'ISO27001';
        if ($this->pci_dss_compliance_enabled) $frameworks[] = 'PCI-DSS';
        if ($this->nist_framework_enabled) $frameworks[] = 'NIST';
        
        return $frameworks;
    }
}
