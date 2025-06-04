@extends('layouts.app')

@section('title', __('Admin Users'))

@section('content')
<div class="container mx-auto">
    <div class="flex flex-wrap justify-center">
        <div class="flex-1 -md-12">
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="bg-white shadow rounded-lg overflow-hidden -header flex justify-between items-center">
                    <h3 class="mb-0">{{ __('Admin Users') }}</h3>
                    <a href="{{ route('admin.admin.create') }}" class="btn px-4 py-2 rounded font-medium transition-colors -primary">
                        <i class="fas fa-plus"></i> {{ __('Add Admin') }}
                    </a>
                </div>

                <div class="bg-white shadow rounded-lg overflow-hidden -body">
                    @if(session('success'))
                        <div class="alert bg-green-50 border border-green-200 text-green-800 p-4 rounded-md mb-4 -dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert bg-red-50 border border-red-200 text-red-800 p-4 rounded-md mb-4 -dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="w-full divide-y divide-gray-200 -responsive">
                        <table class="table w-full divide-y divide-gray-200 -striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($admins as $admin)
                                    <tr>
                                        <td>{{ $admin->first_name }} {{ $admin->last_name }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $admin->is_active ? 'success' : 'danger' }}">
                                                {{ $admin->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $admin->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group px-4 py-2 rounded font-medium transition-colors -group-sm" role="group">
                                                <a href="{{ route('admin.admin.show', $admin) }}" class="btn px-4 py-2 rounded font-medium transition-colors -outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.admin.edit', $admin) }}" class="btn px-4 py-2 rounded font-medium transition-colors -outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($admin->id !== auth()->id())
                                                    <form action="{{ route('admin.admin.destroy', $admin) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn px-4 py-2 rounded font-medium transition-colors -outline-danger" 
                                                                onclick="return confirm('Are you sure you want to delete this admin?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500">{{ __('No admin users found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-center">
                        {{ $admins->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

