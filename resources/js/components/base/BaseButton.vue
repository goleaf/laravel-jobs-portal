<template>
  <component
    :is="tag"
    :type="tag === 'button' ? type : undefined"
    :href="tag === 'a' ? href : undefined"
    :to="tag === 'router-link' ? to : undefined"
    :disabled="disabled || loading"
    :class="buttonClasses"
    :aria-disabled="disabled || loading"
    @click="handleClick"
  >
    <span v-if="loading" class="button-loader">
      <svg
        class="animate-spin h-4 w-4"
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
    </span>

    <span v-if="icon && iconPosition === 'left'" :class="iconClasses">
      <component :is="icon" />
    </span>

    <span v-if="$slots.default" :class="textClasses">
      <slot />
    </span>

    <span v-if="icon && iconPosition === 'right'" :class="iconClasses">
      <component :is="icon" />
    </span>

    <span v-if="badge" :class="badgeClasses">
      {{ badge }}
    </span>
  </component>
</template>

<script setup lang="ts">
import { computed, type Component } from 'vue';
import type { RouteLocationRaw } from 'vue-router';

export type ButtonVariant = 
  | 'primary' 
  | 'secondary' 
  | 'success' 
  | 'danger' 
  | 'warning' 
  | 'info' 
  | 'light' 
  | 'dark' 
  | 'outline-primary'
  | 'outline-secondary'
  | 'outline-success'
  | 'outline-danger'
  | 'outline-warning'
  | 'outline-info'
  | 'ghost'
  | 'link';

export type ButtonSize = 'xs' | 'sm' | 'md' | 'lg' | 'xl';

export type ButtonType = 'button' | 'submit' | 'reset';

export type IconPosition = 'left' | 'right';

export interface Props {
  variant?: ButtonVariant;
  size?: ButtonSize;
  type?: ButtonType;
  disabled?: boolean;
  loading?: boolean;
  block?: boolean;
  rounded?: boolean;
  icon?: Component | string;
  iconPosition?: IconPosition;
  badge?: string | number;
  href?: string;
  to?: RouteLocationRaw;
  tag?: 'button' | 'a' | 'router-link';
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  type: 'button',
  disabled: false,
  loading: false,
  block: false,
  rounded: false,
  iconPosition: 'left',
  tag: 'button'
});

const emit = defineEmits<{
  click: [event: Event];
}>();

// Computed classes
const buttonClasses = computed(() => {
  const classes = [
    'relative',
    'inline-flex',
    'items-center',
    'justify-center',
    'font-medium',
    'transition-all',
    'duration-200',
    'focus:outline-none',
    'focus:ring-2',
    'focus:ring-offset-2',
    'disabled:opacity-50',
    'disabled:cursor-not-allowed',
    'disabled:pointer-events-none'
  ];

  // Size classes
  const sizeClasses = {
    xs: ['text-xs', 'px-2', 'py-1', 'min-h-[24px]'],
    sm: ['text-sm', 'px-3', 'py-1.5', 'min-h-[32px]'],
    md: ['text-sm', 'px-4', 'py-2', 'min-h-[40px]'],
    lg: ['text-base', 'px-6', 'py-3', 'min-h-[48px]'],
    xl: ['text-lg', 'px-8', 'py-4', 'min-h-[56px]']
  };

  // Variant classes
  const variantClasses = {
    primary: [
      'bg-indigo-600',
      'text-white',
      'border-transparent',
      'hover:bg-indigo-700',
      'focus:ring-indigo-500',
      'active:bg-indigo-800'
    ],
    secondary: [
      'bg-gray-600',
      'text-white',
      'border-transparent',
      'hover:bg-gray-700',
      'focus:ring-gray-500',
      'active:bg-gray-800'
    ],
    success: [
      'bg-green-600',
      'text-white',
      'border-transparent',
      'hover:bg-green-700',
      'focus:ring-green-500',
      'active:bg-green-800'
    ],
    danger: [
      'bg-red-600',
      'text-white',
      'border-transparent',
      'hover:bg-red-700',
      'focus:ring-red-500',
      'active:bg-red-800'
    ],
    warning: [
      'bg-yellow-500',
      'text-white',
      'border-transparent',
      'hover:bg-yellow-600',
      'focus:ring-yellow-400',
      'active:bg-yellow-700'
    ],
    info: [
      'bg-blue-600',
      'text-white',
      'border-transparent',
      'hover:bg-blue-700',
      'focus:ring-blue-500',
      'active:bg-blue-800'
    ],
    light: [
      'bg-gray-100',
      'text-gray-800',
      'border-gray-300',
      'hover:bg-gray-200',
      'focus:ring-gray-500',
      'active:bg-gray-300'
    ],
    dark: [
      'bg-gray-800',
      'text-white',
      'border-transparent',
      'hover:bg-gray-900',
      'focus:ring-gray-700',
      'active:bg-gray-900'
    ],
    'outline-primary': [
      'bg-transparent',
      'text-indigo-600',
      'border-indigo-600',
      'border-2',
      'hover:bg-indigo-50',
      'focus:ring-indigo-500',
      'active:bg-indigo-100'
    ],
    'outline-secondary': [
      'bg-transparent',
      'text-gray-600',
      'border-gray-600',
      'border-2',
      'hover:bg-gray-50',
      'focus:ring-gray-500',
      'active:bg-gray-100'
    ],
    'outline-success': [
      'bg-transparent',
      'text-green-600',
      'border-green-600',
      'border-2',
      'hover:bg-green-50',
      'focus:ring-green-500',
      'active:bg-green-100'
    ],
    'outline-danger': [
      'bg-transparent',
      'text-red-600',
      'border-red-600',
      'border-2',
      'hover:bg-red-50',
      'focus:ring-red-500',
      'active:bg-red-100'
    ],
    'outline-warning': [
      'bg-transparent',
      'text-yellow-600',
      'border-yellow-600',
      'border-2',
      'hover:bg-yellow-50',
      'focus:ring-yellow-500',
      'active:bg-yellow-100'
    ],
    'outline-info': [
      'bg-transparent',
      'text-blue-600',
      'border-blue-600',
      'border-2',
      'hover:bg-blue-50',
      'focus:ring-blue-500',
      'active:bg-blue-100'
    ],
    ghost: [
      'bg-transparent',
      'text-gray-600',
      'border-transparent',
      'hover:bg-gray-100',
      'focus:ring-gray-500',
      'active:bg-gray-200'
    ],
    link: [
      'bg-transparent',
      'text-indigo-600',
      'border-transparent',
      'hover:text-indigo-800',
      'focus:ring-indigo-500',
      'active:text-indigo-900',
      'underline',
      'decoration-2',
      'underline-offset-2'
    ]
  };

  classes.push(...sizeClasses[props.size]);
  classes.push(...variantClasses[props.variant]);

  // Border radius
  if (props.rounded) {
    classes.push('rounded-full');
  } else {
    classes.push('rounded-md');
  }

  // Block width
  if (props.block) {
    classes.push('w-full');
  }

  // Loading state
  if (props.loading) {
    classes.push('pointer-events-none');
  }

  return classes;
});

const iconClasses = computed(() => {
  const classes = ['flex', 'items-center'];
  
  if (props.$slots?.default && props.icon) {
    if (props.iconPosition === 'left') {
      classes.push('mr-2');
    } else {
      classes.push('ml-2');
    }
  }

  return classes;
});

const textClasses = computed(() => {
  const classes = [];
  
  if (props.loading) {
    classes.push('opacity-0');
  }

  return classes;
});

const badgeClasses = computed(() => [
  'absolute',
  '-top-1',
  '-right-1',
  'inline-flex',
  'items-center',
  'justify-center',
  'px-1.5',
  'py-0.5',
  'text-xs',
  'font-medium',
  'leading-none',
  'text-white',
  'bg-red-500',
  'rounded-full',
  'transform',
  'translate-x-1/2',
  '-translate-y-1/2'
]);

// Event handlers
const handleClick = (event: Event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event);
  }
};
</script>

<style scoped>
.button-loader {
  @apply absolute inset-0 flex items-center justify-center;
}

/* Ripple effect on click */
@keyframes ripple {
  0% {
    transform: scale(0);
    opacity: 1;
  }
  100% {
    transform: scale(4);
    opacity: 0;
  }
}

button:active::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 4px;
  height: 4px;
  background: rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  animation: ripple 0.6s ease-out;
}

/* Focus styles for accessibility */
button:focus-visible {
  @apply ring-2 ring-offset-2 ring-opacity-50;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .light {
    @apply bg-gray-700 text-gray-200 border-gray-600 hover:bg-gray-600;
  }
  
  .ghost {
    @apply text-gray-300 hover:bg-gray-800;
  }
}
</style> 