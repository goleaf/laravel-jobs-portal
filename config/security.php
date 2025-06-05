<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | job portal application. These settings control authentication,
    | authorization, rate limiting, and other security features.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Authentication Security
    |--------------------------------------------------------------------------
    */

    'authentication' => [
        // Maximum failed login attempts before account lockout
        'max_failed_attempts' => env('AUTH_MAX_FAILED_ATTEMPTS', 5),
        
        // Account lockout duration in minutes
        'lockout_duration' => env('AUTH_LOCKOUT_DURATION', 30),
        
        // Require email verification for new accounts
        'require_email_verification' => env('SECURITY_REQUIRE_EMAIL_VERIFICATION', true),
        
        // Password requirements
        'password_min_length' => env('SECURITY_PASSWORD_MIN_LENGTH', 8),
        'password_require_uppercase' => env('SECURITY_PASSWORD_REQUIRE_UPPERCASE', true),
        'password_require_lowercase' => env('SECURITY_PASSWORD_REQUIRE_LOWERCASE', true),
        'password_require_numbers' => env('SECURITY_PASSWORD_REQUIRE_NUMBERS', true),
        'password_require_symbols' => env('SECURITY_PASSWORD_REQUIRE_SYMBOLS', true),
        
        // Two-factor authentication
        'enable_2fa' => env('SECURITY_ENABLE_2FA', false),
        'force_2fa_for_admin' => env('SECURITY_FORCE_2FA_FOR_ADMIN', true),
        
        // Password expiration
        'password_expires_days' => env('AUTH_PASSWORD_EXPIRES_DAYS', 90),
        
        // Force password change
        'force_password_change' => env('AUTH_FORCE_PASSWORD_CHANGE', true),
        
        // Remember me duration in minutes
        'remember_me_duration' => env('AUTH_REMEMBER_DURATION', 2160), // minutes (36 hours)
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */

    'session' => [
        // Validate session security (IP, User Agent changes)
        'validate_sessions' => env('SECURITY_VALIDATE_SESSIONS', true),
        
        // Allow IP address changes during session
        'allow_ip_changes' => env('SECURITY_ALLOW_IP_CHANGES', false),
        
        // Allow user agent changes during session
        'allow_user_agent_changes' => env('SECURITY_ALLOW_USER_AGENT_CHANGES', false),
        
        // Session timeout in minutes
        'session_timeout' => env('SECURITY_SESSION_TIMEOUT', 120),
        
        // Force session regeneration after login
        'regenerate_on_login' => env('SECURITY_REGENERATE_ON_LOGIN', true),
        
        // Notify users of suspicious activity
        'notify_suspicious_activity' => env('SECURITY_NOTIFY_SUSPICIOUS_ACTIVITY', true),
        
        // Validate IP
        'validate_ip' => env('SESSION_VALIDATE_IP', true),
        
        // Validate User Agent
        'validate_user_agent' => env('SESSION_VALIDATE_USER_AGENT', true),
        
        // Idle timeout in minutes
        'idle_timeout' => env('SESSION_IDLE_TIMEOUT', 120),
        
        // Absolute timeout in minutes
        'absolute_timeout' => env('SESSION_ABSOLUTE_TIMEOUT', 720),
        
        // Concurrent sessions
        'concurrent_sessions' => env('SESSION_CONCURRENT_LIMIT', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    */

    'rate_limiting' => [
        // Global application rate limit per minute
        'global_limit' => env('SECURITY_GLOBAL_RATE_LIMIT', 1000),
        
        // API rate limits
        'api' => [
            'authenticated' => env('RATE_LIMIT_API_AUTH', 120),
            'guest' => env('RATE_LIMIT_API_GUEST', 60),
        ],
        
        // Authentication rate limits
        'login' => [
            'global' => env('RATE_LIMIT_LOGIN_GLOBAL', 10),
            'per_email' => env('RATE_LIMIT_LOGIN_EMAIL', 3),
        ],
        
        // Registration rate limits
        'registration' => [
            'per_minute' => env('RATE_LIMIT_REGISTER_MINUTE', 5),
            'per_day' => env('RATE_LIMIT_REGISTER_DAY', 10),
        ],
        
        // Password reset rate limits
        'password_reset' => env('RATE_LIMIT_PASSWORD_RESET', 3),
        
        // Job search rate limits
        'job_search' => env('RATE_LIMIT_JOB_SEARCH', 100),
        
        // Job application rate limits
        'job_applications' => env('RATE_LIMIT_JOB_APPLY', 20),
        
        // File upload rate limits
        'file_uploads' => env('RATE_LIMIT_UPLOADS', 10),
        
        // Admin operations rate limits
        'admin_operations' => env('RATE_LIMIT_ADMIN', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization & Permissions
    |--------------------------------------------------------------------------
    */

    'authorization' => [
        // Cache permission checks for performance
        'cache_permissions' => env('SECURITY_CACHE_PERMISSIONS', true),
        
        // Permission cache duration in minutes
        'permission_cache_duration' => env('SECURITY_PERMISSION_CACHE_DURATION', 15),
        
        // Default user role for new registrations
        'default_user_role' => env('SECURITY_DEFAULT_USER_ROLE', 'candidate'),
        
        // Roles that require additional verification
        'verified_roles' => ['employer', 'admin'],
        
        // Super admin role (bypasses all checks)
        'super_admin_role' => env('SECURITY_SUPER_ADMIN_ROLE', 'super-admin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Security Policy
    |--------------------------------------------------------------------------
    */

    'csp' => [
        'enabled' => env('CSP_ENABLED', true),
        
        'directives' => [
            'default-src' => ["'self'"],
            'script-src' => [
                "'self'",
                "'unsafe-inline'", // Remove this in production
                'https://cdn.jsdelivr.net',
                'https://unpkg.com',
            ],
            'style-src' => [
                "'self'",
                "'unsafe-inline'",
                'https://fonts.googleapis.com',
                'https://cdn.jsdelivr.net',
            ],
            'img-src' => [
                "'self'",
                'data:',
                'https:',
                'blob:',
            ],
            'font-src' => [
                "'self'",
                'https://fonts.gstatic.com',
                'https://cdn.jsdelivr.net',
            ],
            'connect-src' => [
                "'self'",
                'https://api.company.com',
            ],
            'frame-ancestors' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
        ],
        
        'report_uri' => env('SECURITY_CSP_REPORT_URI', null),
        'report_only' => env('CSP_REPORT_ONLY', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */

    'headers' => [
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'DENY',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
        
        // HSTS (only on HTTPS)
        'hsts' => [
            'max_age' => 31536000, // 1 year
            'include_subdomains' => true,
            'preload' => false,
        ],
        
        // Strict Transport Security
        'strict-transport-security' => 'max-age=31536000; includeSubDomains; preload',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */

    'file_upload' => [
        // Maximum file size in bytes (default: 10MB)
        'max_file_size' => env('SECURITY_MAX_FILE_SIZE', 10485760),
        
        // Allowed file types for resumes
        'allowed_resume_types' => ['pdf', 'doc', 'docx'],
        
        // Allowed file types for images
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        
        // Scan uploaded files for malware
        'scan_uploads' => env('SECURITY_SCAN_UPLOADS', false),
        
        // Store uploads outside web root
        'secure_storage' => env('SECURITY_SECURE_STORAGE', true),
        
        // Maximum file size in KB
        'max_size' => env('UPLOAD_MAX_SIZE', 10240),
        
        // Allowed file extensions
        'allowed_extensions' => [
            'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'documents' => ['pdf', 'doc', 'docx', 'txt'],
            'resumes' => ['pdf', 'doc', 'docx'],
        ],
        
        // Validate MIME type
        'validate_mime_type' => env('UPLOAD_VALIDATE_MIME', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Security
    |--------------------------------------------------------------------------
    */

    'api' => [
        // Require API key for certain endpoints
        'require_api_key' => env('SECURITY_REQUIRE_API_KEY', false),
        
        // API key header name
        'api_key_header' => env('SECURITY_API_KEY_HEADER', 'X-API-Key'),
        
        // Enable API versioning
        'enable_versioning' => env('SECURITY_API_VERSIONING', true),
        
        // Default API version
        'default_version' => env('SECURITY_API_DEFAULT_VERSION', 'v1'),
        
        // CORS settings
        'cors' => [
            'allowed_origins' => explode(',', env('SECURITY_CORS_ALLOWED_ORIGINS', '*')),
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'max_age' => 86400, // 24 hours
        ],
        
        // Require HTTPS
        'require_https' => env('API_REQUIRE_HTTPS', true),
        
        // Enable CORS
        'cors_enabled' => env('API_CORS_ENABLED', true),
        
        // CORS origins
        'cors_origins' => explode(',', env('API_CORS_ORIGINS', 'localhost')),
        
        // API key required
        'api_key_required' => env('API_KEY_REQUIRED', false),
        
        // Rate limit by user
        'rate_limit_by_user' => env('API_RATE_LIMIT_BY_USER', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging & Monitoring
    |--------------------------------------------------------------------------
    */

    'logging' => [
        // Log all security events
        'log_security_events' => env('SECURITY_LOG_EVENTS', true),
        
        // Security log channel
        'security_channel' => env('SECURITY_LOG_CHANNEL', 'security'),
        
        // Log failed authentication attempts
        'log_failed_auth' => env('SECURITY_LOG_FAILED_AUTH', true),
        
        // Log authorization failures
        'log_auth_failures' => env('SECURITY_LOG_AUTH_FAILURES', true),
        
        // Log rate limit violations
        'log_rate_limits' => env('SECURITY_LOG_RATE_LIMITS', true),
        
        // Alert on suspicious activity
        'alert_suspicious_activity' => env('SECURITY_ALERT_SUSPICIOUS_ACTIVITY', true),
        
        // Log file uploads
        'log_file_uploads' => env('LOG_FILE_UPLOADS', true),
        
        // Log retention days
        'retention_days' => env('LOG_RETENTION_DAYS', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Account Security
    |--------------------------------------------------------------------------
    */

    'account' => [
        // Require password confirmation for sensitive actions
        'require_password_confirmation' => env('SECURITY_REQUIRE_PASSWORD_CONFIRMATION', true),
        
        // Password confirmation timeout in seconds
        'password_confirmation_timeout' => env('SECURITY_PASSWORD_CONFIRMATION_TIMEOUT', 10800), // 3 hours
        
        // Force password change after certain period (days)
        'force_password_change_after' => env('SECURITY_FORCE_PASSWORD_CHANGE_AFTER', null),
        
        // Prevent password reuse (number of previous passwords to check)
        'prevent_password_reuse' => env('SECURITY_PREVENT_PASSWORD_REUSE', 5),
        
        // Account deletion requires confirmation
        'require_deletion_confirmation' => env('SECURITY_REQUIRE_DELETION_CONFIRMATION', true),
        
        // Email verification required
        'email_verification_required' => env('ACCOUNT_EMAIL_VERIFICATION', true),
        
        // Two-factor authentication enabled
        'two_factor_enabled' => env('ACCOUNT_2FA_ENABLED', false),
        
        // Password history count
        'password_history_count' => env('ACCOUNT_PASSWORD_HISTORY', 5),
        
        // Suspicious activity alerts
        'suspicious_activity_alerts' => env('ACCOUNT_SUSPICIOUS_ALERTS', true),
        
        // Login notifications
        'login_notifications' => env('ACCOUNT_LOGIN_NOTIFICATIONS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Protection
    |--------------------------------------------------------------------------
    */

    'data_protection' => [
        // Encrypt sensitive data at rest
        'encrypt_sensitive_data' => env('DATA_ENCRYPT_SENSITIVE', true),
        
        // Automatically anonymize old data (days)
        'anonymize_data_after' => env('SECURITY_ANONYMIZE_DATA_AFTER', null),
        
        // Data retention period (days)
        'data_retention_period' => env('SECURITY_DATA_RETENTION_PERIOD', 2555), // 7 years
        
        // PII fields that require special handling
        'pii_fields' => [
            'email', 'phone', 'address', 'ssn', 'date_of_birth',
            'national_id', 'passport_number'
        ],
        
        // Anonymize logs
        'anonymize_logs' => env('DATA_ANONYMIZE_LOGS', false),
        
        // GDPR compliance
        'gdpr_compliance' => env('DATA_GDPR_COMPLIANCE', true),
        
        // Data retention days
        'data_retention_days' => env('DATA_RETENTION_DAYS', 2555), // 7 years
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Portal Specific Security
    |--------------------------------------------------------------------------
    */

    'job_portal' => [
        // Verify employer companies
        'verify_employer_companies' => env('SECURITY_VERIFY_EMPLOYER_COMPANIES', true),
        
        // Moderate job postings
        'moderate_job_postings' => env('SECURITY_MODERATE_JOB_POSTINGS', false),
        
        // Maximum job applications per day per user
        'max_applications_per_day' => env('SECURITY_MAX_APPLICATIONS_PER_DAY', 10),
        
        // Prevent duplicate applications
        'prevent_duplicate_applications' => env('SECURITY_PREVENT_DUPLICATE_APPLICATIONS', true),
        
        // Hide company information until application
        'hide_company_info' => env('SECURITY_HIDE_COMPANY_INFO', false),
        
        // Require terms acceptance for job applications
        'require_terms_acceptance' => env('SECURITY_REQUIRE_TERMS_ACCEPTANCE', true),
        
        // Verify company emails
        'verify_company_emails' => env('JOB_VERIFY_COMPANY_EMAILS', true),
        
        // Moderate job posts
        'moderate_job_posts' => env('JOB_MODERATE_POSTS', true),
        
        // Validate company domains
        'validate_company_domains' => env('JOB_VALIDATE_DOMAINS', false),
        
        // Candidate profile privacy
        'candidate_profile_privacy' => env('JOB_CANDIDATE_PRIVACY', true),
        
        // Employer verification required
        'employer_verification_required' => env('JOB_EMPLOYER_VERIFICATION', false),
    ],

]; 