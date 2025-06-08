import { defineStore } from "pinia"
import { ref, computed } from "vue"
import { jobsApi } from "../services/api"
import type { ApiResponse, PaginatedResponse } from "../types/api"

interface Job {
  id: number
  title: string
  company: string
  company_id: number
  location: string
  type: string
  description: string
  salary_from?: number
  salary_to?: number
  salary_currency?: string
  requirements?: string
  benefits?: string
  is_active: boolean
  is_featured: boolean
  created_at: string
  updated_at: string
  applications_count?: number
}

interface JobFilters {
  search: string
  location: string
  category: string
  type: string
  salary_min?: number
  salary_max?: number
  company_id?: number
}

interface JobSearchParams extends Partial<JobFilters> {
  page?: number
  per_page?: number
  sort?: string
  order?: 'asc' | 'desc'
}

export const useJobsStore = defineStore("jobs", () => {
  // State
  const jobs = ref<Job[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const currentPage = ref(1)
  const lastPage = ref(1)
  const perPage = ref(12)
  const total = ref(0)
  
  const filters = ref<JobFilters>({
    search: "",
    location: "",
    category: "",
    type: ""
  })

  // Getters
  const filteredJobs = computed(() => {
    if (!filters.value.search && !filters.value.location && !filters.value.category && !filters.value.type) {
      return jobs.value
    }

    return jobs.value.filter((job: Job) => {
      const matchesSearch = !filters.value.search || 
        job.title.toLowerCase().includes(filters.value.search.toLowerCase()) ||
        job.description.toLowerCase().includes(filters.value.search.toLowerCase())
      
      const matchesLocation = !filters.value.location || 
        job.location.toLowerCase().includes(filters.value.location.toLowerCase())
      
      const matchesType = !filters.value.type || job.type === filters.value.type

      return matchesSearch && matchesLocation && matchesType
    })
  })

  const featuredJobs = computed(() => {
    return jobs.value.filter((job: Job) => job.is_featured && job.is_active).slice(0, 6)
  })

  const activeJobs = computed(() => {
    return jobs.value.filter((job: Job) => job.is_active)
  })

  const pagination = computed(() => ({
    currentPage: currentPage.value,
    lastPage: lastPage.value,
    perPage: perPage.value,
    total: total.value,
    hasNextPage: currentPage.value < lastPage.value,
    hasPrevPage: currentPage.value > 1
  }))

  // Actions
  const fetchJobs = async (params: JobSearchParams = {}) => {
    loading.value = true
    error.value = null
    
    try {
      console.log('Fetching jobs with params:', params)
      
      const response: PaginatedResponse<Job> = await jobsApi.getAll({
        page: currentPage.value,
        per_page: perPage.value,
        ...params
      })
      
      if (response.data) {
        jobs.value = response.data
        currentPage.value = response.current_page || 1
        lastPage.value = response.last_page || 1
        perPage.value = response.per_page || 12
        total.value = response.total || 0
        
        console.log(`Loaded ${jobs.value.length} jobs (page ${currentPage.value} of ${lastPage.value})`)
      } else {
        jobs.value = []
        console.warn('No jobs data received from API')
      }
    } catch (err: any) {
      console.error('Failed to fetch jobs:', err)
      error.value = err.response?.data?.message || err.message || "Failed to fetch jobs"
      jobs.value = []
    } finally {
      loading.value = false
    }
  }

  const searchJobs = async (searchParams: Partial<JobFilters>) => {
    console.log('Searching jobs with filters:', searchParams)
    
    // Update filters
    Object.assign(filters.value, searchParams)
    
    // Reset to first page for new search
    currentPage.value = 1
    
    // Fetch jobs with search parameters
    await fetchJobs({
      search: filters.value.search,
      location: filters.value.location,
      category: filters.value.category,
      type: filters.value.type,
      salary_min: filters.value.salary_min,
      salary_max: filters.value.salary_max,
      company_id: filters.value.company_id
    })
  }

  const getJobById = async (id: number): Promise<Job | null> => {
    try {
      loading.value = true
      console.log('Fetching job details for ID:', id)
      
      const response: ApiResponse<Job> = await jobsApi.getById(id)
      
      if (response.success && response.data) {
        console.log('Job details loaded:', response.data.title)
        return response.data
      } else {
        console.warn('Job not found or invalid response')
        return null
      }
    } catch (err: any) {
      console.error('Failed to fetch job details:', err)
      error.value = err.response?.data?.message || err.message || "Failed to fetch job details"
      return null
    } finally {
      loading.value = false
    }
  }

  const createJob = async (jobData: Partial<Job>): Promise<boolean> => {
    try {
      loading.value = true
      console.log('Creating new job:', jobData.title)
      
      const response: ApiResponse<Job> = await jobsApi.create(jobData)
      
      if (response.success && response.data) {
        // Add new job to the list
        jobs.value.unshift(response.data)
        console.log('Job created successfully:', response.data.title)
        return true
      } else {
        error.value = response.message || "Failed to create job"
        return false
      }
    } catch (err: any) {
      console.error('Failed to create job:', err)
      error.value = err.response?.data?.message || err.message || "Failed to create job"
      return false
    } finally {
      loading.value = false
    }
  }

  const updateJob = async (id: number, jobData: Partial<Job>): Promise<boolean> => {
    try {
      loading.value = true
      console.log('Updating job ID:', id)
      
      const response: ApiResponse<Job> = await jobsApi.update(id, jobData)
      
      if (response.success && response.data) {
        // Update job in the list
        const index = jobs.value.findIndex(job => job.id === id)
        if (index !== -1) {
          jobs.value[index] = response.data
        }
        console.log('Job updated successfully:', response.data.title)
        return true
      } else {
        error.value = response.message || "Failed to update job"
        return false
      }
    } catch (err: any) {
      console.error('Failed to update job:', err)
      error.value = err.response?.data?.message || err.message || "Failed to update job"
      return false
    } finally {
      loading.value = false
    }
  }

  const deleteJob = async (id: number): Promise<boolean> => {
    try {
      loading.value = true
      console.log('Deleting job ID:', id)
      
      const response: ApiResponse = await jobsApi.delete(id)
      
      if (response.success) {
        // Remove job from the list
        jobs.value = jobs.value.filter(job => job.id !== id)
        total.value = Math.max(0, total.value - 1)
        console.log('Job deleted successfully')
        return true
      } else {
        error.value = response.message || "Failed to delete job"
        return false
      }
    } catch (err: any) {
      console.error('Failed to delete job:', err)
      error.value = err.response?.data?.message || err.message || "Failed to delete job"
      return false
    } finally {
      loading.value = false
    }
  }

  const goToPage = async (page: number) => {
    if (page >= 1 && page <= lastPage.value && page !== currentPage.value) {
      currentPage.value = page
      await fetchJobs()
    }
  }

  const nextPage = async () => {
    if (currentPage.value < lastPage.value) {
      await goToPage(currentPage.value + 1)
    }
  }

  const prevPage = async () => {
    if (currentPage.value > 1) {
      await goToPage(currentPage.value - 1)
    }
  }

  const clearError = () => {
    error.value = null
  }

  const clearFilters = () => {
    filters.value = {
      search: "",
      location: "",
      category: "",
      type: ""
    }
  }

  const refreshJobs = async () => {
    await fetchJobs()
  }

  return {
    // State
    jobs,
    loading,
    error,
    currentPage,
    lastPage,
    perPage,
    total,
    filters,
    
    // Getters
    filteredJobs,
    featuredJobs,
    activeJobs,
    pagination,
    
    // Actions
    fetchJobs,
    searchJobs,
    getJobById,
    createJob,
    updateJob,
    deleteJob,
    goToPage,
    nextPage,
    prevPage,
    clearError,
    clearFilters,
    refreshJobs
  }
})