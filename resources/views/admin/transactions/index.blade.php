@extends('layouts.app')
@section('title')
    {{ __('Transactions Management') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">{{ __('Transactions Management') }}</h1>
            <a href="{{ route('admin.transactions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Add Transaction') }}
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('All Transactions') }}</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Invoice ID') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Payment Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Meta') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>
                                        @if($transaction->invoice_id)
                                            <span class="badge bg-info">{{ $transaction->invoice_id }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($transaction->user->avatar_url)
                                                <img src="{{ $transaction->user->avatar_url }}" alt="Avatar" class="rounded-circle me-2" width="30" height="30">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $transaction->user->full_name }}</div>
                                                <small class="text-muted">{{ $transaction->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">
                                            ${{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($transaction->payment_type)
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->payment_type) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($transaction->status ?? 'pending')
                                            @case('approved')
                                            @case('paid')
                                            @case('completed')
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                                @break
                                            @case('rejected')
                                            @case('failed')
                                                <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ ucfirst($transaction->status ?? 'Unknown') }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($transaction->meta)
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#metaModal{{ $transaction->id }}">
                                                <i class="fas fa-info-circle"></i> {{ __('View') }}
                                            </button>
                                            
                                            <!-- Meta Modal -->
                                            <div class="modal fade" id="metaModal{{ $transaction->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('Transaction Meta') }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <pre>{{ json_encode(json_decode($transaction->meta), JSON_PRETTY_PRINT) }}</pre>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No meta</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $transaction->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.transactions.edit', $transaction->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.transactions.destroy', $transaction->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-credit-card fa-2x mb-2"></i>
                                            <p>{{ __('No transactions found') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transactions->hasPages())
                    <div class="d-flex justify-content-center mt-4">
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