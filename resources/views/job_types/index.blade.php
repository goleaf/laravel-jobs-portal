@extends('layouts.app')

@section('title')
    {{ __('messages.job_type.job_types') }}
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <livewire:job-type-table />
                <livewire:job-type-form />
                <x-notification />
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh', () => {
                window.dispatchEvent(new CustomEvent('refresh-table'));
            });
            
            Livewire.on('success', (message) => {
                window.dispatchEvent(new CustomEvent('success', {
                    detail: {message: message}
                }));
            });
            
            Livewire.on('error', (message) => {
                window.dispatchEvent(new CustomEvent('error', {
                    detail: {message: message}
                }));
            });
        });
    </script>
@endsection
