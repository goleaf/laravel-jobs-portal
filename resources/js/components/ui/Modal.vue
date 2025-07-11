<template>
  <Teleport to="body">
    <Transition name="modal-backdrop" appear>
      <div 
        v-if="show" 
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 backdrop-blur-sm p-4"
        @click="handleBackdropClick"
        role="dialog"
        :aria-label="title || $t('ui.modal.default_title')"
        aria-modal="true"
        :aria-describedby="contentId"
      >
        <Transition name="modal-content" appear>
          <div 
            :class="[
              'bg-white dark:bg-gray-800 rounded-xl w-full mx-auto max-h-[95vh] overflow-hidden shadow-2xl transition-all duration-300 ease-in-out transform',
              sizeClasses[size],
              isMaximized ? 'h-full' : '',
              isMinimized ? 'h-auto w-auto translate-y-full opacity-0 pointer-events-none' : '',
            ]"
            @click.stop
            role="document"
            ref="modalRef"
          >
            <!-- Header -->
            <div 
              :class="[
                'flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700',
                {
                  'bg-gray-50 dark:bg-gray-700': headerBackground,
                  'bg-blue-50 dark:bg-blue-900/20': variant === 'info',
                  'bg-green-50 dark:bg-green-900/20': variant === 'success',
                  'bg-yellow-50 dark:bg-yellow-900/20': variant === 'warning',
                  'bg-red-50 dark:bg-red-900/20': variant === 'danger',
                }
              ]"
            >
              <!-- Title Section -->
              <div class="flex items-center space-x-3">
                <slot name="header-icon"></slot>
                <div>
                  <h3 
                    class="text-2xl font-extrabold text-gray-900 dark:text-white leading-tight"
                    :id="titleId"
                  >
                    {{ title || $t('ui.modal.default_title') }}
                  </h3>
                  <p 
                    v-if="subtitle" 
                    class="text-base text-gray-600 dark:text-gray-400 mt-1"
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
                  class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                  :aria-label="$t('ui.modal.minimize')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                  </svg>
                </button>
                
                <button
                  v-if="maximizable"
                  @click="handleMaximize"
                  class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
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
                  class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
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
              class="overflow-y-auto custom-scrollbar"
              :class="contentPaddingClasses"
              :style="{ maxHeight: isMaximized ? 'calc(100vh - 120px)' : 'calc(95vh - 120px)' }"
              :id="contentId"
            >
              <slot></slot>
            </div>

            <!-- Footer -->
            <div 
              v-if="$slots.footer" 
              :class="[
                'p-6 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3',
                {
                  'bg-gray-50 dark:bg-gray-700': footerBackground,
                  'bg-blue-50 dark:bg-blue-900/20': variant === 'info',
                  'bg-green-50 dark:bg-green-900/20': variant === 'success',
                  'bg-yellow-50 dark:bg-yellow-900/20': variant === 'warning',
                  'bg-red-50 dark:bg-red-900/20': variant === 'danger',
                }
              ]"
            >
              <slot name="footer"></slot>
            </div>

            <!-- Loading Overlay -->
            <div 
              v-if="loading" 
              class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-90 dark:bg-opacity-90 flex flex-col items-center justify-center transition-opacity duration-300"
            >
              <svg class="animate-spin h-10 w-10 text-blue-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span class="text-lg font-medium text-gray-700 dark:text-gray-300">
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
  // icon?: string; // Removed temporarily until a proper Vue icon component is integrated
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
  full: 'max-w-full h-full m-0 rounded-none' // Added full-screen classes
}

// Removed iconColorClasses as icon prop is temporarily removed
// const iconColorClasses = {
//   default: 'text-gray-500',
//   success: 'text-green-500',
//   warning: 'text-yellow-500',
//   danger: 'text-red-500',
//   info: 'text-blue-500'
// }

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

// Keyboard event listener for escape key
onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})

// Watch for show prop changes to manage scrollbar
watch(() => props.show, (newVal) => {
  if (newVal) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
}, { immediate: true })
</script>

<style>
/* Transitions */
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
  transition: opacity 0.3s ease;
}
.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
  opacity: 0;
}

.modal-content-enter-active,
.modal-content-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.modal-content-enter-from,
.modal-content-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}

/* Custom scrollbar for content area */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1; /* Light gray for track */
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #888; /* Dark gray for thumb */
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #555; /* Even darker gray on hover */
}

/* Dark mode scrollbar */
.dark .custom-scrollbar::-webkit-scrollbar-track {
  background: #333; /* Darker track for dark mode */
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #bbb; /* Lighter thumb for dark mode */
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #ddd; /* Even lighter thumb on hover */
}
</style>