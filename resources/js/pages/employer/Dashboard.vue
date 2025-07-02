<template>
  <MainLayout :breadcrumbs="breadcrumbs">
    <template #header>
      <div class="bg-white px-6 py-8">
        <div class="max-w-7xl mx-auto">
          <!-- Welcome Header -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
              <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">
                Welcome back, {{ user?.company_name || user?.name || 'Employer' }}! 👋
              </h1>
              <p class="text-gray-600 text-lg">
                Manage your job postings and find the perfect candidates for your team.
              </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex flex-col sm:flex-row gap-3">
              <BaseButton
                variant="primary"
                size="lg"
                :to="{ name: 'employer.jobs.create' }"
                tag="router-link"
                class="justify-center sm:justify-start"
              >
                <PlusIcon class="h-5 w-5 mr-2" />
                Post New Job
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="lg"
                :to="{ name: 'employer.candidates' }"
                tag="router-link"
                class="justify-center sm:justify-start"
              >
                <UsersIcon class="h-5 w-5 mr-2" />
                Browse Candidates
              </BaseButton>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Dashboard Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Jobs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
              <BriefcaseIcon class="h-6 w-6 text-blue-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Active Jobs</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.activeJobs }}</p>
              <p class="text-xs text-gray-500">{{ stats.newJobsThisWeek }} posted this week</p>
            </div>
          </div>
        </div>

        <!-- Total Applications -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
              <DocumentTextIcon class="h-6 w-6 text-green-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Applications</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.totalApplications }}</p>
              <p class="text-xs text-gray-500">{{ stats.newApplicationsToday }} new today</p>
            </div>
          </div>
        </div>

        <!-- Interviews Scheduled -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
              <CalendarIcon class="h-6 w-6 text-purple-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Interviews</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.scheduledInterviews }}</p>
              <p class="text-xs text-gray-500">{{ stats.interviewsThisWeek }} this week</p>
            </div>
          </div>
        </div>

        <!-- Profile Views -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
              <EyeIcon class="h-6 w-6 text-yellow-600" />
            </div>
            <div class="ml-4">
              <p class="text-sm font-medium text-gray-600">Profile Views</p>
              <p class="text-2xl font-bold text-gray-900">{{ stats.companyViews }}</p>
              <p class="text-xs text-gray-500">{{ stats.viewsThisWeek }} this week</p>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (Left Column - 2/3) -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Hiring Funnel Overview -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-lg font-semibold text-gray-900">Hiring Funnel</h2>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'employer.analytics' }"
                tag="router-link"
              >
                View Details
                <ArrowRightIcon class="h-4 w-4 ml-1" />
              </BaseButton>
            </div>
            
            <!-- Funnel Stages -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div 
                v-for="stage in hiringFunnelStages" 
                :key="stage.id"
                class="text-center p-4 bg-gray-50 rounded-lg"
              >
                <div class="text-2xl font-bold text-gray-900 mb-1">{{ stage.count }}</div>
                <div class="text-sm text-gray-600 mb-2">{{ stage.label }}</div>
                <div 
                  :class="[
                    'text-xs px-2 py-1 rounded-full',
                    stage.change >= 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ stage.change >= 0 ? '+' : '' }}{{ stage.change }}%
                </div>
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
                :to="{ name: 'employer.applications' }"
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
                  <div class="flex items-start space-x-4">
                    <!-- Candidate Avatar -->
                    <img
                      v-if="application.candidate?.avatar"
                      :src="application.candidate.avatar"
                      :alt="application.candidate.name"
                      class="w-12 h-12 rounded-full object-cover"
                    />
                    <div
                      v-else
                      class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center"
                    >
                      <span class="text-white font-medium">
                        {{ (application.candidate?.name || 'C').charAt(0).toUpperCase() }}
                      </span>
                    </div>
                    
                    <!-- Application Info -->
                    <div class="flex-1">
                      <h3 class="text-sm font-medium text-gray-900 mb-1">
                        {{ application.candidate?.name }}
                      </h3>
                      <p class="text-sm text-gray-600 mb-2">Applied for {{ application.job_title }}</p>
                      <div class="flex items-center text-xs text-gray-500 space-x-4">
                        <span>{{ formatDate(application.applied_at) }}</span>
                        <span>•</span>
                        <span>{{ application.experience_years }}+ years experience</span>
                        <span v-if="application.match_score" class="flex items-center">
                          •
                          <span class="ml-1 text-green-600 font-medium">{{ application.match_score }}% match</span>
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Quick Actions -->
                  <div class="flex items-center space-x-2">
                    <BaseButton
                      variant="ghost"
                      size="sm"
                      @click="viewApplication(application)"
                    >
                      <EyeIcon class="h-4 w-4 mr-1" />
                      View
                    </BaseButton>
                    
                    <BaseButton
                      v-if="application.status === 'pending'"
                      variant="primary"
                      size="sm"
                      @click="scheduleInterview(application)"
                    >
                      <CalendarIcon class="h-4 w-4 mr-1" />
                      Interview
                    </BaseButton>
                  </div>
                </div>
              </div>
              
              <div v-if="recentApplications.length === 0" class="p-6 text-center">
                <DocumentTextIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
                <p class="text-gray-500 mb-4">No applications yet</p>
                <BaseButton
                  variant="primary"
                  :to="{ name: 'employer.jobs.create' }"
                  tag="router-link"
                >
                  Post Your First Job
                </BaseButton>
              </div>
            </div>
          </div>

          <!-- Active Job Postings Performance -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div>
                <h2 class="text-lg font-semibold text-gray-900">Job Performance</h2>
                <p class="text-sm text-gray-600 mt-1">Track how your job postings are performing</p>
              </div>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'employer.jobs' }"
                tag="router-link"
              >
                Manage Jobs
                <ArrowRightIcon class="h-4 w-4 ml-1" />
              </BaseButton>
            </div>
            
            <div class="p-6">
              <div v-if="isLoadingJobs" class="space-y-4">
                <!-- Loading Skeletons -->
                <div v-for="i in 3" :key="i" class="animate-pulse">
                  <div class="flex justify-between items-center p-4 border border-gray-100 rounded-lg">
                    <div class="flex-1">
                      <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                      <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="text-right">
                      <div class="h-4 bg-gray-200 rounded w-16 mb-2"></div>
                      <div class="h-3 bg-gray-200 rounded w-12"></div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div v-else class="space-y-4">
                <div
                  v-for="job in activeJobs"
                  :key="job.id"
                  class="flex justify-between items-center p-4 border border-gray-100 rounded-lg hover:border-gray-200 transition-colors duration-200"
                >
                  <div class="flex-1">
                    <h3 class="text-sm font-medium text-gray-900 mb-1">{{ job.title }}</h3>
                    <div class="flex items-center text-xs text-gray-500 space-x-4">
                      <span>Posted {{ formatDate(job.created_at) }}</span>
                      <span>•</span>
                      <span>{{ job.location }}</span>
                      <span v-if="job.urgency === 'urgent'" class="text-red-600 font-medium">• Urgent</span>
                    </div>
                  </div>
                  
                  <div class="text-right">
                    <div class="text-sm font-medium text-gray-900">{{ job.application_count }} applications</div>
                    <div class="text-xs text-gray-500">{{ job.view_count }} views</div>
                  </div>
                  
                  <div class="ml-4">
                    <BaseButton
                      variant="ghost"
                      size="sm"
                      :to="{ name: 'employer.jobs.show', params: { id: job.id } }"
                      tag="router-link"
                    >
                      <EyeIcon class="h-4 w-4" />
                    </BaseButton>
                  </div>
                </div>
                
                <div v-if="activeJobs.length === 0" class="text-center py-8">
                  <BriefcaseIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
                  <p class="text-gray-500 mb-4">No active jobs</p>
                  <BaseButton
                    variant="primary"
                    :to="{ name: 'employer.jobs.create' }"
                    tag="router-link"
                  >
                    Post Your First Job
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
                :to="{ name: 'employer.jobs.create' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <PlusIcon class="h-5 w-5 mr-2" />
                Post New Job
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'employer.applications' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <DocumentTextIcon class="h-5 w-5 mr-2" />
                Review Applications
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'employer.candidates' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <MagnifyingGlassIcon class="h-5 w-5 mr-2" />
                Search Candidates
              </BaseButton>
              
              <BaseButton
                variant="outline-primary"
                size="md"
                :to="{ name: 'employer.company.profile' }"
                tag="router-link"
                class="w-full justify-start"
              >
                <BuildingOfficeIcon class="h-5 w-5 mr-2" />
                Company Profile
              </BaseButton>
            </div>
          </div>

          <!-- Upcoming Interviews -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Upcoming Interviews</h3>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'employer.interviews' }"
                tag="router-link"
              >
                View All
              </BaseButton>
            </div>
            
            <div v-if="upcomingInterviews.length > 0" class="space-y-4">
              <div 
                v-for="interview in upcomingInterviews" 
                :key="interview.id"
                class="border border-gray-100 rounded-lg p-4"
              >
                <div class="flex items-start justify-between mb-2">
                  <h4 class="text-sm font-medium text-gray-900">{{ interview.candidate_name }}</h4>
                  <span class="text-xs text-gray-500">{{ interview.type }}</span>
                </div>
                <p class="text-sm text-gray-600 mb-2">{{ interview.job_title }}</p>
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

          <!-- Hiring Pipeline -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Hiring Pipeline</h3>
              <BaseButton
                variant="ghost"
                size="sm"
                :to="{ name: 'employer.pipeline' }"
                tag="router-link"
              >
                Manage
              </BaseButton>
            </div>
            
            <div class="space-y-3">
              <div 
                v-for="stage in pipelineStages" 
                :key="stage.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
              >
                <div class="flex items-center">
                  <div 
                    :class="[
                      'w-3 h-3 rounded-full mr-3',
                      stage.color
                    ]"
                  ></div>
                  <span class="text-sm font-medium text-gray-900">{{ stage.name }}</span>
                </div>
                <div class="flex items-center">
                  <span class="text-sm text-gray-600 mr-2">{{ stage.count }}</span>
                  <ChevronRightIcon class="h-4 w-4 text-gray-400" />
                </div>
              </div>
            </div>
          </div>

          <!-- Company Profile Completion -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-semibold text-gray-900">Profile Completion</h3>
              <span class="text-sm text-gray-500">{{ companyProfileCompletion }}%</span>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
              <div 
                class="bg-gradient-to-r from-indigo-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: `${companyProfileCompletion}%` }"
              ></div>
            </div>
            
            <!-- Completion Tasks -->
            <div class="space-y-2">
              <div 
                v-for="task in companyProfileTasks" 
                :key="task.id"
                class="flex items-center text-sm"
              >
                <CheckCircleIcon 
                  v-if="task.completed" 
                  class="h-4 w-4 text-green-500 mr-2" 
                />
                <ExclamationCircleIcon 
                  v-else 
                  class="h-4 w-4 text-yellow-500 mr-2" 
                />
                <span :class="['flex-1', task.completed ? 'text-gray-600' : 'text-gray-900']">
                  {{ task.title }}
                </span>
              </div>
              
              <BaseButton
                variant="outline-primary"
                size="sm"
                :to="{ name: 'employer.company.profile' }"
                tag="router-link"
                class="w-full mt-3"
              >
                Complete Profile
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
import { useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import { useApiGet } from '@/composables/useApi';
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';

// Icons
import {
  PlusIcon,
  UsersIcon,
  BriefcaseIcon,
  DocumentTextIcon,
  CalendarIcon,
  EyeIcon,
  ArrowRightIcon,
  MagnifyingGlassIcon,
  BuildingOfficeIcon,
  ChevronRightIcon,
  CheckCircleIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline';

const router = useRouter();
const { user } = useAuth();

// Breadcrumbs
const breadcrumbs = [
  { label: 'Employer', to: '/employer' },
  { label: 'Dashboard' }
];

// State
const isLoadingJobs = ref(false);

// Mock data - in real app, these would come from API
const stats = ref({
  activeJobs: 8,
  newJobsThisWeek: 2,
  totalApplications: 156,
  newApplicationsToday: 12,
  scheduledInterviews: 7,
  interviewsThisWeek: 3,
  companyViews: 892,
  viewsThisWeek: 147
});

const hiringFunnelStages = ref([
  { id: 1, label: 'Applications', count: 156, change: 12 },
  { id: 2, label: 'Screening', count: 89, change: 8 },
  { id: 3, label: 'Interviews', count: 34, change: -3 },
  { id: 4, label: 'Offers', count: 12, change: 15 }
]);

const pipelineStages = ref([
  { id: 1, name: 'New Applications', count: 23, color: 'bg-blue-400' },
  { id: 2, name: 'Under Review', count: 45, color: 'bg-yellow-400' },
  { id: 3, name: 'Interview Scheduled', count: 12, color: 'bg-purple-400' },
  { id: 4, name: 'Final Review', count: 8, color: 'bg-green-400' }
]);

const companyProfileCompletion = ref(85);
const companyProfileTasks = ref([
  { id: 1, title: 'Add company logo', completed: true },
  { id: 2, title: 'Write company description', completed: true },
  { id: 3, title: 'Add team photos', completed: false },
  { id: 4, title: 'Set company benefits', completed: true },
  { id: 5, title: 'Add office locations', completed: false }
]);

// API calls for real data
const { data: recentApplications } = useApiGet('/api/employer/applications/recent', {}, {
  immediate: true,
  defaultValue: []
});

const { data: activeJobs } = useApiGet('/api/employer/jobs/active', {}, {
  immediate: true,
  defaultValue: []
});

const { data: upcomingInterviews } = useApiGet('/api/employer/interviews/upcoming', {}, {
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

const viewApplication = (application: any) => {
  router.push(`/employer/applications/${application.id}`);
};

const scheduleInterview = (application: any) => {
  router.push(`/employer/interviews/schedule?application=${application.id}`);
};

// Load dashboard data
onMounted(async () => {
  try {
    // Load real-time stats
    const response = await fetch('/api/employer/dashboard/stats');
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

/* Pipeline stage indicators */
.pipeline-stage {
  transition: all 0.2s ease-in-out;
}

.pipeline-stage:hover {
  transform: translateX(4px);
}

/* Hiring funnel responsive grid */
@media (max-width: 768px) {
  .funnel-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }
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

/* Gradient backgrounds for stats cards */
.stats-card {
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}

/* Interview card hover effects */
.interview-card:hover {
  border-color: #e5e7eb;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Company profile completion visual enhancement */
.completion-progress {
  background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
  box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
}

/* Action button hover states */
.action-button:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Responsive typography */
@media (max-width: 640px) {
  .dashboard-title {
    font-size: 1.5rem;
  }
  
  .dashboard-subtitle {
    font-size: 1rem;
  }
}
</style> 