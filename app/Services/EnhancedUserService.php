<?php

namespace App\Services;

use App\Models\User;
use App\Exceptions\UserCreationException;
use App\Exceptions\UserUpdateException;
use App\Exceptions\UserDeletionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Exception;

class EnhancedUserService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Create a new user with proper transaction handling
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();

        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Create user
            $user = User::create($data);

            // Assign role based on user type
            $this->assignUserRole($user, $data['user_type']);

            // Send welcome notification
            $this->notificationService->sendWelcomeEmail($user);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UserCreationException('Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Update user with transaction handling
     */
    public function updateUser(User $user, array $data): User
    {
        DB::beginTransaction();

        try {
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Update user
            $user->update($data);

            // Update role if user type changed
            if (isset($data['user_type']) && $data['user_type'] !== $user->user_type) {
                $user->syncRoles([]);
                $this->assignUserRole($user, $data['user_type']);
            }

            DB::commit();
            return $user->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw new UserUpdateException('Failed to update user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user with cascade handling
     */
    public function deleteUser(User $user): bool
    {
        DB::beginTransaction();

        try {
            // Handle related records based on user type
            if ($user->isEmployer() && $user->company) {
                // Soft delete company jobs first
                $user->company->jobs()->delete();
                // Soft delete company
                $user->company->delete();
            }

            if ($user->isCandidate() && $user->candidate) {
                // Delete candidate profile
                $user->candidate->delete();
            }

            // Delete user media files
            $user->media()->delete();

            // Soft delete user
            $user->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UserDeletionException('Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Search users with filters
     */
    public function searchUsers(array $filters): LengthAwarePaginator
    {
        $query = User::query();

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('first_name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('last_name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('email', 'LIKE', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['user_type'])) {
            $query->byType($filters['user_type']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['is_verified'])) {
            $query->verified();
        }

        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        // Load relationships
        $query->with(['country', 'state', 'city', 'roles']);

        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'verified_users' => User::verified()->count(),
            'admins' => User::byType(User::TYPE_ADMIN)->count(),
            'employers' => User::byType(User::TYPE_EMPLOYER)->count(),
            'candidates' => User::byType(User::TYPE_CANDIDATE)->count(),
            'users_by_country' => User::select('country_id')
                ->selectRaw('count(*) as count')
                ->whereNotNull('country_id')
                ->groupBy('country_id')
                ->with('country:id,name')
                ->get()
        ];
    }

    /**
     * Activate user account
     */
    public function activateUser(User $user): bool
    {
        try {
            $user->activate();
            $this->notificationService->sendAccountActivatedEmail($user);
            return true;
        } catch (Exception $e) {
            throw new UserUpdateException('Failed to activate user: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate user account
     */
    public function deactivateUser(User $user): bool
    {
        try {
            $user->deactivate();
            $this->notificationService->sendAccountDeactivatedEmail($user);
            return true;
        } catch (Exception $e) {
            throw new UserUpdateException('Failed to deactivate user: ' . $e->getMessage());
        }
    }

    /**
     * Verify user email
     */
    public function verifyUserEmail(User $user): bool
    {
        try {
            $user->update([
                'email_verified_at' => now(),
                'is_verified' => true
            ]);
            return true;
        } catch (Exception $e) {
            throw new UserUpdateException('Failed to verify user email: ' . $e->getMessage());
        }
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $newPassword): bool
    {
        try {
            $user->update(['password' => Hash::make($newPassword)]);
            $this->notificationService->sendPasswordChangedEmail($user);
            return true;
        } catch (Exception $e) {
            throw new UserUpdateException('Failed to change password: ' . $e->getMessage());
        }
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        return User::role($role)->active()->get();
    }

    /**
     * Assign role to user based on user type
     */
    private function assignUserRole(User $user, int $userType): void
    {
        $roleName = match($userType) {
            User::TYPE_ADMIN => 'Admin',
            User::TYPE_EMPLOYER => 'Employer',
            User::TYPE_CANDIDATE => 'Candidate',
            default => throw new \InvalidArgumentException('Invalid user type: ' . $userType)
        };

        $user->assignRole($roleName);
    }

    /**
     * Bulk update users
     */
    public function bulkUpdateUsers(array $userIds, array $data): int
    {
        DB::beginTransaction();

        try {
            $updated = User::whereIn('id', $userIds)->update($data);
            DB::commit();
            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UserUpdateException('Failed to bulk update users: ' . $e->getMessage());
        }
    }

    /**
     * Export users data
     */
    public function exportUsers(array $filters = []): Collection
    {
        $query = User::query();

        // Apply same filters as search
        if (!empty($filters['user_type'])) {
            $query->byType($filters['user_type']);
        }

        if (!empty($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->with(['country', 'state', 'city', 'roles'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
} 