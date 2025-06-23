<?php

namespace App\Policies;

use App\Models\JobType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class JobTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any job types.
     */
    public function viewAny(?User $user): Response
    {
        // Anyone can view job types (public access)
        return Response::allow();
    }

    /**
     * Determine whether the user can view the job type.
     */
    public function view(?User $user, JobType $jobType): Response
    {
        // Anyone can view active job types
        if ($jobType->is_active) {
            return Response::allow();
        }

        // Only authenticated users with proper permissions can view inactive job types
        if (!$user) {
            return Response::deny('You must be logged in to view inactive job types.');
        }

        return $this->canManageJobTypes($user)
            ? Response::allow()
            : Response::deny('You do not have permission to view inactive job types.');
    }

    /**
     * Determine whether the user can create job types.
     */
    public function create(User $user): Response
    {
        if ($user->hasRole('admin')) {
            return Response::allow();
        }

        if ($user->hasRole('super_admin')) {
            return Response::allow();
        }

        // HR managers can create job types
        if ($user->hasRole('hr_manager')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to create job types.');
    }

    /**
     * Determine whether the user can update the job type.
     */
    public function update(User $user, JobType $jobType): Response
    {
        if ($user->hasRole('admin')) {
            return Response::allow();
        }

        if ($user->hasRole('super_admin')) {
            return Response::allow();
        }

        // HR managers can update job types
        if ($user->hasRole('hr_manager')) {
            return Response::allow();
        }

        // Prevent updating default job types by non-admin users
        if ($jobType->is_default && !$user->hasRole(['admin', 'super_admin'])) {
            return Response::deny('You cannot modify default job types.');
        }

        return Response::deny('You do not have permission to update job types.');
    }

    /**
     * Determine whether the user can delete the job type.
     */
    public function delete(User $user, JobType $jobType): Response
    {
        if ($user->hasRole('super_admin')) {
            return Response::allow();
        }

        if ($user->hasRole('admin')) {
            // Admins can delete non-default job types
            if ($jobType->is_default) {
                return Response::deny('Default job types cannot be deleted.');
            }

            return Response::allow();
        }

        // HR managers can delete custom job types only
        if ($user->hasRole('hr_manager')) {
            if ($jobType->is_default) {
                return Response::deny('You cannot delete default job types.');
            }

            // Check if job type is in use
            if ($jobType->jobs()->exists()) {
                return Response::deny('Cannot delete job type that is currently in use by jobs.');
            }

            return Response::allow();
        }

        return Response::deny('You do not have permission to delete job types.');
    }

    /**
     * Determine whether the user can restore the job type.
     */
    public function restore(User $user, JobType $jobType): Response
    {
        return $this->canManageJobTypes($user)
            ? Response::allow()
            : Response::deny('You do not have permission to restore job types.');
    }

    /**
     * Determine whether the user can permanently delete the job type.
     */
    public function forceDelete(User $user, JobType $jobType): Response
    {
        if ($user->hasRole('super_admin')) {
            return Response::allow();
        }

        return Response::deny('Only super administrators can permanently delete job types.');
    }

    /**
     * Determine whether the user can manage job type status (activate/deactivate).
     */
    public function manageStatus(User $user, JobType $jobType): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        // HR managers can manage status of custom job types
        if ($user->hasRole('hr_manager') && !$jobType->is_default) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage job type status.');
    }

    /**
     * Determine whether the user can feature/unfeature job types.
     */
    public function manageFeature(User $user, JobType $jobType): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        // HR managers can feature custom job types
        if ($user->hasRole('hr_manager') && !$jobType->is_default) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage featured job types.');
    }

    /**
     * Determine whether the user can perform bulk operations.
     */
    public function bulkUpdate(User $user): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        if ($user->hasRole('hr_manager')) {
            return Response::allow('Limited bulk operations available.');
        }

        return Response::deny('You do not have permission to perform bulk operations.');
    }

    /**
     * Determine whether the user can view job type statistics.
     */
    public function viewStatistics(User $user): Response
    {
        return $this->canManageJobTypes($user)
            ? Response::allow()
            : Response::deny('You do not have permission to view job type statistics.');
    }

    /**
     * Determine whether the user can export job types.
     */
    public function export(User $user): Response
    {
        return $this->canManageJobTypes($user)
            ? Response::allow()
            : Response::deny('You do not have permission to export job types.');
    }

    /**
     * Determine whether the user can import job types.
     */
    public function import(User $user): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to import job types.');
    }

    /**
     * Determine whether the user can duplicate job types.
     */
    public function duplicate(User $user, JobType $jobType): Response
    {
        // Can duplicate if can create
        return $this->create($user);
    }

    /**
     * Determine whether the user can view SEO fields.
     */
    public function viewSeo(User $user, JobType $jobType): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        if ($user->hasRole('seo_manager')) {
            return Response::allow();
        }

        if ($user->hasRole('hr_manager')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view SEO fields.');
    }

    /**
     * Determine whether the user can manage SEO fields.
     */
    public function manageSeo(User $user, JobType $jobType): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        if ($user->hasRole('seo_manager')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage SEO fields.');
    }

    /**
     * Determine whether the user can manage cache.
     */
    public function manageCache(User $user): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to manage cache.');
    }

    /**
     * Determine whether the user can suggest new job types.
     */
    public function suggest(User $user): Response
    {
        // Employers can suggest new job types
        if ($user->hasRole('employer')) {
            return Response::allow();
        }

        // HR managers can suggest job types
        if ($user->hasRole('hr_manager')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to suggest job types.');
    }

    /**
     * Determine whether the user can approve suggested job types.
     */
    public function approveSuggestion(User $user): Response
    {
        return $this->canManageJobTypes($user)
            ? Response::allow()
            : Response::deny('You do not have permission to approve job type suggestions.');
    }

    /**
     * Determine whether the user can access admin features.
     */
    public function accessAdmin(User $user): Response
    {
        return $this->canManageJobTypes($user)
            ? Response::allow()
            : Response::deny('You do not have permission to access admin features.');
    }

    /**
     * Determine whether the user can view system job types.
     */
    public function viewSystem(User $user): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view system job types.');
    }

    /**
     * Determine whether the user can modify default job types.
     */
    public function modifyDefault(User $user, JobType $jobType): Response
    {
        if (!$jobType->is_default) {
            return Response::allow(); // Not a default job type
        }

        if ($user->hasRole('super_admin')) {
            return Response::allow();
        }

        if ($user->hasRole('admin')) {
            return Response::allow('Limited modifications allowed.');
        }

        return Response::deny('You cannot modify default job types.');
    }

    /**
     * Determine whether the user can perform dangerous operations.
     */
    public function dangerousOperations(User $user): Response
    {
        if ($user->hasRole('super_admin')) {
            return Response::allow();
        }

        return Response::deny('You do not have permission for dangerous operations.');
    }

    /**
     * Check if user can perform bulk operations on specific job types.
     */
    public function bulkUpdateSpecific(User $user, array $jobTypeIds): Response
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return Response::allow();
        }

        if ($user->hasRole('hr_manager')) {
            // Check if any of the job types are default types
            $hasDefaultTypes = JobType::whereIn('id', $jobTypeIds)
                ->where('is_default', true)
                ->exists()
            ;

            if ($hasDefaultTypes) {
                return Response::deny('You cannot perform bulk operations on default job types.');
            }

            return Response::allow();
        }

        return Response::deny('You do not have permission to perform bulk operations.');
    }

    /**
     * Check if user can access job type analytics.
     */
    public function viewAnalytics(User $user): Response
    {
        return $this->canManageJobTypes($user)
            ? Response::allow()
            : Response::deny('You do not have permission to view job type analytics.');
    }

    /**
     * Check rate limiting permissions.
     */
    public function bypassRateLimit(User $user): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Check if user can manage job types in general.
     */
    private function canManageJobTypes(User $user): bool
    {
        return $user->hasAnyRole([
            'admin',
            'super_admin',
            'hr_manager',
            'seo_manager',
        ]);
    }

    /**
     * Check if user is an admin-level user.
     */
    private function isAdmin(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }
}
