{{-- Action Button Component for Company Sizes Table --}}
@props(['item', 'route_prefix' => 'admin.company-sizes'])

<div class="action-buttons d-flex gap-2">
    {{-- Edit Button --}}
    <a href="{{ route($route_prefix . '.edit', $item->id) }}" 
       class="btn btn-sm btn-outline-primary" 
       title="{{ __('Edit') }}">
        <i class="fas fa-edit"></i>
    </a>
    
    {{-- Delete Button --}}
    <button type="button" 
            class="btn btn-sm btn-outline-danger delete-btn" 
            data-id="{{ $item->id }}"
            data-route="{{ route($route_prefix . '.destroy', $item->id) }}"
            title="{{ __('Delete') }}">
        <i class="fas fa-trash"></i>
    </button>
</div>

{{-- Delete Confirmation Modal Script --}}
@push('scripts')
<script>
$(document).on('click', '.delete-btn', function() {
    const itemId = $(this).data('id');
    const deleteRoute = $(this).data('route');
    
    Swal.fire({
        title: '{{ __("Are you sure?") }}',
        text: '{{ __("You won\'t be able to revert this!") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: '{{ __("Yes, delete it!") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: deleteRoute,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire(
                        '{{ __("Deleted!") }}',
                        response.message || '{{ __("Item has been deleted.") }}',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire(
                        '{{ __("Error!") }}',
                        xhr.responseJSON?.message || '{{ __("Something went wrong!") }}',
                        'error'
                    );
                }
            });
        }
    });
});
</script>
@endpush