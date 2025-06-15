<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class BaseTestCase extends BaseTestCase
{
    use CreatesApplication;
    
    /**
     * Remove test fields using Collection forget() for cleaner testing
     */
    protected function removeTestFields(array $data, array $fields): array
    {
        $collection = collect($data);
        $collection->forget($fields);
        return $collection->toArray();
    }
    
    /**
     * Remove fields based on user role for permission testing
     */
    protected function removeFieldsForRole(array $data, string $role): array
    {
        $collection = collect($data);
        
        $roleFieldMap = [
            'guest' => ['admin_fields', 'internal_data'],
            'basic_user' => ['premium_features', 'admin_fields'],
            'employer' => ['admin_fields'],
            'admin' => [] // Admin can access all fields
        ];
        
        if (isset($roleFieldMap[$role])) {
            $collection->forget($roleFieldMap[$role]);
        }
        
        return $collection->toArray();
    }
    
    /**
     * Clean validation test data by removing required fields
     */
    protected function createInvalidData(array $validData, array $requiredFields): array
    {
        return $this->removeTestFields($validData, $requiredFields);
    }
    
    /**
     * Remove sensitive fields for security testing
     */
    protected function removeSensitiveFields(array $data): array
    {
        $sensitiveFields = ['password', 'api_token', 'secret_key', 'private_data'];
        return $this->removeTestFields($data, $sensitiveFields);
    }
} 