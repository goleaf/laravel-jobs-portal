@extends('layouts.app')
@section('title')
    {{ __('messages.company.reported_employers') }}
@endsection

@section('content')
    <div class="container mx-auto px-4 mx-auto fluid">
        <div class="flex justify-between items-center mb-4">
            <h1 class="h3 mb-0">{{ __('messages.company.reported_employers') }}</h1>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-white shadow rounded-lg overflow-hidden header">
                <h3 class="bg-white shadow rounded-lg overflow-hidden title">{{ __('messages.company.reported_employers') }}</h3>
            </div>
            <div class="bg-white shadow rounded-lg overflow-hidden body">
                <div class="w-full divide-y divide-gray-200 responsive">
                    <table class="min-w-full divide-y divide-gray-200 odd:bg-gray-50 w-full divide-y divide-gray-200 hover">
                        <thead>
                            <tr>
                                <th>{{ __('messages.common.id') }}</th>
                                <th>{{ __('messages.company.company_name') }}</th>
                                <th>{{ __('messages.common.email') }}</th>
                                <th>{{ __('messages.company.ceo') }}</th>
                                <th>{{ __('messages.common.location') }}</th>
                                <th>{{ __('messages.common.status') }}</th>
                                <th>{{ __('messages.common.created_at') }}</th>
                                <th>{{ __('messages.common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-gray-500">
                                        <i class="fas fa-building fa-2x mb-2"></i>
                                        <p>{{ __('messages.common.no_data_available') }}</p>
                                        <small>{{ __('This feature is under development') }}</small>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
</style>
@endpush 