<template>
  <div class="relative">
    <!-- Label -->
    <label
      v-if="label"
      :for="inputId"
      class="block text-sm font-medium text-gray-700 mb-1"
      :class="{ 'text-red-700': hasError }"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>

    <!-- Input Container -->
    <div class="relative">
      <!-- Left Icon -->
      <div
        v-if="leftIcon"
        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
      >
        <component :is="leftIcon" class="h-5 w-5 text-gray-400" />
      </div>

      <!-- Input Element -->
      <input
        :id="inputId"
        ref="inputRef"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        :autocomplete="autocomplete"
        :class="inputClasses"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
        @keydown="handleKeydown"
      />

      <!-- Right Icon/Action -->
      <div
        v-if="rightIcon || showClearButton"
        class="absolute inset-y-0 right-0 pr-3 flex items-center"
      >
        <!-- Clear Button -->
        <button
          v-if="showClearButton && modelValue"
          type="button"
          @click="clearInput"
          class="text-gray-400 hover:text-gray-600 focus:outline-none"
        >
          <XMarkIcon class="h-4 w-4" />
        </button>
        
        <!-- Right Icon -->
        <component
          v-else-if="rightIcon"
          :is="rightIcon"
          class="h-5 w-5 text-gray-400"
        />
      </div>

      <!-- Password Toggle -->
      <button
        v-if="type === 'password' && showPasswordToggle"
        type="button"
        @click="togglePasswordVisibility"
        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
      >
        <EyeIcon v-if="!showPassword" class="h-5 w-5" />
        <EyeSlashIcon v-else class="h-5 w-5" />
      </button>
    </div>

    <!-- Helper Text -->
    <p
      v-if="helperText && !hasError"
      class="mt-1 text-sm text-gray-500"
    >
      {{ helperText }}
    </p>

    <!-- Error Message -->
    <p
      v-if="hasError"
      class="mt-1 text-sm text-red-600"
    >
      {{ errorMessage }}
    </p>

    <!-- Character Count -->
    <p
      v-if="showCharacterCount && maxLength"
      class="mt-1 text-xs text-gray-500 text-right"
    >
      {{ characterCount }}/{{ maxLength }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue'
import { XMarkIcon, EyeIcon, EyeSlashIcon } from '@heroicons/vue/24/outline'

interface Props {
  modelValue?: string | number
  type?: 'text' | 'email' | 'password' | 'number' | 'tel' | 'url' | 'search'
  label?: string
  placeholder?: string
  helperText?: string
  errorMessage?: string
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  size?: 'sm' | 'md' | 'lg'
  variant?: 'default' | 'filled' | 'outlined'
  leftIcon?: any
  rightIcon?: any
  showClearButton?: boolean
  showPasswordToggle?: boolean
  showCharacterCount?: boolean
  maxLength?: number
  min?: number
  max?: number
  step?: number
  autocomplete?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  size: 'md',
  variant: 'default',
  showClearButton: false,
  showPasswordToggle: true,
  showCharacterCount: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  focus: [event: FocusEvent]
  blur: [event: FocusEvent]
  keydown: [event: KeyboardEvent]
}>()

const inputRef = ref<HTMLInputElement>()
const showPassword = ref(false)
const isFocused = ref(false)

const inputId = computed(() => `input-${Math.random().toString(36).substr(2, 9)}`)

const hasError = computed(() => !!props.errorMessage)

const characterCount = computed(() => {
  return String(props.modelValue || '').length
})

const baseClasses = 'block w-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-0'

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-3 py-2.5 text-sm',
    lg: 'px-4 py-3 text-base'
  }
  return sizes[props.size]
})

const variantClasses = computed(() => {
  const variants = {
    default: 'border border-gray-300 rounded-md bg-white focus:ring-indigo-500 focus:border-indigo-500',
    filled: 'border-0 bg-gray-100 rounded-md focus:ring-indigo-500 focus:bg-white',
    outlined: 'border-2 border-gray-300 rounded-md bg-transparent focus:ring-indigo-500 focus:border-indigo-500'
  }
  return variants[props.variant]
})

const stateClasses = computed(() => {
  if (hasError.value) {
    return 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500'
  }
  
  if (props.disabled) {
    return 'bg-gray-50 text-gray-500 cursor-not-allowed'
  }
  
  return 'text-gray-900 placeholder-gray-500'
})

const paddingClasses = computed(() => {
  let leftPadding = ''
  let rightPadding = ''

  if (props.leftIcon) {
    leftPadding = props.size === 'lg' ? 'pl-10' : 'pl-9'
  }

  if (props.rightIcon || props.showClearButton || (props.type === 'password' && props.showPasswordToggle)) {
    rightPadding = props.size === 'lg' ? 'pr-10' : 'pr-9'
  }

  return `${leftPadding} ${rightPadding}`
})

const inputClasses = computed(() => {
  return [
    baseClasses,
    sizeClasses.value,
    variantClasses.value,
    stateClasses.value,
    paddingClasses.value
  ].join(' ')
})

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}

const handleFocus = (event: FocusEvent) => {
  isFocused.value = true
  emit('focus', event)
}

const handleBlur = (event: FocusEvent) => {
  isFocused.value = false
  emit('blur', event)
}

const handleKeydown = (event: KeyboardEvent) => {
  emit('keydown', event)
}

const clearInput = () => {
  emit('update:modelValue', '')
  nextTick(() => {
    inputRef.value?.focus()
  })
}

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
  const input = inputRef.value
  if (input) {
    input.type = showPassword.value ? 'text' : 'password'
  }
}

const focus = () => {
  inputRef.value?.focus()
}

const blur = () => {
  inputRef.value?.blur()
}

defineExpose({ focus, blur })
</script> 