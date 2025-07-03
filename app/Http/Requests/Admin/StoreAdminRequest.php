<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BusinessLogic\BusinessLogicRequest;

/**
 * Store Admin Request - Validation for creating new admin users
 *
 * Validates admin creation with comprehensive rules:
 * - Personal information validation
 * - Security requirements (password strength)
 * - Role-based access validation
 * - Email uniqueness checking
 * - Business logic constraints
 *
 * @version 2.0.0
 *
 * @since 2024-12-28
 */
class StoreAdminRequest extends BusinessLogicRequest
{
    /**
     * Security level for admin creation operations
     */
    protected string $securityLevel = 'critical';

    /**
     * Get business logic validation rules for admin creation
     */
    protected function getBusinessLogicRules(): array
    {
        return [
            // Personal Information
            'first_name' => ['required', 'string', 'max:100', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email'],

            // Security Requirements
            'password' => ['required', 'string', 'min:12', 'max:255', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'],
            'password_confirmation' => ['required', 'same:password'],

            // Admin-Specific Fields
            'role' => ['required', 'string', 'in:admin,super_admin'],
            'is_active' => ['sometimes', 'boolean'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]{7,20}$/'],

            // Optional Administrative Fields
            'department' => ['sometimes', 'nullable', 'string', 'max:100'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages for admin creation
     */
    protected function getCustomMessages(): array
    {
        return [
            // Personal Information Messages
            'first_name.required' => __('validation.admin.first_name_required'),
            'first_name.regex' => __('validation.admin.first_name_format'),
            'first_name.min' => __('validation.admin.first_name_min'),
            'last_name.regex' => __('validation.admin.last_name_format'),
            'name.required' => __('validation.admin.name_required'),

            // Email Messages
            'email.required' => __('validation.admin.email_required'),
            'email.email' => __('validation.admin.email_format'),
            'email.unique' => __('validation.admin.email_unique'),

            // Password Messages
            'password.required' => __('validation.admin.password_required'),
            'password.min' => __('validation.admin.password_min'),
            'password.regex' => __('validation.admin.password_complexity'),
            'password.confirmed' => __('validation.admin.password_confirmation'),
            'password_confirmation.required' => __('validation.admin.password_confirmation_required'),
            'password_confirmation.same' => __('validation.admin.password_match'),

            // Role Messages
            'role.required' => __('validation.admin.role_required'),
            'role.in' => __('validation.admin.role_invalid'),

            // Phone Messages
            'phone.regex' => __('validation.admin.phone_format'),

            // General Messages
            'is_active.boolean' => __('validation.admin.status_boolean'),
        ];
    }

    /**
     * Get custom attribute names for admin creation
     */
    protected function getCustomAttributes(): array
    {
        return [
            'first_name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'name' => __('validation.attributes.full_name'),
            'email' => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
            'role' => __('validation.attributes.role'),
            'is_active' => __('validation.attributes.status'),
            'phone' => __('validation.attributes.phone'),
            'department' => __('validation.attributes.department'),
            'position' => __('validation.attributes.position'),
            'notes' => __('validation.attributes.notes'),
        ];
    }

    /**
     * Perform admin-specific validation logic
     */
    protected function performCustomValidation($validator): void
    {
        parent::performCustomValidation($validator);

        // Validate admin creation business rules
        $this->validateAdminCreationRules($validator);

        // Validate role permissions
        $this->validateRolePermissions($validator);

        // Validate security constraints
        $this->validateSecurityConstraints($validator);
    }

    /**
     * Validate admin creation business rules
     */
    protected function validateAdminCreationRules($validator): void
    {
        // Ensure full name is provided if first_name and last_name are separate
        if ($this->filled('first_name') && $this->filled('last_name')) {
            $this->merge([
                'name' => trim($this->first_name.' '.$this->last_name),
            ]);
        } elseif ($this->filled('first_name') && ! $this->filled('name')) {
            $this->merge([
                'name' => $this->first_name,
            ]);
        }

        // Validate admin count limits (if applicable)
        if ($this->role === 'super_admin') {
            $superAdminCount = \App\Models\User::where('role', 'super_admin')->count();
            if ($superAdminCount >= 3) { // Example: max 3 super admins
                $validator->errors()->add('role', __('validation.admin.max_super_admins'));
            }
        }
    }

    /**
     * Validate role permissions for admin creation
     */
    protected function validateRolePermissions($validator): void
    {
        // Note: Since authentication is being removed, this validation is minimal
        // In a real scenario, you'd check if current user can create admins with specified role

        $allowedRoles = ['admin', 'super_admin'];
        if (! in_array($this->role, $allowedRoles)) {
            $validator->errors()->add('role', __('validation.admin.role_not_allowed'));
        }
    }

    /**
     * Validate security constraints for admin creation
     */
    protected function validateSecurityConstraints($validator): void
    {
        // Validate email domain restrictions (if applicable)
        $email = $this->email;
        $allowedDomains = config('admin.allowed_email_domains', []);

        if (! empty($allowedDomains)) {
            $emailDomain = substr(strrchr($email, '@'), 1);
            if (! in_array($emailDomain, $allowedDomains)) {
                $validator->errors()->add('email', __('validation.admin.email_domain_not_allowed'));
            }
        }

        // Validate password against common passwords list
        $commonPasswords = [
            'password123', 'admin123', 'administrator', 'password1',
            '123456789', 'qwerty123', 'admin1234',
        ];

        if (in_array(strtolower($this->password), $commonPasswords)) {
            $validator->errors()->add('password', __('validation.admin.password_too_common'));
        }
    }

    /**
     * Apply admin-specific data sanitization
     */
    protected function applySanitization(array $data): array
    {
        $sanitized = parent::applySanitization($data);

        // Sanitize names (proper case)
        if (isset($sanitized['first_name'])) {
            $sanitized['first_name'] = ucwords(strtolower(trim($sanitized['first_name'])));
        }

        if (isset($sanitized['last_name'])) {
            $sanitized['last_name'] = ucwords(strtolower(trim($sanitized['last_name'])));
        }

        // Sanitize email (lowercase)
        if (isset($sanitized['email'])) {
            $sanitized['email'] = strtolower(trim($sanitized['email']));
        }

        // Sanitize phone (remove spaces, keep only digits and +)
        if (isset($sanitized['phone'])) {
            $sanitized['phone'] = preg_replace('/[^\+0-9]/', '', $sanitized['phone']);
        }

        // Ensure role is lowercase
        if (isset($sanitized['role'])) {
            $sanitized['role'] = strtolower($sanitized['role']);
        }

        return $sanitized;
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate name if not provided
        if (! $this->filled('name') && ($this->filled('first_name'))) {
            $name = trim($this->first_name);
            if ($this->filled('last_name')) {
                $name .= ' '.trim($this->last_name);
            }
            $this->merge(['name' => $name]);
        }

        // Set default role if not provided
        if (! $this->filled('role')) {
            $this->merge(['role' => 'admin']);
        }

        // Set default active status
        if (! $this->has('is_active')) {
            $this->merge(['is_active' => true]);
        }
    }
}
