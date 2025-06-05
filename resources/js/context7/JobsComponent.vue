<template>
  <div class="context7-jobs-component">
    <!-- Context7 Pattern: Authentication Status -->
    <div v-if="!isAuthenticated" class="auth-section">
      <h2>Login to View Jobs</h2>
      <form @submit.prevent="login" class="login-form">
        <div class="mb-4">
          <label for="email">Email:</label>
          <input
            id="email"
            v-model="credentials.email"
            type="email"
            required
            class="block w-full rounded -md border-gray-300 shadow-sm focus: border border border-gray-300 -gray-300 -indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <div class="mb-4">
          <label for="password">Password:</label>
          <input
            id="password"
            v-model="credentials.password"
            type="password"
            required
            class="block w-full rounded -md border-gray-300 shadow-sm focus: border border border-gray-300 -gray-300 -indigo-500 focus:ring-indigo-500 sm:text-sm"
          />
        </div>
        <button 
          type="submit" 
          :disabled="isLoading" 
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out -primary"
        >
          {{ isLoading ? 'Logging in...' : 'Login' }}
        </button>
        <div v-if="authError" class="error-message">
          {{ authError }}
        </div>
      </form>
    </div>

    <!-- Context7 Pattern: Authenticated Content -->
    <div v-else class="authenticated-section">
      <div class="user-info">
        <h2>Welcome, {{ user?.name }}!</h2>
        <button @click="logout" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out -secondary">Logout</button>
      </div>

      <!-- Context7 Pattern: Jobs List -->
      <div class="jobs-section">
        <h3>Available Jobs</h3>
        <div v-if="isLoadingJobs" class="loading">Loading jobs...</div>
        <div v-else-if="jobsError" class="error-message">{{ jobsError }}</div>
        <div v-else class="jobs-list">
          <div 
            v-for="job in jobs" 
            :key="job.id" 
            class="job- bg-white overflow-hidden shadow rounded -lg"
          >
            <h4>{{ job.title }}</h4>
            <p>{{ job.description?.substring(0, 200) }}...</p>
            <div class="job-meta">
              <span class="salary">
                ${{ job.salary_from?.toLocaleString() }} - ${{ job.salary_to?.toLocaleString() }}
              </span>
              <span class="company">{{ job.company?.name }}</span>
            </div>
            <div class="job-actions">
              <button 
                @click="applyToJob(job.id)" 
                :disabled="isApplying"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out -primary"
              >
                {{ isApplying ? 'Applying...' : 'Apply Now' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Context7JobsComponent',
  
  data() {
    return {
      // Authentication state
      user: null,
      isAuthenticated: false,
      credentials: {
        email: '',
        password: ''
      },
      authError: null,
      isLoading: false,
      
      // Jobs state
      jobs: [],
      isLoadingJobs: false,
      jobsError: null,
      isApplying: false,
    };
  },

  async mounted() {
    // Context7 Pattern: Check existing authentication
    if (window.context7Api?.isAuthenticated()) {
      await this.checkAuthStatus();
    }
  },

  methods: {
    /**
     * Context7 Pattern: Login with error handling
     */
    async login() {
      this.isLoading = true;
      this.authError = null;

      try {
        const result = await window.context7Api.login(
          this.credentials.email,
          this.credentials.password,
          'vue-spa'
        );

        if (result.success) {
          this.user = result.user;
          this.isAuthenticated = true;
          this.credentials = { email: '', password: '' };
          await this.loadJobs();
        } else {
          this.authError = result.error;
        }
      } catch (error) {
        this.authError = 'Login failed. Please try again.';
        console.error('Login error:', error);
      } finally {
        this.isLoading = false;
      }
    },

    /**
     * Context7 Pattern: Check authentication status
     */
    async checkAuthStatus() {
      try {
        const result = await window.context7Api.getUser();
        if (result.success) {
          this.user = result.user;
          this.isAuthenticated = true;
          await this.loadJobs();
        } else {
          this.isAuthenticated = false;
        }
      } catch (error) {
        this.isAuthenticated = false;
        console.error('Auth check error:', error);
      }
    },

    /**
     * Context7 Pattern: Logout with cleanup
     */
    async logout() {
      try {
        await window.context7Api.logout();
        this.user = null;
        this.isAuthenticated = false;
        this.jobs = [];
      } catch (error) {
        console.error('Logout error:', error);
        // Still clean up local state
        this.user = null;
        this.isAuthenticated = false;
        this.jobs = [];
      }
    },

    /**
     * Context7 Pattern: Load jobs with error handling
     */
    async loadJobs() {
      this.isLoadingJobs = true;
      this.jobsError = null;

      try {
        const result = await window.context7Api.getJobs();
        if (result.success) {
          this.jobs = result.jobs.data || result.jobs;
        } else {
          this.jobsError = result.error;
        }
      } catch (error) {
        this.jobsError = 'Failed to load jobs. Please try again.';
        console.error('Jobs loading error:', error);
      } finally {
        this.isLoadingJobs = false;
      }
    },

    /**
     * Context7 Pattern: Apply to job with validation
     */
    async applyToJob(jobId) {
      if (!this.isAuthenticated) {
        alert('Please login to apply for jobs');
        return;
      }

      this.isApplying = true;

      try {
        const result = await window.context7Api.applyToJob(jobId, {
          notes: 'Applied via Context7 Vue component',
          expected_salary: 75000
        });

        if (result.success) {
          alert('Application submitted successfully!');
        } else {
          alert(result.error || 'Application failed');
        }
      } catch (error) {
        alert('Application failed. Please try again.');
        console.error('Application error:', error);
      } finally {
        this.isApplying = false;
      }
    }
  }
};
</script>

<style scoped>
.context7-jobs-component {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.login-form {
  max-width: 400px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 1rem;
}

.form-control {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  color: #dc3545;
  margin-top: 0.5rem;
}

.user-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 4px;
}

.jobs-list {
  display: grid;
  gap: 1rem;
}

.job-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1.5rem;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.job-meta {
  display: flex;
  justify-content: space-between;
  margin: 1rem 0;
  color: #666;
}

.job-actions {
  text-align: right;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}
</style>