<template>
  <div class="profession-list">
    <!-- Header with Search and Filters -->
    <div class="mb-6 space-y-4">
      <!-- Search Bar -->
      <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search professions..."
          class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
          @input="handleSearch"
        />
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-4">
        <!-- Category Filter -->
        <select v-model="selectedCategory" @change="handleFilter" class="px-3 py-2 border border-gray-300 rounded-md">
          <option value="">All Categories</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>

        <!-- Skill Level Filter -->
        <select v-model="selectedSkillLevel" @change="handleFilter" class="px-3 py-2 border border-gray-300 rounded-md">
          <option value="">All Skill Levels</option>
          <option value="High">High</option>
          <option value="Medium">Medium</option>
          <option value="Low">Low</option>
        </select>

        <!-- Featured Filter -->
        <label class="flex items-center">
          <input v-model="featuredOnly" @change="handleFilter" type="checkbox" class="mr-2">
          Featured Only
        </label>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-3 text-gray-600">Loading professions...</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <p class="text-red-700">{{ error }}</p>
    </div>

    <!-- Profession Grid -->
    <div v-else-if="professions.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="profession in professions"
        :key="profession.id"
        class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
        @click="handleProfessionSelect(profession)"
      >
        <div class="p-6">
          <!-- Header -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ profession.name }}</h3>
              <p class="text-sm text-gray-600">{{ profession.category_name }}</p>
            </div>
            
            <div class="flex flex-col items-end space-y-2">
              <!-- Featured Badge -->
              <span v-if="profession.is_featured" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                Featured
              </span>
              
              <!-- Skill Level -->
              <span :class="getSkillLevelClass(profession.skill_level)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                {{ profession.skill_level }}
              </span>
            </div>
          </div>

          <!-- Description -->
          <p v-if="profession.description" class="text-sm text-gray-700 mb-4 line-clamp-3">
            {{ profession.description }}
          </p>

          <!-- Skills Preview -->
          <div v-if="profession.skills_required && profession.skills_required.length > 0" class="mb-4">
            <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Key Skills</h4>
            <div class="flex flex-wrap gap-1">
              <span
                v-for="skill in profession.skills_required.slice(0, 3)"
                :key="skill"
                class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-700"
              >
                {{ skill }}
              </span>
              <span v-if="profession.skills_required.length > 3" class="text-xs text-gray-500">
                +{{ profession.skills_required.length - 3 }} more
              </span>
            </div>
          </div>

          <!-- Footer -->
          <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-4 text-xs text-gray-500">
              <span v-if="profession.isco_code">ISCO: {{ profession.isco_code }}</span>
              <span>Code: {{ profession.code }}</span>
            </div>
            
            <div v-if="profession.active_jobs_count !== undefined" class="text-xs text-gray-500">
              {{ profession.active_jobs_count }} active jobs
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">No professions found</h3>
      <p class="mt-1 text-sm text-gray-500">
        <span v-if="searchQuery || selectedCategory || selectedSkillLevel || featuredOnly">
          Try adjusting your search criteria
        </span>
        <span v-else>No professions are available</span>
      </p>
    </div>

    <!-- Pagination -->
    <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
      </div>
      
      <div class="flex space-x-2">
        <button
          @click="handlePageChange(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        
        <span class="px-3 py-2 text-sm border border-gray-300 rounded-md bg-blue-50 text-blue-700">
          {{ pagination.current_page }}
        </span>
        
        <button
          @click="handlePageChange(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useProfessionsStore } from '@/stores/professions';

// Simple debounce implementation
const debounce = (func, wait) => {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
};

// Emits
const emit = defineEmits(['profession-select']);

// Store
const professionsStore = useProfessionsStore();

// Reactive data
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedSkillLevel = ref('');
const featuredOnly = ref(false);

// Computed
const loading = computed(() => professionsStore.loading.professions);
const error = computed(() => professionsStore.error);
const professions = computed(() => professionsStore.professions);
const categories = computed(() => professionsStore.categories);
const pagination = computed(() => professionsStore.pagination.professions);

// Methods
const getSkillLevelClass = (level) => {
  const classes = {
    High: 'bg-red-100 text-red-800',
    Medium: 'bg-yellow-100 text-yellow-800',
    Low: 'bg-green-100 text-green-800',
  };
  return classes[level] || 'bg-gray-100 text-gray-800';
};

const handleProfessionSelect = (profession) => {
  emit('profession-select', profession);
};

const handleSearch = debounce(() => {
  loadProfessions();
}, 300);

const handleFilter = () => {
  loadProfessions();
};

const handlePageChange = (page) => {
  loadProfessions({ page });
};

const loadProfessions = async (options = {}) => {
  const filters = {
    search: searchQuery.value || undefined,
    category_id: selectedCategory.value || undefined,
    skill_level: selectedSkillLevel.value || undefined,
    featured_only: featuredOnly.value || undefined,
    page: options.page || 1,
    locale: professionsStore.currentLocale,
  };

  if (searchQuery.value) {
    await professionsStore.searchProfessions(filters);
  } else {
    await professionsStore.fetchProfessions(filters);
  }
};

// Lifecycle
onMounted(async () => {
  await Promise.all([
    professionsStore.fetchCategories(),
    loadProfessions(),
  ]);
});
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
