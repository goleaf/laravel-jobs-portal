<template>
  <component
    :is="as"
    :class="[
      baseClasses,
      sizeClasses[size],
      variantClasses[variant],
      {
        'opacity-50 cursor-not-allowed': disabled,
        'animate-pulse': loading,
        'w-full': fullWidth,
        'rounded-full': rounded === 'full',
        'rounded-none': rounded === 'none',
      }
    ]"
    :disabled="disabled || loading"
    :href="as === 'a' ? href : undefined"
    :to="as === 'router-link' ? to : undefined"
    v-bind="$attrs"
    @click="handleClick"
  >
    <!-- Loading Spinner -->
    <div v-if="loading" class="animate-spin rounded-full border-2 border-current border-t-transparent mr-2"
         :class="sizeClasses[size].includes('text-sm') ? 'h-4 w-4' : 'h-5 w-5'">
    </div>
    
    <!-- Leading Icon -->
    <slot name="leading-icon" v-if="!loading" />
    
    <!-- Button Content -->
    <span class="relative">
      <slot />
    </span>
    
    <!-- Trailing Icon -->
    <slot name="trailing-icon" v-if="!loading" />
  </component>
</template>

<script setup lang="ts">
interface Props {
  variant?: "primary" | "secondary" | "success" | "warning" | "error" | "outline" | "ghost" | "link" | "gradient"
  size?: "xs" | "sm" | "md" | "lg" | "xl"
  disabled?: boolean
  loading?: boolean
  fullWidth?: boolean
  rounded?: "default" | "full" | "none"
  as?: "button" | "a" | "router-link"
  href?: string
  to?: string | object
}

const props = withDefaults(defineProps<Props>(), {
  variant: "primary",
  size: "md",
  disabled: false,
  loading: false,
  fullWidth: false,
  rounded: "default",
  as: "button"
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const baseClasses = "inline-flex items-center justify-center font-medium transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 active:scale-95 relative overflow-hidden group"

const sizeClasses = {
  xs: "px-2.5 py-1.5 text-xs rounded-md gap-1",
  sm: "px-3 py-2 text-sm rounded-md gap-1.5",
  md: "px-4 py-2.5 text-sm rounded-lg gap-2",
  lg: "px-6 py-3 text-base rounded-lg gap-2",
  xl: "px-8 py-4 text-lg rounded-xl gap-3"
}

const variantClasses = {
  primary: "bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500 shadow-md hover:shadow-lg",
  secondary: "bg-secondary-600 text-white hover:bg-secondary-700 focus:ring-secondary-500 shadow-md hover:shadow-lg",
  success: "bg-success-600 text-white hover:bg-success-700 focus:ring-success-500 shadow-md hover:shadow-lg",
  warning: "bg-warning-600 text-white hover:bg-warning-700 focus:ring-warning-500 shadow-md hover:shadow-lg",
  error: "bg-error-600 text-white hover:bg-error-700 focus:ring-error-500 shadow-md hover:shadow-lg",
  outline: "border-2 border-neutral-300 bg-white text-neutral-700 hover:bg-neutral-50 hover:border-neutral-400 focus:ring-neutral-500 active:bg-neutral-100",
  ghost: "text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900 focus:ring-neutral-500 active:bg-neutral-200",
  link: "text-primary-600 hover:text-primary-700 underline-offset-4 hover:underline focus:ring-primary-500 p-0",
  gradient: "bg-gradient-to-r from-primary-600 to-purple-600 text-white hover:from-primary-700 hover:to-purple-700 focus:ring-primary-500 shadow-md hover:shadow-lg"
}

const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit("click", event)
  }
}
</script>

<style scoped>
/* Custom button effects */
.group:hover::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  transition: left 0.5s;
}

.group:hover::before {
  left: 100%;
}
</style>