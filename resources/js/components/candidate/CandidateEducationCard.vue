<template>
  <div 
    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4 hover:shadow-md transition-shadow duration-200"
    :data-education-id="education.id"
  >
    <article class="space-y-4">
      <!-- Header with degree and actions -->
      <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold text-indigo-600 truncate">
            {{ education.degree_level }}
          </h3>
          <h4 class="text-base font-medium text-gray-700 mt-1">
            {{ education.degree_title }}
          </h4>
        </div>
        
        <!-- Action buttons -->
        <div class="flex items-center gap-2 ml-4">
          <ActionButtons
            :item="education"
            :show-view="false"
            :show-labels="false"
            @edit="handleEdit"
            @delete="handleDelete"
            @deleted="handleDeleted"
          />
        </div>
      </div>

      <!-- Education details -->
      <div class="space-y-2">
        <!-- Institution -->
        <div class="flex items-center text-sm text-gray-700">
          <AcademicCapIcon class="w-4 h-4 mr-2 flex-shrink-0" />
          <span class="font-medium">{{ education.institute }}</span>
        </div>

        <!-- Year and location -->
        <div class="flex items-center text-sm text-gray-600">
          <CalendarIcon class="w-4 h-4 mr-2 flex-shrink-0" />
          <span>{{ formatEducationYear(education.year) }}</span>
          <span v-if="education.country" class="mx-2">•</span>
          <span v-if="education.country">{{ education.country }}</span>
        </div>

        <!-- GPA/Grade (if available) -->
        <div v-if="education.gpa || education.grade" class="flex items-center text-sm text-gray-600">
          <StarIcon class="w-4 h-4 mr-2 flex-shrink-0" />
          <span v-if="education.gpa">{{ t('education.gpa') }}: {{ education.gpa }}</span>
          <span v-else-if="education.grade">{{ t('education.grade') }}: {{ education.grade }}</span>
        </div>

        <!-- Description/Achievements -->
        <div v-if="education.description" class="text-gray-700">
          <p class="text-sm leading-relaxed">{{ education.description }}</p>
        </div>

        <!-- Subjects/Specializations (if available) -->
        <div v-if="education.subjects && education.subjects.length" class="flex flex-wrap gap-2 mt-3">
          <span
            v-for="subject in education.subjects"
            :key="subject.id || subject"
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
          >
            {{ typeof subject === 'string' ? subject : subject.name }}
          </span>
        </div>

        <!-- Honors/Awards (if available) -->
        <div v-if="education.honors && education.honors.length" class="mt-3">
          <h5 class="text-sm font-medium text-gray-700 mb-2">{{ t('education.honors_awards') }}:</h5>
          <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
            <li v-for="honor in education.honors" :key="honor">{{ honor }}</li>
          </ul>
        </div>
      </div>

      <!-- Footer with metadata -->
      <div v-if="showMetadata" class="pt-3 border-t border-gray-100">
        <div class="flex items-center justify-between text-xs text-gray-500">
          <span v-if="education.created_at">
            {{ t('common.added') }}: {{ formatDate(education.created_at) }}
          </span>
          <span v-if="education.updated_at && education.updated_at !== education.created_at">
            {{ t('common.updated') }}: {{ formatDate(education.updated_at) }}
          </span>
        </div>
      </div>
    </article>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { AcademicCapIcon, CalendarIcon, StarIcon } from '@heroicons/vue/24/outline'
import { useContext7I18n } from '@/composables/useContext7I18n'
import { useToast } from '@/composables/useToast'
import ActionButtons from '@/components/ui/ActionButtons.vue'

// Types
interface Education {
  id: number
  degree_level: string
  degree_title: string
  institute: string
  year: string | number
  country?: string
  gpa?: string | number
  grade?: string
  description?: string
  subjects?: Array<{ id: number; name: string } | string>
  honors?: string[]
  created_at?: string
  updated_at?: string
}

interface CandidateEducationCardProps {
  education: Education
  showMetadata?: boolean
  editable?: boolean
}

interface CandidateEducationCardEmits {
  edit: [education: Education]
  delete: [education: Education]
  deleted: [education: Education]
}

// Props
const props = withDefaults(defineProps<CandidateEducationCardProps>(), {
  showMetadata: false,
  editable: true
})

// Emits
const emit = defineEmits<CandidateEducationCardEmits>()

// Composables
const { t, formatDate: formatDateBase } = useContext7I18n()
const { showSuccess } = useToast()

// Computed
const educationLevel = computed(() => {
  const level = props.education.degree_level.toLowerCase()
  if (level.includes('phd') || level.includes('doctorate')) return 'doctorate'
  if (level.includes('master') || level.includes('msc') || level.includes('ma')) return 'masters'
  if (level.includes('bachelor') || level.includes('bsc') || level.includes('ba')) return 'bachelors'
  if (level.includes('diploma')) return 'diploma'
  if (level.includes('certificate')) return 'certificate'
  return 'other'
})

const educationIcon = computed(() => {
  switch (educationLevel.value) {
    case 'doctorate': return '🎓'
    case 'masters': return '📚'
    case 'bachelors': return '🎓'
    case 'diploma': return '📜'
    case 'certificate': return '🏆'
    default: return '📖'
  }
})

// Methods
const formatDate = (date: string) => {
  return formatDateBase(new Date(date), {
    year: 'numeric',
    month: 'short'
  })
}

const formatEducationYear = (year: string | number) => {
  if (typeof year === 'string') {
    // Handle year ranges like "2018-2022"
    if (year.includes('-')) {
      const [start, end] = year.split('-')
      return `${start} - ${end}`
    }
    return year
  }
  return year.toString()
}

const handleEdit = (education: Education) => {
  emit('edit', education)
}

const handleDelete = (education: Education) => {
  emit('delete', education)
}

const handleDeleted = (education: Education) => {
  showSuccess(t('candidate.education_deleted'))
  emit('deleted', education)
}

// Get education priority for sorting
const getEducationPriority = () => {
  const priorities = {
    doctorate: 5,
    masters: 4,
    bachelors: 3,
    diploma: 2,
    certificate: 1,
    other: 0
  }
  return priorities[educationLevel.value] || 0
}

// Expose methods for parent component
defineExpose({
  getEducationPriority,
  educationLevel: readonly(educationLevel)
})
</script>

<style scoped>
/* Component-specific animations */
.education-card-enter-active,
.education-card-leave-active {
  transition: all 0.3s ease;
}

.education-card-enter-from,
.education-card-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Hover effects */
.education-card:hover .action-buttons {
  opacity: 1;
}

.action-buttons {
  opacity: 0.7;
  transition: opacity 0.2s ease;
}

/* Education level indicators */
.doctorate {
  border-left: 4px solid #7c3aed;
}

.masters {
  border-left: 4px solid #2563eb;
}

.bachelors {
  border-left: 4px solid #059669;
}

.diploma {
  border-left: 4px solid #d97706;
}

.certificate {
  border-left: 4px solid #dc2626;
}
</style> 