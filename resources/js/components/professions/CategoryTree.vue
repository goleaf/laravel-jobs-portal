<template>
  <div class="profession-category-tree">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      <span class="ml-3 text-gray-600">{{ $t('loading_categories') }}</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">{{ $t('error_loading_categories') }}</h3>
          <p class="mt-1 text-sm text-red-700">{{ error }}</p>
        </div>
      </div>
    </div>

    <!-- Category Tree -->
    <div v-else class="space-y-2">
      <!-- Search -->
      <div class="relative mb-4">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('search_categories_placeholder')"
          class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        />
      </div>

      <!-- Language Selector -->
      <div v-if="showLanguageSelector" class="mb-4">
        <select
          v-model="selectedLocale"
          @change="handleLocaleChange"
          class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
        >
          <option value="">{{ $t('select_language') }}</option>
          <option v-for="(name, code) in availableLanguages" :key="code" :value="code">
            {{ name }}
          </option>
        </select>
      </div>

      <!-- Tree Nodes -->
      <div class="space-y-1">
        <CategoryTreeNode
          v-for="category in filteredRootCategories"
          :key="category.id"
          :category="category"
          :level="0"
          :selected-id="selectedCategoryId"
          :expanded-ids="expandedIds"
          :show-profession-count="showProfessionCount"
          :show-actions="showActions"
          @select="handleCategorySelect"
          @toggle="handleToggle"
          @edit="handleEdit"
          @delete="handleDelete"
          @add-child="handleAddChild"
        />
      </div>

      <!-- Empty State -->
      <div v-if="filteredRootCategories.length === 0" class="text-center py-8 text-gray-500">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $t('no_categories_found') }}</h3>
        <p class="mt-1 text-sm text-gray-500">
          <span v-if="searchQuery">{{ $t('no_categories_match_search') }}</span>
          <span v-else>{{ $t('no_categories_available') }}</span>
        </p>
      </div>

      <!-- Add Root Category Button -->
      <div v-if="showActions && !searchQuery" class="pt-4 border-t border-gray-200">
        <button
          @click="handleAddRoot"
          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
        >
          <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          {{ $t('add_root_category') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue';
import { useProfessionsStore } from '@/stores/professions';
import CategoryTreeNode from './CategoryTreeNode.vue';

// Props
interface Props {
  selectedCategoryId?: number | null;
  showProfessionCount?: boolean;
  showActions?: boolean;
  showLanguageSelector?: boolean;
  autoExpand?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  selectedCategoryId: null,
  showProfessionCount: true,
  showActions: false,
  showLanguageSelector: true,
  autoExpand: true,
});

// Emits
const emit = defineEmits<{
  categorySelect: [category: any];
  categoryEdit: [category: any];
  categoryDelete: [category: any];
  categoryAdd: [parentCategory?: any];
}>();

// Store
const professionsStore = useProfessionsStore();

// Reactive data
const searchQuery = ref('');
const selectedLocale = ref('en');
const expandedIds = ref<Set<number>>(new Set());

// Computed
const loading = computed(() => professionsStore.loading.categories);
const error = computed(() => professionsStore.error);
const categoryTree = computed(() => professionsStore.categoryTree);
const availableLanguages = computed(() => professionsStore.availableLanguages);

const filteredRootCategories = computed(() => {
  if (!searchQuery.value) {
    return categoryTree.value;
  }

  const query = searchQuery.value.toLowerCase();
  
  // Recursive filter function
  const filterCategory = (category: any): any | null => {
    const matchesSearch = 
      category.name.toLowerCase().includes(query) ||
      (category.description && category.description.toLowerCase().includes(query));

    const filteredChildren = category.children?.map(filterCategory).filter(Boolean) || [];

    if (matchesSearch || filteredChildren.length > 0) {
      return {
        ...category,
        children: filteredChildren,
      };
    }

    return null;
  };

  return categoryTree.value.map(filterCategory).filter(Boolean);
});

// Methods
const handleCategorySelect = (category: any) => {
  emit('categorySelect', category);
};

const handleToggle = (categoryId: number) => {
  if (expandedIds.value.has(categoryId)) {
    expandedIds.value.delete(categoryId);
  } else {
    expandedIds.value.add(categoryId);
  }
};

const handleEdit = (category: any) => {
  emit('categoryEdit', category);
};

const handleDelete = (category: any) => {
  emit('categoryDelete', category);
};

const handleAddChild = (parentCategory: any) => {
  emit('categoryAdd', parentCategory);
};

const handleAddRoot = () => {
  emit('categoryAdd');
};

const handleLocaleChange = () => {
  professionsStore.setLocale(selectedLocale.value);
  loadCategoryTree();
};

const loadCategoryTree = async () => {
  await professionsStore.fetchCategoryTree(selectedLocale.value);
  
  if (props.autoExpand) {
    autoExpandTree();
  }
};

const autoExpandTree = () => {
  const expandAllCategories = (categories: any[]) => {
    categories.forEach(category => {
      expandedIds.value.add(category.id);
      if (category.children && category.children.length > 0) {
        expandAllCategories(category.children);
      }
    });
  };

  expandAllCategories(categoryTree.value);
};

// Watchers
watch(searchQuery, (newQuery) => {
  if (newQuery) {
    // Auto-expand when searching
    autoExpandTree();
  }
});

// Lifecycle
onMounted(async () => {
  await Promise.all([
    professionsStore.fetchAvailableLanguages(),
    loadCategoryTree(),
  ]);
  
  selectedLocale.value = professionsStore.currentLocale;
});
</script>

<style scoped>
.profession-category-tree {
  /* Custom styles can be added here if needed */
}

/* Smooth transitions */
.tree-node-enter-active,
.tree-node-leave-active {
  transition: all 0.2s ease-in-out;
}

.tree-node-enter-from,
.tree-node-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}
</style> 