<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoMaliciousContent implements Rule
{
    public function passes($attribute, $value): bool
    {
        // Check for common XSS patterns
        $maliciousPatterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/<link/i',
            '/<meta/i',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return false;
            }
        }

        return true;
    }

    public function message(): string
    {
        return 'The :attribute contains potentially malicious content.';
    }
}
