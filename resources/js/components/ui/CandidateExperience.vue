<template>
  <div
    class="overflow-hidden shadow rounded-lg p-5 mb-5 bg-white candidate-experience"
    :data-experience-id="experience.candidateExperienceNumber"
    :data-id="experience.id"
  >
    <article class="article article-style-b">
      <div class="article-details">
        <div class="flex justify-between">
          <div class="article-title">
            <h4 class="text-indigo-600 text-lg font-semibold">{{ experience.title }}</h4>
            <h6 class="text-gray-500 text-base">{{ experience.company }}</h6>
          </div>
          
          <div class="article-cta candidate-experience-edit-delete">
            <button
              @click="$emit('edit', experience.id)"
              class="rounded border border-transparent text-indigo-600 inline-flex items-center px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 hover:bg-indigo-50"
                             :title="t('common.edit')"
              :disabled="loading"
            >
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            
            <button
              @click="$emit('delete', experience.id)"
              class="rounded border border-transparent text-red-600 inline-flex items-center px-3 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 hover:bg-red-50 ml-2"
                             :title="t('common.delete')"
              :disabled="loading"
            >
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
        
        <span class="text-gray-500 text-sm">
          {{ formatDateRange(experience.startDateExperience, experience.endDateExperience) }} | {{ experience.country }}
        </span>
        
        <p class="mt-2 text-gray-700" v-html="experience.description"></p>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
// Props
interface Experience {
  id: number
  candidateExperienceNumber: number
  title: string
  company: string
  startDateExperience: string
  endDateExperience: string
  country: string
  description: string
}

defineProps<{
  experience: Experience
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
    'common.delete': 'Delete',
    'common.present': 'Present'
  }
  return translations[key] || key
}

// Methods
const formatDateRange = (startDate: string, endDate: string): string => {
  if (!startDate) return ''
  
  const start = new Date(startDate).toLocaleDateString()
  const end = endDate && endDate !== 'Present' 
    ? new Date(endDate).toLocaleDateString() 
    : t('common.present')
    
  return `${start} - ${end}`
}
</script>

<style scoped>
.candidate-experience {
  transition: all 0.2s ease-in-out;
}

.candidate-experience:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.article-cta button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style> 