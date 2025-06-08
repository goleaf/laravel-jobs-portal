<template>
  <div class="space-y-1">
    <!-- Label -->
    <label 
      v-if="label" 
      :for="inputId" 
      class="block text-sm font-medium"
      :class="{
        'text-neutral-700': !error,
        'text-error-600': error
      }"
    >
      {{ label }}
      <span v-if="required" class="text-error-500 ml-1">*</span>
    </label>

    <!-- Input Container -->
    <div class="relative">
      <!-- Leading Icon -->
      <div v-if="$slots['leading-icon']" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <slot name="leading-icon" />
      </div>

      <!-- Input Element -->
      <component
        :is="inputElement"
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :autocomplete="autocomplete"
        :maxlength="maxlength"
        :rows="rows"
        :class="[
          baseClasses,
          sizeClasses[size],
          stateClasses,
          {
            'pl-10': $slots['leading-icon'],
            'pr-10': $slots['trailing-icon'] || error,
          }
        ]"
        v-bind="$attrs"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
      />

      <!-- Trailing Icon or Error Icon -->
      <div v-if="$slots['trailing-icon'] || error" class="absolute inset-y-0 right-0 pr-3 flex items-center">
        <slot name="trailing-icon" v-if="!error" />
        <svg v-if="error" class="h-5 w-5 text-error-500" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>

    <!-- Helper Text or Error Message -->
    <div v-if="helperText || error" class="text-sm">
      <p v-if="error" class="text-error-600 flex items-center gap-1">
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        {{ error }}
      </p>
      <p v-else-if="helperText" class="text-neutral-500">{{ helperText }}</p>
    </div>

    <!-- Character Count -->
    <div v-if="maxlength && showCharCount" class="text-xs text-neutral-400 text-right">
      {{ characterCount }} / {{ maxlength }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'

interface Props {
  modelValue?: string | number
  type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' | 'search' | 'textarea'
  label?: string
  placeholder?: string
  helperText?: string
  error?: string
  size?: 'sm' | 'md' | 'lg'
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  autocomplete?: string
  maxlength?: number
  showCharCount?: boolean
  rows?: number
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  size: 'md',
  disabled: false,
  readonly: false,
  required: false,
  showCharCount: false,
  rows: 3
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  focus: [event: FocusEvent]
  blur: [event: FocusEvent]
}>()

const inputId = ref(`input-${Math.random().toString(36).substr(2, 9)}`)
const isFocused = ref(false)

const inputElement = computed(() => {
  return props.type === 'textarea' ? 'textarea' : 'input'
})

const characterCount = computed(() => {
  return String(props.modelValue || '').length
})

const baseClasses = "block w-full border-neutral-300 rounded-lg shadow-sm transition-all duration-200 ease-in-out focus:ring-2 focus:ring-offset-0 placeholder-neutral-400"

const sizeClasses = {
  sm: "px-3 py-2 text-sm",
  md: "px-4 py-3 text-base",
  lg: "px-5 py-4 text-lg"
}

const stateClasses = computed(() => {
  if (props.error) {
    return "border-error-300 text-error-900 placeholder-error-300 focus:border-error-500 focus:ring-error-500"
  }
  
  if (props.disabled) {
    return "bg-neutral-50 text-neutral-500 cursor-not-allowed"
  }
  
  if (isFocused.value) {
    return "border-primary-500 focus:border-primary-500 focus:ring-primary-500"
  }
  
  return "border-neutral-300 focus:border-primary-500 focus:ring-primary-500 hover:border-neutral-400"
})

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement | HTMLTextAreaElement
  let value: string | number = target.value
  
  if (props.type === 'number') {
    value = target.valueAsNumber || 0
  }
  
  emit('update:modelValue', value)
}

const handleFocus = (event: FocusEvent) => {
  isFocused.value = true
  emit('focus', event)
}

const handleBlur = (event: FocusEvent) => {
  isFocused.value = false
  emit('blur', event)
}
</script>