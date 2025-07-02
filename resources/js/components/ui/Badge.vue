<template>
  <span
    :class="badgeClasses"
    class="inline-flex items-center font-medium rounded-full"
  >
    <component
      v-if="leftIcon"
      :is="leftIcon"
      :class="iconClasses"
      class="mr-1"
    />
    
    <slot>{{ text }}</slot>
    
    <component
      v-if="rightIcon"
      :is="rightIcon"
      :class="iconClasses"
      class="ml-1"
    />
    
    <button
      v-if="removable"
      @click="$emit('remove')"
      class="ml-1 hover:bg-black hover:bg-opacity-10 rounded-full p-0.5 transition-colors"
    >
      <XMarkIcon :class="iconClasses" />
    </button>
  </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'

interface Props {
  text?: string
  variant?: 'primary' | 'secondary' | 'success' | 'warning' | 'error' | 'info' | 'dark' | 'light'
  size?: 'xs' | 'sm' | 'md' | 'lg'
  leftIcon?: any
  rightIcon?: any
  removable?: boolean
  pill?: boolean
  outlined?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  pill: false,
  outlined: false,
  removable: false
})

defineEmits<{
  remove: []
}>()

const badgeClasses = computed(() => {
  let classes = ''
  
  // Size classes
  const sizes = {
    xs: 'px-2 py-0.5 text-xs',
    sm: 'px-2.5 py-0.5 text-xs',
    md: 'px-3 py-1 text-sm',
    lg: 'px-4 py-1.5 text-base'
  }
  classes += sizes[props.size]
  
  // Variant classes
  if (props.outlined) {
    const outlinedVariants = {
      primary: 'border border-indigo-200 text-indigo-700 bg-indigo-50',
      secondary: 'border border-gray-200 text-gray-700 bg-gray-50',
      success: 'border border-green-200 text-green-700 bg-green-50',
      warning: 'border border-yellow-200 text-yellow-700 bg-yellow-50',
      error: 'border border-red-200 text-red-700 bg-red-50',
      info: 'border border-blue-200 text-blue-700 bg-blue-50',
      dark: 'border border-gray-600 text-gray-900 bg-gray-100',
      light: 'border border-gray-300 text-gray-600 bg-white'
    }
    classes += ` ${outlinedVariants[props.variant]}`
  } else {
    const solidVariants = {
      primary: 'bg-indigo-100 text-indigo-800',
      secondary: 'bg-gray-100 text-gray-800',
      success: 'bg-green-100 text-green-800',
      warning: 'bg-yellow-100 text-yellow-800',
      error: 'bg-red-100 text-red-800',
      info: 'bg-blue-100 text-blue-800',
      dark: 'bg-gray-800 text-white',
      light: 'bg-white text-gray-800 border border-gray-200'
    }
    classes += ` ${solidVariants[props.variant]}`
  }
  
  return classes
})

const iconClasses = computed(() => {
  const sizes = {
    xs: 'w-3 h-3',
    sm: 'w-3 h-3',
    md: 'w-4 h-4',
    lg: 'w-5 h-5'
  }
  return sizes[props.size]
})
</script>