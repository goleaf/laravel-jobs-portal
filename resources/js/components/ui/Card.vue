<template>
  <div 
    :class="[
      baseClasses,
      variantClasses[variant],
      sizeClasses[size],
      {
        'hover:shadow-strong hover:-translate-y-1': hoverable,
        'cursor-pointer': clickable,
      }
    ]"
    @click="handleClick"
  >
    <!-- Header -->
    <div v-if="$slots.header || title" class="card-header">
      <slot name="header">
        <h3 v-if="title" class="text-lg font-semibold text-neutral-900">{{ title }}</h3>
      </slot>
    </div>

    <!-- Content -->
    <div class="card-content">
      <slot />
    </div>

    <!-- Footer -->
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer" />
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  variant?: 'default' | 'elevated' | 'outlined' | 'glass' | 'gradient'
  size?: 'sm' | 'md' | 'lg' | 'xl'
  hoverable?: boolean
  clickable?: boolean
  title?: string
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'md',
  hoverable: false,
  clickable: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const baseClasses = "rounded-xl transition-all duration-200 ease-in-out"

const variantClasses = {
  default: "bg-white border border-neutral-200 shadow-soft",
  elevated: "bg-white shadow-strong",
  outlined: "bg-white border-2 border-neutral-300",
  glass: "glass border border-white/20",
  gradient: "bg-gradient-to-br from-white to-neutral-50 border border-neutral-200 shadow-soft"
}

const sizeClasses = {
  sm: "p-4 space-y-3",
  md: "p-6 space-y-4", 
  lg: "p-8 space-y-6",
  xl: "p-10 space-y-8"
}

const handleClick = (event: MouseEvent) => {
  if (props.clickable) {
    emit('click', event)
  }
}
</script>

<style scoped>
.card-header {
  @apply pb-3 border-b border-neutral-200;
}

.card-content {
  @apply flex-1;
}

.card-footer {
  @apply pt-3 border-t border-neutral-200;
}
</style>