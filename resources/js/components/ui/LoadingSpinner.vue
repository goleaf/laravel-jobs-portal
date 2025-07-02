<template>
  <div
    class="inline-flex items-center justify-center"
    :class="containerClasses"
  >
    <svg
      class="animate-spin"
      :class="spinnerClasses"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
      ></path>
    </svg>
    <span v-if="text" class="ml-2" :class="textClasses">
      {{ text }}
    </span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl'
  variant?: 'primary' | 'secondary' | 'white' | 'gray'
  text?: string
  overlay?: boolean
  fullScreen?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  variant: 'primary'
})

const containerClasses = computed(() => {
  let classes = ''
  
  if (props.fullScreen) {
    classes += 'fixed inset-0 z-50 bg-white bg-opacity-75'
  } else if (props.overlay) {
    classes += 'absolute inset-0 bg-white bg-opacity-75 z-10'
  }
  
  return classes
})

const spinnerClasses = computed(() => {
  const sizes = {
    xs: 'w-3 h-3',
    sm: 'w-4 h-4', 
    md: 'w-6 h-6',
    lg: 'w-8 h-8',
    xl: 'w-12 h-12'
  }
  
  const variants = {
    primary: 'text-indigo-600',
    secondary: 'text-gray-600',
    white: 'text-white',
    gray: 'text-gray-400'
  }
  
  return `${sizes[props.size]} ${variants[props.variant]}`
})

const textClasses = computed(() => {
  const sizes = {
    xs: 'text-xs',
    sm: 'text-sm',
    md: 'text-base',
    lg: 'text-lg',
    xl: 'text-xl'
  }
  
  const variants = {
    primary: 'text-indigo-600',
    secondary: 'text-gray-600',
    white: 'text-white',
    gray: 'text-gray-500'
  }
  
  return `${sizes[props.size]} ${variants[props.variant]} font-medium`
})
</script> 