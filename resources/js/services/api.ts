import axios, { AxiosInstance, AxiosResponse } from "axios"

// Create axios instance with default config
const api: AxiosInstance = axios.create({
  baseURL: "/api",
  timeout: 10000,
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-Requested-With": "XMLHttpRequest"
  }
})

// Request interceptor to add auth token
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("auth_token")
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  
  // Add CSRF token if available
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (csrfToken) {
    config.headers['X-CSRF-TOKEN'] = csrfToken
  }
  
  return config
})

// Response interceptor for error handling
api.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Unauthorized - redirect to login
      localStorage.removeItem("auth_token")
      window.location.href = "/login"
    }
    return Promise.reject(error)
  }
)

// API response interfaces
interface ApiResponse<T = any> {
  success: boolean
  data: T
  message: string
  errors?: Record<string, string[]>
}

interface PaginatedResponse<T = any> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

// Dashboard API
export const dashboardApi = {
  async getStats(): Promise<any> {
    try {
      const response = await api.get("/v1/admin/dashboard/stats")
      return response.data.data || {}
    } catch (error) {
      console.error("Failed to fetch dashboard stats:", error)
      return {
        total_jobs: 0,
        total_companies: 0,
        total_candidates: 0,
        total_applications: 0
      }
    }
  },

  async getRecentJobs(): Promise<any> {
    try {
      const response = await api.get("/v1/admin/dashboard/recent-jobs")
      return response.data || { data: [] }
    } catch (error) {
      console.error("Failed to fetch recent jobs:", error)
      return { data: [] }
    }
  },

  async getRecentApplications(): Promise<any> {
    try {
      const response = await api.get("/v1/admin/dashboard/recent-applications")
      return response.data || { data: [] }
    } catch (error) {
      console.error("Failed to fetch recent applications:", error)
      return { data: [] }
    }
  }
}

// Jobs API
export const jobsApi = {
  async getAll(params: any = {}): Promise<PaginatedResponse> {
    const response = await api.get("/v1/jobs", { params })
    return response.data
  },

  async getById(id: number): Promise<ApiResponse> {
    const response = await api.get(`/v1/jobs/${id}`)
    return response.data
  },

  async create(data: any): Promise<ApiResponse> {
    const response = await api.post("/v1/jobs", data)
    return response.data
  },

  async update(id: number, data: any): Promise<ApiResponse> {
    const response = await api.put(`/v1/jobs/${id}`, data)
    return response.data
  },

  async delete(id: number): Promise<ApiResponse> {
    const response = await api.delete(`/v1/jobs/${id}`)
    return response.data
  }
}

// Companies API
export const companiesApi = {
  async getAll(params: any = {}): Promise<PaginatedResponse> {
    const response = await api.get("/v1/companies", { params })
    return response.data
  },

  async getById(id: number): Promise<ApiResponse> {
    const response = await api.get(`/v1/companies/${id}`)
    return response.data
  },

  async create(data: any): Promise<ApiResponse> {
    const response = await api.post("/v1/companies", data)
    return response.data
  },

  async update(id: number, data: any): Promise<ApiResponse> {
    const response = await api.put(`/v1/companies/${id}`, data)
    return response.data
  },

  async delete(id: number): Promise<ApiResponse> {
    const response = await api.delete(`/v1/companies/${id}`)
    return response.data
  }
}

// Candidates API
export const candidatesApi = {
  async getAll(params: any = {}): Promise<PaginatedResponse> {
    const response = await api.get("/v1/candidates", { params })
    return response.data
  },

  async getById(id: number): Promise<ApiResponse> {
    const response = await api.get(`/v1/candidates/${id}`)
    return response.data
  },

  async create(data: any): Promise<ApiResponse> {
    const response = await api.post("/v1/candidates", data)
    return response.data
  },

  async update(id: number, data: any): Promise<ApiResponse> {
    const response = await api.put(`/v1/candidates/${id}`, data)
    return response.data
  },

  async delete(id: number): Promise<ApiResponse> {
    const response = await api.delete(`/v1/candidates/${id}`)
    return response.data
  }
}

// Authentication API
export const authApi = {
  async login(email: string, password: string): Promise<ApiResponse> {
    const response = await api.post("/v1/auth/login", { email, password })
    return response.data
  },

  async register(data: any): Promise<ApiResponse> {
    const response = await api.post("/v1/auth/register", data)
    return response.data
  },

  async logout(): Promise<ApiResponse> {
    const response = await api.post("/v1/auth/logout")
    return response.data
  },

  async getUser(): Promise<ApiResponse> {
    const response = await api.get("/v1/auth/user")
    return response.data
  },

  async refresh(): Promise<ApiResponse> {
    const response = await api.post("/v1/auth/refresh")
    return response.data
  },

  async checkRole(role: string): Promise<ApiResponse> {
    const response = await api.get(`/v1/auth/check-role/${role}`)
    return response.data
  }
}

// Admin Users API
export const adminUsersApi = {
  async getAll(params: any = {}): Promise<PaginatedResponse> {
    const response = await api.get("/v1/admin/users", { params })
    return response.data
  },

  async getById(id: number): Promise<ApiResponse> {
    const response = await api.get(`/v1/admin/users/${id}`)
    return response.data
  },

  async create(data: any): Promise<ApiResponse> {
    const response = await api.post("/v1/admin/users", data)
    return response.data
  },

  async update(id: number, data: any): Promise<ApiResponse> {
    const response = await api.put(`/v1/admin/users/${id}`, data)
    return response.data
  },

  async delete(id: number): Promise<ApiResponse> {
    const response = await api.delete(`/v1/admin/users/${id}`)
    return response.data
  },

  async toggleStatus(id: number): Promise<ApiResponse> {
    const response = await api.patch(`/v1/admin/users/${id}/toggle-status`)
    return response.data
  }
}

// Health check
export const healthApi = {
  async check(): Promise<ApiResponse> {
    const response = await api.get("/v1/health")
    return response.data
  }
}

// Export the api client for use with authentication store  
export const apiClient = api

export default api 