<template>
  <div class="flex flex-col h-full">
    <!-- Logo / Brand -->
    <div class="flex items-center px-4 py-4">
      <div v-if="!isCollapsed" class="flex items-center">
        <div class="flex-shrink-0">
          <svg class="h-8 w-8 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h1 class="text-xl font-bold text-white">JobPortal</h1>
        </div>
      </div>
      <div v-else class="flex justify-center w-full">
        <svg class="h-8 w-8 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 pb-4 space-y-1 sidebar-scroll overflow-y-auto">
      <template v-for="item in navigationItems" :key="item.name">
        <!-- Simple navigation item -->
        <router-link
          v-if="!item.children"
          :to="item.to"
          :class="getNavItemClasses(item)"
          :aria-current="isActive(item) ? 'page' : undefined"
        >
          <component :is="item.icon" :class="getIconClasses()" />
          <span v-if="!isCollapsed" class="ml-3 flex-1">{{ item.name }}</span>
          <span 
            v-if="item.badge && !isCollapsed" 
            :class="getBadgeClasses(item.badgeVariant)"
          >
            {{ item.badge }}
          </span>
        </router-link>

        <!-- Navigation item with children (collapsible) -->
        <div v-else>
          <button
            @click="toggleSection(item.name)"
            :class="getNavItemClasses(item, true)"
            :aria-expanded="openSections.includes(item.name)"
          >
            <component :is="item.icon" :class="getIconClasses()" />
            <span v-if="!isCollapsed" class="ml-3 flex-1 text-left">{{ item.name }}</span>
            <svg
              v-if="!isCollapsed"
              :class="{ 'rotate-90': openSections.includes(item.name) }"
              class="ml-3 h-5 w-5 transform transition-transform duration-200"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
          </button>

          <!-- Submenu -->
          <div 
            v-show="(openSections.includes(item.name) || isCollapsed) && !isCollapsed"
            class="space-y-1"
          >
            <router-link
              v-for="child in item.children"
              :key="child.name"
              :to="child.to"
              :class="getSubNavItemClasses(child)"
              :aria-current="isActive(child) ? 'page' : undefined"
            >
              <component :is="child.icon" :class="getIconClasses(true)" />
              <span class="ml-3">{{ child.name }}</span>
              <span 
                v-if="child.badge" 
                :class="getBadgeClasses(child.badgeVariant)"
              >
                {{ child.badge }}
              </span>
            </router-link>
          </div>

          <!-- Collapsed submenu tooltip -->
          <div 
            v-if="isCollapsed"
            class="relative group"
          >
            <div class="absolute left-full top-0 ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
              <div class="space-y-1">
                <div v-for="child in item.children" :key="child.name" class="block">
                  {{ child.name }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </nav>

    <!-- User section -->
    <div class="flex-shrink-0 p-4 border-t border-gray-700">
      <div v-if="!isCollapsed" class="flex items-center">
        <div class="flex-shrink-0">
          <img
            class="h-8 w-8 rounded-full"
            :src="userAvatar || '/images/default-avatar.png'"
            :alt="userName || 'User'"
          />
        </div>
        <div class="ml-3 flex-1 min-w-0">
          <p class="text-sm font-medium text-white truncate">
            {{ userName || 'Guest' }}
          </p>
          <p class="text-sm text-gray-300 truncate capitalize">
            {{ userRole }}
          </p>
        </div>
        <div class="ml-3">
          <button
            @click="logout"
            class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
            aria-label="Sign out"
          >
            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
      </div>
      <div v-else class="flex justify-center">
        <button
          @click="logout"
          class="text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
          aria-label="Sign out"
        >
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import type { Component } from 'vue';
import type { UserRole } from '@/types/user';

// Import icons (you can replace these with your preferred icon library)
import {
  HomeIcon,
  BriefcaseIcon,
  BuildingOfficeIcon,
  UserGroupIcon,
  ChartBarIcon,
  Cog6ToothIcon,
  DocumentTextIcon,
  HeartIcon,
  BellIcon,
  InboxIcon,
  UserIcon,
  PlusIcon,
  ClipboardDocumentListIcon,
  MagnifyingGlassIcon
} from '@heroicons/vue/24/outline';

interface Props {
  userRole: UserRole;
  isCollapsed: boolean;
}

const props = defineProps<Props>();

const route = useRoute();
const { userName, userAvatar, logout } = useAuth();

// State for collapsible sections
const openSections = ref<string[]>(['Jobs', 'Company', 'Applications', 'Users']);

interface NavigationItem {
  name: string;
  to: string;
  icon: Component;
  badge?: string | number;
  badgeVariant?: 'primary' | 'success' | 'warning' | 'danger';
  children?: NavigationItem[];
}

// Role-based navigation configuration
const navigationConfig: Record<UserRole, NavigationItem[]> = {
  visitor: [
    {
      name: 'Browse Jobs',
      to: '/jobs',
      icon: MagnifyingGlassIcon
    },
    {
      name: 'Companies',
      to: '/companies',
      icon: BuildingOfficeIcon
    },
    {
      name: 'Sign In',
      to: '/login',
      icon: UserIcon
    }
  ],
  candidate: [
    {
      name: 'Dashboard',
      to: '/candidate/dashboard',
      icon: HomeIcon
    },
    {
      name: 'Browse Jobs',
      to: '/jobs',
      icon: MagnifyingGlassIcon
    },
    {
      name: 'My Applications',
      to: '/candidate/applications',
      icon: ClipboardDocumentListIcon,
      badge: 3,
      badgeVariant: 'primary'
    },
    {
      name: 'Saved Jobs',
      to: '/candidate/saved-jobs',
      icon: HeartIcon,
      badge: 12,
      badgeVariant: 'success'
    },
    {
      name: 'Job Alerts',
      to: '/candidate/job-alerts',
      icon: BellIcon
    },
    {
      name: 'Profile',
      to: '/candidate/profile',
      icon: UserIcon
    },
    {
      name: 'Resume',
      to: '/candidate/resume',
      icon: DocumentTextIcon
    }
  ],
  employer: [
    {
      name: 'Dashboard',
      to: '/employer/dashboard',
      icon: HomeIcon
    },
    {
      name: 'Jobs',
      to: '/employer/jobs',
      icon: BriefcaseIcon,
      children: [
        {
          name: 'All Jobs',
          to: '/employer/jobs',
          icon: BriefcaseIcon,
          badge: 8,
          badgeVariant: 'primary'
        },
        {
          name: 'Post New Job',
          to: '/employer/jobs/create',
          icon: PlusIcon
        }
      ]
    },
    {
      name: 'Applications',
      to: '/employer/applications',
      icon: InboxIcon,
      badge: 15,
      badgeVariant: 'warning'
    },
    {
      name: 'Company',
      to: '/employer/company',
      icon: BuildingOfficeIcon,
      children: [
        {
          name: 'Company Profile',
          to: '/employer/company',
          icon: BuildingOfficeIcon
        },
        {
          name: 'Team',
          to: '/employer/company/team',
          icon: UserGroupIcon
        }
      ]
    },
    {
      name: 'Analytics',
      to: '/employer/analytics',
      icon: ChartBarIcon
    }
  ],
  admin: [
    {
      name: 'Dashboard',
      to: '/admin/dashboard',
      icon: HomeIcon
    },
    {
      name: 'Users',
      to: '/admin/users',
      icon: UserGroupIcon,
      badge: 1250,
      badgeVariant: 'primary'
    },
    {
      name: 'Jobs',
      to: '/admin/jobs',
      icon: BriefcaseIcon,
      badge: 45,
      badgeVariant: 'success'
    },
    {
      name: 'Companies',
      to: '/admin/companies',
      icon: BuildingOfficeIcon,
      badge: 12,
      badgeVariant: 'warning'
    },
    {
      name: 'Analytics',
      to: '/admin/analytics',
      icon: ChartBarIcon
    },
    {
      name: 'Settings',
      to: '/admin/settings',
      icon: Cog6ToothIcon
    }
  ]
};

const navigationItems = computed(() => navigationConfig[props.userRole] || []);

// Methods
const toggleSection = (sectionName: string) => {
  const index = openSections.value.indexOf(sectionName);
  if (index > -1) {
    openSections.value.splice(index, 1);
  } else {
    openSections.value.push(sectionName);
  }
};

const isActive = (item: NavigationItem): boolean => {
  return route.path === item.to || route.path.startsWith(item.to + '/');
};

const getNavItemClasses = (item: NavigationItem, hasChildren = false) => {
  const baseClasses = [
    'group',
    'flex',
    'items-center',
    'px-2',
    'py-2',
    'text-sm',
    'font-medium',
    'rounded-md',
    'transition-colors',
    'duration-200'
  ];

  if (hasChildren) {
    baseClasses.push('w-full', 'text-left');
  }

  if (isActive(item) && !hasChildren) {
    baseClasses.push(
      'bg-gray-900',
      'text-white'
    );
  } else {
    baseClasses.push(
      'text-gray-300',
      'hover:bg-gray-700',
      'hover:text-white'
    );
  }

  return baseClasses;
};

const getSubNavItemClasses = (item: NavigationItem) => {
  const baseClasses = [
    'group',
    'flex',
    'items-center',
    'pl-8',
    'pr-2',
    'py-2',
    'text-sm',
    'font-medium',
    'rounded-md',
    'transition-colors',
    'duration-200'
  ];

  if (isActive(item)) {
    baseClasses.push(
      'bg-gray-900',
      'text-white'
    );
  } else {
    baseClasses.push(
      'text-gray-400',
      'hover:bg-gray-700',
      'hover:text-white'
    );
  }

  return baseClasses;
};

const getIconClasses = (isSubItem = false) => [
  'flex-shrink-0',
  isSubItem ? 'h-4 w-4' : 'h-5 w-5'
];

const getBadgeClasses = (variant: string = 'primary') => {
  const baseClasses = [
    'ml-3',
    'inline-block',
    'py-0.5',
    'px-2',
    'text-xs',
    'font-medium',
    'rounded-full'
  ];

  const variantClasses = {
    primary: ['bg-indigo-100', 'text-indigo-800'],
    success: ['bg-green-100', 'text-green-800'],
    warning: ['bg-yellow-100', 'text-yellow-800'],
    danger: ['bg-red-100', 'text-red-800']
  };

  return [...baseClasses, ...variantClasses[variant]];
};
</script>

<style scoped>
/* Ensure smooth transitions for all interactive elements */
.transition-colors {
  transition-property: color, background-color, border-color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

.transition-transform {
  transition-property: transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

/* Tooltip arrow for collapsed state */
.group:hover .absolute::before {
  content: '';
  position: absolute;
  top: 50%;
  left: -4px;
  transform: translateY(-50%);
  border: 4px solid transparent;
  border-right-color: #1f2937;
}
</style> 