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
      <textarea
        :id="id"
        :name="name"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :rows="rows"
        :maxlength="maxlength"
        :class="[
          'block w-full rounded-md shadow-sm transition-all duration-300 ease-in-out',
          'border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200',
          {
            'border-red-500 focus:border-red-500 focus:ring-red-200': error,
            'opacity-50 cursor-not-allowed': disabled
          }
        ]"
        @input="handleInput"
        @blur="$emit('blur', $event)"
      ></textarea>
      
      <div 
        v-if="error" 
        class="absolute top-2 right-2 pointer-events-none"
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
    
    <div class="flex justify-between mt-1">
      <p 
        v-if="error" 
        class="text-sm text-red-600"
      >
        {{ error }}
      </p>
      <p 
        v-if="maxlength" 
        class="text-sm text-gray-500 ml-auto"
      >
        {{ currentLength }}/{{ maxlength }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface Props {
  modelValue?: string
  label?: string
  name?: string
  id?: string
  placeholder?: string
  disabled?: boolean
  required?: boolean
  rows?: number
  maxlength?: number
  error?: string
}

const props = withDefaults(defineProps<Props>(), {
  label: '',
  name: '',
  id: '',
  placeholder: '',
  disabled: false,
  required: false,
  rows: 4,
  maxlength: undefined,
  error: ''
})

const emit = defineEmits(['update:modelValue', 'blur'])

const currentLength = computed(() => props.modelValue?.length || 0)

const handleInput = (event: Event) => {
  const target = event.target as HTMLTextAreaElement
  const value = target.value

  // If maxlength is set, truncate the input
  if (props.maxlength && value.length > props.maxlength) {
    target.value = value.slice(0, props.maxlength)
    return
  }

  emit('update:modelValue', value)
}
</script>

<style scoped>
textarea:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
}

textarea {
  resize: vertical;
}
</style> 