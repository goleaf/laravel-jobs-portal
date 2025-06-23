<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;

class JobPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view job listings
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Job $job): bool
    {
        return true; // Anyone can view individual job
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['Admin', 'Employer']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Job $job): bool
    {
        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $user->id === $job->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Job $job): bool
    {
        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $user->id === $job->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Job $job): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Job $job): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can toggle job status.
     */
    public function toggleStatus(User $user, Job $job): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can feature/unfeature the job.
     */
    public function feature(User $user, Job $job): bool
    {
        return $user->hasRole('Admin');
    }
}
