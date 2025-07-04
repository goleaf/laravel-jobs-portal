<template>
  <button
    :type="type"
    :class="[
      'px-4 py-2 rounded-md transition-all duration-300 ease-in-out',
      variantClasses,
      sizeClasses,
      { 'opacity-50 cursor-not-allowed': disabled }
    ]"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <slot>{{ label }}</slot>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  type?: 'button' | 'submit' | 'reset'
  variant?: 'primary' | 'secondary' | 'danger' | 'success' | 'warning'
  size?: 'sm' | 'md' | 'lg'
  label?: string
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'button',
  variant: 'primary',
  size: 'md',
  label: '',
  disabled: false
})

const variantClasses = computed(() => {
  switch (props.variant) {
    case 'primary':
      return 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-2 focus:ring-blue-300'
    case 'secondary':
      return 'bg-gray-500 text-white hover:bg-gray-600 focus:ring-2 focus:ring-gray-300'
    case 'danger':
      return 'bg-red-500 text-white hover:bg-red-600 focus:ring-2 focus:ring-red-300'
    case 'success':
      return 'bg-green-500 text-white hover:bg-green-600 focus:ring-2 focus:ring-green-300'
    case 'warning':
      return 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-2 focus:ring-yellow-300'
    default:
      return 'bg-blue-500 text-white hover:bg-blue-600 focus:ring-2 focus:ring-blue-300'
  }
})

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'text-sm px-2 py-1'
    case 'md':
      return 'text-base px-4 py-2'
    case 'lg':
      return 'text-lg px-6 py-3'
    default:
      return 'text-base px-4 py-2'
  }
})

defineEmits(['click'])
</script> 