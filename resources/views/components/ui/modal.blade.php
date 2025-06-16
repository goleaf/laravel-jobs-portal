@props([
    'id' => 'modal',
    'title' => '',
    'size' => 'md',
    'closable' => true,
    'backdrop' => 'static',
    'centered' => true,
    'scrollable' => false,
    'fullscreen' => false,
    'show' => false
])

@php
$sizes = [
    'xs' => 'max-w-xs',
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
    '5xl' => 'max-w-5xl',
    '6xl' => 'max-w-6xl',
    '7xl' => 'max-w-7xl',
];

$modalClasses = 'fixed inset-0 z-50 overflow-y-auto';
$backdropClasses = 'fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity duration-300';
$containerClasses = 'flex min-h-full items-center justify-center p-4 text-center sm:p-0';
$contentClasses = 'relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all duration-300 sm:my-8 sm:w-full';

if ($fullscreen) {
    $contentClasses .= ' sm:max-w-none sm:h-full sm:m-0 sm:rounded-none';
} else {
    $contentClasses .= ' ' . $sizes[$size];
}

if ($centered) {
    $containerClasses .= ' sm:items-center';
} else {
    $containerClasses .= ' sm:items-start sm:pt-16';
}

if ($scrollable) {
    $contentClasses .= ' max-h-[90vh] overflow-y-auto';
}
@endphp

<!-- Modal -->
<div 
    id="{{ $id }}" 
    class="{{ $modalClasses }} {{ $show ? 'block' : 'hidden' }}" 
    aria-labelledby="{{ $id }}-title" 
    role="dialog" 
    aria-modal="true"
    data-modal="{{ $id }}"
    data-backdrop="{{ $backdrop }}"
>
    <!-- Backdrop -->
    <div class="{{ $backdropClasses }}" data-modal-backdrop="{{ $id }}"></div>

    <!-- Modal Container -->
    <div class="{{ $containerClasses }}">
        <!-- Modal Content -->
        <div class="{{ $contentClasses }}">
            @if($title || $closable)
                <!-- Modal Header -->
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="flex items-center justify-between">
                        @if($title)
                            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white" id="{{ $id }}-title">
                                {{ $title }}
                            </h3>
                        @endif
                        
                        @if($closable)
                            <button 
                                type="button" 
                                class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                data-modal-close="{{ $id }}"
                                aria-label="{{ __('ui.close') }}"
                            >
                                <span class="sr-only">{{ __('ui.close') }}</span>
                                <x-icon name="x-mark" class="h-6 w-6" />
                            </button>
                        @endif
                    </div>
                    
                    @if($title)
                        <hr class="mt-4 border-gray-200 dark:border-gray-700">
                    @endif
                </div>
            @endif

            <!-- Modal Body -->
            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:p-6">
                {{ $slot }}
            </div>

            <!-- Modal Footer (if provided) -->
            @isset($footer)
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal functionality
    const modal = document.getElementById('{{ $id }}');
    const backdrop = document.querySelector('[data-modal-backdrop="{{ $id }}"]');
    const closeBtns = document.querySelectorAll('[data-modal-close="{{ $id }}"]');
    const openBtns = document.querySelectorAll('[data-modal-open="{{ $id }}"]');
    
    // Open modal function
    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('block');
        document.body.classList.add('overflow-hidden');
        
        // Focus management
        const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            firstFocusable.focus();
        }
        
        // Dispatch custom event
        modal.dispatchEvent(new CustomEvent('modal:opened', { 
            detail: { modalId: '{{ $id }}' } 
        }));
    }
    
    // Close modal function
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('block');
        document.body.classList.remove('overflow-hidden');
        
        // Dispatch custom event
        modal.dispatchEvent(new CustomEvent('modal:closed', { 
            detail: { modalId: '{{ $id }}' } 
        }));
    }
    
    // Open button handlers
    openBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal();
        });
    });
    
    // Close button handlers
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            closeModal();
        });
    });
    
    // Backdrop click handler
    @if($backdrop !== 'static')
    backdrop.addEventListener('click', function(e) {
        if (e.target === backdrop) {
            closeModal();
        }
    });
    @endif
    
    // Escape key handler
    @if($closable)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
    @endif
    
    // Focus trap
    modal.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    lastElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastElement) {
                    firstElement.focus();
                    e.preventDefault();
                }
            }
        }
    });
    
    // Global functions for programmatic control
    window['open{{ ucfirst($id) }}Modal'] = openModal;
    window['close{{ ucfirst($id) }}Modal'] = closeModal;
});
</script>
@endpush 