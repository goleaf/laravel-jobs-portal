import { defineStore } from 'pinia'
import { ref, computed, readonly } from 'vue'
import type { User, LoginCredentials, RegisterData, ApiResponse } from '../types/auth'
import { apiClient } from '../services/api'

/**
 * Universal Authentication Store
 * Manages user authentication state for Vue.js SPA
 * Uses Laravel Sanctum for SPA authentication
 */
export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  // Getters
  const isAuthenticated = computed(() => !!user.value && !!token.value)
  const isAdmin = computed(() => user.value?.is_admin || user.value?.role === 'admin')
  const userRole = computed(() => user.value?.role || 'guest')
  const userName = computed(() => user.value?.name || 'Guest')

  // Actions
  async function login(credentials: LoginCredentials): Promise<boolean> {
    isLoading.value = true
    error.value = null

    try {
      console.log('Attempting login for:', credentials.email)
      
      const response = await apiClient.post<ApiResponse<{
        user: User
        token: string
        token_type: string
      }>>('/auth/login', credentials)

      if (response.data.success && response.data.data) {
        const { user: userData, token: authToken } = response.data.data
        
        // Update state
        user.value = userData
        token.value = authToken
        
        // Persist token
        localStorage.setItem('auth_token', authToken)
        
        // Update API client headers
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
        
        console.log('Login successful:', userData.name)
        return true
      }

      throw new Error(response.data.message || 'Login failed')

    } catch (err: any) {
      console.error('Login error:', err)
      error.value = err.response?.data?.message || err.message || 'Login failed'
      return false
    } finally {
      isLoading.value = false
    }
  }

  async function register(data: RegisterData): Promise<boolean> {
    isLoading.value = true
    error.value = null

    try {
      console.log('Attempting registration for:', data.email)
      
      const response = await apiClient.post<ApiResponse<{
        user: User
        token: string
        token_type: string
      }>>('/auth/register', data)

      if (response.data.success && response.data.data) {
        const { user: userData, token: authToken } = response.data.data
        
        // Update state
        user.value = userData
        token.value = authToken
        
        // Persist token
        localStorage.setItem('auth_token', authToken)
        
        // Update API client headers
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
        
        console.log('Registration successful:', userData.name)
        return true
      }

      throw new Error(response.data.message || 'Registration failed')

    } catch (err: any) {
      console.error('Registration error:', err)
      error.value = err.response?.data?.message || err.message || 'Registration failed'
      return false
    } finally {
      isLoading.value = false
    }
  }

  async function logout(): Promise<void> {
    isLoading.value = true
    
    try {
      // Call logout endpoint if we have a token
      if (token.value) {
        await apiClient.post('/auth/logout')
      }
    } catch (err) {
      console.warn('Logout API call failed:', err)
      // Continue with local logout even if API fails
    } finally {
      // Clear state regardless of API response
      user.value = null
      token.value = null
      error.value = null
      
      // Clear persisted data
      localStorage.removeItem('auth_token')
      
      // Clear API client headers
      delete apiClient.defaults.headers.common['Authorization']
      
      isLoading.value = false
      console.log('Logout completed')
    }
  }

  async function fetchUser(): Promise<boolean> {
    if (!token.value) {
      return false
    }

    isLoading.value = true
    error.value = null

    try {
      // Set token in API client
      apiClient.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      
      const response = await apiClient.get<ApiResponse<{ user: User }>>('/auth/user')

      if (response.data.success && response.data.data) {
        user.value = response.data.data.user
        console.log('User fetched successfully:', user.value.name)
        return true
      }

      throw new Error(response.data.message || 'Failed to fetch user')

    } catch (err: any) {
      console.error('Fetch user error:', err)
      
      // If token is invalid, clear auth state
      if (err.response?.status === 401) {
        await logout()
      } else {
        error.value = err.response?.data?.message || err.message || 'Failed to fetch user'
      }
      
      return false
    } finally {
      isLoading.value = false
    }
  }

  async function refreshToken(): Promise<boolean> {
    if (!token.value) {
      return false
    }

    isLoading.value = true
    error.value = null

    try {
      const response = await apiClient.post<ApiResponse<{
        token: string
        token_type: string
      }>>('/auth/refresh')

      if (response.data.success && response.data.data) {
        const { token: newToken } = response.data.data
        
        // Update token
        token.value = newToken
        localStorage.setItem('auth_token', newToken)
        apiClient.defaults.headers.common['Authorization'] = `Bearer ${newToken}`
        
        console.log('Token refreshed successfully')
        return true
      }

      throw new Error(response.data.message || 'Token refresh failed')

    } catch (err: any) {
      console.error('Token refresh error:', err)
      
      // If refresh fails, logout user
      if (err.response?.status === 401) {
        await logout()
      } else {
        error.value = err.response?.data?.message || err.message || 'Token refresh failed'
      }
      
      return false
    } finally {
      isLoading.value = false
    }
  }

  async function checkRole(role: string): Promise<boolean> {
    if (!token.value) {
      return false
    }

    try {
      const response = await apiClient.get<ApiResponse<{
        has_role: boolean
        role: string
        user_roles: string[]
      }>>(`/auth/check-role/${role}`)

      return response.data.success && response.data.data?.has_role || false

    } catch (err) {
      console.warn('Role check failed:', err)
      return false
    }
  }

  // Initialize authentication state
  async function initialize(): Promise<void> {
    console.log('Initializing auth store...')
    
    // If we have a stored token, try to fetch user
    if (token.value) {
      console.log('Found stored token, fetching user...')
      const success = await fetchUser()
      
      if (!success) {
        console.log('Failed to fetch user with stored token, clearing auth state')
        await logout()
      }
    } else {
      console.log('No stored token found')
    }
  }

  // Clear error
  function clearError(): void {
    error.value = null
  }

  return {
    // State
    user: readonly(user),
    token: readonly(token),
    isLoading: readonly(isLoading),
    error: readonly(error),
    
    // Getters
    isAuthenticated,
    isAdmin,
    userRole,
    userName,
    
    // Actions
    login,
    register,
    logout,
    fetchUser,
    refreshToken,
    checkRole,
    initialize,
    clearError
  }
}) 