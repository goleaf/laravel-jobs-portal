import axios, { 
  AxiosInstance, 
  AxiosRequestConfig, 
  AxiosResponse, 
  AxiosError,
  InternalAxiosRequestConfig
} from 'axios';
import type { ApiError } from '@/types/user';

// API Response wrapper
export interface ApiResponse<T = any> {
  data: T;
  message?: string;
  success: boolean;
  errors?: Record<string, string[]>;
}

// Request configuration
export interface RequestConfig extends AxiosRequestConfig {
  skipAuth?: boolean;
  retryAttempts?: number;
  timeout?: number;
}

class ApiService {
  private client: AxiosInstance;
  private baseURL: string;
  private defaultTimeout: number = 30000; // 30 seconds

  constructor() {
    this.baseURL = this.getBaseURL();
    this.client = this.createAxiosInstance();
    this.setupInterceptors();
  }

  private getBaseURL(): string {
    // Get base URL from environment or use default
    const baseURL = import.meta.env.VITE_API_BASE_URL || '/api';
    return baseURL.endsWith('/') ? baseURL.slice(0, -1) : baseURL;
  }

  private createAxiosInstance(): AxiosInstance {
    return axios.create({
      baseURL: this.baseURL,
      timeout: this.defaultTimeout,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      withCredentials: true, // Important for Laravel Sanctum
    });
  }

  private setupInterceptors(): void {
    // Request interceptor
    this.client.interceptors.request.use(
      (config: InternalAxiosRequestConfig) => {
        // Add CSRF token for Laravel
        const csrfToken = this.getCSRFToken();
        if (csrfToken) {
          config.headers['X-CSRF-TOKEN'] = csrfToken;
        }

        // Add auth token if available and not skipped
        if (!config.skipAuth) {
          const token = localStorage.getItem('auth_token');
          if (token) {
            config.headers.Authorization = `Bearer ${token}`;
          }
        }

        // Add request timestamp for debugging
        config.metadata = { startTime: new Date() };

        console.log(`[API Request] ${config.method?.toUpperCase()} ${config.url}`, {
          headers: config.headers,
          data: config.data,
        });

        return config;
      },
      (error: AxiosError) => {
        console.error('[API Request Error]', error);
        return Promise.reject(error);
      }
    );

    // Response interceptor
    this.client.interceptors.response.use(
      (response: AxiosResponse) => {
        const duration = new Date().getTime() - 
          (response.config.metadata?.startTime?.getTime() || 0);

        console.log(`[API Response] ${response.status} ${response.config.url} (${duration}ms)`, {
          data: response.data,
        });

        return response;
      },
      async (error: AxiosError) => {
        const originalRequest = error.config as RequestConfig & { _retry?: boolean };

        console.error('[API Response Error]', {
          status: error.response?.status,
          url: error.config?.url,
          message: error.message,
          data: error.response?.data,
        });

        // Handle specific error cases
        if (error.response?.status === 401 && !originalRequest._retry) {
          originalRequest._retry = true;

          // Try to refresh token
          const refreshed = await this.refreshToken();
          if (refreshed) {
            // Retry original request with new token
            const token = localStorage.getItem('auth_token');
            if (token) {
              originalRequest.headers = originalRequest.headers || {};
              originalRequest.headers.Authorization = `Bearer ${token}`;
            }
            return this.client(originalRequest);
          } else {
            // Redirect to login if refresh fails
            this.handleAuthenticationError();
          }
        }

        // Handle CSRF token mismatch
        if (error.response?.status === 419) {
          await this.refreshCSRFToken();
          return this.client(originalRequest);
        }

        // Handle network errors
        if (!error.response) {
          const networkError: ApiError = {
            message: 'Network error. Please check your connection.',
            code: 0,
            details: { originalError: error.message }
          };
          return Promise.reject(networkError);
        }

        // Transform Laravel validation errors
        const transformedError = this.transformError(error);
        return Promise.reject(transformedError);
      }
    );
  }

  private transformError(error: AxiosError): ApiError {
    const response = error.response;
    const data = response?.data as any;

    // Default error structure
    const apiError: ApiError = {
      message: 'An unexpected error occurred',
      code: response?.status || 500,
    };

    if (data) {
      // Laravel validation error format
      if (data.errors && typeof data.errors === 'object') {
        apiError.message = data.message || 'Validation failed';
        apiError.errors = Object.entries(data.errors).map(([field, messages]) => ({
          field,
          message: Array.isArray(messages) ? messages[0] : messages,
        }));
      }
      // Laravel error with message
      else if (data.message) {
        apiError.message = data.message;
      }
      // Simple error string
      else if (typeof data === 'string') {
        apiError.message = data;
      }
    }

    // Add specific error details for different status codes
    switch (response?.status) {
      case 400:
        apiError.message = apiError.message || 'Bad request';
        break;
      case 401:
        apiError.message = apiError.message || 'Authentication required';
        break;
      case 403:
        apiError.message = apiError.message || 'Access forbidden';
        break;
      case 404:
        apiError.message = apiError.message || 'Resource not found';
        break;
      case 422:
        apiError.message = apiError.message || 'Validation error';
        break;
      case 429:
        apiError.message = apiError.message || 'Too many requests';
        break;
      case 500:
        apiError.message = apiError.message || 'Internal server error';
        break;
      case 503:
        apiError.message = apiError.message || 'Service unavailable';
        break;
    }

    return apiError;
  }

  private async refreshToken(): Promise<boolean> {
    try {
      const response = await axios.post(
        `${this.baseURL}/auth/refresh`,
        {},
        {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token')}`,
          },
          withCredentials: true,
        }
      );

      if (response.data.token) {
        localStorage.setItem('auth_token', response.data.token);
        return true;
      }
      return false;
    } catch (error) {
      console.error('Token refresh failed:', error);
      return false;
    }
  }

  private async refreshCSRFToken(): Promise<void> {
    try {
      await axios.get('/sanctum/csrf-cookie', { withCredentials: true });
    } catch (error) {
      console.error('CSRF token refresh failed:', error);
    }
  }

  private getCSRFToken(): string | null {
    // Try to get CSRF token from meta tag
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag) {
      return metaTag.getAttribute('content');
    }

    // Try to get from cookie
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    if (match) {
      return decodeURIComponent(match[1]);
    }

    return null;
  }

  private handleAuthenticationError(): void {
    // Clear stored token
    localStorage.removeItem('auth_token');
    
    // Emit event for auth store to handle
    window.dispatchEvent(new CustomEvent('auth:logout'));
    
    // Redirect to login if not already there
    if (!window.location.pathname.includes('/login')) {
      const returnUrl = encodeURIComponent(window.location.pathname + window.location.search);
      window.location.href = `/login?return=${returnUrl}`;
    }
  }

  // HTTP Methods
  async get<T = any>(url: string, config?: RequestConfig): Promise<AxiosResponse<T>> {
    return this.client.get<T>(url, config);
  }

  async post<T = any>(url: string, data?: any, config?: RequestConfig): Promise<AxiosResponse<T>> {
    return this.client.post<T>(url, data, config);
  }

  async put<T = any>(url: string, data?: any, config?: RequestConfig): Promise<AxiosResponse<T>> {
    return this.client.put<T>(url, data, config);
  }

  async patch<T = any>(url: string, data?: any, config?: RequestConfig): Promise<AxiosResponse<T>> {
    return this.client.patch<T>(url, data, config);
  }

  async delete<T = any>(url: string, config?: RequestConfig): Promise<AxiosResponse<T>> {
    return this.client.delete<T>(url, config);
  }

  // File upload helper
  async uploadFile<T = any>(
    url: string, 
    file: File, 
    progressCallback?: (progress: number) => void,
    additionalData?: Record<string, any>
  ): Promise<AxiosResponse<T>> {
    const formData = new FormData();
    formData.append('file', file);

    // Add additional data if provided
    if (additionalData) {
      Object.entries(additionalData).forEach(([key, value]) => {
        formData.append(key, value);
      });
    }

    return this.client.post<T>(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      onUploadProgress: (progressEvent) => {
        if (progressCallback && progressEvent.total) {
          const progress = (progressEvent.loaded / progressEvent.total) * 100;
          progressCallback(Math.round(progress));
        }
      },
    });
  }

  // Bulk operations helper
  async bulkOperation<T = any>(
    url: string,
    items: any[],
    operation: 'create' | 'update' | 'delete',
    batchSize: number = 50
  ): Promise<AxiosResponse<T>[]> {
    const results: AxiosResponse<T>[] = [];
    
    for (let i = 0; i < items.length; i += batchSize) {
      const batch = items.slice(i, i + batchSize);
      const response = await this.post<T>(url, {
        operation,
        items: batch,
      });
      results.push(response);
    }
    
    return results;
  }

  // Retry mechanism for failed requests
  async requestWithRetry<T = any>(
    requestFn: () => Promise<AxiosResponse<T>>,
    maxRetries: number = 3,
    delay: number = 1000
  ): Promise<AxiosResponse<T>> {
    let lastError: any;
    
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
      try {
        return await requestFn();
      } catch (error) {
        lastError = error;
        
        if (attempt === maxRetries) {
          throw error;
        }
        
        // Exponential backoff
        const waitTime = delay * Math.pow(2, attempt - 1);
        await new Promise(resolve => setTimeout(resolve, waitTime));
      }
    }
    
    throw lastError;
  }

  // Health check
  async healthCheck(): Promise<boolean> {
    try {
      const response = await this.get('/health', { skipAuth: true, timeout: 5000 });
      return response.status === 200;
    } catch (error) {
      return false;
    }
  }

  // Get current configuration
  getConfig(): AxiosRequestConfig {
    return this.client.defaults;
  }

  // Update base URL (useful for switching environments)
  updateBaseURL(newBaseURL: string): void {
    this.baseURL = newBaseURL.endsWith('/') ? newBaseURL.slice(0, -1) : newBaseURL;
    this.client.defaults.baseURL = this.baseURL;
  }

  // Cancel all pending requests
  cancelAllRequests(): void {
    // This would require tracking request sources, implementing if needed
    console.warn('Cancel all requests not implemented yet');
  }
}

// Create singleton instance
export const apiService = new ApiService();

// Export for direct use
export default apiService;

// Specific API endpoints
export const authAPI = {
  login: (credentials: any) => apiService.post('/auth/login', credentials),
  register: (data: any) => apiService.post('/auth/register', data),
  logout: () => apiService.post('/auth/logout'),
  refresh: () => apiService.post('/auth/refresh'),
  user: () => apiService.get('/auth/user'),
  forgotPassword: (email: string) => apiService.post('/auth/forgot-password', { email }),
  resetPassword: (data: any) => apiService.post('/auth/reset-password', data),
  updateProfile: (data: any) => apiService.put('/auth/profile', data),
  changePassword: (data: any) => apiService.put('/auth/password', data),
};

export const jobsAPI = {
  list: (params?: Record<string, any>) => apiService.get('/jobs', { params }),
  show: (id: number) => apiService.get(`/jobs/${id}`),
  create: (data: any) => apiService.post('/jobs', data),
  update: (id: number, data: any) => apiService.put(`/jobs/${id}`, data),
  delete: (id: number) => apiService.delete(`/jobs/${id}`),
  apply: (id: number, data: any) => apiService.post(`/jobs/${id}/apply`, data),
  save: (id: number) => apiService.post(`/jobs/${id}/save`),
  unsave: (id: number) => apiService.delete(`/jobs/${id}/save`),
  search: (query: string, filters?: Record<string, any>) => 
    apiService.get('/jobs/search', { params: { q: query, ...filters } }),
};

export const companiesAPI = {
  list: (params?: Record<string, any>) => apiService.get('/companies', { params }),
  show: (id: number) => apiService.get(`/companies/${id}`),
  create: (data: any) => apiService.post('/companies', data),
  update: (id: number, data: any) => apiService.put(`/companies/${id}`, data),
  delete: (id: number) => apiService.delete(`/companies/${id}`),
  follow: (id: number) => apiService.post(`/companies/${id}/follow`),
  unfollow: (id: number) => apiService.delete(`/companies/${id}/follow`),
  jobs: (id: number, params?: Record<string, any>) => 
    apiService.get(`/companies/${id}/jobs`, { params }),
};

export const candidatesAPI = {
  profile: () => apiService.get('/candidate/profile'),
  updateProfile: (data: any) => apiService.put('/candidate/profile', data),
  applications: (params?: Record<string, any>) => 
    apiService.get('/candidate/applications', { params }),
  savedJobs: (params?: Record<string, any>) => 
    apiService.get('/candidate/saved-jobs', { params }),
  resume: {
    upload: (file: File) => apiService.uploadFile('/candidate/resume', file),
    download: (id: number) => apiService.get(`/candidate/resume/${id}/download`),
    delete: (id: number) => apiService.delete(`/candidate/resume/${id}`),
  },
  jobAlerts: {
    list: () => apiService.get('/candidate/job-alerts'),
    create: (data: any) => apiService.post('/candidate/job-alerts', data),
    update: (id: number, data: any) => apiService.put(`/candidate/job-alerts/${id}`, data),
    delete: (id: number) => apiService.delete(`/candidate/job-alerts/${id}`),
  },
};

export const employersAPI = {
  dashboard: () => apiService.get('/employer/dashboard'),
  company: () => apiService.get('/employer/company'),
  updateCompany: (data: any) => apiService.put('/employer/company', data),
  jobs: (params?: Record<string, any>) => 
    apiService.get('/employer/jobs', { params }),
  createJob: (data: any) => apiService.post('/employer/jobs', data),
  updateJob: (id: number, data: any) => apiService.put(`/employer/jobs/${id}`, data),
  deleteJob: (id: number) => apiService.delete(`/employer/jobs/${id}`),
  applications: (params?: Record<string, any>) => 
    apiService.get('/employer/applications', { params }),
  updateApplication: (id: number, data: any) => 
    apiService.put(`/employer/applications/${id}`, data),
  analytics: (params?: Record<string, any>) => 
    apiService.get('/employer/analytics', { params }),
};

export const adminAPI = {
  dashboard: () => apiService.get('/admin/dashboard'),
  users: {
    list: (params?: Record<string, any>) => apiService.get('/admin/users', { params }),
    show: (id: number) => apiService.get(`/admin/users/${id}`),
    update: (id: number, data: any) => apiService.put(`/admin/users/${id}`, data),
    delete: (id: number) => apiService.delete(`/admin/users/${id}`),
    suspend: (id: number) => apiService.post(`/admin/users/${id}/suspend`),
    restore: (id: number) => apiService.post(`/admin/users/${id}/restore`),
  },
  jobs: {
    list: (params?: Record<string, any>) => apiService.get('/admin/jobs', { params }),
    approve: (id: number) => apiService.post(`/admin/jobs/${id}/approve`),
    reject: (id: number, reason: string) => 
      apiService.post(`/admin/jobs/${id}/reject`, { reason }),
    feature: (id: number) => apiService.post(`/admin/jobs/${id}/feature`),
  },
  companies: {
    list: (params?: Record<string, any>) => apiService.get('/admin/companies', { params }),
    verify: (id: number) => apiService.post(`/admin/companies/${id}/verify`),
    reject: (id: number, reason: string) => 
      apiService.post(`/admin/companies/${id}/reject`, { reason }),
  },
  analytics: (params?: Record<string, any>) => 
    apiService.get('/admin/analytics', { params }),
  reports: {
    generate: (type: string, params?: Record<string, any>) => 
      apiService.post('/admin/reports/generate', { type, ...params }),
    download: (id: string) => apiService.get(`/admin/reports/${id}/download`),
  },
}; 