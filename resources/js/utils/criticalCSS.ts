/**
 * Critical CSS and Performance Optimization Utilities
 * 
 * This module provides utilities for:
 * - Critical CSS identification and loading
 * - Non-critical CSS deferred loading
 * - CSS performance monitoring
 */

interface CriticalCSSConfig {
  // Above-the-fold selectors that should be inlined
  criticalSelectors: string[]
  // CSS files to defer loading
  deferredFiles: string[]
  // Performance monitoring options
  monitoring: {
    enabled: boolean
    reportEndpoint?: string
  }
}

class CriticalCSSManager {
  private config: CriticalCSSConfig
  private loadedStyles: Set<string> = new Set()
  private performanceMetrics: {
    criticalCSSSize: number
    deferredCSSSize: number
    loadTimes: Record<string, number>
  } = {
    criticalCSSSize: 0,
    deferredCSSSize: 0,
    loadTimes: {}
  }

  constructor(config: CriticalCSSConfig) {
    this.config = config
    this.initializePerformanceMonitoring()
  }

  /**
   * Extract critical CSS for above-the-fold content
   */
  extractCriticalCSS(): string {
    const criticalRules: string[] = []
    
    // Get all stylesheets
    const styleSheets = Array.from(document.styleSheets)
    
    for (const sheet of styleSheets) {
      try {
        const rules = Array.from(sheet.cssRules || sheet.rules || [])
        
        for (const rule of rules) {
          if (rule instanceof CSSStyleRule) {
            // Check if this rule applies to critical selectors
            if (this.isCriticalSelector(rule.selectorText)) {
              criticalRules.push(rule.cssText)
            }
          } else if (rule instanceof CSSMediaRule) {
            // Include media queries for responsive critical styles
            const mediaRules = Array.from(rule.cssRules)
            const criticalMediaRules = mediaRules
              .filter(mediaRule => 
                mediaRule instanceof CSSStyleRule && 
                this.isCriticalSelector(mediaRule.selectorText)
              )
              .map(mediaRule => mediaRule.cssText)
            
            if (criticalMediaRules.length > 0) {
              criticalRules.push(`@media ${rule.media.mediaText} { ${criticalMediaRules.join('')} }`)
            }
          }
        }
      } catch (e) {
        // Skip cross-origin stylesheets
        console.warn('Cannot access stylesheet:', sheet.href, e)
      }
    }
    
    const criticalCSS = criticalRules.join('')
    this.performanceMetrics.criticalCSSSize = criticalCSS.length
    
    return criticalCSS
  }

  /**
   * Check if a selector is considered critical
   */
  private isCriticalSelector(selectorText: string): boolean {
    if (!selectorText) return false
    
    // Default critical selectors (above-the-fold elements)
    const defaultCriticalPatterns = [
      /^(html|body)/,
      /^\.hero/, // Hero sections
      /^\.header/, // Headers
      /^\.navbar/, // Navigation
      /^\.banner/, // Banners
      /^\.main-content/, // Main content containers
      /^\.breadcrumb/, // Breadcrumbs
      /^\.page-title/, // Page titles
      /^\.loading/, // Loading states
      /^\.error/, // Error messages
      /^\.alert/, // Alert messages
      // Utility classes that are commonly used above-the-fold
      /^\.container/,
      /^\.grid/,
      /^\.flex/,
      /^\.block/,
      /^\.hidden/,
      /^\.sr-only/,
      // Responsive utilities for mobile-first critical rendering
      /^\.sm:/,
      /^\.md:/,
      // Interactive elements that should respond immediately
      /^\.btn/,
      /^\.button/,
      /^\.link/,
      // Form elements for immediate interaction
      /^\.form/,
      /^\.input/,
      /^\.select/,
      /^\.checkbox/,
      /^\.radio/
    ]
    
    const allCriticalPatterns = [
      ...defaultCriticalPatterns,
      ...this.config.criticalSelectors.map(selector => new RegExp(selector))
    ]
    
    return allCriticalPatterns.some(pattern => pattern.test(selectorText))
  }

  /**
   * Defer loading of non-critical CSS
   */
  async deferNonCriticalCSS(): Promise<void> {
    const deferredPromises = this.config.deferredFiles.map(async (fileName) => {
      if (this.loadedStyles.has(fileName)) {
        return Promise.resolve()
      }
      
      const startTime = performance.now()
      
      return new Promise<void>((resolve, reject) => {
        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = fileName
        link.media = 'print' // Load with low priority
        
        link.onload = () => {
          // Switch to screen media after load
          link.media = 'all'
          this.loadedStyles.add(fileName)
          
          const loadTime = performance.now() - startTime
          this.performanceMetrics.loadTimes[fileName] = loadTime
          
          // Estimate file size (rough approximation)
          this.performanceMetrics.deferredCSSSize += this.estimateFileSize(fileName)
          
          resolve()
        }
        
        link.onerror = () => {
          console.warn(`Failed to load deferred CSS: ${fileName}`)
          reject(new Error(`Failed to load ${fileName}`))
        }
        
        document.head.appendChild(link)
      })
    })
    
    try {
      await Promise.all(deferredPromises)
      console.log('All deferred CSS loaded successfully')
    } catch (error) {
      console.error('Some deferred CSS failed to load:', error)
    }
  }

  /**
   * Estimate file size based on link element
   */
  private estimateFileSize(fileName: string): number {
    // This is a rough estimation - in a real implementation,
    // you might want to use the Response headers or other methods
    try {
      const styleSheets = Array.from(document.styleSheets)
      const sheet = styleSheets.find(s => s.href && s.href.includes(fileName))
      if (sheet) {
        const rules = Array.from(sheet.cssRules || [])
        return rules.reduce((size, rule) => size + rule.cssText.length, 0)
      }
    } catch (e) {
      // Fallback estimation
    }
    return 0
  }

  /**
   * Initialize performance monitoring
   */
  private initializePerformanceMonitoring(): void {
    if (!this.config.monitoring.enabled) return
    
    // Monitor First Contentful Paint
    if ('PerformanceObserver' in window) {
      const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        entries.forEach((entry) => {
          if (entry.entryType === 'paint' && entry.name === 'first-contentful-paint') {
            console.log('First Contentful Paint:', entry.startTime)
            this.reportPerformance('fcp', entry.startTime)
          }
        })
      })
      
      observer.observe({ entryTypes: ['paint'] })
    }
    
    // Monitor Largest Contentful Paint
    if ('PerformanceObserver' in window) {
      const observer = new PerformanceObserver((list) => {
        const entries = list.getEntries()
        const lastEntry = entries[entries.length - 1]
        if (lastEntry) {
          console.log('Largest Contentful Paint:', lastEntry.startTime)
          this.reportPerformance('lcp', lastEntry.startTime)
        }
      })
      
      observer.observe({ entryTypes: ['largest-contentful-paint'] })
    }
  }

  /**
   * Report performance metrics
   */
  private reportPerformance(metric: string, value: number): void {
    if (this.config.monitoring.reportEndpoint) {
      // Send to analytics endpoint
      fetch(this.config.monitoring.reportEndpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          metric,
          value,
          timestamp: Date.now(),
          url: window.location.href,
          userAgent: navigator.userAgent
        })
      }).catch(error => {
        console.warn('Failed to report performance metric:', error)
      })
    }
  }

  /**
   * Get performance metrics
   */
  getMetrics() {
    return {
      ...this.performanceMetrics,
      loadedStylesCount: this.loadedStyles.size,
      totalCSSSize: this.performanceMetrics.criticalCSSSize + this.performanceMetrics.deferredCSSSize
    }
  }

  /**
   * Preload critical resources
   */
  preloadCriticalResources(resources: string[]): void {
    resources.forEach(resource => {
      const link = document.createElement('link')
      link.rel = 'preload'
      
      // Determine resource type
      if (resource.endsWith('.css')) {
        link.as = 'style'
      } else if (resource.endsWith('.js')) {
        link.as = 'script'
      } else if (resource.match(/\.(woff2?|eot|ttf|otf)$/)) {
        link.as = 'font'
        link.crossOrigin = 'anonymous'
      } else if (resource.match(/\.(png|jpg|jpeg|webp|svg)$/)) {
        link.as = 'image'
      }
      
      link.href = resource
      document.head.appendChild(link)
    })
  }
}

/**
 * Default configuration for job portal
 */
const defaultConfig: CriticalCSSConfig = {
  criticalSelectors: [
    // Job portal specific critical selectors
    '^\.job-card',
    '^\.company-logo',
    '^\.search-form',
    '^\.filters',
    '^\.pagination',
    '^\.dashboard-widget',
    '^\.notification',
    '^\.modal',
    '^\.toast'
  ],
  deferredFiles: [
    // Files that can be loaded after initial render
    '/build/styles/admin',
    '/build/styles/charts',
    '/build/styles/advanced-components'
  ],
  monitoring: {
    enabled: process.env.NODE_ENV === 'production',
    reportEndpoint: '/api/performance-metrics'
  }
}

// Export singleton instance
export const criticalCSSManager = new CriticalCSSManager(defaultConfig)

// Export class for custom configurations
export { CriticalCSSManager, type CriticalCSSConfig } 