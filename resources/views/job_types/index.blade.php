@extends('layouts.app')

@section('title')
    {{ __('messages.job_types') }}
@endsection

@section('content')
    <div class="container mx-auto px-4 py-6">
        <livewire:job-type-table />
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
