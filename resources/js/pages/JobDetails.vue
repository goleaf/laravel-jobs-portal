<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <LoadingSpinner
      v-if="isLoading"
      overlay
      size="lg"
      text="Loading job details..."
    />

    <!-- Job Not Found -->
    <div v-else-if="!job" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="text-center bg-white rounded-lg shadow-sm border border-gray-200 p-12">
        <div class="w-20 h-20 mx-auto mb-4 text-gray-300">
          <ExclamationTriangleIcon class="h-20 w-20" />
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Job Not Found</h2>
        <p class="text-gray-600 mb-6">
          The job you're looking for doesn't exist or has been removed.
        </p>
        <BaseButton
          variant="primary"
          @click="router.push({ name: 'jobs' })"
        >
          Browse All Jobs
        </BaseButton>
      </div>
    </div>

    <!-- Job Details -->
    <div v-else>
      <!-- Job Header Section -->
      <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <!-- Breadcrumb -->
          <nav class="mb-6">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
              <li>
                <router-link
                  to="/"
                  class="hover:text-indigo-600 transition-colors duration-200"
                >
                  Home
                </router-link>
              </li>
              <ChevronRightIcon class="h-4 w-4" />
              <li>
                <router-link
                  :to="{ name: 'jobs' }"
                  class="hover:text-indigo-600 transition-colors duration-200"
                >
                  Jobs
                </router-link>
              </li>
              <ChevronRightIcon class="h-4 w-4" />
              <li class="text-gray-900 font-medium truncate">{{ job.title }}</li>
            </ol>
          </nav>

          <div class="lg:grid lg:grid-cols-3 lg:gap-8">
            <!-- Job Info -->
            <div class="lg:col-span-2">
              <div class="flex items-start space-x-4 mb-6">
                <!-- Company Logo -->
                <div class="flex-shrink-0">
                  <div
                    v-if="job.company.logo"
                    class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200"
                  >
                    <img
                      :src="job.company.logo"
                      :alt="job.company.name"
                      class="w-full h-full object-cover"
                    />
                  </div>
                  <div
                    v-else
                    class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xl"
                  >
                    {{ job.company.name.charAt(0).toUpperCase() }}
                  </div>
                </div>

                <div class="flex-1 min-w-0">
                  <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ job.title }}</h1>
                  <div class="flex flex-wrap items-center gap-4 text-lg text-gray-600 mb-4">
                    <router-link
                      :to="{ name: 'companies.show', params: { id: job.company.id } }"
                      class="font-semibold hover:text-indigo-600 transition-colors duration-200"
                    >
                      {{ job.company.name }}
                    </router-link>
                    <span class="inline-flex items-center">
                      <MapPinIcon class="h-4 w-4 mr-1" />
                      {{ job.location }}
                    </span>
                  </div>

                  <!-- Job Badges -->
                  <div class="flex flex-wrap gap-2 mb-4">
                    <Badge
                      :text="job.employment_type"
                      variant="primary"
                      :left-icon="BriefcaseIcon"
                    />
                    <Badge
                      :text="job.experience_level"
                      variant="secondary"
                      :left-icon="ChartBarIcon"
                    />
                    <Badge
                      v-if="job.remote_ok"
                      text="Remote OK"
                      variant="success"
                      :left-icon="ComputerDesktopIcon"
                    />
                    <Badge
                      v-if="job.is_urgent"
                      text="Urgent"
                      variant="warning"
                      :left-icon="ExclamationCircleIcon"
                    />
                    <Badge
                      v-if="job.is_featured"
                      text="Featured"
                      variant="info"
                      :left-icon="StarIcon"
                    />
                  </div>

                  <!-- Salary & Posted Info -->
                  <div class="space-y-2">
                    <div v-if="job.salary_min || job.salary_max" class="flex items-center text-lg font-semibold text-gray-900">
                      <CurrencyDollarIcon class="h-5 w-5 mr-2 text-green-600" />
                      <span v-if="job.salary_min && job.salary_max">
                        ${{ job.salary_min.toLocaleString() }} - ${{ job.salary_max.toLocaleString() }}
                      </span>
                      <span v-else-if="job.salary_min">
                        From ${{ job.salary_min.toLocaleString() }}
                      </span>
                      <span v-else>
                        Up to ${{ job.salary_max.toLocaleString() }}
                      </span>
                      <span class="text-sm text-gray-600 ml-2">per year</span>
                    </div>

                    <div class="flex items-center text-sm text-gray-600">
                      <CalendarIcon class="h-4 w-4 mr-2" />
                      Posted {{ formatRelativeTime(job.created_at) }}
                      <span v-if="job.deadline" class="ml-4">
                        <ClockIcon class="h-4 w-4 mr-1 inline" />
                        Apply by {{ formatDate(job.deadline) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Sidebar -->
            <div class="lg:col-span-1">
              <div class="bg-gray-50 rounded-lg p-6 sticky top-4">
                <div class="space-y-4">
                  <!-- Apply Button -->
                  <BaseButton
                    variant="primary"
                    size="lg"
                    :full-width="true"
                    :loading="isApplying"
                    @click="applyToJob"
                  >
                    <PaperAirplaneIcon class="h-5 w-5 mr-2" />
                    Apply Now
                  </BaseButton>

                  <!-- Bookmark Button -->
                  <BaseButton
                    variant="outline-primary"
                    :full-width="true"
                    :loading="isBookmarking"
                    @click="toggleBookmark"
                  >
                    <BookmarkIcon class="h-5 w-5 mr-2" :class="{ 'fill-current': isBookmarked }" />
                    {{ isBookmarked ? 'Bookmarked' : 'Bookmark' }}
                  </BaseButton>

                  <!-- Share Button -->
                  <BaseButton
                    variant="outline"
                    :full-width="true"
                    @click="shareJob"
                  >
                    <ShareIcon class="h-5 w-5 mr-2" />
                    Share Job
                  </BaseButton>
                </div>

                <!-- Job Stats -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                  <h4 class="text-sm font-medium text-gray-900 mb-3">Job Stats</h4>
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Applications</span>
                      <span class="font-medium">{{ job.applications_count || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Views</span>
                      <span class="font-medium">{{ job.views_count || 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Posted</span>
                      <span class="font-medium">{{ formatDate(job.created_at) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-3 lg:gap-8">
          <!-- Job Description -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Description -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Description</h2>
              <div 
                class="prose prose-indigo max-w-none"
                v-html="job.description"
              ></div>
            </div>

            <!-- Requirements -->
            <div v-if="job.requirements" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Requirements</h2>
              <div 
                class="prose prose-indigo max-w-none"
                v-html="job.requirements"
              ></div>
            </div>

            <!-- Benefits -->
            <div v-if="job.benefits" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Benefits</h2>
              <div 
                class="prose prose-indigo max-w-none"
                v-html="job.benefits"
              ></div>
            </div>

            <!-- Skills -->
            <div v-if="job.skills && job.skills.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Required Skills</h2>
              <div class="flex flex-wrap gap-2">
                <Badge
                  v-for="skill in job.skills"
                  :key="skill"
                  :text="skill"
                  variant="secondary"
                  size="md"
                />
              </div>
            </div>
          </div>

          <!-- Company Info Sidebar -->
          <div class="lg:col-span-1 mt-8 lg:mt-0">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">About {{ job.company.name }}</h3>
              
              <!-- Company Logo -->
              <div class="flex items-center space-x-3 mb-4">
                <div
                  v-if="job.company.logo"
                  class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200"
                >
                  <img
                    :src="job.company.logo"
                    :alt="job.company.name"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div
                  v-else
                  class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold"
                >
                  {{ job.company.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <h4 class="font-semibold text-gray-900">{{ job.company.name }}</h4>
                  <p class="text-sm text-gray-600">{{ job.company.industry }}</p>
                </div>
              </div>

              <!-- Company Description -->
              <p v-if="job.company.description" class="text-sm text-gray-700 mb-4 line-clamp-3">
                {{ job.company.description }}
              </p>

              <!-- Company Stats -->
              <div class="space-y-2 text-sm mb-4">
                <div v-if="job.company.size" class="flex justify-between">
                  <span class="text-gray-600">Company Size</span>
                  <span class="font-medium">{{ job.company.size }}</span>
                </div>
                <div v-if="job.company.founded" class="flex justify-between">
                  <span class="text-gray-600">Founded</span>
                  <span class="font-medium">{{ job.company.founded }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Open Jobs</span>
                  <span class="font-medium">{{ job.company.jobs_count || 0 }}</span>
                </div>
              </div>

              <!-- View Company Button -->
              <BaseButton
                variant="outline-primary"
                :full-width="true"
                :to="{ name: 'companies.show', params: { id: job.company.id } }"
                tag="router-link"
              >
                View Company Profile
              </BaseButton>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Jobs Section -->
      <div v-if="relatedJobs.length > 0" class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Jobs</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <JobCard
              v-for="relatedJob in relatedJobs"
              :key="relatedJob.id"
              :job="relatedJob"
              :show-company-logo="true"
              class="hover:transform hover:scale-105 transition-transform duration-200"
              @bookmark="handleJobBookmark"
              @apply="handleJobApply"
              @view="viewJobDetails"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useHead } from '@/composables/useHead';
import BaseButton from '@/components/base/BaseButton.vue';
import JobApplicationModal from '@/components/jobs/JobApplicationModal.vue';
import LoadingSpinner from '../components/ui/LoadingSpinner.vue'
import Badge from '../components/ui/Badge.vue'
import JobCard from '../components/jobs/JobCard.vue'

// Icons
import {
  MapPinIcon,
  BriefcaseIcon,
  ChartBarIcon,
  ComputerDesktopIcon,
  ExclamationCircleIcon,
  StarIcon,
  CurrencyDollarIcon,
  CalendarIcon,
  ClockIcon,
  PaperAirplaneIcon,
  BookmarkIcon,
  ShareIcon,
  CheckCircleIcon,
  ArrowTopRightOnSquareIcon,
  BuildingOfficeIcon,
  ChevronRightIcon,
  ExclamationTriangleIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();
const route = useRoute();

// State
const isLoading = ref(true);
const isApplying = ref(false);
const isBookmarking = ref(false);
const showApplicationModal = ref(false);

// Mock job data - replace with API call
const job = ref({
  id: 1,
  title: 'Senior Frontend Developer',
  slug: 'senior-frontend-developer',
  company: {
    id: 1,
    name: 'TechCorp Inc.',
    logo: null,
    industry: 'Technology',
    size: '51-200 employees',
    founded: '2015',
    website: 'https://techcorp.com',
    description: 'TechCorp Inc. is a leading technology company focused on building innovative web applications and digital solutions for modern businesses.'
  },
  location: 'New York, NY',
  department: 'Engineering',
  employment_type: 'Full-time',
  experience_level: 'Senior',
  salary_min: 90000,
  salary_max: 130000,
  remote_ok: true,
  is_urgent: false,
  is_featured: true,
  is_bookmarked: false,
  description: `
    <p>We are looking for a talented Senior Frontend Developer to join our dynamic engineering team. You will be responsible for building and maintaining high-quality web applications using modern frontend technologies.</p>
    
    <h3>Responsibilities:</h3>
    <ul>
      <li>Develop responsive and interactive user interfaces using Vue.js and TypeScript</li>
      <li>Collaborate with designers and backend developers to implement new features</li>
      <li>Optimize applications for maximum speed and scalability</li>
      <li>Write clean, maintainable, and well-documented code</li>
      <li>Participate in code reviews and provide constructive feedback</li>
      <li>Stay up-to-date with the latest frontend technologies and best practices</li>
    </ul>
  `,
  requirements: `
    <h3>Required Qualifications:</h3>
    <ul>
      <li>5+ years of experience in frontend development</li>
      <li>Strong proficiency in Vue.js, JavaScript, and TypeScript</li>
      <li>Experience with modern CSS frameworks (TailwindCSS preferred)</li>
      <li>Knowledge of build tools like Vite, Webpack, or similar</li>
      <li>Experience with version control systems (Git)</li>
      <li>Strong problem-solving skills and attention to detail</li>
    </ul>
    
    <h3>Preferred Qualifications:</h3>
    <ul>
      <li>Experience with Node.js and full-stack development</li>
      <li>Knowledge of testing frameworks (Jest, Cypress, etc.)</li>
      <li>Experience with cloud platforms (AWS, Azure, GCP)</li>
      <li>Understanding of DevOps practices and CI/CD pipelines</li>
    </ul>
  `,
  skills: ['Vue.js', 'TypeScript', 'TailwindCSS', 'Node.js', 'JavaScript', 'HTML5', 'CSS3', 'Git'],
  benefits: [
    'Health, dental, and vision insurance',
    'Flexible working hours',
    'Remote work options',
    'Professional development budget',
    'Unlimited PTO',
    'Stock options',
    'Modern equipment provided',
    'Gym membership reimbursement'
  ],
  created_at: '2024-01-15T10:00:00Z',
  deadline: '2024-02-15T23:59:59Z'
});

const similarJobs = ref([
  {
    id: 2,
    title: 'Frontend Developer',
    company: { name: 'StartupXYZ' },
    location: 'San Francisco, CA',
    salary_max: 110000,
    created_at: '2024-01-14T08:00:00Z'
  },
  {
    id: 3,
    title: 'Senior Vue.js Developer',
    company: { name: 'Innovation Labs' },
    location: 'Remote',
    salary_max: 140000,
    created_at: '2024-01-13T15:30:00Z'
  }
]);

// Methods
const formatRelativeTime = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();
  const diffInMs = now.getTime() - date.getTime();
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

  if (diffInDays === 0) return 'today';
  if (diffInDays === 1) return 'yesterday';
  if (diffInDays < 7) return `${diffInDays} days ago`;
  if (diffInDays < 30) return `${Math.floor(diffInDays / 7)} weeks ago`;
  return `${Math.floor(diffInDays / 30)} months ago`;
};

const formatDate = (dateString: string): string => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const applyToJob = () => {
  showApplicationModal.value = true;
};

const toggleBookmark = async () => {
  isBookmarking.value = true;
  
  try {
    // API call to toggle bookmark
    // await apiService.post(`/api/jobs/${job.value.id}/bookmark`);
    job.value.is_bookmarked = !job.value.is_bookmarked;
  } catch (error) {
    console.error('Failed to toggle bookmark:', error);
  } finally {
    isBookmarking.value = false;
  }
};

const shareJob = () => {
  if (navigator.share) {
    navigator.share({
      title: `${job.value.title} at ${job.value.company.name}`,
      text: `Check out this job opportunity: ${job.value.title}`,
      url: window.location.href
    });
  } else {
    copyJobLink();
  }
};

const shareVia = (platform: string) => {
  const url = encodeURIComponent(window.location.href);
  const title = encodeURIComponent(`${job.value.title} at ${job.value.company.name}`);
  
  let shareUrl = '';
  
  switch (platform) {
    case 'linkedin':
      shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
      break;
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
      break;
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
      break;
  }
  
  if (shareUrl) {
    window.open(shareUrl, '_blank', 'width=600,height=400');
  }
};

const copyJobLink = async () => {
  try {
    await navigator.clipboard.writeText(window.location.href);
    // Show success message
    console.log('Link copied to clipboard');
  } catch (error) {
    console.error('Failed to copy link:', error);
  }
};

const viewJob = (job: any) => {
  router.push({
    name: 'jobs.show',
    params: { slug: job.slug || job.id }
  });
};

const viewCompany = () => {
  router.push({
    name: 'companies.show',
    params: { id: job.value.company.id }
  });
};

const handleApplicationSubmitted = () => {
  showApplicationModal.value = false;
  // Show success message or redirect
  console.log('Application submitted successfully');
};

const loadJob = async () => {
  isLoading.value = true;
  
  try {
    const jobId = route.params.slug || route.params.id;
    
    // API call to load job details
    // const response = await apiService.get(`/api/jobs/${jobId}`);
    // job.value = response.data.job;
    // similarJobs.value = response.data.similarJobs;
    
    // Simulate API delay
    await new Promise(resolve => setTimeout(resolve, 1000));
  } catch (error) {
    console.error('Failed to load job:', error);
    job.value = null;
  } finally {
    isLoading.value = false;
  }
};

// Lifecycle
onMounted(() => {
  loadJob();
});

// SEO
useHead({
  title: computed(() => job.value ? `${job.value.title} at ${job.value.company.name} - Jobs` : 'Job Details'),
  meta: computed(() => job.value ? [
    {
      name: 'description',
      content: `${job.value.title} position at ${job.value.company.name} in ${job.value.location}. ${job.value.employment_type} role with competitive salary.`
    },
    {
      name: 'keywords',
      content: `${job.value.title}, ${job.value.company.name}, ${job.value.location}, ${job.value.employment_type}, jobs, careers`
    },
    {
      property: 'og:title',
      content: `${job.value.title} at ${job.value.company.name}`
    },
    {
      property: 'og:description',
      content: `Apply for ${job.value.title} position at ${job.value.company.name}. Located in ${job.value.location}.`
    },
    {
      property: 'og:type',
      content: 'website'
    }
  ] : [])
});
</script>

<style scoped>
/* Custom styles for job description content */
.prose h3 {
  @apply text-lg font-semibold text-gray-900 mt-6 mb-3;
}

.prose ul {
  @apply list-disc list-inside space-y-1 text-gray-700;
}

.prose li {
  @apply leading-relaxed;
}

.prose p {
  @apply text-gray-700 leading-relaxed mb-4;
}
</style>