{{-- Context7 Action Button Component for Company Sizes Table --}}
@props(['item', 'route_prefix' => 'admin.company-sizes'])

<div class="flex items-center gap-2" x-data="{ deleting: false }">
    {{-- Edit Button --}}
    <a href="{{ route($route_prefix . '.edit', $item->id) }}" 
       class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
       title="{{ __('common.edit') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
    </a>
    
    {{-- Delete Button --}}
    <button type="button" 
            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50"
            x-on:click="confirmDelete"
            :disabled="deleting"
            title="{{ __('common.delete') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
    </button>
</div>

{{-- Context7 Alpine.js Delete Confirmation --}}
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('actionButton', () => ({
        deleting: false,
        
        async confirmDelete() {
            if (this.deleting) return;
            
            const result = await Swal.fire({
                title: '{{ __("common.are_you_sure") }}',
                text: '{{ __("common.delete_warning") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __("common.yes_delete") }}',
                cancelButtonText: '{{ __("common.cancel") }}',
                reverseButtons: true
            });
            
            if (result.isConfirmed) {
                await this.performDelete();
            }
        },
        
        async performDelete() {
            this.deleting = true;
            
            try {
                const response = await fetch('{{ route($route_prefix . '.destroy', $item->id) }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    await Swal.fire({
                        title: '{{ __("common.deleted") }}',
                        text: data.message || '{{ __("common.delete_success") }}',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Reload page or trigger custom event
                    window.location.reload();
                } else {
                    throw new Error(data.message || '{{ __("common.delete_error") }}');
                }
            } catch (error) {
                await Swal.fire({
                    title: '{{ __("common.error") }}',
                    text: error.message || '{{ __("common.something_wrong") }}',
                    icon: 'error'
                });
            } finally {
                this.deleting = false;
            }
        }
    }));
});
</script>
@endpush