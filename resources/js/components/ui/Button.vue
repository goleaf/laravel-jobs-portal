<template>
  <button
    :class="[
      baseClasses,
      sizeClasses[size],
      variantClasses[variant],
      { 
        'opacity-50 cursor-not-allowed': disabled,
        'animate-pulse': loading,
        'shadow-lg': elevation && !disabled,
        'w-full': fullWidth
      }
    ]"
    :disabled="disabled || loading"
    :aria-label="ariaLabel || $t('ui.button.default_label')"
    :aria-describedby="ariaDescribedby"
    :type="type"
    @click="handleClick"
    @keydown.enter="handleKeyDown"
    @keydown.space="handleKeyDown"
  >
    <!-- Loading Spinner -->
    <div 
      v-if="loading" 
      class="animate-spin rounded-full h-4 w-4 border-b-2 border-current mr-2"
      :aria-label="$t('ui.button.loading')"
    ></div>
    
    <!-- Left Icon -->
    <component 
      v-if="leftIcon && !loading" 
      :is="leftIcon" 
      class="w-4 h-4 mr-2" 
      :aria-hidden="true"
    />
    
    <!-- Button Content -->
    <span :class="{ 'sr-only': loading && hideTextOnLoading }">
      <slot>{{ text || $t('ui.button.default_text') }}</slot>
    </span>
    
    <!-- Right Icon -->
    <component 
      v-if="rightIcon && !loading" 
      :is="rightIcon" 
      class="w-4 h-4 ml-2" 
      :aria-hidden="true"
    />
    
    <!-- Badge/Counter -->
    <span 
      v-if="badge && !loading" 
      class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full"
      :aria-label="$t('ui.button.badge_count', { count: badge })"
    >
      {{ badge }}
    </span>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

interface Props {
  variant?: "primary" | "secondary" | "success" | "danger" | "warning" | "info" | "outline" | "ghost"
  size?: "xs" | "sm" | "md" | "lg" | "xl"
  disabled?: boolean
  loading?: boolean
  type?: "button" | "submit" | "reset"
  fullWidth?: boolean
  elevation?: boolean
  leftIcon?: string
  rightIcon?: string
  text?: string
  ariaLabel?: string
  ariaDescribedby?: string
  badge?: number | string
  hideTextOnLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: "primary",
  size: "md",
  disabled: false,
  loading: false,
  type: "button",
  fullWidth: false,
  elevation: false,
  hideTextOnLoading: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
  keydown: [event: KeyboardEvent]
}>()

const { t } = useI18n()

const baseClasses = computed(() => 
  "inline-flex items-center justify-center font-medium rounded-md transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 active:transform active:scale-95"
)

const sizeClasses = {
  xs: "px-2 py-1 text-xs",
  sm: "px-3 py-1.5 text-sm",
  md: "px-4 py-2 text-sm",
  lg: "px-6 py-3 text-base",
  xl: "px-8 py-4 text-lg"
}

const variantClasses = {
  primary: "bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 shadow-sm",
  secondary: "bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500 shadow-sm",
  success: "bg-green-600 text-white hover:bg-green-700 focus:ring-green-500 shadow-sm",
  danger: "bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-sm",
  warning: "bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500 shadow-sm",
  info: "bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 shadow-sm",
  outline: "border-2 border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-gray-500",
  ghost: "text-gray-700 hover:bg-gray-100 focus:ring-gray-500"
}

const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit("click", event)
  }
}

const handleKeyDown = (event: KeyboardEvent) => {
  if (!props.disabled && !props.loading && (event.key === 'Enter' || event.key === ' ')) {
    event.preventDefault()
    emit("keydown", event)
    // Trigger click event for keyboard interaction
    handleClick(event as unknown as MouseEvent)
  }
}
</script>

<style scoped>
/* Additional Enhanced Button Styles */
.btn-ripple {
  position: relative;
  overflow: hidden;
}

.btn-ripple::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.5);
  transform: translate(-50%, -50%);
  transition: width 0.6s, height 0.6s;
}

.btn-ripple:active::before {
  width: 300px;
  height: 300px;
}

/* Focus visible for better accessibility */
.focus\:ring-offset-2:focus-visible {
  outline: 2px solid transparent;
  outline-offset: 2px;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .dark\:bg-gray-800 {
    background-color: #1f2937;
  }
  
  .dark\:text-gray-200 {
    color: #e5e7eb;
  }
  
  .dark\:border-gray-600 {
    border-color: #4b5563;
  }
}
</style>