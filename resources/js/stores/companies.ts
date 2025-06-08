import { defineStore } from "pinia"
import { ref, computed } from "vue"
import { companiesApi } from "../services/api"
import type { ApiResponse, PaginatedResponse } from "../types/api"

interface Company {
  id: number
  name: string
  slug: string
  description?: string
  industry?: string
  website?: string
  email?: string
  phone?: string
  location?: string
  address?: string
  logo?: string
  cover_image?: string
  company_size?: string
  founded_year?: number
  is_active: boolean
  is_featured: boolean
  job_count?: number
  created_at: string
  updated_at: string
}

interface CompanyFilters {
  search: string
  industry: string
  location: string
  company_size: string
}

interface CompanySearchParams extends Partial<CompanyFilters> {
  page?: number
  per_page?: number
  sort?: string
  order?: 'asc' | 'desc'
}

export const useCompaniesStore = defineStore("companies", () => {
  // State
  const companies = ref<Company[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)
  const currentPage = ref(1)
  const lastPage = ref(1)
  const perPage = ref(12)
  const total = ref(0)
  
  const filters = ref<CompanyFilters>({
    search: "",
    industry: "",
    location: "",
    company_size: ""
  })

  // Getters
  const filteredCompanies = computed(() => {
    if (!filters.value.search && !filters.value.industry && !filters.value.location && !filters.value.company_size) {
      return companies.value
    }

    return companies.value.filter((company: Company) => {
      const matchesSearch = !filters.value.search || 
        company.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
        (company.description && company.description.toLowerCase().includes(filters.value.search.toLowerCase()))
      
      const matchesIndustry = !filters.value.industry || 
        (company.industry && company.industry.toLowerCase().includes(filters.value.industry.toLowerCase()))
      
      const matchesLocation = !filters.value.location || 
        (company.location && company.location.toLowerCase().includes(filters.value.location.toLowerCase()))

      const matchesSize = !filters.value.company_size || company.company_size === filters.value.company_size

      return matchesSearch && matchesIndustry && matchesLocation && matchesSize
    })
  })

  const featuredCompanies = computed(() => {
    return companies.value.filter((company: Company) => company.is_featured && company.is_active).slice(0, 8)
  })

  const activeCompanies = computed(() => {
    return companies.value.filter((company: Company) => company.is_active)
  })

  const topCompanies = computed(() => {
    return companies.value
      .filter((company: Company) => company.is_active && (company.job_count || 0) > 0)
      .sort((a: Company, b: Company) => (b.job_count || 0) - (a.job_count || 0))
      .slice(0, 10)
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
  const fetchCompanies = async (params: CompanySearchParams = {}) => {
    loading.value = true
    error.value = null
    
    try {
      console.log('Fetching companies with params:', params)
      
      const response: PaginatedResponse<Company> = await companiesApi.getAll({
        page: currentPage.value,
        per_page: perPage.value,
        ...params
      })
      
      if (response.data) {
        companies.value = response.data
        currentPage.value = response.current_page || 1
        lastPage.value = response.last_page || 1
        perPage.value = response.per_page || 12
        total.value = response.total || 0
        
        console.log(`Loaded ${companies.value.length} companies (page ${currentPage.value} of ${lastPage.value})`)
      } else {
        companies.value = []
        console.warn('No companies data received from API')
      }
    } catch (err: any) {
      console.error('Failed to fetch companies:', err)
      error.value = err.response?.data?.message || err.message || "Failed to fetch companies"
      companies.value = []
    } finally {
      loading.value = false
    }
  }

  const searchCompanies = async (searchParams: Partial<CompanyFilters>) => {
    console.log('Searching companies with filters:', searchParams)
    
    // Update filters
    Object.assign(filters.value, searchParams)
    
    // Reset to first page for new search
    currentPage.value = 1
    
    // Fetch companies with search parameters
    await fetchCompanies({
      search: filters.value.search,
      industry: filters.value.industry,
      location: filters.value.location,
      company_size: filters.value.company_size
    })
  }

  const getCompanyById = async (id: number): Promise<Company | null> => {
    try {
      loading.value = true
      console.log('Fetching company details for ID:', id)
      
      const response: ApiResponse<Company> = await companiesApi.getById(id)
      
      if (response.success && response.data) {
        console.log('Company details loaded:', response.data.name)
        return response.data
      } else {
        console.warn('Company not found or invalid response')
        return null
      }
    } catch (err: any) {
      console.error('Failed to fetch company details:', err)
      error.value = err.response?.data?.message || err.message || "Failed to fetch company details"
      return null
    } finally {
      loading.value = false
    }
  }

  const createCompany = async (companyData: Partial<Company>): Promise<boolean> => {
    try {
      loading.value = true
      console.log('Creating new company:', companyData.name)
      
      const response: ApiResponse<Company> = await companiesApi.create(companyData)
      
      if (response.success && response.data) {
        // Add new company to the list
        companies.value.unshift(response.data)
        console.log('Company created successfully:', response.data.name)
        return true
      } else {
        error.value = response.message || "Failed to create company"
        return false
      }
    } catch (err: any) {
      console.error('Failed to create company:', err)
      error.value = err.response?.data?.message || err.message || "Failed to create company"
      return false
    } finally {
      loading.value = false
    }
  }

  const updateCompany = async (id: number, companyData: Partial<Company>): Promise<boolean> => {
    try {
      loading.value = true
      console.log('Updating company ID:', id)
      
      const response: ApiResponse<Company> = await companiesApi.update(id, companyData)
      
      if (response.success && response.data) {
        // Update company in the list
        const index = companies.value.findIndex((company: Company) => company.id === id)
        if (index !== -1) {
          companies.value[index] = response.data
        }
        console.log('Company updated successfully:', response.data.name)
        return true
      } else {
        error.value = response.message || "Failed to update company"
        return false
      }
    } catch (err: any) {
      console.error('Failed to update company:', err)
      error.value = err.response?.data?.message || err.message || "Failed to update company"
      return false
    } finally {
      loading.value = false
    }
  }

  const deleteCompany = async (id: number): Promise<boolean> => {
    try {
      loading.value = true
      console.log('Deleting company ID:', id)
      
      const response: ApiResponse = await companiesApi.delete(id)
      
      if (response.success) {
        // Remove company from the list
        companies.value = companies.value.filter((company: Company) => company.id !== id)
        total.value = Math.max(0, total.value - 1)
        console.log('Company deleted successfully')
        return true
      } else {
        error.value = response.message || "Failed to delete company"
        return false
      }
    } catch (err: any) {
      console.error('Failed to delete company:', err)
      error.value = err.response?.data?.message || err.message || "Failed to delete company"
      return false
    } finally {
      loading.value = false
    }
  }

  const goToPage = async (page: number) => {
    if (page >= 1 && page <= lastPage.value && page !== currentPage.value) {
      currentPage.value = page
      await fetchCompanies()
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
      industry: "",
      location: "",
      company_size: ""
    }
  }

  const refreshCompanies = async () => {
    await fetchCompanies()
  }

  return {
    // State
    companies,
    loading,
    error,
    currentPage,
    lastPage,
    perPage,
    total,
    filters,
    
    // Getters
    filteredCompanies,
    featuredCompanies,
    activeCompanies,
    topCompanies,
    pagination,
    
    // Actions
    fetchCompanies,
    searchCompanies,
    getCompanyById,
    createCompany,
    updateCompany,
    deleteCompany,
    goToPage,
    nextPage,
    prevPage,
    clearError,
    clearFilters,
    refreshCompanies
  }
})