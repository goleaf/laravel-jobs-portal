import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'jsdom',
    include: [
      'resources/js/**/*.test.{js,ts}',
      'resources/js/**/*.spec.{js,ts}',
      'tests/frontend/**/*.test.{js,ts}',
      'tests/frontend/**/*.spec.{js,ts}'
    ],
    exclude: [
      'node_modules',
      'vendor/**',
      'dist',
      '.nuxt',
      'coverage'
    ],
    globals: true,
    setupFiles: ['tests/frontend/setup.ts']
  },
  resolve: {
    alias: {
      '@': resolve(__dirname, 'resources/js'),
      '~': resolve(__dirname, 'resources'),
    },
  },
})