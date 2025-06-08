/**
 * Universal Authentication Types
 * TypeScript definitions for authentication system
 */

export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  is_admin: boolean
  role: string
  created_at: string
  updated_at?: string
}

export interface LoginCredentials {
  email: string
  password: string
  remember?: boolean
}

export interface RegisterData {
  name: string
  email: string
  password: string
  password_confirmation: string
  role?: 'admin' | 'employer' | 'candidate'
}

export interface AuthResponse {
  user: User
  token: string
  token_type: string
}

export interface ApiResponse<T = any> {
  success: boolean
  message: string
  data?: T
  errors?: Record<string, string[]>
  error?: string
}

export interface ValidationErrors {
  [key: string]: string[]
}

export interface AuthState {
  user: User | null
  token: string | null
  isLoading: boolean
  error: string | null
}

export interface TokenRefreshResponse {
  token: string
  token_type: string
}

export interface RoleCheckResponse {
  has_role: boolean
  role: string
  user_roles: string[]
}