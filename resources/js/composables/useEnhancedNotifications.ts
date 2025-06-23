/**
 * Enhanced Notifications Composable
 * Provides consistent notification handling across the Vue3 application
 * Integrates with SweetAlert2 and toast notifications
 */

import Swal from 'sweetalert2'

interface NotificationOptions {
  title?: string
  text?: string
  icon?: 'success' | 'error' | 'warning' | 'info' | 'question'
  timer?: number
  showConfirmButton?: boolean
  confirmButtonText?: string
  cancelButtonText?: string
  confirmButtonColor?: string
  cancelButtonColor?: string
  showCancelButton?: boolean
  reverseButtons?: boolean
  html?: string
  position?: 'top' | 'top-start' | 'top-end' | 'center' | 'center-start' | 'center-end' | 'bottom' | 'bottom-start' | 'bottom-end'
  toast?: boolean
}

interface ConfirmationOptions {
  title: string
  text: string
  confirmText?: string
  cancelText?: string
  icon?: 'warning' | 'question'
  confirmButtonColor?: string
  cancelButtonColor?: string
  reverseButtons?: boolean
}

export function useEnhancedNotifications() {
  
  /**
   * Show a success notification
   */
  const showSuccess = (message: string, options: NotificationOptions = {}) => {
    return Swal.fire({
      title: options.title || 'Success!',
      text: message,
      icon: 'success',
      timer: options.timer || 3000,
      showConfirmButton: options.showConfirmButton ?? false,
      position: options.position || 'top-end',
      toast: options.toast ?? true,
      timerProgressBar: true,
      ...options
    })
  }

  /**
   * Show an error notification
   */
  const showError = (message: string, options: NotificationOptions = {}) => {
    return Swal.fire({
      title: options.title || 'Error!',
      text: message,
      icon: 'error',
      showConfirmButton: options.showConfirmButton ?? true,
      confirmButtonText: options.confirmButtonText || 'OK',
      confirmButtonColor: options.confirmButtonColor || '#dc2626',
      ...options
    })
  }

  /**
   * Show a warning notification
   */
  const showWarning = (message: string, options: NotificationOptions = {}) => {
    return Swal.fire({
      title: options.title || 'Warning!',
      text: message,
      icon: 'warning',
      showConfirmButton: options.showConfirmButton ?? true,
      confirmButtonText: options.confirmButtonText || 'OK',
      confirmButtonColor: options.confirmButtonColor || '#f59e0b',
      ...options
    })
  }

  /**
   * Show an info notification
   */
  const showInfo = (message: string, options: NotificationOptions = {}) => {
    return Swal.fire({
      title: options.title || 'Info',
      text: message,
      icon: 'info',
      timer: options.timer || 4000,
      showConfirmButton: options.showConfirmButton ?? false,
      position: options.position || 'top-end',
      toast: options.toast ?? true,
      timerProgressBar: true,
      ...options
    })
  }

  /**
   * Show a confirmation dialog
   */
  const showConfirmation = async (options: ConfirmationOptions): Promise<boolean> => {
    const result = await Swal.fire({
      title: options.title,
      text: options.text,
      icon: options.icon || 'warning',
      showCancelButton: true,
      confirmButtonColor: options.confirmButtonColor || '#dc2626',
      cancelButtonColor: options.cancelButtonColor || '#6b7280',
      confirmButtonText: options.confirmText || 'Yes, delete it!',
      cancelButtonText: options.cancelText || 'Cancel',
      reverseButtons: options.reverseButtons ?? true,
      focusCancel: true
    })

    return result.isConfirmed
  }

  /**
   * Show a loading notification
   */
  const showLoading = (message: string = 'Loading...') => {
    return Swal.fire({
      title: message,
      allowEscapeKey: false,
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading()
      }
    })
  }

  /**
   * Close any open notification
   */
  const close = () => {
    Swal.close()
  }

  /**
   * Show a toast notification (simplified)
   */
  const toast = (message: string, type: 'success' | 'error' | 'warning' | 'info' = 'info') => {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })

    return Toast.fire({
      icon: type,
      title: message
    })
  }

  /**
   * Show API error message with proper formatting
   */
  const showApiError = (error: any) => {
    let message = 'An unexpected error occurred'
    
    if (error?.response?.data?.message) {
      message = error.response.data.message
    } else if (error?.message) {
      message = error.message
    } else if (typeof error === 'string') {
      message = error
    }

    return showError(message)
  }

  /**
   * Show validation errors from Laravel
   */
  const showValidationErrors = (errors: Record<string, string[]>) => {
    const errorMessages = Object.values(errors).flat()
    const message = errorMessages.join('\n')
    
    return Swal.fire({
      title: 'Validation Errors',
      html: errorMessages.map(msg => `<p class="text-left">${msg}</p>`).join(''),
      icon: 'error',
      confirmButtonText: 'OK',
      confirmButtonColor: '#dc2626'
    })
  }

  /**
   * Show a custom HTML notification
   */
  const showHtml = (html: string, options: NotificationOptions = {}) => {
    return Swal.fire({
      html,
      showConfirmButton: options.showConfirmButton ?? true,
      confirmButtonText: options.confirmButtonText || 'OK',
      ...options
    })
  }

  /**
   * Show a progress notification
   */
  const showProgress = (title: string, progress: number) => {
    return Swal.fire({
      title,
      html: `
        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
          <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${progress}%"></div>
        </div>
        <p class="mt-2 text-sm text-gray-600">${progress}% Complete</p>
      `,
      showConfirmButton: false,
      allowEscapeKey: false,
      allowOutsideClick: false
    })
  }

  return {
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showConfirmation,
    showLoading,
    showApiError,
    showValidationErrors,
    showHtml,
    showProgress,
    toast,
    close
  }
} 