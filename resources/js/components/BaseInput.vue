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
      <input
        :id="id"
        :type="type"
        :name="name"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :class="[
          'block w-full rounded-md shadow-sm transition-all duration-300 ease-in-out',
          'border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200',
          { 'border-red-500 focus:border-red-500 focus:ring-red-200': error },
          { 'opacity-50 cursor-not-allowed': disabled }
        ]"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        @blur="$emit('blur', $event)"
      />
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

interface Props {
  type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' | 'date'
  label?: string
  name?: string
  id?: string
  modelValue?: string | number
  placeholder?: string
  disabled?: boolean
  required?: boolean
  error?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  label: '',
  name: '',
  id: '',
  placeholder: '',
  disabled: false,
  required: false,
  error: ''
})

defineEmits(['update:modelValue', 'blur'])
</script> 