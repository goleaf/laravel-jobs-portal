<template>
  <div class="text-center py-16">
    <h1 class="text-6xl font-bold text-gray-400 mb-4">404</h1>
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Page Not Found</h2>
    <p class="text-gray-600 mb-8">The page you are looking for does not exist.</p>
    <Button @click="$router.push('/')">
      Go Home
    </Button>
  </div>
</template>

<script setup lang="ts">
import Button from "../components/ui/Button.vue"
</script>