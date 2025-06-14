<?php

namespace App\Repositories\Enhanced;

use App\Models\User;
use App\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * Enhanced User Repository - Enhanced Laravel Pattern
 * 
 * Domain-specific repository for User model with authentication,
 * profile management, and role-based queries.
 */
class UserRepository extends EnhancedBaseRepository implements RepositoryInterface
{
    /**
     * Searchable fields for full-text search
     */
    protected array $searchableFields = [
        'name',
        'email',
        'first_name',
        'last_name'
    ];

    /**
     * Default relations to eager load
     */
    protected array $defaultRelations = ['roles'];

    /**
     * Fields that can be filtered
     */
    protected array $filterableFields = [
        'status',
        'email_verified_at',
        'is_verified',
        'is_admin',
        'created_at',
        'updated_at'
    ];

    /**
     * Fields that can be sorted
     */
    protected array $sortableFields = [
        'id',
        'name',
        'email',
        'created_at',
        'updated_at',
        'last_login_at'
    ];

    /**
     * Get the model class name
     */
    protected function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Create user with encrypted password
     */
    public function createUser(array $data): Model
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return $this->create($data);
    }

    /**
     * Update user password
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        $hashedPassword = Hash::make($newPassword);
        
        return $this->update($userId, ['password' => $hashedPassword]) !== null;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?Model
    {
        return $this->findBy('email', $email);
    }

    /**
     * Find verified users
     */
    public function findVerifiedUsers(array $relations = []): Collection
    {
        return $this->findAllBy('is_verified', 1, $relations);
    }

    /**
     * Find users by role
     */
    public function findUsersByRole(string $role, array $relations = []): Collection
    {
        $cacheKey = $this->generateCacheKey('findUsersByRole', ['role' => $role], $relations);
        
        return $this->cache($cacheKey, $this->cacheTtl, function () use ($role, $relations) {
            $query = $this->model->newQuery();
            
            if (!empty($relations)) {
                $query->with($relations);
            }
            
            return $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            })->get();
        });
    }

    /**
     * Get admin users
     */
    public function getAdminUsers(array $relations = []): Collection
    {
        return $this->findAllBy('is_admin', 1, $relations);
    }

    /**
     * Get candidates (users with candidate role)
     */
    public function getCandidates(array $relations = ['candidate']): Collection
    {
        return $this->findUsersByRole('candidate', $relations);
    }

    /**
     * Get employers (users with employer role)
     */
    public function getEmployers(array $relations = ['company']): Collection
    {
        return $this->findUsersByRole('employer', $relations);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(int $userId): bool
    {
        return $this->update($userId, ['last_login_at' => now()]) !== null;
    }

    /**
     * Verify user email
     */
    public function verifyEmail(int $userId): bool
    {
        return $this->update($userId, [
            'email_verified_at' => now(),
            'is_verified' => 1
        ]) !== null;
    }

    /**
     * Search users with advanced filters
     */
    public function searchUsers(array $criteria): Collection
    {
        $cacheKey = $this->generateCacheKey('searchUsers', $criteria);
        
        return $this->cache($cacheKey, $this->cacheTtl / 2, function () use ($criteria) {
            $query = $this->model->newQuery();
            
            // Search by name or email
            if (isset($criteria['search'])) {
                $search = $criteria['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%");
                });
            }
            
            // Filter by verification status
            if (isset($criteria['verified'])) {
                $query->where('is_verified', $criteria['verified']);
            }
            
            // Filter by admin status
            if (isset($criteria['is_admin'])) {
                $query->where('is_admin', $criteria['is_admin']);
            }
            
            // Filter by role
            if (isset($criteria['role'])) {
                $query->whereHas('roles', function ($q) use ($criteria) {
                    $q->where('name', $criteria['role']);
                });
            }
            
            // Filter by date range
            if (isset($criteria['created_from'])) {
                $query->where('created_at', '>=', $criteria['created_from']);
            }
            
            if (isset($criteria['created_to'])) {
                $query->where('created_at', '<=', $criteria['created_to']);
            }
            
            // Apply sorting
            $sortBy = $criteria['sort_by'] ?? 'created_at';
            $sortDirection = $criteria['sort_direction'] ?? 'desc';
            $query->orderBy($sortBy, $sortDirection);
            
            // Apply limit
            $limit = $criteria['limit'] ?? 100;
            
            return $query->limit($limit)->get();
        });
    }

    /**
     * Get user statistics
     */
    public function getUserStats(): array
    {
        $cacheKey = $this->generateCacheKey('getUserStats');
        
        return $this->cache($cacheKey, $this->cacheTtl, function () {
            return [
                'total_users' => $this->count(),
                'verified_users' => $this->count(['is_verified' => 1]),
                'admin_users' => $this->count(['is_admin' => 1]),
                'recent_users' => $this->count(['created_at' => ['>=', now()->subDays(30)]]),
                'active_users' => $this->count(['last_login_at' => ['>=', now()->subDays(30)]])
            ];
        });
    }

    /**
     * Bulk update user status
     */
    public function bulkUpdateStatus(array $userIds, int $status): int
    {
        $count = $this->model->whereIn('id', $userIds)->update(['is_verified' => $status]);
        
        // Clear cache
        $this->clearModelCache();
        
        return $count;
    }

    /**
     * Get recently registered users
     */
    public function getRecentUsers(int $days = 7, int $limit = 10): Collection
    {
        $cacheKey = $this->generateCacheKey('getRecentUsers', ['days' => $days, 'limit' => $limit]);
        
        return $this->cache($cacheKey, $this->cacheTtl, function () use ($days, $limit) {
            return $this->model->where('created_at', '>=', now()->subDays($days))
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }
        
        return $query->exists();
    }

    /**
     * Soft delete user with related data cleanup
     */
    public function deleteUser(int $userId): bool
    {
        return $this->transaction(function () use ($userId) {
            $user = $this->findOrFail($userId);
            
            // Perform any related data cleanup here
            // e.g., anonymize applications, remove personal data, etc.
            
            return $user->delete();
        });
    }

    /**
     * Restore deleted user
     */
    public function restoreUser(int $userId): bool
    {
        return $this->restore($userId);
    }
} 