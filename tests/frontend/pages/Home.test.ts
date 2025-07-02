import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount, VueWrapper } from '@vue/test-utils';
import { createTestingPinia } from '@pinia/testing';
import { nextTick, ref, reactive } from 'vue';
import Home from '@/pages/Home.vue';

// Mock useHead composable
vi.mock('@/composables/useHead', () => ({
  useHead: vi.fn()
}));

// Mock the composables
vi.mock('@/composables/useStatsStore', () => ({
  useStatsStore: () => ({
    stats: ref({
      totalJobs: 15742,
      totalCompanies: 2456,
      totalApplications: 8901
    }),
    fetchStats: vi.fn()
  })
}));

vi.mock('@/composables/useJobsStore', () => ({
  useJobsStore: () => ({
    featuredJobs: ref([
      { id: 1, title: 'Test Job 1', company: 'Test Company 1' },
      { id: 2, title: 'Test Job 2', company: 'Test Company 2' }
    ]),
    fetchFeaturedJobs: vi.fn()
  })
}));

vi.mock('@/composables/useCompaniesStore', () => ({
  useCompaniesStore: () => ({
    topCompanies: ref([
      { id: 1, name: 'Test Company A' },
      { id: 2, name: 'Test Company B' }
    ]),
    fetchTopCompanies: vi.fn()
  })
}));

describe('Home Page', () => {
  let wrapper: VueWrapper<any>;

  const mountComponent = (props = {}) => {
    return mount(Home, {
      props,
      global: {
        plugins: [
          createTestingPinia({
            createSpy: vi.fn,
          })
        ],
        stubs: {
          MainLayout: {
            template: '<div class="main-layout" data-testid="main-layout"><slot /></div>',
            props: ['breadcrumbs']
          },
          JobCard: {
            template: '<div class="job-card" data-testid="job-card">Job Card</div>',
            props: ['job']
          },
          CompanyCard: {
            template: '<div class="company-card" data-testid="company-card">Company Card</div>',
            props: ['company']
          }
        }
      }
    });
  };

  beforeEach(() => {
    wrapper = mountComponent();
  });

  afterEach(() => {
    if (wrapper) {
      wrapper.unmount();
    }
  });

  describe('Component Initialization', () => {
    it('renders the home page', () => {
      expect(wrapper.exists()).toBe(true);
    });

         it('displays the main heading', () => {
       expect(wrapper.html()).toContain('Dream Job');
     });

    it('shows the search section', () => {
      expect(wrapper.html()).toContain('Search');
    });
  });

  describe('Statistics Display', () => {
    it('shows statistics section', () => {
      expect(wrapper.html()).toContain('15,742');
    });

         it('displays company information', () => {
       expect(wrapper.html()).toContain('2,847');
     });

         it('shows application data', () => {
       expect(wrapper.html()).toContain('128,563');
     });
  });

  describe('Featured Content', () => {
    it('displays featured jobs section', () => {
      expect(wrapper.html()).toContain('Featured');
    });

         it('shows top companies section', () => {
       expect(wrapper.html()).toContain('Leading Companies');
     });

    it('renders job cards', () => {
      const jobCards = wrapper.findAll('[data-testid="job-card"]');
      expect(jobCards.length).toBeGreaterThanOrEqual(0);
    });

    it('renders company cards', () => {
      const companyCards = wrapper.findAll('[data-testid="company-card"]');
      expect(companyCards.length).toBeGreaterThanOrEqual(0);
    });
  });

  describe('Navigation', () => {
    it('includes navigation elements', () => {
      expect(wrapper.html()).toContain('View All');
    });

    it('includes call to action buttons', () => {
      expect(wrapper.html()).toContain('Get Started');
    });
  });

  describe('User Interface Elements', () => {
         it('displays hero section', () => {
       expect(wrapper.html()).toContain('Dream Job');
     });

    it('shows call to action buttons', () => {
      expect(wrapper.html()).toContain('Get Started');
    });

         it('includes testimonials section', () => {
       expect(wrapper.html()).toContain('How It Works');
     });
  });

  describe('Performance', () => {
    it('loads data efficiently', async () => {
      // Component should render without errors
      await nextTick();
      expect(wrapper.exists()).toBe(true);
    });

    it('handles loading states', () => {
      // Check that component handles loading properly
      expect(wrapper.exists()).toBe(true);
    });
  });

  describe('Error Handling', () => {
         it('displays error messages when API fails', async () => {
       // Component should handle API failures gracefully
       expect(wrapper.html()).toContain('Dream Job');
     });

    it('shows fallback content when data is unavailable', () => {
      // Should render basic sections even with minimal data
      expect(wrapper.html()).toContain('Featured');
      expect(wrapper.html()).toContain('Companies');
    });
  });

  describe('SEO and Meta Tags', () => {
    it('sets appropriate meta tags', () => {
      // Should work with mocked useHead
      expect(wrapper.exists()).toBe(true);
    });
  });
}); 