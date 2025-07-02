import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { createPinia } from 'pinia';
import './bootstrap';
import '../css/app.css';

// Import stores
import { useAuthStore } from '@/stores/auth';
import { useAuth } from '@/composables/useAuth';
import { usePerformance } from '@/composables/usePerformance';

// Import layouts
import MainLayout from '@/layouts/MainLayout.vue';
import BaseButton from '@/components/base/BaseButton.vue';
import BaseInput from '@/components/base/BaseInput.vue';

// ===== DYNAMIC IMPORTS FOR CODE SPLITTING =====

// Public pages - Lazy loaded for better performance
const Home = () => import(/* webpackChunkName: "home" */ '@/pages/Home.vue');
const Login = () => import(/* webpackChunkName: "auth" */ '@/pages/auth/Login.vue');
const Register = () => import(/* webpackChunkName: "auth" */ '@/pages/auth/Register.vue');

// Candidate pages - Lazy loaded with route-based splitting
const CandidateDashboard = () => import(/* webpackChunkName: "candidate" */ '@/pages/candidate/Dashboard.vue');
const CandidateApplications = () => import(/* webpackChunkName: "candidate" */ '@/pages/candidate/Applications.vue');

// Employer pages - Lazy loaded with employer chunking
const EmployerDashboard = () => import(/* webpackChunkName: "employer" */ '@/pages/employer/Dashboard.vue');
const EmployerApplications = () => import(/* webpackChunkName: "employer" */ '@/pages/employer/Applications.vue');
const EmployerJobCreate = () => import(/* webpackChunkName: "employer" */ '@/pages/employer/JobCreate.vue');

// Admin pages - Lazy loaded with admin-specific chunking
const AdminDashboard = () => import(/* webpackChunkName: "admin" */ '@/pages/admin/Dashboard.vue');
const AdminUsers = () => import(/* webpackChunkName: "admin" */ '@/pages/admin/Users.vue');

// Additional pages - Lazy loaded with grouped chunking
const JobDetails = () => import(/* webpackChunkName: "browse" */ '@/pages/JobDetails.vue');
const Jobs = () => import(/* webpackChunkName: "browse" */ '@/pages/Jobs.vue');
const Companies = () => import(/* webpackChunkName: "browse" */ '@/pages/Companies.vue');
const CompanyDetails = () => import(/* webpackChunkName: "browse" */ '@/pages/CompanyDetails.vue');

// Error pages - Static import for immediate availability
import NotFound from '@/pages/NotFound.vue';

// Configure router
const routes = [
  // Public routes
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: { 
      title: 'Job Portal | Find Your Dream Job',
      preload: ['auth', 'browse'] // Preload chunks likely to be needed next
    }
  },
  {
    path: '/jobs',
    name: 'jobs',
    component: Jobs,
    meta: { 
      title: 'Browse Jobs | Job Portal',
      requiresAuth: false 
    }
  },
  {
    path: '/jobs/:id',
    name: 'job.details',
    component: JobDetails,
    meta: { 
      title: 'Job Details | Job Portal',
      requiresAuth: false 
    }
  },
  {
    path: '/companies',
    name: 'companies',
    component: Companies,
    meta: { 
      title: 'Browse Companies | Job Portal',
      requiresAuth: false 
    }
  },
  {
    path: '/companies/:id',
    name: 'company.details',
    component: CompanyDetails,
    meta: { 
      title: 'Company Details | Job Portal',
      requiresAuth: false 
    }
  },

  // Candidate routes
  {
    path: '/candidate',
    meta: { requiresAuth: true, role: 'candidate' },
    children: [
      {
        path: '',
        redirect: '/candidate/dashboard'
      },
      {
        path: 'dashboard',
        name: 'candidate.dashboard',
        component: CandidateDashboard,
        meta: { 
          title: 'Candidate Dashboard | Job Portal',
          requiresAuth: true,
          role: 'candidate'
        }
      },
      {
        path: 'applications',
        name: 'candidate.applications',
        component: CandidateApplications,
        meta: { 
          title: 'My Applications | Job Portal',
          requiresAuth: true,
          role: 'candidate'
        }
      }
    ]
  },

  // Employer routes
  {
    path: '/employer',
    meta: { requiresAuth: true, role: 'employer' },
    children: [
      {
        path: '',
        redirect: '/employer/dashboard'
      },
      {
        path: 'dashboard',
        name: 'employer.dashboard',
        component: EmployerDashboard,
        meta: { 
          title: 'Employer Dashboard | Job Portal',
          requiresAuth: true,
          role: 'employer'
        }
      },
      {
        path: 'applications',
        name: 'employer.applications',
        component: EmployerApplications,
        meta: { 
          title: 'Manage Applications | Job Portal',
          requiresAuth: true,
          role: 'employer'
        }
      },
      {
        path: 'jobs/create',
        name: 'employer.jobs.create',
        component: EmployerJobCreate,
        meta: { 
          title: 'Post New Job | Job Portal',
          requiresAuth: true,
          role: 'employer'
        }
      },
      {
        path: 'jobs/:id/edit',
        name: 'employer.jobs.edit',
        component: EmployerJobCreate,
        meta: { 
          title: 'Edit Job | Job Portal',
          requiresAuth: true,
          role: 'employer'
        }
      }
    ]
  },

  // Admin routes (placeholder for future implementation)
  {
    path: '/admin',
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard'
      },
      {
        path: 'dashboard',
        name: 'admin.dashboard',
        component: AdminDashboard,
        meta: { 
          title: 'Admin Dashboard | Job Portal',
          requiresAuth: true,
          role: 'admin'
        }
      },
      {
        path: 'users',
        name: 'admin.users',
        component: AdminUsers,
        meta: { 
          title: 'Admin Users | Job Portal',
          requiresAuth: true,
          role: 'admin'
        }
      }
      // Add more admin routes as they are implemented
    ]
  },

  // Authentication routes (grouped in auth chunk)
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { 
      title: 'Login | Job Portal',
      preload: ['candidate', 'employer'] // Preload role-specific chunks
    }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { 
      title: 'Register | Job Portal',
      preload: ['candidate', 'employer'] // Preload role-specific chunks
    }
  },

  // Catch-all 404 route
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFound,
    meta: { 
      title: 'Page Not Found | Job Portal',
      requiresAuth: false 
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Return saved position for back/forward navigation
    if (savedPosition) {
      return savedPosition;
    }
    
    // Scroll to anchor if present
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      };
    }
    
    // Scroll to top for new routes
    return { top: 0, behavior: 'smooth' };
  }
});

// Global performance monitoring instance
let performanceTracker = null;
let routeChangeTracker = null;

// Navigation guards with performance optimizations
router.beforeEach(async (to, from, next) => {
  // Start route change performance tracking
  if (performanceTracker) {
    routeChangeTracker = performanceTracker.trackRouteChange(from.path, to.path);
  }
  
  // Update document title
  if (to.meta.title) {
    document.title = to.meta.title;
  }
  
  // Preload chunks for likely next routes
  if (to.meta.preload && Array.isArray(to.meta.preload)) {
    preloadChunks(to.meta.preload);
  }
  
  // Authentication check
  if (to.meta.requiresAuth) {
    const authStore = useAuthStore();
    const { checkAuth } = useAuth();
    
    // Check if user is authenticated
    if (!authStore.isAuthenticated) {
      await checkAuth();
    }
    
    // Redirect to login if not authenticated
    if (!authStore.isAuthenticated) {
      next({ 
        name: 'login', 
        query: { redirect: to.fullPath }
      });
      return;
    }
    
    // Role-based access control
    if (to.meta.role && authStore.user?.role !== to.meta.role) {
      // Redirect to appropriate dashboard based on user role
      const roleDashboards = {
        candidate: 'candidate.dashboard',
        employer: 'employer.dashboard', 
        admin: 'admin.dashboard'
      };
      
      const redirectRoute = roleDashboards[authStore.user?.role] || 'home';
      next({ name: redirectRoute });
      return;
    }
  }
  
  // Redirect authenticated users away from auth pages
  if (['login', 'register'].includes(to.name) && useAuthStore().isAuthenticated) {
    const user = useAuthStore().user;
    const roleDashboards = {
      candidate: 'candidate.dashboard',
      employer: 'employer.dashboard',
      admin: 'admin.dashboard'
    };
    
    const redirectRoute = roleDashboards[user?.role] || 'home';
    next({ name: redirectRoute });
    return;
  }
  
  next();
});

// Chunk preloading function for better performance
function preloadChunks(chunkNames) {
  const chunkMap = {
    auth: [Login, Register],
    browse: [Jobs, JobDetails, Companies, CompanyDetails],
    candidate: [CandidateDashboard, CandidateApplications],
    employer: [EmployerDashboard, EmployerApplications, EmployerJobCreate],
    admin: [AdminDashboard, AdminUsers]
  };
  
  chunkNames.forEach(chunkName => {
    const components = chunkMap[chunkName];
    if (components) {
      components.forEach(component => {
        // Trigger dynamic import to preload the chunk
        if (typeof component === 'function') {
          component().catch(() => {
            // Silently fail if preload fails
            console.warn(`Failed to preload chunk: ${chunkName}`);
          });
        }
      });
    }
  });
}

// Performance tracking for route changes
router.beforeResolve((to, from, next) => {
  // Track performance metrics
  if (typeof performance !== 'undefined' && performance.mark) {
    performance.mark(`route-start-${to.name}`);
  }
  next();
});

router.afterEach((to, from) => {
  // Complete route change performance tracking
  if (routeChangeTracker) {
    routeChangeTracker.end();
    routeChangeTracker = null;
  }
  
  // Complete traditional performance tracking
  if (typeof performance !== 'undefined' && performance.mark && performance.measure) {
    performance.mark(`route-end-${to.name}`);
    try {
      performance.measure(
        `route-${to.name}`,
        `route-start-${to.name}`,
        `route-end-${to.name}`
      );
    } catch (error) {
      console.warn('Performance measurement failed:', error);
    }
  }
  
  // Scroll to top on route change (unless hash navigation)
  if (!to.hash) {
    window.scrollTo(0, 0);
  }
});

// Create Pinia store
const pinia = createPinia();

// Create Vue app
const app = createApp({
  template: '<router-view />',
  mounted() {
    console.log('Vue3 Job Portal SPA initialized successfully');
    
    // Initialize global performance monitoring
    const { trackRouteChange, getPerformanceScore, exportMetrics } = usePerformance();
    performanceTracker = { trackRouteChange, getPerformanceScore, exportMetrics };
    
    // Register service worker for offline support and caching
    if ('serviceWorker' in navigator && import.meta.env.PROD) {
      this.registerServiceWorker();
    }
    
    // Performance tracking
    if (typeof performance !== 'undefined' && performance.mark) {
      performance.mark('app-mounted');
    }
    
    // Log initial performance metrics after 3 seconds
    setTimeout(() => {
      if (performanceTracker) {
        const metrics = performanceTracker.exportMetrics();
        const score = performanceTracker.getPerformanceScore();
        console.log('[Performance] Initial Score:', score.grade, `(${score.score}/100)`);
        console.log('[Performance] Core Web Vitals:', {
          FCP: metrics.metrics.fcp ? `${metrics.metrics.fcp}ms` : 'N/A',
          LCP: metrics.metrics.lcp ? `${metrics.metrics.lcp}ms` : 'N/A',
          FID: metrics.metrics.fid ? `${metrics.metrics.fid}ms` : 'N/A',
          CLS: metrics.metrics.cls !== null ? metrics.metrics.cls : 'N/A',
        });
      }
    }, 3000);
  },
  
  methods: {
    async registerServiceWorker() {
      try {
        console.log('[SW] Registering service worker...');
        
        const registration = await navigator.serviceWorker.register('/sw.js', {
          scope: '/'
        });
        
        console.log('[SW] Service worker registered successfully:', registration);
        
        // Listen for updates
        registration.addEventListener('updatefound', () => {
          const newWorker = registration.installing;
          
          newWorker.addEventListener('statechange', () => {
            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
              console.log('[SW] New service worker available');
              
              // Show update notification to user
              this.showUpdateNotification();
            }
          });
        });
        
        // Check for updates periodically
        setInterval(() => {
          registration.update();
        }, 300000); // Check every 5 minutes
        
      } catch (error) {
        console.warn('[SW] Service worker registration failed:', error);
      }
    },
    
    showUpdateNotification() {
      // Simple update notification (can be enhanced with a proper notification component)
      if (confirm('A new version of the app is available. Would you like to reload to get the latest features?')) {
        window.location.reload();
      }
    }
  }
});

// Configure global properties
app.config.globalProperties.$route = router.currentRoute;
app.config.globalProperties.$router = router;

// Error handling
app.config.errorHandler = (err, vm, info) => {
  console.error('Vue error:', err, info);
  
  // In production, you might want to send this to an error reporting service
  if (import.meta.env.PROD) {
    // Send to error reporting service (e.g., Sentry, Bugsnag)
  }
};

// Performance tracking
app.config.performance = import.meta.env.DEV;

// Install plugins
app.use(pinia);
app.use(router);

// Global components
app.component('MainLayout', MainLayout);
app.component('BaseButton', BaseButton);
app.component('BaseInput', BaseInput);

// Wait for router to be ready and mount app
router.isReady().then(() => {
  app.mount('#app');
  
  // Add loading complete class for any CSS transitions
  document.body.classList.add('vue-app-loaded');
});

// Export for testing or external access
export { app, router, pinia }; 