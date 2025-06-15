<?php

namespace App\Http\Resources\Universal;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Universal Company Resource
 * Implements Collection forget() for dynamic field filtering
 */
class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array with dynamic field removal.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Base data array
        $data = collect([
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'website' => $this->website,
            'email' => $this->email,
            'location' => $this->location,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Premium fields (will be removed for basic users)
            'premium_badge' => $this->premium_badge,
            'featured_until' => $this->featured_until,
            'priority_score' => $this->priority_score,
            'advanced_analytics' => $this->advanced_analytics,
            
            // Admin-only fields
            'admin_notes' => $this->admin_notes,
            'internal_rating' => $this->internal_rating,
            'moderation_status' => $this->moderation_status,
            'system_flags' => $this->system_flags,
            
            // Sensitive fields
            'private_contact_info' => $this->private_contact_info,
            'internal_id' => $this->internal_id,
            'audit_trail' => $this->audit_trail,
            
            // Relationships
            'jobs' => $this->whenLoaded('jobs'),
            'user' => $this->whenLoaded('user'),
        ]);
        
        // Apply dynamic field removal based on user role and subscription
        $data = $this->applyFieldFiltering($data, $request);
        
        // Add conditional links
        $data->put('links', [
            'self' => route('companys.show', $this->id),
            'jobs' => $this->when($this->jobs_count > 0, route('jobs.index', ['company_id' => $this->id])),
        ]);
        
        return $data->toArray();
    }
    
    /**
     * Apply field filtering using Collection forget() based on user permissions
     */
    protected function applyFieldFiltering($data, Request $request)
    {
        $user = $request->user();
        
        // Remove admin-only fields for non-admin users
        if (!$user || !$user->hasRole('admin')) {
            $adminOnlyFields = ['admin_notes', 'internal_rating', 'moderation_status', 'system_flags'];
            $data->forget($adminOnlyFields);
        }
        
        // Remove premium fields for users without subscription
        if (!$user || !$user->hasActiveSubscription()) {
            $premiumFields = ['premium_badge', 'featured_until', 'advanced_analytics', 'priority_score'];
            $data->forget($premiumFields);
        }
        
        // Remove sensitive fields for non-owners
        if (!$user || ($user->id !== $this->user_id && !$user->hasRole('admin'))) {
            $sensitiveFields = ['private_contact_info', 'internal_id', 'audit_trail'];
            $data->forget($sensitiveFields);
        }
        
        // Remove deprecated fields
        $deprecatedFields = ['old_company_format', 'legacy_id', 'deprecated_status'];
        $data->forget($deprecatedFields);
        
        // Guest user restrictions
        if (!$user) {
            $guestRestrictedFields = ['email', 'private_contact_info'];
            $data->forget($guestRestrictedFields);
        }
        
        return $data;
    }

    /**
     * Universal Pattern: Add metadata to the response
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
                'resource_type' => 'company',
                'user_permissions' => $this->getUserPermissions($request),
                'fields_filtered' => $this->getFilteredFieldsCount($request)
            ],
        ];
    }
    
    /**
     * Get user permissions for metadata
     */
    protected function getUserPermissions(Request $request): array
    {
        $user = $request->user();
        
        return [
            'is_admin' => $user?->hasRole('admin') ?? false,
            'has_subscription' => $user?->hasActiveSubscription() ?? false,
            'is_owner' => $user && $user->id === $this->user_id,
            'is_authenticated' => (bool) $user
        ];
    }
    
    /**
     * Count how many fields were filtered for debugging
     */
    protected function getFilteredFieldsCount(Request $request): int
    {
        $user = $request->user();
        $filteredCount = 0;
        
        if (!$user || !$user->hasRole('admin')) {
            $filteredCount += 4; // admin-only fields
        }
        
        if (!$user || !$user->hasActiveSubscription()) {
            $filteredCount += 4; // premium fields
        }
        
        if (!$user || ($user->id !== $this->user_id && !$user->hasRole('admin'))) {
            $filteredCount += 3; // sensitive fields
        }
        
        return $filteredCount;
    }
}