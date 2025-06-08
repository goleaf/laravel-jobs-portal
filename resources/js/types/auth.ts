export interface User {
  id: number
  name: string
  email: string
  email_verified_at?: string
  role: 'admin' | 'employer' | 'candidate'
  created_at: string
  updated_at: string
}

export interface LoginRequest {
  email: string
  password: string
  remember?: boolean
}

export interface RegisterRequest {
  name: string
  email: string
  password: string
  password_confirmation: string
  role: 'employer' | 'candidate'
}

export interface ApiResponse<T = any> {
  data: T
  message?: string
  status: number
}