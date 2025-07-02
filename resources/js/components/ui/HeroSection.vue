<template>
  <section
    class="relative overflow-hidden"
    :class="backgroundClasses"
  >
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
      <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
        <defs>
          <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
          </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#grid)" />
      </svg>
    </div>

    <!-- Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 lg:py-32">
      <div :class="contentAlignment">
        <!-- Badge -->
        <div v-if="badge" class="mb-6">
          <span
            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
            :class="badgeClasses"
          >
            <component v-if="badgeIcon" :is="badgeIcon" class="w-4 h-4 mr-2" />
            {{ badge }}
          </span>
        </div>

        <!-- Main Headline -->
        <h1 :class="titleClasses">
          <slot name="title">
            <span v-html="title"></span>
          </slot>
        </h1>
        
        <!-- Subheadline -->
        <p v-if="subtitle" :class="subtitleClasses">
          <slot name="subtitle">
            {{ subtitle }}
          </slot>
        </p>

        <!-- Custom Content Slot -->
        <div v-if="$slots.content" class="mt-8">
          <slot name="content" />
        </div>

        <!-- Call to Action Buttons -->
        <div v-if="showActions" class="mt-8 flex flex-col sm:flex-row gap-4" :class="actionsAlignment">
          <BaseButton
            v-if="primaryAction"
            :variant="primaryAction.variant || 'primary'"
            :size="primaryAction.size || 'lg'"
            :to="primaryAction.to"
            :href="primaryAction.href"
            :tag="primaryAction.to ? 'router-link' : primaryAction.href ? 'a' : 'button'"
            @click="primaryAction.onClick"
            class="hover:scale-105 transition-transform duration-200"
          >
            <component v-if="primaryAction.icon" :is="primaryAction.icon" class="w-5 h-5 mr-2" />
            {{ primaryAction.text }}
          </BaseButton>

          <BaseButton
            v-if="secondaryAction"
            :variant="secondaryAction.variant || 'outline'"
            :size="secondaryAction.size || 'lg'"
            :to="secondaryAction.to"
            :href="secondaryAction.href"
            :tag="secondaryAction.to ? 'router-link' : secondaryAction.href ? 'a' : 'button'"
            @click="secondaryAction.onClick"
            class="hover:scale-105 transition-transform duration-200"
          >
            <component v-if="secondaryAction.icon" :is="secondaryAction.icon" class="w-5 h-5 mr-2" />
            {{ secondaryAction.text }}
          </BaseButton>
        </div>

        <!-- Statistics -->
        <div v-if="stats && stats.length > 0" class="mt-12">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 max-w-3xl" :class="statsAlignment">
            <div
              v-for="stat in stats"
              :key="stat.label"
              class="text-center"
            >
              <div class="text-3xl sm:text-4xl font-bold text-white mb-2">
                {{ typeof stat.value === 'number' ? stat.value.toLocaleString() : stat.value }}{{ stat.suffix || '' }}
              </div>
              <div class="text-indigo-200">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Optional Background Image Overlay -->
    <div
      v-if="backgroundImage"
      class="absolute inset-0 bg-black bg-opacity-50"
      :style="{ backgroundImage: `url(${backgroundImage})`, backgroundSize: 'cover', backgroundPosition: 'center' }"
    ></div>
  </section>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import BaseButton from './BaseButton.vue'

interface Action {
  text: string
  variant?: 'primary' | 'secondary' | 'outline' | 'outline-primary' | 'ghost'
  size?: 'sm' | 'md' | 'lg' | 'xl'
  to?: string | object
  href?: string
  icon?: any
  onClick?: () => void
}

interface Stat {
  value: string | number
  label: string
  suffix?: string
}

interface Props {
  title?: string
  subtitle?: string
  badge?: string
  badgeIcon?: any
  badgeVariant?: 'primary' | 'secondary' | 'success' | 'warning' | 'error'
  theme?: 'primary' | 'secondary' | 'dark' | 'light' | 'success' | 'warning'
  size?: 'sm' | 'md' | 'lg' | 'xl'
  alignment?: 'left' | 'center' | 'right'
  backgroundImage?: string
  primaryAction?: Action
  secondaryAction?: Action
  stats?: Stat[]
  showActions?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  theme: 'primary',
  size: 'lg',
  alignment: 'center',
  showActions: true
})

// Computed classes
const backgroundClasses = computed(() => {
  const themes = {
    primary: 'bg-gradient-to-br from-indigo-600 via-purple-600 to-blue-700',
    secondary: 'bg-gradient-to-br from-gray-600 via-gray-700 to-gray-800',
    dark: 'bg-gradient-to-br from-gray-900 via-gray-800 to-black',
    light: 'bg-gradient-to-br from-gray-50 via-white to-gray-100',
    success: 'bg-gradient-to-br from-green-600 via-emerald-600 to-teal-700',
    warning: 'bg-gradient-to-br from-yellow-500 via-orange-500 to-red-600'
  }
  return themes[props.theme]
})

const contentAlignment = computed(() => {
  const alignments = {
    left: 'text-left',
    center: 'text-center',
    right: 'text-right'
  }
  return alignments[props.alignment]
})

const actionsAlignment = computed(() => {
  const alignments = {
    left: 'justify-start',
    center: 'justify-center',
    right: 'justify-end'
  }
  return alignments[props.alignment]
})

const statsAlignment = computed(() => {
  const alignments = {
    left: 'mx-0',
    center: 'mx-auto',
    right: 'ml-auto'
  }
  return alignments[props.alignment]
})

const titleClasses = computed(() => {
  const sizes = {
    sm: 'text-2xl sm:text-3xl lg:text-4xl',
    md: 'text-3xl sm:text-4xl lg:text-5xl',
    lg: 'text-4xl sm:text-5xl lg:text-6xl',
    xl: 'text-5xl sm:text-6xl lg:text-7xl'
  }
  
  const textColor = props.theme === 'light' ? 'text-gray-900' : 'text-white'
  
  return `${sizes[props.size]} font-bold mb-6 ${textColor}`
})

const subtitleClasses = computed(() => {
  const sizes = {
    sm: 'text-lg sm:text-xl',
    md: 'text-xl sm:text-2xl',
    lg: 'text-xl sm:text-2xl',
    xl: 'text-2xl sm:text-3xl'
  }
  
  const textColor = props.theme === 'light' ? 'text-gray-600' : 'text-indigo-100'
  const maxWidth = props.alignment === 'center' ? 'max-w-3xl mx-auto' : 'max-w-3xl'
  
  return `${sizes[props.size]} ${textColor} mb-8 ${maxWidth}`
})

const badgeClasses = computed(() => {
  const variants = {
    primary: 'bg-indigo-100 text-indigo-800',
    secondary: 'bg-gray-100 text-gray-800',
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800',
    error: 'bg-red-100 text-red-800'
  }
  return variants[props.badgeVariant || 'primary']
})
</script> 