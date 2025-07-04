<template>
  <div class="flex items-center">
    <input
      :id="id"
      type="checkbox"
      :name="name"
      :checked="modelValue"
      :disabled="disabled"
      :required="required"
      :class="[
        'h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all duration-300 ease-in-out',
        {
          'opacity-50 cursor-not-allowed': disabled,
          'border-red-500 focus:ring-red-500': error
        }
      ]"
      @change="handleChange"
    />
    <label 
      :for="id" 
      :class="[
        'ml-2 block text-sm text-gray-900 select-none',
        { 'opacity-50 cursor-not-allowed': disabled }
      ]"
    >
      {{ label }}
    </label>
  </div>
  
  <p 
    v-if="error" 
    class="mt-2 text-sm text-red-600"
  >
    {{ error }}
  </p>
</template>

<script setup lang="ts">
interface Props {
  modelValue: boolean
  label?: string
  name?: string
  id?: string
  disabled?: boolean
  required?: boolean
  error?: string
}

const props = withDefaults(defineProps<Props>(), {
  label: '',
  name: '',
  id: '',
  disabled: false,
  required: false,
  error: ''
})

const emit = defineEmits(['update:modelValue', 'change'])

const handleChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.checked)
  emit('change', target.checked)
}
</script>

<style scoped>
/* Custom focus ring for checkbox */
input[type="checkbox"]:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.5);
}

/* Custom checked state */
input[type="checkbox"]:checked {
  background-color: theme('colors.blue.600');
  border-color: theme('colors.blue.600');
}
</style> 