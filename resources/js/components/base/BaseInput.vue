<template>
  <div :class="wrapperClasses">
    <label v-if="label" :for="inputId" :class="labelClasses">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1" aria-label="required">*</span>
    </label>

    <div class="relative">
      <!-- Left Icon -->
      <div v-if="leftIcon" :class="leftIconClasses">
        <component :is="leftIcon" :class="iconSizeClasses" />
      </div>

      <!-- Input Element -->
      <component
        :is="inputComponent"
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        :maxlength="maxlength"
        :rows="rows"
        :cols="cols"
        :class="inputClasses"
        :aria-invalid="hasError"
        :aria-describedby="describedBy"
        @input="handleInput"
        @change="handleChange"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
      />

      <!-- Right Icon / Clear Button -->
      <div v-if="rightIcon || (clearable && modelValue)" :class="rightIconClasses">
        <button
          v-if="clearable && modelValue"
          type="button"
          :class="clearButtonClasses"
          @click="clearInput"
          :aria-label="clearLabel"
        >
          <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <component v-else-if="rightIcon" :is="rightIcon" :class="iconSizeClasses" />
      </div>

      <!-- Loading Spinner -->
      <div v-if="loading" :class="loadingClasses">
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </div>
    </div>

    <!-- Helper Text / Error Messages -->
    <div v-if="helpText || errorMessage" :class="messageClasses">
      <p v-if="errorMessage" :id="`${inputId}-error`" class="text-red-600 text-sm">
        {{ errorMessage }}
      </p>
      <p v-else-if="helpText" :id="`${inputId}-help`" class="text-gray-500 text-sm">
        {{ helpText }}
      </p>
    </div>

    <!-- Character Count -->
    <div v-if="showCharCount && maxlength" :class="charCountClasses">
      <span :class="charCountTextClasses">
        {{ characterCount }}/{{ maxlength }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref, nextTick, type Component } from 'vue';

export type InputType = 
  | 'text' 
  | 'password' 
  | 'email' 
  | 'number' 
  | 'tel' 
  | 'url' 
  | 'search' 
  | 'date' 
  | 'datetime-local' 
  | 'time' 
  | 'month' 
  | 'week' 
  | 'color'
  | 'file'
  | 'range'
  | 'hidden';

export type InputSize = 'sm' | 'md' | 'lg';

export type InputVariant = 'default' | 'borderless' | 'filled';

export interface Props {
  modelValue?: string | number;
  type?: InputType;
  label?: string;
  placeholder?: string;
  helpText?: string;
  errorMessage?: string;
  size?: InputSize;
  variant?: InputVariant;
  disabled?: boolean;
  readonly?: boolean;
  required?: boolean;
  clearable?: boolean;
  loading?: boolean;
  leftIcon?: Component | string;
  rightIcon?: Component | string;
  min?: number | string;
  max?: number | string;
  step?: number | string;
  maxlength?: number;
  showCharCount?: boolean;
  rows?: number;
  cols?: number;
  id?: string;
  clearLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  size: 'md',
  variant: 'default',
  disabled: false,
  readonly: false,
  required: false,
  clearable: false,
  loading: false,
  showCharCount: false,
  rows: 3,
  clearLabel: 'Clear input'
});

const emit = defineEmits<{
  'update:modelValue': [value: string | number];
  input: [event: Event];
  change: [event: Event];
  focus: [event: FocusEvent];
  blur: [event: FocusEvent];
  clear: [];
  keydown: [event: KeyboardEvent];
}>();

// Generate unique ID
const inputId = computed(() => props.id || `input-${Math.random().toString(36).substr(2, 9)}`);

// Determine input component (input vs textarea)
const inputComponent = computed(() => {
  return props.type === 'textarea' ? 'textarea' : 'input';
});

// State
const isFocused = ref(false);

// Computed properties
const hasError = computed(() => !!props.errorMessage);
const hasValue = computed(() => props.modelValue !== null && props.modelValue !== undefined && props.modelValue !== '');
const characterCount = computed(() => String(props.modelValue || '').length);

const describedBy = computed(() => {
  const ids = [];
  if (props.errorMessage) ids.push(`${inputId.value}-error`);
  if (props.helpText) ids.push(`${inputId.value}-help`);
  return ids.length > 0 ? ids.join(' ') : undefined;
});

// Style classes
const wrapperClasses = computed(() => [
  'w-full'
]);

const labelClasses = computed(() => [
  'block',
  'text-sm',
  'font-medium',
  'mb-1',
  props.disabled ? 'text-gray-400' : hasError.value ? 'text-red-700' : 'text-gray-700',
  'dark:text-gray-300'
]);

const inputClasses = computed(() => {
  const classes = [
    'block',
    'w-full',
    'border',
    'rounded-md',
    'transition-colors',
    'duration-200',
    'focus:outline-none',
    'focus:ring-2',
    'focus:ring-offset-0',
    'disabled:opacity-50',
    'disabled:cursor-not-allowed',
    'dark:bg-gray-800',
    'dark:border-gray-600',
    'dark:text-white'
  ];

  // Size classes
  const sizeClasses = {
    sm: ['text-sm', 'py-1.5', 'px-3'],
    md: ['text-sm', 'py-2', 'px-3'],
    lg: ['text-base', 'py-3', 'px-4']
  };

  // Variant classes
  const variantClasses = {
    default: [
      hasError.value ? 'border-red-300' : 'border-gray-300',
      hasError.value ? 'focus:ring-red-500 focus:border-red-500' : 'focus:ring-indigo-500 focus:border-indigo-500',
      'bg-white'
    ],
    borderless: [
      'border-transparent',
      'focus:ring-indigo-500',
      'bg-gray-50',
      'dark:bg-gray-700'
    ],
    filled: [
      hasError.value ? 'border-red-300' : 'border-gray-200',
      hasError.value ? 'focus:ring-red-500 focus:border-red-500' : 'focus:ring-indigo-500 focus:border-indigo-500',
      'bg-gray-50',
      'dark:bg-gray-700'
    ]
  };

  classes.push(...sizeClasses[props.size]);
  classes.push(...variantClasses[props.variant]);

  // Icon padding
  if (props.leftIcon) {
    classes.push('pl-10');
  }
  if (props.rightIcon || props.clearable || props.loading) {
    classes.push('pr-10');
  }

  return classes;
});

const leftIconClasses = computed(() => [
  'absolute',
  'inset-y-0',
  'left-0',
  'pl-3',
  'flex',
  'items-center',
  'pointer-events-none',
  props.disabled ? 'text-gray-400' : hasError.value ? 'text-red-400' : 'text-gray-400'
]);

const rightIconClasses = computed(() => [
  'absolute',
  'inset-y-0',
  'right-0',
  'pr-3',
  'flex',
  'items-center'
]);

const loadingClasses = computed(() => [
  'absolute',
  'inset-y-0',
  'right-0',
  'pr-3',
  'flex',
  'items-center',
  'pointer-events-none',
  'text-gray-400'
]);

const clearButtonClasses = computed(() => [
  'text-gray-400',
  'hover:text-gray-600',
  'focus:outline-none',
  'focus:text-gray-600',
  'transition-colors',
  'duration-200'
]);

const iconSizeClasses = computed(() => {
  const sizeMap = {
    sm: 'h-4 w-4',
    md: 'h-5 w-5',
    lg: 'h-6 w-6'
  };
  return sizeMap[props.size];
});

const messageClasses = computed(() => [
  'mt-1'
]);

const charCountClasses = computed(() => [
  'mt-1',
  'flex',
  'justify-end'
]);

const charCountTextClasses = computed(() => [
  'text-xs',
  props.maxlength && characterCount.value > props.maxlength 
    ? 'text-red-600' 
    : 'text-gray-500'
]);

// Event handlers
const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement | HTMLTextAreaElement;
  let value: string | number = target.value;

  // Convert to number for number inputs
  if (props.type === 'number' && value !== '') {
    value = parseFloat(value);
  }

  emit('update:modelValue', value);
  emit('input', event);
};

const handleChange = (event: Event) => {
  emit('change', event);
};

const handleFocus = (event: FocusEvent) => {
  isFocused.value = true;
  emit('focus', event);
};

const handleBlur = (event: FocusEvent) => {
  isFocused.value = false;
  emit('blur', event);
};

const handleKeydown = (event: KeyboardEvent) => {
  emit('keydown', event);
  
  // Handle escape key to clear clearable inputs
  if (event.key === 'Escape' && props.clearable && hasValue.value) {
    clearInput();
  }
};

const clearInput = () => {
  emit('update:modelValue', '');
  emit('clear');
  
  // Focus back to input after clearing
  nextTick(() => {
    const input = document.getElementById(inputId.value);
    if (input) {
      input.focus();
    }
  });
};

// Expose methods for parent components
defineExpose({
  focus: () => {
    const input = document.getElementById(inputId.value);
    if (input) {
      input.focus();
    }
  },
  blur: () => {
    const input = document.getElementById(inputId.value);
    if (input) {
      input.blur();
    }
  },
  clear: clearInput
});
</script>

<style scoped>
/* Custom styles for different input types */
input[type="range"] {
  @apply h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer;
}

input[type="range"]::-webkit-slider-thumb {
  @apply appearance-none h-4 w-4 bg-indigo-600 rounded-full cursor-pointer;
}

input[type="range"]::-moz-range-thumb {
  @apply h-4 w-4 bg-indigo-600 rounded-full cursor-pointer border-0;
}

input[type="color"] {
  @apply h-10 w-20 rounded border border-gray-300 cursor-pointer;
}

input[type="file"] {
  @apply file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100;
}

/* Dark mode for file input */
@media (prefers-color-scheme: dark) {
  input[type="file"] {
    @apply file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600;
  }
}

/* Focus within for better accessibility */
.focus-within\:ring-2:focus-within {
  @apply ring-2 ring-indigo-500;
}

/* Textarea resize handle styling */
textarea {
  resize: vertical;
  min-height: 80px;
}

/* Number input spinner buttons */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  @apply appearance-none m-0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style> 