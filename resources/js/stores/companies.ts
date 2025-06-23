import { defineStore } from "pinia"
import { ref, computed } from "vue"

interface Company {
  id: number
  name: string
  logo?: string
  industry: string
  description: string
  website?: string
  location: string
  job_count: number
  is_featured: boolean
}

export const useCompaniesStore = defineStore("companies", () => {
  const companies = ref<Company[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const featuredCompanies = computed(() => {
    return companies.value.filter(company => company.is_featured).slice(0, 8)
  })

  const fetchCompanies = async () => {
    loading.value = true
    error.value = null
    
    try {
      const response = await fetch("/api/companies", {
        headers: { "Accept": "application/json" }
      })
      
      if (!response.ok) {
        throw new Error("Failed to fetch companies")
      }
      
      const data = await response.json()
      companies.value = data.data || data
    } catch (err) {
      error.value = err instanceof Error ? err.message : "Unknown error"
    } finally {
      loading.value = false
    }
  }

  const getCompanyById = (id: number) => {
    return companies.value.find(company => company.id === id)
  }

  return {
    companies,
    loading,
    error,
    featuredCompanies,
    fetchCompanies,
    getCompanyById
  }
})