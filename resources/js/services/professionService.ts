import { ApiService } from './api';
import type { AxiosResponse } from 'axios';

// Type definitions for professions
export interface ProfessionCategory {
  id: number;
  code: string;
  name: string;
  description?: string;
  parent_id?: number;
  parent_name?: string;
  level: number;
  sort_order: number;
  is_active: boolean;
  metadata?: {
    icon?: string;
    color?: string;
    [key: string]: any;
  };
  children_count?: number;
  professions_count?: number;
  path?: { id: number; code: string; name: string }[];
  children?: ProfessionCategory[];
  translations?: ProfessionCategoryTranslation[];
  created_at: string;
  updated_at: string;
}

export interface ProfessionCategoryTranslation {
  locale: string;
  language_name: string;
  name: string;
  description?: string;
  is_complete: boolean;
}

export interface Profession {
  id: number;
  code: string;
  isco_code?: string;
  name: string;
  description?: string;
  category_id: number;
  category_name: string;
  category_code: string;
  skill_level: 'High' | 'Medium' | 'Low';
  is_active: boolean;
  is_featured: boolean;
  sort_order: number;
  metadata?: {
    difficulty_level?: 'High' | 'Medium' | 'Low';
    in_high_demand?: boolean;
    [key: string]: any;
  };
  skills_required?: string[];
  education_requirements?: string[];
  jobs_count?: number;
  active_jobs_count?: number;
  path?: { id: number; code: string; name: string }[];
  translations?: ProfessionTranslation[];
  created_at: string;
  updated_at: string;
}

export interface ProfessionTranslation {
  locale: string;
  language_name: string;
  name: string;
  description?: string;
  skills_required?: string[];
  education_requirements?: string[];
  is_complete: boolean;
  completion_percentage: number;
}

export interface ProfessionCategoryRequest {
  code: string;
  parent_id?: number;
  level: number;
  sort_order: number;
  is_active?: boolean;
  metadata?: Record<string, any>;
  translations: Record<string, {
    name: string;
    description?: string;
  }>;
}

export interface ProfessionRequest {
  code: string;
  category_id: number;
  isco_code?: string;
  skill_level: 'High' | 'Medium' | 'Low';
  is_active?: boolean;
  is_featured?: boolean;
  sort_order: number;
  metadata?: Record<string, any>;
  translations: Record<string, {
    name: string;
    description?: string;
    skills_required?: string[];
    education_requirements?: string[];
  }>;
}

export interface ProfessionCategoryFilters {
  level?: number;
  parent_id?: number;
  roots_only?: boolean;
  search?: string;
  locale?: string;
  per_page?: number;
  page?: number;
}

export interface ProfessionFilters {
  category_id?: number;
  skill_level?: 'High' | 'Medium' | 'Low';
  featured_only?: boolean;
  search?: string;
  isco_code?: string;
  locale?: string;
  per_page?: number;
  page?: number;
}

export interface ProfessionSearchFilters {
  q?: string;
  categories?: number[] | string;
  skill_levels?: string[] | string;
  isco_code?: string;
  featured_only?: boolean;
  sort_by?: 'name' | 'sort_order' | 'jobs_count';
  sort_order?: 'asc' | 'desc';
  locale?: string;
  per_page?: number;
  page?: number;
}

export interface ProfessionStatistics {
  total_professions: number;
  active_professions: number;
  featured_professions: number;
  professions_with_jobs: number;
  by_skill_level: Record<string, number>;
  by_category: {
    category_id: number;
    category_name: string;
    professions_count: number;
  }[];
  top_demanded: {
    id: number;
    name: string;
    jobs_count: number;
  }[];
}

export interface BulkUpdateRequest {
  profession_ids: number[];
  updates: {
    is_active?: boolean;
    is_featured?: boolean;
    skill_level?: 'High' | 'Medium' | 'Low';
  };
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  from: number;
  last_page: number;
  per_page: number;
  to: number;
  total: number;
  links: {
    first: string;
    last: string;
    prev?: string;
    next?: string;
  };
}

export interface ApiResponseWrapper<T> {
  success: boolean;
  data: T;
  locale?: string;
  message?: string;
  filters?: any;
  search_params?: any;
}

class ProfessionService {
  private api: ApiService;
  private baseUrl = '/professions';

  constructor() {
    this.api = new ApiService();
  }

  // Profession Categories
  async getCategories(filters?: ProfessionCategoryFilters): Promise<AxiosResponse<ApiResponseWrapper<PaginatedResponse<ProfessionCategory>>>> {
    const params = new URLSearchParams();
    
    if (filters) {
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          params.append(key, String(value));
        }
      });
    }

    return this.api.get(`${this.baseUrl}/categories?${params.toString()}`);
  }

  async createCategory(data: ProfessionCategoryRequest): Promise<AxiosResponse<ApiResponseWrapper<ProfessionCategory>>> {
    return this.api.post(`${this.baseUrl}/categories`, data);
  }

  async getCategory(id: number, locale?: string): Promise<AxiosResponse<ApiResponseWrapper<ProfessionCategory>>> {
    const params = locale ? `?locale=${locale}` : '';
    return this.api.get(`${this.baseUrl}/categories/${id}${params}`);
  }

  async updateCategory(id: number, data: Partial<ProfessionCategoryRequest>): Promise<AxiosResponse<ApiResponseWrapper<ProfessionCategory>>> {
    return this.api.put(`${this.baseUrl}/categories/${id}`, data);
  }

  async deleteCategory(id: number): Promise<AxiosResponse<ApiResponseWrapper<null>>> {
    return this.api.delete(`${this.baseUrl}/categories/${id}`);
  }

  async getCategoryTree(locale?: string): Promise<AxiosResponse<ApiResponseWrapper<ProfessionCategory[]>>> {
    const params = locale ? `?locale=${locale}` : '';
    return this.api.get(`${this.baseUrl}/categories/tree${params}`);
  }

  async getAvailableLanguages(): Promise<AxiosResponse<ApiResponseWrapper<Record<string, string>>>> {
    return this.api.get(`${this.baseUrl}/categories/languages`);
  }

  // Professions
  async getProfessions(filters?: ProfessionFilters): Promise<AxiosResponse<ApiResponseWrapper<PaginatedResponse<Profession>>>> {
    const params = new URLSearchParams();
    
    if (filters) {
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null) {
          params.append(key, String(value));
        }
      });
    }

    return this.api.get(`${this.baseUrl}?${params.toString()}`);
  }

  async createProfession(data: ProfessionRequest): Promise<AxiosResponse<ApiResponseWrapper<Profession>>> {
    return this.api.post(`${this.baseUrl}`, data);
  }

  async getProfession(id: number, locale?: string): Promise<AxiosResponse<ApiResponseWrapper<Profession>>> {
    const params = locale ? `?locale=${locale}` : '';
    return this.api.get(`${this.baseUrl}/${id}${params}`);
  }

  async updateProfession(id: number, data: Partial<ProfessionRequest>): Promise<AxiosResponse<ApiResponseWrapper<Profession>>> {
    return this.api.put(`${this.baseUrl}/${id}`, data);
  }

  async deleteProfession(id: number): Promise<AxiosResponse<ApiResponseWrapper<null>>> {
    return this.api.delete(`${this.baseUrl}/${id}`);
  }

  async searchProfessions(filters: ProfessionSearchFilters): Promise<AxiosResponse<ApiResponseWrapper<PaginatedResponse<Profession>>>> {
    const params = new URLSearchParams();
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        if (Array.isArray(value)) {
          params.append(key, value.join(','));
        } else {
          params.append(key, String(value));
        }
      }
    });

    return this.api.get(`${this.baseUrl}/search?${params.toString()}`);
  }

  async getProfessionStatistics(locale?: string): Promise<AxiosResponse<ApiResponseWrapper<ProfessionStatistics>>> {
    const params = locale ? `?locale=${locale}` : '';
    return this.api.get(`${this.baseUrl}/statistics${params}`);
  }

  async bulkUpdateProfessions(request: BulkUpdateRequest): Promise<AxiosResponse<ApiResponseWrapper<{ updated_count: number }>>> {
    return this.api.post(`${this.baseUrl}/bulk-update`, request);
  }

  // Utility methods
  async refreshData(): Promise<void> {
    // Clear any cached data if using caching
    // This would be called after updates to ensure fresh data
  }

  // Search suggestions (debounced search for autocomplete)
  async getSearchSuggestions(query: string, locale?: string, limit: number = 10): Promise<AxiosResponse<ApiResponseWrapper<{ categories: ProfessionCategory[]; professions: Profession[] }>>> {
    const filters: ProfessionSearchFilters = {
      q: query,
      locale,
      per_page: limit,
    };

    // This could be optimized with a dedicated suggestion endpoint
    const response = await this.searchProfessions(filters);
    const professions = response.data.data.data;

    // Also search categories
    const categoryResponse = await this.getCategories({
      search: query,
      locale,
      per_page: limit,
    });
    const categories = categoryResponse.data.data.data;

    return {
      ...response,
      data: {
        ...response.data,
        data: {
          categories,
          professions,
        },
      },
    };
  }

  // Export/Import helpers (for future use)
  async exportData(format: 'json' | 'csv' = 'json', locale?: string): Promise<Blob> {
    const response = await this.api.get(`${this.baseUrl}/export`, {
      params: { format, locale },
      responseType: 'blob',
    });
    return response.data;
  }

  // Validation helpers
  validateCategoryCode(code: string): boolean {
    return /^[0-9]+$/.test(code) && code.length <= 10;
  }

  validateProfessionCode(code: string): boolean {
    return /^[0-9]+$/.test(code) && code.length <= 20;
  }

  validateISCOCode(code: string): boolean {
    return /^[0-9]{4}$/.test(code);
  }
}

// Export singleton instance
export const professionService = new ProfessionService();
export default professionService; 