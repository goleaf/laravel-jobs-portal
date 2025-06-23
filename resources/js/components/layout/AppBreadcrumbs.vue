<template>
  <nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-4">
      <!-- Home -->
      <li>
        <div>
          <router-link 
            to="/" 
            class="text-neutral-400 hover:text-neutral-600 transition-colors"
          >
            <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
              <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            <span class="sr-only">Home</span>
          </router-link>
        </div>
      </li>

      <!-- Dynamic breadcrumbs -->
      <li v-for="(breadcrumb, index) in breadcrumbs" :key="index">
        <div class="flex items-center">
          <svg class="flex-shrink-0 h-5 w-5 text-neutral-300" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
          </svg>
          
          <router-link
            v-if="breadcrumb.path && index < breadcrumbs.length - 1"
            :to="breadcrumb.path"
            class="ml-4 text-sm font-medium text-neutral-500 hover:text-neutral-700 transition-colors"
          >
            {{ breadcrumb.name }}
          </router-link>
          
          <span
            v-else
            class="ml-4 text-sm font-medium text-neutral-900"
            aria-current="page"
          >
            {{ breadcrumb.name }}
          </span>
        </div>
      </li>
    </ol>
  </nav>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useRoute } from 'vue-router';

interface Breadcrumb {
  name: string;
  path?: string;
}

const route = useRoute();

const breadcrumbs = computed((): Breadcrumb[] => {
  const pathArray = route.path.split('/').filter(path => path);
  const breadcrumbArray: Breadcrumb[] = [];

  pathArray.forEach((path, index) => {
    const routePath = '/' + pathArray.slice(0, index + 1).join('/');
    const name = path.charAt(0).toUpperCase() + path.slice(1);
    
    breadcrumbArray.push({
      name: name.replace(/-/g, ' '),
      path: index === pathArray.length - 1 ? undefined : routePath
    });
  });

  return breadcrumbArray;
});
</script> 