@extends('layouts.app')

@section('title', __('team.manage_team'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('team.manage_team') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('team.manage_team_members_permissions') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0">
                    <x-ui.button 
                        href="#" 
                        variant="primary"
                        icon="plus"
                        onclick="showInviteModal()"
                    >
                        {{ __('team.invite_member') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Team Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('team.total_members') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['total'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="user-plus" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('team.active_members') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['active'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="clock" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('team.pending_invites') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['pending'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="shield-check" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('team.admins') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $stats['admins'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('employer.team.index') }}" class="flex flex-wrap items-end gap-4">
                    <!-- Search -->
                    <div class="flex-1 min-w-0">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('team.search_members') }}
                        </label>
                        <x-ui.input
                            name="search"
                            id="search"
                            :placeholder="__('team.search_by_name_email')"
                            :value="request('search')"
                            icon="magnifying-glass"
                        />
                    </div>

                    <!-- Role Filter -->
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('team.role') }}
                        </label>
                        <x-ui.select
                            name="role"
                            id="role"
                            :options="[
                                '' => __('team.all_roles'),
                                'admin' => __('team.admin'),
                                'hr_manager' => __('team.hr_manager'),
                                'recruiter' => __('team.recruiter'),
                                'viewer' => __('team.viewer')
                            ]"
                            :selected="request('role')"
                        />
                    </div>

                    <!-- Status Filter -->
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('team.status') }}
                        </label>
                        <x-ui.select
                            name="status"
                            id="status"
                            :options="[
                                '' => __('team.all_statuses'),
                                'active' => __('team.active'),
                                'pending' => __('team.pending'),
                                'inactive' => __('team.inactive')
                            ]"
                            :selected="request('status')"
                        />
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <x-ui.button type="submit" variant="secondary">
                            {{ __('team.filter') }}
                        </x-ui.button>

                        @if(request()->hasAny(['search', 'role', 'status']))
                            <x-ui.button 
                                href="{{ route('employer.team.index') }}" 
                                variant="ghost"
                            >
                                {{ __('team.clear') }}
                            </x-ui.button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Team Members List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            @if($teamMembers && $teamMembers->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($teamMembers as $member)
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Member Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($member->avatar)
                                            <img class="h-12 w-12 rounded-full" src="{{ $member->avatar }}" alt="{{ $member->name }}">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                <x-icon name="user" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <!-- Member Name -->
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                            {{ $member->name }}
                                            @if($member->id === auth()->id())
                                                <span class="text-sm text-gray-500 dark:text-gray-400">({{ __('team.you') }})</span>
                                            @endif
                                        </h3>

                                        <!-- Member Email -->
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $member->email }}
                                        </p>

                                        <!-- Member Role & Status -->
                                        <div class="mt-2 flex items-center space-x-4">
                                            <!-- Role Badge -->
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $member->role === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                                {{ $member->role === 'hr_manager' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                                {{ $member->role === 'recruiter' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $member->role === 'viewer' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' : '' }}
                                            ">
                                                {{ __('team.role_' . $member->role) }}
                                            </span>

                                            <!-- Status Badge -->
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $member->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                                {{ $member->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                                {{ $member->status === 'inactive' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : '' }}
                                            ">
                                                <x-icon 
                                                    :name="$member->status === 'active' ? 'check-circle' : ($member->status === 'pending' ? 'clock' : 'x-circle')" 
                                                    class="h-3 w-3 mr-1" 
                                                />
                                                {{ __('team.status_' . $member->status) }}
                                            </span>

                                            <!-- Last Active -->
                                            @if($member->last_active_at)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ __('team.last_active') }}: {{ $member->last_active_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Member Permissions Preview -->
                                        @if($member->permissions && count($member->permissions) > 0)
                                            <div class="mt-2">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(array_slice($member->permissions, 0, 3) as $permission)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                            {{ __('team.permission_' . $permission) }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($member->permissions) > 3)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                            +{{ count($member->permissions) - 3 }} {{ __('team.more') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center space-x-2">
                                    @if($member->status === 'pending')
                                        <x-ui.button 
                                            href="{{ route('employer.team.resend-invite', $member) }}" 
                                            variant="secondary" 
                                            size="sm"
                                            icon="envelope"
                                        >
                                            {{ __('team.resend_invite') }}
                                        </x-ui.button>
                                    @endif

                                    <x-ui.button 
                                        href="#" 
                                        variant="secondary" 
                                        size="sm"
                                        icon="pencil"
                                        onclick="editMember({{ $member->id }})"
                                    >
                                        {{ __('team.edit') }}
                                    </x-ui.button>

                                    @if($member->id !== auth()->id())
                                        <!-- Actions Dropdown -->
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open" class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                <x-icon name="ellipsis-vertical" class="h-5 w-5" />
                                            </button>
                                            
                                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
                                                <div class="py-1">
                                                    @if($member->status === 'active')
                                                        <form action="{{ route('employer.team.deactivate', $member) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                <x-icon name="pause" class="h-4 w-4 mr-2 inline" />
                                                                {{ __('team.deactivate') }}
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('employer.team.activate', $member) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                                <x-icon name="play" class="h-4 w-4 mr-2 inline" />
                                                                {{ __('team.activate') }}
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <div class="border-t border-gray-100 dark:border-gray-600"></div>

                                                    <form action="{{ route('employer.team.remove', $member) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900" onclick="return confirm('{{ __('team.confirm_remove') }}')">
                                                            <x-icon name="trash" class="h-4 w-4 mr-2 inline" />
                                                            {{ __('team.remove_member') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($teamMembers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $teamMembers->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <x-icon name="users" class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                        {{ __('team.no_team_members') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('team.start_by_inviting_team_members') }}
                    </p>
                    <div class="mt-6">
                        <x-ui.button 
                            href="#" 
                            variant="primary"
                            onclick="showInviteModal()"
                        >
                            {{ __('team.invite_first_member') }}
                        </x-ui.button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Invite Member Modal -->
<div id="invite-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('team.invite_team_member') }}
                </h3>
                <button onclick="hideInviteModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-icon name="x-mark" class="h-6 w-6" />
                </button>
            </div>

            <!-- Invite Form -->
            <form action="{{ route('employer.team.invite') }}" method="POST" id="invite-form">
                @csrf
                
                <div class="space-y-4">
                    <!-- Email -->
                    <x-ui.input
                        name="email"
                        id="invite_email"
                        type="email"
                        :label="__('team.email_address')"
                        :placeholder="__('team.enter_email_address')"
                        required
                        icon="envelope"
                    />

                    <!-- Name -->
                    <x-ui.input
                        name="name"
                        id="invite_name"
                        :label="__('team.full_name')"
                        :placeholder="__('team.enter_full_name')"
                        required
                        icon="user"
                    />

                    <!-- Role -->
                    <x-ui.select
                        name="role"
                        id="invite_role"
                        :label="__('team.role')"
                        :options="[
                            'recruiter' => __('team.recruiter'),
                            'hr_manager' => __('team.hr_manager'),
                            'admin' => __('team.admin'),
                            'viewer' => __('team.viewer')
                        ]"
                        required
                    />

                    <!-- Permissions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ __('team.permissions') }}
                        </label>
                        
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <div class="flex items-center">
                                <input 
                                    id="perm_view_applications" 
                                    name="permissions[]" 
                                    type="checkbox" 
                                    value="view_applications"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="perm_view_applications" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('team.view_applications') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="perm_manage_applications" 
                                    name="permissions[]" 
                                    type="checkbox" 
                                    value="manage_applications"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="perm_manage_applications" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('team.manage_applications') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="perm_post_jobs" 
                                    name="permissions[]" 
                                    type="checkbox" 
                                    value="post_jobs"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="perm_post_jobs" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('team.post_jobs') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="perm_manage_jobs" 
                                    name="permissions[]" 
                                    type="checkbox" 
                                    value="manage_jobs"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="perm_manage_jobs" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('team.manage_jobs') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="perm_view_analytics" 
                                    name="permissions[]" 
                                    type="checkbox" 
                                    value="view_analytics"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="perm_view_analytics" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('team.view_analytics') }}
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input 
                                    id="perm_manage_company" 
                                    name="permissions[]" 
                                    type="checkbox" 
                                    value="manage_company"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                >
                                <label for="perm_manage_company" class="ml-3 block text-sm text-gray-900 dark:text-gray-300">
                                    {{ __('team.manage_company_profile') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Message -->
                    <x-ui.textarea
                        name="message"
                        id="invite_message"
                        :label="__('team.personal_message')"
                        :placeholder="__('team.invite_message_placeholder')"
                        rows="3"
                        :hint="__('team.optional_personal_message')"
                    />
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <x-ui.button 
                        type="button" 
                        variant="secondary"
                        onclick="hideInviteModal()"
                    >
                        {{ __('team.cancel') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                        id="send-invite-button"
                    >
                        {{ __('team.send_invitation') }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Member Modal (Dynamic Content) -->
<div id="edit-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <!-- Content will be loaded dynamically -->
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter changes
    const filterSelects = ['role', 'status'];
    
    filterSelects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (select) {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
    
    // Search with debounce
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
    
    // Role-based permission presets
    const roleSelect = document.getElementById('invite_role');
    if (roleSelect) {
        roleSelect.addEventListener('change', function() {
            const role = this.value;
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            
            // Clear all permissions
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Set permissions based on role
            const rolePermissions = {
                'admin': ['view_applications', 'manage_applications', 'post_jobs', 'manage_jobs', 'view_analytics', 'manage_company'],
                'hr_manager': ['view_applications', 'manage_applications', 'post_jobs', 'manage_jobs', 'view_analytics'],
                'recruiter': ['view_applications', 'manage_applications', 'post_jobs'],
                'viewer': ['view_applications']
            };
            
            if (rolePermissions[role]) {
                rolePermissions[role].forEach(permission => {
                    const checkbox = document.getElementById('perm_' + permission);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        });
    }
});

function showInviteModal() {
    document.getElementById('invite-modal').classList.remove('hidden');
}

function hideInviteModal() {
    document.getElementById('invite-modal').classList.add('hidden');
    document.getElementById('invite-form').reset();
}

function editMember(memberId) {
    // Load edit form dynamically
    fetch(`{{ route('employer.team.edit', ':id') }}`.replace(':id', memberId))
        .then(response => response.text())
        .then(html => {
            document.getElementById('edit-modal').innerHTML = html;
            document.getElementById('edit-modal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading edit form:', error);
            alert('{{ __("team.error_loading_edit_form") }}');
        });
}

function hideEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    document.getElementById('edit-modal').innerHTML = '';
}

// Form submission with loading state
document.getElementById('invite-form').addEventListener('submit', function() {
    const submitButton = document.getElementById('send-invite-button');
    const originalText = submitButton.textContent;
    
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ __('team.sending_invitation') }}...
        </div>
    `;
});
</script>
@endpush 