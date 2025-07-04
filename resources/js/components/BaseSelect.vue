<template>
  <div class="mb-4">
    <label 
      v-if="label" 
      :for="id" 
      class="block text-sm font-medium text-gray-700 mb-2"
    >
      {{ label }}
    </label>
    <div class="relative">
      <select
        :id="id"
        :name="name"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :multiple="multiple"
        :class="[
          'block w-full rounded-md shadow-sm transition-all duration-300 ease-in-out',
          'border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200',
          {
            'border-red-500 focus:border-red-500 focus:ring-red-200': error,
            'opacity-50 cursor-not-allowed': disabled,
            'h-32': multiple
          }
        ]"
        @change="handleChange"
      >
        <option 
          v-if="placeholder" 
          value="" 
          disabled
        >
          {{ placeholder }}
        </option>
        <option 
          v-for="option in normalizedOptions" 
          :key="option.value" 
          :value="option.value"
          :disabled="option.disabled"
        >
          {{ option.label }}
        </option>
      </select>
      
      <div 
        v-if="error" 
        class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
      >
        <svg 
          class="h-5 w-5 text-red-500" 
          fill="currentColor" 
          viewBox="0 0 20 20"
        >
          <path 
            fill-rule="evenodd" 
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" 
            clip-rule="evenodd" 
          />
        </svg>
      </div>
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
  modelValue?: string | number | string[] | number[]
  label?: string
  name?: string
  id?: string
  placeholder?: string
  options: Option[] | string[] | number[]
  disabled?: boolean
  required?: boolean
  multiple?: boolean
  error?: string
}

const props = withDefaults(defineProps<Props>(), {
  label: '',
  name: '',
  id: '',
  placeholder: '',
  disabled: false,
  required: false,
  multiple: false,
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

const handleChange = (event: Event) => {
  const target = event.target as HTMLSelectElement
  const value = props.multiple 
    ? Array.from(target.selectedOptions).map(option => option.value)
    : target.value

  emit('update:modelValue', value)
  emit('change', value)
}
</script>

<style scoped>
/* Additional scoped styles if needed */
select:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
}
</style> 