<?php

namespace App\Policies;

use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JobApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['Admin', 'Employer', 'Candidate']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JobApplication $jobApplication): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Employer') && $user->id === $jobApplication->job->user_id) || 
               ($user->hasRole('Candidate') && $user->id === $jobApplication->user_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('Candidate');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JobApplication $jobApplication): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Candidate') && $user->id === $jobApplication->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JobApplication $jobApplication): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Candidate') && $user->id === $jobApplication->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JobApplication $jobApplication): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JobApplication $jobApplication): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can update application status.
     */
    public function updateStatus(User $user, JobApplication $jobApplication): bool
    {
        return $user->hasRole('Admin') || 
               ($user->hasRole('Employer') && $user->id === $jobApplication->job->user_id);
    }
}
