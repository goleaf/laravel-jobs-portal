<?php

namespace App\Http\Requests\Communication;

use App\Http\Requests\Foundation\AbstractBaseRequest;
use App\Http\Requests\Foundation\Traits\AuditLoggingTrait;
use App\Http\Requests\Foundation\Traits\MultilingualValidationTrait;
use App\Http\Requests\Foundation\Traits\PerformanceOptimizationTrait;
use App\Http\Requests\Foundation\Traits\SecurityValidationTrait;

/**
 * Communication Request - Base class for communication validation
 *
 * Handles validation for:
 * - Messaging and chat systems
 * - Email and notification management
 * - Content creation and publishing
 * - Communication workflow validation
 * - Multi-channel communication
 *
 * @version 1.0.0
 *
 * @since 2024-12-28
 */
abstract class CommunicationRequest extends AbstractBaseRequest
{
    use AuditLoggingTrait;
    use MultilingualValidationTrait;
    use PerformanceOptimizationTrait;
    use SecurityValidationTrait;

    /**
     * Security level for communication operations
     */
    protected string $securityLevel = 'medium';

    /**
     * Enable performance monitoring for communication operations
     */
    protected bool $performanceTracking = true;

    /**
     * Enable audit logging for communication operations
     */
    protected bool $auditLoggingEnabled = true;

    /**
     * Communication validation modules
     */
    protected array $validationModules = [
        'content_validation',
        'spam_detection',
        'message_security',
        'notification_rules',
    ];

    /**
     * Get domain-specific validation rules for communication data
     */
    protected function getDomainRules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
            'message_type' => ['required', 'string', 'in:email,sms,push,notification,message'],
            'status' => ['sometimes', 'required', 'in:draft,pending,sent,delivered,failed'],
        ];
    }

    /**
     * Get domain-specific error messages for communication data
     */
    protected function getDomainMessages(): array
    {
        return [
            'subject.required' => __('validation.communication.subject_required'),
            'content.required' => __('validation.communication.content_required'),
            'message_type.required' => __('validation.communication.message_type_required'),
        ];
    }

    /**
     * Get domain-specific attribute names for communication data
     */
    protected function getDomainAttributes(): array
    {
        return [
            'subject' => __('validation.attributes.subject'),
            'content' => __('validation.attributes.content'),
            'message_type' => __('validation.attributes.message_type'),
        ];
    }

    /**
     * Common validation rules for email communication
     */
    protected function getEmailRules(): array
    {
        return [
            'to_email' => ['required', 'email', 'max:255'],
            'from_email' => ['sometimes', 'required', 'email', 'max:255'],
            'cc_emails' => ['sometimes', 'array'],
            'cc_emails.*' => ['email', 'max:255'],
            'bcc_emails' => ['sometimes', 'array'],
            'bcc_emails.*' => ['email', 'max:255'],
            'reply_to' => ['sometimes', 'email', 'max:255'],
        ];
    }

    /**
     * Common validation rules for SMS communication
     */
    protected function getSmsRules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'],
            'message' => ['required', 'string', 'max:160'],
            'sender_id' => ['sometimes', 'string', 'max:11'],
        ];
    }

    /**
     * Common validation rules for push notifications
     */
    protected function getPushNotificationRules(): array
    {
        return [
            'device_token' => ['required', 'string'],
            'title' => ['required', 'string', 'max:100'],
            'body' => ['required', 'string', 'max:500'],
            'icon' => ['sometimes', 'string', 'max:255'],
            'sound' => ['sometimes', 'string', 'max:50'],
            'badge' => ['sometimes', 'integer', 'min:0'],
            'data' => ['sometimes', 'array'],
        ];
    }

    /**
     * Common validation rules for internal messaging
     */
    protected function getMessagingRules(): array
    {
        return [
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'sender_id' => ['sometimes', 'required', 'integer', 'exists:users,id'],
            'conversation_id' => ['sometimes', 'integer', 'exists:conversations,id'],
            'message_thread_id' => ['sometimes', 'integer', 'exists:message_threads,id'],
            'is_read' => ['sometimes', 'boolean'],
            'read_at' => ['sometimes', 'date'],
        ];
    }

    /**
     * Common validation rules for content publishing
     */
    protected function getContentRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'excerpt' => ['sometimes', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'tags' => ['sometimes', 'array'],
            'tags.*' => ['string', 'max:50'],
            'featured_image' => ['sometimes', 'image', 'max:2048'],
            'publish_at' => ['sometimes', 'date', 'after_or_equal:now'],
            'status' => ['required', 'in:draft,published,scheduled,archived'],
        ];
    }

    /**
     * Perform communication-specific validation
     */
    protected function performCustomValidation($validator): void
    {
        // Log communication validation attempt
        $this->logUserAction('communication_validation_started', [
            'request_type' => static::class,
            'message_type' => $this->input('message_type'),
            'content_length' => strlen($this->input('content', '')),
        ]);

        // Validate content for spam
        $this->validateSpamDetection($validator);

        // Validate content formatting
        $this->validateContentFormatting($validator);

        // Validate recipient accessibility
        $this->validateRecipientAccessibility($validator);

        // Validate communication limits
        $this->validateCommunicationLimits($validator);
    }

    /**
     * Validate content for spam detection
     */
    protected function validateSpamDetection($validator): void
    {
        $content = $this->input('content');
        $subject = $this->input('subject');

        if ($content || $subject) {
            $text = ($subject ? $subject.' ' : '').($content ? $content : '');

            if ($this->containsSpamPatterns($text)) {
                $this->logSecurityEvent('spam_content_detected', [
                    'content_length' => strlen($text),
                    'spam_indicators' => $this->getSpamIndicators($text),
                ]);

                $validator->errors()->add('content', __('validation.communication.spam_detected'));
            }
        }
    }

    /**
     * Validate content formatting
     */
    protected function validateContentFormatting($validator): void
    {
        $content = $this->input('content');

        if ($content) {
            // Check for excessive HTML tags
            if ($this->hasExcessiveHtml($content)) {
                $validator->errors()->add('content', __('validation.communication.excessive_html'));
            }

            // Check for malicious scripts
            if ($this->containsMaliciousScript($content)) {
                $validator->errors()->add('content', __('validation.communication.malicious_script_detected'));
            }
        }
    }

    /**
     * Validate recipient accessibility
     */
    protected function validateRecipientAccessibility($validator): void
    {
        // Override in specific request classes
    }

    /**
     * Validate communication limits
     */
    protected function validateCommunicationLimits($validator): void
    {
        $messageType = $this->input('message_type');

        if ($messageType) {
            $limits = $this->getCommunicationLimits($messageType);
            $this->validateRateLimits($validator, $limits);
        }
    }

    /**
     * Check for spam patterns in content
     */
    protected function containsSpamPatterns(string $text): bool
    {
        $spamPatterns = [
            '/\b(free|win|winner|prize|urgent|act now|limited time)\b/i',
            '/\$\d+/i', // Money amounts
            '/\b\d{4}[-\s]\d{4}[-\s]\d{4}[-\s]\d{4}\b/', // Credit card patterns
            '/\b[A-Z]{3,}\b.*\b[A-Z]{3,}\b/', // Excessive capitals
            '/[!]{3,}/', // Multiple exclamations
        ];

        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get spam indicators from text
     */
    protected function getSpamIndicators(string $text): array
    {
        $indicators = [];

        if (preg_match('/\b(free|win|winner)\b/i', $text)) {
            $indicators[] = 'promotional_keywords';
        }

        if (preg_match('/[!]{3,}/', $text)) {
            $indicators[] = 'excessive_punctuation';
        }

        if (preg_match('/\b[A-Z]{5,}/', $text)) {
            $indicators[] = 'excessive_capitals';
        }

        return $indicators;
    }

    /**
     * Check for excessive HTML in content
     */
    protected function hasExcessiveHtml(string $content): bool
    {
        $htmlTagCount = preg_match_all('/<[^>]+>/', $content);
        $contentLength = strlen(strip_tags($content));

        // If more than 30% of content is HTML tags
        return $htmlTagCount > 0 && ($htmlTagCount / strlen($content)) > 0.3;
    }

    /**
     * Check for malicious scripts in content
     */
    protected function containsMaliciousScript(string $content): bool
    {
        $maliciousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript:/i',
            '/on\w+\s*=\s*["\'][^"\']*["\']?/i',
            '/<iframe\b[^>]*>/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get communication limits by type
     */
    protected function getCommunicationLimits(string $messageType): array
    {
        return match ($messageType) {
            'email' => ['daily' => 100, 'hourly' => 20],
            'sms' => ['daily' => 50, 'hourly' => 10],
            'push' => ['daily' => 200, 'hourly' => 50],
            'notification' => ['daily' => 500, 'hourly' => 100],
            default => ['daily' => 50, 'hourly' => 10],
        };
    }

    /**
     * Validate rate limits for communication
     */
    protected function validateRateLimits($validator, array $limits): void
    {
        // Override in specific request classes to implement actual rate limiting
    }

    /**
     * Apply communication-specific sanitization
     */
    protected function applySanitization(array $data): array
    {
        $sanitized = parent::applySanitization($data);

        // Sanitize content
        if (isset($sanitized['content'])) {
            $sanitized['content'] = $this->sanitizeContent($sanitized['content']);
        }

        if (isset($sanitized['subject'])) {
            $sanitized['subject'] = strip_tags($sanitized['subject']);
        }

        return $sanitized;
    }

    /**
     * Sanitize content while preserving safe HTML
     */
    protected function sanitizeContent(string $content): string
    {
        // Allow safe HTML tags
        $allowedTags = '<p><br><strong><em><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6>';

        return strip_tags($content, $allowedTags);
    }
}
