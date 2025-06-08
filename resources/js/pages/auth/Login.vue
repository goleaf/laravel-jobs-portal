<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
      <!-- Login Info Block -->
      <LoginInfoBlock 
        :auto-fill="true"
        @credentials-selected="handleCredentialsSelected"
        @login-attempt="handleQuickLogin"
      />
      
      <!-- Login Form -->
      <div class="bg-white shadow-xl rounded-lg p-8">
        <div>
          <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Sign in to your account
          </h2>
          <p class="mt-2 text-center text-sm text-gray-600">
            Enter your credentials to access the job portal
          </p>
        </div>
        <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
          <div class="rounded-md shadow-sm space-y-4">
            <Input
              v-model="credentials.email"
              type="email"
              label="Email address"
              placeholder="Enter your email"
              required
            />
            <Input
              v-model="credentials.password"
              type="password"
              label="Password"
              placeholder="Enter your password"
              required
            />
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="credentials.remember"
                name="remember-me"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              >
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                Remember me
              </label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                Forgot your password?
              </a>
            </div>
          </div>

          <div>
            <Button 
              type="submit" 
              class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors" 
              :loading="loading"
            >
              <span v-if="!loading">Sign in</span>
              <span v-else class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Signing in...
              </span>
            </Button>
          </div>

          <div class="text-center">
            <span class="text-sm text-gray-600">
              Don't have an account? 
              <router-link to="/register" class="font-medium text-blue-600 hover:text-blue-500">
                Register here
              </router-link>
            </span>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue"
import { useRouter } from "vue-router"
import { useAuthStore } from "../../stores/auth"
import Button from "../../components/ui/Button.vue"
import Input from "../../components/forms/Input.vue"
import LoginInfoBlock from "../../components/auth/LoginInfoBlock.vue"

// Types
interface DemoCredential {
  role: string
  email: string
  password: string
  description: string
}

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const credentials = ref({
  email: "",
  password: "",
  remember: false
})

const handleLogin = async () => {
  loading.value = true
  try {
    const result = await authStore.login({
      email: credentials.value.email,
      password: credentials.value.password,
      remember: credentials.value.remember
    })
    
    if (result.success) {
      // Redirect to appropriate dashboard based on user role
      const user = result.data?.user
      if (user?.user_type === 'admin' || user?.roles?.includes('admin')) {
        router.push("/admin")
      } else {
        router.push("/dashboard")
      }
    }
  } catch (error) {
    console.error("Login failed:", error)
    // Handle error (show notification, etc.)
  } finally {
    loading.value = false
  }
}

const handleCredentialsSelected = (credential: DemoCredential) => {
  // Auto-fill the form when credentials are selected from LoginInfoBlock
  credentials.value.email = credential.email
  credentials.value.password = credential.password
  
  // Optional: Auto-submit the form
  // handleLogin()
}

const handleQuickLogin = async (credential: DemoCredential) => {
  // Handle quick login attempt from LoginInfoBlock
  credentials.value.email = credential.email
  credentials.value.password = credential.password
  
  // Automatically attempt login
  await handleLogin()
}
</script> 