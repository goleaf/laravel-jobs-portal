<template>
  <Teleport to="body">
    <Transition name="modal-backdrop" appear>
      <div 
        v-if="show" 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 backdrop-blur-sm"
        @click="handleBackdropClick"
        role="dialog"
        :aria-label="title || $t('ui.modal.default_title')"
        aria-modal="true"
        :aria-describedby="contentId"
      >
        <Transition name="modal-content" appear>
          <div 
            :class="[
              'bg-white rounded-lg w-full mx-4 max-h-[90vh] overflow-hidden shadow-2xl',
              sizeClasses[size],
              {
                'dark:bg-gray-800 dark:text-white': isDark
              }
            ]"
            @click.stop
            role="document"
            ref="modalRef"
          >
            <!-- Header -->
            <div 
              :class="[
                'flex justify-between items-center p-6 border-b',
                {
                  'border-gray-200 dark:border-gray-700': true,
                  'bg-gray-50 dark:bg-gray-700': headerBackground
                }
              ]"
            >
              <!-- Title Section -->
              <div class="flex items-center space-x-3">
                <component 
                  v-if="icon" 
                  :is="icon" 
                  :class="[
                    'w-6 h-6',
                    iconColorClasses[variant]
                  ]"
                  :aria-hidden="true"
                />
                <div>
                  <h3 
                    class="text-lg font-semibold leading-6"
                    :id="titleId"
                  >
                    {{ title || $t('ui.modal.default_title') }}
                  </h3>
                  <p 
                    v-if="subtitle" 
                    class="text-sm text-gray-500 dark:text-gray-400 mt-1"
                  >
                    {{ subtitle }}
                  </p>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center space-x-2">
                <button
                  v-if="minimizable"
                  @click="handleMinimize"
                  class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                  :aria-label="$t('ui.modal.minimize')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                  </svg>
                </button>
                
                <button
                  v-if="maximizable"
                  @click="handleMaximize"
                  class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                  :aria-label="isMaximized ? $t('ui.modal.restore') : $t('ui.modal.maximize')"
                >
                  <svg v-if="!isMaximized" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9V4.5M9 9H4.5M9 9L3.5 3.5M15 9v-4.5M15 9h4.5M15 9l5.5-5.5M9 15v4.5M9 15H4.5M9 15l-5.5 5.5M15 15v4.5M15 15h4.5m0 0l5.5 5.5"></path>
                  </svg>
                </button>

                <button
                  v-if="closable"
                  @click="closeModal"
                  class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                  :aria-label="$t('ui.modal.close')"
                >
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
            </div>

            <!-- Content -->
            <div 
              class="overflow-y-auto"
              :class="contentPaddingClasses"
              :style="{ maxHeight: isMaximized ? 'calc(100vh - 200px)' : 'calc(90vh - 200px)' }"
              :id="contentId"
            >
              <slot></slot>
            </div>

            <!-- Footer -->
            <div 
              v-if="$slots.footer" 
              :class="[
                'p-6 border-t flex justify-end space-x-3',
                {
                  'border-gray-200 dark:border-gray-700': true,
                  'bg-gray-50 dark:bg-gray-700': footerBackground
                }
              ]"
            >
              <slot name="footer"></slot>
            </div>

            <!-- Loading Overlay -->
            <div 
              v-if="loading" 
              class="absolute inset-0 bg-white bg-opacity-75 dark:bg-gray-800 dark:bg-opacity-75 flex items-center justify-center"
            >
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
              <span class="ml-3 text-sm text-gray-600 dark:text-gray-400">
                {{ loadingText || $t('ui.modal.loading') }}
              </span>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useI18n } from 'vue-i18n'

interface Props {
  show: boolean
  title?: string
  subtitle?: string
  size?: "xs" | "sm" | "md" | "lg" | "xl" | "full"
  variant?: "default" | "success" | "warning" | "danger" | "info"
  icon?: string
  closable?: boolean
  minimizable?: boolean
  maximizable?: boolean
  loading?: boolean
  loadingText?: string
  closeOnBackdrop?: boolean
  closeOnEscape?: boolean
  persistent?: boolean
  headerBackground?: boolean
  footerBackground?: boolean
  isDark?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: "md",
  variant: "default",
  closable: true,
  minimizable: false,
  maximizable: false,
  loading: false,
  closeOnBackdrop: true,
  closeOnEscape: true,
  persistent: false,
  headerBackground: false,
  footerBackground: false,
  isDark: false
})

const emit = defineEmits<{
  close: []
  minimize: []
  maximize: []
  restore: []
}>()

const { t } = useI18n()

// Refs
const modalRef = ref<HTMLElement>()
const isMaximized = ref(false)
const isMinimized = ref(false)

// Computed
const titleId = computed(() => `modal-title-${Math.random().toString(36).substr(2, 9)}`)
const contentId = computed(() => `modal-content-${Math.random().toString(36).substr(2, 9)}`)

const sizeClasses = {
  xs: 'max-w-md',
  sm: 'max-w-lg',
  md: 'max-w-2xl',
  lg: 'max-w-4xl',
  xl: 'max-w-6xl',
  full: 'max-w-full h-full m-0 rounded-none'
}

const iconColorClasses = {
  default: 'text-gray-500',
  success: 'text-green-500',
  warning: 'text-yellow-500',
  danger: 'text-red-500',
  info: 'text-blue-500'
}

const contentPaddingClasses = computed(() => {
  return props.size === 'full' ? 'p-4' : 'p-6'
})

// Methods
const closeModal = () => {
  if (!props.persistent) {
    emit("close")
  }
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    closeModal()
  }
}

const handleMinimize = () => {
  isMinimized.value = !isMinimized.value
  emit("minimize")
}

const handleMaximize = () => {
  isMaximized.value = !isMaximized.value
  emit(isMaximized.value ? "maximize" : "restore")
}

const handleEscape = (e: KeyboardEvent) => {
  if (e.key === "Escape" && props.closeOnEscape && props.show) {
    closeModal()
  }
}

const trapFocus = (e: KeyboardEvent) => {
  if (!modalRef.value || e.key !== 'Tab') return

  const focusableElements = modalRef.value.querySelectorAll(
    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
  )
  
  const firstElement = focusableElements[0] as HTMLElement
  const lastElement = focusableElements[focusableElements.length - 1] as HTMLElement

  if (e.shiftKey) {
    if (document.activeElement === firstElement) {
      lastElement.focus()
      e.preventDefault()
    }
  } else {
    if (document.activeElement === lastElement) {
      firstElement.focus()
      e.preventDefault()
    }
  }
}

// Lifecycle
watch(() => props.show, async (newShow) => {
  if (newShow) {
    await nextTick()
    // Focus first focusable element
    const firstFocusable = modalRef.value?.querySelector(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    ) as HTMLElement
    firstFocusable?.focus()
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden'
  } else {
    // Restore body scroll
    document.body.style.overflow = ''
    isMaximized.value = false
    isMinimized.value = false
  }
})

onMounted(() => {
  document.addEventListener("keydown", handleEscape)
  document.addEventListener("keydown", trapFocus)
})

onUnmounted(() => {
  document.removeEventListener("keydown", handleEscape)
  document.removeEventListener("keydown", trapFocus)
  document.body.style.overflow = ''
})
</script>

<style scoped>
/* Modal Backdrop Transitions */
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
  transition: opacity 0.3s ease;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
  opacity: 0;
}

/* Modal Content Transitions */
.modal-content-enter-active {
  transition: all 0.3s ease;
}

.modal-content-leave-active {
  transition: all 0.2s ease;
}

.modal-content-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(-20px);
}

.modal-content-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .dark\:bg-gray-800 {
    background-color: #1f2937;
  }
  
  .dark\:text-white {
    color: #ffffff;
  }
  
  .dark\:border-gray-700 {
    border-color: #374151;
  }
}
</style>