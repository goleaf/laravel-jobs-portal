<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.job_category.job_categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @livewire('job-category-table')
        </div>
    </div>
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refresh', () => {
                Livewire.dispatch('refresh');
            });
            
            Livewire.on('success', (event) => {
                window.dispatchEvent(new CustomEvent('success', { detail: event }));
            });
            
            Livewire.on('error', (event) => {
                window.dispatchEvent(new CustomEvent('error', { detail: event }));
            });
        });
    </script>
@endpush
