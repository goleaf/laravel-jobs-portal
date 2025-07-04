<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import HelloWorld from './components/HelloWorld.vue'
import NavigationComponent from '@/components/NavigationComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
import { useThemeStore } from '@/stores/theme'

const themeStore = useThemeStore()

onMounted(() => {
  // Apply saved theme
  themeStore.initializeTheme()
})
</script>

<template>
  <div id="app" class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <nav class="bg-white dark:bg-gray-800 shadow">
      <!-- Navigation Component -->
      <NavigationComponent />
    </nav>

    <main class="container mx-auto px-4 py-8">
      <router-view v-slot="{ Component }">
        <transition 
          name="fade" 
          mode="out-in"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <footer class="bg-white dark:bg-gray-800 text-center py-6">
      <!-- Footer Component -->
      <FooterComponent />
    </footer>
  </div>
</template>

<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 2rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;

    padding: 1rem 0;
    margin-top: 1rem;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
