import axios from 'axios'

// Types
interface Experience {
  id?: number
  title: string
  company: string
  startDateExperience: string
  endDateExperience: string
  country: string
  description: string
}

interface Education {
  id?: number
  degreeLevel: string
  degreeTitle: string
  year: string
  country: string
  institute: string
}

interface CandidateProfile {
  id?: number
  first_name: string
  last_name: string
  email: string
  phone?: string
  address?: string
  city?: string
  country?: string
}

// API Response wrapper
interface ApiResponse<T = any> {
  data: T
  success: boolean
  message?: string
  errors?: Record<string, string[]>
}

class CandidateService {
  private readonly baseURL = '/api/candidate'

  // Profile operations
  async getProfile(): Promise<ApiResponse<CandidateProfile>> {
    const response = await axios.get(`${this.baseURL}/profile`)
    return response.data
  }

  async updateProfile(profile: Partial<CandidateProfile>): Promise<ApiResponse<CandidateProfile>> {
    const response = await axios.put(`${this.baseURL}/profile`, profile)
    return response.data
  }

  async changePassword(data: { current_password: string; password: string; password_confirmation: string }): Promise<ApiResponse> {
    const response = await axios.post(`${this.baseURL}/change-password`, data)
    return response.data
  }

  // Experience operations
  async getExperiences(): Promise<ApiResponse<Experience[]>> {
    const response = await axios.get(`${this.baseURL}/experiences`)
    return response.data
  }

  async createExperience(experience: Experience): Promise<ApiResponse<Experience>> {
    const response = await axios.post(`${this.baseURL}/experiences`, experience)
    return response.data
  }

  async updateExperience(id: number, experience: Partial<Experience>): Promise<ApiResponse<Experience>> {
    const response = await axios.put(`${this.baseURL}/experiences/${id}`, experience)
    return response.data
  }

  async deleteExperience(id: number): Promise<ApiResponse> {
    const response = await axios.delete(`${this.baseURL}/experiences/${id}`)
    return response.data
  }

  // Education operations
  async getEducations(): Promise<ApiResponse<Education[]>> {
    const response = await axios.get(`${this.baseURL}/educations`)
    return response.data
  }

  async createEducation(education: Education): Promise<ApiResponse<Education>> {
    const response = await axios.post(`${this.baseURL}/educations`, education)
    return response.data
  }

  async updateEducation(id: number, education: Partial<Education>): Promise<ApiResponse<Education>> {
    const response = await axios.put(`${this.baseURL}/educations/${id}`, education)
    return response.data
  }

  async deleteEducation(id: number): Promise<ApiResponse> {
    const response = await axios.delete(`${this.baseURL}/educations/${id}`)
    return response.data
  }

  // File operations
  async uploadResume(file: File): Promise<ApiResponse<{ resume_url: string }>> {
    const formData = new FormData()
    formData.append('resume', file)
    
    const response = await axios.post(`${this.baseURL}/upload-resume`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    return response.data
  }

  async deleteResume(id: number): Promise<ApiResponse> {
    const response = await axios.delete(`${this.baseURL}/resumes/${id}`)
    return response.data
  }

  async downloadResume(id: number): Promise<Blob> {
    const response = await axios.get(`${this.baseURL}/resumes/${id}/download`, {
      responseType: 'blob'
    })
    return response.data
  }

  // Language update
  async updateLanguage(locale: string): Promise<ApiResponse> {
    const response = await axios.post('/api/update-language', { locale })
    return response.data
  }
}

// Export singleton instance
export const candidateService = new CandidateService()
export default candidateService

// Export types
export type {
  Experience,
  Education,
  CandidateProfile,
  ApiResponse
} 