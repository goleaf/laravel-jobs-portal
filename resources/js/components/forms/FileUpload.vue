<template>
  <div class="file-upload-component">
    <!-- Main Upload Area -->
    <div
      @drop.prevent="handleDrop"
      @dragover.prevent="isDragOver = true"
      @dragleave.prevent="isDragOver = false"
      :class="[
        'border-2 border-dashed rounded-lg p-6 text-center transition-colors',
        isDragOver
          ? 'border-blue-400 bg-blue-50'
          : error
          ? 'border-red-300 bg-red-50'
          : 'border-gray-300 bg-gray-50 hover:bg-gray-100'
      ]"
    >
      <!-- Upload Icon -->
      <div class="mb-4">
        <svg
          class="mx-auto h-12 w-12 text-gray-400"
          stroke="currentColor"
          fill="none"
          viewBox="0 0 48 48"
        >
          <path
            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          />
        </svg>
      </div>

      <!-- Upload Text -->
      <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900">
          {{ isDragOver ? 'Drop files here' : 'Upload files' }}
        </h3>
        <p class="text-sm text-gray-500 mt-1">
          Drag and drop your files here, or
          <button
            type="button"
            @click="openFileDialog"
            class="text-blue-600 hover:text-blue-800 font-medium"
          >
            browse
          </button>
        </p>
      </div>

      <!-- File Restrictions -->
      <div class="text-xs text-gray-400">
        <p v-if="allowedTypes.length">
          Allowed: {{ allowedTypes.join(', ') }}
        </p>
        <p v-if="maxSize">
          Max size: {{ formatFileSize(maxSize) }}
        </p>
        <p v-if="maxFiles > 1">
          Max files: {{ maxFiles }}
        </p>
      </div>

      <!-- Hidden File Input -->
      <input
        ref="fileInput"
        type="file"
        :multiple="maxFiles > 1"
        :accept="allowedTypes.join(',')"
        @change="handleFileSelect"
        class="hidden"
      />
    </div>

    <!-- Error Message -->
    <div v-if="error" class="mt-2 text-sm text-red-600">
      {{ error }}
    </div>

    <!-- Uploaded Files List -->
    <div v-if="files.length > 0" class="mt-4 space-y-2">
      <h4 class="text-sm font-medium text-gray-900">
        {{ files.length === 1 ? 'Uploaded File' : 'Uploaded Files' }}
      </h4>
      
      <div
        v-for="(file, index) in files"
        :key="index"
        class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-md"
      >
        <div class="flex items-center flex-1 min-w-0">
          <!-- File Icon -->
          <div class="flex-shrink-0 mr-3">
            <div
              :class="[
                'w-8 h-8 rounded flex items-center justify-center text-xs font-bold text-white',
                getFileTypeColor(file.type)
              ]"
            >
              {{ getFileTypeIcon(file.type) }}
            </div>
          </div>

          <!-- File Info -->
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">
              {{ file.name }}
            </p>
            <p class="text-xs text-gray-500">
              {{ formatFileSize(file.size) }}
              <span v-if="file.uploadedAt">
                • Uploaded {{ formatDate(file.uploadedAt) }}
              </span>
            </p>
          </div>

          <!-- Upload Progress -->
          <div v-if="file.progress !== undefined && file.progress < 100" class="flex-shrink-0 ml-4">
            <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
              <div
                class="h-full bg-blue-600 transition-all duration-300"
                :style="{ width: file.progress + '%' }"
              ></div>
            </div>
            <p class="text-xs text-gray-500 text-center mt-1">{{ file.progress }}%</p>
          </div>

          <!-- Success/Error Status -->
          <div v-else class="flex-shrink-0 ml-4">
            <div v-if="file.uploaded" class="text-green-600">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
            <div v-else-if="file.error" class="text-red-600">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                  clip-rule="evenodd"
                />
              </svg>
            </div>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center space-x-2 ml-4">
          <!-- Preview Button (for images) -->
          <button
            v-if="file.type.startsWith('image/') && file.preview"
            @click="showPreview(file)"
            class="text-gray-400 hover:text-gray-600"
            title="Preview"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>

          <!-- Download Button (for uploaded files) -->
          <button
            v-if="file.uploaded && file.url"
            @click="downloadFile(file)"
            class="text-gray-400 hover:text-gray-600"
            title="Download"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
          </button>

          <!-- Remove Button -->
          <button
            @click="removeFile(index)"
            class="text-red-400 hover:text-red-600"
            title="Remove"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Image Preview Modal -->
    <div
      v-if="showPreviewModal && previewFile"
      class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
      @click="closePreview"
    >
      <div class="max-w-4xl max-h-full p-4" @click.stop>
        <img
          :src="previewFile.preview"
          :alt="previewFile.name"
          class="max-w-full max-h-full object-contain"
        />
        <div class="mt-4 text-center">
          <p class="text-white text-lg font-medium">{{ previewFile.name }}</p>
          <button
            @click="closePreview"
            class="mt-2 px-4 py-2 bg-white text-black rounded hover:bg-gray-100"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface FileUploadItem {
  name: string
  size: number
  type: string
  file?: File
  preview?: string
  progress?: number
  uploaded: boolean
  uploadedAt?: Date
  url?: string
  id?: string
  error?: string
}

interface Props {
  modelValue?: FileUploadItem[]
  allowedTypes?: string[]
  maxSize?: number // in bytes
  maxFiles?: number
  uploadUrl?: string
  label?: string
  error?: string
}

interface Emits {
  (e: 'update:modelValue', files: FileUploadItem[]): void
  (e: 'upload', file: FileUploadItem): void
  (e: 'remove', file: FileUploadItem): void
  (e: 'error', error: string): void
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: () => [],
  allowedTypes: () => [],
  maxSize: 10 * 1024 * 1024, // 10MB
  maxFiles: 1,
  uploadUrl: '/api/files/upload'
})

const emit = defineEmits<Emits>()

// Reactive state
const fileInput = ref<HTMLInputElement>()
const isDragOver = ref(false)
const showPreviewModal = ref(false)
const previewFile = ref<FileUploadItem | null>(null)

// Computed
const files = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

// Utility functions
const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (date: Date): string => {
  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
}

const getFileTypeIcon = (type: string): string => {
  if (type.startsWith('image/')) return '📷'
  if (type.includes('pdf')) return '📄'
  if (type.includes('word') || type.includes('doc')) return '📝'
  if (type.includes('excel') || type.includes('sheet')) return '📊'
  if (type.includes('powerpoint') || type.includes('presentation')) return '📽️'
  if (type.includes('zip') || type.includes('rar')) return '📦'
  return '📄'
}

const getFileTypeColor = (type: string): string => {
  if (type.startsWith('image/')) return 'bg-green-500'
  if (type.includes('pdf')) return 'bg-red-500'
  if (type.includes('word') || type.includes('doc')) return 'bg-blue-500'
  if (type.includes('excel') || type.includes('sheet')) return 'bg-green-600'
  if (type.includes('powerpoint') || type.includes('presentation')) return 'bg-orange-500'
  if (type.includes('zip') || type.includes('rar')) return 'bg-gray-500'
  return 'bg-gray-400'
}

// Validation
const validateFile = (file: File): string | null => {
  // Check file size
  if (props.maxSize && file.size > props.maxSize) {
    return `File size exceeds ${formatFileSize(props.maxSize)}`
  }

  // Check file type
  if (props.allowedTypes.length > 0) {
    const fileExtension = '.' + file.name.split('.').pop()?.toLowerCase()
    const mimeType = file.type
    
    const isTypeAllowed = props.allowedTypes.some(type => 
      type === mimeType || type === fileExtension || 
      (type.endsWith('/*') && mimeType.startsWith(type.replace('/*', '/')))
    )
    
    if (!isTypeAllowed) {
      return `File type not allowed. Allowed types: ${props.allowedTypes.join(', ')}`
    }
  }

  // Check max files
  if (files.value.length >= props.maxFiles) {
    return `Maximum ${props.maxFiles} file(s) allowed`
  }

  return null
}

// File handling
const openFileDialog = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    handleFiles(Array.from(target.files))
  }
}

const handleDrop = (event: DragEvent) => {
  isDragOver.value = false
  if (event.dataTransfer?.files) {
    handleFiles(Array.from(event.dataTransfer.files))
  }
}

const handleFiles = async (fileList: File[]) => {
  for (const file of fileList) {
    const validationError = validateFile(file)
    if (validationError) {
      emit('error', validationError)
      continue
    }

    const fileItem: FileUploadItem = {
      name: file.name,
      size: file.size,
      type: file.type,
      file,
      progress: 0,
      uploaded: false
    }

    // Create preview for images
    if (file.type.startsWith('image/')) {
      fileItem.preview = await createImagePreview(file)
    }

    // Add to files list
    const newFiles = [...files.value, fileItem]
    files.value = newFiles

    // Start upload
    uploadFile(fileItem)
  }
}

const createImagePreview = (file: File): Promise<string> => {
  return new Promise((resolve) => {
    const reader = new FileReader()
    reader.onload = (e) => resolve(e.target?.result as string)
    reader.readAsDataURL(file)
  })
}

const uploadFile = async (fileItem: FileUploadItem) => {
  if (!fileItem.file) return

  const formData = new FormData()
  formData.append('file', fileItem.file)

  try {
    // Simulate upload progress
    const xhr = new XMLHttpRequest()
    
    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) {
        const progress = Math.round((e.loaded / e.total) * 100)
        updateFileProgress(fileItem, progress)
      }
    })

    xhr.onload = () => {
      if (xhr.status === 200) {
        try {
          const response = JSON.parse(xhr.responseText)
          if (response.success) {
            updateFileSuccess(fileItem, response.data)
          } else {
            updateFileError(fileItem, response.message || 'Upload failed')
          }
        } catch (error) {
          updateFileError(fileItem, 'Invalid response from server')
        }
      } else {
        updateFileError(fileItem, `Upload failed with status ${xhr.status}`)
      }
    }

    xhr.onerror = () => {
      updateFileError(fileItem, 'Network error during upload')
    }

    xhr.open('POST', props.uploadUrl)
    
    // Add CSRF token and authorization if available
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (csrfToken) {
      xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken)
    }
    
    const authToken = localStorage.getItem('auth_token')
    if (authToken) {
      xhr.setRequestHeader('Authorization', `Bearer ${authToken}`)
    }

    xhr.send(formData)
    
    emit('upload', fileItem)
  } catch (error) {
    updateFileError(fileItem, 'Upload failed')
  }
}

const updateFileProgress = (fileItem: FileUploadItem, progress: number) => {
  const index = files.value.findIndex(f => f === fileItem)
  if (index !== -1) {
    const updatedFiles = [...files.value]
    updatedFiles[index] = { ...fileItem, progress }
    files.value = updatedFiles
  }
}

const updateFileSuccess = (fileItem: FileUploadItem, responseData: any) => {
  const index = files.value.findIndex(f => f === fileItem)
  if (index !== -1) {
    const updatedFiles = [...files.value]
    updatedFiles[index] = {
      ...fileItem,
      progress: 100,
      uploaded: true,
      uploadedAt: new Date(),
      url: responseData.url,
      id: responseData.id
    }
    files.value = updatedFiles
  }
}

const updateFileError = (fileItem: FileUploadItem, error: string) => {
  const index = files.value.findIndex(f => f === fileItem)
  if (index !== -1) {
    const updatedFiles = [...files.value]
    updatedFiles[index] = { ...fileItem, error, progress: undefined }
    files.value = updatedFiles
  }
}

const removeFile = (index: number) => {
  const fileToRemove = files.value[index]
  const newFiles = files.value.filter((_, i) => i !== index)
  files.value = newFiles
  emit('remove', fileToRemove)
}

const showPreview = (file: FileUploadItem) => {
  previewFile.value = file
  showPreviewModal.value = true
}

const closePreview = () => {
  showPreviewModal.value = false
  previewFile.value = null
}

const downloadFile = (file: FileUploadItem) => {
  if (file.url) {
    const link = document.createElement('a')
    link.href = file.url
    link.download = file.name
    link.click()
  }
}
</script>

<style scoped>
.file-upload-component {
  @apply w-full;
}
</style>