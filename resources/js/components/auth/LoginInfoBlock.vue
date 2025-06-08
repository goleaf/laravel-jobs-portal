<template>
  <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 rounded-lg p-6 mb-6 shadow-lg">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <div class="bg-blue-500 p-2 rounded-full">
                        <CircleStackIcon class="h-6 w-6 text-white" />
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-800">Demo Login Information</h3>
          <p class="text-sm text-gray-600">Quick access for testing and development</p>
        </div>
      </div>
      
      <!-- Database Status -->
      <div class="flex items-center space-x-2">
        <div 
          :class="[
            'w-3 h-3 rounded-full',
            databaseConnected ? 'bg-green-500 animate-pulse' : 'bg-red-500'
          ]"
        ></div>
        <span 
          :class="[
            'text-sm font-medium',
            databaseConnected ? 'text-green-700' : 'text-red-700'
          ]"
        >
          {{ databaseConnected ? 'Database Connected' : 'Database Error' }}
        </span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-8">
      <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-blue-600 bg-blue-100">
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading login information...
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
      <div class="flex items-center">
        <ExclamationTriangleIcon class="h-5 w-5 text-red-500 mr-2" />
        <span class="text-red-700 font-medium">{{ error }}</span>
      </div>
      <p class="text-red-600 text-sm mt-1">Using fallback demo credentials</p>
    </div>

    <!-- Demo Credentials Section -->
    <div v-if="!loading" class="space-y-4">
      <!-- Quick Login Buttons -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <button
          v-for="credential in demoCredentials"
          :key="credential.role"
          @click="quickLogin(credential)"
          :disabled="loggingIn"
          :class="[
            'relative overflow-hidden group transition-all duration-300 transform hover:scale-105',
            'flex items-center justify-between p-4 rounded-lg border-2 font-medium',
            'focus:outline-none focus:ring-4 focus:ring-opacity-50',
            credential.role === 'admin' 
              ? 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white border-red-600 focus:ring-red-500'
              : credential.role === 'employer'
              ? 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white border-blue-600 focus:ring-blue-500'
              : 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white border-green-600 focus:ring-green-500',
            loggingIn ? 'opacity-75 cursor-not-allowed' : 'cursor-pointer'
          ]"
        >
          <div class="flex items-center space-x-3">
            <UserIcon class="h-5 w-5" />
            <div class="text-left">
              <div class="font-semibold">{{ credential.role.charAt(0).toUpperCase() + credential.role.slice(1) }}</div>
              <div class="text-xs opacity-90">{{ credential.description }}</div>
            </div>
          </div>
          <ChevronRightIcon class="h-5 w-5 transition-transform group-hover:translate-x-1" />
          
          <!-- Loading overlay -->
          <div v-if="loggingIn && currentLoginRole === credential.role" 
               class="absolute inset-0 bg-black bg-opacity-20 flex items-center justify-center">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>
        </button>
      </div>

      <!-- Expandable Information Section -->
      <div class="border-t pt-4">
        <button
          @click="showDetails = !showDetails"
          class="flex items-center justify-between w-full text-left hover:bg-blue-50 p-2 rounded-lg transition-colors"
        >
          <span class="font-medium text-gray-700">
            {{ showDetails ? 'Hide' : 'Show' }} Detailed Information
          </span>
          <ChevronRightIcon 
            :class="[
              'h-5 w-5 text-gray-500 transition-transform',
              showDetails ? 'rotate-90' : ''
            ]" 
          />
        </button>

        <!-- Detailed Information -->
        <div v-show="showDetails" class="mt-4 space-y-4">
          <!-- System Information -->
          <div class="bg-white rounded-lg p-4 border">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
              <ServerIcon class="h-5 w-5 mr-2 text-blue-500" />
              System Information
            </h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-600">Laravel Version:</span>
                <span class="ml-2 font-medium">{{ systemInfo.laravel_version || 'Unknown' }}</span>
              </div>
              <div>
                <span class="text-gray-600">PHP Version:</span>
                <span class="ml-2 font-medium">{{ systemInfo.php_version || 'Unknown' }}</span>
              </div>
              <div>
                <span class="text-gray-600">Environment:</span>
                <span class="ml-2 font-medium capitalize">{{ systemInfo.environment || 'Unknown' }}</span>
              </div>
              <div>
                <span class="text-gray-600">Users Count:</span>
                <span class="ml-2 font-medium">{{ systemInfo.users_count || 0 }}</span>
              </div>
            </div>
          </div>

          <!-- Database Users -->
          <div v-if="databaseUsers.length > 0" class="bg-white rounded-lg p-4 border">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
              <UsersIcon class="h-5 w-5 mr-2 text-blue-500" />
              Database Users ({{ databaseUsers.length }})
            </h4>
            <div class="space-y-2">
              <div 
                v-for="user in databaseUsers" 
                :key="user.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div>
                  <div class="font-medium text-gray-800">{{ user.name }}</div>
                  <div class="text-sm text-gray-600">{{ user.email }}</div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-medium text-blue-600">{{ user.user_type }}</div>
                  <div class="text-xs text-gray-500">{{ user.roles || 'No Role' }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Demo Credentials Table -->
          <div class="bg-white rounded-lg p-4 border">
            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
              <KeyIcon class="h-5 w-5 mr-2 text-blue-500" />
              Demo Credentials
            </h4>
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="border-b">
                  <tr class="text-left">
                    <th class="py-2 text-gray-600 font-medium">Role</th>
                    <th class="py-2 text-gray-600 font-medium">Email</th>
                    <th class="py-2 text-gray-600 font-medium">Password</th>
                    <th class="py-2 text-gray-600 font-medium">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                    v-for="credential in demoCredentials" 
                    :key="credential.role"
                    class="border-b last:border-b-0"
                  >
                    <td class="py-3">
                      <span 
                        :class="[
                          'px-2 py-1 rounded-full text-xs font-medium',
                          credential.role === 'admin' ? 'bg-red-100 text-red-800' :
                          credential.role === 'employer' ? 'bg-blue-100 text-blue-800' :
                          'bg-green-100 text-green-800'
                        ]"
                      >
                        {{ credential.role }}
                      </span>
                    </td>
                    <td class="py-3 font-mono text-xs">{{ credential.email }}</td>
                    <td class="py-3 font-mono text-xs">{{ credential.password }}</td>
                    <td class="py-3">
                      <button
                        @click="copyCredentials(credential)"
                        class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors"
                      >
                        Copy
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Warning Notice -->
      <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mt-4">
        <div class="flex items-start space-x-3">
          <ExclamationTriangleIcon class="h-5 w-5 text-amber-500 mt-0.5 flex-shrink-0" />
          <div>
            <p class="text-amber-800 font-medium text-sm">Development Environment Only</p>
            <p class="text-amber-700 text-xs mt-1">
              This login information block is for testing purposes only. 
              Remove in production environment for security.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { 
  CircleStackIcon, 
  UserIcon, 
  UsersIcon, 
  ServerIcon, 
  KeyIcon,
  ChevronRightIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline'

// Types
interface DemoCredential {
  role: string
  email: string
  password: string
  description: string
}

interface DatabaseUser {
  id: number
  name: string
  email: string
  user_type: string
  roles: string
  is_active: boolean
  created_at: string
}

interface SystemInfo {
  laravel_version: string
  php_version: string
  environment: string
  database_connection: string
  users_count: number
  last_check: string
}

// Props
interface Props {
  autoFill?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  autoFill: true
})

// Emits
const emit = defineEmits<{
  credentialsSelected: [credential: DemoCredential]
  loginAttempt: [credential: DemoCredential]
}>()

// Reactive state
const loading = ref(true)
const error = ref<string | null>(null)
const databaseConnected = ref(false)
const showDetails = ref(false)
const loggingIn = ref(false)
const currentLoginRole = ref<string | null>(null)

const databaseUsers = ref<DatabaseUser[]>([])
const systemInfo = ref<SystemInfo>({
  laravel_version: '',
  php_version: '',
  environment: '',
  database_connection: '',
  users_count: 0,
  last_check: ''
})

// Fallback demo credentials
const fallbackCredentials: DemoCredential[] = [
  {
    role: 'admin',
    email: 'admin@jobportal.com',
    password: 'password',
    description: 'Super Admin Access'
  },
  {
    role: 'employer',
    email: 'john@example.com',
    password: 'password',
    description: 'Employer Dashboard'
  },
  {
    role: 'candidate',
    email: 'jane@example.com',
    password: 'password',
    description: 'Candidate Portal'
  }
]

const demoCredentials = ref<DemoCredential[]>(fallbackCredentials)

// Methods
const fetchLoginInfo = async (): Promise<void> => {
  try {
    loading.value = true
    error.value = null

    const response = await fetch('/api/auth/login-info', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}: ${response.statusText}`)
    }

    const data = await response.json()

    if (data.success) {
      databaseConnected.value = true
      databaseUsers.value = data.users || []
      systemInfo.value = data.system_info || systemInfo.value
      
      // Use API credentials if available, otherwise fallback
      if (data.demo_credentials && data.demo_credentials.length > 0) {
        demoCredentials.value = data.demo_credentials
      }
    } else {
      throw new Error(data.message || 'Failed to fetch login information')
    }
  } catch (err) {
    console.error('Login info fetch error:', err)
    error.value = err instanceof Error ? err.message : 'Failed to connect to API'
    databaseConnected.value = false
    
    // Use fallback credentials
    demoCredentials.value = fallbackCredentials
  } finally {
    loading.value = false
  }
}

const quickLogin = async (credential: DemoCredential): Promise<void> => {
  try {
    loggingIn.value = true
    currentLoginRole.value = credential.role
    
    emit('loginAttempt', credential)
    
    if (props.autoFill) {
      // Auto-fill the form fields if parent component supports it
      emit('credentialsSelected', credential)
    }
    
    // Show success feedback
    await new Promise(resolve => setTimeout(resolve, 1000))
    
  } catch (err) {
    console.error('Quick login error:', err)
  } finally {
    loggingIn.value = false
    currentLoginRole.value = null
  }
}

const copyCredentials = async (credential: DemoCredential): Promise<void> => {
  try {
    const text = `Email: ${credential.email}\nPassword: ${credential.password}`
    await navigator.clipboard.writeText(text)
    
    // You could add a toast notification here
    console.log('Credentials copied to clipboard')
  } catch (err) {
    console.error('Failed to copy credentials:', err)
    
    // Fallback for older browsers
    const textArea = document.createElement('textarea')
    textArea.value = `${credential.email}\t${credential.password}`
    document.body.appendChild(textArea)
    textArea.select()
    document.execCommand('copy')
    document.body.removeChild(textArea)
  }
}

// Lifecycle
onMounted(() => {
  fetchLoginInfo()
})
</script>

<style scoped>
/* Custom animations */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: .5;
  }
}
</style>