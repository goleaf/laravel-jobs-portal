<?php

namespace App\Http\Controllers\Enhanced;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Enhanced AdminController - Enhanced patterns implementation.
 *
 * Demonstrates modern Laravel controller patterns with:
 * - Advanced caching strategies
 * - Comprehensive error handling
 * - Performance optimization
 * - Enhanced security features
 * - Bulk operations support
 * - Admin activity tracking
 */
class AdminController extends AppBaseController
{
    /**
     * Cache TTL for admin operations (30 minutes).
     */
    private const CACHE_TTL = 1800;

    /**
     * Display a listing of admin users with enhanced filtering and search.
     */
    public function index(Request $request)
    {
        try {
            // Check if this is an API request
            if ($this->isApiRequest($request)) {
                return $this->getAdminsApi($request);
            }

            // For web requests, return the view with enhanced data
            $data = $this->prepareAdminsIndexData($request);

            return view('admin.admins.index', $data);
        } catch (\Exception $e) {
            Log::error('Error in AdminController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            if ($this->isApiRequest($request)) {
                return $this->sendServerError('Failed to retrieve admins');
            }

            return redirect()->back()->with('error', 'Failed to load admins');
        }
    }

    /**
     * Store a newly created admin with enhanced validation and security.
     */
    public function store(StoreAdminRequest $request): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $admin = User::create([
                'name' => $request->name,
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
                'is_active' => $request->boolean('is_active', true),
                'phone' => $request->phone,
                'created_by' => auth()->id(),
                'email_verified_at' => now(),
            ]);

            // Clear related caches
            $this->clearAdminCaches();

            // Log the creation
            Log::info('Admin created successfully', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            if ($this->isApiRequest($request)) {
                return $this->sendResponse($admin->load(['profile']), 'Admin created successfully');
            }

            return redirect()->route('admin.admin.index')
                ->with('success', 'Admin created successfully')
            ;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating admin', [
                'error' => $e->getMessage(),
                'input' => $request->except(['password']),
                'user_id' => auth()->id(),
            ]);

            if ($this->isApiRequest($request)) {
                return $this->sendServerError('Failed to create admin');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create admin')
            ;
        }
    }

    /**
     * Update the specified admin with enhanced validation and security.
     */
    public function update(UpdateAdminRequest $request, User $admin): JsonResponse|RedirectResponse
    {
        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? $admin->last_name,
                'email' => $request->email,
                'is_active' => $request->boolean('is_active', $admin->is_active),
                'phone' => $request->phone ?? $admin->phone,
                'updated_by' => auth()->id(),
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $admin->update($updateData);

            // Clear related caches
            $this->clearAdminCaches($admin->id);

            // Log the update
            Log::info('Admin updated successfully', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'updated_by' => auth()->id(),
                'changes' => $admin->getChanges(),
            ]);

            DB::commit();

            if ($this->isApiRequest($request)) {
                return $this->sendResponse($admin->fresh()->load(['profile']), 'Admin updated successfully');
            }

            return redirect()->route('admin.admin.index')
                ->with('success', 'Admin updated successfully')
            ;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating admin', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
                'input' => $request->except(['password']),
            ]);

            if ($this->isApiRequest($request)) {
                return $this->sendServerError('Failed to update admin');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update admin')
            ;
        }
    }

    /**
     * Remove the specified admin with enhanced security checks.
     */
    public function destroy(User $admin): JsonResponse|RedirectResponse
    {
        try {
            // Enhanced security checks
            if ($admin->id === auth()->id()) {
                $message = 'Cannot delete your own account';
                if ($this->isApiRequest(request())) {
                    return $this->sendError($message, 403);
                }

                return redirect()->back()->with('error', $message);
            }

            // Prevent deletion of super admin
            if ('admin@admin.com' === $admin->email) {
                $message = 'Cannot delete super admin account';
                if ($this->isApiRequest(request())) {
                    return $this->sendError($message, 403);
                }

                return redirect()->back()->with('error', $message);
            }

            DB::beginTransaction();

            // Archive instead of delete to maintain audit trail
            $admin->update([
                'is_active' => false,
                'deleted_at' => now(),
                'deleted_by' => auth()->id(),
            ]);

            // Clear related caches
            $this->clearAdminCaches($admin->id);

            // Log the deletion
            Log::info('Admin deleted/archived successfully', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'deleted_by' => auth()->id(),
            ]);

            DB::commit();

            if ($this->isApiRequest(request())) {
                return $this->sendSuccess('Admin deleted successfully');
            }

            return redirect()->route('admin.admin.index')
                ->with('success', 'Admin deleted successfully')
            ;
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting admin', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);

            if ($this->isApiRequest(request())) {
                return $this->sendServerError('Failed to delete admin');
            }

            return redirect()->back()->with('error', 'Failed to delete admin');
        }
    }

    /**
     * Toggle admin status (active/inactive).
     */
    public function toggleStatus(User $admin): JsonResponse
    {
        try {
            // Prevent deactivating own account
            if ($admin->id === auth()->id()) {
                return $this->sendError('Cannot deactivate your own account', 403);
            }

            DB::beginTransaction();

            $newStatus = !$admin->is_active;
            $admin->update([
                'is_active' => $newStatus,
                'status_changed_by' => auth()->id(),
                'status_changed_at' => now(),
            ]);

            // Clear related caches
            $this->clearAdminCaches($admin->id);

            // Log the status change
            Log::info('Admin status toggled', [
                'admin_id' => $admin->id,
                'admin_email' => $admin->email,
                'new_status' => $newStatus ? 'active' : 'inactive',
                'changed_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendResponse($admin->fresh(), 'Admin status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error toggling admin status', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to update admin status');
        }
    }

    /**
     * Bulk actions for admins.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'admin_ids' => 'required|array|min:1',
            'admin_ids.*' => 'exists:users,id',
        ]);

        try {
            DB::beginTransaction();

            $adminIds = $request->get('admin_ids');
            $action = $request->get('action');
            $affectedCount = 0;

            // Prevent bulk actions on current user
            if (in_array(auth()->id(), $adminIds)) {
                return $this->sendError('Cannot perform bulk actions on your own account');
            }

            switch ($action) {
                case 'activate':
                    $affectedCount = User::whereIn('id', $adminIds)->update([
                        'is_active' => true,
                        'status_changed_by' => auth()->id(),
                        'status_changed_at' => now(),
                    ]);

                    break;

                case 'deactivate':
                    $affectedCount = User::whereIn('id', $adminIds)->update([
                        'is_active' => false,
                        'status_changed_by' => auth()->id(),
                        'status_changed_at' => now(),
                    ]);

                    break;

                case 'delete':
                    $affectedCount = User::whereIn('id', $adminIds)->update([
                        'is_active' => false,
                        'deleted_at' => now(),
                        'deleted_by' => auth()->id(),
                    ]);

                    break;
            }

            // Clear related caches
            $this->clearAdminCaches();

            // Log the bulk action
            Log::info('Bulk action performed on admins', [
                'action' => $action,
                'admin_ids' => $adminIds,
                'affected_count' => $affectedCount,
                'performed_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendSuccess("Successfully {$action}ed {$affectedCount} admin(s)");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error performing bulk action on admins', [
                'action' => $request->get('action'),
                'admin_ids' => $request->get('admin_ids'),
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to perform bulk action');
        }
    }

    /**
     * Get admins for API requests with enhanced filtering.
     */
    private function getAdminsApi(Request $request): JsonResponse
    {
        $cacheKey = $this->buildCacheKey('admins.api', $request->all());

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            $query = User::where('role', 'admin')->with(['profile']);

            // Apply Enhanced scopes for filtering
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $search = $request->get('search');
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                    ;
                });
            }

            if ($request->filled('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            if ($request->filled('created_from')) {
                $query->where('created_at', '>=', $request->get('created_from'));
            }

            if ($request->filled('created_to')) {
                $query->where('created_at', '<=', $request->get('created_to'));
            }

            // Apply sorting
            $sortBy = $request->get('sort', 'created_at');
            $sortDirection = $request->get('direction', 'desc');

            if (in_array($sortBy, ['name', 'email', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->latest();
            }

            return $query->paginate($request->get('per_page', 15));
        });

        return $this->sendPaginatedResponse($data, 'Admins retrieved successfully');
    }

    /**
     * Prepare data for admins index view.
     */
    private function prepareAdminsIndexData(Request $request): array
    {
        $cacheKey = $this->buildCacheKey('admins.index.data', $request->all());

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            // Get admins with enhanced filtering
            $admins = User::where('role', 'admin')
                ->with(['profile'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->get('search');
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                        ;
                    });
                })
                ->when($request->filled('is_active'), function ($query) use ($request) {
                    $query->where('is_active', $request->boolean('is_active'));
                })
                ->latest()
                ->paginate(20)
            ;

            // Get admin statistics
            $statistics = $this->getAdminStatistics();

            return [
                'admins' => $admins,
                'statistics' => $statistics,
                'filters' => $request->only(['search', 'is_active']),
            ];
        });
    }

    /**
     * Get admin statistics for dashboard.
     */
    private function getAdminStatistics(): array
    {
        return Cache::remember('admins.statistics', self::CACHE_TTL, function () {
            return [
                'total_admins' => User::where('role', 'admin')->count(),
                'active_admins' => User::where('role', 'admin')->where('is_active', true)->count(),
                'inactive_admins' => User::where('role', 'admin')->where('is_active', false)->count(),
                'recent_admins' => User::where('role', 'admin')->where('created_at', '>=', now()->subDays(30))->count(),
                'admins_today' => User::where('role', 'admin')->whereDate('created_at', today())->count(),
            ];
        });
    }

    /**
     * Clear admin-related caches.
     */
    private function clearAdminCaches(?int $adminId = null): void
    {
        $tags = ['admins', 'admins.api', 'admins.index', 'admins.statistics'];

        if ($adminId) {
            $tags[] = "admin.show.{$adminId}";
        }

        foreach ($tags as $tag) {
            Cache::tags($tag)->flush();
        }

        Cache::forget('admins.statistics');
    }
}
