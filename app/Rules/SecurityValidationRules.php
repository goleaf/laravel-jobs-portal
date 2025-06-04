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

class SecureFileName implements Rule
{
    public function passes($attribute, $value): bool
    {
        // Allow only safe characters in filenames
        return preg_match('/^[a-zA-Z0-9_\-\.]+$/', $value) && 
               !str_contains($value, '..') &&
               strlen($value) <= 255;
    }

    public function message(): string
    {
        return 'The :attribute must be a valid filename.';
    }
}

class StrongPassword implements Rule
{
    public function passes($attribute, $value): bool
    {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number, 1 special char
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
    }

    public function message(): string
    {
        return 'The :attribute must contain at least 8 characters with uppercase, lowercase, number and special character.';
    }
}