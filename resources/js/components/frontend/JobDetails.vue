<template>
  <div class="job-details">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center min-h-screen">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <span class="ml-3 text-lg text-gray-600">{{ $t('common.loading') }}</span>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="min-h-screen flex items-center justify-center">
      <div class="text-center">
        <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">{{ $t('jobs.error_loading') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ error }}</p>
        <button
          @click="fetchJob"
          class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          {{ $t('common.try_again') }}
        </button>
      </div>
    </div>

    <!-- Job Content -->
    <div v-else-if="job" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <router-link
              to="/"
              class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
              </svg>
              {{ $t('common.home') }}
            </router-link>
          </li>
          <li>
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
              <router-link
                to="/jobs"
                class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2"
              >
                {{ $t('common.jobs') }}
              </router-link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
              </svg>
              <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ job.title }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
          <!-- Job Header -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ job.title }}</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-600 mb-4">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <router-link
                      :to="`/companies/${job.company.slug}`"
                      class="hover:text-indigo-600 font-medium"
                    >
                      {{ job.company.name }}
                    </router-link>
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>{{ job.location.full_location }}</span>
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ formatDate(job.created_at) }}</span>
                  </div>
                </div>

                <!-- Job Tags -->
                <div class="flex flex-wrap gap-2 mb-4">
                  <span
                    v-if="job.job_type"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ job.job_type.name }}
                  </span>
                  <span
                    v-if="job.career_level"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                  >
                    {{ job.career_level.name }}
                  </span>
                  <span
                    v-if="job.is_remote"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                  >
                    {{ $t('jobs.remote') }}
                  </span>
                  <span
                    v-if="job.is_featured"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                  >
                    {{ $t('jobs.featured') }}
                  </span>
                  <span
                    v-if="job.is_urgent"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"
                  >
                    {{ $t('jobs.urgent') }}
                  </span>
                </div>

                <!-- Salary Range -->
                <div v-if="job.salary_range" class="mb-4">
                  <div class="flex items-center text-lg font-semibold text-gray-900">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                    {{ job.salary_range }}
                    <span v-if="job.salary_period" class="text-sm text-gray-600 ml-1">
                      / {{ job.salary_period.period }}
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center space-x-3 ml-6">
                <button
                  @click="toggleBookmark"
                  :class="[
                    'p-2 rounded-full border transition-colors',
                    isBookmarked
                      ? 'bg-red-50 border-red-200 text-red-600 hover:bg-red-100'
                      : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'
                  ]"
                  :title="isBookmarked ? $t('jobs.remove_bookmark') : $t('jobs.bookmark')"
                >
                  <svg class="w-5 h-5" :fill="isBookmarked ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                  </svg>
                </button>
                <button
                  @click="shareJob"
                  class="p-2 rounded-full border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 transition-colors"
                  :title="$t('jobs.share')"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Job Description -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $t('jobs.description') }}</h2>
            <div class="prose max-w-none" v-html="job.description"></div>
          </div>

          <!-- Job Requirements -->
          <div v-if="job.requirements" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $t('jobs.requirements') }}</h2>
            <div class="prose max-w-none" v-html="job.requirements"></div>
          </div>

          <!-- Skills Required -->
          <div v-if="job.skills && job.skills.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $t('jobs.skills_required') }}</h2>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="skill in job.skills"
                :key="skill.id"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
              >
                {{ skill.name }}
              </span>
            </div>
          </div>

          <!-- Benefits -->
          <div v-if="job.benefits" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $t('jobs.benefits') }}</h2>
            <div class="prose max-w-none" v-html="job.benefits"></div>
          </div>

          <!-- Similar Jobs -->
          <div v-if="similarJobs.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $t('jobs.similar_jobs') }}</h2>
            <div class="space-y-4">
              <div
                v-for="similarJob in similarJobs"
                :key="similarJob.id"
                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900 mb-1">
                      <router-link
                        :to="`/jobs/${similarJob.slug}`"
                        class="hover:text-indigo-600"
                      >
                        {{ similarJob.title }}
                      </router-link>
                    </h3>
                    <p class="text-sm text-gray-600 mb-2">{{ similarJob.company.name }}</p>
                    <div class="flex items-center space-x-4 text-xs text-gray-500">
                      <span>{{ similarJob.location.full_location }}</span>
                      <span>{{ formatDate(similarJob.created_at) }}</span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <router-link
                      :to="`/jobs/${similarJob.slug}`"
                      class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                      {{ $t('common.view') }}
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
          <!-- Application Card -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 sticky top-6">
            <div v-if="!user" class="text-center">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('jobs.apply_for_job') }}</h3>
              <p class="text-sm text-gray-600 mb-4">{{ $t('jobs.login_to_apply') }}</p>
              <div class="space-y-3">
                <router-link
                  to="/login"
                  class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  {{ $t('auth.login') }}
                </router-link>
                <router-link
                  to="/register"
                  class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  {{ $t('auth.register') }}
                </router-link>
              </div>
            </div>

            <div v-else-if="hasApplied" class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $t('jobs.application_submitted') }}</h3>
              <p class="text-sm text-gray-600 mb-4">{{ $t('jobs.application_submitted_description') }}</p>
              <router-link
                to="/dashboard/applications"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-600 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                {{ $t('jobs.view_applications') }}
              </router-link>
            </div>

            <div v-else-if="job.is_active && !isExpired" class="text-center">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('jobs.apply_for_job') }}</h3>
              <button
                @click="showApplicationModal = true"
                :disabled="applying"
                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="applying" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ applying ? $t('jobs.applying') : $t('jobs.apply_now') }}
              </button>
              <p class="text-xs text-gray-500 mt-2">{{ $t('jobs.quick_apply_description') }}</p>
            </div>

            <div v-else class="text-center">
              <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $t('jobs.application_closed') }}</h3>
              <p class="text-sm text-gray-600">
                {{ isExpired ? $t('jobs.application_expired') : $t('jobs.application_inactive') }}
              </p>
            </div>
          </div>

          <!-- Job Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('jobs.job_information') }}</h3>
            <dl class="space-y-3">
              <div v-if="job.deadline">
                <dt class="text-sm font-medium text-gray-500">{{ $t('jobs.deadline') }}</dt>
                <dd class="text-sm text-gray-900">{{ formatDate(job.deadline) }}</dd>
              </div>
              <div v-if="job.experience_required">
                <dt class="text-sm font-medium text-gray-500">{{ $t('jobs.experience_required') }}</dt>
                <dd class="text-sm text-gray-900">{{ job.experience_required }} {{ $t('jobs.years') }}</dd>
              </div>
              <div v-if="job.education_level">
                <dt class="text-sm font-medium text-gray-500">{{ $t('jobs.education_level') }}</dt>
                <dd class="text-sm text-gray-900">{{ job.education_level.name }}</dd>
              </div>
              <div v-if="job.category">
                <dt class="text-sm font-medium text-gray-500">{{ $t('jobs.category') }}</dt>
                <dd class="text-sm text-gray-900">{{ job.category.name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">{{ $t('jobs.applications') }}</dt>
                <dd class="text-sm text-gray-900">{{ job.applications_count || 0 }} {{ $t('jobs.applicants') }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500">{{ $t('jobs.views') }}</dt>
                <dd class="text-sm text-gray-900">{{ job.views_count || 0 }} {{ $t('jobs.views') }}</dd>
              </div>
            </dl>
          </div>

          <!-- Company Information -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('jobs.about_company') }}</h3>
            <div class="flex items-center mb-4">
              <img
                :src="job.company.logo || '/images/default-company-logo.png'"
                :alt="job.company.name"
                class="w-12 h-12 rounded-lg object-cover"
              />
              <div class="ml-3">
                <h4 class="text-sm font-medium text-gray-900">{{ job.company.name }}</h4>
                <p class="text-sm text-gray-500">{{ job.company.industry?.name }}</p>
              </div>
            </div>
            <p v-if="job.company.description" class="text-sm text-gray-600 mb-4">
              {{ truncateText(job.company.description, 150) }}
            </p>
            <div class="space-y-2 text-sm">
              <div v-if="job.company.company_size" class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-gray-600">{{ job.company.company_size.size }}</span>
              </div>
              <div v-if="job.company.established_in" class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0V7a1 1 0 00-1 1v9a2 2 0 002 2h4a2 2 0 002-2V8a1 1 0 00-1-1V7" />
                </svg>
                <span class="text-gray-600">{{ $t('jobs.established') }} {{ job.company.established_in }}</span>
              </div>
              <div class="flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6" />
                </svg>
                <span class="text-gray-600">{{ job.company.statistics?.jobs_count || 0 }} {{ $t('jobs.open_positions') }}</span>
              </div>
            </div>
            <div class="mt-4">
              <router-link
                :to="`/companies/${job.company.slug}`"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                {{ $t('jobs.view_company') }}
                <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Application Modal -->
    <div
      v-if="showApplicationModal"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
      @click="closeApplicationModal"
    >
      <div
        class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white"
        @click.stop
      >
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">{{ $t('jobs.apply_for_position') }}</h3>
            <button
              @click="closeApplicationModal"
              class="text-gray-400 hover:text-gray-600"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <form @submit.prevent="submitApplication" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $t('jobs.select_resume') }} *
              </label>
              <select
                v-model="applicationForm.resume_id"
                required
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              >
                <option value="">{{ $t('jobs.choose_resume') }}</option>
                <option
                  v-for="resume in userResumes"
                  :key="resume.id"
                  :value="resume.id"
                >
                  {{ resume.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $t('jobs.cover_letter') }}
              </label>
              <textarea
                v-model="applicationForm.cover_letter"
                rows="4"
                :placeholder="$t('jobs.cover_letter_placeholder')"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              ></textarea>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                {{ $t('jobs.expected_salary') }}
              </label>
              <input
                v-model="applicationForm.expected_salary"
                type="number"
                :placeholder="$t('jobs.expected_salary_placeholder')"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
            </div>

            <div class="flex items-center">
              <input
                v-model="applicationForm.consent_data_processing"
                type="checkbox"
                required
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label class="ml-2 block text-sm text-gray-900">
                {{ $t('jobs.consent_data_processing') }} *
              </label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeApplicationModal"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                {{ $t('common.cancel') }}
              </button>
              <button
                type="submit"
                :disabled="applying"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg v-if="applying" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ applying ? $t('jobs.submitting') : $t('jobs.submit_application') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'

// Props
interface Props {
  jobSlug?: string
}

const props = withDefaults(defineProps<Props>(), {
  jobSlug: ''
})

// Composables
const route = useRoute()
const router = useRouter()
const { t } = useI18n()
const authStore = useAuthStore()

// Reactive state
const loading = ref(false)
const applying = ref(false)
const error = ref('')
const job = ref(null)
const similarJobs = ref([])
const userResumes = ref([])
const isBookmarked = ref(false)
const hasApplied = ref(false)
const showApplicationModal = ref(false)

// Application form
const applicationForm = reactive({
  resume_id: '',
  cover_letter: '',
  expected_salary: '',
  consent_data_processing: false
})

// Computed properties
const user = computed(() => authStore.user)
const jobSlug = computed(() => props.jobSlug || route.params.slug as string)
const isExpired = computed(() => {
  if (!job.value?.deadline) return false
  return new Date(job.value.deadline) < new Date()
})

// Methods
const fetchJob = async () => {
  loading.value = true
  error.value = ''
  
  try {
    const response = await fetch(`/api/jobs/${jobSlug.value}`)
    const data = await response.json()
    
    if (response.ok) {
      job.value = data.data
      
      // Track job view
      if (user.value) {
        trackJobView()
      }
      
      // Fetch related data
      await Promise.all([
        fetchSimilarJobs(),
        checkApplicationStatus(),
        checkBookmarkStatus()
      ])
      
      if (user.value && user.value.role === 'candidate') {
        await fetchUserResumes()
      }
    } else {
      error.value = data.message || t('jobs.error_loading')
    }
  } catch (err) {
    error.value = t('jobs.error_loading')
    console.error('Error fetching job:', err)
  } finally {
    loading.value = false
  }
}

const fetchSimilarJobs = async () => {
  try {
    const response = await fetch(`/api/jobs/${job.value.id}/similar`)
    const data = await response.json()
    
    if (response.ok) {
      similarJobs.value = data.data || []
    }
  } catch (err) {
    console.error('Error fetching similar jobs:', err)
  }
}

const fetchUserResumes = async () => {
  try {
    const response = await fetch('/api/user/resumes')
    const data = await response.json()
    
    if (response.ok) {
      userResumes.value = data.data || []
    }
  } catch (err) {
    console.error('Error fetching user resumes:', err)
  }
}

const checkApplicationStatus = async () => {
  if (!user.value || user.value.role !== 'candidate') return
  
  try {
    const response = await fetch(`/api/jobs/${job.value.id}/application-status`)
    const data = await response.json()
    
    if (response.ok) {
      hasApplied.value = data.has_applied
    }
  } catch (err) {
    console.error('Error checking application status:', err)
  }
}

const checkBookmarkStatus = async () => {
  if (!user.value) return
  
  try {
    const response = await fetch(`/api/jobs/${job.value.id}/bookmark-status`)
    const data = await response.json()
    
    if (response.ok) {
      isBookmarked.value = data.is_bookmarked
    }
  } catch (err) {
    console.error('Error checking bookmark status:', err)
  }
}

const trackJobView = async () => {
  try {
    await fetch(`/api/jobs/${job.value.id}/view`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })
  } catch (err) {
    console.error('Error tracking job view:', err)
  }
}

const toggleBookmark = async () => {
  if (!user.value) {
    router.push('/login')
    return
  }
  
  try {
    const response = await fetch(`/api/jobs/${job.value.id}/bookmark`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      }
    })
    
    const data = await response.json()
    
    if (response.ok) {
      isBookmarked.value = data.is_bookmarked
    }
  } catch (err) {
    console.error('Error toggling bookmark:', err)
  }
}

const shareJob = () => {
  if (navigator.share) {
    navigator.share({
      title: job.value.title,
      text: `${job.value.title} at ${job.value.company.name}`,
      url: window.location.href
    })
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href)
    // Show toast notification
    console.log('Job URL copied to clipboard')
  }
}

const submitApplication = async () => {
  applying.value = true
  
  try {
    const response = await fetch('/api/job-applications', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        job_id: job.value.id,
        ...applicationForm
      })
    })
    
    const data = await response.json()
    
    if (response.ok) {
      hasApplied.value = true
      showApplicationModal.value = false
      
      // Reset form
      Object.keys(applicationForm).forEach(key => {
        if (typeof applicationForm[key] === 'boolean') {
          applicationForm[key] = false
        } else {
          applicationForm[key] = ''
        }
      })
      
      // Show success message
      console.log('Application submitted successfully')
    } else {
      console.error('Application failed:', data.message)
    }
  } catch (err) {
    console.error('Error submitting application:', err)
  } finally {
    applying.value = false
  }
}

const closeApplicationModal = () => {
  showApplicationModal.value = false
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString()
}

const truncateText = (text: string, length: number) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

// Lifecycle hooks
onMounted(() => {
  fetchJob()
})

// Watchers
watch(() => route.params.slug, (newSlug) => {
  if (newSlug && newSlug !== jobSlug.value) {
    fetchJob()
  }
})
</script>

<style scoped>
.job-details {
  @apply min-h-screen bg-gray-50;
}

.prose {
  @apply text-gray-700;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
  @apply text-gray-900 font-semibold;
}

.prose ul,
.prose ol {
  @apply pl-6;
}

.prose li {
  @apply mb-1;
}

.prose p {
  @apply mb-4;
}

.prose a {
  @apply text-indigo-600 hover:text-indigo-500;
}
</style> 