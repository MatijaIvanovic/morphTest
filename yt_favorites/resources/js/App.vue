<script setup>
import { useRouter } from 'vue-router'
import {ref, computed, onMounted} from 'vue'
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline'
// Get the current route
const router = useRouter()

import { useAuthStore } from './stores/authStore'


const authStore = useAuthStore();

onMounted(()=>{
  authStore.checkLoginStatus();
  if(!authStore.isLoggedIn){
  router.push('/auth/login');
}
})


const navigation = [
  { name: 'Home', href: '/', requiresAuth:true},
  { name: 'Favorites', href: '/user/favorites', requiresAuth: true},
  { name: 'Logout', href:'/auth/logout', requiresAuth:true},
  
]
const computedNavigation = computed(() => {
  return navigation.filter(item => {
    if(item.requiresAuth=== undefined) return true;
    return item.requiresAuth ? authStore.isLoggedIn : !authStore.isLoggedIn;
  }).map(item => ({
    ...item,
    current:router.path===item.href,
  }))
})


</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <div class="bg-indigo-600 pb-32">
      <Disclosure as="nav" class="border-b border-indigo-300/25 bg-indigo-600 mb-8" >
        <div class="mx-auto max-w-7xl px-2 sm:px-4 lg:px-8">
          <div class="relative flex h-16 items-center justify-between lg:border-b lg:border-indigo-400/25">
            
          
            <div class="hidden lg:flex space-x-4 ml-auto">
              <router-link v-for="item in computedNavigation" 
                 :key="item.name" 
                 :to="item.href" 
                 :class="[item.current ? 'bg-indigo-700 text-white' : 'text-white hover:bg-indigo-500/75', 'rounded-md px-3 py-2 text-sm font-medium']" 
                 :aria-current="item.current ? 'page' : undefined">
                {{ item.name }}
              </router-link>
            </div>
          </div>
        </div>
      </Disclosure>
    </div>
   
    <main class="-mt-32 ">
      <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white px-5 py-6 shadow sm:px-6">
          <router-view></router-view>
        </div>
      </div>
    </main>
  </div>
</template>
