<template>
  <div 
    :class="[
      'bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300 ease-in-out',
      {
        'hover:shadow-lg': hoverable,
        'border border-gray-200': bordered,
        'p-4': !nopadding,
        'cursor-pointer': clickable
      }
    ]"
    @click="handleClick"
  >
    <!-- Header Slot -->
    <div 
      v-if="$slots.header" 
      class="card-header border-b border-gray-200 px-4 py-3"
    >
      <slot name="header"></slot>
    </div>

    <!-- Main Content Slot -->
    <div 
      :class="[
        'card-body',
        { 'p-4': !nopadding }
      ]"
    >
      <slot></slot>
    </div>

    <!-- Footer Slot -->
    <div 
      v-if="$slots.footer" 
      class="card-footer border-t border-gray-200 px-4 py-3"
    >
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  hoverable?: boolean
  bordered?: boolean
  nopadding?: boolean
  clickable?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  hoverable: false,
  bordered: false,
  nopadding: false,
  clickable: false
})

const emit = defineEmits(['click'])

const handleClick = () => {
  if (props.clickable) {
    emit('click')
  }
}
</script>

<style scoped>
.card-body {
  @apply flex flex-col justify-between;
}
</style> 