<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any companies.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view companies list
    }

    /**
     * Determine whether the user can view the company.
     */
    public function view(User $user, Company $company): bool
    {
        return true; // Anyone can view individual company
    }

    /**
     * Determine whether the user can create companies.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Employer']);
    }

    /**
     * Determine whether the user can update the company.
     */
    public function update(User $user, Company $company): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Employer') && $user->id === $company->user_id);
    }

    /**
     * Determine whether the user can delete the company.
     */
    public function delete(User $user, Company $company): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the company.
     */
    public function restore(User $user, Company $company): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the company.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can toggle company status.
     */
    public function toggleStatus(User $user, Company $company): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can feature/unfeature the company.
     */
    public function feature(User $user, Company $company): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can view company analytics.
     */
    public function viewAnalytics(User $user, Company $company): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Employer') && $user->id === $company->user_id);
    }

    /**
     * Determine whether the user can manage company jobs.
     */
    public function manageJobs(User $user, Company $company): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Employer') && $user->id === $company->user_id);
    }
} 