import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/Home.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/auth/Login.vue'),
      meta: { guest: true },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/Register.vue'),
      meta: { guest: true },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/Dashboard.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/jobs',
      name: 'jobs.index',
      component: () => import('@/views/jobs/Index.vue'),
    },
    {
      path: '/jobs/:id',
      name: 'jobs.show',
      component: () => import('@/views/jobs/Show.vue'),
      props: true,
    },
    {
      path: '/companies',
      name: 'companies.index',
      component: () => import('@/views/companies/Index.vue'),
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('@/views/admin/Dashboard.vue'),
      meta: { requiresAuth: true, role: 'admin' },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/errors/404.vue'),
    },
  ],
})

// Route guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'login' })
    return
  }

  // Check guest routes
  if (to.meta.guest && authStore.isAuthenticated) {
    next({ name: 'dashboard' })
    return
  }

  // Check role requirements
  if (to.meta.role && authStore.user?.role !== to.meta.role) {
    next({ name: 'home' })
    return
  }

  next()
})

export default router