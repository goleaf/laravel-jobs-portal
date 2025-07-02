import { ref, onMounted, onUnmounted } from 'vue';

interface PerformanceMetrics {
  fcp: number | null; // First Contentful Paint
  lcp: number | null; // Largest Contentful Paint
  fid: number | null; // First Input Delay
  cls: number | null; // Cumulative Layout Shift
  ttfb: number | null; // Time to First Byte
  domLoad: number | null; // DOM Content Loaded
  windowLoad: number | null; // Window Load
  routeChange: number | null; // Route change time
}

interface NavigationMetrics {
  routeFrom: string;
  routeTo: string;
  duration: number;
  timestamp: Date;
}

export function usePerformance() {
  const metrics = ref<PerformanceMetrics>({
    fcp: null,
    lcp: null,
    fid: null,
    cls: null,
    ttfb: null,
    domLoad: null,
    windowLoad: null,
    routeChange: null,
  });

  const navigationHistory = ref<NavigationMetrics[]>([]);
  const isSupported = ref(false);
  const observer = ref<PerformanceObserver | null>(null);

  // Check browser support
  const checkSupport = () => {
    isSupported.value = !!(
      typeof performance !== 'undefined' &&
      performance.mark &&
      performance.measure &&
      performance.getEntriesByType &&
      PerformanceObserver
    );
    return isSupported.value;
  };

  // Get First Contentful Paint
  const getFCP = () => {
    try {
      const fcpEntry = performance.getEntriesByName('first-contentful-paint')[0];
      if (fcpEntry) {
        metrics.value.fcp = Math.round(fcpEntry.startTime);
        console.log('[Performance] FCP:', metrics.value.fcp + 'ms');
      }
    } catch (error) {
      console.warn('[Performance] Error getting FCP:', error);
    }
  };

  // Get Time to First Byte
  const getTTFB = () => {
    try {
      const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming;
      if (navigation) {
        metrics.value.ttfb = Math.round(navigation.responseStart - navigation.requestStart);
        console.log('[Performance] TTFB:', metrics.value.ttfb + 'ms');
      }
    } catch (error) {
      console.warn('[Performance] Error getting TTFB:', error);
    }
  };

  // Get DOM Content Loaded time
  const getDOMLoad = () => {
    try {
      const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming;
      if (navigation) {
        metrics.value.domLoad = Math.round(navigation.domContentLoadedEventEnd - navigation.navigationStart);
        console.log('[Performance] DOM Load:', metrics.value.domLoad + 'ms');
      }
    } catch (error) {
      console.warn('[Performance] Error getting DOM load time:', error);
    }
  };

  // Get Window Load time
  const getWindowLoad = () => {
    try {
      const navigation = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming;
      if (navigation) {
        metrics.value.windowLoad = Math.round(navigation.loadEventEnd - navigation.navigationStart);
        console.log('[Performance] Window Load:', metrics.value.windowLoad + 'ms');
      }
    } catch (error) {
      console.warn('[Performance] Error getting window load time:', error);
    }
  };

  // Track Largest Contentful Paint
  const trackLCP = () => {
    try {
      const lcpObserver = new PerformanceObserver((entryList) => {
        const entries = entryList.getEntries();
        const lastEntry = entries[entries.length - 1];
        metrics.value.lcp = Math.round(lastEntry.startTime);
        console.log('[Performance] LCP:', metrics.value.lcp + 'ms');
      });

      lcpObserver.observe({ type: 'largest-contentful-paint', buffered: true });
      return lcpObserver;
    } catch (error) {
      console.warn('[Performance] Error tracking LCP:', error);
      return null;
    }
  };

  // Track First Input Delay
  const trackFID = () => {
    try {
      const fidObserver = new PerformanceObserver((entryList) => {
        const entries = entryList.getEntries();
        entries.forEach((entry: any) => {
          metrics.value.fid = Math.round(entry.processingStart - entry.startTime);
          console.log('[Performance] FID:', metrics.value.fid + 'ms');
        });
      });

      fidObserver.observe({ type: 'first-input', buffered: true });
      return fidObserver;
    } catch (error) {
      console.warn('[Performance] Error tracking FID:', error);
      return null;
    }
  };

  // Track Cumulative Layout Shift
  const trackCLS = () => {
    try {
      let clsValue = 0;
      const clsObserver = new PerformanceObserver((entryList) => {
        const entries = entryList.getEntries();
        entries.forEach((entry: any) => {
          if (!entry.hadRecentInput) {
            clsValue += entry.value;
            metrics.value.cls = Math.round(clsValue * 1000) / 1000;
            console.log('[Performance] CLS:', metrics.value.cls);
          }
        });
      });

      clsObserver.observe({ type: 'layout-shift', buffered: true });
      return clsObserver;
    } catch (error) {
      console.warn('[Performance] Error tracking CLS:', error);
      return null;
    }
  };

  // Track route changes
  const trackRouteChange = (from: string, to: string) => {
    const startTime = performance.now();
    
    // Mark the start of route change
    performance.mark(`route-change-start-${to}`);
    
    return {
      end: () => {
        try {
          const endTime = performance.now();
          const duration = Math.round(endTime - startTime);
          
          performance.mark(`route-change-end-${to}`);
          performance.measure(
            `route-change-${to}`,
            `route-change-start-${to}`,
            `route-change-end-${to}`
          );
          
          const navigationMetric: NavigationMetrics = {
            routeFrom: from,
            routeTo: to,
            duration,
            timestamp: new Date(),
          };
          
          navigationHistory.value.push(navigationMetric);
          metrics.value.routeChange = duration;
          
          console.log(`[Performance] Route change ${from} → ${to}:`, duration + 'ms');
          
          // Keep only last 10 navigation entries
          if (navigationHistory.value.length > 10) {
            navigationHistory.value = navigationHistory.value.slice(-10);
          }
          
          return duration;
        } catch (error) {
          console.warn('[Performance] Error tracking route change:', error);
          return null;
        }
      }
    };
  };

  // Get performance score based on Core Web Vitals
  const getPerformanceScore = () => {
    let score = 100;
    let details: string[] = [];

    // FCP scoring (good < 1.8s, needs improvement < 3s, poor >= 3s)
    if (metrics.value.fcp !== null) {
      if (metrics.value.fcp > 3000) {
        score -= 20;
        details.push('FCP too slow');
      } else if (metrics.value.fcp > 1800) {
        score -= 10;
        details.push('FCP needs improvement');
      }
    }

    // LCP scoring (good < 2.5s, needs improvement < 4s, poor >= 4s)
    if (metrics.value.lcp !== null) {
      if (metrics.value.lcp > 4000) {
        score -= 25;
        details.push('LCP too slow');
      } else if (metrics.value.lcp > 2500) {
        score -= 15;
        details.push('LCP needs improvement');
      }
    }

    // FID scoring (good < 100ms, needs improvement < 300ms, poor >= 300ms)
    if (metrics.value.fid !== null) {
      if (metrics.value.fid > 300) {
        score -= 20;
        details.push('FID too slow');
      } else if (metrics.value.fid > 100) {
        score -= 10;
        details.push('FID needs improvement');
      }
    }

    // CLS scoring (good < 0.1, needs improvement < 0.25, poor >= 0.25)
    if (metrics.value.cls !== null) {
      if (metrics.value.cls > 0.25) {
        score -= 15;
        details.push('CLS too high');
      } else if (metrics.value.cls > 0.1) {
        score -= 8;
        details.push('CLS needs improvement');
      }
    }

    return {
      score: Math.max(0, score),
      grade: score >= 90 ? 'A' : score >= 80 ? 'B' : score >= 70 ? 'C' : score >= 60 ? 'D' : 'F',
      details,
    };
  };

  // Export performance data
  const exportMetrics = () => {
    return {
      metrics: { ...metrics.value },
      navigation: [...navigationHistory.value],
      score: getPerformanceScore(),
      timestamp: new Date(),
      userAgent: navigator.userAgent,
      url: window.location.href,
    };
  };

  // Send metrics to analytics (placeholder)
  const sendToAnalytics = (data: any) => {
    // This could send data to Google Analytics, custom analytics, etc.
    console.log('[Performance] Analytics data:', data);
    
    // Example: Send to custom analytics endpoint
    // fetch('/api/analytics/performance', {
    //   method: 'POST',
    //   headers: { 'Content-Type': 'application/json' },
    //   body: JSON.stringify(data)
    // }).catch(err => console.warn('Analytics send failed:', err));
  };

  // Initialize performance monitoring
  const startMonitoring = () => {
    if (!checkSupport()) {
      console.warn('[Performance] Performance monitoring not supported');
      return;
    }

    console.log('[Performance] Starting performance monitoring...');

    // Immediate measurements
    getTTFB();
    getFCP();
    getDOMLoad();

    // Window load event
    if (document.readyState === 'complete') {
      getWindowLoad();
    } else {
      window.addEventListener('load', getWindowLoad);
    }

    // Set up observers
    const observers = [
      trackLCP(),
      trackFID(),
      trackCLS(),
    ].filter(Boolean);

    observer.value = {
      disconnect: () => {
        observers.forEach(obs => obs?.disconnect());
      }
    } as PerformanceObserver;

    // Send initial metrics after 5 seconds
    setTimeout(() => {
      const data = exportMetrics();
      sendToAnalytics(data);
    }, 5000);
  };

  // Cleanup
  const stopMonitoring = () => {
    if (observer.value) {
      observer.value.disconnect();
      observer.value = null;
    }
  };

  // Auto-start monitoring on mount
  onMounted(() => {
    startMonitoring();
  });

  onUnmounted(() => {
    stopMonitoring();
  });

  return {
    metrics,
    navigationHistory,
    isSupported,
    trackRouteChange,
    getPerformanceScore,
    exportMetrics,
    sendToAnalytics,
    startMonitoring,
    stopMonitoring,
  };
} 