<template>
  <div>
    <h1 class="text-3xl font-bold mb-8">Browse Companies</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="company in companiesStore.companies" :key="company.id" 
           class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow cursor-pointer"
           @click="viewCompany(company.id)">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">
            {{ company.name.charAt(0) }}
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-semibold">{{ company.name }}</h3>
            <p class="text-gray-600">{{ company.industry }}</p>
          </div>
        </div>
        <p class="text-gray-700 mb-4">{{ company.description }}</p>
        <div class="flex justify-between items-center">
          <span class="text-sm text-gray-500">{{ company.job_count }} jobs</span>
          <Button variant="outline" size="sm">
            View Details
          </Button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted } from "vue"
import { useRouter } from "vue-router"
import { useCompaniesStore } from "../stores/companies"
import Button from "../components/ui/Button.vue"

const router = useRouter()
const companiesStore = useCompaniesStore()

const viewCompany = (id: number) => {
  router.push(`/companies/${id}`)
}

onMounted(() => {
  companiesStore.fetchCompanies()
})
</script> 