<template>
  <div class="professions-dashboard">
    <!-- Header -->
    <div class="mb-8">
      <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
          <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
            Professional Categories & Careers
          </h1>
          <p class="mt-1 text-sm text-gray-500">
            Explore career opportunities organized by professional categories
          </p>
        </div>
        
        <div class="mt-4 flex md:mt-0 md:ml-4">
          <!-- Language Selector -->
          <select
            v-model="currentLocale"
            @change="handleLocaleChange"
            class="mr-3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
          >
            <option v-for="(name, code) in availableLanguages" :key="code" :value="code">
              {{ name }}
            </option>
          </select>
          
          <!-- View Toggle -->
          <div class="bg-gray-100 p-1 rounded-lg flex">
            <button
              @click="viewMode = 'categories'"
              :class="[
                'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                viewMode === 'categories' 
                  ? 'bg-white text-gray-900 shadow-sm' 
                  : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              Categories
            </button>
            <button
              @click="viewMode = 'professions'"
              :class="[
                'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                viewMode === 'professions' 
                  ? 'bg-white text-gray-900 shadow-sm' 
                  : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              Professions
            </button>
            <button
              @click="viewMode = 'both'"
              :class="[
                'px-3 py-1 rounded-md text-sm font-medium transition-colors',
                viewMode === 'both' 
                  ? 'bg-white text-gray-900 shadow-sm' 
                  : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              Both
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Bar -->
    <div v-if="statistics" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Total Professions</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.total_professions }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Active Professions</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.active_professions }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Featured</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.featured_professions }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">With Jobs</dt>
                <dd class="text-lg font-medium text-gray-900">{{ statistics.professions_with_jobs }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 gap-8" :class="viewMode === 'both' ? 'lg:grid-cols-3' : ''">
      <!-- Categories Panel -->
      <div
        v-if="viewMode === 'categories' || viewMode === 'both'"
        :class="viewMode === 'both' ? 'lg:col-span-1' : ''"
        class="bg-white shadow rounded-lg"
      >
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            Professional Categories
          </h3>
          
          <CategoryTree
            :selected-category-id="selectedCategoryId"
            :show-profession-count="true"
            :show-actions="false"
            :show-language-selector="false"
            @category-select="handleCategorySelect"
          />
        </div>
      </div>

      <!-- Professions Panel -->
      <div
        v-if="viewMode === 'professions' || viewMode === 'both'"
        :class="viewMode === 'both' ? 'lg:col-span-2' : ''"
        class="bg-white shadow rounded-lg"
      >
        <div class="px-4 py-5 sm:p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ selectedCategory ? selectedCategory.name : 'All Professions' }}
            </h3>
            
            <button
              v-if="selectedCategory"
              @click="clearCategorySelection"
              class="text-sm text-gray-500 hover:text-gray-700"
            >
              View All
            </button>
          </div>
          
          <ProfessionList
            :category-filter="selectedCategoryId"
            @profession-select="handleProfessionSelect"
          />
        </div>
      </div>
    </div>

    <!-- Selected Profession Modal/Details -->
    <div
      v-if="selectedProfession"
      class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50"
      @click="closeModal"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        @click.stop
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-900">{{ selectedProfession.name }}</h2>
            <button
              @click="closeModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
        
        <div class="px-6 py-4">
          <div class="space-y-4">
            <!-- Basic Info -->
            <div>
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Basic Information</h3>
              <dl class="mt-2 space-y-1">
                <div class="flex justify-between">
                  <dt class="text-sm text-gray-600">Category:</dt>
                  <dd class="text-sm text-gray-900">{{ selectedProfession.category_name }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm text-gray-600">Skill Level:</dt>
                  <dd class="text-sm text-gray-900">{{ selectedProfession.skill_level }}</dd>
                </div>
                <div v-if="selectedProfession.isco_code" class="flex justify-between">
                  <dt class="text-sm text-gray-600">ISCO Code:</dt>
                  <dd class="text-sm text-gray-900">{{ selectedProfession.isco_code }}</dd>
                </div>
              </dl>
            </div>

            <!-- Description -->
            <div v-if="selectedProfession.description">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Description</h3>
              <p class="mt-2 text-sm text-gray-700">{{ selectedProfession.description }}</p>
            </div>

            <!-- Skills Required -->
            <div v-if="selectedProfession.skills_required && selectedProfession.skills_required.length > 0">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Skills Required</h3>
              <div class="mt-2 flex flex-wrap gap-2">
                <span
                  v-for="skill in selectedProfession.skills_required"
                  :key="skill"
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                >
                  {{ skill }}
                </span>
              </div>
            </div>

            <!-- Education Requirements -->
            <div v-if="selectedProfession.education_requirements && selectedProfession.education_requirements.length > 0">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Education Requirements</h3>
              <ul class="mt-2 space-y-1">
                <li
                  v-for="requirement in selectedProfession.education_requirements"
                  :key="requirement"
                  class="text-sm text-gray-700 flex items-start"
                >
                  <span class="mr-2">•</span>
                  {{ requirement }}
                </li>
              </ul>
            </div>

            <!-- Job Statistics -->
            <div v-if="selectedProfession.active_jobs_count !== undefined">
              <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Job Market</h3>
              <p class="mt-2 text-sm text-gray-700">
                Currently {{ selectedProfession.active_jobs_count }} active job openings
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useProfessionsStore } from '@/stores/professions';
import CategoryTree from './CategoryTree.vue';
import ProfessionList from './ProfessionList.vue';

// Store
const professionsStore = useProfessionsStore();

// Reactive data
const viewMode = ref('both'); // 'categories', 'professions', 'both'
const currentLocale = ref('en');
const selectedCategoryId = ref(null);
const selectedCategory = ref(null);
const selectedProfession = ref(null);

// Computed
const availableLanguages = computed(() => professionsStore.availableLanguages);
const statistics = computed(() => professionsStore.statistics);

// Methods
const handleLocaleChange = () => {
  professionsStore.setLocale(currentLocale.value);
  loadData();
};

const handleCategorySelect = (category) => {
  selectedCategoryId.value = category.id;
  selectedCategory.value = category;
};

const clearCategorySelection = () => {
  selectedCategoryId.value = null;
  selectedCategory.value = null;
};

const handleProfessionSelect = (profession) => {
  selectedProfession.value = profession;
};

const closeModal = () => {
  selectedProfession.value = null;
};

const loadData = async () => {
  await Promise.all([
    professionsStore.fetchCategoryTree(),
    professionsStore.fetchStatistics(),
  ]);
};

// Lifecycle
onMounted(async () => {
  currentLocale.value = professionsStore.currentLocale;
  await loadData();
});

// Watchers
watch(currentLocale, () => {
  professionsStore.setLocale(currentLocale.value);
});
</script>
