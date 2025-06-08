<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <!-- Background overlay -->
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          @click="closeOnBackdrop && close()"
        ></div>

        <!-- Modal container -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <Transition
            enter-active-class="transition duration-300 ease-out transform"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="transition duration-200 ease-in transform"
            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <div
              v-if="modelValue"
              :class="[
                'relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all',
                sizeClasses,
                'sm:my-8 sm:w-full'
              ]"
            >
              <!-- Header -->
              <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4" v-if="title || $slots.header">
                <div class="flex items-center justify-between">
                  <div class="flex items-center">
                    <!-- Icon -->
                    <div
                      v-if="icon"
                      :class="[
                        'mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10',
                        iconClasses
                      ]"
                    >
                      <component :is="icon" class="h-6 w-6" />
                    </div>
                    <!-- Title -->
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left" :class="{ 'sm:ml-0': !icon }">
                      <h3
                        v-if="title"
                        class="text-lg font-medium leading-6 text-gray-900"
                        id="modal-title"
                      >
                        {{ title }}
                      </h3>
                      <slot name="header" />
                    </div>
                  </div>
                  <!-- Close button -->
                  <button
                    v-if="closable"
                    @click="close"
                    class="rounded-md bg-white text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                  >
                    <span class="sr-only">{{ $t('common.close') }}</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Body -->
              <div class="bg-white px-4 pb-4 sm:p-6 sm:pb-4" :class="{ 'pt-5': !title && !$slots.header }">
                <div class="sm:flex sm:items-start">
                  <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                    <div v-if="description" class="mt-2">
                      <p class="text-sm text-gray-500">{{ description }}</p>
                    </div>
                    <div class="mt-4" :class="{ 'mt-2': !description }">
                      <slot />
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div
                v-if="$slots.footer || showDefaultFooter"
                class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6"
              >
                <slot name="footer">
                  <div v-if="showDefaultFooter" class="flex space-x-3 sm:space-x-0 sm:space-x-reverse sm:space-x-3">
                    <!-- Confirm button -->
                    <button
                      v-if="confirmText"
                      @click="confirm"
                      :disabled="loading"
                      :class="[
                        'inline-flex w-full justify-center rounded-md border border-transparent px-4 py-2 text-base font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm',
                        confirmButtonClasses,
                        { 'opacity-50 cursor-not-allowed': loading }
                      ]"
                    >
                      <svg
                        v-if="loading"
                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                      >
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ loading ? loadingText : confirmText }}
                    </button>
                    <!-- Cancel button -->
                    <button
                      v-if="cancelText"
                      @click="cancel"
                      :disabled="loading"
                      class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      {{ cancelText }}
                    </button>
                  </div>
                </slot>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { computed, watch, nextTick } from 'vue'

// Props interface
interface Context7ModalProps {
  modelValue: boolean
  title?: string
  description?: string
  size?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | 'full'
  icon?: any
  iconType?: 'success' | 'error' | 'warning' | 'info'
  closable?: boolean
  closeOnBackdrop?: boolean
  confirmText?: string
  cancelText?: string
  confirmType?: 'primary' | 'danger' | 'success' | 'warning'
  loading?: boolean
  loadingText?: string
  persistent?: boolean
}

// Props with defaults
const props = withDefaults(defineProps<Context7ModalProps>(), {
  size: 'md',
  closable: true,
  closeOnBackdrop: true,
  confirmType: 'primary',
  loading: false,
  loadingText: 'Loading...',
  persistent: false
})

// Emits
const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'confirm': []
  'cancel': []
  'close': []
}>()

// Computed properties
const sizeClasses = computed(() => {
  const sizes = {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
    full: 'sm:max-w-full sm:m-4'
  }
  return sizes[props.size]
})

const iconClasses = computed(() => {
  if (!props.iconType) return 'bg-gray-100'
  
  const types = {
    success: 'bg-green-100',
    error: 'bg-red-100',
    warning: 'bg-yellow-100',
    info: 'bg-blue-100'
  }
  return types[props.iconType]
})

const confirmButtonClasses = computed(() => {
  const types = {
    primary: 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
    danger: 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
    success: 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
    warning: 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
  }
  return types[props.confirmType]
})

const showDefaultFooter = computed(() => {
  return props.confirmText || props.cancelText
})

// Methods
const close = () => {
  if (props.persistent && props.loading) return
  emit('update:modelValue', false)
  emit('close')
}

const confirm = () => {
  emit('confirm')
}

const cancel = () => {
  emit('cancel')
  if (!props.persistent) {
    close()
  }
}

// Handle escape key
const handleEscape = (event: KeyboardEvent) => {
  if (event.key === 'Escape' && props.modelValue && props.closable && !props.persistent) {
    close()
  }
}

// Watch for modal open/close to manage body scroll and keyboard events
watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    document.body.style.overflow = 'hidden'
    document.addEventListener('keydown', handleEscape)
    
    // Focus management
    nextTick(() => {
      const modal = document.querySelector('[role="dialog"]')
      const focusableElements = modal?.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      )
      if (focusableElements && focusableElements.length > 0) {
        (focusableElements[0] as HTMLElement).focus()
      }
    })
  } else {
    document.body.style.overflow = ''
    document.removeEventListener('keydown', handleEscape)
  }
})

// Cleanup on unmount
import { onUnmounted } from 'vue'
onUnmounted(() => {
  document.body.style.overflow = ''
  document.removeEventListener('keydown', handleEscape)
})
</script>

<style scoped>
/* Additional styles if needed */
</style> 