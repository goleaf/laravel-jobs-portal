import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useProfessionsStore = defineStore('professions', () => {
  // State
  const categories = ref([]);
  const professions = ref([]);
  const loading = ref({
    categories: false,
    professions: false,
    search: false,
  });
  const error = ref(null);
  const currentLocale = ref('en');

  // Computed
  const isLoading = computed(() => {
    return Object.values(loading.value).some(Boolean);
  });

  // Actions
  const fetchCategories = async () => {
    loading.value.categories = true;
    try {
      // API call will be implemented
      console.log('Fetching categories...');
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value.categories = false;
    }
  };

  const fetchProfessions = async () => {
    loading.value.professions = true;
    try {
      // API call will be implemented
      console.log('Fetching professions...');
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value.professions = false;
    }
  };

  return {
    categories,
    professions,
    loading,
    error,
    currentLocale,
    isLoading,
    fetchCategories,
    fetchProfessions,
  };
});
