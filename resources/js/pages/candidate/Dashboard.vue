<template>
  <MainLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="bg-white px-6 py-8">
        <div class="max-w-7xl mx-auto">
          <!-- Welcome Header -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                Welcome back, {{ user?.name || 'Candidate' }}! 👋
              </h1>
              <p class="text-gray-600 text-lg">
                Discover amazing opportunities and track your job applications.
              </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex flex-col sm:flex-row gap-3">
              <BaseButton
                variant="primary"
                size="lg"
                :to="{ name: 'jobs.index' }"
                tag="router-link"
                class="justify-center sm:justify-start"
              >
                <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                Browse Jobs
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="lg"
                :to="{ name: 'candidate.profile' }"
                tag="router-link"
                class="justify-center sm:justify-start"
              >
                <UserIcon class="h-5 w-5 mr-2" />
                Update Profile
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Dashboard Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Applications Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <BriefcaseIcon class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Applications</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.totalApplications }}</p>
              <p class="text-xs text-gray-500">{{ stats.newApplicationsThisWeek }} this week</p>
            </div>
          </div>
        </div>

        <!-- Interview Invitations -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <ChatBubbleLeftRightIcon class="h-6 w-6 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Interviews</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.interviewInvitations }}</p>
              <p class="text-xs text-gray-500">{{ stats.upcomingInterviews }} upcoming</p>
            </div>
          </div>
        </div>

        <!-- Profile Views -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <EyeIcon class="h-6 w-6 text-purple-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Profile Views</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.profileViews }}</p>
              <p class="text-xs text-gray-500">{{ stats.profileViewsThisWeek }} this week</p>
            </div>
          </div>
        </div>

        <!-- Saved Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <BookmarkIcon class="h-6 w-6 text-yellow-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Saved Jobs</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.savedJobs }}</p>
              <p class="text-xs text-gray-500">{{ stats.newSavedJobs }} new matches</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (Left Column - 2/3) -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Profile Completion Widget -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h2 class="text-lg font-semibold text-gray-900">Profile Completion</h2>
              <span class="text-sm text-gray-500">{{ profileCompletion }}% complete</span>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
              <div 
                class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${profileCompletion}%` }"
              ></div>
            </div>
            
            <!-- Completion Tasks -->
            <div class="space-y-3">
              <div 
                v-for="task in profileTasks" 
                :key="task.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center">
                  <CheckCircleIcon 
                    v-if="task.completed" 
                    class="h-5 w-5 text-green-500 mr-3" 
                  />
                  <ExclamationCircleIcon 
                    v-else 
                    class="h-5 w-5 text-yellow-500 mr-3" 
                  />
                  <span :class="['text-sm', task.completed ? 'text-gray-600' : 'text-gray-900 font-medium']">
                    {{ task.title }}
                  </span>
                </div>
                <BaseButton
                  v-if="!task.completed"
                  variant="ghost"
                  size="sm"
                  :to="task.action"
                  tag="router-link"
                >
                  {{ task.actionText }}
                </BaseButton>
              </div>
            </div>
          </div>

          <!-- Recent Applications -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <h2 class="text-lg font-semibold text-gray-900">Recent Applications</h2>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'candidate.applications' }"
                tag="router-link"
              >
                View All
                <ArrowRightIcon class="h-4 w-4 ml-1" />
              </BaseButton>
            </div>
            
            <div class="divide-y divide-gray-200">
              <div 
                v-for="application in recentApplications" 
                :key="application.id"
                class="p-6 hover:bg-gray-50 transition-colors duration-200"
              >
                <div class="flex items-start justify-between">
                  <div class="flex items-start space-x-3">
                    <!-- Company Logo -->
                    <img
                      v-if="application.company?.logo"
                      :src="application.company.logo"
                      :alt="application.company.name"
                      class="w-12 h-12 rounded-lg object-cover"
                    />
                    <div
                      v-else
                      class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center"
                    >
                      <span class="text-lg font-semibold text-indigo-600">
                        {{ (application.company?.name || 'C').charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    
                    <!-- Application Info -->
                    <div class="flex-1">
                      <h3 class="text-sm font-medium text-gray-900 mb-1">
                        {{ application.job_title }}
                      </h3>
                      <p class="text-sm text-gray-600 mb-2">{{ application.company?.name }}</p>
                      <div class="flex items-center text-xs text-gray-500 space-x-4">
                        <span>Applied {{ formatDate(application.applied_at) }}</span>
                        <span>•</span>
                        <span>{{ application.location }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Status Badge -->
                  <span 
                    :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      getStatusBadgeClasses(application.status)
                    ]"
                  >
                    {{ formatStatus(application.status) }}
                  </span>
                </div>
              </div>
              
              <div v-if="recentApplications.length === 0" class="p-6 text-center">
                <BriefcaseIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
                <p class="text-gray-500 mb-4">No applications yet</p>
                <BaseButton
                  variant="primary"
                  :to="{ name: 'jobs.index' }"
                  tag="router-link"
                >
                  Start Applying to Jobs
                </BaseButton>
              </div>
            </div>
          </div>

          <!-- Job Recommendations -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div>
                <h2 class="text-lg font-semibold text-gray-900">Recommended for You</h2>
                <p class="text-sm text-gray-600 mt-1">Based on your profile and preferences</p>
              </div>
              <BaseButton
                variant="ghost"
                size="sm"
                @click="refreshRecommendations"
                :loading="isRefreshingRecommendations"
              >
                <ArrowPathIcon class="h-4 w-4 mr-1" />
                Refresh
              </BaseButton>
            </div>
            
            <div class="p-6">
              <div v-if="isLoadingRecommendations" class="space-y-4">
                <!-- Loading Skeletons -->
                <div v-for="i in 3" :key="i" class="animate-pulse">
                  <div class="flex space-x-3">
                    <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                    <div class="flex-1 space-y-2">
                      <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                      <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                      <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="space-y-4">
                <JobCard
                  v-for="job in recommendedJobs"
                  :key="job.id"
                  :job="job"
                  :show-company-logo="true"
                  class="border border-gray-100 rounded-lg"
                />
                
                <div v-if="recommendedJobs.length === 0" class="text-center py-8">
                  <MagnifyingGlassIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
                  <p class="text-gray-500 mb-4">No recommendations yet</p>
                  <p class="text-sm text-gray-400 mb-4">Complete your profile to get personalized job recommendations</p>
                  <BaseButton
                    variant="primary"
                    :to="{ name: 'candidate.profile' }"
                    tag="router-link"
                  >
                    Complete Profile
                  </BaseButton>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar (Right Column - 1/3) -->
        <div class="space-y-6">
          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'jobs.index' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                Search Jobs
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'candidate.applications' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <DocumentTextIcon class="h-5 w-5 mr-2" />
                My Applications
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'candidate.saved-jobs' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <BookmarkIcon class="h-5 w-5 mr-2" />
                Saved Jobs
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'candidate.resume' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <DocumentIcon class="h-5 w-5 mr-2" />
                Manage Resume
              </BaseButton>
            </div>
          </div>

          <!-- Upcoming Interviews -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Interviews</h3>
            
            <div v-if="upcomingInterviews.length > 0" class="space-y-4">
              <div 
                v-for="interview in upcomingInterviews" 
                :key="interview.id"
                class="border border-gray-100 rounded-lg p-4"
              >
                <div class="flex items-start justify-between mb-2">
                  <h4 class="text-sm font-medium text-gray-900">{{ interview.job_title }}</h4>
                  <span class="text-xs text-gray-500">{{ interview.type }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">{{ interview.company_name }}</p>
                <div class="flex items-center text-xs text-gray-500">
                  <CalendarIcon class="h-4 w-4 mr-1" />
                  {{ formatInterviewDate(interview.scheduled_at) }}
                </div>
              </div>
            </div>
            
            <div v-else class="text-center py-4">
              <CalendarIcon class="h-8 w-8 text-gray-300 mx-auto mb-2" />
              <p class="text-sm text-gray-500">No upcoming interviews</p>
            </div>
          </div>

          <!-- Job Alerts -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Job Alerts</h3>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'candidate.alerts' }"
                tag="router-link"
              >
                Manage
              </BaseButton>
            </div>
            
            <div class="space-y-3">
              <div 
                v-for="alert in jobAlerts" 
                :key="alert.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div>
                  <p class="text-sm font-medium text-gray-900">{{ alert.title }}</p>
                  <p class="text-xs text-gray-500">{{ alert.criteria }}</p>
                </div>
                <div class="flex items-center">
                  <span class="text-xs text-green-600 mr-2">{{ alert.new_jobs }} new</span>
                  <div 
                    :class="[
                      'w-2 h-2 rounded-full',
                      alert.active ? 'bg-green-400' : 'bg-gray-300'
                    ]"
                  ></div>
                </div>
              </div>
              
              <BaseButton
                variant="outline-primary"
                size="sm"
                :to="{ name: 'candidate.alerts.create' }"
                tag="router-link"
                class="w-full justify-center"
              >
                <PlusIcon class="h-4 w-4 mr-1" />
                Create Alert
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { useApiGet } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import JobCard from '@/components/jobs/JobCard.vue';

// Icons
import {
  MagnifyingGlassIcon,
  UserIcon,
  BriefcaseIcon,
  ChatBubbleLeftRightIcon,
  EyeIcon,
  BookmarkIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  ArrowRightIcon,
  ArrowPathIcon,
  DocumentTextIcon,
  DocumentIcon,
  CalendarIcon,
  PlusIcon
} from '@heroicons/vue/24/outline';

const { user } = useAuth();

// Breadcrumbs
const breadcrumbs = [
  { label: 'Candidate', to: '/candidate' },
  { label: 'Dashboard' }
];

// State
const isRefreshingRecommendations = ref(false);
const isLoadingRecommendations = ref(false);

// Mock data - in real app, these would come from API
const stats = ref({
  totalApplications: 12,
  newApplicationsThisWeek: 3,
  interviewInvitations: 2,
  upcomingInterviews: 1,
  profileViews: 47,
  profileViewsThisWeek: 8,
  savedJobs: 23,
  newSavedJobs: 5
});

const profileCompletion = ref(75);
const profileTasks = ref([
  {
    id: 1,
    title: 'Add profile photo',
    completed: true,
    action: '/candidate/profile#photo',
    actionText: 'Upload'
  },
  {
    id: 2,
    title: 'Complete work experience',
    completed: true,
    action: '/candidate/profile#experience',
    actionText: 'Add'
  },
  {
    id: 3,
    title: 'Add skills and certifications',
    completed: false,
    action: '/candidate/profile#skills',
    actionText: 'Complete'
  },
  {
    id: 4,
    title: 'Upload resume',
    completed: false,
    action: '/candidate/resume',
    actionText: 'Upload'
  }
]);

// API calls for real data
const { data: recentApplications } = useApiGet('/api/candidate/applications/recent', {}, {
  immediate: true,
  defaultValue: []
});

const { data: recommendedJobs } = useApiGet('/api/candidate/recommendations', {}, {
  immediate: true,
  defaultValue: []
});

const { data: upcomingInterviews } = useApiGet('/api/candidate/interviews/upcoming', {}, {
  immediate: true,
  defaultValue: []
});

const { data: jobAlerts } = useApiGet('/api/candidate/job-alerts', {}, {
  immediate: true,
  defaultValue: []
});

// Methods
const formatDate = (dateString: string): string => {
  const date = new Date(dateString);
  const now = new Date();
  const diffTime = Math.abs(now.getTime() - date.getTime());
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

  if (diffDays === 1) return '1 day ago';
  if (diffDays < 7) return `${diffDays} days ago`;
  if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
  return `${Math.floor(diffDays / 30)} months ago`;
};

const formatInterviewDate = (dateString: string): string => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const formatStatus = (status: string): string => {
  const statusMap: Record<string, string> = {
    'pending': 'Pending',
    'reviewing': 'Under Review',
    'interview': 'Interview',
    'accepted': 'Accepted',
    'rejected': 'Rejected'
  };
  return statusMap[status] || status;
};

const getStatusBadgeClasses = (status: string): string => {
  const classMap: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'reviewing': 'bg-blue-100 text-blue-800',
    'interview': 'bg-purple-100 text-purple-800',
    'accepted': 'bg-green-100 text-green-800',
    'rejected': 'bg-red-100 text-red-800'
  };
  return classMap[status] || 'bg-gray-100 text-gray-800';
};

const refreshRecommendations = async () => {
  isRefreshingRecommendations.value = true;
  
  try {
    // In real app, trigger API refresh
    await new Promise(resolve => setTimeout(resolve, 1000));
    // Reload recommendations
  } catch (error) {
    console.error('Failed to refresh recommendations:', error);
  } finally {
    isRefreshingRecommendations.value = false;
  }
};

// Load dashboard data
onMounted(async () => {
  try {
    // Load real-time stats
    const response = await fetch('/api/candidate/dashboard/stats');
    if (response.ok) {
      const data = await response.json();
      stats.value = { ...stats.value, ...data };
    }
  } catch (error) {
    console.log('Could not load real-time stats, using defaults');
  }
});
</script>

<style scoped>
/* Custom animations for smooth transitions */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-in {
  animation: slideIn 0.5s ease-out;
}

/* Hover effects for interactive elements */
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease-in-out;
}

/* Progress bar animation */
.progress-bar {
  transition: width 0.8s ease-in-out;
}

/* Gradient text effect */
.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Card loading skeleton animation */
@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Custom scrollbar for long content */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style> 