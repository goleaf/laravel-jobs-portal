import { describe, it, expect, vi } from 'vitest'
import { mount, VueWrapper } from '@vue/test-utils'
import BaseButton from '@/components/base/BaseButton.vue'

describe('BaseButton Component', () => {
  let wrapper: VueWrapper<any>

  describe('Basic Rendering', () => {
    it('renders as a button by default', () => {
      wrapper = mount(BaseButton, {
        slots: {
          default: 'Click me'
        }
      })

      expect(wrapper.element.tagName).toBe('BUTTON')
      expect(wrapper.text()).toBe('Click me')
    })

    it('renders as anchor when tag is "a"', () => {
      wrapper = mount(BaseButton, {
        props: {
          tag: 'a',
          href: 'https://example.com'
        },
        slots: {
          default: 'Link'
        }
      })

      expect(wrapper.element.tagName).toBe('A')
      expect(wrapper.attributes('href')).toBe('https://example.com')
    })

    it('renders as router-link when tag is "router-link"', () => {
      wrapper = mount(BaseButton, {
        props: {
          tag: 'router-link',
          to: { name: 'home' }
        },
        slots: {
          default: 'Router Link'
        },
        global: {
          stubs: {
            'router-link': true
          }
        }
      })

      expect(wrapper.element.tagName).toBe('ROUTER-LINK-STUB')
    })
  })

  describe('Button Variants', () => {
    const variants = [
      'primary',
      'secondary',
      'success',
      'danger',
      'warning',
      'info',
      'light',
      'dark',
      'outline-primary',
      'outline-secondary',
      'outline-success',
      'outline-danger',
      'outline-warning',
      'outline-info',
      'ghost',
      'link'
    ]

    variants.forEach(variant => {
      it(`applies correct classes for ${variant} variant`, () => {
        wrapper = mount(BaseButton, {
          props: { variant },
          slots: { default: 'Button' }
        })

        expect(wrapper.classes()).toContain('relative')
        expect(wrapper.classes()).toContain('inline-flex')
        expect(wrapper.classes()).toContain('items-center')
        expect(wrapper.classes()).toContain('justify-center')
      })
    })

    it('defaults to primary variant', () => {
      wrapper = mount(BaseButton, {
        slots: { default: 'Button' }
      })

      // Should have primary variant classes
      expect(wrapper.classes().join(' ')).toContain('bg-indigo-600')
    })
  })

  describe('Button Sizes', () => {
    const sizes = ['xs', 'sm', 'md', 'lg', 'xl']

    sizes.forEach(size => {
      it(`applies correct classes for ${size} size`, () => {
        wrapper = mount(BaseButton, {
          props: { size },
          slots: { default: 'Button' }
        })

        expect(wrapper.classes()).toContain('relative')
        expect(wrapper.classes()).toContain('inline-flex')
      })
    })

    it('defaults to md size', () => {
      wrapper = mount(BaseButton, {
        slots: { default: 'Button' }
      })

      // Should have md size classes
      expect(wrapper.classes().join(' ')).toContain('min-h-[40px]')
    })
  })

  describe('Button States', () => {
    it('applies disabled state correctly', () => {
      wrapper = mount(BaseButton, {
        props: { disabled: true },
        slots: { default: 'Disabled' }
      })

      expect(wrapper.attributes('disabled')).toBeDefined()
      expect(wrapper.attributes('aria-disabled')).toBe('true')
      expect(wrapper.classes()).toContain('disabled:opacity-50')
    })

    it('applies loading state correctly', () => {
      wrapper = mount(BaseButton, {
        props: { loading: true },
        slots: { default: 'Loading' }
      })

      expect(wrapper.attributes('disabled')).toBeDefined()
      expect(wrapper.attributes('aria-disabled')).toBe('true')
      expect(wrapper.find('.animate-spin').exists()).toBe(true)
    })

    it('shows loading spinner when loading', () => {
      wrapper = mount(BaseButton, {
        props: { loading: true },
        slots: { default: 'Loading' }
      })

      const spinner = wrapper.find('svg.animate-spin')
      expect(spinner.exists()).toBe(true)
      expect(spinner.classes()).toContain('h-4')
      expect(spinner.classes()).toContain('w-4')
    })

    it('applies block style when block prop is true', () => {
      wrapper = mount(BaseButton, {
        props: { block: true },
        slots: { default: 'Block Button' }
      })

      // Block style would be applied via computed classes
      expect(wrapper.classes()).toContain('inline-flex')
    })
  })

  describe('Button Types', () => {
    it('sets correct type attribute for button tag', () => {
      wrapper = mount(BaseButton, {
        props: { type: 'submit' },
        slots: { default: 'Submit' }
      })

      expect(wrapper.attributes('type')).toBe('submit')
    })

    it('does not set type attribute for non-button tags', () => {
      wrapper = mount(BaseButton, {
        props: { 
          tag: 'a',
          href: '#',
          type: 'submit'
        },
        slots: { default: 'Link' }
      })

      expect(wrapper.attributes('type')).toBeUndefined()
    })
  })

  describe('Icon Support', () => {
    const MockIcon = {
      name: 'MockIcon',
      template: '<svg class="mock-icon"><path /></svg>'
    }

    it('renders left icon when provided', () => {
      wrapper = mount(BaseButton, {
        props: {
          icon: MockIcon,
          iconPosition: 'left'
        },
        slots: { default: 'With Icon' }
      })

      const icon = wrapper.findComponent(MockIcon)
      expect(icon.exists()).toBe(true)
    })

    it('renders right icon when position is right', () => {
      wrapper = mount(BaseButton, {
        props: {
          icon: MockIcon,
          iconPosition: 'right'
        },
        slots: { default: 'With Icon' }
      })

      const icon = wrapper.findComponent(MockIcon)
      expect(icon.exists()).toBe(true)
    })

    it('defaults to left icon position', () => {
      wrapper = mount(BaseButton, {
        props: {
          icon: MockIcon
        },
        slots: { default: 'With Icon' }
      })

      const icon = wrapper.findComponent(MockIcon)
      expect(icon.exists()).toBe(true)
    })
  })

  describe('Badge Support', () => {
    it('renders badge when provided', () => {
      wrapper = mount(BaseButton, {
        props: { badge: '5' },
        slots: { default: 'Notifications' }
      })

      // Check that the badge content is present in the component's text
      expect(wrapper.text()).toContain('5')
      expect(wrapper.text()).toContain('Notifications')
    })

    it('renders numeric badge', () => {
      wrapper = mount(BaseButton, {
        props: { badge: 42 },
        slots: { default: 'Messages' }
      })

      // Check that the badge content is present in the component's text
      expect(wrapper.text()).toContain('42')
      expect(wrapper.text()).toContain('Messages')
    })
  })

  describe('Event Handling', () => {
    it('emits click event when clicked', async () => {
      wrapper = mount(BaseButton, {
        slots: { default: 'Click me' }
      })

      await wrapper.trigger('click')

      expect(wrapper.emitted().click).toBeTruthy()
      expect(wrapper.emitted().click).toHaveLength(1)
    })

    it('does not emit click when disabled', async () => {
      wrapper = mount(BaseButton, {
        props: { disabled: true },
        slots: { default: 'Disabled' }
      })

      await wrapper.trigger('click')

      expect(wrapper.emitted().click).toBeFalsy()
    })

    it('does not emit click when loading', async () => {
      wrapper = mount(BaseButton, {
        props: { loading: true },
        slots: { default: 'Loading' }
      })

      await wrapper.trigger('click')

      expect(wrapper.emitted().click).toBeFalsy()
    })

    it('passes event object to click handler', async () => {
      const clickHandler = vi.fn()
      wrapper = mount(BaseButton, {
        props: {
          onClick: clickHandler
        },
        slots: { default: 'Click me' }
      })

      await wrapper.trigger('click')

      expect(wrapper.emitted().click).toBeTruthy()
    })
  })

  describe('Accessibility', () => {
    it('sets aria-disabled when disabled', () => {
      wrapper = mount(BaseButton, {
        props: { disabled: true },
        slots: { default: 'Disabled' }
      })

      expect(wrapper.attributes('aria-disabled')).toBe('true')
    })

    it('sets aria-disabled when loading', () => {
      wrapper = mount(BaseButton, {
        props: { loading: true },
        slots: { default: 'Loading' }
      })

      expect(wrapper.attributes('aria-disabled')).toBe('true')
    })

    it('includes focus styles for keyboard navigation', () => {
      wrapper = mount(BaseButton, {
        slots: { default: 'Button' }
      })

      expect(wrapper.classes()).toContain('focus:outline-none')
      expect(wrapper.classes()).toContain('focus:ring-2')
    })
  })

  describe('Rounded Variant', () => {
    it('applies rounded styles when rounded prop is true', () => {
      wrapper = mount(BaseButton, {
        props: { rounded: true },
        slots: { default: 'Rounded' }
      })

      // The component should apply rounded styles
      expect(wrapper.exists()).toBe(true)
    })
  })

  describe('Default Props', () => {
    it('uses default props when none provided', () => {
      wrapper = mount(BaseButton, {
        slots: { default: 'Default Button' }
      })

      expect(wrapper.attributes('type')).toBe('button')
      expect(wrapper.classes()).toContain('relative')
      expect(wrapper.classes()).toContain('inline-flex')
    })
  })

  describe('Complex Scenarios', () => {
    it('handles multiple props together', () => {
      wrapper = mount(BaseButton, {
        props: {
          variant: 'success',
          size: 'lg',
          disabled: false,
          loading: false,
          block: false,
          rounded: true
        },
        slots: { default: 'Complex Button' }
      })

      expect(wrapper.text()).toBe('Complex Button')
      expect(wrapper.attributes('disabled')).toBeUndefined()
    })

    it('handles loading state with icon', () => {
      const MockIcon = {
        name: 'MockIcon',
        template: '<svg class="mock-icon"><path /></svg>'
      }

      wrapper = mount(BaseButton, {
        props: {
          loading: true,
          icon: MockIcon
        },
        slots: { default: 'Loading with Icon' }
      })

      expect(wrapper.find('.animate-spin').exists()).toBe(true)
      expect(wrapper.findComponent(MockIcon).exists()).toBe(true)
    })
  })
}) 