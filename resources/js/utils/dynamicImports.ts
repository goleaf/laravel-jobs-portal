/**
 * Dynamic Import Utilities for Bundle Optimization
 * 
 * This module provides lazy loading for heavy libraries to improve initial bundle size
 * and page load performance. Libraries are only loaded when actually needed.
 */

// Type definitions for dynamic imports
export interface DynamicImportCache {
  [key: string]: Promise<any> | any;
}

// Cache for dynamic imports to prevent multiple loads
const importCache: DynamicImportCache = {};

/**
 * Dynamically import SweetAlert2 only when needed
 * Reduces initial bundle size by ~150KB
 */
export async function loadSweetAlert() {
  const cacheKey = 'sweetalert2';
  
  if (importCache[cacheKey]) {
    return importCache[cacheKey];
  }
  
  try {
    const module = await import(/* webpackChunkName: "vendor-alerts" */ 'sweetalert2');
    importCache[cacheKey] = module.default;
    return module.default;
  } catch (error) {
    console.error('Failed to load SweetAlert2:', error);
    throw error;
  }
}

/**
 * Custom debounce implementation to avoid lodash dependency
 * Optimized for performance and smaller bundle size
 */
export function debounce<T extends (...args: any[]) => any>(
  func: T,
  wait: number = 300,
  options: {
    leading?: boolean;
    trailing?: boolean;
    maxWait?: number;
  } = {}
): (...args: Parameters<T>) => void {
  const { leading = false, trailing = true, maxWait } = options;
  
  let timerId: NodeJS.Timeout | undefined;
  let maxTimerId: NodeJS.Timeout | undefined;
  let lastCallTime: number | undefined;
  let lastInvokeTime = 0;
  let lastArgs: Parameters<T> | undefined;
  let lastThis: any;
  let result: ReturnType<T>;

  function invokeFunc(time: number) {
    const args = lastArgs!;
    const thisArg = lastThis;

    lastArgs = lastThis = undefined;
    lastInvokeTime = time;
    result = func.apply(thisArg, args);
    return result;
  }

  function leadingEdge(time: number) {
    lastInvokeTime = time;
    timerId = setTimeout(timerExpired, wait);
    return leading ? invokeFunc(time) : result;
  }

  function remainingWait(time: number) {
    const timeSinceLastCall = time - lastCallTime!;
    const timeSinceLastInvoke = time - lastInvokeTime;
    const timeWaiting = wait - timeSinceLastCall;

    return maxWait !== undefined
      ? Math.min(timeWaiting, maxWait - timeSinceLastInvoke)
      : timeWaiting;
  }

  function shouldInvoke(time: number) {
    const timeSinceLastCall = time - lastCallTime!;
    const timeSinceLastInvoke = time - lastInvokeTime;

    return (
      lastCallTime === undefined ||
      timeSinceLastCall >= wait ||
      timeSinceLastCall < 0 ||
      (maxWait !== undefined && timeSinceLastInvoke >= maxWait)
    );
  }

  function timerExpired() {
    const time = Date.now();
    if (shouldInvoke(time)) {
      return trailingEdge(time);
    }
    timerId = setTimeout(timerExpired, remainingWait(time));
  }

  function trailingEdge(time: number) {
    timerId = undefined;

    if (trailing && lastArgs) {
      return invokeFunc(time);
    }
    lastArgs = lastThis = undefined;
    return result;
  }

  function cancel() {
    if (timerId !== undefined) {
      clearTimeout(timerId);
    }
    if (maxTimerId !== undefined) {
      clearTimeout(maxTimerId);
    }
    lastInvokeTime = 0;
    lastArgs = lastCallTime = lastThis = timerId = undefined;
  }

  function flush() {
    return timerId === undefined ? result : trailingEdge(Date.now());
  }

  function debounced(...args: Parameters<T>) {
    const time = Date.now();
    const isInvoking = shouldInvoke(time);

    lastArgs = args;
    lastThis = this;
    lastCallTime = time;

    if (isInvoking) {
      if (timerId === undefined) {
        return leadingEdge(lastCallTime);
      }
      if (maxWait !== undefined) {
        timerId = setTimeout(timerExpired, wait);
        return invokeFunc(lastCallTime);
      }
    }
    if (timerId === undefined) {
      timerId = setTimeout(timerExpired, wait);
    }
    return result;
  }

  debounced.cancel = cancel;
  debounced.flush = flush;
  return debounced;
}

/**
 * Custom throttle implementation for performance optimization
 */
export function throttle<T extends (...args: any[]) => any>(
  func: T,
  wait: number = 300,
  options: {
    leading?: boolean;
    trailing?: boolean;
  } = {}
): (...args: Parameters<T>) => void {
  const { leading = true, trailing = true } = options;
  return debounce(func, wait, {
    leading,
    trailing,
    maxWait: wait
  });
}

/**
 * Dynamically import chart libraries only when needed for analytics
 * This is for future chart implementations
 */
export async function loadChartJS() {
  const cacheKey = 'chart-js';
  
  if (importCache[cacheKey]) {
    return importCache[cacheKey];
  }
  
  try {
    const module = await import(/* webpackChunkName: "vendor-charts" */ 'chart.js');
    importCache[cacheKey] = module;
    return module;
  } catch (error) {
    console.error('Failed to load Chart.js:', error);
    throw error;
  }
}

/**
 * Enhanced SweetAlert utility with optimized loading
 */
export class OptimizedAlert {
  private static swalInstance: any = null;
  
  static async getInstance() {
    if (!this.swalInstance) {
      this.swalInstance = await loadSweetAlert();
    }
    return this.swalInstance;
  }
  
  static async fire(options: any) {
    const Swal = await this.getInstance();
    return Swal.fire(options);
  }
  
  static async confirm(options: any) {
    const Swal = await this.getInstance();
    return Swal.fire({
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3b82f6',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Yes',
      cancelButtonText: 'Cancel',
      ...options
    });
  }
  
  static async success(title: string, text?: string) {
    const Swal = await this.getInstance();
    return Swal.fire({
      icon: 'success',
      title,
      text,
      timer: 3000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  static async error(title: string, text?: string) {
    const Swal = await this.getInstance();
    return Swal.fire({
      icon: 'error',
      title,
      text
    });
  }
  
  static async warning(title: string, text?: string) {
    const Swal = await this.getInstance();
    return Swal.fire({
      icon: 'warning',
      title,
      text
    });
  }
}

/**
 * Enhanced utility functions with optimized implementations
 */
export class OptimizedLodash {
  /**
   * Returns a debounced function using our custom implementation
   */
  static debounce<T extends (...args: any[]) => any>(
    func: T,
    wait?: number,
    options?: { leading?: boolean; trailing?: boolean; maxWait?: number }
  ) {
    return debounce(func, wait, options);
  }
  
  /**
   * Returns a throttled function using our custom implementation
   */
  static throttle<T extends (...args: any[]) => any>(
    func: T,
    wait?: number,
    options?: { leading?: boolean; trailing?: boolean }
  ) {
    return throttle(func, wait, options);
  }
}

/**
 * Preload utility for critical dynamic imports
 * Call this on route entry to preload likely-needed libraries
 */
export async function preloadCriticalDependencies() {
  const promises: Promise<any>[] = [];
  
  // Preload commonly used libraries
  promises.push(loadSweetAlert().catch(() => {})); // Silent fail for preload
  
  return Promise.allSettled(promises);
}

/**
 * Bundle analyzer helper - logs import cache status
 */
export function logImportCacheStatus() {
  if (process.env.NODE_ENV === 'development') {
    console.log('Dynamic Import Cache Status:', {
      cached: Object.keys(importCache),
      cacheSize: Object.keys(importCache).length
    });
  }
}

// Performance monitoring for dynamic imports
let importMetrics: { [key: string]: number } = {};

export function trackImportPerformance(name: string, startTime: number) {
  const loadTime = performance.now() - startTime;
  importMetrics[name] = loadTime;
  
  if (process.env.NODE_ENV === 'development') {
    console.log(`Dynamic import "${name}" loaded in ${loadTime.toFixed(2)}ms`);
  }
}

export function getImportMetrics() {
  return { ...importMetrics };
} 