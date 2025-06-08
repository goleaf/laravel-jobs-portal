<template>
  <div>
    <h1 class="text-3xl font-bold mb-8">Profile</h1>
    
    <div class="bg-white p-8 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Personal Information</h2>
      <div class="space-y-4">
        <Input
          v-model="profile.name"
          label="Full Name"
        />
        <Input
          v-model="profile.email"
          type="email"
          label="Email"
        />
        <Input
          v-model="profile.phone"
          label="Phone Number"
        />
      </div>
      <div class="mt-6">
        <Button>Save Changes</Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue"
import Button from "../components/ui/Button.vue"
import Input from "../components/forms/Input.vue"

const profile = ref({
  name: "John Doe",
  email: "john@example.com",
  phone: "+1234567890"
})
</script> 