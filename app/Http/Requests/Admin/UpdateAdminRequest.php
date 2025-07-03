<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BusinessLogic\BusinessLogicRequest;

/**
 * Update Admin Request - Validation for updating existing admin users
 * 
 * Validates admin updates with comprehensive rules:
 * - Personal information validation
 * - Optional password updates with security requirements
 * - Role-based access validation
 * - Email uniqueness checking (excluding current record)
 * - Business logic constraints
 * 
 * @package App\Http\Requests\Admin
 * @version 2.0.0
 * @since 2024-12-28
 */
class UpdateAdminRequest extends BusinessLogicRequest
{
    /**
     * Security level for admin update operations
     */
    protected string $securityLevel = 'critical';

    /**
     * Get business logic validation rules for admin updates
     */
    protected function getBusinessLogicRules(): array
    {
        $userId = $this->route('admin') ? $this->route('admin')->id : $this->route('id');

        return [
            // Personal Information
            'first_name' => ['required', 'string', 'max:100', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100', 'min:2', 'regex:/^[a-zA-Z\s]+$/'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:users,email,' . $userId],
            
            // Optional Security Requirements
            'password' => ['sometimes', 'nullable', 'string', 'min:12', 'max:255', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'],
            'password_confirmation' => ['sometimes', 'nullable', 'same:password'],
            
            // Admin-Specific Fields
            'role' => ['sometimes', 'string', 'in:admin,super_admin'],
            'is_active' => ['sometimes', 'boolean'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]{7,20}$/'],
            
            // Optional Personal Information
            'dob' => ['sometimes', 'nullable', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['sometimes', 'nullable', 'in:male,female,other,prefer_not_to_say'],
            
            // Optional Administrative Fields
            'department' => ['sometimes', 'nullable', 'string', 'max:100'],
            'position' => ['sometimes', 'nullable', 'string', 'max:100'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages for admin updates
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
            'email.unique' => __('validation.admin.email_unique_update'),
            
            // Password Messages (Optional)
            'password.min' => __('validation.admin.password_min'),
            'password.regex' => __('validation.admin.password_complexity'),
            'password.confirmed' => __('validation.admin.password_confirmation'),
            'password_confirmation.same' => __('validation.admin.password_match'),
            
            // Role Messages
            'role.in' => __('validation.admin.role_invalid'),
            
            // Phone Messages
            'phone.regex' => __('validation.admin.phone_format'),
            
            // Date Messages
            'dob.before' => __('validation.admin.dob_before_today'),
            'dob.after' => __('validation.admin.dob_after_1900'),
            
            // Gender Messages
            'gender.in' => __('validation.admin.gender_invalid'),
            
            // General Messages
            'is_active.boolean' => __('validation.admin.status_boolean'),
        ];
    }

    /**
     * Get custom attribute names for admin updates
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
            'dob' => __('validation.attributes.date_of_birth'),
            'gender' => __('validation.attributes.gender'),
            'department' => __('validation.attributes.department'),
            'position' => __('validation.attributes.position'),
            'notes' => __('validation.attributes.notes'),
        ];
    }

    /**
     * Perform admin-specific validation logic for updates
     */
    protected function performCustomValidation($validator): void
    {
        parent::performCustomValidation($validator);
        
        // Validate admin update business rules
        $this->validateAdminUpdateRules($validator);
        
        // Validate role permissions for updates
        $this->validateRoleUpdatePermissions($validator);
        
        // Validate security constraints for updates
        $this->validateUpdateSecurityConstraints($validator);
        
        // Validate password requirements if provided
        $this->validatePasswordUpdateConstraints($validator);
    }

    /**
     * Validate admin update business rules
     */
    protected function validateAdminUpdateRules($validator): void
    {
        $userId = $this->route('admin') ? $this->route('admin')->id : $this->route('id');
        
        // Ensure full name is updated if first_name and last_name are provided
        if ($this->filled('first_name') && $this->filled('last_name')) {
            $this->merge([
                'name' => trim($this->first_name . ' ' . $this->last_name)
            ]);
        } elseif ($this->filled('first_name') && !$this->filled('name')) {
            $this->merge([
                'name' => $this->first_name
            ]);
        }

        // Prevent self-deactivation
        if ($this->has('is_active') && !$this->is_active && $userId == auth()->id()) {
            $validator->errors()->add('is_active', __('validation.admin.cannot_deactivate_self'));
        }

        // Validate super admin count if role is being changed
        if ($this->filled('role') && $this->role === 'super_admin') {
            $currentUser = \App\Models\User::find($userId);
            if ($currentUser && $currentUser->role !== 'super_admin') {
                $superAdminCount = \App\Models\User::where('role', 'super_admin')->count();
                if ($superAdminCount >= 3) { // Example: max 3 super admins
                    $validator->errors()->add('role', __('validation.admin.max_super_admins'));
                }
            }
        }
    }

    /**
     * Validate role permissions for admin updates
     */
    protected function validateRoleUpdatePermissions($validator): void
    {
        // Note: Since authentication is being removed, this validation is minimal
        // In a real scenario, you'd check if current user can update admin roles
        
        if ($this->filled('role')) {
            $allowedRoles = ['admin', 'super_admin'];
            if (!in_array($this->role, $allowedRoles)) {
                $validator->errors()->add('role', __('validation.admin.role_not_allowed'));
            }
        }
    }

    /**
     * Validate security constraints for admin updates
     */
    protected function validateUpdateSecurityConstraints($validator): void
    {
        // Validate email domain restrictions (if applicable)
        if ($this->filled('email')) {
            $email = $this->email;
            $allowedDomains = config('admin.allowed_email_domains', []);
            
            if (!empty($allowedDomains)) {
                $emailDomain = substr(strrchr($email, '@'), 1);
                if (!in_array($emailDomain, $allowedDomains)) {
                    $validator->errors()->add('email', __('validation.admin.email_domain_not_allowed'));
                }
            }
        }
    }

    /**
     * Validate password update constraints
     */
    protected function validatePasswordUpdateConstraints($validator): void
    {
        // Only validate password if it's being updated
        if ($this->filled('password')) {
            // Validate password against common passwords list
            $commonPasswords = [
                'password123', 'admin123', 'administrator', 'password1', 
                '123456789', 'qwerty123', 'admin1234'
            ];
            
            if (in_array(strtolower($this->password), $commonPasswords)) {
                $validator->errors()->add('password', __('validation.admin.password_too_common'));
            }

            // Ensure password confirmation is provided when password is updated
            if (!$this->filled('password_confirmation')) {
                $validator->errors()->add('password_confirmation', __('validation.admin.password_confirmation_required'));
            }
        }
    }

    /**
     * Apply admin-specific data sanitization for updates
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

        // Remove password fields if empty (no update needed)
        if (isset($sanitized['password']) && empty($sanitized['password'])) {
            unset($sanitized['password'], $sanitized['password_confirmation']);
        }

        return $sanitized;
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Auto-generate name if not provided but first_name is
        if (!$this->filled('name') && $this->filled('first_name')) {
            $name = trim($this->first_name);
            if ($this->filled('last_name')) {
                $name .= ' ' . trim($this->last_name);
            }
            $this->merge(['name' => $name]);
        }

        // Ensure boolean conversion for is_active
        if ($this->has('is_active')) {
            $this->merge(['is_active' => $this->boolean('is_active')]);
        }

        // Convert gender numeric values to string (if needed for backward compatibility)
        if ($this->filled('gender') && is_numeric($this->gender)) {
            $genderMapping = [
                '0' => 'male',
                '1' => 'female',
                '2' => 'other',
                '3' => 'prefer_not_to_say'
            ];
            $this->merge(['gender' => $genderMapping[$this->gender] ?? 'prefer_not_to_say']);
        }
    }
}
