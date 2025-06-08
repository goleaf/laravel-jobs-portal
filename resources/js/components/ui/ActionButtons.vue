<template>
  <div class="flex items-center gap-2">
    <!-- Edit Button -->
    <button
      v-if="showEdit"
      type="button"
      class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50"
      :disabled="loading"
      :title="t('common.edit')"
      @click="handleEdit"
    >
      <PencilSquareIcon class="w-4 h-4" />
      <span v-if="showLabels" class="ml-1">{{ t('common.edit') }}</span>
    </button>

    <!-- Delete Button -->
    <button
      v-if="showDelete"
      type="button"
      class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50"
      :disabled="loading || deleting"
      :title="t('common.delete')"
      @click="handleDelete"
    >
      <TrashIcon v-if="!deleting" class="w-4 h-4" />
      <div v-else class="w-4 h-4">
        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
      <span v-if="showLabels" class="ml-1">{{ t('common.delete') }}</span>
    </button>

    <!-- View Button -->
    <button
      v-if="showView"
      type="button"
      class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-md hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50"
      :disabled="loading"
      :title="t('common.view')"
      @click="handleView"
    >
      <EyeIcon class="w-4 h-4" />
      <span v-if="showLabels" class="ml-1">{{ t('common.view') }}</span>
    </button>

    <!-- Custom Actions Slot -->
    <slot name="actions" :item="item" :loading="loading" />
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { PencilSquareIcon, TrashIcon, EyeIcon } from '@heroicons/vue/24/outline'
import { useContext7I18n } from '@/composables/useContext7I18n'
import { useToast } from '@/composables/useToast'
import Swal from 'sweetalert2'

// Types
interface ActionButtonsProps {
  item: Record<string, any>
  showEdit?: boolean
  showDelete?: boolean
  showView?: boolean
  showLabels?: boolean
  editRoute?: string
  deleteRoute?: string
  viewRoute?: string
  confirmDelete?: boolean
  deleteMessage?: string
  loading?: boolean
}

interface ActionButtonsEmits {
  edit: [item: Record<string, any>]
  delete: [item: Record<string, any>]
  view: [item: Record<string, any>]
  deleted: [item: Record<string, any>]
}

// Props with defaults
const props = withDefaults(defineProps<ActionButtonsProps>(), {
  showEdit: true,
  showDelete: true,
  showView: false,
  showLabels: false,
  confirmDelete: true,
  loading: false
})

// Emits
const emit = defineEmits<ActionButtonsEmits>()

// Composables
const { t } = useContext7I18n()
const { showSuccess, showError } = useToast()

// State
const deleting = ref(false)

// Computed
const itemId = computed(() => props.item?.id || props.item?.uuid)

// Methods
const handleEdit = () => {
  if (props.loading) return
  
  if (props.editRoute) {
    // Navigate to edit route
    window.location.href = props.editRoute.replace(':id', itemId.value)
  } else {
    // Emit edit event
    emit('edit', props.item)
  }
}

const handleView = () => {
  if (props.loading) return
  
  if (props.viewRoute) {
    // Navigate to view route
    window.location.href = props.viewRoute.replace(':id', itemId.value)
  } else {
    // Emit view event
    emit('view', props.item)
  }
}

const handleDelete = async () => {
  if (props.loading || deleting.value) return

  if (props.confirmDelete) {
    const result = await Swal.fire({
      title: t('common.are_you_sure'),
      text: props.deleteMessage || t('common.delete_warning'),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#6b7280',
      confirmButtonText: t('common.yes_delete'),
      cancelButtonText: t('common.cancel'),
      reverseButtons: true,
      focusCancel: true
    })

    if (!result.isConfirmed) {
      return
    }
  }

  await performDelete()
}

const performDelete = async () => {
  deleting.value = true

  try {
    if (props.deleteRoute) {
      // Make API call to delete
      const response = await fetch(props.deleteRoute.replace(':id', itemId.value), {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json'
        }
      })

      const data = await response.json()

      if (response.ok) {
        showSuccess(data.message || t('common.delete_success'))
        emit('deleted', props.item)
      } else {
        throw new Error(data.message || t('common.delete_error'))
      }
    } else {
      // Emit delete event for parent to handle
      emit('delete', props.item)
    }
  } catch (error) {
    console.error('Delete error:', error)
    showError(error instanceof Error ? error.message : t('common.something_wrong'))
  } finally {
    deleting.value = false
  }
}

// Expose methods for parent component access
defineExpose({
  handleEdit,
  handleDelete,
  handleView,
  deleting: readonly(deleting)
})
</script>

<style scoped>
/* Component-specific styles if needed */
.action-buttons-enter-active,
.action-buttons-leave-active {
  transition: all 0.2s ease;
}

.action-buttons-enter-from,
.action-buttons-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style> 