/**
 * Toast Composable - Compatibility wrapper around useEnhancedNotifications
 * Provides simple toast functionality for Vue components
 */

import { useEnhancedNotifications } from './useEnhancedNotifications'

export function useToast() {
  const { showSuccess, showError, showWarning, showInfo, toast } = useEnhancedNotifications()

  /**
   * Show a simple toast message
   */
  const showToast = (message: string, type: 'success' | 'error' | 'warning' | 'info' = 'info') => {
    return toast(message, type)
  }

  return {
    showToast,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    toast
  }
}
