<template>
  <Teleport to="body">
    <Transition 
      enter-active-class="duration-300 ease-out" 
      enter-from-class="opacity-0" 
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in" 
      leave-from-class="opacity-100" 
      leave-to-class="opacity-0"
    >
      <div 
        v-if="modelValue" 
        class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none"
        role="dialog"
        aria-modal="true"
        @keydown.esc="closeModal"
      >
        <div 
          class="fixed inset-0 bg-black opacity-50" 
          @click="closeModal"
        ></div>
        
        <div 
          ref="modalRef"
          class="relative w-auto max-w-3xl mx-auto my-6 transition-all duration-300 ease-in-out"
          role="document"
        >
          <div 
            class="relative flex flex-col w-full bg-white border-0 rounded-lg shadow-lg outline-none focus:outline-none"
          >
            <!-- Modal Header -->
            <div 
              class="flex items-start justify-between p-5 border-b border-solid rounded-t border-blueGray-200"
            >
              <h3 class="text-3xl font-semibold">
                <slot name="header">{{ title }}</slot>
              </h3>
              <button
                class="float-right p-1 ml-auto text-3xl font-semibold leading-none text-black bg-transparent border-0 outline-none opacity-5 focus:outline-none"
                @click="closeModal"
              >
                <span class="block w-6 h-6 text-2xl text-black bg-transparent outline-none opacity-5 focus:outline-none">
                  ×
                </span>
              </button>
            </div>
            
            <!-- Modal Body -->
            <div class="relative flex-auto p-6">
              <slot>
                <p class="my-4 text-lg leading-relaxed text-blueGray-500">
                  {{ content }}
                </p>
              </slot>
            </div>
            
            <!-- Modal Footer -->
            <div 
              class="flex items-center justify-end p-6 border-t border-solid rounded-b border-blueGray-200"
            >
              <slot name="footer">
                <BaseButton 
                  variant="secondary" 
                  class="mr-4" 
                  @click="closeModal"
                >
                  Close
                </BaseButton>
                <BaseButton 
                  variant="primary" 
                  @click="$emit('confirm')"
                >
                  Confirm
                </BaseButton>
              </slot>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import BaseButton from './BaseButton.vue'

interface Props {
  modelValue: boolean
  title?: string
  content?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Modal',
  content: ''
})

const emit = defineEmits(['update:modelValue', 'confirm'])

const modalRef = ref<HTMLElement | null>(null)

const closeModal = () => {
  emit('update:modelValue', false)
}

// Trap focus within modal when open
watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    setTimeout(() => {
      const focusableElements = modalRef.value?.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      )
      if (focusableElements && focusableElements.length > 0) {
        (focusableElements[0] as HTMLElement).focus()
      }
    }, 100)
  }
})
</script> 