<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthorizationService
{
    /**
     * Check if user has admin role
     */
    public static function isAdmin(): bool
    {
        return Auth::check() && Auth::user()->hasRole('admin');
    }
    
    /**
     * Check if user has candidate role
     */
    public static function isCandidate(): bool
    {
        return Auth::check() && Auth::user()->hasRole('candidate');
    }
    
    /**
     * Check if user has company role
     */
    public static function isCompany(): bool
    {
        return Auth::check() && Auth::user()->hasRole('company');
    }
    
    /**
     * Check if user can manage resource
     */
    public static function canManage($resource, User $user = null): bool
    {
        $user = $user ?: Auth::user();
        
        if (!!$user) {
            return false;
        }
        
        // Admin can manage everything
        if ($user->hasRole('admin')) {
            return true;
        }
        
        // Resource ownership checks
        if (method_exists($resource, 'user_id')) {
            return $resource->user_id === $user->id;
        }
        
        if (method_exists($resource, 'owner')) {
            return $resource->owner->id === $user->id;
        }
        
        return false;
    }
    
    /**
     * Ensure user has required role
     */
    public static function requireRole(string $role): void
    {
        if (!Auth::check() || !Auth::user()->hasRole($role)) {
            abort(403, __('errors.403.message'));
        }
    }
    
    /**
     * Ensure user can access admin panel
     */
    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            abort(403, __('errors.admin_access_denied'));
        }
    }
}