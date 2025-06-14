<template>
  <div 
    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4 hover:shadow-md transition-shadow duration-200"
    :data-experience-id="experience.id"
  >
    <article class="space-y-4">
      <!-- Header with title and actions -->
      <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold text-indigo-600 truncate">
            {{ experience.title }}
          </h3>
          <h4 class="text-base font-medium text-gray-700 mt-1">
            {{ experience.company }}
          </h4>
        </div>
        
        <!-- Action buttons -->
        <div class="flex items-center gap-2 ml-4">
          <ActionButtons
            :item="experience"
            :show-view="false"
            :show-labels="false"
            @edit="handleEdit"
            @delete="handleDelete"
            @deleted="handleDeleted"
          />
        </div>
      </div>

      <!-- Experience details -->
      <div class="space-y-2">
        <!-- Duration and location -->
        <div class="flex items-center text-sm text-gray-600">
          <CalendarIcon class="w-4 h-4 mr-2 flex-shrink-0" />
          <span>{{ formatDateRange(experience.start_date, experience.end_date) }}</span>
          <span v-if="experience.country" class="mx-2">•</span>
          <span v-if="experience.country">{{ experience.country }}</span>
        </div>

        <!-- Description -->
        <div v-if="experience.description" class="text-gray-700">
          <p class="text-sm leading-relaxed">{{ experience.description }}</p>
        </div>

        <!-- Skills/Technologies (if available) -->
        <div v-if="experience.skills && experience.skills.length" class="flex flex-wrap gap-2 mt-3">
          <span
            v-for="skill in experience.skills"
            :key="skill.id"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
          >
            {{ skill.name }}
          </span>
        </div>
      </div>

      <!-- Footer with metadata -->
      <div v-if="showMetadata" class="pt-3 border-t border-gray-100">
        <div class="flex items-center justify-between text-xs text-gray-500">
          <span v-if="experience.created_at">
            {{ t('common.added') }}: {{ formatDate(experience.created_at) }}
          </span>
          <span v-if="experience.updated_at && experience.updated_at !== experience.created_at">
            {{ t('common.updated') }}: {{ formatDate(experience.updated_at) }}
          </span>
        </div>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { CalendarIcon } from '@heroicons/vue/24/outline'
import { useEnhancedI18n } from '@/composables/useEnhancedI18n'
import { useToast } from '@/composables/useToast'
import ActionButtons from '@/components/ui/ActionButtons.vue'

// Types
interface Experience {
  id: number
  title: string
  company: string
  start_date: string
  end_date?: string
  country?: string
  description?: string
  skills?: Array<{ id: number; name: string }>
  created_at?: string
  updated_at?: string
}

interface CandidateExperienceCardProps {
  experience: Experience
  showMetadata?: boolean
  editable?: boolean
}

interface CandidateExperienceCardEmits {
  edit: [experience: Experience]
  delete: [experience: Experience]
  deleted: [experience: Experience]
}

// Props
const props = withDefaults(defineProps<CandidateExperienceCardProps>(), {
  showMetadata: false,
  editable: true
})

// Emits
const emit = defineEmits<CandidateExperienceCardEmits>()

// Composables
const { t, formatDate: formatDateBase } = useEnhancedI18n()
const { showSuccess } = useToast()

// Computed
const isCurrentJob = computed(() => !props.experience.end_date)

// Methods
const formatDate = (date: string) => {
  return formatDateBase(new Date(date), {
    year: 'numeric',
    month: 'short'
  })
}

const formatDateRange = (startDate: string, endDate?: string) => {
  const start = formatDate(startDate)
  
  if (!endDate) {
    return `${start} - ${t('common.present')}`
  }
  
  const end = formatDate(endDate)
  return `${start} - ${end}`
}

const handleEdit = (experience: Experience) => {
  emit('edit', experience)
}

const handleDelete = (experience: Experience) => {
  emit('delete', experience)
}

const handleDeleted = (experience: Experience) => {
  showSuccess(t('candidate.experience_deleted'))
  emit('deleted', experience)
}

// Calculate experience duration
const getExperienceDuration = () => {
  const start = new Date(props.experience.start_date)
  const end = props.experience.end_date ? new Date(props.experience.end_date) : new Date()
  
  const diffTime = Math.abs(end.getTime() - start.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  const diffMonths = Math.floor(diffDays / 30)
  const diffYears = Math.floor(diffMonths / 12)
  
  if (diffYears > 0) {
    const remainingMonths = diffMonths % 12
    if (remainingMonths > 0) {
      return `${diffYears} ${t('common.years')} ${remainingMonths} ${t('common.months')}`
    }
    return `${diffYears} ${t('common.years')}`
  }
  
  return `${diffMonths} ${t('common.months')}`
}

// Expose methods for parent component
defineExpose({
  getExperienceDuration
})
</script>

<style scoped>
/* Component-specific animations */
.experience-card-enter-active,
.experience-card-leave-active {
  transition: all 0.3s ease;
}

.experience-card-enter-from,
.experience-card-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Hover effects */
.experience-card:hover .action-buttons {
  opacity: 1;
}

.action-buttons {
  opacity: 0.7;
  transition: opacity 0.2s ease;
}

/* Current job indicator */
.current-job::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: linear-gradient(to bottom, #10b981, #059669);
  border-radius: 0 2px 2px 0;
}
</style> 