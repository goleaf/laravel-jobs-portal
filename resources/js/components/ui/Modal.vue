<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="closeModal">
      <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
        <div class="flex justify-between items-center p-6 border-b">
          <h3 class="text-lg font-semibold">{{ title }}</h3>
          <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <div class="p-6">
          <slot></slot>
        </div>
        <div v-if="$slots.footer" class="p-6 border-t bg-gray-50 rounded-b-lg">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
interface Props {
  show: boolean
  title: string
}

defineProps<Props>()

const emit = defineEmits<{
  close: []
}>()

const closeModal = () => {
  emit("close")
}

// Close on Escape key
import { onMounted, onUnmounted } from "vue"

const handleEscape = (e: KeyboardEvent) => {
  if (e.key === "Escape") {
    closeModal()
  }
}

onMounted(() => {
  document.addEventListener("keydown", handleEscape)
})

onUnmounted(() => {
  document.removeEventListener("keydown", handleEscape)
})
</script>