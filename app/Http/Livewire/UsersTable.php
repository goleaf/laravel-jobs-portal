<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UsersTable extends BaseTable
{
    public $showButtonOnHeader = true;
    public $showFilterOnHeader = true;
    public $buttonComponent = 'buttons.add-user-button';
    
    public function getQuery(): Builder
    {
        return User::query();
    }
    
    public function getColumns(): array
    {
        return [
            [
                'label' => 'ID',
                'field' => 'id',
                'sortable' => true,
            ],
            [
                'label' => 'Name',
                'field' => 'first_name',
                'searchable' => true,
                'sortable' => true,
            ],
            [
                'label' => 'Email',
                'field' => 'email',
                'searchable' => true,
                'sortable' => true,
            ],
            [
                'label' => 'Role',
                'field' => 'role',
                'view' => 'livewire.users.role-column',
            ],
            [
                'label' => 'Status',
                'field' => 'is_active',
                'view' => 'livewire.users.status-column',
            ],
            [
                'label' => 'Actions',
                'view' => 'livewire.users.action-buttons',
            ],
        ];
    }
    
    // Override default styling if needed
    public function getTableClass(): string
    {
        return 'w-full table-fixed border-collapse';
    }
} 