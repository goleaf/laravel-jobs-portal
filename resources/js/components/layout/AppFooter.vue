<template>
  <footer class="bg-white border-t border-neutral-200">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <span class="text-sm text-neutral-600">
            © {{ currentYear }} Job Portal. All rights reserved.
          </span>
        </div>
        
        <div class="flex items-center space-x-4">
          <a href="#" class="text-sm text-neutral-600 hover:text-primary-600 transition-colors">
            Privacy Policy
          </a>
          <a href="#" class="text-sm text-neutral-600 hover:text-primary-600 transition-colors">
            Terms of Service
          </a>
          <a href="#" class="text-sm text-neutral-600 hover:text-primary-600 transition-colors">
            Contact
          </a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
</script> 