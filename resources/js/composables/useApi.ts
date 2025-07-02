import { ref, computed, watchEffect, onUnmounted } from 'vue';
import { apiService } from '@/services/api';
import type { AxiosResponse, AxiosRequestConfig } from 'axios';
import type { ApiError } from '@/types/user';

interface UseApiOptions {
  immediate?: boolean;
  cache?: boolean;
  cacheKey?: string;
  cacheTTL?: number; // in milliseconds
  retry?: number;
  retryDelay?: number;
  transform?: (data: any) => any;
  onError?: (error: ApiError) => void;
  onSuccess?: (data: any) => void;
}

interface UseApiReturn<T> {
  data: ComputedRef<T | null>;
  loading: ComputedRef<boolean>;
  error: ComputedRef<ApiError | null>;
  execute: () => Promise<void>;
  refresh: () => Promise<void>;
  reset: () => void;
}

// Simple cache implementation
class ApiCache {
  private cache = new Map<string, { data: any; timestamp: number; ttl: number }>();

  set(key: string, data: any, ttl: number = 300000): void { // 5 minutes default
    this.cache.set(key, {
      data,
      timestamp: Date.now(),
      ttl
    });
  }

  get(key: string): any | null {
    const cached = this.cache.get(key);
    if (!cached) return null;

    const now = Date.now();
    if (now - cached.timestamp > cached.ttl) {
      this.cache.delete(key);
      return null;
    }

    return cached.data;
  }

  delete(key: string): void {
    this.cache.delete(key);
  }

  clear(): void {
    this.cache.clear();
  }

  has(key: string): boolean {
    const cached = this.cache.get(key);
    if (!cached) return false;

    const now = Date.now();
    if (now - cached.timestamp > cached.ttl) {
      this.cache.delete(key);
      return false;
    }

    return true;
  }
}

const apiCache = new ApiCache();

export function useApi<T = any>(
  requestFn: () => Promise<AxiosResponse<T>>,
  options: UseApiOptions = {}
): UseApiReturn<T> {
  const {
    immediate = false,
    cache = false,
    cacheKey,
    cacheTTL = 300000,
    retry = 0,
    retryDelay = 1000,
    transform,
    onError,
    onSuccess
  } = options;

  const data = ref<T | null>(null);
  const loading = ref(false);
  const error = ref<ApiError | null>(null);

  // Generate cache key if not provided
  const finalCacheKey = cacheKey || (cache ? JSON.stringify(requestFn.toString()) : null);

  const execute = async (): Promise<void> => {
    loading.value = true;
    error.value = null;

    // Check cache first
    if (cache && finalCacheKey && apiCache.has(finalCacheKey)) {
      const cachedData = apiCache.get(finalCacheKey);
      if (cachedData !== null) {
        data.value = transform ? transform(cachedData) : cachedData;
        loading.value = false;
        if (onSuccess) onSuccess(data.value);
        return;
      }
    }

    let attempt = 0;
    const maxAttempts = retry + 1;

    while (attempt < maxAttempts) {
      try {
        const response = await requestFn();
        const responseData = response.data;
        
        // Transform data if transformer provided
        data.value = transform ? transform(responseData) : responseData;
        
        // Cache the result
        if (cache && finalCacheKey) {
          apiCache.set(finalCacheKey, responseData, cacheTTL);
        }

        if (onSuccess) onSuccess(data.value);
        break;

      } catch (err: any) {
        attempt++;
        
        if (attempt >= maxAttempts) {
          error.value = err as ApiError;
          if (onError) onError(error.value);
        } else {
          // Wait before retry
          await new Promise(resolve => setTimeout(resolve, retryDelay * attempt));
        }
      }
    }

    loading.value = false;
  };

  const refresh = async (): Promise<void> => {
    // Clear cache for this request
    if (cache && finalCacheKey) {
      apiCache.delete(finalCacheKey);
    }
    await execute();
  };

  const reset = (): void => {
    data.value = null;
    loading.value = false;
    error.value = null;
    
    if (cache && finalCacheKey) {
      apiCache.delete(finalCacheKey);
    }
  };

  // Execute immediately if requested
  if (immediate) {
    execute();
  }

  return {
    data: computed(() => data.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    execute,
    refresh,
    reset
  };
}

// Specialized composables for common patterns

export function useApiGet<T = any>(
  url: string,
  config?: AxiosRequestConfig,
  options?: UseApiOptions
): UseApiReturn<T> {
  return useApi<T>(() => apiService.get<T>(url, config), options);
}

export function useApiPost<T = any>(
  url: string,
  data?: any,
  config?: AxiosRequestConfig,
  options?: UseApiOptions
): UseApiReturn<T> {
  return useApi<T>(() => apiService.post<T>(url, data, config), options);
}

export function useApiPut<T = any>(
  url: string,
  data?: any,
  config?: AxiosRequestConfig,
  options?: UseApiOptions
): UseApiReturn<T> {
  return useApi<T>(() => apiService.put<T>(url, data, config), options);
}

export function useApiDelete<T = any>(
  url: string,
  config?: AxiosRequestConfig,
  options?: UseApiOptions
): UseApiReturn<T> {
  return useApi<T>(() => apiService.delete<T>(url, config), options);
}

// Paginated data composable
export function usePaginatedApi<T = any>(
  baseUrl: string,
  initialParams: Record<string, any> = {},
  options?: UseApiOptions
) {
  const params = ref({ page: 1, per_page: 15, ...initialParams });
  const allData = ref<T[]>([]);
  const hasMore = ref(true);
  const total = ref(0);
  const currentPage = ref(1);
  const lastPage = ref(1);

  const { data, loading, error, execute } = useApi<{
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  }>(
    () => apiService.get(baseUrl, { params: params.value }),
    {
      ...options,
      immediate: false,
      onSuccess: (response) => {
        if (params.value.page === 1) {
          allData.value = response.data;
        } else {
          allData.value.push(...response.data);
        }
        
        currentPage.value = response.current_page;
        lastPage.value = response.last_page;
        total.value = response.total;
        hasMore.value = currentPage.value < lastPage.value;
        
        if (options?.onSuccess) options.onSuccess(response);
      }
    }
  );

  const loadMore = async (): Promise<void> => {
    if (!hasMore.value || loading.value) return;
    
    params.value.page = currentPage.value + 1;
    await execute();
  };

  const reload = async (): Promise<void> => {
    params.value.page = 1;
    allData.value = [];
    hasMore.value = true;
    await execute();
  };

  const updateParams = (newParams: Record<string, any>): void => {
    params.value = { ...params.value, ...newParams, page: 1 };
    reload();
  };

  return {
    data: computed(() => allData.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    hasMore: computed(() => hasMore.value),
    total: computed(() => total.value),
    currentPage: computed(() => currentPage.value),
    lastPage: computed(() => lastPage.value),
    params: computed(() => params.value),
    loadMore,
    reload,
    updateParams,
    execute
  };
}

// Form submission composable
export function useApiForm<T = any>(
  submitFn: (data: any) => Promise<AxiosResponse<T>>,
  options?: UseApiOptions & {
    resetOnSuccess?: boolean;
    validateFn?: (data: any) => Record<string, string> | null;
  }
) {
  const { resetOnSuccess = false, validateFn } = options || {};
  
  const formData = ref<Record<string, any>>({});
  const validationErrors = ref<Record<string, string>>({});
  const isSubmitting = ref(false);
  const submitError = ref<ApiError | null>(null);
  const submitSuccess = ref(false);

  const { data, loading, error, execute } = useApi<T>(
    () => submitFn(formData.value),
    {
      ...options,
      immediate: false,
      onSuccess: (response) => {
        submitSuccess.value = true;
        if (resetOnSuccess) {
          formData.value = {};
          validationErrors.value = {};
        }
        if (options?.onSuccess) options.onSuccess(response);
      },
      onError: (err) => {
        submitError.value = err;
        submitSuccess.value = false;
        
        // Handle validation errors
        if (err.errors) {
          validationErrors.value = err.errors.reduce((acc, error) => {
            acc[error.field] = error.message;
            return acc;
          }, {} as Record<string, string>);
        }
        
        if (options?.onError) options.onError(err);
      }
    }
  );

  const submit = async (): Promise<void> => {
    // Reset previous state
    submitError.value = null;
    submitSuccess.value = false;
    validationErrors.value = {};

    // Client-side validation
    if (validateFn) {
      const clientErrors = validateFn(formData.value);
      if (clientErrors) {
        validationErrors.value = clientErrors;
        return;
      }
    }

    isSubmitting.value = true;
    await execute();
    isSubmitting.value = false;
  };

  const setField = (field: string, value: any): void => {
    formData.value[field] = value;
    
    // Clear validation error for this field
    if (validationErrors.value[field]) {
      delete validationErrors.value[field];
    }
  };

  const setFormData = (data: Record<string, any>): void => {
    formData.value = { ...data };
    validationErrors.value = {};
    submitError.value = null;
    submitSuccess.value = false;
  };

  const reset = (): void => {
    formData.value = {};
    validationErrors.value = {};
    submitError.value = null;
    submitSuccess.value = false;
  };

  return {
    formData: computed(() => formData.value),
    validationErrors: computed(() => validationErrors.value),
    isSubmitting: computed(() => isSubmitting.value || loading.value),
    submitError: computed(() => submitError.value || error.value),
    submitSuccess: computed(() => submitSuccess.value),
    data: computed(() => data.value),
    submit,
    setField,
    setFormData,
    reset
  };
}

// File upload composable
export function useFileUpload<T = any>(
  uploadUrl: string,
  options?: UseApiOptions & {
    maxSize?: number; // in bytes
    allowedTypes?: string[];
    multiple?: boolean;
  }
) {
  const { maxSize = 10 * 1024 * 1024, allowedTypes = [], multiple = false } = options || {};
  
  const files = ref<File[]>([]);
  const uploadProgress = ref(0);
  const isUploading = ref(false);
  const uploadError = ref<string | null>(null);
  const uploadData = ref<T | null>(null);

  const validateFile = (file: File): string | null => {
    if (maxSize && file.size > maxSize) {
      return `File size must be less than ${Math.round(maxSize / 1024 / 1024)}MB`;
    }
    
    if (allowedTypes.length > 0 && !allowedTypes.includes(file.type)) {
      return `File type not allowed. Allowed types: ${allowedTypes.join(', ')}`;
    }
    
    return null;
  };

  const addFiles = (newFiles: File[] | FileList): void => {
    const fileArray = Array.from(newFiles);
    
    for (const file of fileArray) {
      const validation = validateFile(file);
      if (validation) {
        uploadError.value = validation;
        return;
      }
    }

    if (multiple) {
      files.value.push(...fileArray);
    } else {
      files.value = [fileArray[0]];
    }
    
    uploadError.value = null;
  };

  const removeFile = (index: number): void => {
    files.value.splice(index, 1);
  };

  const upload = async (): Promise<void> => {
    if (files.value.length === 0) {
      uploadError.value = 'No files selected';
      return;
    }

    isUploading.value = true;
    uploadError.value = null;
    uploadProgress.value = 0;

    try {
      const fileToUpload = files.value[0]; // For now, upload one file at a time
      
      const response = await apiService.uploadFile<T>(
        uploadUrl,
        fileToUpload,
        (progress) => {
          uploadProgress.value = progress;
        }
      );

      uploadData.value = response.data;
      uploadProgress.value = 100;
      
      if (options?.onSuccess) {
        options.onSuccess(response.data);
      }

    } catch (error: any) {
      uploadError.value = error.message || 'Upload failed';
      uploadProgress.value = 0;
      
      if (options?.onError) {
        options.onError(error);
      }
    } finally {
      isUploading.value = false;
    }
  };

  const reset = (): void => {
    files.value = [];
    uploadProgress.value = 0;
    uploadError.value = null;
    uploadData.value = null;
    isUploading.value = false;
  };

  return {
    files: computed(() => files.value),
    uploadProgress: computed(() => uploadProgress.value),
    isUploading: computed(() => isUploading.value),
    uploadError: computed(() => uploadError.value),
    uploadData: computed(() => uploadData.value),
    addFiles,
    removeFile,
    upload,
    reset
  };
}

// Export cache management utilities
export const useApiCache = () => ({
  clear: () => apiCache.clear(),
  delete: (key: string) => apiCache.delete(key),
  has: (key: string) => apiCache.has(key),
  get: (key: string) => apiCache.get(key),
  set: (key: string, data: any, ttl?: number) => apiCache.set(key, data, ttl)
}); 