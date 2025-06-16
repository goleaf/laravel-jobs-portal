@extends('layouts.app')

@section('title', __('resumes.my_resumes'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                        {{ __('resumes.my_resumes') }}
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">
                        {{ __('resumes.manage_your_resumes_description') }}
                    </p>
                </div>
                
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <x-ui.button 
                        href="{{ route('candidate.resumes.builder') }}" 
                        variant="secondary"
                        icon="document-plus"
                    >
                        {{ __('resumes.create_resume') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        variant="primary"
                        icon="arrow-up-tray"
                        onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                    >
                        {{ __('resumes.upload_resume') }}
                    </x-ui.button>
                </div>
            </div>
        </div>

        <!-- Resume Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="document-text" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('resumes.total_resumes') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $resumes->count() }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="eye" class="h-6 w-6 text-green-600 dark:text-green-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('resumes.total_views') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $resumes->sum('view_count') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <x-icon name="arrow-down-tray" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    {{ __('resumes.total_downloads') }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $resumes->sum('download_count') }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumes Grid -->
        @if($resumes && $resumes->count() > 0)
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($resumes as $resume)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <!-- Resume Preview -->
                        <div class="aspect-[3/4] bg-gray-100 dark:bg-gray-700 relative group">
                            @if($resume->thumbnail)
                                <img src="{{ $resume->thumbnail }}" alt="{{ $resume->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <x-icon name="document-text" class="h-16 w-16 text-gray-400" />
                                </div>
                            @endif
                            
                            <!-- Overlay Actions -->
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center space-x-2">
                                <x-ui.button 
                                    href="{{ route('candidate.resumes.preview', $resume) }}" 
                                    variant="secondary" 
                                    size="sm"
                                    icon="eye"
                                >
                                    {{ __('resumes.preview') }}
                                </x-ui.button>
                                
                                <x-ui.button 
                                    href="{{ route('candidate.resumes.download', $resume) }}" 
                                    variant="secondary" 
                                    size="sm"
                                    icon="arrow-down-tray"
                                >
                                    {{ __('resumes.download') }}
                                </x-ui.button>
                            </div>

                            <!-- Status Badge -->
                            @if($resume->is_default)
                                <div class="absolute top-2 left-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <x-icon name="star" class="h-3 w-3 mr-1" />
                                        {{ __('resumes.default') }}
                                    </span>
                                </div>
                            @endif

                            @if($resume->is_public)
                                <div class="absolute top-2 right-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <x-icon name="globe-alt" class="h-3 w-3 mr-1" />
                                        {{ __('resumes.public') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Resume Info -->
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white truncate">
                                        {{ $resume->title }}
                                    </h3>
                                    
                                    @if($resume->description)
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                            {{ $resume->description }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Dropdown Menu -->
                                <div class="ml-2 flex-shrink-0 relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="p-2 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <x-icon name="ellipsis-vertical" class="h-5 w-5" />
                                    </button>
                                    
                                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10">
                                        <div class="py-1">
                                            <a href="{{ route('candidate.resumes.edit', $resume) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <x-icon name="pencil" class="h-4 w-4 mr-2 inline" />
                                                {{ __('resumes.edit') }}
                                            </a>
                                            
                                            <a href="{{ route('candidate.resumes.duplicate', $resume) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <x-icon name="document-duplicate" class="h-4 w-4 mr-2 inline" />
                                                {{ __('resumes.duplicate') }}
                                            </a>
                                            
                                            @if(!$resume->is_default)
                                                <form action="{{ route('candidate.resumes.set-default', $resume) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                        <x-icon name="star" class="h-4 w-4 mr-2 inline" />
                                                        {{ __('resumes.set_as_default') }}
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <form action="{{ route('candidate.resumes.toggle-visibility', $resume) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    <x-icon :name="$resume->is_public ? 'eye-slash' : 'eye'" class="h-4 w-4 mr-2 inline" />
                                                    {{ $resume->is_public ? __('resumes.make_private') : __('resumes.make_public') }}
                                                </button>
                                            </form>
                                            
                                            <div class="border-t border-gray-100 dark:border-gray-600"></div>
                                            
                                            <form action="{{ route('candidate.resumes.destroy', $resume) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('resumes.confirm_delete') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                                                    <x-icon name="trash" class="h-4 w-4 mr-2 inline" />
                                                    {{ __('resumes.delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resume Stats -->
                            <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center">
                                        <x-icon name="eye" class="h-4 w-4 mr-1" />
                                        {{ $resume->view_count }} {{ __('resumes.views') }}
                                    </span>
                                    
                                    <span class="flex items-center">
                                        <x-icon name="arrow-down-tray" class="h-4 w-4 mr-1" />
                                        {{ $resume->download_count }} {{ __('resumes.downloads') }}
                                    </span>
                                </div>
                                
                                <span>{{ $resume->updated_at->diffForHumans() }}</span>
                            </div>

                            <!-- File Info -->
                            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                <span class="uppercase">{{ $resume->file_type ?? 'PDF' }}</span>
                                <span>{{ $resume->file_size ? number_format($resume->file_size / 1024, 1) . ' KB' : '' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <x-icon name="document-text" class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                    {{ __('resumes.no_resumes') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('resumes.get_started_by_creating_resume') }}
                </p>
                <div class="mt-6 flex justify-center space-x-3">
                    <x-ui.button 
                        href="{{ route('candidate.resumes.builder') }}" 
                        variant="primary"
                    >
                        {{ __('resumes.create_resume') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        variant="secondary"
                        onclick="document.getElementById('upload-modal').classList.remove('hidden')"
                    >
                        {{ __('resumes.upload_resume') }}
                    </x-ui.button>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('resumes.upload_new_resume') }}
                </h3>
                <button onclick="document.getElementById('upload-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-icon name="x-mark" class="h-6 w-6" />
                </button>
            </div>

            <!-- Upload Form -->
            <form action="{{ route('candidate.resumes.upload') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                
                <div class="space-y-4">
                    <!-- Resume Title -->
                    <x-ui.input
                        name="title"
                        id="title"
                        :label="__('resumes.resume_title')"
                        :placeholder="__('resumes.resume_title_placeholder')"
                        required
                        :error="$errors->first('title')"
                    />

                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('resumes.resume_file') }}
                        </label>
                        <x-ui.file-upload
                            name="resume_file"
                            id="resume_file"
                            accept=".pdf,.doc,.docx"
                            :allowedTypes="['resume']"
                            maxSize="10"
                            :hint="__('resumes.file_requirements')"
                            :error="$errors->first('resume_file')"
                            required
                        />
                    </div>

                    <!-- Description -->
                    <x-ui.textarea
                        name="description"
                        id="description"
                        :label="__('resumes.description')"
                        :placeholder="__('resumes.description_placeholder')"
                        rows="3"
                        :error="$errors->first('description')"
                    />

                    <!-- Options -->
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <input 
                                id="is_default" 
                                name="is_default" 
                                type="checkbox" 
                                value="1"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="is_default" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                {{ __('resumes.set_as_default_resume') }}
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input 
                                id="is_public" 
                                name="is_public" 
                                type="checkbox" 
                                value="1"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="is_public" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                                {{ __('resumes.make_publicly_visible') }}
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-end space-x-3 mt-6">
                    <x-ui.button 
                        type="button" 
                        variant="secondary"
                        onclick="document.getElementById('upload-modal').classList.add('hidden')"
                    >
                        {{ __('resumes.cancel') }}
                    </x-ui.button>
                    
                    <x-ui.button 
                        type="submit" 
                        variant="primary"
                        id="upload-button"
                    >
                        {{ __('resumes.upload_resume') }}
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('upload-form');
    const uploadButton = document.getElementById('upload-button');
    
    if (uploadForm) {
        uploadForm.addEventListener('submit', function() {
            uploadButton.disabled = true;
            uploadButton.innerHTML = `
                <div class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('resumes.uploading') }}...
                </div>
            `;
        });
    }
    
    // Auto-generate title from filename
    const fileInput = document.getElementById('resume_file');
    const titleInput = document.getElementById('title');
    
    if (fileInput && titleInput) {
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0 && !titleInput.value) {
                const filename = this.files[0].name;
                const title = filename.replace(/\.[^/.]+$/, "").replace(/[-_]/g, ' ');
                titleInput.value = title.charAt(0).toUpperCase() + title.slice(1);
            }
        });
    }
});
</script>
@endpush 