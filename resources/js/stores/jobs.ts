import { defineStore } from "pinia"
import { ref, computed } from "vue"

interface Job {
  id: number
  title: string
  company: string
  location: string
  type: string
  description: string
  salary?: string
  created_at: string
  is_featured: boolean
}

interface JobFilters {
  search: string
  location: string
  category: string
  type: string
}

export const useJobsStore = defineStore("jobs", () => {
  const jobs = ref<Job[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const filters = ref<JobFilters>({
    search: "",
    location: "",
    category: "",
    type: ""
  })

  const filteredJobs = computed(() => {
    return jobs.value.filter(job => {
      const matchesSearch = !filters.value.search || 
        job.title.toLowerCase().includes(filters.value.search.toLowerCase()) ||
        job.company.toLowerCase().includes(filters.value.search.toLowerCase())
      
      const matchesLocation = !filters.value.location || 
        job.location.toLowerCase().includes(filters.value.location.toLowerCase())
      
      const matchesType = !filters.value.type || job.type === filters.value.type

      return matchesSearch && matchesLocation && matchesType
    })
  })

  const featuredJobs = computed(() => {
    return jobs.value.filter(job => job.is_featured).slice(0, 6)
  })

  const fetchJobs = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch("/api/jobs", {
        headers: { "Accept": "application/json" }
      })
      
      if (!response.ok) {
        throw new Error("Failed to fetch jobs")
      }
      
      const data = await response.json()
      jobs.value = data.data || data
    } catch (err) {
      error.value = err instanceof Error ? err.message : "Unknown error"
    } finally {
      loading.value = false
    }
  }

  const searchJobs = async (searchParams: Partial<JobFilters>) => {
    Object.assign(filters.value, searchParams)
    await fetchJobs()
  }

  const getJobById = (id: number) => {
    return jobs.value.find(job => job.id === id)
  }

  return {
    jobs,
    loading,
    error,
    filters,
    filteredJobs,
    featuredJobs,
    fetchJobs,
    searchJobs,
    getJobById
  }
})