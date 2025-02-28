<template>
    
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router';
import { useAuthStore   } from '../stores/authStore';
const router = useRouter();
const authStore = useAuthStore();

if (!authStore.token) {
  alert('Nothing to logout! Returning to the main page!');
  router.push('/');
} else {
  fetch('/api/logout', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${authStore.token}`,  
      'Content-Type': 'application/json',
    }
  })
  .then((response) => {
    if (!response.ok) {
      throw new Error('Failed to logout!');
    }
    return response.json();
  })
  .then(() => {
    
    authStore.logout();
    router.push('/auth/login');
  })
  .catch((error) => {
    alert('Logout failed. Please try again!');
    console.error(error);
  });
}
</script>
