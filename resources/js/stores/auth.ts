import { defineStore } from 'pinia'
import { ref, computed, readonly } from 'vue'
import type { 
  UserRole, 
  BaseUser, 
  Candidate, 
  Employer, 
  Administrator, 
  Visitor,
  UserResponse,
  LoginCredentials,
  RegisterData,
  ApiError
} from '@/types/user'
import { apiService } from '@/services/api'

/**
 * Universal Authentication Store
 * Manages user authentication state for Vue.js SPA
 * Uses Laravel Sanctum for SPA authentication
 */
export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<Candidate | Employer | Administrator | null>(null)
  const visitor = ref<Visitor | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))
  const isLoading = ref(false)
  const isInitialized = ref(false)
  const lastActivity = ref<Date | null>(null)
  const permissions = ref<string[]>([])
  const userPreferences = ref<Record<string, any>>({})

  // Getters
  const isAuthenticated = computed(() => !!user.value && !!token.value)
  
  const userRole = computed((): UserRole => {
    if (user.value) {
      return user.value.role
    }
    return 'visitor'
  })

  const isCandidate = computed(() => userRole.value === 'candidate')
  const isEmployer = computed(() => userRole.value === 'employer')
  const isAdmin = computed(() => userRole.value === 'admin')
  const isVisitor = computed(() => userRole.value === 'visitor')

  const userName = computed(() => {
    if (!user.value) return null
    
    switch (user.value.role) {
      case 'candidate':
        const candidate = user.value as Candidate
        return `${candidate.profile.first_name} ${candidate.profile.last_name}`
      case 'employer':
        const employer = user.value as Employer
        return employer.company.name
      case 'admin':
        const admin = user.value as Administrator
        return `${admin.adminProfile.first_name} ${admin.adminProfile.last_name}`
      default:
        return user.value.email
    }
  })

  const userAvatar = computed(() => {
    if (!user.value) return null
    
    if (user.value.profile_image) {
      return user.value.profile_image
    }
    
    // Default avatar based on role
    switch (user.value.role) {
      case 'candidate':
        return '/images/default-candidate-avatar.png'
      case 'employer':
        const employer = user.value as Employer
        return employer.company.logo || '/images/default-company-logo.png'
      case 'admin':
        return '/images/default-admin-avatar.png'
      default:
        return '/images/default-avatar.png'
    }
  })

  const canAccess = computed((permission: string): boolean => {
    if (!isAuthenticated.value) return false
    if (isAdmin.value) return true // Admins have access to everything
    return permissions.value.includes(permission)
  })

  const dashboardRoute = computed(() => {
    switch (userRole.value) {
      case 'candidate':
        return '/candidate/dashboard'
      case 'employer':
        return '/employer/dashboard'
      case 'admin':
        return '/admin/dashboard'
      default:
        return '/'
    }
  })

  // Actions
  async function login(credentials: LoginCredentials): Promise<UserResponse> {
    isLoading.value = true
    
    try {
      const response = await apiService.post<UserResponse>('/auth/login', credentials)
      
      if (response.data.user && response.data.token) {
        await setAuthenticatedUser(response.data)
        updateLastActivity()
        return response.data
      } else {
        throw new Error('Invalid response from server')
      }
    } catch (error) {
      console.error('Login error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  async function register(data: RegisterData): Promise<UserResponse> {
    isLoading.value = true
    
    try {
      const response = await apiService.post<UserResponse>('/auth/register', data)
      
      if (response.data.user && response.data.token) {
        await setAuthenticatedUser(response.data)
        updateLastActivity()
        return response.data
      } else {
        throw new Error('Invalid response from server')
      }
    } catch (error) {
      console.error('Registration error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  async function logout(): Promise<void> {
    isLoading.value = true
    
    try {
      // Call logout endpoint if user is authenticated
      if (token.value) {
        await apiService.post('/auth/logout')
      }
    } catch (error) {
      console.error('Logout error:', error)
      // Continue with logout even if API call fails
    } finally {
      clearAuthenticatedUser()
      isLoading.value = false
    }
  }

  async function refreshToken(): Promise<boolean> {
    if (!token.value) return false
    
    try {
      const response = await apiService.post<{ token: string }>('/auth/refresh')
      
      if (response.data.token) {
        token.value = response.data.token
        localStorage.setItem('auth_token', response.data.token)
        updateLastActivity()
        return true
      }
      
      return false
    } catch (error) {
      console.error('Token refresh error:', error)
      await logout()
      return false
    }
  }

  async function fetchUser(): Promise<void> {
    if (!token.value) return
    
    isLoading.value = true
    
    try {
      const response = await apiService.get<UserResponse>('/auth/user')
      
      if (response.data.user) {
        setUserData(response.data)
        updateLastActivity()
      } else {
        throw new Error('No user data received')
      }
    } catch (error) {
      console.error('Fetch user error:', error)
      await logout()
    } finally {
      isLoading.value = false
    }
  }

  async function updateProfile(data: Partial<BaseUser>): Promise<void> {
    if (!user.value) throw new Error('No authenticated user')
    
    isLoading.value = true
    
    try {
      const response = await apiService.put<UserResponse>('/auth/profile', data)
      
      if (response.data.user) {
        setUserData(response.data)
      }
    } catch (error) {
      console.error('Profile update error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  async function changePassword(data: { 
    current_password: string
    password: string
    password_confirmation: string
  }): Promise<void> {
    if (!user.value) throw new Error('No authenticated user')
    
    isLoading.value = true
    
    try {
      await apiService.put('/auth/password', data)
    } catch (error) {
      console.error('Password change error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  async function forgotPassword(email: string): Promise<void> {
    isLoading.value = true
    
    try {
      await apiService.post('/auth/forgot-password', { email })
    } catch (error) {
      console.error('Forgot password error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  async function resetPassword(data: {
    token: string
    email: string
    password: string
    password_confirmation: string
  }): Promise<void> {
    isLoading.value = true
    
    try {
      await apiService.post('/auth/reset-password', data)
    } catch (error) {
      console.error('Reset password error:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  function initializeVisitor(): Visitor {
    const sessionId = generateSessionId()
    const visitorData: Visitor = {
      sessionId,
      preferences: {
        jobAlerts: false,
        newsletterSubscription: false,
        savedJobs: [],
        recentSearches: []
      }
    }
    
    visitor.value = visitorData
    localStorage.setItem('visitor_session', JSON.stringify(visitorData))
    
    return visitorData
  }

  function updateVisitorPreferences(preferences: Partial<Visitor['preferences']>): void {
    if (!visitor.value) {
      initializeVisitor()
    }
    
    if (visitor.value && visitor.value.preferences) {
      visitor.value.preferences = { ...visitor.value.preferences, ...preferences }
      localStorage.setItem('visitor_session', JSON.stringify(visitor.value))
    }
  }

  async function initialize(): Promise<void> {
    if (isInitialized.value) return
    
    // Check for existing token
    const storedToken = localStorage.getItem('auth_token')
    if (storedToken) {
      token.value = storedToken
      await fetchUser()
    } else {
      // Initialize visitor session
      const storedVisitor = localStorage.getItem('visitor_session')
      if (storedVisitor) {
        try {
          visitor.value = JSON.parse(storedVisitor)
        } catch {
          initializeVisitor()
        }
      } else {
        initializeVisitor()
      }
    }
    
    isInitialized.value = true
  }

  function checkSession(): boolean {
    if (!lastActivity.value) return true
    
    const now = new Date()
    const timeSinceLastActivity = now.getTime() - lastActivity.value.getTime()
    const sessionTimeout = 30 * 60 * 1000 // 30 minutes
    
    if (timeSinceLastActivity > sessionTimeout) {
      logout()
      return false
    }
    
    return true
  }

  function updateLastActivity(): void {
    lastActivity.value = new Date()
  }

  // Helper functions
  async function setAuthenticatedUser(data: UserResponse): Promise<void> {
    user.value = data.user
    token.value = data.token || null
    permissions.value = data.permissions || []
    userPreferences.value = data.preferences || {}
    
    if (token.value) {
      localStorage.setItem('auth_token', token.value)
    }
    
    // Clear visitor session when user authenticates
    visitor.value = null
    localStorage.removeItem('visitor_session')
  }

  function setUserData(data: UserResponse): void {
    user.value = data.user
    permissions.value = data.permissions || []
    userPreferences.value = data.preferences || {}
  }

  function clearAuthenticatedUser(): void {
    user.value = null
    token.value = null
    permissions.value = []
    userPreferences.value = {}
    lastActivity.value = null
    
    localStorage.removeItem('auth_token')
    
    // Reinitialize visitor session
    initializeVisitor()
  }

  function generateSessionId(): string {
    return 'visitor_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now()
  }

  // Role-specific helpers
  function getCandidateData(): Candidate | null {
    return isCandidate.value ? user.value as Candidate : null
  }

  function getEmployerData(): Employer | null {
    return isEmployer.value ? user.value as Employer : null
  }

  function getAdminData(): Administrator | null {
    return isAdmin.value ? user.value as Administrator : null
  }

  // Return store
  return {
    // State
    user: readonly(user),
    visitor: readonly(visitor),
    token: readonly(token),
    isLoading: readonly(isLoading),
    isInitialized: readonly(isInitialized),
    lastActivity: readonly(lastActivity),
    permissions: readonly(permissions),
    userPreferences: readonly(userPreferences),
    
    // Computed
    isAuthenticated,
    userRole,
    isCandidate,
    isEmployer,
    isAdmin,
    isVisitor,
    userName,
    userAvatar,
    canAccess,
    dashboardRoute,
    
    // Actions
    login,
    register,
    logout,
    refreshToken,
    fetchUser,
    updateProfile,
    changePassword,
    forgotPassword,
    resetPassword,
    initializeVisitor,
    updateVisitorPreferences,
    initialize,
    checkSession,
    updateLastActivity,
    
    // Role-specific helpers
    getCandidateData,
    getEmployerData,
    getAdminData
  }
}) 