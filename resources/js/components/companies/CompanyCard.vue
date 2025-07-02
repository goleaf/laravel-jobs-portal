<template>
  <div
    class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 cursor-pointer hover:border-indigo-300 transition-all duration-200"
    @click="$emit('click', company)"
  >
    <!-- Company Header -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center space-x-4">
        <!-- Company Logo -->
        <div class="flex-shrink-0">
          <div
            v-if="company.logo"
            class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200"
          >
            <img
              :src="company.logo"
              :alt="company.name"
              class="w-full h-full object-cover"
            />
          </div>
          <div
            v-else
            class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-lg"
          >
            {{ company.name.charAt(0).toUpperCase() }}
          </div>
        </div>

        <!-- Company Info -->
        <div class="flex-1 min-w-0">
          <div class="flex items-center space-x-2 mb-1">
            <h3 class="text-lg font-semibold text-gray-900 truncate">
              {{ company.name }}
            </h3>
            <span
              v-if="company.is_featured"
              class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
            >
              <StarIcon class="h-3 w-3 mr-1" />
              Featured
            </span>
          </div>
          <p class="text-sm text-gray-600 mb-1">{{ company.industry }}</p>
          <div class="flex items-center text-sm text-gray-500">
            <MapPinIcon class="h-4 w-4 mr-1 flex-shrink-0" />
            <span class="truncate">{{ company.location }}</span>
            <span class="mx-2">•</span>
            <span>{{ company.size }}</span>
          </div>
        </div>
      </div>

      <!-- Follow Button -->
      <button
        @click.stop="$emit('follow', company)"
        :class="[
          'inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium transition-colors duration-200',
          company.is_following
            ? 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
        ]"
      >
        <HeartIcon 
          :class="[
            'h-4 w-4 mr-1',
            company.is_following ? 'fill-current text-indigo-600' : ''
          ]"
        />
        {{ company.is_following ? 'Following' : 'Follow' }}
      </button>
    </div>

    <!-- Company Description -->
    <p class="text-gray-700 text-sm leading-relaxed mb-4 line-clamp-2">
      {{ company.description }}
    </p>

    <!-- Company Stats -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-4 text-sm text-gray-600">
        <!-- Jobs Count -->
        <div class="flex items-center">
          <BriefcaseIcon class="h-4 w-4 mr-1" />
          <span>{{ company.jobs_count }} {{ company.jobs_count === 1 ? 'job' : 'jobs' }}</span>
        </div>

        <!-- Followers -->
        <div class="flex items-center">
          <UsersIcon class="h-4 w-4 mr-1" />
          <span>{{ formatNumber(company.followers_count) }} followers</span>
        </div>

        <!-- Rating -->
        <div v-if="company.rating" class="flex items-center">
          <StarIcon class="h-4 w-4 mr-1 text-yellow-400 fill-current" />
          <span>{{ company.rating.toFixed(1) }}</span>
        </div>
      </div>

      <!-- Founded -->
      <div v-if="company.founded" class="text-xs text-gray-500">
        Founded {{ company.founded }}
      </div>
    </div>

    <!-- Tech Stack -->
    <div v-if="company.tech_stack && company.tech_stack.length > 0" class="mb-4">
      <div class="flex flex-wrap gap-1">
        <span
          v-for="tech in company.tech_stack.slice(0, 4)"
          :key="tech"
          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800"
        >
          {{ tech }}
        </span>
        <span
          v-if="company.tech_stack.length > 4"
          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700"
        >
          +{{ company.tech_stack.length - 4 }} more
        </span>
      </div>
    </div>

    <!-- Benefits Preview -->
    <div v-if="company.benefits && company.benefits.length > 0" class="mb-4">
      <div class="flex flex-wrap gap-1">
        <span
          v-for="benefit in company.benefits.slice(0, 3)"
          :key="benefit"
          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800"
        >
          <CheckCircleIcon class="h-3 w-3 mr-1" />
          {{ benefit }}
        </span>
        <span
          v-if="company.benefits.length > 3"
          class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700"
        >
          +{{ company.benefits.length - 3 }} more
        </span>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
      <div class="flex items-center space-x-3">
        <!-- View Jobs -->
        <button
          @click.stop="viewJobs"
          class="text-sm text-indigo-600 hover:text-indigo-700 font-medium"
        >
          View Jobs
        </button>

        <!-- Visit Website -->
        <button
          v-if="company.website"
          @click.stop="openWebsite"
          class="flex items-center text-sm text-gray-600 hover:text-gray-700"
        >
          <ArrowTopRightOnSquareIcon class="h-4 w-4 mr-1" />
          Website
        </button>
      </div>

      <!-- Share -->
      <button
        @click.stop="shareCompany"
        class="flex items-center text-sm text-gray-600 hover:text-gray-700"
      >
        <ShareIcon class="h-4 w-4 mr-1" />
        Share
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';

// Icons
import {
  StarIcon,
  MapPinIcon,
  HeartIcon,
  BriefcaseIcon,
  UsersIcon,
  CheckCircleIcon,
  ArrowTopRightOnSquareIcon,
  ShareIcon
} from '@heroicons/vue/24/outline';

interface Company {
  id: number;
  name: string;
  slug?: string;
  logo?: string;
  industry: string;
  location: string;
  size: string;
  founded?: number;
  description: string;
  website?: string;
  jobs_count: number;
  followers_count: number;
  is_following: boolean;
  is_featured?: boolean;
  rating?: number;
  benefits?: string[];
  tech_stack?: string[];
}

interface Props {
  company: Company;
}

interface Emits {
  (e: 'click', company: Company): void;
  (e: 'follow', company: Company): void;
}

defineProps<Props>();
defineEmits<Emits>();

const router = useRouter();

// Methods
const formatNumber = (num: number): string => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M';
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K';
  }
  return num.toString();
};

const viewJobs = () => {
  router.push({
    name: 'jobs',
    query: { company: props.company.slug || props.company.id }
  });
};

const openWebsite = () => {
  if (props.company.website) {
    window.open(props.company.website, '_blank', 'noopener,noreferrer');
  }
};

const shareCompany = () => {
  const url = `${window.location.origin}/companies/${props.company.slug || props.company.id}`;
  
  if (navigator.share) {
    navigator.share({
      title: `${props.company.name} - Company Profile`,
      text: `Check out ${props.company.name} on our job portal`,
      url: url
    });
  } else if (navigator.clipboard) {
    navigator.clipboard.writeText(url).then(() => {
      // Show success message
      console.log('Company URL copied to clipboard');
    });
  }
};
</script>

<style scoped>
/* Text truncation for description */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

/* Hover effects */
.hover\\:border-indigo-300:hover {
  border-color: rgb(165 180 252);
}

/* Smooth transitions */
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style> 