<template>
  <span 
    :class="[
      baseClasses,
      variantClasses[variant],
      sizeClasses[size],
      {
        'rounded-full': rounded,
        'cursor-pointer hover:opacity-80': clickable,
      }
    ]"
    @click="handleClick"
  >
    <slot name="leading-icon" />
    <span>{{ label || $slots.default }}</span>
    <slot name="trailing-icon" />
    
    <!-- Close button for removable badges -->
    <button 
      v-if="removable" 
      @click.stop="$emit('remove')"
      class="ml-1 -mr-1 p-0.5 rounded-full hover:bg-black/10 transition-colors"
    >
      <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>
    </button>
  </span>
</template>

<script setup lang="ts">
interface Props {
  label?: string
  variant?: 'primary' | 'secondary' | 'success' | 'warning' | 'error' | 'neutral' | 'outline'
  size?: 'xs' | 'sm' | 'md' | 'lg'
  rounded?: boolean
  clickable?: boolean
  removable?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'neutral',
  size: 'md',
  rounded: false,
  clickable: false,
  removable: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
  remove: []
}>()

const baseClasses = "inline-flex items-center font-medium rounded-md transition-all duration-150"

const sizeClasses = {
  xs: "px-2 py-0.5 text-xs gap-1",
  sm: "px-2.5 py-1 text-xs gap-1", 
  md: "px-3 py-1.5 text-sm gap-1.5",
  lg: "px-4 py-2 text-base gap-2"
}

const variantClasses = {
  primary: "bg-primary-100 text-primary-800 border border-primary-200",
  secondary: "bg-secondary-100 text-secondary-800 border border-secondary-200",
  success: "bg-success-100 text-success-800 border border-success-200",
  warning: "bg-warning-100 text-warning-800 border border-warning-200", 
  error: "bg-error-100 text-error-800 border border-error-200",
  neutral: "bg-neutral-100 text-neutral-800 border border-neutral-200",
  outline: "bg-white text-neutral-700 border border-neutral-300 hover:bg-neutral-50"
}

const handleClick = (event: MouseEvent) => {
  if (props.clickable) {
    emit('click', event)
  }
}
</script>