<template>
  <div class="category-tree-node">
    <!-- Category Item -->
    <div class="flex items-center group hover:bg-gray-50 rounded-lg p-2">
      <!-- Expand/Collapse Button -->
      <button
        v-if="hasChildren"
        @click="handleToggle"
        class="w-6 h-6 flex items-center justify-center rounded hover:bg-gray-200 mr-2"
      >
        <svg
          :class="['w-4 h-4 text-gray-500 transition-transform', { 'rotate-90': isExpanded }]"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </button>
      <div v-else class="w-6 mr-2"></div>

      <!-- Category Content -->
      <div class="flex-1 cursor-pointer" @click="handleSelect">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-medium text-gray-900">{{ category.name }}</h3>
            <p v-if="category.description" class="text-xs text-gray-500">{{ category.description }}</p>
          </div>
          
          <div class="flex items-center space-x-2">
            <span v-if="showProfessionCount" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
              {{ category.professions_count || 0 }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Children -->
    <div v-if="hasChildren && isExpanded" class="ml-6 mt-1 space-y-1">
      <CategoryTreeNode
        v-for="child in category.children"
        :key="child.id"
        :category="child"
        :level="level + 1"
        :selected-id="selectedId"
        :expanded-ids="expandedIds"
        :show-profession-count="showProfessionCount"
        @select="$emit('select', $event)"
        @toggle="$emit('toggle', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  category: Object,
  level: Number,
  selectedId: Number,
  expandedIds: Set,
  showProfessionCount: Boolean,
});

const emit = defineEmits(['select', 'toggle']);

const isExpanded = computed(() => props.expandedIds.has(props.category.id));
const hasChildren = computed(() => props.category.children && props.category.children.length > 0);

const handleSelect = () => {
  emit('select', props.category);
};

const handleToggle = () => {
  emit('toggle', props.category.id);
};
</script>
