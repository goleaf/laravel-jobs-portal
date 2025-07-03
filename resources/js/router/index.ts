import { createRouter, createWebHistory } from "vue-router"
import type { RouteRecordRaw, NavigationGuardNext, RouteLocationNormalized } from "vue-router"
import { useAuthStore } from "../stores/auth"
import type { UserRole } from "@/types/user"

// Lazy load components for better performance
const Home = () => import("../pages/Home.vue")
const Jobs = () => import("../pages/Jobs.vue")
const JobDetails = () => import("../pages/JobDetails.vue")
const Companies = () => import("../pages/Companies.vue")
const CompanyDetails = () => import("../pages/CompanyDetails.vue")
const About = () => import("../pages/About.vue")
const Contact = () => import("../pages/Contact.vue")
const Terms = () => import("../pages/Terms.vue")
const Privacy = () => import("../pages/Privacy.vue")
const Professions = () => import("../pages/Professions.vue")
const Login = () => import("../pages/auth/Login.vue")
const Register = () => import("../pages/auth/Register.vue")
const Dashboard = () => import("../pages/Dashboard.vue")
const Profile = () => import("../pages/Profile.vue")
const NotFound = () => import("../pages/NotFound.vue")

// Admin pages
const AdminDashboard = () => import("../pages/admin/Dashboard.vue")
const AdminJobs = () => import("../pages/admin/Jobs.vue")
const AdminCompanies = () => import("../pages/admin/Companies.vue")
const AdminCandidates = () => import("../pages/admin/Candidates.vue")
const AdminSettings = () => import("../pages/admin/Settings.vue")

// Using existing pages instead of non-existent visitor pages
// const JobsListing = () => import("../pages/jobs/JobsListing.vue") // File doesn't exist
// const CompaniesListing = () => import("../pages/companies/CompaniesListing.vue") // File doesn't exist

// Candidate pages
const CandidateDashboard = () => import("../pages/candidate/Dashboard.vue")
// const CandidateProfile = () => import("../pages/candidate/Profile.vue") // File doesn't exist
const CandidateApplications = () => import("../pages/candidate/Applications.vue")
// const CandidateSavedJobs = () => import("../pages/candidate/SavedJobs.vue") // File doesn't exist
// const CandidateJobAlerts = () => import("../pages/candidate/JobAlerts.vue") // File doesn't exist
// const CandidateResume = () => import("../pages/candidate/Resume.vue") // File doesn't exist

// Employer pages
const EmployerDashboard = () => import("../pages/employer/Dashboard.vue")
// const EmployerJobs = () => import("../pages/employer/Jobs.vue") // File doesn't exist
const EmployerJobCreate = () => import("../pages/employer/JobCreate.vue")
// const EmployerJobEdit = () => import("../pages/employer/JobEdit.vue") // File doesn't exist
const EmployerApplications = () => import("../pages/employer/Applications.vue")
// const EmployerCompanyProfile = () => import("../pages/employer/CompanyProfile.vue") // File doesn't exist
// const EmployerAnalytics = () => import("../pages/employer/Analytics.vue") // File doesn't exist

// Layout components
const PublicLayout = () => import("../layouts/PublicLayout.vue")
const MainLayout = () => import("../layouts/MainLayout.vue")
// const AuthenticatedLayout = () => import("../layouts/AuthenticatedLayout.vue") // File doesn't exist - using MainLayout
// const AdminLayout = () => import("../layouts/AdminLayout.vue") // File doesn't exist - using MainLayout

// Route definitions
const routes: RouteRecordRaw[] = [
  // Public routes (available to all users)
  {
    path: "/",
    component: PublicLayout,
    children: [
      {
        path: "",
        name: "home",
        component: Home,
        meta: {
          title: "Home - Find Your Dream Job",
          description: "Discover amazing job opportunities and connect with top employers.",
          public: true
        }
      },
      {
        path: "/jobs",
        name: "jobs.index",
        component: Jobs,
        meta: {
          title: "Jobs - Browse Available Positions",
          description: "Browse thousands of job opportunities across various industries.",
          public: true
        }
      },
      {
        path: "/jobs/:slug",
        name: "jobs.show",
        component: JobDetails,
        meta: {
          title: "Job Details",
          description: "View detailed job information and apply online.",
          public: true
        }
      },
      {
        path: "/companies",
        name: "companies.index",
        component: Companies,
        meta: {
          title: "Companies - Discover Top Employers",
          description: "Explore companies and learn about their culture and opportunities.",
          public: true
        }
      },
      {
        path: "/companies/:slug",
        name: "companies.show",
        component: CompanyDetails,
        meta: {
          title: "Company Profile",
          description: "Learn about company culture, benefits, and job opportunities.",
          public: true
        }
      },
      {
        path: "/professions",
        name: "professions.index",
        component: Professions,
        meta: {
          title: "Professional Categories & Careers - Explore Career Paths",
          description: "Discover comprehensive career information organized by professional categories. Explore professions, required skills, education requirements, and job market insights.",
          public: true
        }
      },
      {
        path: "/about",
        name: "about",
        component: About,
        meta: {
          title: "About Us - Learn About Our Mission",
          description: "Learn about our mission to connect talented professionals with exceptional opportunities.",
          public: true
        }
      },
      {
        path: "/contact",
        name: "contact",
        component: Contact,
        meta: {
          title: "Contact Us - Get in Touch",
          description: "Get in touch with our team for support, partnerships, or general inquiries.",
          public: true
        }
      },
      {
        path: "/terms",
        name: "terms",
        component: Terms,
        meta: {
          title: "Terms of Service - User Agreement",
          description: "Read our terms of service to understand your rights and responsibilities.",
          public: true
        }
      },
      {
        path: "/privacy",
        name: "privacy",
        component: Privacy,
        meta: {
          title: "Privacy Policy - Data Protection",
          description: "Learn how we collect, use, and protect your personal information.",
          public: true
        }
      }
    ]
  },

  // Authentication routes
  {
    path: "/auth",
    component: PublicLayout,
    meta: { requiresGuest: true },
    children: [
      {
        path: "/login",
        name: "login",
        component: Login,
        meta: {
          title: "Login - Access Your Account",
          description: "Sign in to your account to access your dashboard.",
          public: true
        }
      },
      {
        path: "/register",
        name: "register",
        component: Register,
        meta: {
          title: "Register - Create Your Account",
          description: "Create a new account to start your job search or find candidates.",
          public: true
        }
      },
      // Commented out until these components are created
      // {
      //   path: "/forgot-password",
      //   name: "forgot-password",
      //   component: ForgotPassword,
      //   meta: {
      //     title: "Forgot Password",
      //     description: "Reset your password securely.",
      //     public: true
      //   }
      // },
      // {
      //   path: "/reset-password/:token",
      //   name: "reset-password",
      //   component: ResetPassword,
      //   meta: {
      //     title: "Reset Password",
      //     description: "Create a new password for your account.",
      //     public: true
      //   }
      // }
    ]
  },

  // Candidate routes
  {
    path: "/candidate",
    component: MainLayout,
    meta: { 
      requiresAuth: true, 
      requiredRole: "candidate" as UserRole,
      layout: "candidate"
    },
    children: [
      {
        path: "/candidate/dashboard",
        name: "candidate.dashboard",
        component: CandidateDashboard,
        meta: {
          title: "Candidate Dashboard",
          description: "Manage your job search and applications."
        }
      },
      // {
      //   path: "/candidate/profile",
      //   name: "candidate.profile",
      //   component: CandidateProfile,
      //   meta: {
      //     title: "My Profile",
      //     description: "Update your profile and showcase your skills."
      //   }
      // },
      {
        path: "/candidate/applications",
        name: "candidate.applications",
        component: CandidateApplications,
        meta: {
          title: "My Applications",
          description: "Track your job applications and their status."
        }
      },
      // {
      //   path: "/candidate/saved-jobs",
      //   name: "candidate.saved-jobs",
      //   component: CandidateSavedJobs,
      //   meta: {
      //     title: "Saved Jobs",
      //     description: "View jobs you have saved for later."
      //   }
      // },
      // {
      //   path: "/candidate/job-alerts",
      //   name: "candidate.job-alerts",
      //   component: CandidateJobAlerts,
      //   meta: {
      //     title: "Job Alerts",
      //     description: "Manage your job alert preferences."
      //   }
      // },
      // {
      //   path: "/candidate/resume",
      //   name: "candidate.resume",
      //   component: CandidateResume,
      //   meta: {
      //     title: "My Resume",
      //     description: "Manage your resume and documents."
      //   }
      // }
    ]
  },

  // Employer routes
  {
    path: "/employer",
    component: MainLayout,
    meta: { 
      requiresAuth: true, 
      requiredRole: "employer" as UserRole,
      layout: "employer"
    },
    children: [
      {
        path: "/employer/dashboard",
        name: "employer.dashboard",
        component: EmployerDashboard,
        meta: {
          title: "Employer Dashboard",
          description: "Manage your company and job postings."
        }
      },
      // {
      //   path: "/employer/jobs",
      //   name: "employer.jobs.index",
      //   component: EmployerJobs,
      //   meta: {
      //     title: "Manage Jobs",
      //     description: "View and manage your job postings."
      //   }
      // },
      {
        path: "/employer/jobs/create",
        name: "employer.jobs.create",
        component: EmployerJobCreate,
        meta: {
          title: "Post New Job",
          description: "Create a new job posting to find candidates."
        }
      },
      // {
      //   path: "/employer/jobs/:id/edit",
      //   name: "employer.jobs.edit",
      //   component: EmployerJobEdit,
      //   meta: {
      //     title: "Edit Job",
      //     description: "Update your job posting details."
      //   }
      // },
      {
        path: "/employer/applications",
        name: "employer.applications",
        component: EmployerApplications,
        meta: {
          title: "Job Applications",
          description: "Review applications from candidates."
        }
      },
      // {
      //   path: "/employer/company",
      //   name: "employer.company",
      //   component: EmployerCompanyProfile,
      //   meta: {
      //     title: "Company Profile",
      //     description: "Manage your company profile and information."
      //   }
      // },
      // {
      //   path: "/employer/analytics",
      //   name: "employer.analytics",
      //   component: EmployerAnalytics,
      //   meta: {
      //     title: "Analytics",
      //     description: "View hiring analytics and job performance."
      //   }
      // }
    ]
  },

  // Admin routes
  {
    path: "/admin",
    component: MainLayout,
    meta: { 
      requiresAuth: true, 
      requiredRole: "admin" as UserRole,
      layout: "admin"
    },
    children: [
      {
        path: "/admin/dashboard",
        name: "admin.dashboard",
        component: AdminDashboard,
        meta: {
          title: "Admin Dashboard",
          description: "System overview and management."
        }
      },
      {
        path: "/admin/users",
        name: "admin.users",
        component: AdminUsers,
        meta: {
          title: "User Management",
          description: "Manage all system users."
        }
      },
      {
        path: "/admin/jobs",
        name: "admin.jobs",
        component: AdminJobs,
        meta: {
          title: "Job Management",
          description: "Moderate and manage job postings."
        }
      },
      {
        path: "/admin/companies",
        name: "admin.companies",
        component: AdminCompanies,
        meta: {
          title: "Company Management",
          description: "Verify and manage company profiles."
        }
      },
      {
        path: "/admin/analytics",
        name: "admin.analytics",
        component: AdminAnalytics,
        meta: {
          title: "System Analytics",
          description: "View comprehensive system analytics."
        }
      },
      {
        path: "/admin/settings",
        name: "admin.settings",
        component: AdminSettings,
        meta: {
          title: "System Settings",
          description: "Configure system settings and preferences."
        }
      }
    ]
  },

  // Error routes
  {
    path: "/unauthorized",
    name: "unauthorized",
    component: Unauthorized,
    meta: {
      title: "Unauthorized Access",
      description: "You do not have permission to access this resource.",
      public: true
    }
  },
  {
    path: "/:pathMatch(.*)*",
    name: "not-found",
    component: NotFound,
    meta: {
      title: "Page Not Found",
      description: "The page you are looking for does not exist.",
      public: true
    }
  }
]

// Create router instance
const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Return saved position if available (browser back/forward)
    if (savedPosition) {
      return savedPosition
    }
    
    // Scroll to anchor if present
    if (to.hash) {
      return {
        el: to.hash,
        behavior: "smooth"
      }
    }
    
    // Scroll to top for new pages
    return { top: 0, behavior: "smooth" }
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Initialize auth store if not already done
  if (!authStore.isInitialized) {
    await authStore.initialize()
  }

  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      // Store intended route for redirect after login
      const returnUrl = encodeURIComponent(to.fullPath)
      return next(`/login?return=${returnUrl}`)
    }

    // Check session validity
    if (!authStore.checkSession()) {
      return next("/login")
    }

    // Check role requirements
    if (to.meta.requiredRole) {
      const requiredRole = to.meta.requiredRole as UserRole
      if (authStore.userRole !== requiredRole) {
        return next("/unauthorized")
      }
    }

    // Check permissions if specified
    if (to.meta.requiredPermissions) {
      const requiredPermissions = to.meta.requiredPermissions as string[]
      const hasAllPermissions = requiredPermissions.every(permission => 
        authStore.canAccess(permission)
      )
      
      if (!hasAllPermissions) {
        return next("/unauthorized")
      }
    }
  }

  // Check if route requires guest (not authenticated)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    // Redirect authenticated users to their dashboard
    return next(authStore.dashboardRoute)
  }

  // Update last activity for authenticated users
  if (authStore.isAuthenticated) {
    authStore.updateLastActivity()
  }

  next()
})

// After navigation guard for updating page metadata
router.afterEach((to) => {
  // Update page title
  if (to.meta.title) {
    document.title = to.meta.title as string
  }

  // Update meta description
  if (to.meta.description) {
    const metaDescription = document.querySelector('meta[name="description"]')
    if (metaDescription) {
      metaDescription.setAttribute('content', to.meta.description as string)
    }
  }

  // Update canonical URL
  const canonicalLink = document.querySelector('link[rel="canonical"]')
  if (canonicalLink) {
    canonicalLink.setAttribute('href', window.location.origin + to.fullPath)
  }

  // Add structured data for job-related pages
  if (to.name && typeof to.name === "string") {
    if (to.name.startsWith('jobs.') || to.name.startsWith('companies.')) {
      // This could be enhanced to add JSON-LD structured data
      console.log('Page is job/company related, could add structured data')
    }
  }
})

// Helper functions for programmatic navigation
export const navigationHelpers = {
  // Go to user's appropriate dashboard
  goToDashboard() {
    const authStore = useAuthStore()
    router.push(authStore.dashboardRoute)
  },

  // Go to login with return URL
  goToLogin(returnUrl?: string) {
    const url = returnUrl ? `/login?return=${encodeURIComponent(returnUrl)}` : '/login'
    router.push(url)
  },

  // Role-based navigation
  goToRoleSpecificRoute(baseRoute: string) {
    const authStore = useAuthStore()
    const rolePrefix = authStore.userRole !== 'visitor' ? `/${authStore.userRole}` : ''
    router.push(`${rolePrefix}${baseRoute}`)
  },

  // Safe navigation with role check
  async safeNavigate(to: string | RouteLocationNormalized, requiredRole?: UserRole) {
    const authStore = useAuthStore()
    
    if (requiredRole && authStore.userRole !== requiredRole) {
      router.push('/unauthorized')
      return false
    }

    await router.push(to)
    return true
  }
}

// Route name helpers
export const routeNames = {
  home: 'home',
  login: 'login',
  register: 'register',
  
  // Candidate routes
  candidateDashboard: 'candidate.dashboard',
  candidateProfile: 'candidate.profile',
  candidateApplications: 'candidate.applications',
  candidateSavedJobs: 'candidate.saved-jobs',
  candidateJobAlerts: 'candidate.job-alerts',
  candidateResume: 'candidate.resume',
  
  // Employer routes
  employerDashboard: 'employer.dashboard',
  employerJobs: 'employer.jobs.index',
  employerJobCreate: 'employer.jobs.create',
  employerJobEdit: 'employer.jobs.edit',
  employerApplications: 'employer.applications',
  employerCompany: 'employer.company',
  employerAnalytics: 'employer.analytics',
  
  // Admin routes
  adminDashboard: 'admin.dashboard',
  adminUsers: 'admin.users',
  adminJobs: 'admin.jobs',
  adminCompanies: 'admin.companies',
  adminAnalytics: 'admin.analytics',
  adminSettings: 'admin.settings',
  
  // Public routes
  jobsIndex: 'jobs.index',
  jobsShow: 'jobs.show',
  companiesIndex: 'companies.index',
  companiesShow: 'companies.show',
  
  // Error routes
  unauthorized: 'unauthorized',
  notFound: 'not-found'
}

// Type augmentation for route meta
declare module 'vue-router' {
  interface RouteMeta {
    title?: string
    description?: string
    requiresAuth?: boolean
    requiresGuest?: boolean
    requiredRole?: UserRole
    requiredPermissions?: string[]
    layout?: 'candidate' | 'employer' | 'admin' | 'public'
    public?: boolean
  }
}

export default router