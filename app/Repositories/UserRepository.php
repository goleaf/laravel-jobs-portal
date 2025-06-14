<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Enhanced User Repository
 * 
 * Comprehensive user management repository with Enhanced patterns
 */
class EnhancedUserRepository extends EnhancedBaseRepository
{
    /**
     * Cache duration for user-related queries (in minutes)
     */
    private const CACHE_DURATION = 30;

    protected array $defaultWith = [
        'roles', 
        'permissions',
        'candidate',
        'company'
    ];
    
    protected array $searchableFields = [
        'first_name',
        'last_name',
        'email',
        'phone'
    ];
    
    protected array $filterableFields = [
        'is_active',
        'is_verified',
        'user_type',
        'created_at',
        'last_login_at',
        'email_verified_at'
    ];
    
    protected array $sortableFields = [
        'id',
        'first_name',
        'last_name',
        'email',
        'created_at',
        'updated_at',
        'last_login_at'
    ];

    protected function getModelClass(): string
    {
        return User::class;
    }

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find users with advanced filtering and search capabilities
     */
    public function findUsersWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'users:filtered:' . md5(serialize($filters) . $perPage);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($filters, $perPage) {
            $query = $this->model->newQuery()
                ->with($this->defaultWith);

            // Apply search filters
            $this->applyUserSearchFilters($query, $filters);
            
            // Apply role filters
            $this->applyRoleFilters($query, $filters);
            
            // Apply status filters
            $this->applyStatusFilters($query, $filters);
            
            // Apply date filters
            $this->applyUserDateFilters($query, $filters);

            // Apply sorting
            $this->applyUserSorting($query, $filters['sort'] ?? 'latest');

            return $query->paginate($perPage);
        });
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $roleName, int $limit = null): Collection
    {
        $cacheKey = "users:role:{$roleName}:" . ($limit ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($roleName, $limit) {
            $query = $this->model->newQuery()
                ->with($this->defaultWith)
                ->whereHas('roles', function ($roleQuery) use ($roleName) {
                    $roleQuery->where('name', $roleName);
                })
                ->where('is_active', true);

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }

    /**
     * Get active users with recent activity
     */
    public function getActiveUsers(int $days = 30, int $limit = 50): Collection
    {
        $cacheKey = "users:active:{$days}:{$limit}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($days, $limit) {
            return $this->model->newQuery()
                ->with(['roles'])
                ->where('is_active', true)
                ->where('last_login_at', '>=', now()->subDays($days))
                ->orderBy('last_login_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get user statistics for dashboard
     */
    public function getUserStatistics(): array
    {
        $cacheKey = 'users:statistics';

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $totalUsers = $this->model->count();
            $activeUsers = $this->model->where('is_active', true)->count();
            $verifiedUsers = $this->model->whereNotNull('email_verified_at')->count();
            
            $todayUsers = $this->model->whereDate('created_at', today())->count();
            $weekUsers = $this->model->where('created_at', '>=', now()->subDays(7))->count();
            $monthUsers = $this->model->where('created_at', '>=', now()->subDays(30))->count();

            $recentLogins = $this->model->where('last_login_at', '>=', now()->subDays(7))->count();

            // Role distribution
            $roleStats = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('COUNT(*) as count'))
                ->where('model_type', User::class)
                ->groupBy('roles.name')
                ->pluck('count', 'name')
                ->toArray();

            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'verified_users' => $verifiedUsers,
                'verification_rate' => $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 2) : 0,
                'today_registrations' => $todayUsers,
                'week_registrations' => $weekUsers,
                'month_registrations' => $monthUsers,
                'recent_logins' => $recentLogins,
                'role_distribution' => $roleStats,
                'inactive_users' => $totalUsers - $activeUsers,
            ];
        });
    }

    /**
     * Create user with role assignment
     */
    public function createUserWithRole(array $userData, string $roleName): User
    {
        try {
            return DB::transaction(function () use ($userData, $roleName) {
                // Hash password if provided
                if (isset($userData['password'])) {
                    $userData['password'] = Hash::make($userData['password']);
                }

                // Create user
                $user = $this->model->create($userData);

                // Assign role
                if ($role = Role::where('name', $roleName)->first()) {
                    $user->assignRole($role);
                }

                $this->clearUserCaches();
                $this->logActivity('created_with_role', $user, ['role' => $roleName]);

                return $user->load($this->defaultWith);
            });
        } catch (\Exception $e) {
            $this->logError('Failed to create user with role', [
                'user_data' => $userData,
                'role' => $roleName,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update user password
     */
    public function updatePassword(int $userId, string $newPassword): bool
    {
        try {
            $hashedPassword = Hash::make($newPassword);
            
            $updated = $this->model->where('id', $userId)
                ->update([
                    'password' => $hashedPassword,
                    'password_changed_at' => now()
                ]);

            if ($updated) {
                $this->clearUserCaches($userId);
                $this->logActivity('password_updated', $this->findById($userId));
            }

            return $updated > 0;
        } catch (\Exception $e) {
            $this->logError('Failed to update password', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Verify user email
     */
    public function verifyEmail(int $userId): bool
    {
        try {
            $updated = $this->model->where('id', $userId)
                ->whereNull('email_verified_at')
                ->update(['email_verified_at' => now()]);

            if ($updated) {
                $this->clearUserCaches($userId);
                $this->logActivity('email_verified', $this->findById($userId));
            }

            return $updated > 0;
        } catch (\Exception $e) {
            $this->logError('Failed to verify email', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update user last login
     */
    public function updateLastLogin(int $userId, ?string $ipAddress = null): bool
    {
        try {
            $data = ['last_login_at' => now()];
            
            if ($ipAddress) {
                $data['last_login_ip'] = $ipAddress;
            }

            $updated = $this->model->where('id', $userId)->update($data);

            if ($updated) {
                $this->clearUserCaches($userId);
            }

            return $updated > 0;
        } catch (\Exception $e) {
            $this->logError('Failed to update last login', [
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Activate/Deactivate user
     */
    public function toggleUserStatus(int $userId, bool $isActive): bool
    {
        try {
            $updated = $this->model->where('id', $userId)
                ->update([
                    'is_active' => $isActive,
                    'status_changed_at' => now()
                ]);

            if ($updated) {
                $this->clearUserCaches($userId);
                $this->logActivity($isActive ? 'activated' : 'deactivated', $this->findById($userId));
            }

            return $updated > 0;
        } catch (\Exception $e) {
            $this->logError('Failed to toggle user status', [
                'user_id' => $userId,
                'is_active' => $isActive,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get users requiring attention (unverified, inactive, etc.)
     */
    public function getUsersRequiringAttention(): array
    {
        $cacheKey = 'users:requiring_attention';

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () {
            $unverifiedUsers = $this->model->whereNull('email_verified_at')
                ->where('created_at', '<=', now()->subDays(1))
                ->count();

            $inactiveUsers = $this->model->where('is_active', false)
                ->count();

            $staleUsers = $this->model->where('last_login_at', '<=', now()->subDays(90))
                ->where('is_active', true)
                ->count();

            $incompleteProfiles = $this->model->where(function ($query) {
                $query->whereNull('first_name')
                      ->orWhereNull('last_name')
                      ->orWhereNull('phone');
            })->where('is_active', true)->count();

            return [
                'unverified_users' => $unverifiedUsers,
                'inactive_users' => $inactiveUsers,
                'stale_users' => $staleUsers,
                'incomplete_profiles' => $incompleteProfiles,
                'total_attention_required' => $unverifiedUsers + $inactiveUsers + $staleUsers + $incompleteProfiles
            ];
        });
    }

    /**
     * Search users by multiple criteria
     */
    public function searchUsers(string $query, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = 'users:search:' . md5($query . serialize($filters) . $perPage);

        return Cache::remember($cacheKey, self::CACHE_DURATION / 2, function () use ($query, $filters, $perPage) {
            $builder = $this->model->newQuery()
                ->with($this->defaultWith);

            // Search across multiple fields
            if (!empty($query)) {
                $builder->where(function ($q) use ($query) {
                    $q->where('first_name', 'LIKE', "%{$query}%")
                      ->orWhere('last_name', 'LIKE', "%{$query}%")
                      ->orWhere('email', 'LIKE', "%{$query}%")
                      ->orWhere('phone', 'LIKE', "%{$query}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
                });
            }

            // Apply additional filters
            $this->applyUserSearchFilters($builder, $filters);
            $this->applyRoleFilters($builder, $filters);
            $this->applyStatusFilters($builder, $filters);

            // Apply sorting
            $this->applyUserSorting($builder, $filters['sort'] ?? 'relevance');

            return $builder->paginate($perPage);
        });
    }

    /**
     * Bulk update user status
     */
    public function bulkUpdateUserStatus(array $userIds, bool $isActive): int
    {
        try {
            $updated = $this->model->whereIn('id', $userIds)
                ->update([
                    'is_active' => $isActive,
                    'status_changed_at' => now()
                ]);

            if ($updated > 0) {
                $this->clearUserCaches();
                $this->logActivity('bulk_status_update', null, [
                    'user_ids' => $userIds,
                    'is_active' => $isActive,
                    'updated_count' => $updated
                ]);
            }

            return $updated;
        } catch (\Exception $e) {
            $this->logError('Failed bulk user status update', [
                'user_ids' => $userIds,
                'is_active' => $isActive,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Apply user-specific search filters
     */
    private function applyUserSearchFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['name'])) {
            $name = $filters['name'];
            $query->where(function ($q) use ($name) {
                $q->where('first_name', 'LIKE', "%{$name}%")
                  ->orWhere('last_name', 'LIKE', "%{$name}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$name}%"]);
            });
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['phone'])) {
            $query->where('phone', 'LIKE', '%' . $filters['phone'] . '%');
        }
    }

    /**
     * Apply role filters
     */
    private function applyRoleFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['role'])) {
            $roles = is_array($filters['role']) ? $filters['role'] : [$filters['role']];
            $query->whereHas('roles', function ($roleQuery) use ($roles) {
                $roleQuery->whereIn('name', $roles);
            });
        }

        if (!empty($filters['permission'])) {
            $permissions = is_array($filters['permission']) ? $filters['permission'] : [$filters['permission']];
            $query->whereHas('permissions', function ($permQuery) use ($permissions) {
                $permQuery->whereIn('name', $permissions);
            });
        }
    }

    /**
     * Apply status filters
     */
    private function applyStatusFilters(Builder $query, array $filters): void
    {
        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['is_verified'])) {
            if ($filters['is_verified']) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if (!empty($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }
    }

    /**
     * Apply user-specific date filters
     */
    private function applyUserDateFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['registered_from'])) {
            $date = Carbon::parse($filters['registered_from']);
            $query->where('created_at', '>=', $date);
        }

        if (!empty($filters['registered_to'])) {
            $date = Carbon::parse($filters['registered_to']);
            $query->where('created_at', '<=', $date);
        }

        if (!empty($filters['last_login_from'])) {
            $date = Carbon::parse($filters['last_login_from']);
            $query->where('last_login_at', '>=', $date);
        }

        if (!empty($filters['last_login_to'])) {
            $date = Carbon::parse($filters['last_login_to']);
            $query->where('last_login_at', '<=', $date);
        }
    }

    /**
     * Apply user-specific sorting
     */
    private function applyUserSorting(Builder $query, string $sort): void
    {
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('first_name', 'asc')->orderBy('last_name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('first_name', 'desc')->orderBy('last_name', 'desc');
                break;

            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;

            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;

            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'last_login':
                $query->orderBy('last_login_at', 'desc');
                break;

            case 'verified_first':
                $query->orderByRaw('email_verified_at IS NOT NULL DESC')
                      ->orderBy('created_at', 'desc');
                break;

            case 'active_first':
                $query->orderBy('is_active', 'desc')
                      ->orderBy('created_at', 'desc');
                break;

            case 'relevance':
            default:
                $query->orderBy('is_active', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
        }
    }

    /**
     * Clear user-specific caches
     */
    private function clearUserCaches(?int $userId = null): void
    {
        $patterns = [
            'users:*',
            'users:statistics',
            'users:requiring_attention',
        ];

        if ($userId) {
            $patterns[] = "users:user:{$userId}";
        }

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Get user by email with caching
     */
    public function findByEmail(string $email): ?User
    {
        $cacheKey = 'users:email:' . md5($email);

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($email) {
            return $this->model->newQuery()
                ->with($this->defaultWith)
                ->where('email', $email)
                ->first();
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
     * Get users with incomplete profiles
     */
    public function getUsersWithIncompleteProfiles(int $limit = 50): Collection
    {
        $cacheKey = "users:incomplete_profiles:{$limit}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($limit) {
            return $this->model->newQuery()
                ->where(function ($query) {
                    $query->whereNull('first_name')
                          ->orWhereNull('last_name')
                          ->orWhereNull('phone')
                          ->orWhereNull('email_verified_at');
                })
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }
} 