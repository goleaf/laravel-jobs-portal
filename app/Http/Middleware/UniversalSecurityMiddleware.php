<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UniversalSecurityMiddleware
{
    /**
     * Handle an incoming request with comprehensive security checks
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Perform security checks
            $this->checkRateLimiting($request);
            $this->checkSuspiciousActivity($request);
            $this->checkBotProtection($request);
            $this->checkGeoLocation($request);
            $this->checkDeviceFingerprint($request);
            $this->checkSecurityHeaders($request);
            $this->checkContentSecurityPolicy($request);

            // Add security headers to response
            $response = $next($request);

            return $this->addSecurityHeaders($response, $request);

        } catch (\Exception $e) {
            Log::error('Security middleware error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);

            return $next($request);
        }
    }

    /**
     * Enhanced rate limiting with intelligent detection
     */
    private function checkRateLimiting(Request $request)
    {
        $ip = $request->ip();
        $key = 'rate_limit:'.$ip;
        $attempts = Cache::get($key, 0);

        // Different limits for different endpoints
        $limits = [
            'login' => 5,     // 5 attempts per minute
            'api' => 60,      // 60 requests per minute
            'general' => 120,  // 120 requests per minute
        ];

        $endpoint = $this->getEndpointType($request);
        $limit = $limits[$endpoint] ?? $limits['general'];

        if ($attempts >= $limit) {
            // Log suspicious activity
            $this->logSecurityEvent($request, 'rate_limit_exceeded', [
                'attempts' => $attempts,
                'limit' => $limit,
                'endpoint_type' => $endpoint,
            ]);

            abort(429, 'Rate limit exceeded');
        }

        // Increment counter
        Cache::put($key, $attempts + 1, now()->addMinute());
    }

    /**
     * Advanced suspicious activity detection
     */
    private function checkSuspiciousActivity(Request $request)
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $suspiciousPatterns = [
            'sql_injection' => '/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bDELETE\b|\bDROP\b)/i',
            'xss_attempt' => '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi',
            'directory_traversal' => '/\.\.[\/\\]/',
            'command_injection' => '/(\bexec\b|\bsystem\b|\bpassthru\b|\bshell_exec\b)/i',
        ];

        $requestData = $request->all();
        $requestString = json_encode($requestData);

        foreach ($suspiciousPatterns as $type => $pattern) {
            if (preg_match($pattern, $requestString)) {
                $this->logSecurityEvent($request, 'suspicious_activity_detected', [
                    'threat_type' => $type,
                    'pattern' => $pattern,
                    'request_data' => $requestData,
                ]);

                // Block immediately for high-risk patterns
                if (in_array($type, ['sql_injection', 'command_injection'])) {
                    abort(403, 'Suspicious activity detected');
                }
            }
        }

        // Check for brute force patterns
        $this->checkBruteForcePatterns($request);
    }

    /**
     * Advanced bot protection and CAPTCHA integration
     */
    private function checkBotProtection(Request $request)
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();

        // Known bot patterns
        $botPatterns = [
            '/bot/i', '/crawler/i', '/spider/i', '/scraper/i',
            '/curl/i', '/wget/i', '/python/i', '/php/i',
        ];

        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                $this->logSecurityEvent($request, 'bot_detected', [
                    'user_agent' => $userAgent,
                    'pattern' => $pattern,
                ]);

                // Allow legitimate bots but limit their access
                Cache::put('bot_detected:'.$ip, true, now()->addHour());
                break;
            }
        }

        // Check for headless browser signatures
        $this->checkHeadlessBrowser($request);

        // Implement honeypot field checking
        $this->checkHoneypot($request);
    }

    /**
     * Geolocation-based security checks
     */
    private function checkGeoLocation(Request $request)
    {
        $ip = $request->ip();

        // Skip for localhost/private IPs
        if ($this->isPrivateIP($ip)) {
            return;
        }

        try {
            // Get geolocation data (implement with your preferred service)
            $location = $this->getGeoLocation($ip);

            if ($location) {
                // Check against blocked countries
                $blockedCountries = config('security.blocked_countries', []);

                if (in_array($location['country'], $blockedCountries)) {
                    $this->logSecurityEvent($request, 'blocked_country_access', [
                        'country' => $location['country'],
                        'city' => $location['city'] ?? 'Unknown',
                    ]);

                    abort(403, 'Access denied from your location');
                }

                // Check for VPN/Proxy usage
                if ($location['is_proxy'] ?? false) {
                    $this->logSecurityEvent($request, 'proxy_detected', $location);

                    // Optional: Block proxy access
                    if (config('security.block_proxies', false)) {
                        abort(403, 'Proxy access not allowed');
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Geolocation check failed', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Device fingerprinting for anomaly detection
     */
    private function checkDeviceFingerprint(Request $request)
    {
        $fingerprint = $this->generateDeviceFingerprint($request);
        $userId = auth()->id();

        if ($userId) {
            $key = "device_fingerprint:user:{$userId}";
            $knownFingerprints = Cache::get($key, []);

            if (! in_array($fingerprint, $knownFingerprints)) {
                // New device detected
                $this->logSecurityEvent($request, 'new_device_detected', [
                    'user_id' => $userId,
                    'fingerprint' => $fingerprint,
                    'known_count' => count($knownFingerprints),
                ]);

                // Add to known devices
                $knownFingerprints[] = $fingerprint;
                Cache::put($key, array_slice($knownFingerprints, -10), now()->addDays(30));

                // Optional: Require additional verification
                if (config('security.verify_new_devices', true)) {
                    session(['requires_device_verification' => true]);
                }
            }
        }
    }

    /**
     * Comprehensive security headers validation
     */
    private function checkSecurityHeaders(Request $request)
    {
        $requiredHeaders = [
            'X-Requested-With' => 'XMLHttpRequest', // For AJAX requests
            'X-CSRF-TOKEN' => null, // CSRF protection
        ];

        // Check for missing security headers in AJAX requests
        if ($request->expectsJson()) {
            foreach ($requiredHeaders as $header => $expectedValue) {
                if (! $request->hasHeader($header)) {
                    $this->logSecurityEvent($request, 'missing_security_header', [
                        'missing_header' => $header,
                    ]);
                }
            }
        }
    }

    /**
     * Content Security Policy validation
     */
    private function checkContentSecurityPolicy(Request $request)
    {
        // Validate uploaded files
        if ($request->hasFile('file')) {
            foreach ($request->allFiles() as $file) {
                if ($file->isValid()) {
                    $this->validateUploadedFile($file, $request);
                }
            }
        }

        // Check for dangerous content in form submissions
        $this->validateFormContent($request);
    }

    /**
     * Add comprehensive security headers to response
     */
    private function addSecurityHeaders($response, Request $request)
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
            'Content-Security-Policy' => $this->generateCSP(),
        ];

        foreach ($headers as $header => $value) {
            $response->headers->set($header, $value);
        }

        return $response;
    }

    /**
     * Generate Content Security Policy header
     */
    private function generateCSP()
    {
        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self' https:",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        return implode('; ', $directives);
    }

    /**
     * Generate device fingerprint based on request characteristics
     */
    private function generateDeviceFingerprint(Request $request)
    {
        $components = [
            $request->userAgent(),
            $request->header('Accept-Language'),
            $request->header('Accept-Encoding'),
            $request->header('Accept'),
        ];

        return hash('sha256', implode('|', array_filter($components)));
    }

    /**
     * Check for brute force attack patterns
     */
    private function checkBruteForcePatterns(Request $request)
    {
        $ip = $request->ip();
        $endpoint = $request->path();

        // Track failed login attempts
        if (Str::contains($endpoint, 'login') && $request->isMethod('POST')) {
            $key = 'login_attempts:'.$ip;
            $attempts = Cache::get($key, 0);

            if ($attempts >= 5) {
                $this->logSecurityEvent($request, 'brute_force_detected', [
                    'attempts' => $attempts,
                    'endpoint' => $endpoint,
                ]);

                // Temporary IP ban
                Cache::put('banned_ip:'.$ip, true, now()->addMinutes(30));
                abort(429, 'Too many failed attempts');
            }
        }
    }

    /**
     * Check for headless browser indicators
     */
    private function checkHeadlessBrowser(Request $request)
    {
        $userAgent = $request->userAgent();

        $headlessIndicators = [
            'HeadlessChrome', 'PhantomJS', 'SlimerJS', 'HtmlUnit',
            'webdriver', 'selenium', 'puppeteer',
        ];

        foreach ($headlessIndicators as $indicator) {
            if (stripos($userAgent, $indicator) !== false) {
                $this->logSecurityEvent($request, 'headless_browser_detected', [
                    'user_agent' => $userAgent,
                    'indicator' => $indicator,
                ]);
                break;
            }
        }
    }

    /**
     * Check honeypot field (anti-bot measure)
     */
    private function checkHoneypot(Request $request)
    {
        $honeypotField = config('security.honeypot_field', 'website_url');

        if ($request->has($honeypotField) && ! empty($request->get($honeypotField))) {
            $this->logSecurityEvent($request, 'honeypot_triggered', [
                'field' => $honeypotField,
                'value' => $request->get($honeypotField),
            ]);

            abort(403, 'Bot detected');
        }
    }

    /**
     * Validate uploaded files for security threats
     */
    private function validateUploadedFile($file, Request $request)
    {
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        // Check for dangerous file extensions
        $dangerousExtensions = [
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
            'exe', 'bat', 'cmd', 'scr', 'vbs', 'js', 'jar',
        ];

        if (in_array(strtolower($extension), $dangerousExtensions)) {
            $this->logSecurityEvent($request, 'dangerous_file_upload', [
                'filename' => $filename,
                'extension' => $extension,
                'mime_type' => $mimeType,
            ]);

            abort(403, 'File type not allowed');
        }

        // Scan file content for malware signatures
        $this->scanFileForMalware($file, $request);
    }

    /**
     * Basic malware scanning (implement with professional scanner)
     */
    private function scanFileForMalware($file, Request $request)
    {
        $content = file_get_contents($file->getRealPath());

        $malwareSignatures = [
            'eval(', 'base64_decode(', 'shell_exec(', 'system(',
            'exec(', 'passthru(', 'file_get_contents(',
        ];

        foreach ($malwareSignatures as $signature) {
            if (stripos($content, $signature) !== false) {
                $this->logSecurityEvent($request, 'malware_detected', [
                    'filename' => $file->getClientOriginalName(),
                    'signature' => $signature,
                ]);

                abort(403, 'Malicious file detected');
            }
        }
    }

    /**
     * Validate form content for dangerous patterns
     */
    private function validateFormContent(Request $request)
    {
        $formData = $request->except(['_token', '_method']);

        foreach ($formData as $field => $value) {
            if (is_string($value)) {
                // Check for JavaScript injection
                if (preg_match('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi', $value)) {
                    $this->logSecurityEvent($request, 'xss_attempt_in_form', [
                        'field' => $field,
                        'value' => Str::limit($value, 100),
                    ]);
                }

                // Check for SQL injection patterns
                if (preg_match('/(\bUNION\b|\bSELECT\b.*\bFROM\b|\bINSERT\b.*\bINTO\b)/i', $value)) {
                    $this->logSecurityEvent($request, 'sql_injection_attempt', [
                        'field' => $field,
                        'value' => Str::limit($value, 100),
                    ]);
                }
            }
        }
    }

    /**
     * Get geolocation data for IP address
     */
    private function getGeoLocation($ip)
    {
        // Implement with your preferred geolocation service
        // This is a placeholder - integrate with MaxMind, IPinfo, etc.
        return Cache::remember("geo:{$ip}", now()->addHours(24), function () use ($ip) {
            try {
                // Example implementation with free service
                $response = file_get_contents("http://ip-api.com/json/{$ip}");
                $data = json_decode($response, true);

                return [
                    'country' => $data['country'] ?? 'Unknown',
                    'city' => $data['city'] ?? 'Unknown',
                    'is_proxy' => ($data['proxy'] ?? false) || ($data['hosting'] ?? false),
                ];
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    /**
     * Check if IP is private/local
     */
    private function isPrivateIP($ip)
    {
        return ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Determine endpoint type for rate limiting
     */
    private function getEndpointType(Request $request)
    {
        $path = $request->path();

        if (Str::contains($path, 'login')) {
            return 'login';
        }
        if (Str::startsWith($path, 'api/')) {
            return 'api';
        }

        return 'general';
    }

    /**
     * Log security events with comprehensive data
     */
    private function logSecurityEvent(Request $request, $eventType, $additionalData = [])
    {
        $eventData = [
            'event_type' => $eventType,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
            'additional_data' => $additionalData,
        ];

        // Log to security log
        Log::channel('security')->warning("Security Event: {$eventType}", $eventData);

        // Store in database for analysis
        try {
            DB::table('security_events')->insert([
                'event_type' => $eventType,
                'ip_address' => $request->ip(),
                'user_id' => auth()->id(),
                'event_data' => json_encode($eventData),
                'severity' => $this->getEventSeverity($eventType),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to store security event', [
                'error' => $e->getMessage(),
                'event' => $eventData,
            ]);
        }
    }

    /**
     * Get severity level for security events
     */
    private function getEventSeverity($eventType)
    {
        $severityMap = [
            'sql_injection_attempt' => 'critical',
            'command_injection' => 'critical',
            'malware_detected' => 'critical',
            'brute_force_detected' => 'high',
            'suspicious_activity_detected' => 'high',
            'rate_limit_exceeded' => 'medium',
            'bot_detected' => 'low',
            'new_device_detected' => 'info',
        ];

        return $severityMap[$eventType] ?? 'medium';
    }
}
