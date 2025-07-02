<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 cursor-pointer" @click="viewJob">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-start space-x-3 flex-1">
        <!-- Company Logo -->
        <div v-if="showCompanyLogo" class="flex-shrink-0">
          <img
            v-if="job.company?.logo"
            :src="job.company.logo"
            :alt="job.company?.name"
            class="w-12 h-12 rounded-lg object-cover"
          />
          <div
            v-else
            class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center"
          >
            <span class="text-lg font-semibold text-indigo-600">
              {{ (job.company?.name || 'C').charAt(0).toUpperCase() }}
            </span>
          </div>
        </div>

        <!-- Job Info -->
        <div class="flex-1 min-w-0">
          <h3 class="text-lg font-semibold text-gray-900 hover:text-indigo-600 transition-colors duration-200 line-clamp-2">
            {{ job.title }}
          </h3>
          <p class="text-indigo-600 font-medium mt-1 hover:text-indigo-700 transition-colors duration-200">
            {{ job.company?.name }}
          </p>
          <div class="flex items-center text-gray-500 text-sm mt-1 space-x-4">
            <div class="flex items-center">
              <MapPinIcon class="h-4 w-4 mr-1" />
              {{ job.location || 'Remote' }}
            </div>
            <div v-if="job.department" class="flex items-center">
              <TagIcon class="h-4 w-4 mr-1" />
              {{ job.department }}
            </div>
          </div>
        </div>
      </div>

      <!-- Bookmark Button -->
      <button
        @click.stop="toggleBookmark"
        :class="[
          'p-2 rounded-full transition-colors duration-200',
          isBookmarked 
            ? 'text-red-500 hover:text-red-600 bg-red-50' 
            : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50'
        ]"
        :aria-label="isBookmarked ? 'Remove from saved jobs' : 'Save job'"
      >
        <HeartIcon 
          :class="['h-5 w-5', isBookmarked ? 'fill-current' : '']" 
        />
      </button>
    </div>

    <!-- Job Tags -->
    <div class="flex flex-wrap gap-2 mb-4">
      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
        {{ job.employment_type || 'Full-time' }}
      </span>
      
      <span v-if="job.experience_level" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
        {{ job.experience_level }}
      </span>
      
      <span v-if="job.remote_ok" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
        <ComputerDesktopIcon class="h-3 w-3 mr-1" />
        Remote OK
      </span>
      
      <span v-if="job.is_urgent" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
        <ExclamationTriangleIcon class="h-3 w-3 mr-1" />
        Urgent
      </span>
      
      <span v-if="job.is_featured" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
        <StarIcon class="h-3 w-3 mr-1" />
        Featured
      </span>
    </div>

    <!-- Job Description -->
    <p class="text-gray-600 text-sm line-clamp-3 mb-4">
      {{ job.description || 'Job description not available.' }}
    </p>

    <!-- Skills -->
    <div v-if="job.skills && job.skills.length > 0" class="mb-4">
      <div class="flex flex-wrap gap-1">
        <span
          v-for="skill in job.skills.slice(0, 4)"
          :key="skill"
          class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700"
        >
          {{ skill }}
        </span>
        <span v-if="job.skills.length > 4" class="inline-flex items-center px-2 py-1 rounded text-xs font-medium text-gray-500">
          +{{ job.skills.length - 4 }} more
        </span>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
      <div class="flex items-center space-x-4">
        <!-- Salary -->
        <div v-if="job.salary_min || job.salary_max || job.salary_range">
          <div class="text-sm font-medium text-gray-900">
            <CurrencyDollarIcon class="h-4 w-4 inline mr-1" />
            <span v-if="job.salary_range">{{ job.salary_range }}</span>
            <span v-else-if="job.salary_min && job.salary_max">
              ${{ formatSalary(job.salary_min) }} - ${{ formatSalary(job.salary_max) }}
            </span>
            <span v-else-if="job.salary_min">
              From ${{ formatSalary(job.salary_min) }}
            </span>
            <span v-else>
              Up to ${{ formatSalary(job.salary_max) }}
            </span>
          </div>
        </div>

        <!-- Posted Time -->
        <div class="text-xs text-gray-500">
          <ClockIcon class="h-4 w-4 inline mr-1" />
          {{ formatPostedTime(job.created_at) }}
        </div>
      </div>

      <!-- Apply Button -->
      <BaseButton
        variant="primary"
        size="sm"
        @click.stop="applyToJob"
        :disabled="hasApplied"
        class="min-w-[80px]"
      >
        <span v-if="hasApplied">Applied</span>
        <span v-else-if="isApplying">Applying...</span>
        <span v-else>Apply</span>
      </BaseButton>
    </div>

    <!-- Application Status -->
    <div v-if="applicationStatus" class="mt-3 pt-3 border-t border-gray-100">
      <div class="flex items-center text-sm">
        <div
          :class="[
            'w-2 h-2 rounded-full mr-2',
            applicationStatusColor
          ]"
        ></div>
        <span :class="applicationStatusTextColor">
          Application {{ applicationStatus }}
        </span>
        <span v-if="applicationDate" class="text-gray-500 ml-2">
          on {{ formatDate(applicationDate) }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import { useApiPost } from '@/composables/useApi';
import BaseButton from '@/components/base/BaseButton.vue';

// Icons
import {
  MapPinIcon,
  HeartIcon,
  TagIcon,
  ComputerDesktopIcon,
  ExclamationTriangleIcon,
  StarIcon,
  CurrencyDollarIcon,
  ClockIcon
} from '@heroicons/vue/24/outline';

export interface JobData {
  id: number;
  title: string;
  description?: string;
  location?: string;
  department?: string;
  employment_type?: string;
  experience_level?: string;
  salary_min?: number;
  salary_max?: number;
  salary_range?: string;
  remote_ok?: boolean;
  is_urgent?: boolean;
  is_featured?: boolean;
  skills?: string[];
  created_at: string;
  company?: {
    id: number;
    name: string;
    logo?: string;
    slug?: string;
  };
  application_status?: string;
  application_date?: string;
  is_bookmarked?: boolean;
}

export interface Props {
  job: JobData;
  showCompanyLogo?: boolean;
  showApplicationStatus?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  showCompanyLogo: true,
  showApplicationStatus: false
});

const router = useRouter();
const { isAuthenticated, requireAuth } = useAuth();

// State
const isApplying = ref(false);
const isBookmarked = ref(props.job.is_bookmarked || false);

// Computed properties
const hasApplied = computed(() => {
  return props.job.application_status !== undefined;
});

const applicationStatus = computed(() => {
  return props.job.application_status;
});

const applicationDate = computed(() => {
  return props.job.application_date;
});

const applicationStatusColor = computed(() => {
  switch (applicationStatus.value) {
    case 'pending':
      return 'bg-yellow-400';
    case 'reviewing':
      return 'bg-blue-400';
    case 'interview':
      return 'bg-purple-400';
    case 'accepted':
      return 'bg-green-400';
    case 'rejected':
      return 'bg-red-400';
    default:
      return 'bg-gray-400';
  }
});

const applicationStatusTextColor = computed(() => {
  switch (applicationStatus.value) {
    case 'pending':
      return 'text-yellow-700';
    case 'reviewing':
      return 'text-blue-700';
    case 'interview':
      return 'text-purple-700';
    case 'accepted':
      return 'text-green-700';
    case 'rejected':
      return 'text-red-700';
    default:
      return 'text-gray-700';
  }
});

// API calls
const { execute: submitApplication } = useApiPost(
  `/api/jobs/${props.job.id}/apply`,
  {},
  {},
  {
    immediate: false,
    onSuccess: () => {
      // Update local state to show applied
      // In a real app, you'd emit an event or update a store
      console.log('Application submitted successfully');
    },
    onError: (error) => {
      console.error('Application failed:', error);
    }
  }
);

const { execute: toggleBookmarkRequest } = useApiPost(
  `/api/jobs/${props.job.id}/bookmark`,
  {},
  {},
  {
    immediate: false,
    onSuccess: () => {
      isBookmarked.value = !isBookmarked.value;
    },
    onError: (error) => {
      console.error('Bookmark toggle failed:', error);
    }
  }
);

// Methods
const viewJob = () => {
  router.push({
    name: 'jobs.show',
    params: { slug: props.job.id.toString() }
  });
};

const applyToJob = async () => {
  if (!requireAuth()) return;

  if (hasApplied.value || isApplying.value) return;

  isApplying.value = true;
  
  try {
    await submitApplication();
  } catch (error) {
    console.error('Application error:', error);
  } finally {
    isApplying.value = false;
  }
};

const toggleBookmark = async () => {
  if (!requireAuth()) return;

  try {
    await toggleBookmarkRequest();
  } catch (error) {
    console.error('Bookmark error:', error);
  }
};

const formatSalary = (amount: number): string => {
  if (amount >= 1000000) {
    return (amount / 1000000).toFixed(1) + 'M';
  } else if (amount >= 1000) {
    return (amount / 1000).toFixed(0) + 'K';
  }
  return amount.toLocaleString();
};

const formatPostedTime = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now.getTime() - date.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 1) {
    return '1 day ago';
  } else if (diffDays < 7) {
    return `${diffDays} days ago`;
  } else if (diffDays < 30) {
    const weeks = Math.floor(diffDays / 7);
    return weeks === 1 ? '1 week ago' : `${weeks} weeks ago`;
  } else {
    const months = Math.floor(diffDays / 30);
    return months === 1 ? '1 month ago' : `${months} months ago`;
  }
};

const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Hover effects for the card */
.group:hover .group-hover\:shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Smooth transitions for all interactive elements */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Custom scrollbar for overflow content */
.overflow-hidden {
  scrollbar-width: none;
  -ms-overflow-style: none;
}

.overflow-hidden::-webkit-scrollbar {
  display: none;
}
</style> 