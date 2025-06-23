<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index(): JsonResponse|View
    {
        $admins = User::where('role', 'admin')
            ->with(['profile'])
            ->latest()
            ->paginate(15)
        ;

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $admins,
                'message' => 'Admins retrieved successfully',
            ]);
        }

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create(): JsonResponse|View
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Create admin form data',
            ]);
        }

        return view('admin.admins.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(StoreAdminRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $admin = User::create([
                'name' => $request->name,
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? '',
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'admin',
                'is_active' => $request->is_active ?? true,
                'phone' => $request->phone ?? null,
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $admin,
                    'message' => 'Admin created successfully',
                ], 201);
            }

            return redirect()->route('admin.admin.index')
                ->with('success', __('messages.admin.created_successfully'))
            ;
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create admin: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.admin.creation_failed'))
            ;
        }
    }

    /**
     * Display the specified admin.
     */
    public function show(User $admin): JsonResponse|View
    {
        $admin->load(['profile']);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $admin,
                'message' => 'Admin retrieved successfully',
            ]);
        }

        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin): JsonResponse|View
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $admin,
                'message' => 'Admin edit form data',
            ]);
        }

        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(UpdateAdminRequest $request, User $admin): JsonResponse|RedirectResponse
    {
        try {
            $updateData = [
                'name' => $request->name,
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? $admin->last_name,
                'email' => $request->email,
                'is_active' => $request->is_active ?? $admin->is_active,
                'phone' => $request->phone ?? $admin->phone,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = bcrypt($request->password);
            }

            $admin->update($updateData);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $admin->refresh(),
                    'message' => 'Admin updated successfully',
                ]);
            }

            return redirect()->route('admin.admin.index')
                ->with('success', __('messages.admin.updated_successfully'))
            ;
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update admin: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', __('messages.admin.update_failed'))
            ;
        }
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $admin): JsonResponse|RedirectResponse
    {
        try {
            // Prevent deletion of current user
            if ($admin->id === auth()->id()) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete your own account',
                    ], 403);
                }

                return redirect()->back()
                    ->with('error', __('messages.admin.cannot_delete_self'))
                ;
            }

            // Prevent deletion of super admin (if you have such logic)
            if ('admin@admin.com' === $admin->email || $admin->hasRole('super-admin')) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete super admin account',
                    ], 403);
                }

                return redirect()->back()
                    ->with('error', __('messages.admin.cannot_delete_super_admin'))
                ;
            }

            $admin->delete();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Admin deleted successfully',
                ]);
            }

            return redirect()->route('admin.admin.index')
                ->with('success', __('messages.admin.deleted_successfully'))
            ;
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete admin: '.$e->getMessage(),
                ], 500);
            }

            return redirect()->back()
                ->with('error', __('messages.admin.deletion_failed'))
            ;
        }
    }

    /**
     * Toggle admin active status.
     */
    public function toggleStatus(User $admin): JsonResponse
    {
        try {
            $admin->update(['is_active' => !$admin->is_active]);

            return response()->json([
                'success' => true,
                'data' => ['is_active' => $admin->is_active],
                'message' => 'Admin status updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin status: '.$e->getMessage(),
            ], 500);
        }
    }
}
