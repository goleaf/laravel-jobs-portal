<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to your account
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm -space-y-px">
          <Input
            v-model="credentials.email"
            type="email"
            label="Email address"
            required
          />
          <Input
            v-model="credentials.password"
            type="password"
            label="Password"
            required
          />
        </div>

        <div>
          <Button type="submit" class="w-full" :loading="loading">
            Sign in
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useAuthStore } from "../../stores/auth"
import Button from "../../components/ui/Button.vue"
import Input from "../../components/forms/Input.vue"

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const credentials = ref({
  email: "",
  password: ""
})

const handleLogin = async () => {
  loading.value = true
  try {
    const result = await authStore.login(credentials.value)
    if (result.success) {
      router.push("/dashboard")
    }
  } catch (error) {
    console.error("Login failed:", error)
  } finally {
    loading.value = false
  }
}
</script> 