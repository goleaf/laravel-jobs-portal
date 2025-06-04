@extends('layouts.app')
@section('title')
    {{ __('Transactions Management') }}
@endsection

@section('content')
    <div class="container mx-auto px-4 mx-auto -fluid">
        <div class="flex justify-between items-center mb-4">
            <h1 class="h3 mb-0">{{ __('Transactions Management') }}</h1>
            <a href="{{ route('admin.transactions.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary">
                <i class="fas fa-plus"></i> {{ __('Add Transaction') }}
            </a>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-white shadow rounded-lg overflow-hidden -header">
                <h3 class="bg-white shadow rounded-lg overflow-hidden -title">{{ __('All Transactions') }}</h3>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden -body">
                <div class="w-full divide-y divide-gray-200 -responsive">
                    <table class="min-w-full divide-y divide-gray-200 odd:bg-gray-50 w-full divide-y divide-gray-200 -hover">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Invoice ID') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Payment Type') }}</th>
                                <th>{{ __('{{ __('admin.status') }}') }}</th>
                                <th>{{ __('Meta') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('{{ __('admin.actions') }}') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if($transaction->invoice_id)
                                            <span class="badge bg-blue-500">{{ $transaction->invoice_id }}</span>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center">
                                            @if($transaction->user->avatar_url)
                                                <img src="{{ $transaction->user->avatar_url }}" alt="Avatar" class="rounded-circle me-2" width="30" height="30">
                                            @else
                                                <div class="bg-gray-600 rounded-circle me-2 flex items-center justify-center" style="width: 30px; height: 30px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $transaction->user->full_name }}</div>
                                                <small class="text-gray-500">{{ $transaction->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-green-600">
                                            ${{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->payment_type)
                                            <span class="badge bg-gray-600">{{ ucfirst($transaction->payment_type) }}</span>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($transaction->status ?? 'pending')
                                            @case('approved')
                                            @case('paid')
                                            @case('completed')
                                                <span class="badge bg-green-600">{{ __('Approved') }}</span>
                                                @break
                                            @case('rejected')
                                            @case('failed')
                                                <span class="badge bg-red-600">{{ __('Rejected') }}</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-yellow-500">{{ __('Pending') }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-gray-100 text-gray-900">{{ ucfirst($transaction->status ?? 'Unknown') }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($transaction->meta)
                                            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -outline-info" data-bs-toggle="modal" data-bs-target="#metaModal{{ $transaction->id }}">
                                                <i class="fas fa-info-circle"></i> {{ __('{{ __('admin.view') }}') }}
                                            </button>
                                            
                                            <!-- Meta Modal -->
                                            <div class="fixed inset-0 z-50 overflow-y-auto fade" id="metaModal{{ $transaction->id }}" tabindex="-1">
                                                <div class="flex items-center justify-center min-h-screen px-4">
                                                    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                                                        <div class="px-6 py-4 border-b border-gray-200">
                                                            <h5 class="modal-title">{{ __('Transaction Meta') }}</h5>
                                                            <button type="button" class="px-4 py-2 rounded font-medium transition-colors -close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="px-6 py-4">
                                                            <pre>{{ json_encode(json_decode($transaction->meta), JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-500">No meta</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $transaction->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="px-4 py-2 rounded font-medium transition-colors -group" role="group">
                                            <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -outline-info" title="{{ __('{{ __('admin.view') }}') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.transactions.edit', $transaction->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -outline-warning" title="{{ __('{{ __('admin.edit') }}') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.transactions.destroy', $transaction->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-3 py-1.5 text-sm px-4 py-2 rounded font-medium transition-colors -outline-danger" title="{{ __('{{ __('admin.delete') }}') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-gray-500">
                                            <i class="fas fa-credit- bg-white shadow rounded-lg overflow-hidden fa-2x mb-2"></i>
                                            <p>{{ __('No transactions found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="flex justify-center mt-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }
    pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        max-height: 300px;
        overflow-y: auto;
    }
</style>
@endpush 