import axios from 'axios'

// Types
interface Job {
  id?: number
  title: string
  job_category_id: number | null
  job_type_id: number | null
  career_level_id: number | null
  description: string
  key_responsibilities: string
  salary_from: number | null
  salary_to: number | null
  salary_currency_id: number | null
  country_id: number | null
  state_id: number | null
  city_id: number | null
  degree_level_id: number | null
  status: 'draft' | 'published'
  company_id?: number
  created_at?: string
  updated_at?: string
}

interface JobApplication {
  id: number
  job_id: number
  candidate_id: number
  status: string
  applied_at: string
  candidate?: {
    id: number
    first_name: string
    last_name: string
    email: string
  }
}

interface JobCategory {
  id: number
  name: string
  description?: string
}

interface JobType {
  id: number
  name: string
  description?: string
}

interface CareerLevel {
  id: number
  level_name: string
  description?: string
}

interface Country {
  id: number
  name: string
  code: string
}

interface State {
  id: number
  name: string
  country_id: number
}

interface City {
  id: number
  name: string
  state_id: number
}

interface SalaryCurrency {
  id: number
  currency_name: string
  currency_code: string
  currency_symbol: string
}

interface DegreeLevel {
  id: number
  name: string
  description?: string
}

// Search and Filter types
interface JobSearchFilters {
  title?: string
  job_category_id?: number
  job_type_id?: number
  career_level_id?: number
  country_id?: number
  state_id?: number
  city_id?: number
  salary_from?: number
  salary_to?: number
  status?: string
  company_id?: number
  is_featured?: boolean
  date_from?: string
  date_to?: string
}

interface PaginationParams {
  page?: number
  per_page?: number
  sort_by?: string
  sort_direction?: 'asc' | 'desc'
}

// API Response wrapper
interface ApiResponse<T = any> {
  data: T
  success: boolean
  message?: string
  errors?: Record<string, string[]>
}

interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  last_page: number
  per_page: number
  total: number
  from: number
  to: number
}

class JobService {
  private readonly baseURL = '/api/jobs'
  private readonly employerURL = '/api/employer/jobs'
  private readonly adminURL = '/api/admin/jobs'

  // Public job operations (for candidates/visitors)
  async getPublicJobs(filters?: JobSearchFilters, pagination?: PaginationParams): Promise<ApiResponse<PaginatedResponse<Job>>> {
    const params = { ...filters, ...pagination }
    const response = await axios.get(this.baseURL, { params })
    return response.data
  }

  async getJobById(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.get(`${this.baseURL}/${id}`)
    return response.data
  }

  async applyForJob(jobId: number, applicationData: { resume_id?: number; cover_letter?: string }): Promise<ApiResponse> {
    const response = await axios.post(`${this.baseURL}/${jobId}/apply`, applicationData)
    return response.data
  }

  // Employer job operations
  async getEmployerJobs(filters?: JobSearchFilters, pagination?: PaginationParams): Promise<ApiResponse<PaginatedResponse<Job>>> {
    const params = { ...filters, ...pagination }
    const response = await axios.get(this.employerURL, { params })
    return response.data
  }

  async createJob(job: Job): Promise<ApiResponse<Job>> {
    const response = await axios.post(this.employerURL, job)
    return response.data
  }

  async updateJob(id: number, job: Partial<Job>): Promise<ApiResponse<Job>> {
    const response = await axios.put(`${this.employerURL}/${id}`, job)
    return response.data
  }

  async deleteJob(id: number): Promise<ApiResponse> {
    const response = await axios.delete(`${this.employerURL}/${id}`)
    return response.data
  }

  async getEmployerJobById(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.get(`${this.employerURL}/${id}`)
    return response.data
  }

  async publishJob(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.post(`${this.employerURL}/${id}/publish`)
    return response.data
  }

  async unpublishJob(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.post(`${this.employerURL}/${id}/unpublish`)
    return response.data
  }

  async markAsFeatured(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.post(`${this.employerURL}/${id}/feature`)
    return response.data
  }

  async unmarkAsFeatured(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.post(`${this.employerURL}/${id}/unfeature`)
    return response.data
  }

  // Job applications management
  async getJobApplications(jobId: number, pagination?: PaginationParams): Promise<ApiResponse<PaginatedResponse<JobApplication>>> {
    const params = { ...pagination }
    const response = await axios.get(`${this.employerURL}/${jobId}/applications`, { params })
    return response.data
  }

  async updateApplicationStatus(jobId: number, applicationId: number, status: string): Promise<ApiResponse<JobApplication>> {
    const response = await axios.put(`${this.employerURL}/${jobId}/applications/${applicationId}/status`, { status })
    return response.data
  }

  async downloadApplicationResume(jobId: number, applicationId: number): Promise<Blob> {
    const response = await axios.get(`${this.employerURL}/${jobId}/applications/${applicationId}/resume`, {
      responseType: 'blob'
    })
    return response.data
  }

  // Admin job operations (if user has admin privileges)
  async getAdminJobs(filters?: JobSearchFilters, pagination?: PaginationParams): Promise<ApiResponse<PaginatedResponse<Job>>> {
    const params = { ...filters, ...pagination }
    const response = await axios.get(this.adminURL, { params })
    return response.data
  }

  async approveJob(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.post(`${this.adminURL}/${id}/approve`)
    return response.data
  }

  async rejectJob(id: number, reason?: string): Promise<ApiResponse<Job>> {
    const response = await axios.post(`${this.adminURL}/${id}/reject`, { reason })
    return response.data
  }

  // Master data operations
  async getJobCategories(): Promise<ApiResponse<JobCategory[]>> {
    const response = await axios.get('/api/job-categories')
    return response.data
  }

  async getJobTypes(): Promise<ApiResponse<JobType[]>> {
    const response = await axios.get('/api/job-types')
    return response.data
  }

  async getCareerLevels(): Promise<ApiResponse<CareerLevel[]>> {
    const response = await axios.get('/api/career-levels')
    return response.data
  }

  async getCountries(): Promise<ApiResponse<Country[]>> {
    const response = await axios.get('/api/countries')
    return response.data
  }

  async getStates(countryId: number): Promise<ApiResponse<State[]>> {
    const response = await axios.get(`/api/countries/${countryId}/states`)
    return response.data
  }

  async getCities(stateId: number): Promise<ApiResponse<City[]>> {
    const response = await axios.get(`/api/states/${stateId}/cities`)
    return response.data
  }

  async getSalaryCurrencies(): Promise<ApiResponse<SalaryCurrency[]>> {
    const response = await axios.get('/api/salary-currencies')
    return response.data
  }

  async getDegreeLevels(): Promise<ApiResponse<DegreeLevel[]>> {
    const response = await axios.get('/api/degree-levels')
    return response.data
  }

  // Analytics and reporting
  async getJobStats(jobId: number): Promise<ApiResponse<{
    total_applications: number
    new_applications: number
    viewed_applications: number
    shortlisted_applications: number
    rejected_applications: number
    views_count: number
    saves_count: number
  }>> {
    const response = await axios.get(`${this.employerURL}/${jobId}/stats`)
    return response.data
  }

  async getEmployerJobsAnalytics(dateRange?: { from: string; to: string }): Promise<ApiResponse<{
    total_jobs: number
    active_jobs: number
    draft_jobs: number
    total_applications: number
    total_views: number
    applications_by_day: Array<{ date: string; count: number }>
    top_performing_jobs: Array<{ id: number; title: string; applications_count: number }>
  }>> {
    const params = dateRange ? { date_from: dateRange.from, date_to: dateRange.to } : {}
    const response = await axios.get(`${this.employerURL}/analytics`, { params })
    return response.data
  }

  // Bulk operations
  async bulkDeleteJobs(jobIds: number[]): Promise<ApiResponse> {
    const response = await axios.post(`${this.employerURL}/bulk-delete`, { job_ids: jobIds })
    return response.data
  }

  async bulkUpdateStatus(jobIds: number[], status: string): Promise<ApiResponse> {
    const response = await axios.post(`${this.employerURL}/bulk-status`, { job_ids: jobIds, status })
    return response.data
  }

  async duplicateJob(id: number): Promise<ApiResponse<Job>> {
    const response = await axios.post(`${this.employerURL}/${id}/duplicate`)
    return response.data
  }

  // Search and filtering helpers
  async searchJobs(query: string, filters?: JobSearchFilters): Promise<ApiResponse<Job[]>> {
    const params = { q: query, ...filters }
    const response = await axios.get(`${this.baseURL}/search`, { params })
    return response.data
  }

  async getSimilarJobs(jobId: number, limit = 5): Promise<ApiResponse<Job[]>> {
    const response = await axios.get(`${this.baseURL}/${jobId}/similar`, { params: { limit } })
    return response.data
  }

  async getFeaturedJobs(limit = 10): Promise<ApiResponse<Job[]>> {
    const response = await axios.get(`${this.baseURL}/featured`, { params: { limit } })
    return response.data
  }

  async getRecentJobs(limit = 10): Promise<ApiResponse<Job[]>> {
    const response = await axios.get(`${this.baseURL}/recent`, { params: { limit } })
    return response.data
  }
}

// Export singleton instance
export const jobService = new JobService()
export default jobService

// Export types
export type {
  Job,
  JobApplication,
  JobCategory,
  JobType,
  CareerLevel,
  Country,
  State,
  City,
  SalaryCurrency,
  DegreeLevel,
  JobSearchFilters,
  PaginationParams,
  ApiResponse,
  PaginatedResponse
} 