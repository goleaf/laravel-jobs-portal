// API Response Types
export interface ApiResponse<T = any> {
  success: boolean
  data: T
  message: string
  errors?: Record<string, string[]>
}

export interface PaginatedResponse<T = any> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from?: number
  to?: number
  prev_page_url?: string | null
  next_page_url?: string | null
  first_page_url?: string
  last_page_url?: string
  links?: Array<{
    url: string | null
    label: string
    active: boolean
  }>
  path?: string
}

// Error Response Types
export interface ApiError {
  message: string
  errors?: Record<string, string[]>
  status?: number
}

// Request/Response wrapper types
export interface ApiRequestConfig {
  headers?: Record<string, string>
  params?: Record<string, any>
  timeout?: number
}

// Meta information for paginated responses
export interface PaginationMeta {
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
  has_more_pages: boolean
}

// Generic API list response
export interface ApiListResponse<T = any> extends ApiResponse<T[]> {
  meta?: PaginationMeta
}

// Generic API resource response
export interface ApiResourceResponse<T = any> extends ApiResponse<T> {
  meta?: Record<string, any>
}

// Authentication response types
export interface AuthResponse {
  user: {
    id: number
    name: string
    email: string
    email_verified_at?: string
    is_admin?: boolean
    role?: string
    created_at?: string
    updated_at?: string
  }
  token: string
  token_type: string
}

export interface AuthUser {
  id: number
  name: string
  email: string
  email_verified_at?: string
  is_admin?: boolean
  role?: string
  created_at?: string
  updated_at?: string
}