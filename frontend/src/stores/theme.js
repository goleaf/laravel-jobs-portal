import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', {
  state: () => ({
    theme: localStorage.getItem('theme') || 'system'
  }),
  actions: {
    setTheme(theme) {
      this.theme = theme
      localStorage.setItem('theme', theme)
      this.applyTheme()
    },
    initializeTheme() {
      this.applyTheme()
    },
    applyTheme() {
      const isDark = this.theme === 'dark' || 
        (this.theme === 'system' && 
          window.matchMedia('(prefers-color-scheme: dark)').matches)

      document.documentElement.classList.toggle('dark', isDark)
    },
    toggleTheme() {
      const themes = ['light', 'dark', 'system']
      const currentIndex = themes.indexOf(this.theme)
      const nextIndex = (currentIndex + 1) % themes.length
      this.setTheme(themes[nextIndex])
    }
  }
}) 