/**
 * Context7 API Client
 * Modern JavaScript client for Context7 Sanctum API
 */

class Context7ApiClient {
    constructor(baseURL = '/api', csrfEndpoint = '/sanctum/csrf-cookie') {
        this.baseURL = baseURL;
        this.csrfEndpoint = csrfEndpoint;
        this.token = localStorage.getItem('context7_token');
        
        // Configure axios defaults
        if (window.axios) {
            axios.defaults.withCredentials = true;
            axios.defaults.withXSRFToken = true;
            axios.defaults.headers.common['Accept'] = 'application/json';
            axios.defaults.headers.common['Content-Type'] = 'application/json';
            
            if (this.token) {
                this.setAuthToken(this.token);
            }
        }
    }

    /**
     * Context7 Pattern: Initialize CSRF protection
     */
    async initializeCSRF() {
        try {
            await axios.get(this.csrfEndpoint);
        } catch (error) {
            console.warn('CSRF initialization failed:', error);
        }
    }

    /**
     * Context7 Pattern: Set authentication token
     */
    setAuthToken(token) {
        this.token = token;
        localStorage.setItem('context7_token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    /**
     * Context7 Pattern: Remove authentication token
     */
    removeAuthToken() {
        this.token = null;
        localStorage.removeItem('context7_token');
        delete axios.defaults.headers.common['Authorization'];
    }

    /**
     * Context7 Pattern: Login with credentials
     */
    async login(email, password, deviceName = 'web-browser') {
        try {
            await this.initializeCSRF();
            
            const response = await axios.post(`${this.baseURL}/auth/login`, {
                email,
                password,
                device_name: deviceName
            });

            const { token, user } = response.data;
            this.setAuthToken(token);
            
            return { success: true, user, token };
        } catch (error) {
            return { 
                success: false, 
                error: error.response?.data?.message || 'Login failed',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    /**
     * Context7 Pattern: Get authenticated user
     */
    async getUser() {
        try {
            const response = await axios.get(`${this.baseURL}/auth/user`);
            return { success: true, user: response.data.user };
        } catch (error) {
            return { 
                success: false, 
                error: error.response?.data?.message || 'Failed to fetch user'
            };
        }
    }

    /**
     * Context7 Pattern: Logout
     */
    async logout() {
        try {
            await axios.post(`${this.baseURL}/auth/logout`);
            this.removeAuthToken();
            return { success: true };
        } catch (error) {
            this.removeAuthToken(); // Remove token even if request fails
            return { 
                success: false, 
                error: error.response?.data?.message || 'Logout failed'
            };
        }
    }

    /**
     * Context7 Pattern: Get jobs with authentication
     */
    async getJobs(params = {}) {
        try {
            const response = await axios.get(`${this.baseURL}/v1/job`, { params });
            return { success: true, jobs: response.data };
        } catch (error) {
            return { 
                success: false, 
                error: error.response?.data?.message || 'Failed to fetch jobs'
            };
        }
    }

    /**
     * Context7 Pattern: Create job application
     */
    async applyToJob(jobId, applicationData) {
        try {
            const response = await axios.post(`${this.baseURL}/v1/jobapplication`, {
                job_id: jobId,
                ...applicationData
            });
            return { success: true, application: response.data };
        } catch (error) {
            return { 
                success: false, 
                error: error.response?.data?.message || 'Application failed',
                errors: error.response?.data?.errors || {}
            };
        }
    }

    /**
     * Context7 Pattern: Check authentication status
     */
    isAuthenticated() {
        return !!this.token;
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Context7ApiClient;
}

// Global instance
window.context7Api = new Context7ApiClient();