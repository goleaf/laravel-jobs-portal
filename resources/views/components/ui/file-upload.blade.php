@props([
    'name' => 'file',
    'id' => null,
    'accept' => null,
    'multiple' => false,
    'maxSize' => '10', // MB
    'maxFiles' => 1,
    'required' => false,
    'disabled' => false,
    'label' => null,
    'hint' => null,
    'error' => null,
    'preview' => true,
    'allowedTypes' => [],
    'dropzone' => true,
    'existingFiles' => []
])

@php
$uploadId = $id ?? $name;
$maxSizeBytes = (int)$maxSize * 1024 * 1024; // Convert MB to bytes

// Common file type patterns
$acceptPatterns = [
    'image' => 'image/*',
    'pdf' => '.pdf,application/pdf',
    'document' => '.pdf,.doc,.docx,.txt',
    'resume' => '.pdf,.doc,.docx',
    'logo' => 'image/png,image/jpeg,image/jpg,image/gif,image/svg+xml',
    'all' => '*'
];

if (!$accept && !empty($allowedTypes)) {
    $accept = collect($allowedTypes)->map(fn($type) => $acceptPatterns[$type] ?? $type)->join(',');
}
@endphp

<div class="space-y-2" data-file-upload="{{ $uploadId }}">
    @if($label)
        <label for="{{ $uploadId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- File Input (Hidden) -->
    <input
        type="file"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        id="{{ $uploadId }}"
        @if($accept) accept="{{ $accept }}" @endif
        @if($multiple) multiple @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
        class="sr-only"
        data-max-size="{{ $maxSizeBytes }}"
        data-max-files="{{ $maxFiles }}"
    >

    <!-- Dropzone Area -->
    @if($dropzone)
        <div 
            class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-gray-400 dark:hover:border-gray-500 focus-within:border-blue-500 dark:focus-within:border-blue-400 transition-colors duration-200 {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}"
            data-dropzone="{{ $uploadId }}"
        >
            <!-- Upload Icon & Text -->
            <div class="space-y-2" data-upload-prompt>
                <x-icon name="cloud-arrow-up" class="mx-auto h-12 w-12 text-gray-400" />
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <label for="{{ $uploadId }}" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        <span>{{ __('ui.upload_file') }}</span>
                    </label>
                    <span class="pl-1">{{ __('ui.or_drag_and_drop') }}</span>
                </div>
                
                @if($hint)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        @if($accept)
                            {{ __('ui.allowed_types') }}: {{ str_replace(',', ', ', $accept) }}
                        @endif
                        @if($maxSize)
                            {{ $accept ? ' • ' : '' }}{{ __('ui.max_size') }}: {{ $maxSize }}MB
                        @endif
                        @if($multiple && $maxFiles > 1)
                            • {{ __('ui.max_files') }}: {{ $maxFiles }}
                        @endif
                    </p>
                @endif
            </div>

            <!-- Loading State -->
            <div class="hidden space-y-2" data-upload-loading>
                <div class="animate-spin mx-auto h-8 w-8 text-blue-600">
                    <x-icon name="arrow-path" class="h-8 w-8" />
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.uploading') }}...</p>
            </div>

            <!-- Drag Overlay -->
            <div class="absolute inset-0 bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-400 dark:border-blue-500 rounded-lg hidden items-center justify-center" data-drag-overlay>
                <div class="text-center">
                    <x-icon name="cloud-arrow-up" class="mx-auto h-12 w-12 text-blue-500" />
                    <p class="mt-2 text-sm font-medium text-blue-600 dark:text-blue-400">
                        {{ __('ui.drop_files_here') }}
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Traditional File Input Button -->
        <div class="flex items-center space-x-4">
            <x-ui.button 
                type="button" 
                variant="secondary" 
                onclick="document.getElementById('{{ $uploadId }}').click()"
                :disabled="$disabled"
            >
                <x-icon name="paper-clip" class="h-4 w-4 mr-2" />
                {{ __('ui.choose_file') }}
            </x-ui.button>
            
            <span class="text-sm text-gray-500 dark:text-gray-400" data-file-name>
                {{ __('ui.no_file_chosen') }}
            </span>
        </div>
    @endif

    <!-- Existing Files -->
    @if(!empty($existingFiles))
        <div class="space-y-2">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.existing_files') }}</h4>
            <div class="grid grid-cols-1 gap-2" data-existing-files>
                @foreach($existingFiles as $file)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <x-icon name="document" class="h-5 w-5 text-gray-400" />
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $file['name'] ?? 'Unknown' }}
                                </p>
                                @if(isset($file['size']))
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ number_format($file['size'] / 1024, 1) }} KB
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if(isset($file['url']))
                                <a href="{{ $file['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                            @endif
                            
                            <button type="button" class="text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300" data-remove-existing="{{ $file['id'] ?? $loop->index }}">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- File Preview Area -->
    @if($preview)
        <div class="hidden space-y-2" data-file-preview>
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.selected_files') }}</h4>
            <div class="grid grid-cols-1 gap-2" data-preview-container></div>
        </div>
    @endif

    <!-- Error Messages -->
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
    
    <div class="hidden text-sm text-red-600 dark:text-red-400" data-upload-error></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('[data-file-upload="{{ $uploadId }}"]');
    const fileInput = document.getElementById('{{ $uploadId }}');
    const dropzone = container.querySelector('[data-dropzone="{{ $uploadId }}"]');
    const dragOverlay = container.querySelector('[data-drag-overlay]');
    const uploadPrompt = container.querySelector('[data-upload-prompt]');
    const uploadLoading = container.querySelector('[data-upload-loading]');
    const previewArea = container.querySelector('[data-file-preview]');
    const previewContainer = container.querySelector('[data-preview-container]');
    const errorContainer = container.querySelector('[data-upload-error]');
    const fileName = container.querySelector('[data-file-name]');
    
    let selectedFiles = [];
    const maxSize = {{ $maxSizeBytes }};
    const maxFiles = {{ $maxFiles }};
    const multiple = {{ $multiple ? 'true' : 'false' }};
    
    // File validation
    function validateFile(file) {
        const errors = [];
        
        // Size validation
        if (file.size > maxSize) {
            errors.push(`{{ __('ui.file_too_large') }}: ${file.name} (${(file.size / 1024 / 1024).toFixed(1)}MB > {{ $maxSize }}MB)`);
        }
        
        // Type validation (if accept attribute is set)
        @if($accept)
        const acceptedTypes = '{{ $accept }}'.split(',').map(type => type.trim());
        const fileType = file.type;
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        const isValidType = acceptedTypes.some(type => {
            if (type.includes('*')) {
                return fileType.startsWith(type.replace('*', ''));
            }
            return type === fileType || type === fileExtension;
        });
        
        if (!isValidType) {
            errors.push(`{{ __('ui.invalid_file_type') }}: ${file.name}`);
        }
        @endif
        
        return errors;
    }
    
    // Create file preview
    function createFilePreview(file, index) {
        const isImage = file.type.startsWith('image/');
        const div = document.createElement('div');
        div.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg';
        div.dataset.fileIndex = index;
        
        div.innerHTML = `
            <div class="flex items-center space-x-3">
                ${isImage ? 
                    `<img src="${URL.createObjectURL(file)}" alt="${file.name}" class="h-10 w-10 object-cover rounded">` :
                    `<svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>`
                }
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">${file.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${(file.size / 1024).toFixed(1)} KB</p>
                </div>
            </div>
            <button type="button" class="text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300" data-remove-file="${index}">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        
        // Add remove functionality
        div.querySelector('[data-remove-file]').addEventListener('click', function() {
            removeFile(index);
        });
        
        return div;
    }
    
    // Handle file selection
    function handleFiles(files) {
        const fileArray = Array.from(files);
        let allErrors = [];
        
        // Validate each file
        fileArray.forEach(file => {
            const errors = validateFile(file);
            allErrors = allErrors.concat(errors);
        });
        
        // Check total file count
        if (!multiple && fileArray.length > 1) {
            allErrors.push('{{ __("ui.multiple_files_not_allowed") }}');
        } else if (selectedFiles.length + fileArray.length > maxFiles) {
            allErrors.push(`{{ __('ui.too_many_files') }}: ${selectedFiles.length + fileArray.length} > ${maxFiles}`);
        }
        
        // Show errors or add files
        if (allErrors.length > 0) {
            showErrors(allErrors);
            return;
        }
        
        // Clear errors
        hideErrors();
        
        // Add files
        if (!multiple) {
            selectedFiles = fileArray;
        } else {
            selectedFiles = selectedFiles.concat(fileArray);
        }
        
        updateUI();
        updateFileInput();
    }
    
    // Update file input with selected files
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        
        // Trigger change event
        fileInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
    
    // Remove file
    function removeFile(index) {
        selectedFiles.splice(index, 1);
        updateUI();
        updateFileInput();
    }
    
    // Update UI
    function updateUI() {
        if (!previewContainer) return;
        
        // Update preview
        previewContainer.innerHTML = '';
        
        if (selectedFiles.length > 0) {
            selectedFiles.forEach((file, index) => {
                previewContainer.appendChild(createFilePreview(file, index));
            });
            
            if (previewArea) {
                previewArea.classList.remove('hidden');
            }
            
            if (fileName) {
                fileName.textContent = selectedFiles.length === 1 ? 
                    selectedFiles[0].name : 
                    `${selectedFiles.length} {{ __('ui.files_selected') }}`;
            }
        } else {
            if (previewArea) {
                previewArea.classList.add('hidden');
            }
            
            if (fileName) {
                fileName.textContent = '{{ __("ui.no_file_chosen") }}';
            }
        }
    }
    
    // Show errors
    function showErrors(errors) {
        if (errorContainer) {
            errorContainer.innerHTML = errors.join('<br>');
            errorContainer.classList.remove('hidden');
        }
    }
    
    // Hide errors
    function hideErrors() {
        if (errorContainer) {
            errorContainer.classList.add('hidden');
        }
    }
    
    // Event listeners
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFiles(e.target.files);
            }
        });
    }
    
    if (dropzone && !{{ $disabled ? 'true' : 'false' }}) {
        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function() {
                dropzone.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
                if (dragOverlay) {
                    dragOverlay.classList.remove('hidden');
                    dragOverlay.classList.add('flex');
                }
            });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function() {
                dropzone.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
                if (dragOverlay) {
                    dragOverlay.classList.add('hidden');
                    dragOverlay.classList.remove('flex');
                }
            });
        });
        
        dropzone.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFiles(files);
            }
        });
        
        // Click to select files
        dropzone.addEventListener('click', function(e) {
            if (e.target === dropzone || e.target.closest('[data-upload-prompt]')) {
                fileInput.click();
            }
        });
    }
    
    // Handle existing file removal
    container.querySelectorAll('[data-remove-existing]').forEach(button => {
        button.addEventListener('click', function() {
            const fileId = this.dataset.removeExisting;
            // Add hidden input to mark for deletion
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_files[]';
            input.value = fileId;
            container.appendChild(input);
            
            // Remove from UI
            this.closest('.flex').remove();
        });
    });
});
</script>
@endpush 