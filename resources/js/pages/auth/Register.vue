<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create your account
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="space-y-4">
          <Input
            v-model="userData.name"
            label="Full Name"
            required
          />
          <Input
            v-model="userData.email"
            type="email"
            label="Email address"
            required
          />
          <Input
            v-model="userData.password"
            type="password"
            label="Password"
            required
          />
        </div>

        <div>
          <Button type="submit" class="w-full" :loading="loading">
            Sign up
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue"
import { useRouter } from "vue-router"
import Button from "../../components/ui/Button.vue"
import Input from "../../components/forms/Input.vue"

const router = useRouter()

const loading = ref(false)
const userData = ref({
  name: "",
  email: "",
  password: ""
})

const handleRegister = async () => {
  loading.value = true
  try {
    // Registration logic here
    console.log("Registering user:", userData.value)
    await new Promise(resolve => setTimeout(resolve, 1000))
    router.push("/login")
  } catch (error) {
    console.error("Registration failed:", error)
  } finally {
    loading.value = false
  }
}
</script> 