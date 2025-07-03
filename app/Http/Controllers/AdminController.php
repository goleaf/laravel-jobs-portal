<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index(): View
    {
        // Get admin users (users without owner_type or with specific role)
        $admins = User::whereNull('owner_type')
            ->orWhere('is_super_admin', true)
            ->paginate(15);

        return view('admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create(): View
    {
        return view('admins.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(CreateAdminRequest $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = User::create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin user created successfully.');
    }

    /**
     * Display the specified admin.
     */
    public function show(User $admin): View
    {
        return view('admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin): View
    {
        return view('admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(UpdateAdminRequest $request, User $admin): RedirectResponse
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$admin->id,
        ]);

        $admin->update($validatedData);

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin user updated successfully.');
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $admin): RedirectResponse
    {
        if ($admin->id === auth()->id()) {
            return redirect()->route('admin.admin.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admin.admin.index')
            ->with('success', 'Admin user deleted successfully.');
    }
}
