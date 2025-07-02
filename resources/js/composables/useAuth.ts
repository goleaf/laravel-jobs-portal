import { computed, watch, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import type { 
  UserRole, 
  Candidate, 
  Employer, 
  Administrator,
  LoginCredentials,
  RegisterData 
} from '@/types/user';

export function useAuth() {
  const authStore = useAuthStore();
  const router = useRouter();

  // Reactive auth state
  const user = computed(() => authStore.user);
  const visitor = computed(() => authStore.visitor);
  const isAuthenticated = computed(() => authStore.isAuthenticated);
  const isLoading = computed(() => authStore.isLoading);
  const userRole = computed(() => authStore.userRole);
  const userName = computed(() => authStore.userName);
  const userAvatar = computed(() => authStore.userAvatar);
  const dashboardRoute = computed(() => authStore.dashboardRoute);

  // Role checks
  const isCandidate = computed(() => authStore.isCandidate);
  const isEmployer = computed(() => authStore.isEmployer);
  const isAdmin = computed(() => authStore.isAdmin);
  const isVisitor = computed(() => authStore.isVisitor);

  // Permission checks
  const canAccess = (permission: string): boolean => {
    return authStore.canAccess(permission);
  };

  const hasRole = (role: UserRole): boolean => {
    return userRole.value === role;
  };

  const hasAnyRole = (roles: UserRole[]): boolean => {
    return roles.includes(userRole.value);
  };

  // Authentication actions
  const login = async (credentials: LoginCredentials): Promise<boolean> => {
    try {
      await authStore.login(credentials);
      return true;
    } catch (error) {
      console.error('Login failed:', error);
      return false;
    }
  };

  const register = async (data: RegisterData): Promise<boolean> => {
    try {
      await authStore.register(data);
      return true;
    } catch (error) {
      console.error('Registration failed:', error);
      return false;
    }
  };

  const logout = async (): Promise<void> => {
    try {
      await authStore.logout();
      await router.push('/');
    } catch (error) {
      console.error('Logout failed:', error);
      // Force logout even if API call fails
      await router.push('/');
    }
  };

  const refreshToken = async (): Promise<boolean> => {
    try {
      return await authStore.refreshToken();
    } catch (error) {
      console.error('Token refresh failed:', error);
      return false;
    }
  };

  // Navigation guards
  const requireAuth = (): boolean => {
    if (!isAuthenticated.value) {
      router.push('/login');
      return false;
    }
    return true;
  };

  const requireRole = (requiredRole: UserRole): boolean => {
    if (!isAuthenticated.value) {
      router.push('/login');
      return false;
    }
    
    if (userRole.value !== requiredRole) {
      router.push('/unauthorized');
      return false;
    }
    
    return true;
  };

  const requireAnyRole = (requiredRoles: UserRole[]): boolean => {
    if (!isAuthenticated.value) {
      router.push('/login');
      return false;
    }
    
    if (!requiredRoles.includes(userRole.value)) {
      router.push('/unauthorized');
      return false;
    }
    
    return true;
  };

  const requirePermission = (permission: string): boolean => {
    if (!isAuthenticated.value) {
      router.push('/login');
      return false;
    }
    
    if (!canAccess(permission)) {
      router.push('/unauthorized');
      return false;
    }
    
    return true;
  };

  // Role-specific data getters
  const getCandidateData = (): Candidate | null => {
    return authStore.getCandidateData();
  };

  const getEmployerData = (): Employer | null => {
    return authStore.getEmployerData();
  };

  const getAdminData = (): Administrator | null => {
    return authStore.getAdminData();
  };

  // Session management
  const updateLastActivity = (): void => {
    authStore.updateLastActivity();
  };

  const checkSession = (): boolean => {
    return authStore.checkSession();
  };

  // Initialize auth state
  const initialize = async (): Promise<void> => {
    await authStore.initialize();
  };

  // Visitor management
  const initializeVisitor = () => {
    return authStore.initializeVisitor();
  };

  const updateVisitorPreferences = (preferences: any) => {
    authStore.updateVisitorPreferences(preferences);
  };

  // Auto session check
  let sessionCheckInterval: NodeJS.Timeout | null = null;

  const startSessionCheck = (intervalMs: number = 60000): void => {
    if (sessionCheckInterval) {
      clearInterval(sessionCheckInterval);
    }
    
    sessionCheckInterval = setInterval(() => {
      if (isAuthenticated.value) {
        checkSession();
      }
    }, intervalMs);
  };

  const stopSessionCheck = (): void => {
    if (sessionCheckInterval) {
      clearInterval(sessionCheckInterval);
      sessionCheckInterval = null;
    }
  };

  // Activity tracking
  const trackActivity = (): void => {
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
    
    const activityHandler = () => {
      if (isAuthenticated.value) {
        updateLastActivity();
      }
    };

    events.forEach(event => {
      document.addEventListener(event, activityHandler, { passive: true });
    });

    // Cleanup function
    return () => {
      events.forEach(event => {
        document.removeEventListener(event, activityHandler);
      });
    };
  };

  // Handle logout events from other tabs
  const handleStorageEvent = (event: StorageEvent) => {
    if (event.key === 'auth_token' && event.newValue === null) {
      // Token was removed in another tab, logout here too
      authStore.logout();
    }
  };

  // Lifecycle hooks
  onMounted(() => {
    // Listen for auth events
    window.addEventListener('auth:logout', logout);
    window.addEventListener('storage', handleStorageEvent);
    
    // Start session monitoring
    startSessionCheck();
    
    // Track user activity
    const cleanupActivity = trackActivity();

    // Cleanup on unmount
    onUnmounted(() => {
      window.removeEventListener('auth:logout', logout);
      window.removeEventListener('storage', handleStorageEvent);
      stopSessionCheck();
      cleanupActivity();
    });
  });

  // Watch for route changes to update activity
  watch(() => router.currentRoute.value.path, () => {
    if (isAuthenticated.value) {
      updateLastActivity();
    }
  });

  // Return composable interface
  return {
    // State
    user,
    visitor,
    isAuthenticated,
    isLoading,
    userRole,
    userName,
    userAvatar,
    dashboardRoute,
    
    // Role checks
    isCandidate,
    isEmployer,
    isAdmin,
    isVisitor,
    hasRole,
    hasAnyRole,
    canAccess,
    
    // Authentication actions
    login,
    register,
    logout,
    refreshToken,
    initialize,
    
    // Navigation guards
    requireAuth,
    requireRole,
    requireAnyRole,
    requirePermission,
    
    // Role-specific data
    getCandidateData,
    getEmployerData,
    getAdminData,
    
    // Session management
    updateLastActivity,
    checkSession,
    startSessionCheck,
    stopSessionCheck,
    
    // Visitor management
    initializeVisitor,
    updateVisitorPreferences,
  };
} 