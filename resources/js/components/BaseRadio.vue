<template>
  <div class="space-y-2">
    <div 
      v-for="(option, index) in normalizedOptions" 
      :key="option.value" 
      class="flex items-center"
    >
      <input
        :id="`${id}-${index}`"
        type="radio"
        :name="name"
        :value="option.value"
        :checked="modelValue === option.value"
        :disabled="disabled || option.disabled"
        :required="required"
        :class="[
          'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded-full transition-all duration-300 ease-in-out',
          {
            'opacity-50 cursor-not-allowed': disabled || option.disabled,
            'border-red-500 focus:ring-red-500': error
          }
        ]"
        @change="handleChange(option.value)"
      />
      <label 
        :for="`${id}-${index}`"
        :class="[
          'ml-2 block text-sm text-gray-900 select-none',
          { 
            'opacity-50 cursor-not-allowed': disabled || option.disabled 
          }
        ]"
      >
        {{ option.label }}
      </label>
    </div>
    
    <p 
      v-if="error" 
      class="mt-2 text-sm text-red-600"
    >
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

type Option = {
  value: string | number
  label: string
  disabled?: boolean
}

interface Props {
  modelValue?: string | number
  label?: string
  name?: string
  id?: string
  options: Option[] | string[] | number[]
  disabled?: boolean
  required?: boolean
  error?: string
}

const props = withDefaults(defineProps<Props>(), {
  label: '',
  name: '',
  id: '',
  disabled: false,
  required: false,
  error: ''
})

const emit = defineEmits(['update:modelValue', 'change'])

const normalizedOptions = computed(() => {
  return props.options.map(option => {
    if (typeof option === 'string' || typeof option === 'number') {
      return { 
        value: option, 
        label: String(option) 
      }
    }
    return option
  })
})

const handleChange = (value: string | number) => {
  emit('update:modelValue', value)
  emit('change', value)
}
</script>

<style scoped>
/* Custom focus ring for radio */
input[type="radio"]:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
}

/* Custom checked state */
input[type="radio"]:checked {
  background-color: theme('colors.blue.600');
  border-color: theme('colors.blue.600');
}
</style> 