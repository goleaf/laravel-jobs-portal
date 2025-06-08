import { createRouter, createWebHistory } from "vue-router"
import { useAuthStore } from "../stores/auth"

// Lazy load components for better performance
const Home = () => import("../pages/Home.vue")
const Jobs = () => import("../pages/Jobs.vue")
const JobDetails = () => import("../pages/JobDetails.vue")
const Companies = () => import("../pages/Companies.vue")
const CompanyDetails = () => import("../pages/CompanyDetails.vue")
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

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: "/",
      name: "home",
      component: Home,
      meta: { title: "Find Your Dream Job" }
    },
    {
      path: "/jobs",
      name: "jobs",
      component: Jobs,
      meta: { title: "Browse Jobs" }
    },
    {
      path: "/jobs/:id",
      name: "job-details",
      component: JobDetails,
      meta: { title: "Job Details" }
    },
    {
      path: "/companies",
      name: "companies", 
      component: Companies,
      meta: { title: "Browse Companies" }
    },
    {
      path: "/companies/:id",
      name: "company-details",
      component: CompanyDetails,
      meta: { title: "Company Details" }
    },
    {
      path: "/login",
      name: "login",
      component: Login,
      meta: { title: "Login", guest: true }
    },
    {
      path: "/register",
      name: "register",
      component: Register,
      meta: { title: "Register", guest: true }
    },
    {
      path: "/dashboard",
      name: "dashboard",
      component: Dashboard,
      meta: { title: "Dashboard", requiresAuth: true }
    },
    {
      path: "/profile",
      name: "profile",
      component: Profile,
      meta: { title: "Profile", requiresAuth: true }
    },
    // Admin routes
    {
      path: "/admin",
      name: "admin-dashboard",
      component: AdminDashboard,
      meta: { title: "Admin Dashboard", requiresAuth: true, requiresAdmin: true }
    },
    {
      path: "/admin/jobs",
      name: "admin-jobs",
      component: AdminJobs,
      meta: { title: "Manage Jobs", requiresAuth: true, requiresAdmin: true }
    },
    {
      path: "/admin/companies",
      name: "admin-companies",
      component: AdminCompanies,
      meta: { title: "Manage Companies", requiresAuth: true, requiresAdmin: true }
    },
    {
      path: "/admin/candidates",
      name: "admin-candidates",
      component: AdminCandidates,
      meta: { title: "Manage Candidates", requiresAuth: true, requiresAdmin: true }
    },
    {
      path: "/admin/settings",
      name: "admin-settings",
      component: AdminSettings,
      meta: { title: "Admin Settings", requiresAuth: true, requiresAdmin: true }
    },
    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      component: NotFound,
      meta: { title: "Page Not Found" }
    }
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  
  // Set page title
  document.title = to.meta.title 
    ? `${to.meta.title} - JobPortal` 
    : "JobPortal"

  // Check authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: "login", query: { redirect: to.fullPath } })
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: "dashboard" })
  } else if (to.meta.requiresAdmin && (!authStore.isAuthenticated || !authStore.isAdmin)) {
    // Redirect non-admin users trying to access admin routes
    next({ name: "dashboard" })
  } else {
    next()
  }
})

export default router