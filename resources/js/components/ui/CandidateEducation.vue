<template>
  <div
    class="overflow-hidden shadow rounded-lg p-5 mb-5 bg-white candidate-education"
    :data-education-id="education.candidateEducationNumber"
    :data-id="education.id"
  >
    <article class="article article-style-b">
      <div class="article-details">
        <div class="flex justify-between">
          <div class="article-title">
            <h4 class="text-indigo-600 text-lg font-semibold education-degree-level">{{ education.degreeLevel }}</h4>
            <h6 class="text-gray-500 text-base">{{ education.degreeTitle }}</h6>
          </div>
          
          <div class="article-cta candidate-education-edit-delete">
            <button
              @click="$emit('edit', education.id)"
              class="rounded border border-transparent text-indigo-600 inline-flex items-center px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 hover:bg-indigo-50"
                             :title="t('common.edit')"
              :disabled="loading"
            >
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            
            <button
              @click="$emit('delete', education.id)"
              class="rounded border border-transparent text-red-600 inline-flex items-center px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 hover:bg-red-50 ml-2"
                             :title="t('common.delete')"
              :disabled="loading"
            >
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
        
        <span class="text-gray-500 text-sm">
          {{ education.year }} | {{ education.country }}
        </span>
        
        <p class="mt-2 text-gray-700 font-medium">{{ education.institute }}</p>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
// Props
interface Education {
  id: number
  candidateEducationNumber: number
  degreeLevel: string
  degreeTitle: string
  year: string
  country: string
  institute: string
}

defineProps<{
  education: Education
  loading?: boolean
}>()

// Emits
defineEmits<{
  edit: [id: number]
  delete: [id: number]
}>()

// Translation helper (simplified for now)
const t = (key: string) => {
  const translations: Record<string, string> = {
    'common.edit': 'Edit',
    'common.delete': 'Delete'
  }
  return translations[key] || key
}
</script>

<style scoped>
.candidate-education {
  transition: all 0.2s ease-in-out;
}

.candidate-education:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.article-cta button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style> 