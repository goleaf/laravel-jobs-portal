<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">{{ $t('job_search.title') }}</h1>
    <form @submit.prevent="searchJobs" class="mb-4">
      <div class="flex flex-col md:flex-row gap-4">
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="$t('job_search.placeholder')"
          class="border rounded p-2 flex-1"
        />
        <button
          type="submit"
          class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600"
        >
          {{ $t('job_search.search') }}
        </button>
      </div>
    </form>
    <div v-if="loading" class="text-center p-4">{{ $t('job_search.loading') }}</div>
    <div v-else-if="error" class="text-red-500 text-center p-4">{{ error }}</div>
    <div v-else-if="jobs.length === 0" class="text-center p-4">{{ $t('job_search.no_results') }}</div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div
        v-for="job in jobs"
        :key="job.id"
        class="border rounded p-4 shadow hover:shadow-md transition-shadow"
      >
        <h2 class="text-xl font-semibold mb-2">{{ job.job_title }}</h2>
        <p class="text-gray-600 mb-2">{{ job.company.name }}</p>
        <p class="text-sm text-gray-500 mb-2">{{ job.full_location }}</p>
        <p class="text-sm mb-2">{{ $t('job_search.salary') }}: {{ job.salary_from }} - {{ job.salary_to }} {{ job.currency.name }}</p>
        <router-link
          :to="{ name: 'job.details', params: { id: job.id } }"
          class="text-blue-500 hover:underline"
        >
          {{ $t('job_search.view_details') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import axios from 'axios';

export default defineComponent({
  name: 'JobSearch',
  setup() {
    const searchQuery = ref<string>('');
    const jobs = ref<any[]>([]);
    const loading = ref<boolean>(false);
    const error = ref<string | null>(null);

    const searchJobs = async () => {
      if (!searchQuery.value.trim()) {
        error.value = 'Please enter a search term';
        return;
      }

      loading.value = true;
      error.value = null;

      try {
        const response = await axios.get('/api/jobs', {
          params: { search: searchQuery.value }
        });
        jobs.value = response.data.data;
      } catch (err) {
        error.value = 'An error occurred while searching for jobs. Please try again.';
        console.error(err);
      } finally {
        loading.value = false;
      }
    };

    return {
      searchQuery,
      jobs,
      loading,
      error,
      searchJobs
    };
  }
});
</script>

<style scoped>
/* Add any additional scoped styles if needed */
</style> 