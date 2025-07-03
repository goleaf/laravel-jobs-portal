<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get all users with pagination.
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return User::with(['roles'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        DB::beginTransaction();

        try {
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => $data['user_type'] ?? User::CANDIDATE,
                'is_active' => $data['is_active'] ?? true,
                'phone' => $data['phone'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'state_id' => $data['state_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
            ];

            $user = User::create($userData);

            // Assign default role based on user type
            $role = match ($user->user_type) {
                User::ADMIN => 'Admin',
                User::EMPLOYER => 'Employer',
                User::CANDIDATE => 'Candidate',
                default => 'Candidate'
            };

            $user->assignRole($role);

            DB::commit();

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Update user data.
     */
    public function update(User $user, array $data): User
    {
        DB::beginTransaction();

        try {
            // Handle password update separately
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            $user->update($data);

            DB::commit();

            return $user->fresh();
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Soft delete user and related data.
     */
    public function delete(User $user): bool
    {
        DB::beginTransaction();

        try {
            // Deactivate instead of delete for data integrity
            $user->update(['is_active' => false]);

            // If user has a company, deactivate it
            if ($user->company) {
                $user->company->user->update(['is_active' => false]);
            }

            // If user has candidate profile, deactivate it
            if ($user->candidate) {
                // Could add candidate-specific cleanup here
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Search users by various criteria.
     */
    public function search(string $query, ?string $role = null): Collection
    {
        $users = User::query()
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->when($role, fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', $role)))
            ->with(['roles'])
            ->get();

        return $users;
    }

    /**
     * Get users by role.
     */
    public function getByRole(string $role, ?int $limit = null): Collection
    {
        $query = User::whereHas('roles', fn ($q) => $q->where('name', $role))
            ->active()
            ->with(['roles']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Toggle user active status.
     */
    public function toggleActiveStatus(User $user): User
    {
        $user->update(['is_active' => ! $user->is_active]);

        return $user->fresh();
    }

    /**
     * Get active employers with companies.
     */
    public function getActiveEmployers(): Collection
    {
        return User::whereHas('roles', fn ($q) => $q->where('name', 'Employer'))
            ->whereHas('company')
            ->active()
            ->with(['company', 'company.industry'])
            ->get();
    }

    /**
     * Get active candidates.
     */
    public function getActiveCandidates(): Collection
    {
        return User::whereHas('roles', fn ($q) => $q->where('name', 'Candidate'))
            ->whereHas('candidate')
            ->active()
            ->with(['candidate'])
            ->get();
    }
}
