<template>
  <div class="flex min-h-screen items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
          {{ $t('auth.forgot_password_title') }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          {{ $t('auth.forgot_password_subtitle') }}
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="onSubmit">
        <BaseInput
          v-model="email"
          :label="$t('auth.email_label')"
          name="email"
          id="email"
          type="email"
          :placeholder="$t('auth.email_placeholder')"
          :error="emailError"
          required
        />
        <BaseButton
          type="submit"
          variant="primary"
          class="w-full"
        >
          {{ $t('auth.send_reset_link') }}
        </BaseButton>
      </form>
      <div v-if="successMessage" class="mt-4 text-green-600 text-center">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="mt-4 text-red-600 text-center">
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import BaseInput from '@/components/BaseInput.vue'
import BaseButton from '@/components/BaseButton.vue'

const email = ref('')
const emailError = ref('')
const successMessage = ref('')
const errorMessage = ref('')

const onSubmit = () => {
  emailError.value = ''
  successMessage.value = ''
  errorMessage.value = ''

  if (!email.value) {
    emailError.value = $t('auth.email_required')
    return
  }
  // Simulate API call
  setTimeout(() => {
    successMessage.value = $t('auth.reset_link_sent')
  }, 1000)
}
</script> 