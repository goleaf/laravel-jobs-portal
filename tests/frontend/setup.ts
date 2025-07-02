import { config } from '@vue/test-utils'
import { createTestingPinia } from '@pinia/testing'
import { createRouter, createWebHistory } from 'vue-router'
import { vi } from 'vitest'

// Create a test router
const routes = [
  { path: '/', name: 'home', component: { template: '<div>Home</div>' } },
  { path: '/jobs', name: 'jobs', component: { template: '<div>Jobs</div>' } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Mock Vue Router
config.global.mocks = {
  $router: {
    push: vi.fn(),
    go: vi.fn(),
    back: vi.fn(),
    forward: vi.fn(),
    resolve: vi.fn(),
    replace: vi.fn(),
  },
  $route: {
    path: '/jobs',
    params: {},
    query: {},
    name: 'jobs',
    fullPath: '/jobs',
    matched: [],
    meta: {},
    hash: '',
  },
  $t: vi.fn((key) => key),
  $tc: vi.fn((key) => key),
  $te: vi.fn(() => true),
  $d: vi.fn((date) => date.toString()),
  $n: vi.fn((number) => number.toString()),
  $i18n: {
    locale: 'en',
  }
}

// Global component stubs
config.global.stubs = {
  'router-link': true,
  'router-view': true,
  'MainLayout': {
    template: '<div class="main-layout"><slot /></div>',
    props: ['breadcrumbs']
  },
  'BaseButton': {
    template: '<button class="base-button" :disabled="disabled"><slot /></button>',
    props: ['disabled', 'loading', 'variant', 'size', 'type']
  },
  'BaseInput': {
    template: '<input class="base-input" :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" />',
    props: ['modelValue', 'type', 'placeholder', 'leftIcon', 'size'],
    emits: ['update:modelValue']
  },
  'JobCard': {
    template: '<div class="job-card">Job Card</div>',
    props: ['job', 'viewMode']
  },
}

// Global plugins
config.global.plugins = [
  createTestingPinia({
    createSpy: vi.fn,
  }),
  router
]

// Global directives
config.global.directives = {
  loading: vi.fn(),
}

// Mock window.scrollTo
Object.defineProperty(window, 'scrollTo', {
  value: vi.fn(),
  writable: true,
})

// Mock ResizeObserver
global.ResizeObserver = vi.fn().mockImplementation(() => ({
  observe: vi.fn(),
  unobserve: vi.fn(),
  disconnect: vi.fn(),
}))

// Mock IntersectionObserver
global.IntersectionObserver = vi.fn().mockImplementation(() => ({
  observe: vi.fn(),
  unobserve: vi.fn(),
  disconnect: vi.fn(),
}))

// Mock matchMedia
Object.defineProperty(window, 'matchMedia', {
  writable: true,
  value: vi.fn().mockImplementation(query => ({
    matches: false,
    media: query,
    onchange: null,
    addListener: vi.fn(), // deprecated
    removeListener: vi.fn(), // deprecated
    addEventListener: vi.fn(),
    removeEventListener: vi.fn(),
    dispatchEvent: vi.fn(),
  })),
})

// Mock Heroicons
vi.mock('@heroicons/vue/24/outline', () => ({
  MagnifyingGlassIcon: { template: '<svg>search</svg>' },
  MapPinIcon: { template: '<svg>map</svg>' },
  FunnelIcon: { template: '<svg>filter</svg>' },
  Squares2X2Icon: { template: '<svg>grid</svg>' },
  ListBulletIcon: { template: '<svg>list</svg>' },
  ChevronLeftIcon: { template: '<svg>left</svg>' },
  ChevronRightIcon: { template: '<svg>right</svg>' },
  ChevronDownIcon: { template: '<svg>down</svg>' },
  ChevronUpIcon: { template: '<svg>up</svg>' },
  XMarkIcon: { template: '<svg>close</svg>' },
  AdjustmentsHorizontalIcon: { template: '<svg>adjustments</svg>' },
  BuildingOfficeIcon: { template: '<svg>building</svg>' },
  ClockIcon: { template: '<svg>clock</svg>' },
  CurrencyDollarIcon: { template: '<svg>dollar</svg>' },
  MapIcon: { template: '<svg>location</svg>' },
  BriefcaseIcon: { template: '<svg>briefcase</svg>' },
}))

vi.mock('@heroicons/vue/24/solid', () => ({
  MagnifyingGlassIcon: { template: '<svg>search</svg>' },
  MapPinIcon: { template: '<svg>map</svg>' },
  FunnelIcon: { template: '<svg>filter</svg>' },
  Squares2X2Icon: { template: '<svg>grid</svg>' },
  ListBulletIcon: { template: '<svg>list</svg>' },
  ChevronLeftIcon: { template: '<svg>left</svg>' },
  ChevronRightIcon: { template: '<svg>right</svg>' },
  ChevronDownIcon: { template: '<svg>down</svg>' },
  ChevronUpIcon: { template: '<svg>up</svg>' },
  XMarkIcon: { template: '<svg>close</svg>' },
  AdjustmentsHorizontalIcon: { template: '<svg>adjustments</svg>' },
  BuildingOfficeIcon: { template: '<svg>building</svg>' },
  ClockIcon: { template: '<svg>clock</svg>' },
  CurrencyDollarIcon: { template: '<svg>dollar</svg>' },
  MapIcon: { template: '<svg>location</svg>' },
  BriefcaseIcon: { template: '<svg>briefcase</svg>' },
})) 